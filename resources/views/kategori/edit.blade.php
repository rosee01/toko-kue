@extends('layouts.template')

@section('page-title', 'Edit Kategori')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Edit Kategori</h3></div>
    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori) }}" method="POST">
            @csrf
            @method('PUT')
            @include('kategori._form', ['tombol' => 'Perbarui'])
        </form>
    </div>
</div>
@endsection
