<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\Pengaturan;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    // Halaman keranjang. Data harga dan stok terbaru dikirim supaya keranjang
    // di browser selalu disesuaikan dengan kondisi toko saat ini.
    public function index(): View
    {
        $fotoDefault = asset('images/login-hero.jpg');
        $kontakTerakhir = Pesanan::query()
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->latest('id')
            ->first(['nama_pelanggan', 'no_telepon', 'alamat_pengiriman']);
        $produk = Produk::where('is_active', true)
            ->whereHas('kategori', fn ($query) => $query->where('is_active', true))
            ->orderBy('name_produk')
            ->get()
            ->map(fn (Produk $p) => [
            'id' => $p->id_produk,
            'nama' => $p->name_produk,
            'harga' => (int) $p->harga,
            'stok' => (int) $p->stok,
            'foto' => $p->foto ? asset('storage/' . $p->foto) : $fotoDefault,
            'foto_default' => ! $p->foto,
        ])->values();

        $informasiPembayaran = [
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
        ];
        $alamatToko = Pengaturan::ambil('alamat', config('delivery.origin'));

        return view('keranjang', compact('produk', 'kontakTerakhir', 'informasiPembayaran', 'alamatToko'));
    }
}