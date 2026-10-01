<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pesanan;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('cari', '');

        $produk = collect();
        $pesanan = collect();
        $pelanggan = collect();

        if (!empty($keyword)) {

            // 1. Cari di Menu Kue
            $produk = Produk::where('name_produk', 'LIKE', "%{$keyword}%")
                            ->orWhere('deskripsi', 'LIKE', "%{$keyword}%")
                            ->limit(5)
                            ->get();

            // 2. Cari di Pesanan (berdasarkan nama pelanggan atau ID pesanan)
            $pesanan = Pesanan::where('nama_pelanggan', 'LIKE', "%{$keyword}%")
                              ->orWhere('id', 'LIKE', "%{$keyword}%")
                              ->orWhere('menu', 'LIKE', "%{$keyword}%")
                              ->limit(5)
                              ->get();

            // 3. Pelanggan tidak ada sebagai tabel terpisah
            // Kita ambil dari pesanan yang unik
            $pelanggan = Pesanan::where('nama_pelanggan', 'LIKE', "%{$keyword}%")
                                ->select('nama_pelanggan')
                                ->distinct()
                                ->limit(5)
                                ->get();
        }

        return view('search.index', compact('keyword', 'produk', 'pesanan', 'pelanggan'));
    }
}