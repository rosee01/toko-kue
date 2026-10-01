@extends('auth.layout')

@section('judul', 'Masuk')
@section('judul-kartu', 'Selamat Datang!')
@section('sub-judul', 'Masuk ke akun Anda untuk melanjutkan')

@section('isi')
    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf

        @error('email')
            <div class="alert-custom"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
        @enderror

        <label class="form-label-custom">Email</label>
        <div class="input-group-custom">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda"
                   value="{{ old('email') }}" required autofocus>
        </div>

        <label class="form-label-custom">Password</label>
        <div class="input-group-custom">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control"
                   placeholder="Masukkan password Anda" required>
            <button type="button" class="toggle-password" onclick="togglePassword('password', 'mata')">
                <i class="bi bi-eye" id="mata"></i>
            </button>
        </div>

        <label class="form-check-custom">
            <input type="checkbox" name="remember"> Ingat saya
        </label>

        <button type="submit" class="btn-login">Masuk <i class="bi bi-arrow-right"></i></button>
    </form>

    <div class="divider"><span>Belum punya akun?</span></div>
    <div class="pindah-halaman"><a href="{{ route('register') }}">Daftar sekarang</a></div>
@endsection