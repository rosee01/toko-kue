<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppNotification;
use App\Models\NotifikasiWhatsApp;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Services\PesananService;
use App\Services\TarifPengiriman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeliveryNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Pengaturan::simpan('alamat', 'Toko Kue, Bandung');
        config(['services.google_maps.server_key' => 'test-maps-key']);
    }

    private function fakeMaps(array $graphResponse = ['messages' => [['id' => 'wamid.test']]], int $graphStatus = 200): void
    {
        Http::fake([
            'routes.googleapis.com/*' => Http::response(['routes' => [['distanceMeters' => 5000]]], 200),
            'graph.facebook.com/*' => Http::response($graphResponse, $graphStatus),
        ]);
    }

    private function checkoutData(Produk $produk, array $override = []): array
    {
        return array_replace([
            'nama_pelanggan' => 'Siti',
            'no_telepon' => '08123456789',
            'alamat_pengiriman' => 'Alamat simulasi tujuan',
            'metode_pembayaran' => Pesanan::METODE_COD,
            'jenis_pengiriman' => 'hemat',
            'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'items' => [['produk_id' => $produk->id_produk, 'jumlah' => 1]],
        ], $override);
    }

    public function test_tarif_dihitung_server_dari_rute_dan_pesanan_menyimpan_jadwal(): void
    {
        $this->fakeMaps();
        Pengaturan::simpan('tarif_per_km_hemat', '8400');
        $produk = Produk::factory()->create(['harga' => 12000]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, [
                'jenis_pengiriman' => 'hemat',
                'jadwal_diminta' => now()->addDays(3)->format('Y-m-d H:i:s'),
                'ongkir' => 1,
            ]))
            ->assertOk();

        $this->assertDatabaseHas('pesanan', [
            'produk_id' => $produk->id_produk,
            'jenis_pengiriman' => 'hemat',
            'jarak_pengiriman_km' => 5,
            'ongkir' => 42000,
            'jadwal_dikonfirmasi' => false,
            'ongkir_dikonfirmasi' => false,
        ]);
        $this->assertNotNull(Pesanan::firstOrFail()->jadwal_diminta);
    }

    public function test_endpoint_ongkir_menghitung_dari_alamat_dan_mengembalikan_tiga_layanan(): void
    {
        $this->fakeMaps();
        $this->actingAs(User::factory()->create())
            ->postJson(route('order.delivery-quote'), ['alamat_pengiriman' => 'Jl. Tujuan Nomor 10, Bandung'])
            ->assertOk()
            ->assertJsonPath('distance_km', 5)
            ->assertJsonPath('services.hemat.fee', 5000)
            ->assertJsonPath('services.cepat.label', 'Cepat')
            ->assertJsonPath('services.lambat.label', 'Lambat');

        Http::assertSent(fn ($request) => $request->url() === 'https://routes.googleapis.com/directions/v2:computeRoutes'
            && $request['origin']['address'] === 'Toko Kue, Bandung'
            && $request['destination']['address'] === 'Jl. Tujuan Nomor 10, Bandung'
            && $request->hasHeader('X-Goog-Api-Key', 'test-maps-key'));
    }

    public function test_endpoint_ongkir_menolak_jika_alamat_toko_belum_diatur(): void
    {
        $this->fakeMaps();
        config(['delivery.origin' => null]);
        Pengaturan::simpan('alamat', null);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.delivery-quote'), ['alamat_pengiriman' => 'Jl. Tujuan Nomor 10, Bandung'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.alamat_pengiriman.0', 'Admin harus mengatur alamat toko yang sebenarnya sebelum ongkir dapat dihitung.');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'routes.googleapis.com'));
    }

    public function test_endpoint_ongkir_menjelaskan_google_maps_key_belum_dikonfigurasi(): void
    {
        $this->fakeMaps();
        config(['services.google_maps.server_key' => null]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.delivery-quote'), ['alamat_pengiriman' => 'Jl. Tujuan Nomor 10, Bandung'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.alamat_pengiriman.0', 'Perhitungan jarak belum tersedia. Admin perlu mengonfigurasi Google Maps API key.');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'routes.googleapis.com'));
    }

    public function test_endpoint_ongkir_menjelaskan_jika_akses_google_routes_ditolak(): void
    {
        Http::fake([
            'routes.googleapis.com/*' => Http::response(['error' => ['status' => 'PERMISSION_DENIED']], 403),
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.delivery-quote'), ['alamat_pengiriman' => 'Jl. Tujuan Nomor 10, Bandung'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.alamat_pengiriman.0', 'Google Maps Routes API tidak dapat diakses. Admin perlu memeriksa API key, izin, billing, dan status Routes API di Google Cloud.');
    }

    public function test_jenis_pengiriman_yang_tidak_dikenal_ditolak_server(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, ['jenis_pengiriman' => 'gratis']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jenis_pengiriman');

        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_tarif_ongkir_nol_diizinkan_sebagai_tarif_gratis(): void
    {
        $this->fakeMaps();
        Pengaturan::simpan('tarif_per_km_cepat', '0');

        $this->assertSame(0, app(TarifPengiriman::class)->quote('Alamat pelanggan, Bandung', 'cepat')['fee']);
    }

    public function test_nomor_telepon_dengan_format_tidak_valid_ditolak(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, ['no_telepon' => 'nomor tidak valid']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('no_telepon');

        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_jadwal_pemesanan_minimal_satu_jam_dari_sekarang(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, [
                'jadwal_diminta' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jadwal_diminta');

        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_admin_mengonfirmasi_ongkir_dan_jadwal_pesanan_sekaligus(): void
    {
        $this->fakeMaps();
        $produk = Produk::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user)->postJson(route('order.keranjang'), $this->checkoutData($produk))->assertOk();
        $pesanan = Pesanan::firstOrFail();
        $waktu = now()->addDays(4)->startOfHour();

        app(PesananService::class)->ubahOngkir($pesanan, 18000, $waktu->format('Y-m-d H:i:s'));

        $this->assertDatabaseHas('pesanan', [
            'id' => $pesanan->id,
            'ongkir' => 18000,
            'ongkir_dikonfirmasi' => true,
            'jadwal_dikonfirmasi' => true,
            'jadwal_pengiriman' => $waktu->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_admin_harus_mengirim_jadwal_eksplisit_untuk_pesanan_dengan_rute(): void
    {
        $this->fakeMaps();
        $admin = User::factory()->admin()->create();
        $produk = Produk::factory()->create();
        $this->actingAs(User::factory()->create())->postJson(
            route('order.keranjang'),
            $this->checkoutData($produk)
        )->assertOk();
        $pesanan = Pesanan::firstOrFail();

        $this->actingAs($admin)
            ->patch(route('pesanan.ongkir', $pesanan), ['ongkir' => 10000])
            ->assertSessionHasErrors('jadwal_pengiriman');

        $this->assertFalse($pesanan->fresh()->ongkir_dikonfirmasi);
        $this->assertFalse($pesanan->fresh()->jadwal_dikonfirmasi);
    }

    public function test_whatsapp_hanya_dikirim_dengan_persetujuan_dan_api_mencatat_hasil(): void
    {
        config([
            'services.whatsapp_cloud.access_token' => 'test-token',
            'services.whatsapp_cloud.phone_number_id' => '123456',
            'services.whatsapp_cloud.template' => 'order_update',
            'services.whatsapp_cloud.language' => 'id',
        ]);
        $this->fakeMaps();
        Queue::fake();
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, ['izin_notifikasi_whatsapp' => true]))
            ->assertOk();

        $notification = NotifikasiWhatsApp::firstOrFail();
        Queue::assertPushed(SendWhatsAppNotification::class);
        (new SendWhatsAppNotification($notification->id))->handle();

        Http::assertSent(fn ($request) => $request->url() === 'https://graph.facebook.com/v22.0/123456/messages'
            && $request['to'] === '628123456789'
            && $request['template']['name'] === 'order_update');
        $this->assertDatabaseHas('notifikasi_whatsapp', [
            'telepon' => '628123456789',
            'peristiwa' => 'order-created',
            'status' => 'sent',
            'provider_message_id' => 'wamid.test',
        ]);
    }

    public function test_checkout_tanpa_opt_in_tidak_mengirim_whatsapp(): void
    {
        $this->fakeMaps();
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk))
            ->assertOk();

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'graph.facebook.com'));
        $this->assertDatabaseCount('notifikasi_whatsapp', 0);
    }

    public function test_whatsapp_api_gagal_dicatat_dan_checkout_tetap_berhasil(): void
    {
        config([
            'services.whatsapp_cloud.access_token' => 'test-token',
            'services.whatsapp_cloud.phone_number_id' => '123456',
            'services.whatsapp_cloud.template' => 'order_update',
            'services.whatsapp_cloud.language' => 'id',
        ]);
        $this->fakeMaps(['error' => ['message' => 'template rejected']], 400);
        Queue::fake();
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->checkoutData($produk, ['izin_notifikasi_whatsapp' => true]))
            ->assertOk();

        (new SendWhatsAppNotification(NotifikasiWhatsApp::firstOrFail()->id))->handle();

        $this->assertDatabaseHas('notifikasi_whatsapp', [
            'peristiwa' => 'order-created',
            'status' => 'failed',
            'pesan_error' => 'template rejected',
        ]);
    }
}
