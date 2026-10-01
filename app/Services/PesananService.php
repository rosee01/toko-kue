<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Mengelola pesanan sekaligus menjaga stok produk tetap konsisten.
 * Semua operasi berjalan di dalam transaksi database dengan row lock.
 */
class PesananService
{
    public function __construct(private WhatsAppOrderNotifier $whatsappNotifier)
    {
    }

    public function buat(array $data): Pesanan
    {
        return DB::transaction(function () use ($data) {
            $produk = $this->ambilProdukDenganStok((int) $data['produk_id'], (int) $data['jumlah']);
            $jumlah = (int) $data['jumlah'];
            $stokSebelum = $produk->stok;
            $produk->decrement('stok', $jumlah);

            $pesanan = Pesanan::create($this->atribut($data, $produk, $produk->harga));
            $this->catatPerubahanStok($produk, $pesanan, 'keluar', $jumlah, $stokSebelum, 'Stok digunakan untuk pesanan');

            return $pesanan;
        });
    }

    public function ubah(Pesanan $pesanan, array $data): Pesanan
    {
        return DB::transaction(function () use ($pesanan, $data) {
            $pesanan->refresh();
            if ($pesanan->status !== Pesanan::STATUS_PENDING
                || $pesanan->bukti_pembayaran !== null
                || ! in_array($pesanan->status_pembayaran, [
                    Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    Pesanan::PEMBAYARAN_COD,
                ], true)) {
                throw ValidationException::withMessages([
                    'pesanan' => 'Pesanan hanya dapat diedit saat masih menunggu konfirmasi dan belum ada pembayaran yang dikirim.',
                ]);
            }

            $this->kembalikanStok($pesanan);

            $produk = $this->ambilProdukDenganStok((int) $data['produk_id'], (int) $data['jumlah']);
            $jumlah = (int) $data['jumlah'];
            $stokSebelum = $produk->stok;
            $produk->decrement('stok', $jumlah);

            // Produk yang sama tetap memakai harga saat pesanan dibuat.
            $harga = $pesanan->produk_id === $produk->id_produk ? $pesanan->harga_satuan : $produk->harga;

            $data['status'] = $pesanan->status;
            $pesanan->update($this->atribut($data, $produk, $harga));
            $this->catatPerubahanStok($produk, $pesanan, 'keluar', $jumlah, $stokSebelum, 'Stok digunakan untuk pesanan');

            return $pesanan;
        });
    }

    public function hapus(Pesanan $pesanan): void
    {
        DB::transaction(function () use ($pesanan) {
            $items = $this->ambilItemPesanan($pesanan);
            if ($items->isEmpty() || $items->contains(fn (Pesanan $item) => $item->status !== Pesanan::STATUS_PENDING
                || ! in_array($item->status_pembayaran, [Pesanan::PEMBAYARAN_BELUM_DIBAYAR, Pesanan::PEMBAYARAN_COD], true)
                || $item->bukti_pembayaran !== null)) {
                throw ValidationException::withMessages([
                    'pesanan' => 'Pesanan hanya dapat dihapus saat masih menunggu konfirmasi dan belum ada pembayaran yang dikirim.',
                ]);
            }

            $items->each(function (Pesanan $item): void {
                $this->kembalikanStok($item);
                $item->delete();
            });
        });
    }

