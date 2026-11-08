@php
    // Badge jumlah isi keranjang (session('cart') berupa array).
    $cartCount = is_array(session('cart')) ? count(session('cart')) : 0;

    $customerUser  = auth('customer')->user();
    $customerName  = $customerUser?->customer?->name ?? $customerUser?->email;
    $customerEmail = $customerUser?->email;
    $initial       = strtoupper(mb_substr($customerName ?? '?', 0, 1));
    $verified      = $customerUser && method_exists($customerUser, 'hasVerifiedEmail') ? $customerUser->hasVerifiedEmail() : false;

    $links = [
        ['home',                     'bi-house-door',  'Home',      request()->routeIs('home')],
        ['customer.packages.index',  'bi-box-seam',    'Paket',     request()->routeIs('customer.packages.*')],
        ['customer.prasmanan.index', 'bi-egg-fried',   'Prasmanan', request()->routeIs('customer.prasmanan.*')],
        ['about',                    'bi-info-circle', 'Tentang',   request()->routeIs('about')],
        ['contact',                  'bi-telephone',   'Kontak',    request()->routeIs('contact')],
    ];
@endphp

<style>
    .bjn { --brand:#198754; --brand-dark:#126640; --ink:#1e2922; --mute:#64746b; --line:#e6ebe7; --soft:#f1f7f3;
           position:sticky; top:0; z-index:1030; background:rgba(255,255,255,.82);
           -webkit-backdrop-filter:saturate(170%) blur(14px); backdrop-filter:saturate(170%) blur(14px);
           border-bottom:1px solid transparent; transition:box-shadow .25s, border-color .25s, background .25s; }
    .bjn.is-scrolled { background:rgba(255,255,255,.94); border-bottom-color:var(--line); box-shadow:0 10px 30px rgba(20,40,28,.08); }
    .bjn-bar { display:flex; align-items:center; gap:1rem; min-height:68px; transition:min-height .25s; }
    .bjn.is-scrolled .bjn-bar { min-height:60px; }

    /* Brand */
    .bjn-brand { display:flex; align-items:center; gap:.65rem; text-decoration:none; color:var(--ink); font-weight:800; letter-spacing:.2px; flex:none; }
    .bjn-brand:hover { color:var(--ink); }
    .bjn-logo { width:40px; height:40px; border-radius:13px; display:grid; place-items:center; font-size:1.3rem;
                background:linear-gradient(145deg,#eaf7ef,#d6efe0); box-shadow:inset 0 0 0 1px rgba(25,135,84,.12); transition:transform .25s; }
    .bjn-brand:hover .bjn-logo { transform:rotate(-6deg) scale(1.05); }

    /* Menu desktop */
    .bjn-links { list-style:none; margin:0 auto; padding:.3rem; display:flex; gap:.15rem; background:var(--soft); border-radius:999px; }
    .bjn-links a { display:flex; align-items:center; gap:.4rem; padding:.5rem .95rem; border-radius:999px; color:var(--mute); font-weight:600; font-size:.9rem; text-decoration:none; transition:color .2s, background .2s, box-shadow .2s; }
    .bjn-links a:hover { color:var(--brand-dark); background:rgba(255,255,255,.7); }
    .bjn-links a.active { color:var(--brand-dark); background:#fff; box-shadow:0 2px 8px rgba(20,40,28,.1); }

    /* Sisi kanan */
    .bjn-right { display:flex; align-items:center; gap:.55rem; margin-left:auto; }
    @media (min-width:992px) { .bjn-right { margin-left:0; } }

    .bjn-iconbtn { position:relative; width:42px; height:42px; border-radius:13px; border:1px solid var(--line); background:#fff; color:var(--ink);
                   display:grid; place-items:center; font-size:1.15rem; text-decoration:none; transition:transform .2s, border-color .2s, background .2s, color .2s; }
    .bjn-iconbtn:hover { color:var(--brand-dark); border-color:#cfe3d6; background:var(--soft); transform:translateY(-1px); }
    .bjn-iconbtn.active { color:var(--brand-dark); background:#eaf5ed; border-color:#cde2d3; }
    .bjn-badge { position:absolute; top:-6px; right:-6px; background:var(--brand); color:#fff; border-radius:999px; font-size:.68rem; font-weight:700;
                 min-width:20px; height:20px; padding:0 .35rem; display:inline-flex; align-items:center; justify-content:center; border:2px solid #fff; }

    .bjn-btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:13px; padding:.55rem 1.05rem; font-weight:700; font-size:.9rem; text-decoration:none; transition:transform .2s, box-shadow .2s, background .2s, color .2s; }
    .bjn-btn:hover { transform:translateY(-1px); }
    .bjn-btn.ghost { color:var(--brand-dark); background:#fff; border:1px solid #cfe3d6; }
    .bjn-btn.ghost:hover { background:var(--soft); color:var(--brand-dark); }
    .bjn-btn.solid { color:#fff; background:linear-gradient(135deg,#22a46d,#198754 60%,#126640); box-shadow:0 8px 18px rgba(25,135,84,.28); }
    .bjn-btn.solid:hover { color:#fff; box-shadow:0 12px 24px rgba(25,135,84,.34); }

    .bjn-burger { width:42px; height:42px; border-radius:13px; border:1px solid var(--line); background:#fff; color:var(--ink); font-size:1.5rem; display:grid; place-items:center; padding:0; }
    .bjn-burger:hover { background:var(--soft); }
    .bjn-burger:focus-visible, .bjn-iconbtn:focus-visible, .bjn-user:focus-visible, .bjn-links a:focus-visible, .bjn-btn:focus-visible { outline:3px solid #7fd1a0; outline-offset:2px; }

    /* Akun (desktop) */
    .bjn-user { display:flex; align-items:center; gap:.55rem; border:1px solid var(--line); background:#fff; border-radius:999px; padding:.22rem .8rem .22rem .25rem; color:var(--ink); font-weight:600; font-size:.88rem; cursor:pointer; transition:border-color .2s, box-shadow .2s; }
    .bjn-user:hover, .bjn-user[aria-expanded="true"] { border-color:#cde2d3; box-shadow:0 6px 16px rgba(20,40,28,.08); }
    .bjn-user::after { display:none !important; }
    .bjn-user .chev { font-size:.7rem; color:var(--mute); transition:transform .2s; }
    .bjn-user[aria-expanded="true"] .chev { transform:rotate(180deg); }
    .bjn-av { width:34px; height:34px; border-radius:50%; background:linear-gradient(145deg,#22a46d,#126640); color:#fff; font-weight:700; font-size:.9rem; display:grid; place-items:center; flex:none; }
    .bjn-uname { max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    .bjn-menu { --bs-dropdown-min-width:280px; border:1px solid var(--line); border-radius:18px; padding:.45rem;
                box-shadow:0 22px 50px rgba(20,40,28,.16); min-width:280px; }
    .bjn-menu.show { animation:bjnPop .18s ease-out; }
    @keyframes bjnPop { from { opacity:0; transform:translateY(-6px) scale(.98); } to { opacity:1; transform:none; } }
    .bjn-me { display:flex; align-items:center; gap:.75rem; padding:.8rem .8rem; margin-bottom:.35rem; border-radius:14px; background:linear-gradient(135deg,#f1f9f4,#e5f4ea); }
    .bjn-me .bjn-av { width:46px; height:46px; font-size:1.15rem; }
    .bjn-me b { display:block; color:var(--ink); font-size:.95rem; line-height:1.25; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bjn-me small { display:block; color:var(--mute); font-size:.78rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bjn-me .tag { display:inline-flex; align-items:center; gap:.25rem; margin-top:.3rem; font-size:.68rem; font-weight:700; padding:.12rem .5rem; border-radius:999px; background:#fff; }
    .bjn-me .tag.ok { color:var(--brand-dark); } .bjn-me .tag.warn { color:#8a5a00; }
    .bjn-menu .dropdown-item { display:flex; align-items:center; gap:.7rem; padding:.6rem .7rem; border-radius:11px; font-weight:600; font-size:.9rem; color:var(--ink); }
    .bjn-menu .dropdown-item i.ic { width:32px; height:32px; border-radius:10px; display:grid; place-items:center; background:var(--soft); color:var(--brand-dark); font-size:1rem; flex:none; transition:background .2s, color .2s; }
    .bjn-menu .dropdown-item:hover { background:#f6faf7; }
    .bjn-menu .dropdown-item:hover i.ic { background:var(--brand); color:#fff; }
    .bjn-menu .dropdown-item.active { background:#eaf5ed; color:var(--brand-dark); }
    .bjn-menu .dropdown-item.active i.ic { background:var(--brand); color:#fff; }
    .bjn-menu .dropdown-item:active { background:#dff0e5; color:var(--ink); }
    .bjn-menu .dropdown-item.danger { color:#b02a37; }
    .bjn-menu .dropdown-item.danger i.ic { background:#fde8e8; color:#b02a37; }
    .bjn-menu .dropdown-item.danger:hover { background:#fff3f3; }
    .bjn-menu .dropdown-item.danger:hover i.ic { background:#b02a37; color:#fff; }
    .bjn-menu hr { margin:.4rem .3rem; border-color:var(--line); opacity:1; }

    /* Drawer HP / tablet */
    .bjn-drawer { --bs-offcanvas-width:min(88vw, 340px); border-left:0; border-radius:24px 0 0 24px; box-shadow:-20px 0 50px rgba(20,40,28,.18); }
    .bjn-drawer .offcanvas-header { padding:1rem 1.1rem .6rem; }
    .bjn-drawer .offcanvas-body { padding:.4rem 1.1rem 1.2rem; display:flex; flex-direction:column; gap:1rem; }
    .bjn-dclose { width:40px; height:40px; border-radius:12px; border:1px solid var(--line); background:#fff; color:var(--ink); font-size:1.2rem; display:grid; place-items:center; padding:0; }
    .bjn-dclose:hover { background:var(--soft); }
    .bjn-dlist { list-style:none; margin:0; padding:0; display:grid; gap:.3rem; }
    .bjn-dlist a, .bjn-dlist button { width:100%; display:flex; align-items:center; gap:.8rem; padding:.75rem .8rem; border-radius:14px; color:var(--ink); font-weight:600; font-size:.97rem; text-decoration:none; background:transparent; border:0; text-align:left; transition:background .2s; }
    .bjn-dlist a:hover, .bjn-dlist button:hover { background:#f4f9f6; }
    .bjn-dlist .ic { width:38px; height:38px; border-radius:12px; display:grid; place-items:center; background:var(--soft); color:var(--brand-dark); font-size:1.1rem; flex:none; }
    .bjn-dlist a.active { background:#eaf5ed; color:var(--brand-dark); }
    .bjn-dlist a.active .ic { background:var(--brand); color:#fff; }
    .bjn-dlist .end { margin-left:auto; }
    .bjn-dlist .danger { color:#b02a37; } .bjn-dlist .danger .ic { background:#fde8e8; color:#b02a37; }
    .bjn-dtitle { font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--mute); padding:0 .3rem; margin-bottom:.15rem; }
    .bjn-dme { display:flex; align-items:center; gap:.8rem; padding:.9rem; border-radius:18px; background:linear-gradient(135deg,#f1f9f4,#e5f4ea); }
    .bjn-dme .bjn-av { width:48px; height:48px; font-size:1.2rem; }
    .bjn-dme b { display:block; font-size:.97rem; line-height:1.25; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bjn-dme small { color:var(--mute); display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bjn-dauth { display:grid; gap:.6rem; margin-top:auto; }
    .bjn-dauth .bjn-btn { padding:.8rem 1rem; }

    @media (max-width:991.98px) { .bjn-bar { min-height:62px; } }
    @media (max-width:380px) { .bjn-brand span.t { font-size:.9rem; } }
    @media (prefers-reduced-motion: reduce) {
        .bjn, .bjn-bar, .bjn-logo, .bjn-iconbtn, .bjn-btn, .bjn-menu.show { transition:none; animation:none; }
    }
</style>

<header class="bjn" id="bjn">
    <div class="container">
        <nav class="bjn-bar" aria-label="Navigasi utama">

            <a class="bjn-brand" href="{{ route('home') }}">
                <span class="bjn-logo">🍱</span>
                <span class="t">KEDAI BANGJO</span>
            </a>

            {{-- Menu desktop --}}
            <ul class="bjn-links d-none d-lg-flex">
                @foreach ($links as [$route, $icon, $label, $active])
                    <li>
                        <a class="{{ $active ? 'active' : '' }}" href="{{ route($route) }}" @if ($active) aria-current="page" @endif>
                            <i class="bi {{ $icon }}"></i>{{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="bjn-right">

                <a href="{{ route('cart.index') }}" class="bjn-iconbtn {{ request()->routeIs('cart.*') ? 'active' : '' }}" aria-label="Keranjang{{ $cartCount > 0 ? ', ' . $cartCount . ' item' : '' }}">
                    <i class="bi bi-cart3"></i>
                    @if ($cartCount > 0)<span class="bjn-badge">{{ $cartCount }}</span>@endif
                </a>

                @auth('customer')
                    {{-- Dropdown akun (desktop) --}}
                    <div class="dropdown d-none d-lg-block">
                        <button class="bjn-user dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-offset="0,10" aria-expanded="false" aria-label="Menu akun">
                            <span class="bjn-av">{{ $initial }}</span>
                            <span class="bjn-uname">{{ $customerName }}</span>
                            <i class="bi bi-chevron-down chev"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end bjn-menu">
                            <li>
                                <div class="bjn-me">
                                    <span class="bjn-av">{{ $initial }}</span>
                                    <div class="min-w-0" style="min-width:0">
                                        <b>{{ $customerName }}</b>
                                        <small>{{ $customerEmail }}</small>
                                        @if ($verified)
                                            <span class="tag ok"><i class="bi bi-patch-check-fill"></i>Terverifikasi</span>
                                        @else
                                            <span class="tag warn"><i class="bi bi-exclamation-circle"></i>Belum verifikasi</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                                    <i class="bi bi-receipt ic"></i>Pesanan saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('customer.profile*') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                                    <i class="bi bi-person-circle ic"></i>Akun saya
                                </a>
                            </li>
                            <li><hr></li>
                            <li>
                                <form action="{{ route('customer.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <i class="bi bi-box-arrow-right ic"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="bjn-btn ghost d-none d-lg-inline-flex" href="{{ route('customer.login') }}">Masuk</a>
                    <a class="bjn-btn solid d-none d-lg-inline-flex" href="{{ route('customer.register') }}">Daftar</a>
                @endauth

                <button class="bjn-burger d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#bjnMenu" aria-controls="bjnMenu" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </nav>
    </div>
</header>

{{-- Drawer menu HP & tablet (di luar <header> supaya posisinya tidak terpengaruh efek blur) --}}
<div class="offcanvas offcanvas-end bjn-drawer" tabindex="-1" id="bjnMenu" aria-labelledby="bjnMenuLabel">
    <div class="offcanvas-header">
        <a class="bjn-brand" href="{{ route('home') }}" id="bjnMenuLabel">
            <span class="bjn-logo">🍱</span>
            <span class="t">KEDAI BANGJO</span>
        </a>
        <button type="button" class="bjn-dclose" data-bs-dismiss="offcanvas" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="offcanvas-body">

        @auth('customer')
            <div class="bjn-dme">
                <span class="bjn-av">{{ $initial }}</span>
                <div style="min-width:0">
                    <b>{{ $customerName }}</b>
                    <small>{{ $customerEmail }}</small>
                </div>
            </div>
        @endauth

        <div>
            <div class="bjn-dtitle">Menu</div>
            <ul class="bjn-dlist">
                @foreach ($links as [$route, $icon, $label, $active])
                    <li>
                        <a class="{{ $active ? 'active' : '' }}" href="{{ route($route) }}" @if ($active) aria-current="page" @endif>
                            <span class="ic"><i class="bi {{ $icon }}"></i></span>{{ $label }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a class="{{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        <span class="ic"><i class="bi bi-cart3"></i></span>Keranjang
                        @if ($cartCount > 0)<span class="bjn-badge end" style="position:static;border:0">{{ $cartCount }}</span>@endif
                    </a>
                </li>
            </ul>
        </div>

        @auth('customer')
            <div>
                <div class="bjn-dtitle">Akun</div>
                <ul class="bjn-dlist">
                    <li>
                        <a class="{{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                            <span class="ic"><i class="bi bi-receipt"></i></span>Pesanan saya
                        </a>
                    </li>
                    <li>
                        <a class="{{ request()->routeIs('customer.profile*') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                            <span class="ic"><i class="bi bi-person-circle"></i></span>Akun saya
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="danger"><span class="ic"><i class="bi bi-box-arrow-right"></i></span>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <div class="bjn-dauth">
                <a class="bjn-btn solid" href="{{ route('customer.register') }}">Daftar sekarang</a>
                <a class="bjn-btn ghost" href="{{ route('customer.login') }}">Masuk</a>
            </div>
        @endauth
    </div>
</div>

<script>
    // Bayangan & garis bawah navbar muncul saat halaman di-scroll
    (function () {
        var el = document.getElementById('bjn');
        if (!el) { return; }
        var sync = function () { el.classList.toggle('is-scrolled', window.scrollY > 8); };
        sync();
        window.addEventListener('scroll', sync, { passive: true });
    })();
</script>