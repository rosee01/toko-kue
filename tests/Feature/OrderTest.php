<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Pengaturan::simpan('alamat', 'Toko Kue, Bandung');
        config(['services.google_maps.server_key' => 'test-maps-key']);
        Http::fake([
            'routes.googleapis.com/*' => Http::response(['routes' => [['distanceMeters' => 8000]]], 200),
        ]);
    }

    private function payload(array $items): array
    {
        return [
            'nama_pelanggan' => 'Siti',
            'no_telepon' => '08123456789',
            'alamat_pengiriman' => 'Jl. Melati No. 1',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'jenis_pengiriman' => 'hemat',
            'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'items' => $items,
        ];
    }

    public function test_tamu_tidak_bisa_checkout_keranjang(): void
    {
        $produk = Produk::factory()->create();

        $this->postJson(route('order.keranjang'), $this->payload([
            ['produk_id' => $produk->id_produk, 'jumlah' => 1],
        ]))->assertUnauthorized();


        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_checkout_keranjang_membuat_pesanan_per_item_dan_mengurangi_stok(): void
    {
        Pengaturan::simpan('bank_nama', 'Bank Nusantara');
        Pengaturan::simpan('bank_rekening', '1234567890');
        Pengaturan::simpan('bank_pemilik', 'Toko Kue');
        $a = Produk::factory()->create(['harga' => 10000, 'stok' => 10]);
        $b = Produk::factory()->create(['harga' => 20000, 'stok' => 10]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('order.keranjang'), $this->payload([
                ['produk_id' => $a->id_produk, 'jumlah' => 2],
                ['produk_id' => $b->id_produk, 'jumlah' => 1],
            ]))->assertOk();

        $this->assertDatabaseCount('pesanan', 2);
        $this->assertDatabaseHas('pesanan', [
            'menu' => $a->name_produk, 'jumlah' => 2, 'total' => 20000,
            'status' => 'Pending', 'no_telepon' => '08123456789',
        ]);
        $this->assertSame(8, $a->fresh()->stok);
        $this->assertSame(9, $b->fresh()->stok);

        $kode = Pesanan::firstOrFail()->kode_pesanan;
        $response->assertJson(['redirect' => route('order.sukses', ['kode' => $kode])]);

        $this->get(route('order.sukses', ['kode' => $kode]))
            ->assertOk()
            ->assertSee($kode)
            ->assertSee($a->name_produk)
            ->assertSee($b->name_produk)
            ->assertSee('Menunggu konfirmasi admin')
            ->assertDontSee('1234567890')
            ->assertSee('Rp 48.000')
            ->assertSee('Jadwal');
    }

    public function test_checkout_dibatalkan_semua_jika_stok_salah_satu_item_kurang(): void
    {
        $a = Produk::factory()->create(['stok' => 10]);
        $b = Produk::factory()->create(['stok' => 1]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->payload([

                ['produk_id' => $a->id_produk, 'jumlah' => 2],
                ['produk_id' => $b->id_produk, 'jumlah' => 5],
            ]))
            ->assertStatus(422);

        $this->assertDatabaseCount('pesanan', 0);
        $this->assertSame(10, $a->fresh()->stok);
    }

    public function test_harga_dari_browser_diabaikan(): void
    {
        $produk = Produk::factory()->create(['harga' => 50000, 'stok' => 10]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->payload([
                ['produk_id' => $produk->id_produk, 'jumlah' => 2, 'harga' => 1],
            ]))
            ->assertOk();

        $this->assertDatabaseHas('pesanan', ['harga_satuan' => 50000, 'total' => 100000]);
    }

    public function test_produk_nonaktif_tidak_bisa_dipesan(): void
    {
        $produk = Produk::factory()->create(['is_active' => false, 'stok' => 10]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->payload([
                ['produk_id' => $produk->id_produk, 'jumlah' => 1],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('produk_id');

        $this->assertDatabaseCount('pesanan', 0);
        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_pesan_satu_produk_lewat_form_modal(): void
    {
        $produk = Produk::factory()->create(['harga' => 30000, 'stok' => 5]);

        $this->actingAs(User::factory()->create())
            ->post(route('order.store'), [
                'produk_id' => $produk->id_produk,
                'nama_pelanggan' => 'Budi',
                'no_telepon' => '0811111111',
                'alamat_pengiriman' => 'Jl. Mawar 2',
                'metode_pembayaran' => Pesanan::METODE_E_WALLET,
                'jenis_pengiriman' => 'hemat',
                'jadwal_diminta' => now()->addDays(2)->format('Y-m-d H:i:s'),

                'jumlah' => 2,
            ])
            ->assertRedirect(route('order.sukses', ['kode' => Pesanan::firstOrFail()->kode_pesanan]));

        $this->assertDatabaseHas('pesanan', [
            'nama_pelanggan' => 'Budi',
            'total' => 60000,
            'metode_pembayaran' => Pesanan::METODE_E_WALLET,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
        ]);
        $this->assertSame(3, $produk->fresh()->stok);
    }

    public function test_customer_bisa_memilih_cod_dan_pesanan_dicatat_cod(): void
    {
        $produk = Produk::factory()->create(['stok' => 5]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), array_merge($this->payload([
                ['produk_id' => $produk->id_produk, 'jumlah' => 1],
            ]), ['metode_pembayaran' => Pesanan::METODE_COD]))
            ->assertOk();

        $this->assertDatabaseHas('pesanan', [
            'produk_id' => $produk->id_produk,
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
    }

    public function test_metode_pembayaran_wajib_dipilih_dan_harus_valid(): void
    {
        $produk = Produk::factory()->create();
        $user = User::factory()->create();
        $payload = $this->payload([['produk_id' => $produk->id_produk, 'jumlah' => 1]]);
        unset($payload['metode_pembayaran']);

        $this->actingAs($user)
            ->postJson(route('order.keranjang'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('metode_pembayaran');

        $payload['metode_pembayaran'] = 'unknown';
        $this->postJson(route('order.keranjang'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('metode_pembayaran');

        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_halaman_sukses_membutuhkan_kode_pesanan_milik_customer(): void
    {
        $customer = User::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => User::factory()->create()->id,
            'kode_pesanan' => 'TK-RAHASIA-0001',
        ]);

        $this->actingAs($customer)
            ->get(route('order.sukses'))
            ->assertRedirect(route('order.riwayat'));

        $this->get(route('order.sukses', ['kode' => $pesanan->kode_pesanan]))
            ->assertNotFound();
    }

    public function test_satu_checkout_memakai_satu_kode_dan_tercatat_atas_akun_customer(): void
    {
        $a = Produk::factory()->create(['stok' => 10]);
        $b = Produk::factory()->create(['stok' => 10]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('order.keranjang'), $this->payload([
                ['produk_id' => $a->id_produk, 'jumlah' => 1],
                ['produk_id' => $b->id_produk, 'jumlah' => 1],
            ]))
            ->assertOk();

        $this->assertSame(1, Pesanan::query()->distinct()->count('kode_pesanan'));
        $this->assertSame(2, Pesanan::where('user_id', $user->id)->count());
        $this->assertStringStartsWith('TK-', Pesanan::firstOrFail()->kode_pesanan);
    }

    public function test_pesanan_customer_muncul_di_menu_admin_lengkap_dengan_kontak(): void
    {
        $produk = Produk::factory()->create(['stok' => 10]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('order.keranjang'), $this->payload([

                ['produk_id' => $produk->id_produk, 'jumlah' => 1],
            ]) + ['catatan' => 'Tulis Happy Birthday'])
            ->assertOk();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('pesanan.index'))
            ->assertOk()
            ->assertSee('Siti')
            ->assertSee('08123456789')
            ->assertSee('Jl. Melati No. 1')
            ->assertSee('Tulis Happy Birthday')
            ->assertSee('wa.me/628123456789', false);
    }

    public function test_customer_bisa_mengunggah_bukti_ke_storage_private_dan_menunggu_verifikasi(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $customer->id,
            'kode_pesanan' => 'TK-BUKTI-01',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
        ]);

        $this->actingAs($customer)
            ->post(route('order.bukti-pembayaran', $pesanan->kode_pesanan), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.png'),
            ])
            ->assertRedirect();

        $pesanan->refresh();
        $this->assertSame(Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI, $pesanan->status_pembayaran);
        $this->assertNotNull($pesanan->pembayaran_dikirim_pada);
        Storage::disk('local')->assertExists($pesanan->bukti_pembayaran);
    }

    public function test_customer_hanya_bisa_mengunggah_bukti_miliknya_dan_file_harus_gambar(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => User::factory()->create()->id,
            'kode_pesanan' => 'TK-BUKTI-02',
            'metode_pembayaran' => Pesanan::METODE_E_WALLET,
        ]);

        $this->actingAs($customer)
            ->post(route('order.bukti-pembayaran', $pesanan->kode_pesanan), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.png'),
            ])
            ->assertNotFound();

        $pesanan->update(['user_id' => $customer->id]);
        $this->from(route('order.sukses', ['kode' => $pesanan->kode_pesanan]))
            ->post(route('order.bukti-pembayaran', $pesanan->kode_pesanan), [
                'bukti_pembayaran' => UploadedFile::fake()->create('bukti.pdf', 20, 'application/pdf'),
            ])
            ->assertSessionHasErrors('bukti_pembayaran');
    }

    public function test_cod_atau_pesanan_sudah_lunas_tidak_menerima_bukti_transfer(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create();
        $cod = Pesanan::factory()->create([
            'user_id' => $customer->id,
            'kode_pesanan' => 'TK-COD-01',
            'metode_pembayaran' => Pesanan::METODE_COD,
            'status_pembayaran' => Pesanan::PEMBAYARAN_COD,
        ]);
        $paid = Pesanan::factory()->create([
            'user_id' => $customer->id,
            'kode_pesanan' => 'TK-LUNAS-02',
            'metode_pembayaran' => Pesanan::METODE_TRANSFER_BANK,
            'status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS,
        ]);

        foreach ([$cod, $paid] as $pesanan) {
            $this->actingAs($customer)
                ->post(route('order.bukti-pembayaran', $pesanan->kode_pesanan), [
                    'bukti_pembayaran' => UploadedFile::fake()->image('bukti.png'),
                ])
                ->assertStatus(422);
        }
    }

    public function test_bukti_ulang_memperbarui_seluruh_item_checkout_dan_menghapus_file_lama(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create();
        $oldPath = UploadedFile::fake()->image('bukti-lama.png')->store('bukti-pembayaran', 'local');
        $items = collect([1, 2])->map(fn () => Pesanan::factory()->create([
                'user_id' => $customer->id,
                'kode_pesanan' => 'TK-ULANG-01',
                'metode_pembayaran' => Pesanan::METODE_E_WALLET,
                'status_pembayaran' => Pesanan::PEMBAYARAN_DITOLAK,
                'bukti_pembayaran' => $oldPath,
                'catatan_pembayaran' => 'Bukti kurang jelas.',
        ]));

        $this->actingAs($customer)
                ->post(route('order.bukti-pembayaran', 'TK-ULANG-01'), [
                    'bukti_pembayaran' => UploadedFile::fake()->image('bukti-baru.png'),
                ])
                ->assertRedirect();

        $items->each(function (Pesanan $pesanan) use ($oldPath): void {
                $pesanan->refresh();
                $this->assertSame(Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI, $pesanan->status_pembayaran);
                $this->assertNotSame($oldPath, $pesanan->bukti_pembayaran);
                $this->assertNull($pesanan->catatan_pembayaran);
        });
        $this->assertSame(1, Pesanan::where('kode_pesanan', 'TK-ULANG-01')->distinct('bukti_pembayaran')->count('bukti_pembayaran'));
        Storage::disk('local')->assertMissing($oldPath);
    }
}