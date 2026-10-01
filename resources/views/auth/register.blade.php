@extends('auth.layout')

@section('judul', 'Daftar')
@section('judul-kartu', 'Buat Akun')
@section('sub-judul', 'Daftar untuk mulai memesan kue favoritmu')

@section('isi')
    <form action="{{ route('register.attempt') }}" method="POST">
        @csrf

        <label class="form-label-custom">Nama lengkap</label>
        <div class="input-group-custom">
            <i class="bi bi-person input-icon"></i>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
        </div>
        @error('name')<div class="pesan-error">{{ $message }}</div>@enderror

        <label class="form-label-custom">Email</label>
        <div class="input-group-custom">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
        </div>
        @error('email')<div class="pesan-error">{{ $message }}</div>@enderror

        <label class="form-label-custom">Password</label>
        <div class="input-group-custom">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimal 8 karakter" required>
            <button type="button" class="toggle-password" onclick="togglePassword('password', 'mata')">
                <i class="bi bi-eye" id="mata"></i>
            </button>
        </div>
        @error('password')<div class="pesan-error">{{ $message }}</div>@enderror

        <label class="form-label-custom">Ulangi password</label>
        <div class="input-group-custom">
            <i class="bi bi-lock-fill input-icon"></i>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Ketik ulang password" required>
        </div>

        <button type="submit" class="btn-login">Daftar <i class="bi bi-arrow-right"></i></button>
    </form>

    <div class="divider"><span>Sudah punya akun?</span></div>
    <div class="pindah-halaman"><a href="{{ route('login') }}">Masuk di sini</a></div>
@endsection