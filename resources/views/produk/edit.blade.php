@extends('layouts.template')

@section('page-title', 'Edit Menu Kue')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Edit Menu</h3></div>
    <div class="card-body">
        <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('produk._form', ['tombol' => 'Perbarui'])
        </form>
    </div>
</div>
@endsection
