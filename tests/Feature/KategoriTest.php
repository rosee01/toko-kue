<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_daftar_kategori_tampil(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Kue Tart']);
        Produk::factory()->create(['kategori_id' => $kategori->id_kategori]);

        $this->get(route('kategori.index'))
            ->assertOk()
            ->assertSee('Kue Tart')
            ->assertSee('Total Kategori')
            ->assertSee('Produk Terdaftar')
            ->assertSee('Kategori Aktif')
            ->assertSee('Detail Kategori')
            ->assertDontSee('Tambah Kategori')
            ->assertDontSee('Simpan Kategori');
    }

    public function test_form_tambah_ditampilkan_di_panel_samping(): void
    {
        $this->get(route('kategori.create'))
            ->assertOk()
            ->assertSee('kategori-drawer', false)
            ->assertSee('Gambar / Ikon Kategori')
            ->assertSee('Simpan Kategori');
    }

    public function test_tambah_kategori(): void
    {
        $this->post(route('kategori.store'), ['nama_kategori' => 'Pastry'])
            ->assertRedirect(route('kategori.index'));

        $this->assertDatabaseHas('kategori_produk', ['nama_kategori' => 'Pastry']);
    }

    public function test_tambah_kategori_menyimpan_deskripsi_gambar_dan_status(): void
    {
        Storage::fake('public');

        $this->post(route('kategori.store'), [
            'nama_kategori' => 'Brownies',
            'deskripsi' => 'Aneka brownies panggang.',
            'is_active' => 0,
            'foto' => UploadedFile::fake()->image('brownies.jpg'),
        ])->assertRedirect(route('kategori.index'));

        $kategori = Kategori::where('nama_kategori', 'Brownies')->firstOrFail();
        $this->assertSame('Aneka brownies panggang.', $kategori->deskripsi);
        $this->assertFalse($kategori->is_active);
        Storage::disk('public')->assertExists($kategori->foto);
    }

    public function test_nama_kategori_harus_unik(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Pastry']);

        $this->post(route('kategori.store'), ['nama_kategori' => 'Pastry'])
            ->assertSessionHasErrors('nama_kategori');
    }

    public function test_ubah_kategori_dengan_nama_sendiri_tidak_dianggap_duplikat(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Pastry']);

        $this->put(route('kategori.update', $kategori), ['nama_kategori' => 'Pastry'])
            ->assertSessionHasNoErrors();
    }

    public function test_hapus_kategori_kosong(): void
    {
        $kategori = Kategori::factory()->create();

        $this->delete(route('kategori.destroy', $kategori))->assertRedirect(route('kategori.index'));

        $this->assertModelMissing($kategori);
    }

    public function test_kategori_yang_dipakai_produk_tidak_bisa_dihapus(): void
    {
        $produk = Produk::factory()->create();

        $this->delete(route('kategori.destroy', $produk->kategori))->assertSessionHas('error');

        $this->assertModelExists($produk->kategori);
    }
}