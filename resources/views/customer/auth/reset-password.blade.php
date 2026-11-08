@extends('customer.auth.layout')

@section('title', 'Reset Password')
@section('side-pill')<i class="bi bi-shield-lock"></i> Keamanan akun @endsection
@section('side-title', 'Buat password baru 🔒')
@section('side-text', 'Gunakan password yang kuat dan belum pernah kamu pakai di tempat lain.')

@section('side-extra')
    <div class="auth-steps">
        <div class="auth-steps-title">Tips password kuat</div>
        <ol>
            <li><span><i class="bi bi-check-lg"></i></span><div><b>Minimal 8 karakter</b><small>Makin panjang makin aman</small></div></li>
            <li><span><i class="bi bi-check-lg"></i></span><div><b>Campur huruf besar, kecil, dan angka</b><small>Tambah simbol kalau bisa</small></div></li>
            <li><span><i class="bi bi-check-lg"></i></span><div><b>Jangan pakai ulang</b><small>Bedakan dengan password akun lain</small></div></li>
        </ol>
    </div>
@endsection

@section('form')
    <div class="auth-icon"><i class="bi bi-shield-lock"></i></div>
    <h3 class="mb-1">Reset Password</h3>
    <p class="sub mb-4">Masukkan password baru untuk akunmu</p>

    <form action="{{ route('customer.password.update') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <div class="auth-field">
                <i class="bi bi-envelope field-icon"></i>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $email) }}" autocomplete="email" required readonly>
            </div>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Password baru</label>
            <div class="auth-field has-toggle">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimal 8 karakter" autocomplete="new-password" required autofocus>
                <button type="button" class="toggle-password" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @else
                <div class="pw-meter" id="pwMeter" data-level="0"><span></span><span></span><span></span><span></span></div>
                <small class="pw-text" id="pwText">Minimal 8 karakter</small>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Ulangi password baru</label>
            <div class="auth-field has-toggle">
                <i class="bi bi-lock-fill field-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" placeholder="Ketik ulang password"
                       autocomplete="new-password" required>
                <button type="button" class="toggle-password" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
            </div>
            <small id="matchHint" class="field-hint"></small>
        </div>

        <button type="submit" class="btn btn-auth w-100">Simpan Password Baru</button>
    </form>

    <p class="auth-switch">
        <a href="{{ route('customer.login') }}" class="auth-link">Kembali ke login</a>
    </p>
@endsection