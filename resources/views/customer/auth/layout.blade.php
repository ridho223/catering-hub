<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>
<body class="auth-page cust">

<div class="auth-shell">
    <div class="card auth-card">

        {{-- Banner untuk HP & tablet (panel kiri disembunyikan di layar kecil) --}}
        <div class="auth-mobile-hero d-lg-none">
            <a href="{{ url('/') }}" class="auth-brand" title="Kembali ke beranda">
                <span class="auth-brand-logo"><i class="bi bi-basket2-fill"></i></span>
                <span>{{ config('app.name') }}</span>
            </a>
            <h2>@yield('side-title')</h2>
            <p>@yield('side-text')</p>
        </div>

        <div class="row g-0">

            {{-- Panel kiri (desktop) --}}
            <div class="col-lg-5 d-none d-lg-flex auth-side">
                <div class="auth-side-photo" aria-hidden="true"></div>

                <a href="{{ url('/') }}" class="auth-brand" title="Kembali ke beranda">
                    <span class="auth-brand-logo"><i class="bi bi-basket2-fill"></i></span>
                    <span>{{ config('app.name') }}<span class="auth-brand-hint">← Kembali ke beranda</span></span>
                </a>

                <div class="auth-side-body">
                    @hasSection('side-pill')
                        <span class="auth-pill">@yield('side-pill')</span>
                    @endif
                    <h2>@yield('side-title')</h2>
                    <p>@yield('side-text')</p>
                    @yield('side-list')

                    @hasSection('side-extra')
                        @yield('side-extra')
                    @else
                        <div class="auth-steps">
                            <div class="auth-steps-title">Cara pesan</div>
                            <ol>
                                <li><span>1</span><div><b>Pilih paket atau menu</b><small>Sesuaikan dengan acaramu</small></div></li>
                                <li><span>2</span><div><b>Bayar DP</b><small>Pesanan diproses setelah pembayaran terverifikasi</small></div></li>
                                <li><span>3</span><div><b>Pesanan disiapkan</b><small>Diambil atau diantar sesuai pilihanmu</small></div></li>
                            </ol>
                        </div>
                    @endif
                </div>

                <div class="auth-side-foot">
                    <i class="bi bi-shield-check"></i>
                    <span>Data kamu aman dan hanya dipakai untuk keperluan pesanan.</span>
                </div>
            </div>

            {{-- Form --}}
            <div class="col-lg-7">
                <div class="auth-form">

                    @if (session('error'))
                        <div class="alert alert-warning"><i class="bi bi-info-circle me-2"></i>{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    @endif

                    @yield('form')

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