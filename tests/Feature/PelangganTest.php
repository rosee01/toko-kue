<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_halaman_pelanggan_menggabungkan_akun_dan_pesanan(): void
    {
        $customer = User::factory()->create(['name' => 'Siti Pelanggan', 'email' => 'siti@example.test']);
        $produk = Produk::factory()->create();
        Pesanan::factory()->create([
            'user_id' => $customer->id,
            'kode_pesanan' => 'TK-PELANGGAN-01',
            'produk_id' => $produk->id_produk,
            'nama_pelanggan' => $customer->name,
            'no_telepon' => '081234567890',
            'status' => Pesanan::STATUS_SELESAI,
            'total' => 80000,
        ]);
        Pesanan::factory()->create([
            'user_id' => null,
            'nama_pelanggan' => 'Pelanggan Tamu',
            'status' => Pesanan::STATUS_PENDING,
        ]);

        $this->get(route('pelanggan.index'))
            ->assertOk()
            ->assertSee('Siti Pelanggan')
            ->assertSee('siti@example.test')
            ->assertSee('Pelanggan Tamu')
            ->assertSee('Riwayat Pesanan')
            ->assertViewHas('totalPelanggan', 2);
    }

    public function test_detail_pelanggan_menghitung_checkout_satu_kode_sebagai_satu_pesanan(): void
    {
        $customer = User::factory()->create(['name' => 'Budi']);
        $produk = Produk::factory()->create();
        Pesanan::factory()->count(2)->create([
            'user_id' => $customer->id,
            'kode_pesanan' => 'TK-SATU-CHECKOUT',
            'nama_pelanggan' => 'Budi',
            'produk_id' => $produk->id_produk,
            'status' => Pesanan::STATUS_SELESAI,
        ]);

        $this->get(route('pelanggan.index'))
            ->assertOk()
            ->assertViewHas('pelanggan', fn ($rows) => $rows->firstWhere('nama', 'Budi')->jumlah_pesanan === 1);
    }

    public function test_admin_bisa_mengedit_profil_akun_customer(): void
    {
        $customer = User::factory()->create(['name' => 'Budi Lama', 'email' => 'lama@example.test']);

        $this->put(route('pelanggan.update', $customer), [
            'name' => 'Budi Baru',
            'email' => 'baru@example.test',
        ])->assertRedirect(route('pelanggan.index', ['pilih' => 'akun-' . $customer->id]));

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'Budi Baru', 'email' => 'baru@example.test']);
    }

    public function test_akun_customer_bisa_dinonaktifkan_dan_tidak_bisa_login(): void
    {
        $customer = User::factory()->create(['password' => 'rahasia123', 'is_active' => true]);

        $this->patch(route('pelanggan.status', $customer))
            ->assertRedirect(route('pelanggan.index', ['pilih' => 'akun-' . $customer->id]));
        $this->assertFalse($customer->fresh()->is_active);

        $this->post(route('logout'));
        $this->post('/login', ['email' => $customer->email, 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}