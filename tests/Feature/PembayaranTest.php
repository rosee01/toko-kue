<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_halaman_pembayaran_mengelompokkan_item_checkout(): void
    {
        $produk = Produk::factory()->create(['harga' => 25000]);
        Pesanan::factory()->create([
            'kode_pesanan' => 'TK-BAYAR-10',
            'nama_pelanggan' => 'Siti',
            'produk_id' => $produk->id_produk,
            'menu' => $produk->name_produk,
            'total' => 50000,
            'jumlah' => 2,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
        ]);
        Pesanan::factory()->create([
            'kode_pesanan' => 'TK-BAYAR-10',
            'nama_pelanggan' => 'Siti',
            'produk_id' => $produk->id_produk,
            'menu' => $produk->name_produk,
            'total' => 25000,
            'jumlah' => 1,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
        ]);

        $this->get(route('pembayaran.index'))
            ->assertOk()
            ->assertViewHas('pembayaran', fn ($rows) => $rows->count() === 1)
            ->assertViewHas('nominalMenunggu', 75000)
            ->assertSee('TK-BAYAR-10')
            ->assertSee('Rp 75.000')
            ->assertSee('Transfer bank')
            ->assertSee('Menunggu customer mengunggah bukti');
    }

    public function test_verifikasi_mengubah_status_dan_waktu_pembayaran_semua_item(): void
    {
        Storage::fake('local');
        $bukti = UploadedFile::fake()->image('bukti.png');
        $path = Storage::disk('local')->putFile('bukti-pembayaran', $bukti);
        $produk = Produk::factory()->create();
        $items = collect([1, 2])->map(fn () => Pesanan::factory()->create([
            'kode_pesanan' => 'TK-LUNAS-01',
            'produk_id' => $produk->id_produk,
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => $path,
        ]));

        $this->patch(route('pembayaran.update', $items->first()), [
            'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS,
        ])->assertRedirect();

        $this->assertSame(2, Pesanan::where('kode_pesanan', 'TK-LUNAS-01')->where('status_pembayaran', Pesanan::PEMBAYARAN_LUNAS)->count());
        $this->assertNotNull($items->first()->fresh()->tanggal_pembayaran);
    }

    public function test_penolakan_pembayaran_wajib_mencatat_alasan(): void
    {
        Storage::fake('local');
        $path = UploadedFile::fake()->image('bukti.png')->store('bukti-pembayaran', 'local');
        $pesanan = Pesanan::factory()->create([
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => $path,
        ]);

        $this->from(route('pembayaran.index'))
            ->patch(route('pembayaran.update', $pesanan), ['status_pembayaran' => Pesanan::PEMBAYARAN_DITOLAK])
            ->assertSessionHasErrors('catatan_pembayaran');

        $this->patch(route('pembayaran.update', $pesanan), [
            'status_pembayaran' => Pesanan::PEMBAYARAN_DITOLAK,
            'catatan_pembayaran' => 'Bukti pembayaran tidak sesuai.',
        ])->assertRedirect();

        $this->assertDatabaseHas('pesanan', [
            'id' => $pesanan->id,
            'status_pembayaran' => Pesanan::PEMBAYARAN_DITOLAK,
            'catatan_pembayaran' => 'Bukti pembayaran tidak sesuai.',
        ]);
    }

    public function test_admin_tidak_bisa_mengesahkan_pembayaran_tanpa_bukti_customer(): void
    {
        $pesanan = Pesanan::factory()->create([
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
        ]);

        $this->patch(route('pembayaran.update', $pesanan), [
            'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS,
        ])->assertSessionHasErrors('status_pembayaran');

        $this->assertSame(Pesanan::PEMBAYARAN_BELUM_DIBAYAR, $pesanan->fresh()->status_pembayaran);
    }

    public function test_bukti_pembayaran_dapat_dibuka_admin_dan_tidak_disajikan_dari_storage_public(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $path = UploadedFile::fake()->image('bukti.png')->store('bukti-pembayaran', 'local');
        $pesanan = Pesanan::factory()->create([
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
            'bukti_pembayaran' => $path,
        ]);

        $this->get(route('pembayaran.bukti', $pesanan))->assertOk();
        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_customer_tidak_bisa_membuka_endpoint_bukti_admin(): void
    {
        $pesanan = Pesanan::factory()->create(['bukti_pembayaran' => 'bukti-pembayaran/rahasia.png']);

        $this->actingAs(User::factory()->create())
            ->get(route('pembayaran.bukti', $pesanan))
            ->assertRedirect(route('beranda'));
    }
}