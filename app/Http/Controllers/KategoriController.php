<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        return $this->halaman();
    }

    private function halaman(?Kategori $formKategori = null): View
    {
        $categories = Kategori::withCount('produk')
            ->with('produk:id_produk,kategori_id,name_produk')
            ->orderBy('nama_kategori')
            ->get();
        $totalKategori = $categories->count();
        $totalProduk = $categories->sum('produk_count');
        $kategoriAktif = $categories->where('is_active', true)->count();

        return view('kategori.index', compact(
            'categories',
            'totalKategori',
            'totalProduk',
            'kategoriAktif',
            'formKategori'
        ))->with('panelOpen', $formKategori !== null);
    }

    public function create(): View
    {
        return $this->halaman(new Kategori());
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? true;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-kategori', 'public');
        }

        Kategori::create($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return $this->halaman($kategori);
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($kategori->foto) {
                Storage::disk('public')->delete($kategori->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-kategori', 'public');
        }

        $kategori->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->produk()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh menu kue.');
        }

        if ($kategori->foto) {
            Storage::disk('public')->delete($kategori->foto);
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
