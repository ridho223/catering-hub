<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Customer | Laily Catering</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>
<body class="auth-page">

<div class="auth-shell">
    <div class="card auth-card">
        <div class="row g-0">

            {{-- Panel kiri --}}
            <div class="col-md-5 d-none d-md-flex auth-side">
                <a href="{{ url('/') }}" class="auth-brand" title="Kembali ke beranda">
                    <span class="auth-brand-logo"><i class="bi bi-basket2-fill"></i></span>
                    <span>Laily Catering<span class="auth-brand-hint">← Kembali ke beranda</span></span>
                </a>

                <div class="auth-side-body">
                    <h2>Selamat datang kembali 👋</h2>
                    <p>Lanjutkan pesanan cateringmu dengan cepat dan mudah.</p>
                    <ul class="auth-benefits">
                        <li><i class="bi bi-check-lg"></i> Pilihan menu beragam</li>
                        <li><i class="bi bi-check-lg"></i> Harga dikonfirmasi admin</li>
                        <li><i class="bi bi-check-lg"></i> Pesanan sesuai kebutuhan</li>
                    </ul>
                </div>
            </div>

            {{-- Form --}}
            <div class="col-md-7">
                <div class="auth-form">

                    <a href="{{ url('/') }}" class="auth-mobile-brand d-md-none">
                        <span class="auth-brand-logo"><i class="bi bi-basket2-fill"></i></span>
                        Laily Catering
                    </a>

                    <h3 class="mb-1">Login</h3>
                    <p class="sub mb-4">Masuk untuk melanjutkan pesananmu</p>

                    @if (session('error'))
                        <div class="alert alert-warning"><i class="bi bi-info-circle me-2"></i>{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    @endif

                    {{-- Nanti ganti href="#" jadi route('customer.google.redirect') kalau backend sudah jadi --}}
                    <a href="#" class="btn-google">
                        <svg width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        Lanjutkan dengan Google
                    </a>

                    <div class="divider"><span>atau login dengan email</span></div>

                    <form action="{{ route('customer.login.submit') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="auth-field has-toggle">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Masukkan password" autocomplete="current-password" required>
                                <button type="button" class="toggle-password" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                            </div>
                            @error('password') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check mb-0">
                                <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">Ingat saya</label>
                            </div>
                            {{-- <a href="#" class="auth-link small">Lupa password?</a> --}}
                        </div>

                        <button type="submit" class="btn btn-auth w-100">Login</button>
                    </form>

                    <p class="auth-switch">
                        Belum punya akun?
                        <a href="{{ route('customer.register') }}" class="auth-link">Daftar sekarang</a>
                    </p>

                    <a href="{{ url('/') }}" class="auth-back"><i class="bi bi-arrow-left"></i> Kembali ke beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/auth.js') }}"></script>
</body>
</html>