    public function ubahStatus(Pesanan $pesanan, string $status): void
    {
        if ($status === Pesanan::STATUS_DIBATALKAN) {
            $this->batalkan($pesanan);

            return;
        }

        if (in_array($status, [Pesanan::STATUS_DALAM_PENGANTARAN, Pesanan::STATUS_SELESAI], true)) {
            throw ValidationException::withMessages([
                'status' => 'Status pengantaran hanya dapat diperbarui melalui penugasan dan penyelesaian tugas driver.',
            ]);
        }

        DB::transaction(function () use ($pesanan, $status) {
            $items = $this->ambilItemPesanan($pesanan);
            $statusSaatIni = $items->first()?->status;
            if ($items->contains(fn (Pesanan $item) => $item->status !== $statusSaatIni)) {
                throw ValidationException::withMessages(['status' => 'Status item dalam satu checkout tidak konsisten.']);
            }
            $transisiDiizinkan = match ($statusSaatIni) {
                Pesanan::STATUS_PENDING => [Pesanan::STATUS_DIPROSES],
                Pesanan::STATUS_DIPROSES => [Pesanan::STATUS_SIAP_DIANTAR],
                default => [],
            };

            if (! in_array($status, $transisiDiizinkan, true)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status pesanan tidak sesuai dengan tahap saat ini.']);
            }

            if ($status === Pesanan::STATUS_DIPROSES && $items->contains(fn (Pesanan $item) => ! $item->ongkir_dikonfirmasi
                || ($item->jenis_pengiriman !== null && (! $item->jadwal_dikonfirmasi || ! $item->jadwal_pengiriman)))) {
                throw ValidationException::withMessages(['status' => 'Konfirmasi ongkos kirim dan jadwal pengiriman terlebih dahulu sebelum memproses pesanan.']);
            }
            if ($status === Pesanan::STATUS_DIPROSES && $items->contains('status_pembayaran', Pesanan::PEMBAYARAN_DITOLAK)) {
                throw ValidationException::withMessages(['status' => 'Tunggu customer mengirim ulang bukti pembayaran yang benar sebelum memproses pesanan.']);
            }

            if ($status === Pesanan::STATUS_SIAP_DIANTAR && $items->contains(fn (Pesanan $item) => in_array($item->metode_pembayaran, [
                Pesanan::METODE_TRANSFER_BANK,
                Pesanan::METODE_E_WALLET,
            ], true) && $item->status_pembayaran !== Pesanan::PEMBAYARAN_LUNAS)) {
                throw ValidationException::withMessages(['status' => 'Pembayaran harus lunas atau menggunakan COD sebelum pesanan siap diantar.']);
            }

            $items->each(fn (Pesanan $item) => $item->update(['status' => $status]));
        });

        $this->kirimNotifikasi($pesanan, 'order-status', $status, 'Status pesanan diperbarui oleh toko.');
    }

    public function tugaskanDriver(Pesanan $pesanan, Driver $driver): void
    {
        $this->tugaskanPesananBersamaan([$pesanan->id], $driver);
    }

