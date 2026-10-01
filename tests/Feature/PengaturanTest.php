<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengaturanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['password' => 'password-lama']);
        $this->actingAs($this->admin);
    }

    public function test_halaman_pengaturan_menjelaskan_bagian_yang_dapat_diatur(): void
    {
        $this->get(route('pengaturan.index'))
            ->assertOk()
            ->assertSee('Profil toko')
            ->assertSee('Kontak & lokasi')
            ->assertSee('Metode pembayaran')
            ->assertSee('Tarif per jenis pengiriman')
            ->assertSee('Nomor rekening')
            ->assertSee('Banner etalase')
            ->assertSee('Keamanan akun admin')
            ->assertSee('Yang bisa diatur')
            ->assertSee('Alamat asal toko:')
            ->assertSee('belum diisi. Lengkapi pada bagian Kontak & lokasi.')
            ->assertSee('GOOGLE_MAPS_SERVER_KEY');
    }

    public function test_admin_dapat_menyimpan_informasi_toko(): void
    {
        $this->put(route('pengaturan.update'), [
            'nama_toko' => 'Dapur Manis',
            'slogan' => 'Kue untuk setiap momen',
            'whatsapp' => '6281234567890',
            'alamat' => 'Jl. Mawar No. 10',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengaturan', ['kunci' => 'nama_toko', 'nilai' => 'Dapur Manis']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'slogan', 'nilai' => 'Kue untuk setiap momen']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'whatsapp', 'nilai' => '6281234567890']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'alamat', 'nilai' => 'Jl. Mawar No. 10']);
    }

    public function test_admin_dapat_mengubah_tarif_pengiriman(): void
    {
        $this->put(route('pengaturan.update'), [
            'nama_toko' => 'Dapur Manis',
            'tarif_per_km_cepat' => 9000,
            'tarif_per_km_hemat' => 17500,
            'gratis_sampai_km_hemat' => 3,
            'tarif_per_km_lambat' => 500,
        ])->assertRedirect();

        $this->assertDatabaseHas('pengaturan', ['kunci' => 'tarif_per_km_cepat', 'nilai' => '9000']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'tarif_per_km_hemat', 'nilai' => '17500']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'gratis_sampai_km_hemat', 'nilai' => '3']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'tarif_per_km_lambat', 'nilai' => '500']);
    }

    public function test_admin_dapat_menyimpan_informasi_tujuan_pembayaran(): void
    {
        $this->put(route('pengaturan.update'), [
            'nama_toko' => 'Dapur Manis',
            'bank_nama' => 'BCA',
            'bank_rekening' => '1234567890',
            'bank_pemilik' => 'Dapur Manis',
            'ewallet_provider' => 'DANA',
            'ewallet_nomor' => '081234567890',
            'ewallet_pemilik' => 'Dapur Manis',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengaturan', ['kunci' => 'bank_nama', 'nilai' => 'BCA']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'bank_rekening', 'nilai' => '1234567890']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'bank_pemilik', 'nilai' => 'Dapur Manis']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'ewallet_provider', 'nilai' => 'DANA']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'ewallet_nomor', 'nilai' => '081234567890']);
        $this->assertDatabaseHas('pengaturan', ['kunci' => 'ewallet_pemilik', 'nilai' => 'Dapur Manis']);
    }

    public function test_admin_dapat_mengganti_password(): void
    {
        $this->from(route('pengaturan.index'))
            ->put(route('pengaturan.password'), [
                'password_lama' => 'password-lama',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect(route('pengaturan.index'))
            ->assertSessionHas('success', 'Password berhasil diganti.');

        $this->assertTrue(Hash::check('password-baru-123', $this->admin->fresh()->password));
    }

    public function test_nomor_whatsapp_tidak_valid_ditolak(): void
    {
        $this->from(route('pengaturan.index'))
            ->put(route('pengaturan.update'), [
                'nama_toko' => 'Dapur Manis',
                'whatsapp' => '+6281234567890',
            ])
            ->assertRedirect(route('pengaturan.index'))
            ->assertSessionHasErrors('whatsapp');

        $this->assertNull(Pengaturan::ambil('nama_toko'));
    }
}
