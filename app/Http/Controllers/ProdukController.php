<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukRequest;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(): View
    {
        $produks = Produk::with('kategori')->orderBy('name_produk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $totalProduk = $produks->count();
        $produkAktif = $produks->where('is_active', true)->count();
        $stokMenipis = $produks->filter(fn ($produk) => $produk->stok > 0 && $produk->stok <= $produk->stok_minimum)->count();
        $stokHabis = $produks->where('stok', 0)->count();

        return view('produk.index', compact(
            'produks',
            'kategoris',
            'totalProduk',
            'produkAktif',
            'stokMenipis',
            'stokHabis'
        ));
    }

    public function create(): View
    {
        return view('produk.create', ['kategoris' => Kategori::where('is_active', true)->orderBy('nama_kategori')->get()]);
    }

    public function store(ProdukRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $fotos = $data['fotos'] ?? [];
        unset($data['fotos']);

        if ($fotos !== []) {
            $paths = $this->simpanFotoGaleri($fotos);
            $data['foto'] = array_shift($paths);
            $data['foto_galeri'] = $paths;
        } elseif ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-produk', 'public');
        }

        $produk = Produk::create($data);

        if ($produk->stok > 0) {
            RiwayatStok::create([
                'produk_id' => $produk->id_produk,
                'jenis' => 'manual',
                'jumlah' => $produk->stok,
                'stok_sebelum' => 0,
                'stok_sesudah' => $produk->stok,
                'catatan' => 'Stok awal produk',
            ]);
        }

        return redirect()->route('produk.index')->with('success', 'Menu kue berhasil ditambahkan.');
    }

    public function edit(Produk $produk): View
    {
        return view('produk.edit', [
            'produk' => $produk,
            'kategoris' => Kategori::where('is_active', true)
                ->orWhere('id_kategori', $produk->kategori_id)
                ->orderBy('nama_kategori')
                ->get(),
        ]);
    }

    public function update(ProdukRequest $request, Produk $produk): RedirectResponse
    {
        $data = $request->validated();
        $fotos = $data['fotos'] ?? [];
        unset($data['fotos'], $data['foto']);
        $stokSebelum = $produk->stok;

        if ($fotos !== []) {
            $this->hapusFotoGaleri($produk);
            $paths = $this->simpanFotoGaleri($fotos);
            $data['foto'] = array_shift($paths);
            $data['foto_galeri'] = $paths;
        } elseif ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-produk', 'public');
        }

        $produk->update($data);

        if ($stokSebelum !== $produk->stok) {
            $stokBertambah = $produk->stok > $stokSebelum;
            RiwayatStok::create([
                'produk_id' => $produk->id_produk,
                'jenis' => $stokBertambah ? 'manual' : 'keluar',
                'jumlah' => abs($produk->stok - $stokSebelum),
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $produk->stok,
                'catatan' => 'Penyesuaian stok melalui edit produk',
            ]);
        }

        return redirect()->route('produk.index')->with('success', 'Menu kue berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $this->hapusFotoGaleri($produk);

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Menu kue berhasil dihapus.');
    }

    private function simpanFotoGaleri(array $fotos): array
    {
        return array_map(
            fn ($foto) => $foto->store('foto-produk', 'public'),
            $fotos
        );
    }

    private function hapusFotoGaleri(Produk $produk): void
    {
        $paths = array_filter(array_merge([$produk->foto], $produk->foto_galeri ?? []));

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }
}
