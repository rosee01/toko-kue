<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        return view('laporan.penjualan', $this->data($request));
    }

    public function preview(Request $request): View
    {
        return view('laporan.pdf', $this->data($request) + ['preview' => true]);
    }

    public function download(Request $request)
    {
        $pdf = Pdf::loadView('laporan.pdf', $this->data($request) + ['preview' => false]);

        return $pdf->download('laporan-penjualan-'.now()->format('Y-m-d').'.pdf');
    }

    private function data(Request $request): array
    {
        $filter = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $dari = $filter['dari'] ?? null;
        $sampai = $filter['sampai'] ?? null;

        $pesanan = Pesanan::antara($dari, $sampai)->latest()->get();
        $grupPesanan = $pesanan->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id);
        $checkoutSelesai = $grupPesanan->filter(fn ($items) => $items->every('status', Pesanan::STATUS_SELESAI));
        $checkoutPending = $grupPesanan->filter(fn ($items) => $items->contains('status', Pesanan::STATUS_PENDING));
        $checkoutDibatalkan = $grupPesanan->filter(fn ($items) => $items->every('status', Pesanan::STATUS_DIBATALKAN));
        $itemSelesai = $pesanan->where('status', Pesanan::STATUS_SELESAI);
        $totalPendapatan = $itemSelesai->sum('total');
        $produkTerlaris = $itemSelesai
            ->groupBy('menu')
            ->map(fn ($items, $menu) => (object) ['menu' => $menu, 'jumlah' => $items->sum('jumlah'), 'total' => $items->sum('total')])
            ->sortByDesc('jumlah')
            ->take(5)
            ->values();

        $akhirGrafik = $sampai ? now()->parse($sampai)->startOfDay() : now()->startOfDay();
        $awalGrafik = $dari ? now()->parse($dari)->startOfDay() : $akhirGrafik->copy()->subDays(6);
        if ($awalGrafik->diffInDays($akhirGrafik) > 29) {
            $awalGrafik = $akhirGrafik->copy()->subDays(29);
        }
        $labelsGrafik = [];
        $nilaiGrafik = [];
        foreach (CarbonPeriod::create($awalGrafik, '1 day', $akhirGrafik) as $tanggal) {
            $labelsGrafik[] = $tanggal->format('d M');
            $nilaiGrafik[] = $itemSelesai->filter(fn (Pesanan $item) => $item->created_at?->isSameDay($tanggal))->sum('total');
        }

        return [
            'pesanan' => $pesanan,
            'dari' => $dari,
            'sampai' => $sampai,
            'totalPesanan' => $grupPesanan->count(),
            'pesananSelesai' => $checkoutSelesai->count(),
            'pesananMenunggu' => $checkoutPending->count(),
            'pesananDibatalkan' => $checkoutDibatalkan->count(),
            'produkTerjual' => $itemSelesai->sum('jumlah'),
            'rataRataTransaksi' => $checkoutSelesai->count() ? round($totalPendapatan / $checkoutSelesai->count()) : 0,
            'totalPendapatan' => $totalPendapatan,
            'produkTerlaris' => $produkTerlaris,
            'labelsGrafik' => $labelsGrafik,
            'nilaiGrafik' => $nilaiGrafik,
        ];
    }
}
