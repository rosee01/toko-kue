<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Kategori::pluck('id_kategori', 'nama_kategori');

        $data = [
            ['Tart Coklat Premium', 'Tart coklat lembut dengan ganache belgian chocolate, diameter 20 cm.', 185000, 8, 'Kue Tart'],
            ['Red Velvet Tart', 'Red velvet moist dengan cream cheese frosting, diameter 20 cm.', 210000, 6, 'Kue Tart'],
            ['Tiramisu Cake', 'Tiramisu dengan espresso dan mascarpone, ukuran 18 cm.', 195000, 4, 'Kue Tart'],
            ['Bolu Pandan', 'Bolu pandan alami yang lembut dan wangi.', 45000, 20, 'Bolu & Cake'],
            ['Brownies Panggang', 'Brownies panggang tebal dengan topping kacang almond.', 65000, 15, 'Bolu & Cake'],
            ['Lapis Legit Klasik', 'Lapis legit 18 lapis dengan rempah pilihan.', 250000, 5, 'Bolu & Cake'],
            ['Nastar Keju', 'Nastar isi selai nanas dengan taburan keju, toples 500 gr.', 95000, 30, 'Kue Kering'],
            ['Kastengel', 'Kastengel keju edam gurih renyah, toples 500 gr.', 105000, 25, 'Kue Kering'],
            ['Croissant Butter', 'Croissant berlapis dengan mentega premium.', 22000, 3, 'Pastry & Roti'],
            ['Roti Sobek Coklat Keju', 'Roti sobek lembut isi coklat dan keju.', 35000, 12, 'Pastry & Roti'],
        ];

        foreach ($data as [$nama, $deskripsi, $harga, $stok, $kat]) {
            Produk::updateOrCreate(
                ['name_produk' => $nama],
                [
                    'deskripsi' => $deskripsi,
                    'harga' => $harga,
                    'stok' => $stok,
                    'kategori_id' => $kategori[$kat],
                ],
            );
        }
    }
}
