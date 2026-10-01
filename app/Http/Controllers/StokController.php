<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StokController extends Controller
{
    public function riwayat(): View
    {
        $riwayat = RiwayatStok::with(['produk.kategori', 'pesanan'])->latest()->get();
        $totalRiwayat = $riwayat->count();
        $jumlahStokMasuk = $riwayat->filter(fn (RiwayatStok $item) => $item->jenis !== 'keluar')->sum('jumlah');
        $jumlahStokKeluar = $riwayat->where('jenis', 'keluar')->sum('jumlah');
        $perubahanHariIni = $riwayat->filter(fn (RiwayatStok $item) => $item->created_at?->isToday())->count();

        return view('stok.riwayat', compact(
            'riwayat',
            'totalRiwayat',
            'jumlahStokMasuk',
            'jumlahStokKeluar',
            'perubahanHariIni'
        ));
    }

    public function index(): View
    {
        $produks = Produk::with('kategori')->orderBy('name_produk')->get();
        $totalProduk = $produks->count();
        $kategoris = Kategori::where('is_active', true)->orderBy('nama_kategori')->get();
        $stokAman = $produks->filter(fn (Produk $produk) => $produk->stok > $produk->stok_minimum)->count();
        $stokMenipis = $produks->filter(fn (Produk $produk) => $produk->stok > 0 && $produk->stok <= $produk->stok_minimum)->count();
        $stokHabis = $produks->where('stok', 0)->count();
        $produkPerhatian = $produks
            ->filter(fn (Produk $produk) => $produk->stok <= $produk->stok_minimum)
            ->sortBy('stok')
            ->take(4);
        $jumlahPerhatian = $produks->filter(fn (Produk $produk) => $produk->stok <= $produk->stok_minimum)->count();
        $tampilkanSemuaRiwayat = request()->boolean('riwayat');
        $queryRiwayat = RiwayatStok::with(['produk', 'pesanan'])->latest();
        $riwayatTerbaru = $tampilkanSemuaRiwayat
            ? $queryRiwayat->limit(30)->get()
            : $queryRiwayat->limit(4)->get();

        return view('stok.index', compact(
            'produks',
            'totalProduk',
            'kategoris',
            'stokAman',
            'stokMenipis',
            'stokHabis',
            'produkPerhatian',
            'jumlahPerhatian',
            'riwayatTerbaru',
            'tampilkanSemuaRiwayat'
        ));
    }

    public function tambah(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produk_id' => ['required', 'exists:produk,id_produk'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        DB::transaction(function () use ($data): void {
            $produk = Produk::whereKey($data['produk_id'])->lockForUpdate()->firstOrFail();
            $stokSebelum = $produk->stok;
            $jumlah = (int) $data['jumlah'];
            $produk->increment('stok', (int) $data['jumlah']);

            RiwayatStok::create([
                'produk_id' => $produk->id_produk,
                'jenis' => 'manual',
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSebelum + $jumlah,
                'catatan' => 'Stok manual',
            ]);
        });

        return redirect()->route('stok.index')->with('success', 'Stok produk berhasil ditambahkan.');
    }
}