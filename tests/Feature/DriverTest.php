<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_halaman_driver_menampilkan_ringkasan_dan_status_tugas(): void
    {
        $available = Driver::factory()->create(['nama' => 'Andi Tersedia']);
        $busy = Driver::factory()->create(['nama' => 'Budi Mengantar']);
        $produk = Produk::factory()->create();
        Pesanan::factory()->create([
            'kurir_id' => $busy->id,
            'produk_id' => $produk->id_produk,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
        ]);

        $this->get(route('driver.index'))
            ->assertOk()
            ->assertSee('Andi Tersedia')
            ->assertSee('Budi Mengantar')
            ->assertSee('Tersedia')
            ->assertSee('Sedang Mengantar')
            ->assertViewHas('driverTersedia', 1)
            ->assertViewHas('driverMengantar', 1);
    }

    public function test_admin_bisa_membuat_driver(): void
    {
        $this->post(route('driver.store'), [
            'nama' => 'Andi',
            'no_telepon' => '081234567890',
            'jenis_kendaraan' => 'Motor',
            'plat_nomor' => 'B 1234 ABC',
        ])->assertRedirect();

        $this->assertDatabaseHas('drivers', ['nama' => 'Andi', 'is_active' => true]);
    }

    public function test_penugasan_driver_mengubah_status_pesanan(): void
    {
        $driver = Driver::factory()->create();
        $pesanan = Pesanan::factory()->create(['status' => Pesanan::STATUS_SIAP_DIANTAR]);

        $this->post(route('driver.assign', $driver), ['pesanan_ids' => [$pesanan->id]])->assertRedirect();

        $this->assertDatabaseHas('pesanan', [
            'id' => $pesanan->id,
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
        ]);
    }

    public function test_driver_sedang_mengantar_tidak_bisa_dinonaktifkan(): void
    {
        $driver = Driver::factory()->create();
        Pesanan::factory()->create(['kurir_id' => $driver->id, 'status' => Pesanan::STATUS_DALAM_PENGANTARAN]);

        $this->patch(route('driver.status', $driver))->assertSessionHas('error');
        $this->assertTrue($driver->fresh()->is_active);
    }

    public function test_penugasan_driver_mencakup_seluruh_item_dalam_satu_checkout(): void
    {
        $driver = Driver::factory()->create();
        $pesananA = Pesanan::factory()->create([
            'kode_pesanan' => 'TK-KIRIM-01',
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
        $pesananB = Pesanan::factory()->create([
            'kode_pesanan' => 'TK-KIRIM-01',
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);

        $this->post(route('pesanan.driver', $pesananA), ['driver_id' => $driver->id])->assertRedirect();

        foreach ([$pesananA, $pesananB] as $item) {
            $this->assertDatabaseHas('pesanan', [
                'id' => $item->id,
                'kurir_id' => $driver->id,
                'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            ]);
        }
    }

    public function test_driver_yang_sedang_mengantar_tidak_bisa_diberi_pesanan_baru(): void
    {
        $driver = Driver::factory()->create();
        Pesanan::factory()->create([
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
        $pesananBaru = Pesanan::factory()->create([
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);

        $this->post(route('driver.assign', $driver), ['pesanan_ids' => [$pesananBaru->id]])
            ->assertSessionHasErrors('driver_id');

        $this->assertSame(Pesanan::STATUS_SIAP_DIANTAR, $pesananBaru->fresh()->status);
        $this->assertNull($pesananBaru->fresh()->kurir_id);
    }

    public function test_halaman_driver_menyembunyikan_form_penugasan_saat_driver_sedang_mengantar(): void
    {
        $driver = Driver::factory()->create();
        Pesanan::factory()->create([
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
        Pesanan::factory()->create([
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);

        $this->get(route('driver.index', ['pilih' => $driver->id]))
            ->assertOk()
            ->assertSee('Driver sedang dalam perjalanan')
            ->assertDontSee('Tugaskan sebagai satu perjalanan');
    }

    public function test_beberapa_pesanan_dapat_ditugaskan_sebagai_satu_perjalanan_kepada_driver_tersedia(): void
    {
        $driver = Driver::factory()->create();
        $orders = collect(['TK-JALAN-01', 'TK-JALAN-02'])->map(fn (string $kode) => Pesanan::factory()->create([
            'kode_pesanan' => $kode,
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]));

        $this->post(route('driver.assign', $driver), [
            'pesanan_ids' => $orders->pluck('id')->all(),
        ])->assertRedirect();

        $this->assertSame(2, Pesanan::whereIn('kode_pesanan', ['TK-JALAN-01', 'TK-JALAN-02'])
            ->where('kurir_id', $driver->id)
            ->where('status', Pesanan::STATUS_DALAM_PENGANTARAN)
            ->count());

        $orderTambahan = Pesanan::factory()->create([
            'status' => Pesanan::STATUS_SIAP_DIANTAR,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
        $this->post(route('driver.assign', $driver), ['pesanan_ids' => [$orderTambahan->id]])
            ->assertSessionHasErrors('driver_id');
    }

    public function test_pesanan_tidak_bisa_langsung_ditandai_selesai_dan_harus_ditugaskan_dulu(): void
    {
        $driver = Driver::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);

        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_SELESAI])
            ->assertSessionHasErrors('status');
        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DALAM_PENGANTARAN])
            ->assertSessionHasErrors('status');

        $this->assertSame(Pesanan::STATUS_DALAM_PENGANTARAN, $pesanan->fresh()->status);
    }

    public function test_driver_menyelesaikan_seluruh_item_checkout_yang_ditugaskan_kepadanya(): void
    {
        $driver = Driver::factory()->create();
        $items = collect([1, 2])->map(fn () => Pesanan::factory()->create([
            'kode_pesanan' => 'TK-SELESAI-01',
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]));

        $this->patch(route('driver.pesanan.selesai', [$driver, $items->first()]))->assertRedirect();

        $this->assertSame(2, Pesanan::where('kode_pesanan', 'TK-SELESAI-01')->where('status', Pesanan::STATUS_SELESAI)->count());
    }

    public function test_pesanan_transfer_belum_lunas_tidak_bisa_dikirim(): void
    {
        $pesanan = Pesanan::factory()->create([
            'status' => Pesanan::STATUS_DIPROSES,
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
        ]);

        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_SIAP_DIANTAR])
            ->assertSessionHasErrors('status');

        $this->assertSame(Pesanan::STATUS_DIPROSES, $pesanan->fresh()->status);
    }
}