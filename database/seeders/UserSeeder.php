<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo. Ganti password ini sebelum dipakai di server sungguhan.
        $akun = [
            ['admin@tokokue.test', 'Admin Toko Kue', 'admin'],
            ['pelanggan@tokokue.test', 'Pelanggan Demo', 'customer'],
        ];

        foreach ($akun as [$email, $nama, $role]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->forceFill(['name' => $nama, 'password' => 'password', 'role' => $role])->save();
        }
    }
}