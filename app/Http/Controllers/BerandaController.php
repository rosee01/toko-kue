<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pesanan;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $kontakTerakhir = auth()->check()
            ? Pesanan::query()
                ->where('user_id', auth()->id())
                ->latest('created_at')
                ->latest('id')
                ->first(['nama_pelanggan', 'no_telepon', 'alamat_pengiriman'])
            : null;
        $kategoris = Kategori::where('is_active', true)
            ->withCount(['produk as produk_aktif_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('nama_kategori')
            ->get();
        $menu = Produk::where('is_active', true)
            ->whereHas('kategori', fn ($query) => $query->where('is_active', true))
            ->with('kategori')
            ->orderBy('name_produk')
            ->get()
            ->groupBy(fn (Produk $p) => $p->kategori?->nama_kategori ?? 'Lainnya')
            ->sortKeys();

        return view('beranda', [
            'menu' => $menu,
            'totalMenu' => $menu->flatten()->count(),
            'totalKategori' => $kategoris->count(),
            'totalStokTersedia' => $menu->flatten()->where('stok', '>', 0)->count(),
            'kategoris' => $kategoris,
            'kontakTerakhir' => $kontakTerakhir,
            'informasiPembayaran' => [
                'bank' => [
                    'nama' => Pengaturan::ambil('bank_nama'),
                    'rekening' => Pengaturan::ambil('bank_rekening'),
                    'pemilik' => Pengaturan::ambil('bank_pemilik'),
                ],
                'ewallet' => [
                    'provider' => Pengaturan::ambil('ewallet_provider'),
                    'nomor' => Pengaturan::ambil('ewallet_nomor'),
                    'pemilik' => Pengaturan::ambil('ewallet_pemilik'),
                ],
            ],
            'toko' => [
                'nama' => Pengaturan::ambil('nama_toko', config('app.name')),
                'slogan' => Pengaturan::ambil('slogan', 'Manisnya Setiap Momen'),
                'whatsapp' => Pengaturan::ambil('whatsapp', config('toko.whatsapp')),
                'alamat' => Pengaturan::ambil('alamat', config('toko.alamat')),
                'banner' => Pengaturan::ambil('banner'),
            ],
        ]);
    }
}