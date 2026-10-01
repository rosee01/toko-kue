<?php

namespace Database\Factories;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Pesanan> */
class PesananFactory extends Factory
{
    protected $model = Pesanan::class;

    public function definition(): array
    {
        $produk = Produk::factory()->create();
        $jumlah = fake()->numberBetween(1, 3);

        return [
            'nama_pelanggan' => fake()->name(),
            'produk_id' => $produk->id_produk,
            'menu' => $produk->name_produk,
            'harga_satuan' => $produk->harga,
            'jumlah' => $jumlah,
            'total' => $produk->harga * $jumlah,
            'status' => Pesanan::STATUS_PENDING,
        ];
    }
}
