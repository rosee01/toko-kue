<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\User;
use App\Services\PesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StokTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_halaman_stok_menghitung_status_dari_stok_minimum_produk(): void
    {
        Produk::factory()->create(['stok' => 8, 'stok_minimum' => 3]);
        Produk::factory()->create(['stok' => 3, 'stok_minimum' => 3]);
        Produk::factory()->create(['stok' => 0, 'stok_minimum' => 3]);

        $this->get(route('stok.index'))
            ->assertOk()
            ->assertViewHas('stokAman', 1)
            ->assertViewHas('stokMenipis', 1)
            ->assertViewHas('stokHabis', 1)
            ->assertSee('Stok Minimum')
            ->assertSee('Tambah Stok');
    }

    public function test_admin_bisa_menambahkan_stok_produk(): void
    {
        $produk = Produk::factory()->create(['stok' => 4]);

        $this->post(route('stok.tambah'), ['produk_id' => $produk->id_produk, 'jumlah' => 6])
            ->assertRedirect(route('stok.index'));

        $this->assertSame(10, $produk->fresh()->stok);
        $this->assertDatabaseHas('riwayat_stok', [
            'produk_id' => $produk->id_produk,
            'jenis' => 'manual',
            'jumlah' => 6,
            'stok_sebelum' => 4,
            'stok_sesudah' => 10,
        ]);
    }

    public function test_tambah_stok_menolak_jumlah_nol(): void
    {
        $produk = Produk::factory()->create(['stok' => 4]);

        $this->from(route('stok.index'))
            ->post(route('stok.tambah'), ['produk_id' => $produk->id_produk, 'jumlah' => 0])
            ->assertSessionHasErrors('jumlah');

        $this->assertSame(4, $produk->fresh()->stok);
    }

    public function test_halaman_riwayat_menampilkan_perubahan_stok(): void
    {
        $produk = Produk::factory()->create(['name_produk' => 'Brownies Panggang']);
        RiwayatStok::create([
            'produk_id' => $produk->id_produk,
            'jenis' => 'manual',
            'jumlah' => 5,
            'stok_sebelum' => 2,
            'stok_sesudah' => 7,
            'catatan' => 'Stok manual',
        ]);

        $this->get(route('stok.riwayat'))
            ->assertOk()
            ->assertSee('Riwayat Stok')
            ->assertSee('Brownies Panggang')
            ->assertSee('Stok Masuk')
            ->assertSee('+5 pcs')
            ->assertSee('2 pcs')
            ->assertSee('7 pcs');
    }

    public function test_riwayat_mencatat_stok_keluar_dan_pengembalian_pesanan(): void
    {
        $produk = Produk::factory()->create(['stok' => 10]);
        $pesanan = app(PesananService::class)->buat([
            'nama_pelanggan' => 'Budi',
            'produk_id' => $produk->id_produk,
            'jumlah' => 2,
            'status' => \App\Models\Pesanan::STATUS_PENDING,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('pesanan.update', $pesanan), [
                'nama_pelanggan' => $pesanan->nama_pelanggan,
                'produk_id' => $produk->id_produk,
                'jumlah' => 3,
                'status' => \App\Models\Pesanan::STATUS_DIPROSES,
            ])
            ->assertRedirect(route('pesanan.index'));

        $this->assertSame(7, $produk->fresh()->stok);
        $this->assertSame(3, RiwayatStok::where('produk_id', $produk->id_produk)->count());
        $this->assertSame('kembali', RiwayatStok::where('jenis', 'kembali')->value('jenis'));
        $this->assertSame(2, RiwayatStok::where('jenis', 'kembali')->value('jumlah'));
        $this->assertSame(3, RiwayatStok::where('jenis', 'keluar')->latest('id')->value('jumlah'));
    }
}