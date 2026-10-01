@extends('layouts.template')

@section('page-title', 'Hasil Pencarian')

@section('content')

{{-- Header Pencarian --}}
<div class="card mb-4">
    <div class="card-body">
        <h4 class="mb-2">Hasil Pencarian untuk "<strong>{{ $keyword }}</strong>"</h4>
        @if(empty($keyword))
            <p class="text-muted mb-0">Silakan ketik kata kunci di atas untuk mencari menu, pesanan, atau pelanggan.</p>
        @else
            <p class="text-muted mb-0">
                Ditemukan {{ $produk->count() }} Menu, {{ $pesanan->count() }} Pesanan, dan {{ $pelanggan->count() }} Pelanggan.
            </p>
        @endif
    </div>
</div>

{{-- 1. Hasil Menu Kue --}}
@if($produk->isNotEmpty())
<div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-cake2"></i> Menu Kue</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th></tr>
                </thead>
                <tbody>
                    @foreach($produk as $p)
                    <tr>
                        <td>{{ $p->name_produk }}</td>
                        <td>{{ $p->kategori->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td>{{ $p->stok }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- 2. Hasil Pesanan --}}
@if($pesanan->isNotEmpty())
<div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-clipboard-check"></i> Pesanan</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>ID</th><th>Pelanggan</th><th>Menu</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    @foreach($pesanan as $pes)
                    <tr>
                        <td>#{{ $pes->id }}</td>
                        <td>{{ $pes->nama_pelanggan }}</td>
                        <td>{{ $pes->menu }}</td>
                        <td>Rp {{ number_format($pes->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-secondary">{{ $pes->status }}</span></td>
                        <td>{{ $pes->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- 3. Hasil Pelanggan (diambil dari pesanan) --}}
@if($pelanggan->isNotEmpty())
<div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person"></i> Pelanggan</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Nama Pelanggan</th><th>Jumlah Pesanan</th></tr>
                </thead>
                <tbody>
                    @foreach($pelanggan as $pel)
                    <tr>
                        <td>{{ $pel->nama_pelanggan }}</td>
                        <td>
                            @php
                                $jumlahPesanan = \App\Models\Pesanan::where('nama_pelanggan', $pel->nama_pelanggan)->count();
                            @endphp
                            {{ $jumlahPesanan }} pesanan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Jika Tidak Ada Hasil --}}
@if(!empty($keyword) && $produk->isEmpty() && $pesanan->isEmpty() && $pelanggan->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-search" style="font-size: 3rem; color: #d1d5db;"></i>
        <p class="mt-3 text-muted">Maaf, tidak ada hasil yang ditemukan untuk kata kunci tersebut.</p>
    </div>
@endif

@endsection