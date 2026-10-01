<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Pesanan;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesananLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Pengaturan::simpan('alamat', 'Toko Kue, Bandung');
        config(['services.google_maps.server_key' => 'test-maps-key']);
        Http::fake([
            'routes.googleapis.com/*' => Http::response(['routes' => [['distanceMeters' => 5000]]], 200),
        ]);
    }

    public function test_checkout_transfer_sampai_pesanan_diterima_customer(): void
    {
        Storage::fake('local');
        Pengaturan::simpan('bank_nama', 'Bank Contoh');
        Pengaturan::simpan('bank_rekening', '1234567890');
        Pengaturan::simpan('bank_pemilik', 'Toko Kue');
        $customer = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $produk = Produk::factory()->create(['harga' => 25000, 'stok' => 8]);

        $response = $this->actingAs($customer)->postJson(route('order.keranjang'), [
            'nama_pelanggan' => 'Siti',
            'no_telepon' => '08123456789',
            'alamat_pengiriman' => 'Jl. Melati No. 1',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'jenis_pengiriman' => 'hemat',
            'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'items' => [['produk_id' => $produk->id_produk, 'jumlah' => 2]],
        ])->assertOk();

        $kode = Pesanan::firstOrFail()->kode_pesanan;
        $this->actingAs($customer)
            ->get($response->json('redirect'))
            ->assertOk()
            ->assertSee('Detail pembayaran akan tampil di halaman pesanan setelah dikonfirmasi')
            ->assertSee('Menunggu konfirmasi ongkos kirim dan jadwal')
            ->assertDontSee('1234567890');

        $pesanan = Pesanan::where('kode_pesanan', $kode)->firstOrFail();
        $this->actingAs($customer)
            ->post(route('order.bukti-pembayaran', $kode), [
                'bukti_pembayaran' => UploadedFile::fake()->image('terlalu-awal.png'),
            ])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->patch(route('pesanan.ongkir', $pesanan), [
                'ongkir' => 5000,
                'jadwal_pengiriman' => now()->addDays(2)->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect();
        $this->actingAs($customer)
            ->get($response->json('redirect'))
            ->assertOk()
            ->assertSee('Rp 55.000')
            ->assertSee('1234567890');

        $this->actingAs($customer)
            ->post(route('order.bukti-pembayaran', $kode), [
                'bukti_pembayaran' => UploadedFile::fake()->image('transfer.png'),
            ])
            ->assertRedirect();
        $pesanan->refresh();
        $this->assertSame(Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI, $pesanan->status_pembayaran);

        $this->actingAs($admin)
            ->get(route('pembayaran.index', ['pilih' => $kode]))
            ->assertOk()
            ->assertSee('Lihat foto bukti pembayaran');
        $this->patch(route('pembayaran.update', $pesanan), [
            'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS,
        ])->assertRedirect();

        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIPROSES])->assertRedirect();
        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_SIAP_DIANTAR])->assertRedirect();

        $driver = Driver::factory()->create();
        $this->post(route('driver.assign', $driver), ['pesanan_ids' => [$pesanan->id]])->assertRedirect();
        $pesanan->refresh();
        $this->assertSame(Pesanan::STATUS_DALAM_PENGANTARAN, $pesanan->status);
        $this->assertSame($driver->id, $pesanan->kurir_id);

        $this->patch(route('driver.pesanan.selesai', [$driver, $pesanan]))->assertRedirect();
        $this->assertSame(Pesanan::STATUS_SELESAI, $pesanan->fresh()->status);
        $this->assertSame(Pesanan::PEMBAYARAN_LUNAS, $pesanan->fresh()->status_pembayaran);
        $this->assertSame(6, $produk->fresh()->stok);

        $this->actingAs($customer)
            ->get(route('order.riwayat'))
            ->assertOk()
            ->assertSee('Selesai')
            ->assertSee('Lunas')
            ->assertSee('Rp 55.000')
            ->assertSee($driver->nama);
    }

    public function test_alur_cod_mewajibkan_konfirmasi_ongkir_dan_pembayaran_setelah_pengantaran(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $produk = Produk::factory()->create(['harga' => 10000, 'stok' => 5]);
        $this->actingAs($customer)->postJson(route('order.keranjang'), [
            'nama_pelanggan' => 'Ani',
            'no_telepon' => '08111111111',
            'alamat_pengiriman' => 'Jl. Mawar',
            'metode_pembayaran' => Pesanan::METODE_COD,
            'jenis_pengiriman' => 'hemat',
            'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'items' => [['produk_id' => $produk->id_produk, 'jumlah' => 1]],
        ])->assertOk();
        $pesanan = Pesanan::firstOrFail();

        $this->actingAs($admin)
            ->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIPROSES])
            ->assertSessionHasErrors('status');
        $this->patch(route('pesanan.ongkir', $pesanan), [
            'ongkir' => 7000,
            'jadwal_pengiriman' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ])->assertRedirect();
        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIPROSES])->assertRedirect();
        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_SIAP_DIANTAR])->assertRedirect();

        $driver = Driver::factory()->create();
        $this->post(route('driver.assign', $driver), ['pesanan_ids' => [$pesanan->id]])->assertRedirect();
        $this->patch(route('driver.pesanan.selesai', [$driver, $pesanan]))->assertRedirect();
        $this->assertSame(Pesanan::PEMBAYARAN_COD, $pesanan->fresh()->status_pembayaran);

        $this->patch(route('pembayaran.update', $pesanan), [
            'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS,
        ])->assertRedirect();
        $this->assertSame(Pesanan::PEMBAYARAN_LUNAS, $pesanan->fresh()->status_pembayaran);
    }
}
