@extends('layouts.template')

@section('page-title', 'Edit Pesanan')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Edit Pesanan</h3></div>
    <div class="card-body">
        <form action="{{ route('pesanan.update', $pesanan) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pesanan._form', ['tombol' => 'Perbarui'])
        </form>
    </div>
</div>
@endsection
