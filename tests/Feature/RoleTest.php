<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_yang_membuka_root_masuk_ke_beranda(): void
    {
        $this->get('/')->assertRedirect(route('beranda'));
    }

    public function test_tamu_bisa_melihat_beranda(): void
    {
        $this->get('/beranda')->assertOk();
    }

    public function test_tamu_tidak_bisa_memesan_dan_diarahkan_ke_login(): void
    {
        $this->post('/order', [])->assertRedirect(route('login'));
    }

    public function test_admin_dari_root_diarahkan_ke_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/')->assertRedirect(route('dashboard'));
    }


    public function test_customer_dari_root_diarahkan_ke_beranda(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')->assertRedirect(route('beranda'));
    }

    public function test_customer_tidak_bisa_membuka_halaman_admin(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/dashboard', '/kategori', '/produk', '/pesanan', '/pembayaran', '/stok', '/stok/riwayat', '/drivers', '/laporan', '/pelanggan', '/pengaturan', '/search'] as $url) {
            $this->get($url)->assertRedirect(route('beranda'));
        }
    }

    public function test_login_customer_masuk_ke_beranda_dan_admin_ke_dashboard(): void
    {
        $customer = User::factory()->create(['password' => 'rahasia123']);
        $this->post('/login', ['email' => $customer->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('beranda'));
        $this->post('/logout');

        $admin = User::factory()->admin()->create(['password' => 'rahasia123']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_daftar_membuat_akun_customer_dan_langsung_masuk(): void
    {
        $this->post('/daftar', [
            'name' => 'Budi', 'email' => 'budi@contoh.test',

            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('beranda'));

        $this->assertDatabaseHas('users', ['email' => 'budi@contoh.test', 'role' => 'customer']);
        $this->assertAuthenticated();
    }
}