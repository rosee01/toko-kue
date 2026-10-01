<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'name_produk' => 'Tart Coklat',
            'deskripsi' => 'Tart coklat lembut',
            'harga' => 185000,
            'stok' => 8,
            'stok_minimum' => 3,
            'is_active' => 1,
            'kategori_id' => Kategori::factory()->create()->id_kategori,
        ], $override);
    }

    public function test_daftar_produk_tampil(): void
    {
        Produk::factory()->create(['name_produk' => 'Bolu Pandan', 'stok' => 0]);

        $this->get(route('produk.index'))
            ->assertOk()
            ->assertSee('Bolu Pandan')
            ->assertSee('Total Produk')
            ->assertSee('Produk Aktif')
            ->assertSee('Stok Menipis')
            ->assertSee('Stok Habis')
            ->assertSee('Semua Kategori')
            ->assertSee('Tambah Produk');
    }

    public function test_tambah_produk_tanpa_foto(): void
    {
        $this->post(route('produk.store'), $this->payload())->assertRedirect(route('produk.index'));

        $this->assertDatabaseHas('produk', ['name_produk' => 'Tart Coklat', 'harga' => 185000]);
        $this->assertDatabaseHas('riwayat_stok', [
            'produk_id' => Produk::where('name_produk', 'Tart Coklat')->value('id_produk'),
            'jenis' => 'manual',
            'jumlah' => 8,
            'stok_sebelum' => 0,
            'stok_sesudah' => 8,
        ]);
    }

    public function test_tambah_produk_dengan_foto(): void
    {
        Storage::fake('public');

        $this->post(route('produk.store'), $this->payload(['foto' => UploadedFile::fake()->image('kue.jpg')]))
            ->assertSessionHasNoErrors();

        $foto = Produk::firstOrFail()->foto;
        $this->assertNotNull($foto);
        Storage::disk('public')->assertExists($foto);
    }

    public function test_tambah_produk_menyimpan_stok_minimum_status_dan_galeri(): void
    {
        Storage::fake('public');
        $fotos = [
            UploadedFile::fake()->image('utama.jpg'),
            UploadedFile::fake()->image('samping.jpg'),
        ];

        $this->post(route('produk.store'), $this->payload([
            'stok_minimum' => 4,
            'is_active' => 0,
            'fotos' => $fotos,
        ]))->assertRedirect(route('produk.index'));

        $produk = Produk::firstOrFail();
        $this->assertSame(4, $produk->stok_minimum);
        $this->assertFalse($produk->is_active);
        $this->assertCount(1, $produk->foto_galeri);
        Storage::disk('public')->assertExists($produk->foto);
        Storage::disk('public')->assertExists($produk->foto_galeri[0]);
    }

    public function test_edit_produk_mencatat_penyesuaian_stok(): void
    {
        $produk = Produk::factory()->create(['stok' => 6]);

        $this->put(route('produk.update', $produk), $this->payload([
            'kategori_id' => $produk->kategori_id,
            'stok' => 9,
        ]))->assertRedirect(route('produk.index'));

        $this->assertDatabaseHas('riwayat_stok', [
            'produk_id' => $produk->id_produk,
            'jenis' => 'manual',
            'jumlah' => 3,
            'stok_sebelum' => 6,
            'stok_sesudah' => 9,
        ]);
    }

    public function test_validasi_produk(): void
    {
        $this->post(route('produk.store'), $this->payload(['harga' => -1, 'stok' => 'abc', 'kategori_id' => 999]))
            ->assertSessionHasErrors(['harga', 'stok', 'kategori_id']);
    }

    public function test_ubah_produk_mengganti_dan_menghapus_foto_lama(): void
    {
        Storage::fake('public');
        $lama = UploadedFile::fake()->image('lama.jpg')->store('foto-produk', 'public');
        $produk = Produk::factory()->create(['foto' => $lama]);

        $this->put(route('produk.update', $produk), $this->payload([
            'kategori_id' => $produk->kategori_id,
            'foto' => UploadedFile::fake()->image('baru.jpg'),
        ]))->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($lama);
        Storage::disk('public')->assertExists($produk->fresh()->foto);
    }

    public function test_hapus_produk_ikut_menghapus_foto(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('kue.jpg')->store('foto-produk', 'public');
        $produk = Produk::factory()->create(['foto' => $path]);

        $this->delete(route('produk.destroy', $produk))->assertRedirect(route('produk.index'));

        $this->assertModelMissing($produk);
        Storage::disk('public')->assertMissing($path);
    }
}