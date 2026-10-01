<?php

namespace Database\Seeders;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        if (Pesanan::exists()) {
            return;
        }

        $contoh = [
            ['Ni Made Ayu', 'Tart Coklat Premium', 1, Pesanan::STATUS_SELESAI, 9],
            ['Budi Santoso', 'Nastar Keju', 2, Pesanan::STATUS_SELESAI, 7],
            ['Siti Aminah', 'Lapis Legit Klasik', 1, Pesanan::STATUS_SELESAI, 6],
            ['Kadek Dwi', 'Brownies Panggang', 3, Pesanan::STATUS_SELESAI, 4],
            ['Rina Wulandari', 'Red Velvet Tart', 1, Pesanan::STATUS_DIPROSES, 2],
            ['Andi Pratama', 'Roti Sobek Coklat Keju', 4, Pesanan::STATUS_DIPROSES, 1],
            ['Dewi Lestari', 'Kastengel', 2, Pesanan::STATUS_PENDING, 0],
            ['Made Surya', 'Bolu Pandan', 2, Pesanan::STATUS_PENDING, 0],
        ];

        foreach ($contoh as [$pelanggan, $menu, $jumlah, $status, $hariLalu]) {
            $produk = Produk::where('name_produk', $menu)->first();
            if (! $produk) {
                continue;
            }

            $pesanan = Pesanan::create([
                'nama_pelanggan' => $pelanggan,
                'produk_id' => $produk->id_produk,
                'menu' => $produk->name_produk,
                'harga_satuan' => $produk->harga,
                'jumlah' => $jumlah,
                'total' => $produk->harga * $jumlah,
                'status' => $status,
            ]);

            $pesanan->forceFill([
                'created_at' => now()->subDays($hariLalu),
                'updated_at' => now()->subDays($hariLalu),
            ])->save();
        }
    }
}
