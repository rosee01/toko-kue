<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Produk> */
class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        return [
            'name_produk' => fake()->unique()->words(3, true),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->numberBetween(10, 300) * 1000,
            'stok' => fake()->numberBetween(5, 30),
            'stok_minimum' => 3,
            'is_active' => true,
            'kategori_id' => Kategori::factory(),
            'foto' => null,
            'foto_galeri' => [],
        ];
    }
}
