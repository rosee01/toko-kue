<div class="mb-3">
    <label for="nama_kategori" class="form-label">Nama Kategori</label>
    <input type="text" id="nama_kategori" name="nama_kategori"
           class="form-control @error('nama_kategori') is-invalid @enderror"
           value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}" required>
    @error('nama_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-primary">{{ $tombol }}</button>
<a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
