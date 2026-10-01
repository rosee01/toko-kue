<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Driver> */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'no_telepon' => fake()->numerify('08##########'),
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => strtoupper(fake()->bothify('B #### ???')),
            'is_active' => true,
        ];
    }
}