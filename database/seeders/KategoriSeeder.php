<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Kue Tart', 'Bolu & Cake', 'Kue Kering', 'Pastry & Roti'] as $nama) {
            Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
