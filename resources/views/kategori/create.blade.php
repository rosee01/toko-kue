@extends('layouts.template')

@section('page-title', 'Tambah Kategori')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Tambah Kategori Baru</h3></div>
    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            @include('kategori._form', ['tombol' => 'Simpan'])
        </form>
    </div>
</div>
@endsection
