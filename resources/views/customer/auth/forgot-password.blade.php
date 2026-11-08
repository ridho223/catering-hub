@extends('customer.auth.layout')

@section('title', 'Lupa Password')
@section('side-pill')<i class="bi bi-key"></i> Pemulihan akun @endsection
@section('side-title', 'Lupa password? 🔑')
@section('side-text', 'Masukkan email akunmu. Kami kirim link untuk membuat password baru ke email tersebut.')

@section('side-extra')
    <div class="auth-steps">
        <div class="auth-steps-title">Cara memulihkan akun</div>
        <ol>
            <li><span>1</span><div><b>Masukkan email</b><small>Email yang kamu pakai saat mendaftar</small></div></li>
            <li><span>2</span><div><b>Buka email dari kami</b><small>Cek juga folder spam</small></div></li>
            <li><span>3</span><div><b>Buat password baru</b><small>Lalu login seperti biasa</small></div></li>
        </ol>
    </div>
@endsection

@section('form')
    <div class="auth-icon"><i class="bi bi-key"></i></div>
    <h3 class="mb-1">Lupa Password</h3>
    <p class="sub mb-4">Link reset dikirim ke email akunmu</p>

    <form action="{{ route('customer.password.email') }}" method="POST" novalidate>
        @csrf

        <div class="mb-4">
            <label class="form-label" for="email">Email</label>
            <div class="auth-field">
                <i class="bi bi-envelope field-icon"></i>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="nama@email.com" value="{{ old('email') }}"
                       autocomplete="email" required autofocus>
            </div>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-auth w-100">Kirim Link Reset</button>
    </form>

    <p class="auth-switch">
        Ingat password?
        <a href="{{ route('customer.login') }}" class="auth-link">Kembali ke login</a>
    </p>
@endsection