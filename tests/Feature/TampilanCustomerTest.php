<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TampilanCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_menampilkan_sisa_stok_dan_menandai_stok_habis(): void
    {
        Produk::factory()->create(['name_produk' => 'Roti Sisa Tiga', 'stok' => 3]);
        Produk::factory()->create(['name_produk' => 'Roti Kosong', 'stok' => 0]);

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('Stok: 3')
            ->assertSee('Stok habis');
    }

    public function test_etalase_hanya_menampilkan_produk_dan_kategori_aktif(): void
    {
        $kategoriAktif = Kategori::factory()->create(['nama_kategori' => 'Kategori Aktif', 'is_active' => true]);
        $kategoriNonaktif = Kategori::factory()->create(['nama_kategori' => 'Kategori Nonaktif', 'is_active' => false]);
        Produk::factory()->create([
            'name_produk' => 'Kue Etalase Aktif',
            'kategori_id' => $kategoriAktif->id_kategori,
            'is_active' => true,
        ]);
        Produk::factory()->create([
            'name_produk' => 'Kue Kategori Nonaktif',
            'kategori_id' => $kategoriNonaktif->id_kategori,
            'is_active' => true,
        ]);
        Produk::factory()->create([
            'name_produk' => 'Kue Produk Nonaktif',
            'kategori_id' => $kategoriAktif->id_kategori,
            'is_active' => false,
        ]);

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('Kue Etalase Aktif')
            ->assertSee('Kategori Aktif')
            ->assertDontSee('Kue Kategori Nonaktif')
            ->assertDontSee('Kue Produk Nonaktif')
            ->assertDontSee('Kategori Nonaktif');
    }

    public function test_beranda_menggunakan_informasi_toko_dan_menghapus_kontak_dummy(): void
    {
        Pengaturan::simpan('nama_toko', 'Dapur Ceria');
        Pengaturan::simpan('slogan', 'Kue untuk hari istimewa');
        Pengaturan::simpan('whatsapp', '6281234567890');
        Pengaturan::simpan('alamat', 'Jl. Mawar No. 8');
        Pengaturan::simpan('banner', 'banner/etalase.jpg');

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('<title>Dapur Ceria - Kue untuk hari istimewa</title>', false)
            ->assertSee('Dapur Ceria')
            ->assertSee('Jl. Mawar No. 8')
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('storage/banner/etalase.jpg')
            ->assertDontSee('0812-3456-7890')
            ->assertDontSee('info@tokokue.com')
            ->assertDontSee('500+');
    }

    public function test_customer_menggunakan_gambar_kue_lokal_sebagai_fallback(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('images/login-hero.jpg')
            ->assertSee('images/login-hero.jpg')
            ->assertDontSee('images.unsplash.com');

        Produk::factory()->create(['foto' => null]);

        $this->actingAs(User::factory()->create())
            ->get(route('keranjang'))
            ->assertOk()
            ->assertSee('login-hero.jpg');

        $this->assertFileExists(public_path('images/login-hero.jpg'));
    }

    public function test_navigasi_customer_menyediakan_menu_mobile(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('beranda'))
            ->assertOk()
            ->assertSee('data-bs-target="#customerMobileNav"', false)
            ->assertSee('id="customerMobileNav"', false)
            ->assertSee('Pesanan</a>', false);
    }

    public function test_form_pemesanan_ringkas_dan_memakai_gaya_sweetalert_global(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('beranda'))
            ->assertOk()
            ->assertSee('order-modal-dialog', false)
            ->assertSee('order-modal-body', false)
            ->assertSee('order-product-summary', false)
            ->assertSee('aria-labelledby="orderModalTitle"', false)
            ->assertSee('Transfer bank')
            ->assertSee('E-wallet')
            ->assertSee('COD')
            ->assertSee('window.AppAlert = Swal.mixin', false)
            ->assertSee('app-alert-popup', false);
    }

    public function test_tamu_diarahkan_ke_login_dari_keranjang_dan_pesanan_saya(): void
    {
        $this->get(route('keranjang'))->assertRedirect(route('login'));
        $this->get(route('order.riwayat'))->assertRedirect(route('login'));
    }

    public function test_customer_bisa_membuka_keranjang_dan_datanya_memuat_stok(): void

    {
        Produk::factory()->create(['name_produk' => 'Tart Uji', 'stok' => 7]);

        $this->actingAs(User::factory()->create())
            ->get(route('keranjang'))
            ->assertOk()
            ->assertSee('Keranjang Belanja')
            ->assertSee('Tart Uji')
            ->assertSee('"stok":7', false);
    }

    public function test_nomor_dan_alamat_pesanan_terakhir_terisi_otomatis_di_checkout(): void
    {
        $user = User::factory()->create();
        Pesanan::factory()->create([
            'user_id' => $user->id,
            'kode_pesanan' => 'TK-PROFIL-0001',
            'nama_pelanggan' => 'Siti Pembeli',
            'no_telepon' => '08123456789',
            'alamat_pengiriman' => 'Jl. Mawar No. 12',
        ]);

        $this->actingAs($user)
            ->get(route('keranjang'))
            ->assertOk()
            ->assertSee('value="Siti Pembeli"', false)
            ->assertSee('value="08123456789"', false)
            ->assertSee('Jl. Mawar No. 12')
            ->assertSee('Transfer bank')
            ->assertSee('E-wallet')
            ->assertSee('COD');

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('value="Siti Pembeli"', false)
            ->assertSee('value="08123456789"', false)
            ->assertSee('Jl. Mawar No. 12')
            ->assertSee('name="metode_pembayaran"', false);
    }

    public function test_keranjang_hanya_menerima_produk_dari_menu_aktif(): void
    {
        $kategoriAktif = Kategori::factory()->create(['is_active' => true]);
        $kategoriNonaktif = Kategori::factory()->create(['is_active' => false]);
        Produk::factory()->create([
            'name_produk' => 'Menu Keranjang Aktif',
            'kategori_id' => $kategoriAktif->id_kategori,
            'is_active' => true,
        ]);
        Produk::factory()->create([
            'name_produk' => 'Menu Keranjang Nonaktif',
            'kategori_id' => $kategoriNonaktif->id_kategori,
            'is_active' => true,
        ]);
        Produk::factory()->create([
            'name_produk' => 'Produk Keranjang Nonaktif',
            'kategori_id' => $kategoriAktif->id_kategori,
            'is_active' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('keranjang'))
            ->assertOk()
            ->assertSee('Menu Keranjang Aktif')
            ->assertDontSee('Menu Keranjang Nonaktif')
            ->assertDontSee('Produk Keranjang Nonaktif');
    }

    public function test_riwayat_hanya_menampilkan_pesanan_milik_sendiri(): void
    {
        $saya = User::factory()->create();
        $lain = User::factory()->create();

        Pesanan::factory()->create(['user_id' => $saya->id, 'kode_pesanan' => 'TK-SAYA-0001', 'menu' => 'Kue Milik Saya']);
        Pesanan::factory()->create(['user_id' => $lain->id, 'kode_pesanan' => 'TK-LAIN-0002', 'menu' => 'Kue Orang Lain']);

        $this->actingAs($saya)
            ->get(route('order.riwayat'))
            ->assertOk()
            ->assertSee('Kue Milik Saya')
            ->assertDontSee('Kue Orang Lain');
    }

    public function test_riwayat_menggabungkan_item_satu_checkout_dalam_satu_kartu(): void
    {
        Pengaturan::simpan('alamat', 'Toko Kue, Bandung');
        config(['services.google_maps.server_key' => 'test-maps-key']);
        Http::fake([
            'routes.googleapis.com/*' => Http::response(['routes' => [['distanceMeters' => 5000]]], 200),
        ]);
        $a = Produk::factory()->create(['stok' => 10]);
        $b = Produk::factory()->create(['stok' => 10]);

        $this->actingAs(User::factory()->create())

            ->postJson(route('order.keranjang'), [
                'nama_pelanggan' => 'Siti',
                'no_telepon' => '08123456789',
                'alamat_pengiriman' => 'Jl. Melati No. 1',
                'metode_pembayaran' => \App\Models\Pesanan::METODE_TRANSFER_BANK,
                'jenis_pengiriman' => 'hemat',
                'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'items' => [
                    ['produk_id' => $a->id_produk, 'jumlah' => 1],
                    ['produk_id' => $b->id_produk, 'jumlah' => 2],
                ],
            ])
            ->assertOk();

        $kode = Pesanan::firstOrFail()->kode_pesanan;

        $html = $this->get(route('order.riwayat'))
            ->assertOk()
            ->assertSee($kode)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'class="order-card"'));
    }

    public function test_riwayat_menampilkan_semua_status_pesanan_dengan_tab_yang_sesuai(): void
    {
        $user = User::factory()->create();
        $statuses = [
            'Pending',
            'Diproses',
            'Siap Diantar',
            'Dalam Pengantaran',
            'Selesai',
            'Dibatalkan',
        ];

        foreach ($statuses as $index => $status) {
            Pesanan::factory()->create([
                'user_id' => $user->id,
                'kode_pesanan' => 'TK-STATUS-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'status' => $status,
            ]);
        }

        $response = $this->actingAs($user)->get(route('order.riwayat'));

        $response->assertOk();
        foreach ($statuses as $status) {
            $response->assertSee('data-filter="'.$status.'"', false)
                ->assertSee('data-status="'.$status.'"', false);
        }
        $response->assertSee('status-dalam-pengantaran', false)
            ->assertSee('status-dibatalkan', false);
    }
}