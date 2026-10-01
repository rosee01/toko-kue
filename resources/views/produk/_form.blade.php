<div class="mb-3">
    <label for="name_produk" class="form-label">Nama Menu</label>
    <input type="text" name="name_produk" id="name_produk" class="form-control @error('name_produk') is-invalid @enderror"
           value="{{ old('name_produk', $produk->name_produk ?? '') }}" required>
    @error('name_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi</label>
    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="harga" class="form-label">Harga (Rp)</label>
        <input type="number" min="0" step="1" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror"
               value="{{ old('harga', $produk->harga ?? '') }}" required>
        @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" min="0" step="1" name="stok" id="stok" class="form-control @error('stok') is-invalid @enderror"
               value="{{ old('stok', $produk->stok ?? '') }}" required>
        @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="stok_minimum" class="form-label">Stok Minimum</label>
        <input type="number" min="0" step="1" name="stok_minimum" id="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror"
               value="{{ old('stok_minimum', $produk->stok_minimum ?? 3) }}" required>
        @error('stok_minimum')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="is_active" class="form-label">Status Produk</label>
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" @checked(old('is_active', $produk->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
        @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label for="kategori_id" class="form-label">Kategori</label>
    @if($kategoris->isEmpty())
        <div class="alert alert-warning mb-0">
            Belum ada kategori. <a href="{{ route('kategori.create') }}">Tambahkan kategori</a> terlebih dahulu.
        </div>
    @else
        <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
            <option value="">Pilih kategori</option>
            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id_kategori }}" @selected(old('kategori_id', $produk->kategori_id ?? null) == $kategori->id_kategori)>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @endif
</div>
<div class="mb-3">
    <label for="fotos" class="form-label">Foto Produk</label>
    @if(!empty($produk->foto) || !empty($produk->foto_galeri))
        <div class="d-flex flex-wrap gap-2 mb-2">
            @foreach(array_filter(array_merge([$produk->foto], $produk->foto_galeri ?? [])) as $fotoSaatIni)
                <img src="{{ asset('storage/' . $fotoSaatIni) }}" alt="Foto {{ $produk->name_produk }}" class="img-thumbnail" style="height: 75px; width: 85px; object-fit: cover">
            @endforeach
        </div>
    @endif
    <input type="file" name="fotos[]" id="fotos" class="form-control @error('fotos') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
    <div class="form-text">Pilih hingga 5 foto, JPG, PNG, atau WebP, maksimal 5 MB per foto. Foto pertama menjadi foto utama.{{ !empty($produk->foto) ? ' Pilihan foto baru akan mengganti galeri saat ini.' : '' }}</div>
    @error('fotos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @error('fotos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @error('foto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-success" @disabled($kategoris->isEmpty())>{{ $tombol }}</button>
<a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
