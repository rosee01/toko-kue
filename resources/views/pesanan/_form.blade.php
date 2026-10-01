@php
    $produkTerpilih = old('produk_id', $pesanan->produk_id ?? '');
    $jumlahAwal = old('jumlah', $pesanan->jumlah ?? 1);
@endphp
<div class="mb-3">
    <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
    <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror"
           value="{{ old('nama_pelanggan', $pesanan->nama_pelanggan ?? '') }}" required>
    @error('nama_pelanggan')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="produk_id" class="form-label">Menu</label>
    <select id="produk_id" name="produk_id" class="form-select @error('produk_id') is-invalid @enderror" required>
        <option value="">Pilih menu</option>
        @foreach($products as $product)
            <option value="{{ $product->id_produk }}" data-price="{{ $product->harga }}" data-stok="{{ $product->stok }}"
                    @selected($produkTerpilih == $product->id_produk)>
                {{ $product->name_produk }} — Rp {{ number_format($product->harga, 0, ',', '.') }} (stok {{ $product->stok }})
            </option>
        @endforeach
    </select>
    @error('produk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" min="1" class="form-control @error('jumlah') is-invalid @enderror"
               value="{{ $jumlahAwal }}" required>
        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Total Harga</label>
        <input type="text" id="total-tampil" class="form-control" value="Rp 0" readonly>
        <div class="form-text">Dihitung otomatis dari harga menu × jumlah.</div>
    </div>
</div>
@if(!($pesanan->exists ?? false))
    <div class="mb-3">
        <label for="status" class="form-label">Status awal</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="{{ \App\Models\Pesanan::STATUS_PENDING }}" @selected(old('status', \App\Models\Pesanan::STATUS_PENDING) === \App\Models\Pesanan::STATUS_PENDING)>Pesanan Baru</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@else
    <div class="mb-3"><span class="form-label d-block">Status pesanan</span><span class="badge bg-light text-dark">{{ $pesanan->status }}</span><div class="form-text">Status hanya berubah mengikuti alur proses dan pengantaran.</div></div>
@endif
<button type="submit" class="btn btn-primary">{{ $tombol }}</button>
<a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Batal</a>

@section('scripts')
<script>
    (function () {
        const produk = document.getElementById('produk_id');
        const jumlah = document.getElementById('jumlah');
        const total = document.getElementById('total-tampil');
        const rupiah = new Intl.NumberFormat('id-ID');

        function hitung() {
            const harga = Number(produk.selectedOptions[0]?.dataset.price || 0);
            total.value = 'Rp ' + rupiah.format(harga * Number(jumlah.value || 0));
        }

        produk.addEventListener('change', hitung);
        jumlah.addEventListener('input', hitung);
        hitung();
    })();
</script>
@endsection
