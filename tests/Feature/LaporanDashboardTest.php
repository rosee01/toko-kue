<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_dashboard_menghitung_pendapatan_hanya_dari_pesanan_selesai(): void
    {
        Pesanan::factory()->create(['total' => 100000, 'status' => 'Selesai']);
        Pesanan::factory()->create(['total' => 40000, 'status' => 'Pending']);

        $this->get('/dashboard')->assertOk()->assertSee('Rp 100.000')->assertDontSee('Rp 140.000');
    }

    public function test_dashboard_menghitung_stok_menipis(): void
    {
        Produk::factory()->create(['name_produk' => 'Croissant', 'stok' => 2]);


        $this->get('/dashboard')->assertOk()->assertViewHas('stokMenipis', 1);
    }

    public function test_layout_admin_memakai_gaya_sweetalert_global(): void
    {
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('window.AppAlert = Swal.mixin', false)
            ->assertSee('app-alert-popup', false);
    }

    public function test_dashboard_menampilkan_notifikasi_pesanan_dan_stok_menipis(): void
    {
        Pesanan::factory()->create(['nama_pelanggan' => 'Siti Notifikasi']);
        Produk::factory()->create(['name_produk' => 'Roti Notifikasi', 'stok' => 2]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Notifikasi')
            ->assertSee('Pesanan baru dari Siti Notifikasi')
            ->assertSee('Roti Notifikasi menipis')
            ->assertSee('Perlu restok');
    }

    public function test_notifikasi_navbar_bisa_dibuka_dan_sidebar_tidak_menduplikasi_menu(): void
    {
        $pesanan = Pesanan::factory()->create(['nama_pelanggan' => 'Pelanggan Navbar']);
        Produk::factory()->create(['name_produk' => 'Stok Navbar', 'stok' => 1]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-label="Buka notifikasi"', false)
            ->assertSee('notification-dropdown')
            ->assertSee('Pesanan baru dari Pelanggan Navbar')
            ->assertSee('Stok Stok Navbar menipis')
            ->assertSee(route('pesanan.edit', $pesanan), false)
            ->assertSee(route('produk.index'), false)
            ->assertDontSee('href="'.route('dashboard').'#notifikasi"', false);
    }

    public function test_laporan_bisa_difilter_berdasarkan_tanggal(): void
    {
        $lama = Pesanan::factory()->create(['menu' => 'Pesanan Lama']);
        $lama->forceFill(['created_at' => now()->subDays(30)])->save();
        Pesanan::factory()->create(['menu' => 'Pesanan Baru']);

        $this->get(route('laporan.penjualan', ['dari' => now()->subDays(2)->toDateString()]))
            ->assertOk()
            ->assertSee('Pesanan Baru')
            ->assertSee('Tren pendapatan')
            ->assertSee('Produk terlaris')
            ->assertSee('Rincian pesanan')
            ->assertDontSee('Pesanan Lama');
    }

    public function test_filter_tanggal_tidak_valid_ditolak(): void
    {
        $this->get(route('laporan.penjualan', ['dari' => '2026-05-10', 'sampai' => '2026-05-01']))
            ->assertSessionHasErrors('sampai');
    }

    public function test_preview_laporan_tampil(): void
    {
        Pesanan::factory()->create(['nama_pelanggan' => 'Siti']);

        $this->get(route('laporan.preview'))->assertOk()->assertSee('Siti')->assertSee('Unduh PDF');
    }

    public function test_unduh_laporan_pdf(): void
    {
        Pesanan::factory()->create();


        $response = $this->get(route('laporan.download'));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}