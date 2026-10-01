@extends('layouts.template')

@section('page-title', 'Tambah Pesanan')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Tambah Pesanan Baru</h3></div>
    <div class="card-body">
        <form action="{{ route('pesanan.store') }}" method="POST">
            @csrf
            @include('pesanan._form', ['tombol' => 'Simpan'])
        </form>
    </div>
</div>
@endsection
