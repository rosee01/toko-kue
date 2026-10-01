<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\PesananService;
use App\Services\TarifPengiriman;
use App\Services\WhatsAppOrderNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private PesananService $service,
        private TarifPengiriman $tarifPengiriman,
        private WhatsAppOrderNotifier $whatsappNotifier
    )
    {
    }

    // Pesan satu produk lewat form modal di beranda
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produk_id' => ['required', 'exists:produk,id_produk'],
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'alamat_pengiriman' => ['required', 'string'],
            'metode_pembayaran' => ['required', 'string', 'in:'.implode(',', Pesanan::METODE_PEMBAYARAN)],
            'jenis_pengiriman' => ['required', 'string', 'in:'.implode(',', array_keys(config('delivery.services')))],
            'jadwal_diminta' => ['required', 'date', 'after:'.now()->addHour()->format('Y-m-d H:i:s'), 'before:2 weeks'],
            'izin_notifikasi_whatsapp' => ['sometimes', 'accepted'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:1000'],
            'catatan' => ['nullable', 'string'],
        ]);

        try {
            $kode = $this->buatPesanan(
                [['produk_id' => $data['produk_id'], 'jumlah' => $data['jumlah']]],
                $data,
                $request->user()->id
            );
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('order.sukses', ['kode' => $kode]);
    }

    // Checkout seluruh isi keranjang (dikirim lewat fetch/JSON)
    public function keranjang(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'alamat_pengiriman' => ['required', 'string'],
            'metode_pembayaran' => ['required', 'string', 'in:'.implode(',', Pesanan::METODE_PEMBAYARAN)],
            'jenis_pengiriman' => ['required', 'string', 'in:'.implode(',', array_keys(config('delivery.services')))],
            'jadwal_diminta' => ['required', 'date', 'after:'.now()->addHour()->format('Y-m-d H:i:s'), 'before:2 weeks'],
            'izin_notifikasi_whatsapp' => ['sometimes', 'accepted'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.produk_id' => ['required', 'exists:produk,id_produk'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        try {
            $kode = $this->buatPesanan($data['items'], $data, $request->user()->id);
        } catch (ValidationException $e) {
            return response()->json(['message' => collect($e->errors())->flatten()->first(), 'errors' => $e->errors()], 422);
        }

        return response()->json(['redirect' => route('order.sukses', ['kode' => $kode])]);
    }

    public function hitungOngkir(Request $request): JsonResponse
    {
        $data = $request->validate([
            'alamat_pengiriman' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        return response()->json($this->tarifPengiriman->options($data['alamat_pengiriman']));
    }

    public function sukses(Request $request): View|RedirectResponse
    {
        $kode = $request->query('kode');

        if (! is_string($kode) || $kode === '') {
            return redirect()->route('order.riwayat');
        }

        $pesanan = Pesanan::with('produk')
            ->where('user_id', $request->user()->id)
            ->where('kode_pesanan', $kode)
            ->orderBy('id')
            ->get();

        abort_if($pesanan->isEmpty(), 404);

        return view('order.sukses', [
            'kode' => $kode,
            'pesanan' => $pesanan,
            'subtotal' => $pesanan->sum('total'),
            'ongkir' => (int) $pesanan->first()->ongkir,
            'total' => $pesanan->sum('total') + (int) $pesanan->first()->ongkir,
            'dibuatPada' => $pesanan->first()->created_at,
            'informasiPembayaran' => [
                'bank' => [
                    'nama' => \App\Models\Pengaturan::ambil('bank_nama'),
                    'rekening' => \App\Models\Pengaturan::ambil('bank_rekening'),
                    'pemilik' => \App\Models\Pengaturan::ambil('bank_pemilik'),
                ],
                'ewallet' => [
                    'provider' => \App\Models\Pengaturan::ambil('ewallet_provider'),
                    'nomor' => \App\Models\Pengaturan::ambil('ewallet_nomor'),
                    'pemilik' => \App\Models\Pengaturan::ambil('ewallet_pemilik'),
                ],
                'whatsapp' => \App\Models\Pengaturan::ambil('whatsapp'),
            ],
        ]);
    }

    public function unggahBuktiPembayaran(Request $request, string $kode): RedirectResponse
    {
        $data = $request->validate([
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'bukti_pembayaran.required' => 'Pilih foto bukti pembayaran terlebih dahulu.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format bukti yang didukung: JPG, PNG, atau WEBP.',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
        ]);

        $pesanan = Pesanan::query()
            ->where('user_id', $request->user()->id)
            ->where('kode_pesanan', $kode)
            ->firstOrFail();
        $items = Pesanan::query()->where('kode_pesanan', $kode)->get();
        abort_if(! in_array($pesanan->metode_pembayaran, [Pesanan::METODE_TRANSFER_BANK, Pesanan::METODE_E_WALLET], true), 422);
        abort_if($items->contains(fn (Pesanan $item) => ! $item->ongkir_dikonfirmasi || ($item->jenis_pengiriman !== null && ! $item->jadwal_dikonfirmasi)), 422, 'Tunggu admin mengonfirmasi ongkos kirim dan jadwal sebelum melakukan pembayaran.');
        abort_if($items->contains(fn (Pesanan $item) => ! in_array($item->status_pembayaran, [
            Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
            Pesanan::PEMBAYARAN_DITOLAK,
        ], true)), 422, 'Pesanan ini tidak dapat menerima bukti pembayaran baru.');

        $path = Storage::disk('local')->putFile('bukti-pembayaran', $data['bukti_pembayaran']);
        if (! $path) {
            throw new \RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        $buktiLama = $pesanan->bukti_pembayaran;
        try {
            DB::transaction(function () use ($kode, $path): void {
                $itemsTerkunci = Pesanan::query()->where('kode_pesanan', $kode)->lockForUpdate()->get();
                if ($itemsTerkunci->isEmpty() || $itemsTerkunci->contains(fn (Pesanan $item) => ! in_array($item->status_pembayaran, [
                    Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    Pesanan::PEMBAYARAN_DITOLAK,
                ], true))) {
                    abort(422, 'Pesanan ini tidak dapat menerima bukti pembayaran baru.');
                }

                $itemsTerkunci->each(fn (Pesanan $item) => $item->update([
                    'bukti_pembayaran' => $path,
                    'pembayaran_dikirim_pada' => now(),
                    'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
                    'catatan_pembayaran' => null,
                ]));
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($buktiLama && $buktiLama !== $path) {
            Storage::disk('local')->delete($buktiLama);
        }

        $this->whatsappNotifier->notify(
            $pesanan->fresh(),
            'payment-proof',
            'Bukti pembayaran diterima',
            'Admin akan meninjau bukti pembayaran Anda.'
        );

        return back()->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }

    // Riwayat pesanan milik customer yang sedang login.
    // Item dari satu checkout (kode pesanan sama) digabung menjadi satu kartu.
    public function riwayat(Request $request): View
    {
        $orders = Pesanan::with(['produk', 'driver'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (Pesanan $p) => $p->kode_pesanan ?? 'id-' . $p->id)
            ->map(function (Collection $items) {
                $pertama = $items->first();

                return [
                    'kode' => $pertama->kode_pesanan ?? 'PSN-' . str_pad((string) $pertama->id, 4, '0', STR_PAD_LEFT),
                    'tanggal' => $pertama->created_at,
                    'status' => $this->statusGabungan($items),
                    'subtotal' => $items->sum('total'),
                    'ongkir' => (int) $pertama->ongkir,
                    'total' => $items->sum('total') + (int) $pertama->ongkir,
                    'items' => $items,
                    'nama' => $pertama->nama_pelanggan,
                    'telepon' => $pertama->no_telepon,
                    'alamat' => $pertama->alamat_pengiriman,
                    'catatan' => $pertama->catatan,
                    'metode_pembayaran' => $pertama->metode_pembayaran,
                    'label_metode_pembayaran' => $pertama->label_metode_pembayaran,
                    'status_pembayaran' => $pertama->status_pembayaran,
                    'bukti_pembayaran' => $pertama->bukti_pembayaran,
                    'pembayaran_dikirim_pada' => $pertama->pembayaran_dikirim_pada,
                    'catatan_pembayaran' => $pertama->catatan_pembayaran,
                    'driver' => $pertama->driver,
                    'ongkir_dikonfirmasi' => $pertama->ongkir_dikonfirmasi,
                    'jenis_pengiriman' => $pertama->jenis_pengiriman,
                    'jarak_pengiriman_km' => $pertama->jarak_pengiriman_km,
                    'jadwal_diminta' => $pertama->jadwal_diminta,
                    'jadwal_pengiriman' => $pertama->jadwal_pengiriman,
                    'jadwal_dikonfirmasi' => $pertama->jadwal_dikonfirmasi,
                ];
            })
            ->values();

        return view('pesanan-saya', [
            'orders' => $orders,
            'informasiPembayaran' => [
                'bank' => [
                    'nama' => \App\Models\Pengaturan::ambil('bank_nama'),
                    'rekening' => \App\Models\Pengaturan::ambil('bank_rekening'),
                    'pemilik' => \App\Models\Pengaturan::ambil('bank_pemilik'),
                ],
                'ewallet' => [
                    'provider' => \App\Models\Pengaturan::ambil('ewallet_provider'),
                    'nomor' => \App\Models\Pengaturan::ambil('ewallet_nomor'),
                    'pemilik' => \App\Models\Pengaturan::ambil('ewallet_pemilik'),
                ],
            ],
        ]);
    }

    // Status satu kartu: ikut item yang paling belum selesai.
    private function statusGabungan(Collection $items): string
    {
        foreach ([
            Pesanan::STATUS_PENDING,
            Pesanan::STATUS_DIPROSES,
            Pesanan::STATUS_SIAP_DIANTAR,
            Pesanan::STATUS_DALAM_PENGANTARAN,
            Pesanan::STATUS_DIBATALKAN,
        ] as $status) {
            if ($items->contains('status', $status)) {
                return $status;
            }
        }

        return Pesanan::STATUS_SELESAI;
    }

    /**
     * Semua item satu checkout dibuat dalam satu transaksi dan memakai satu kode pesanan.
     * Kalau satu gagal (mis. stok kurang), semuanya dibatalkan.
     * Harga dan stok selalu diambil dari database.
     */
    private function buatPesanan(array $items, array $kontak, int $userId): string
    {
        $kode = 'TK-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));
        $tarif = $this->tarifPengiriman->quote($kontak['alamat_pengiriman'], $kontak['jenis_pengiriman']);

        DB::transaction(function () use ($items, $kontak, $userId, $kode, $tarif) {
            foreach ($items as $item) {
                $pesanan = $this->service->buat([
                    'nama_pelanggan' => $kontak['nama_pelanggan'],
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'status' => 'Pending',
                ]);

                $pesanan->update([
                    'user_id' => $userId,
                    'kode_pesanan' => $kode,
                    'no_telepon' => $kontak['no_telepon'],
                    'alamat_pengiriman' => $kontak['alamat_pengiriman'],
                    'catatan' => $kontak['catatan'] ?? null,
                    'metode_pembayaran' => $kontak['metode_pembayaran'],
                    'ongkir' => $tarif['fee'],
                    'zona_pengiriman' => null,
                    'jenis_pengiriman' => $kontak['jenis_pengiriman'],
                    'jarak_pengiriman_km' => $tarif['distance_km'],
                    'status_pembayaran' => $kontak['metode_pembayaran'] === Pesanan::METODE_COD
                        ? Pesanan::PEMBAYARAN_COD
                        : Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    'ongkir_dikonfirmasi' => false,
                    'jadwal_diminta' => $kontak['jadwal_diminta'],
                    'jadwal_pengiriman' => null,
                    'jadwal_dikonfirmasi' => false,
                    'izin_notifikasi_whatsapp' => (bool) ($kontak['izin_notifikasi_whatsapp'] ?? false),
                ]);
            }
        });
        $this->whatsappNotifier->notify(
            Pesanan::where('kode_pesanan', $kode)->firstOrFail(),
            'order-created',
            'Pesanan diterima',
            'Menunggu admin mengonfirmasi ongkir dan jadwal pengiriman.'
        );

        return $kode;
    }
}