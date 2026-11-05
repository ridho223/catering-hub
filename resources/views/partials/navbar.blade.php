@php
    // Badge jumlah isi keranjang. Menyesuaikan jika keranjang disimpan di session('cart') sebagai array.
    $cartCount = is_array(session('cart')) ? count(session('cart')) : 0;

    $customerUser = auth('customer')->user();
    $customerName = $customerUser?->customer?->name ?? $customerUser?->email;

    $links = [
        ['home',                     'bi-house-door',  'Home',      request()->routeIs('home')],
        ['customer.packages.index',  'bi-box-seam',    'Paket',     request()->routeIs('customer.packages.*')],
        ['customer.prasmanan.index', 'bi-egg-fried',   'Prasmanan', request()->routeIs('customer.prasmanan.*')],
        ['about',                    'bi-info-circle', 'Tentang',   request()->routeIs('about')],
        ['contact',                  'bi-telephone',   'Kontak',    request()->routeIs('contact')],
    ];
@endphp

<style>
    .bjn { --brand:#198754; --brand-dark:#126640; --ink:#1e2922; --mute:#64746b; --line:#e6ebe7; background:#fff; border-bottom:1px solid var(--line); }
    .bjn .navbar-brand { display:flex; align-items:center; gap:.6rem; font-weight:800; color:var(--ink); letter-spacing:.2px; }
    .bjn-logo { width:38px; height:38px; border-radius:12px; background:#eaf5ed; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
    .bjn .nav-link { position:relative; color:var(--mute); font-weight:600; font-size:.93rem; padding:.55rem .85rem; border-radius:10px; display:flex; align-items:center; gap:.4rem; }
    .bjn .nav-link:hover { color:var(--brand-dark); background:#f3f8f4; }
    .bjn .nav-link.active { color:var(--brand-dark); background:#eaf5ed; }
    .bjn-cart { position:relative; }
    .bjn-badge { background:var(--brand); color:#fff; border-radius:999px; font-size:.7rem; font-weight:700; min-width:19px; height:19px; padding:0 .35rem; display:inline-flex; align-items:center; justify-content:center; }
    .bjn-toggler { border:0; box-shadow:none !important; padding:.35rem .55rem; border-radius:10px; }
    .bjn-toggler:hover { background:#f3f8f4; }

    .bjn-user { display:flex; align-items:center; gap:.5rem; border:1px solid var(--line); background:#fff; border-radius:999px; padding:.25rem .75rem .25rem .3rem; color:var(--ink); font-weight:600; font-size:.9rem; }
    .bjn-user:hover { border-color:#cde2d3; }
    .bjn-user::after { display:none !important; }
    .bjn-av { width:30px; height:30px; border-radius:50%; background:var(--brand); color:#fff; font-weight:700; font-size:.85rem; display:flex; align-items:center; justify-content:center; }
    .bjn-uname { max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bjn .dropdown-menu { border:1px solid var(--line); border-radius:14px; box-shadow:0 14px 32px rgba(0,0,0,.1); padding:.4rem; min-width:220px; }
    .bjn .dropdown-item { border-radius:9px; padding:.5rem .75rem; font-size:.92rem; display:flex; align-items:center; gap:.5rem; }
    .bjn .dropdown-item:hover { background:#f3f8f4; }
    .bjn .dropdown-item.active { background:#eaf5ed; color:var(--brand-dark); }
    .bjn .dropdown-item:active { background:var(--brand); color:#fff; }
    .bjn-ddhead { padding:.5rem .75rem .6rem; border-bottom:1px solid var(--line); margin-bottom:.35rem; }
    .bjn-ddhead small { color:var(--mute); display:block; }
    .bjn-ddhead b { color:var(--ink); }

    /* HP / layar kecil: menu jadi panel putih yang melayang di bawah navbar,
       jadi tidak lagi menyatu dengan isi halaman */
    @media (max-width:991.98px){
        .bjn .navbar-collapse {
            position:absolute; top:100%; left:0; right:0; z-index:1030;
            background:#fff;
            border-bottom:1px solid var(--line);
            box-shadow:0 16px 28px rgba(20,40,28,.12);
            padding:.5rem 1rem 1rem;
            max-height:calc(100vh - 70px); overflow-y:auto;
        }
        .bjn .navbar-nav { padding-top:.25rem; }
        .bjn-actions { padding-top:.8rem; margin-top:.6rem; border-top:1px solid var(--line); }
        .bjn-actions .btn { width:100%; }
        .bjn .dropdown-menu { position:static !important; transform:none !important; width:100%; box-shadow:none; margin-top:.4rem; }
    }
</style>

<nav class="navbar navbar-expand-lg bjn sticky-top">
    <div class="container">

        <a class="navbar-brand" href="{{ route('home') }}">
            <span class="bjn-logo">🍱</span>
            <span>KEDAI BANGJO</span>
        </a>

        <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-1">
            <a href="{{ route('cart.index') }}" class="nav-link bjn-cart p-2 {{ request()->routeIs('cart.*') ? 'active' : '' }}" aria-label="Keranjang">
                <i class="bi bi-cart3 fs-5"></i>
                @if ($cartCount > 0)<span class="bjn-badge">{{ $cartCount }}</span>@endif
            </a>
        </div>

        <button class="navbar-toggler bjn-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                aria-controls="menu" aria-expanded="false" aria-label="Buka menu">
            <i class="bi bi-list fs-3"></i>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav mx-lg-auto gap-lg-1">
                @foreach ($links as [$route, $icon, $label, $active])
                    <li class="nav-item">
                        <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ route($route) }}" @if ($active) aria-current="page" @endif>
                            <i class="bi {{ $icon }}"></i>{{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 bjn-actions">

                <a href="{{ route('cart.index') }}" class="nav-link bjn-cart d-none d-lg-flex {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i>Keranjang
                    @if ($cartCount > 0)<span class="bjn-badge">{{ $cartCount }}</span>@endif
                </a>

                @auth('customer')
                    <div class="dropdown">
                        <a class="bjn-user dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="bjn-av">{{ strtoupper(mb_substr($customerName ?? '?', 0, 1)) }}</span>
                            <span class="bjn-uname">{{ $customerName }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="bjn-ddhead">
                                <small>Masuk sebagai</small>
                                <b>{{ $customerName }}</b>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                                    <i class="bi bi-receipt"></i>Pesanan saya
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('customer.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-outline-success fw-semibold" href="{{ route('customer.login') }}">Masuk</a>
                    <a class="btn btn-success fw-semibold" href="{{ route('customer.register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>