    public function tugaskanPesananBersamaan(array $pesananIds, Driver $driver): void
    {
        DB::transaction(function () use ($pesananIds, $driver) {
            $driver = Driver::whereKey($driver->id)->lockForUpdate()->firstOrFail();
            if (! $driver->is_active) {
                throw ValidationException::withMessages(['driver_id' => 'Driver nonaktif tidak dapat menerima tugas.']);
            }

            if (Pesanan::where('kurir_id', $driver->id)->where('status', Pesanan::STATUS_DALAM_PENGANTARAN)->exists()) {
                throw ValidationException::withMessages([
                    'driver_id' => 'Driver sedang dalam perjalanan. Selesaikan pengantaran yang aktif sebelum memberikan tugas baru.',
                ]);
            }

            $ids = collect($pesananIds)->unique()->values();
            $pilihan = Pesanan::whereIn('id', $ids)->lockForUpdate()->get();
            if ($pilihan->count() !== $ids->count() || $pilihan->isEmpty()) {
                throw ValidationException::withMessages(['pesanan_ids' => 'Satu atau lebih pesanan tidak ditemukan.']);
            }

            $grup = $pilihan->map(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)->unique();
            $items = Pesanan::where(function ($query) use ($grup) {
                foreach ($grup as $key) {
                    if (str_starts_with($key, 'pesanan-')) {
                        $query->orWhere('id', substr($key, strlen('pesanan-')));
                    } else {
                        $query->orWhere('kode_pesanan', $key);
                    }
                }
            })->lockForUpdate()->get();

            if ($items->isEmpty() || $items->contains(fn (Pesanan $item) => $item->status !== Pesanan::STATUS_SIAP_DIANTAR || $item->kurir_id !== null)) {
                throw ValidationException::withMessages(['pesanan_ids' => 'Semua pesanan harus siap diantar dan belum memiliki driver.']);
            }
            if ($items->contains(fn (Pesanan $item) => ! $item->ongkir_dikonfirmasi
                || ($item->jenis_pengiriman !== null && (! $item->jadwal_dikonfirmasi || ! $item->jadwal_pengiriman)))) {
                throw ValidationException::withMessages(['pesanan_ids' => 'Ongkos kirim dan jadwal harus dikonfirmasi sebelum pesanan diserahkan ke driver.']);
            }
            if ($items->contains(fn (Pesanan $item) => in_array($item->metode_pembayaran, [
                Pesanan::METODE_TRANSFER_BANK,
                Pesanan::METODE_E_WALLET,
            ], true) && $item->status_pembayaran !== Pesanan::PEMBAYARAN_LUNAS)) {
                throw ValidationException::withMessages(['pesanan_ids' => 'Semua pesanan transfer/e-wallet harus sudah lunas sebelum diserahkan ke driver.']);
            }

            $items->each(fn (Pesanan $item) => $item->update([
                'kurir_id' => $driver->id,
                'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            ]));
        });

        Pesanan::whereIn('id', $pesananIds)
            ->get()
            ->unique(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
            ->each(fn (Pesanan $item) => $this->kirimNotifikasi(
                $item,
                'driver-assigned',
                'Dalam Pengantaran',
                'Pesanan sedang diantar oleh ' . $driver->nama . '.'
            ));
    }

    public function selesaikanPengantaran(Pesanan $pesanan, Driver $driver): void
    {
        DB::transaction(function () use ($pesanan, $driver) {
            $items = $this->ambilItemPesanan($pesanan);
            if ($items->isEmpty() || $items->contains(fn (Pesanan $item) => $item->status !== Pesanan::STATUS_DALAM_PENGANTARAN || $item->kurir_id !== $driver->id)) {
                throw ValidationException::withMessages(['pesanan_id' => 'Pesanan ini bukan tugas pengantaran aktif driver tersebut.']);
            }

            $items->each(fn (Pesanan $item) => $item->update(['status' => Pesanan::STATUS_SELESAI]));
        });

        $this->kirimNotifikasi($pesanan, 'delivery-completed', 'Selesai', 'Pesanan telah ditandai selesai oleh driver.');
    }

    public function ubahOngkir(Pesanan $pesanan, int $ongkir, ?string $jadwal = null): void
    {
        DB::transaction(function () use ($pesanan, $ongkir, $jadwal): void {
            $items = $this->ambilItemPesanan($pesanan);
            if ($items->isEmpty() || $items->contains(fn (Pesanan $item) => $item->status !== Pesanan::STATUS_PENDING
                || $item->bukti_pembayaran !== null
                || in_array($item->status_pembayaran, [
                    Pesanan::PEMBAYARAN_LUNAS,
                    Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
                ], true))) {
                throw ValidationException::withMessages([
                    'ongkir' => 'Ongkir hanya dapat dikonfirmasi saat pesanan masih baru dan belum ada pembayaran yang dikirim.',
                ]);
            }

            if ($items->contains(fn (Pesanan $item) => $item->jenis_pengiriman !== null) && ! $jadwal) {
                throw ValidationException::withMessages([
                    'jadwal_pengiriman' => 'Pilih dan konfirmasi jadwal pengiriman untuk pesanan ini.',
                ]);
            }

            $jadwalKonfirmasi = $jadwal
                ? \Illuminate\Support\Carbon::parse($jadwal)
                : ($items->first()->jadwal_diminta ?? now()->addDay());
            if (! $jadwalKonfirmasi || $jadwalKonfirmasi->isPast()) {
                throw ValidationException::withMessages(['jadwal_pengiriman' => 'Pilih jadwal pengiriman yang masih akan datang.']);
            }

            $items->each(fn (Pesanan $item) => $item->update([
                'ongkir' => $ongkir,
                'ongkir_dikonfirmasi' => true,
                'jadwal_pengiriman' => $jadwalKonfirmasi,
                'jadwal_dikonfirmasi' => true,
            ]));
        });

        $this->kirimNotifikasi($pesanan, 'delivery-confirmed', 'Ongkir dan jadwal dikonfirmasi', 'Jadwal pengiriman telah dikonfirmasi oleh toko.');
    }

    public function ubahStatusPembayaran(Pesanan $pesanan, string $status, ?string $catatan = null): void
    {
        DB::transaction(function () use ($pesanan, $status, $catatan) {
            $items = $this->ambilItemPesanan($pesanan);
            $utama = $items->first();
            if (! $utama) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Pesanan tidak ditemukan.']);
            }
            if ($items->contains(fn (Pesanan $item) => $item->status !== $utama->status
                || $item->status_pembayaran !== $utama->status_pembayaran
                || $item->metode_pembayaran !== $utama->metode_pembayaran
                || $item->bukti_pembayaran !== $utama->bukti_pembayaran)) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Data status item dalam satu checkout tidak konsisten; pembayaran tidak dapat diubah.']);
            }
            if ($utama->status === Pesanan::STATUS_DIBATALKAN) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Pesanan yang dibatalkan tidak dapat menerima perubahan pembayaran.']);
            }

            if ($status === Pesanan::PEMBAYARAN_LUNAS) {
                if ($utama->metode_pembayaran === Pesanan::METODE_COD) {
                    if ($utama->status !== Pesanan::STATUS_SELESAI || $utama->status_pembayaran !== Pesanan::PEMBAYARAN_COD) {
                        throw ValidationException::withMessages(['status_pembayaran' => 'Pembayaran COD baru dapat dikonfirmasi setelah pesanan selesai diantar.']);
                    }
                } elseif ($utama->status_pembayaran !== Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI || ! $utama->bukti_pembayaran) {
                    throw ValidationException::withMessages(['status_pembayaran' => 'Periksa bukti pembayaran yang diunggah sebelum menandai pembayaran lunas.']);
                }
            }

            if ($status === Pesanan::PEMBAYARAN_DITOLAK && (
                $utama->status_pembayaran !== Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI
                || ! $utama->bukti_pembayaran
                || ! $catatan
            )) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Penolakan membutuhkan bukti yang dikirim customer dan alasan penolakan.']);
            }

            if ($status === Pesanan::PEMBAYARAN_COD && $utama->metode_pembayaran !== Pesanan::METODE_COD) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Metode pembayaran pesanan ini bukan COD.']);
            }
            if (! in_array($status, [Pesanan::PEMBAYARAN_LUNAS, Pesanan::PEMBAYARAN_DITOLAK], true)) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Status pembayaran hanya dapat berubah melalui verifikasi, penolakan, atau konfirmasi COD setelah pengantaran.']);
            }

            $dibayarPada = $status === Pesanan::PEMBAYARAN_LUNAS ? now() : null;
            $items->each(fn (Pesanan $item) => $item->update([
                'status_pembayaran' => $status,
                'tanggal_pembayaran' => $dibayarPada,
                'catatan_pembayaran' => $catatan,
            ]));
        });

        $this->kirimNotifikasi(
            $pesanan,
            'payment-' . strtolower($status),
            $status === Pesanan::PEMBAYARAN_LUNAS ? 'Pembayaran terverifikasi' : 'Pembayaran perlu diperbaiki',
            $catatan ? 'Catatan admin: ' . $catatan : 'Status pembayaran diperbarui oleh admin.'
        );
    }

    private function batalkan(Pesanan $pesanan): void
    {
        if ($pesanan->status === Pesanan::STATUS_DIBATALKAN) {
            return;
        }

        DB::transaction(function () use ($pesanan) {
            $items = $this->ambilItemPesanan($pesanan);
            if ($items->isNotEmpty() && $items->every('status', Pesanan::STATUS_DIBATALKAN)) {
                return;
            }
            if ($items->isEmpty() || $items->contains(fn (Pesanan $item) => ! in_array($item->status, [
                Pesanan::STATUS_PENDING,
                Pesanan::STATUS_DIPROSES,
                Pesanan::STATUS_SIAP_DIANTAR,
            ], true))) {
                throw ValidationException::withMessages(['status' => 'Pesanan yang sudah dalam perjalanan atau selesai tidak dapat dibatalkan.']);
            }
            if ($items->pluck('status')->unique()->count() !== 1) {
                throw ValidationException::withMessages(['status' => 'Status item dalam checkout tidak konsisten; pembatalan ditolak.']);
            }
            if ($items->contains(fn (Pesanan $item) => in_array($item->status_pembayaran, [
                Pesanan::PEMBAYARAN_LUNAS,
                Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            ], true) || $item->bukti_pembayaran !== null)) {
                throw ValidationException::withMessages(['status' => 'Pembayaran sudah dikirim atau terverifikasi. Selesaikan penanganan pembayaran sebelum membatalkan pesanan.']);
            }

            $items->each(function (Pesanan $item) {
                if ($item->status === Pesanan::STATUS_DIBATALKAN) {
                    return;
                }

                $this->kembalikanStok($item);

                $item->update([
                    'status' => Pesanan::STATUS_DIBATALKAN,
                    'status_pembayaran' => Pesanan::PEMBAYARAN_DIBATALKAN,
                    'tanggal_pembayaran' => null,
                ]);
            });
        });

        $this->kirimNotifikasi($pesanan, 'order-cancelled', 'Pesanan dibatalkan', 'Pesanan telah dibatalkan oleh toko.');
    }

    private function kirimNotifikasi(Pesanan $pesanan, string $event, string $status, string $detail): void
    {
        $utama = $pesanan->kode_pesanan
            ? Pesanan::where('kode_pesanan', $pesanan->kode_pesanan)->first()
            : Pesanan::find($pesanan->id);
        if ($utama) {
            $this->whatsappNotifier->notify($utama, $event, $status, $detail);
        }
    }

    private function ambilItemPesanan(Pesanan $pesanan)
    {
        $query = Pesanan::query();

        if ($pesanan->kode_pesanan) {
            $query->where('kode_pesanan', $pesanan->kode_pesanan);
        } else {
            $query->whereKey($pesanan->id);
        }

        return $query->lockForUpdate()->get();
    }

    private function ambilProdukDenganStok(int $produkId, int $jumlah): Produk
    {
        $produk = Produk::whereKey($produkId)->lockForUpdate()->firstOrFail();

        if (! $produk->is_active) {
            throw ValidationException::withMessages([
                'produk_id' => "Produk {$produk->name_produk} sedang tidak tersedia.",
            ]);
        }

        if ($produk->stok < $jumlah) {
            throw ValidationException::withMessages([
                'jumlah' => "Stok {$produk->name_produk} tidak cukup (tersisa {$produk->stok}).",
            ]);
        }

        return $produk;
    }

    private function kembalikanStok(Pesanan $pesanan): void
    {
        if (! $pesanan->produk_id) {
            return;
        }

        $produk = Produk::whereKey($pesanan->produk_id)->lockForUpdate()->first();

        if (! $produk) {
            return;
        }

        $stokSebelum = $produk->stok;
        $produk->increment('stok', $pesanan->jumlah);
        $this->catatPerubahanStok($produk, $pesanan, 'kembali', $pesanan->jumlah, $stokSebelum, 'Stok dikembalikan dari pesanan');
    }

    private function catatPerubahanStok(
        Produk $produk,
        Pesanan $pesanan,
        string $jenis,
        int $jumlah,
        int $stokSebelum,
        string $catatan
    ): void {
        RiwayatStok::create([
            'produk_id' => $produk->id_produk,
            'pesanan_id' => $pesanan->id,
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $produk->stok,
            'catatan' => $catatan,
        ]);
    }

    private function atribut(array $data, Produk $produk, int $harga): array
    {
        return [
            'nama_pelanggan' => $data['nama_pelanggan'],
            'produk_id' => $produk->id_produk,
            'menu' => $produk->name_produk,
            'harga_satuan' => $harga,
            'jumlah' => (int) $data['jumlah'],
            'total' => $harga * (int) $data['jumlah'],
            'status' => $data['status'],
        ];
    }
}
