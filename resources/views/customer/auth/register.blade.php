<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Laily Catering</title>

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
                    <h2>Gabung bersama kami 🍽️</h2>
                    <p>Buat akun dan nikmati kemudahan memesan catering untuk berbagai acara.</p>
                    <ul class="auth-benefits">
                        <li><i class="bi bi-check-lg"></i> Gratis dan cepat</li>
                        <li><i class="bi bi-check-lg"></i> Pilih paket atau susun menu sendiri</li>
                        <li><i class="bi bi-check-lg"></i> Alamat tersimpan untuk pesanan berikutnya</li>
                    </ul>
                </div>

                <div class="auth-side-foot">
                    <i class="bi bi-shield-check"></i>
                    <span>Data kamu aman dan hanya dipakai untuk keperluan pesanan.</span>
                </div>
            </div>

            {{-- Form --}}
            <div class="col-md-7">
                <div class="auth-form">

                    <a href="{{ url('/') }}" class="auth-mobile-brand d-md-none">
                        <span class="auth-brand-logo"><i class="bi bi-basket2-fill"></i></span>
                        Laily Catering
                    </a>

                    <h3 class="mb-1">Buat Akun</h3>
                    <p class="sub mb-4">Daftar untuk mulai memesan catering</p>

                    @if (session('error'))
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
                    @endif

                    {{-- Nanti ganti href="#" jadi route('customer.google.redirect') kalau backend sudah jadi --}}
                    <a href="#" class="btn-google">
                        <svg width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        Daftar dengan Google
                    </a>

                    <div class="divider"><span>atau daftar dengan email</span></div>

                    <form action="{{ route('customer.register.submit') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="name">Nama lengkap</label>
                            <div class="auth-field">
                                <i class="bi bi-person field-icon"></i>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Nama kamu" value="{{ old('name') }}"
                                       autocomplete="name" required autofocus>
                            </div>
                            @error('name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <div class="auth-field">
                                    <i class="bi bi-envelope field-icon"></i>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="nama@email.com" value="{{ old('email') }}"
                                           autocomplete="email" required>
                                </div>
                                @error('email') <div class="field-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">No. HP / WhatsApp</label>
                                <div class="auth-field">
                                    <i class="bi bi-telephone field-icon"></i>
                                    <input type="tel" id="phone" name="phone" inputmode="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="08xxxxxxxxxx" value="{{ old('phone') }}"
                                           autocomplete="tel" required>
                                </div>
                                @error('phone') <div class="field-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Alamat pengantaran</label>
                            <div class="auth-field is-textarea">
                                <i class="bi bi-geo-alt field-icon"></i>
                                <textarea id="address" name="address" rows="2"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Jalan, nomor, kecamatan, kota"
                                          autocomplete="street-address" required>{{ old('address') }}</textarea>
                            </div>
                            @error('address') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="password">Password</label>
                                <div class="auth-field has-toggle">
                                    <i class="bi bi-lock field-icon"></i>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Min. 8 karakter" autocomplete="new-password" required>
                                    <button type="button" class="toggle-password" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                                </div>
                                @error('password')
                                    <div class="field-error">{{ $message }}</div>
                                @else
                                    <small class="field-hint">Minimal 8 karakter</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                                <div class="auth-field has-toggle">
                                    <i class="bi bi-lock-fill field-icon"></i>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-control" placeholder="Ulangi password"
                                           autocomplete="new-password" required>
                                    <button type="button" class="toggle-password" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                                </div>
                                <small id="matchHint" class="field-hint"></small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-auth w-100">Daftar</button>
                    </form>

                    <p class="auth-switch">
                        Sudah punya akun?
                        <a href="{{ route('customer.login') }}" class="auth-link">Login</a>
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