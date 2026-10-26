<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Laily Catering</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>
<body class="auth-page">

<div class="auth-shell narrow">
    <div class="card auth-card">
        <div class="auth-form text-center-header">

            <div class="text-center mb-4">
                <div class="admin-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <span class="admin-badge"><i class="bi bi-person-gear"></i> Admin Panel</span>
                <h3 class="mt-3 mb-1">Login Admin</h3>
                <p class="sub mb-0">Silakan masuk ke dashboard</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <div class="auth-field">
                        <i class="bi bi-envelope field-icon"></i>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="admin@email.com" value="{{ old('email') }}"
                               autocomplete="email" required autofocus>
                    </div>
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
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

                <button type="submit" class="btn btn-auth w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </form>

            <a href="{{ url('/') }}" class="auth-back"><i class="bi bi-arrow-left"></i> Kembali ke beranda</a>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/auth.js') }}"></script>
</body>
</html>