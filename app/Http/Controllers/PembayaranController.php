<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(private PesananService $service)
    {
    }

    public function index(Request $request): View
    {
        $labelStatus = [
            Pesanan::PEMBAYARAN_BELUM_DIBAYAR => 'Belum Dibayar',
            Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            Pesanan::PEMBAYARAN_LUNAS => 'Lunas',
            Pesanan::PEMBAYARAN_COD => 'COD',
            Pesanan::PEMBAYARAN_DITOLAK => 'Ditolak',
            Pesanan::PEMBAYARAN_DIBATALKAN => 'Dibatalkan',
        ];
        $status = $request->query('status');
        $status = is_string($status) && isset($labelStatus[$status]) ? $status : null;

        $semuaPembayaran = Pesanan::with('produk')->latest()->get()
            ->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
            ->map(function ($items) {
                $utama = $items->first();
                $statusPembayaran = $items->every('status_pembayaran', Pesanan::PEMBAYARAN_DIBATALKAN)
                    ? Pesanan::PEMBAYARAN_DIBATALKAN
                    : ($items->every('status_pembayaran', Pesanan::PEMBAYARAN_LUNAS)
                        ? Pesanan::PEMBAYARAN_LUNAS
                        : ($items->every('status_pembayaran', Pesanan::PEMBAYARAN_COD)
                            ? Pesanan::PEMBAYARAN_COD
                            : ($items->contains('status_pembayaran', Pesanan::PEMBAYARAN_DITOLAK)
                                ? Pesanan::PEMBAYARAN_DITOLAK
                                : ($items->contains('status_pembayaran', Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI)
                                    ? Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI
                                    : Pesanan::PEMBAYARAN_BELUM_DIBAYAR))));

                return (object) [
                    'key' => $utama->kode_pesanan ?: 'pesanan-' . $utama->id,
                    'kode' => $utama->kode_pesanan ?: 'PSN-' . str_pad((string) $utama->id, 6, '0', STR_PAD_LEFT),
                    'utama' => $utama,
                    'items' => $items,
                    'nama_pelanggan' => $utama->nama_pelanggan,
                    'no_telepon' => $utama->no_telepon,
                    'alamat_pengiriman' => $utama->alamat_pengiriman,
                    'status_pesanan' => $items->first(fn (Pesanan $item) => $item->status !== Pesanan::STATUS_SELESAI)?->status ?? $utama->status,
                    'status_pembayaran' => $statusPembayaran,
                    'subtotal' => $items->sum('total'),
                    'ongkir' => (int) $utama->ongkir,
                    'total' => $items->sum('total') + (int) $utama->ongkir,
                    'tanggal' => $utama->created_at,
                    'tanggal_pembayaran' => $items->max('tanggal_pembayaran'),
                    'catatan_pembayaran' => $items->pluck('catatan_pembayaran')->filter()->first(),
                    'metode_pembayaran' => $utama->label_metode_pembayaran,
                    'bukti_pembayaran' => $utama->bukti_pembayaran,
                    'pembayaran_dikirim_pada' => $utama->pembayaran_dikirim_pada,
                ];
            })
            ->values();

        $pembayaran = $semuaPembayaran
            ->when($status, fn ($items) => $items->where('status_pembayaran', $status))
            ->values();
        $jumlahPerStatus = $semuaPembayaran->countBy('status_pembayaran');
        $pembayaranTerpilih = $pembayaran->firstWhere('key', $request->query('pilih')) ?? $pembayaran->first();

        return view('pembayaran.index', [
            'pembayaran' => $pembayaran,
            'pembayaranTerpilih' => $pembayaranTerpilih,
            'statusFilter' => $status,
            'jumlahPerStatus' => $jumlahPerStatus,
            'jumlahSemua' => $semuaPembayaran->count(),
            'jumlahBelumDibayar' => $jumlahPerStatus[Pesanan::PEMBAYARAN_BELUM_DIBAYAR] ?? 0,
            'jumlahMenungguVerifikasi' => $jumlahPerStatus[Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI] ?? 0,
            'jumlahLunas' => $jumlahPerStatus[Pesanan::PEMBAYARAN_LUNAS] ?? 0,
            'jumlahCOD' => $jumlahPerStatus[Pesanan::PEMBAYARAN_COD] ?? 0,
            'jumlahDitolak' => $jumlahPerStatus[Pesanan::PEMBAYARAN_DITOLAK] ?? 0,
            'nominalTerkonfirmasi' => $semuaPembayaran->where('status_pembayaran', Pesanan::PEMBAYARAN_LUNAS)->sum('total'),
            'nominalMenunggu' => $semuaPembayaran->whereIn('status_pembayaran', [
                Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            ])->sum('total'),
            'labelStatus' => $labelStatus,
        ]);
    }

    public function bukti(Pesanan $pesanan)
    {
        abort_if(! $pesanan->bukti_pembayaran, 404);
        abort_unless(Storage::disk('local')->exists($pesanan->bukti_pembayaran), 404);

        return Storage::disk('local')->response(
            $pesanan->bukti_pembayaran,
            basename($pesanan->bukti_pembayaran),
            ['Content-Disposition' => 'inline']
        );
    }

    public function update(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $validasi = Validator::make($request->all(), [
            'status_pembayaran' => ['required', 'string', Rule::in([
                Pesanan::PEMBAYARAN_LUNAS,
                Pesanan::PEMBAYARAN_DITOLAK,
            ])],
            'catatan_pembayaran' => [Rule::requiredIf($request->input('status_pembayaran') === Pesanan::PEMBAYARAN_DITOLAK), 'nullable', 'string', 'max:255'],
        ])->validate();

        $this->service->ubahStatusPembayaran(
            $pesanan,
            $validasi['status_pembayaran'],
            $validasi['catatan_pembayaran'] ?? null
        );

        return redirect()->route('pembayaran.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id])
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}