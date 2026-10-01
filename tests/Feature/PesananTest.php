<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Services\PesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    private function payload(Produk $produk, array $override = []): array
    {
        return array_merge([
            'nama_pelanggan' => 'Budi',
            'produk_id' => $produk->id_produk,
            'jumlah' => 2,
            'status' => 'Pending',
        ], $override);
    }

    public function test_buat_pesanan_menghitung_total_dan_mengurangi_stok(): void
    {

        $produk = Produk::factory()->create(['harga' => 50000, 'stok' => 10]);

        $this->post(route('pesanan.store'), $this->payload($produk))->assertRedirect(route('pesanan.index'));

        $this->assertDatabaseHas('pesanan', [
            'nama_pelanggan' => 'Budi',
            'menu' => $produk->name_produk,
            'harga_satuan' => 50000,
            'jumlah' => 2,
            'total' => 100000,
        ]);
        $this->assertSame(8, $produk->fresh()->stok);
    }

    public function test_total_tidak_bisa_dimanipulasi_dari_form(): void
    {
        $produk = Produk::factory()->create(['harga' => 50000, 'stok' => 10]);

        $this->post(route('pesanan.store'), $this->payload($produk) + ['total' => 1]);

        $this->assertDatabaseHas('pesanan', ['total' => 100000]);
    }

    public function test_pesanan_ditolak_jika_stok_tidak_cukup(): void
    {
        $produk = Produk::factory()->create(['stok' => 1]);

        $this->post(route('pesanan.store'), $this->payload($produk, ['jumlah' => 5]))
            ->assertSessionHasErrors('jumlah');

        $this->assertDatabaseCount('pesanan', 0);
        $this->assertSame(1, $produk->fresh()->stok);

    }

    public function test_ubah_jumlah_menyesuaikan_stok(): void
    {
        $produk = Produk::factory()->create(['harga' => 10000, 'stok' => 10]);
        $this->post(route('pesanan.store'), $this->payload($produk, ['jumlah' => 3]));
        $pesanan = Pesanan::firstOrFail();
        $this->assertSame(7, $produk->fresh()->stok);

        $this->put(route('pesanan.update', $pesanan), $this->payload($produk, ['jumlah' => 5, 'status' => 'Selesai']))
            ->assertRedirect(route('pesanan.index'));

        $this->assertSame(5, $produk->fresh()->stok);
        $this->assertDatabaseHas('pesanan', ['id' => $pesanan->id, 'jumlah' => 5, 'total' => 50000, 'status' => Pesanan::STATUS_PENDING]);
    }

    public function test_ganti_menu_mengembalikan_stok_menu_lama(): void
    {
        $a = Produk::factory()->create(['stok' => 10]);
        $b = Produk::factory()->create(['stok' => 10]);
        $this->post(route('pesanan.store'), $this->payload($a, ['jumlah' => 4]));
        $pesanan = Pesanan::firstOrFail();

        $this->put(route('pesanan.update', $pesanan), $this->payload($b, ['jumlah' => 2]));

        $this->assertSame(10, $a->fresh()->stok);
        $this->assertSame(8, $b->fresh()->stok);
    }

    public function test_hapus_pesanan_mengembalikan_stok(): void
    {
        $produk = Produk::factory()->create(['stok' => 10]);

        $this->post(route('pesanan.store'), $this->payload($produk, ['jumlah' => 4]));

        $this->delete(route('pesanan.destroy', Pesanan::firstOrFail()))->assertRedirect(route('pesanan.index'));

        $this->assertSame(10, $produk->fresh()->stok);
        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_menghapus_checkout_pending_mengembalikan_stok_semua_item(): void
    {
        $a = Produk::factory()->create(['stok' => 10]);
        $b = Produk::factory()->create(['stok' => 10]);
        $service = app(PesananService::class);
        $itemA = $service->buat(['nama_pelanggan' => 'Siti', 'produk_id' => $a->id_produk, 'jumlah' => 2, 'status' => Pesanan::STATUS_PENDING]);
        $itemB = $service->buat(['nama_pelanggan' => 'Siti', 'produk_id' => $b->id_produk, 'jumlah' => 3, 'status' => Pesanan::STATUS_PENDING]);
        $itemA->update(['kode_pesanan' => 'TK-HAPUS-01']);
        $itemB->update(['kode_pesanan' => 'TK-HAPUS-01']);

        $this->delete(route('pesanan.destroy', $itemA))->assertRedirect();

        $this->assertDatabaseMissing('pesanan', ['kode_pesanan' => 'TK-HAPUS-01']);
        $this->assertSame(10, $a->fresh()->stok);
        $this->assertSame(10, $b->fresh()->stok);
    }

    public function test_pesanan_selesai_tidak_bisa_dihapus_atau_mengembalikan_stok(): void
    {
        $produk = Produk::factory()->create(['stok' => 8]);
        $pesanan = Pesanan::factory()->create([
            'produk_id' => $produk->id_produk,
            'jumlah' => 2,
            'status' => Pesanan::STATUS_SELESAI,
        ]);

        $this->delete(route('pesanan.destroy', $pesanan))->assertSessionHasErrors('pesanan');

        $this->assertDatabaseHas('pesanan', ['id' => $pesanan->id]);
        $this->assertSame(8, $produk->fresh()->stok);
    }

    public function test_pesanan_yang_sedang_diproses_tidak_bisa_diedit(): void
    {
        $produk = Produk::factory()->create(['stok' => 8]);
        $pesanan = Pesanan::factory()->create([
            'produk_id' => $produk->id_produk,
            'jumlah' => 1,
            'status' => Pesanan::STATUS_DIPROSES,
        ]);

        $this->put(route('pesanan.update', $pesanan), $this->payload($produk, ['jumlah' => 4]))
            ->assertSessionHasErrors('pesanan');

        $this->assertSame(8, $produk->fresh()->stok);
        $this->assertSame(1, $pesanan->fresh()->jumlah);
    }

    public function test_pesanan_dalam_pengantaran_tidak_bisa_dibatalkan_atau_dikembalikan_ke_stok(): void
    {
        $produk = Produk::factory()->create(['stok' => 8]);
        $driver = \App\Models\Driver::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'produk_id' => $produk->id_produk,
            'kurir_id' => $driver->id,
            'status' => Pesanan::STATUS_DALAM_PENGANTARAN,
        ]);

        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIBATALKAN])
            ->assertSessionHasErrors('status');

        $this->assertSame(Pesanan::STATUS_DALAM_PENGANTARAN, $pesanan->fresh()->status);
        $this->assertSame(8, $produk->fresh()->stok);
    }

    public function test_admin_harus_mengonfirmasi_ongkir_sebelum_pesanan_diproses(): void
    {
        $pesanan = Pesanan::factory()->create([
            'status' => Pesanan::STATUS_PENDING,
            'ongkir_dikonfirmasi' => false,
        ]);

        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIPROSES])
            ->assertSessionHasErrors('status');

        $this->patch(route('pesanan.ongkir', $pesanan), ['ongkir' => 12000])->assertRedirect();
        $pesanan->refresh();
        $this->assertSame(12000, $pesanan->ongkir);
        $this->assertTrue($pesanan->ongkir_dikonfirmasi);
        $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIPROSES])->assertRedirect();
    }

    public function test_ongkir_tidak_bisa_diubah_setelah_pembayaran_dikirim(): void
    {
        $pesanan = Pesanan::factory()->create([
            'status' => Pesanan::STATUS_PENDING,
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => 'bukti-pembayaran/transfer.png',
        ]);

        $this->patch(route('pesanan.ongkir', $pesanan), ['ongkir' => 10000])
            ->assertSessionHasErrors('ongkir');
    }

    public function test_pembatalan_tidak_bisa_menghapus_pesanan_yang_sudah_dibayar_atau_menunggu_verifikasi(): void
    {
        foreach ([Pesanan::PEMBAYARAN_LUNAS, Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI, Pesanan::PEMBAYARAN_DITOLAK] as $statusPembayaran) {
            $pesanan = Pesanan::factory()->create([
                'status' => Pesanan::STATUS_DIPROSES,
                'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
                'status_pembayaran' => $statusPembayaran,
                'bukti_pembayaran' => $statusPembayaran !== Pesanan::PEMBAYARAN_LUNAS
                    ? 'bukti-pembayaran/transfer.png'
                    : null,
            ]);

            $this->patch(route('pesanan.status', $pesanan), ['status' => Pesanan::STATUS_DIBATALKAN])
                ->assertSessionHasErrors('status');

            $this->assertSame(Pesanan::STATUS_DIPROSES, $pesanan->fresh()->status);
        }
    }

    public function test_customer_bukti_pembayaran_mengunci_edit_order(): void
    {
        $produk = Produk::factory()->create(['stok' => 8]);
        $pesanan = Pesanan::factory()->create([
            'produk_id' => $produk->id_produk,
            'jumlah' => 1,
            'status' => Pesanan::STATUS_PENDING,
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => 'bukti-pembayaran/transfer.png',
        ]);

        $this->put(route('pesanan.update', $pesanan), $this->payload($produk, ['jumlah' => 4]))
            ->assertSessionHasErrors('pesanan');

        $this->assertSame(1, $pesanan->fresh()->jumlah);
        $this->assertSame(8, $produk->fresh()->stok);
    }

    public function test_riwayat_pesanan_tetap_ada_saat_produk_dihapus(): void
    {
        $produk = Produk::factory()->create(['name_produk' => 'Kue Lama']);
        $this->post(route('pesanan.store'), $this->payload($produk));

        $this->delete(route('produk.destroy', $produk));

        $this->assertDatabaseHas('pesanan', ['menu' => 'Kue Lama', 'produk_id' => null]);
    }

    public function test_status_harus_valid(): void
    {
        $produk = Produk::factory()->create();

        $this->post(route('pesanan.store'), $this->payload($produk, ['status' => 'Ngawur']))
            ->assertSessionHasErrors('status');
    }

    public function test_submenu_pesanan_memfilter_daftar_berdasarkan_status(): void
    {
        Pesanan::factory()->create(['nama_pelanggan' => 'Budi Pending', 'status' => Pesanan::STATUS_PENDING]);
        Pesanan::factory()->create(['nama_pelanggan' => 'Siti Diproses', 'status' => Pesanan::STATUS_DIPROSES]);

        $this->get(route('pesanan.index', ['status' => Pesanan::STATUS_PENDING]))
            ->assertOk()
            ->assertSee('Pesanan Baru')
            ->assertSee('Budi Pending')
            ->assertDontSee('Siti Diproses');
    }

    public function test_item_satu_checkout_ditampilkan_sebagai_satu_pesanan_dengan_detail(): void
    {
        $produkA = Produk::factory()->create(['name_produk' => 'Brownies', 'harga' => 65000, 'stok' => 10]);
        $produkB = Produk::factory()->create(['name_produk' => 'Lapis Legit', 'harga' => 85000, 'stok' => 10]);
        $service = app(PesananService::class);
        $itemA = $service->buat(['nama_pelanggan' => 'Siti', 'produk_id' => $produkA->id_produk, 'jumlah' => 2, 'status' => Pesanan::STATUS_PENDING]);
        $itemB = $service->buat(['nama_pelanggan' => 'Siti', 'produk_id' => $produkB->id_produk, 'jumlah' => 1, 'status' => Pesanan::STATUS_PENDING]);
        $itemA->update(['kode_pesanan' => 'TK-GABUNG-01', 'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS]);
        $itemB->update(['kode_pesanan' => 'TK-GABUNG-01', 'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS]);

        $this->get(route('pesanan.index'))
            ->assertOk()
            ->assertViewHas('pesanan', fn ($pesanan) => $pesanan->count() === 1)
            ->assertViewHas('pesananTerpilih', fn ($pesanan) => $pesanan->items->count() === 2 && $pesanan->subtotal === 215000)
            ->assertSee('TK-GABUNG-01')
            ->assertSee('Brownies')
            ->assertSee('Lapis Legit')
            ->assertSee('Rp 215.000');
    }

    public function test_pembatalan_grup_checkout_mengembalikan_stok_semua_item(): void
    {
        $produkA = Produk::factory()->create(['stok' => 10]);
        $produkB = Produk::factory()->create(['stok' => 10]);
        $service = app(PesananService::class);
        $itemA = $service->buat(['nama_pelanggan' => 'Budi', 'produk_id' => $produkA->id_produk, 'jumlah' => 2, 'status' => Pesanan::STATUS_PENDING]);
        $itemB = $service->buat(['nama_pelanggan' => 'Budi', 'produk_id' => $produkB->id_produk, 'jumlah' => 3, 'status' => Pesanan::STATUS_PENDING]);
        $itemA->update(['kode_pesanan' => 'TK-BATAL-01']);
        $itemB->update(['kode_pesanan' => 'TK-BATAL-01']);

        $this->patch(route('pesanan.status', $itemA), ['status' => Pesanan::STATUS_DIBATALKAN])
            ->assertRedirect();

        $this->assertSame(10, $produkA->fresh()->stok);
        $this->assertSame(10, $produkB->fresh()->stok);
        $this->assertSame(2, Pesanan::where('kode_pesanan', 'TK-BATAL-01')->where('status', Pesanan::STATUS_DIBATALKAN)->count());
        $this->assertSame(2, Pesanan::where('kode_pesanan', 'TK-BATAL-01')->where('status_pembayaran', Pesanan::PEMBAYARAN_DIBATALKAN)->count());
    }

    public function test_status_pembayaran_diperbarui_untuk_seluruh_checkout(): void
    {
        $produk = Produk::factory()->create(['stok' => 10]);
        \Illuminate\Support\Facades\Storage::fake('local');
        $path = \Illuminate\Http\UploadedFile::fake()->image('bukti.png')->store('bukti-pembayaran', 'local');
        $service = app(PesananService::class);
        $itemA = $service->buat(['nama_pelanggan' => 'Ani', 'produk_id' => $produk->id_produk, 'jumlah' => 1, 'status' => Pesanan::STATUS_PENDING]);
        $itemB = $service->buat(['nama_pelanggan' => 'Ani', 'produk_id' => $produk->id_produk, 'jumlah' => 1, 'status' => Pesanan::STATUS_PENDING]);
        $itemA->update([
            'kode_pesanan' => 'TK-BAYAR-01',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => $path,
        ]);
        $itemB->update([
            'kode_pesanan' => 'TK-BAYAR-01',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => $path,
        ]);

        $this->patch(route('pesanan.pembayaran', $itemA), ['status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS])
            ->assertRedirect();

        $this->assertSame(2, Pesanan::where('kode_pesanan', 'TK-BAYAR-01')->where('status_pembayaran', Pesanan::PEMBAYARAN_LUNAS)->count());
        $this->assertNotNull($itemA->fresh()->tanggal_pembayaran);
    }
}