<?php

namespace App\Http\Controllers;

use App\Http\Requests\PesananRequest;
use App\Models\Driver;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function __construct(private PesananService $service)
    {
    }

    public function index(Request $request): View
    {
        $labelStatus = [
            Pesanan::STATUS_PENDING => 'Pesanan Baru',
            Pesanan::STATUS_DIPROSES => 'Diproses',
            Pesanan::STATUS_SIAP_DIANTAR => 'Siap Diantar',
            Pesanan::STATUS_DALAM_PENGANTARAN => 'Dalam Pengantaran',
            Pesanan::STATUS_SELESAI => 'Selesai',
            Pesanan::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
        $status = $request->query('status');
        $status = is_string($status) && isset($labelStatus[$status]) ? $status : null;

        $semuaItem = Pesanan::with(['produk', 'driver'])
            ->latest()
            ->get()
            ->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
            ->map(function ($items) use ($labelStatus) {
                $utama = $items->first();
                $statusTampil = collect(array_keys($labelStatus))
                    ->first(fn ($label) => $items->contains('status', $label), $utama->status);
                $statusPembayaran = $items->every('status_pembayaran', Pesanan::PEMBAYARAN_LUNAS)
                    ? Pesanan::PEMBAYARAN_LUNAS
                    : ($items->contains('status_pembayaran', Pesanan::PEMBAYARAN_DITOLAK)
                        ? Pesanan::PEMBAYARAN_DITOLAK
                        : $utama->status_pembayaran);

                return (object) [
                    'key' => $utama->kode_pesanan ?: 'pesanan-' . $utama->id,
                    'kode' => $utama->kode_pesanan ?: 'PSN-' . str_pad((string) $utama->id, 6, '0', STR_PAD_LEFT),
                    'utama' => $utama,
                    'items' => $items,
                    'nama_pelanggan' => $utama->nama_pelanggan,
                    'no_telepon' => $utama->no_telepon,
                    'alamat_pengiriman' => $utama->alamat_pengiriman,
                    'catatan' => $utama->catatan,
                    'status' => $statusTampil,
                    'status_pembayaran' => $statusPembayaran,
                    'metode_pembayaran' => $utama->label_metode_pembayaran,
                    'subtotal' => $items->sum('total'),
                    'ongkir' => (int) $utama->ongkir,
                    'jenis_pengiriman' => $utama->jenis_pengiriman,
                    'jarak_pengiriman_km' => $utama->jarak_pengiriman_km,
                    'jadwal_diminta' => $utama->jadwal_diminta,
                    'jadwal_pengiriman' => $utama->jadwal_pengiriman,
                    'jadwal_dikonfirmasi' => $utama->jadwal_dikonfirmasi,
                    'total' => $items->sum('total') + (int) $utama->ongkir,
                    'created_at' => $utama->created_at,
                    'driver' => $utama->driver,
                    'bukti_pembayaran' => $utama->bukti_pembayaran,
                ];
            })
            ->values();

        $pesanan = $semuaItem
            ->when($status, fn ($items) => $items->where('status', $status))
            ->values();
        $jumlahPerStatus = $semuaItem->countBy('status');
        $pesananTerpilih = $pesanan->firstWhere('key', $request->query('pilih')) ?? $pesanan->first();

        return view('pesanan.index', [
            'pesanan' => $pesanan,
            'statusFilter' => $status,
            'statusLabel' => $status ? $labelStatus[$status] : null,
            'labelStatus' => $labelStatus,
            'jumlahPerStatus' => $jumlahPerStatus,
            'jumlahSemua' => $semuaItem->count(),
            'jumlahMenungguPembayaran' => $semuaItem->where('status_pembayaran', Pesanan::PEMBAYARAN_BELUM_DIBAYAR)->count(),
            'jumlahDriverAktif' => Driver::where('is_active', true)->count(),
            'driversAktif' => Driver::where('is_active', true)
                ->whereDoesntHave('pesanan', fn ($query) => $query->where('status', Pesanan::STATUS_DALAM_PENGANTARAN))
                ->orderBy('nama')
                ->get(),
            'pesananTerpilih' => $pesananTerpilih,
        ]);
    }

    public function ubahStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', [...Pesanan::STATUSES, Pesanan::STATUS_DIBATALKAN])],
        ]);

        $this->service->ubahStatus($pesanan, $data['status']);

        return redirect()->route('pesanan.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id])
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function ubahOngkir(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'ongkir' => ['required', 'integer', 'min:0', 'max:5000000'],
            'jadwal_pengiriman' => [
                Rule::requiredIf($pesanan->jenis_pengiriman !== null),
                'nullable',
                'date',
                'after:now',
                'before:2 weeks',
            ],
        ]);

        $this->service->ubahOngkir($pesanan, $data['ongkir'], $data['jadwal_pengiriman'] ?? null);

        return redirect()->route('pesanan.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id])
            ->with('success', 'Ongkos kirim dan jadwal pengiriman berhasil dikonfirmasi kepada customer.');
    }

    public function ubahPembayaran(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'status_pembayaran' => ['required', 'string', 'in:' . implode(',', [
                Pesanan::PEMBAYARAN_LUNAS,
                Pesanan::PEMBAYARAN_DITOLAK,
            ])],
        ]);

        $this->service->ubahStatusPembayaran($pesanan, $data['status_pembayaran']);

        return redirect()->route('pesanan.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id])
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function create(): View
    {
        return view('pesanan.create', ['products' => Produk::orderBy('name_produk')->get()]);
    }

    public function store(PesananRequest $request): RedirectResponse
    {
        $this->service->buat($request->validated());

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil ditambahkan.');
    }

    public function edit(Pesanan $pesanan): View
    {
        return view('pesanan.edit', [
            'pesanan' => $pesanan,
            'products' => Produk::orderBy('name_produk')->get(),
        ]);
    }

    public function update(PesananRequest $request, Pesanan $pesanan): RedirectResponse
    {
        $this->service->ubah($pesanan, $request->validated());

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(Pesanan $pesanan): RedirectResponse
    {
        $this->service->hapus($pesanan);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus dan stok dikembalikan.');
    }
}
