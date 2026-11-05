{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    $href    = fn ($name) => Route::has($name) ? route($name) : '#';
    $pending = $admAlerts['payments'] ?? 0;
    $shipping = $admAlerts['shipping'] ?? 0;

    // [judul bagian => [[label, ikon, route, pola route aktif, badge], ...]]
    $groups = [
        'MAIN' => [
            ['Dashboard', 'bi-grid-1x2-fill', 'dashboard', 'dashboard', 0],
        ],
        'KATALOG' => [
            ['Kategori', 'bi-tags',     'categories.index', 'categories.*', 0],
            ['Menu',     'bi-grid',     'menu.index',       'menu.*',       0],
            ['Paket',    'bi-box-seam', 'packages.index',   'packages.*',   0],
        ],
        'TRANSAKSI' => [
            ['Pesanan',    'bi-cart3',       'orders.index',         'orders.*',         0],
            ['Customer',   'bi-people',      'customers.index',      'customers.*',      0],
            ['Pembayaran', 'bi-credit-card', 'admin.payments.index', 'admin.payments.*', $pending],
        ],
        'LAINNYA' => [
            ['Laporan', 'bi-file-earmark-bar-graph', 'reports.index', 'reports.*', 0],
        ],
    ];

    if (Auth::user()->level === 'Superadmin') {
        $groups['SISTEM'] = [
            ['User', 'bi-person-gear', 'users.index', 'users.*', 0],
        ];
    }
@endphp

<style>
    .adm-shell { --green:#198754; --green-dark:#126640; --green-light:#eaf5ed; --ink:#1e2922; --mute:#64746b; --line:#e6ebe7; }

    .sb { position:fixed; inset:0 auto 0 0; width:var(--sb-w); background:#fff; border-right:1px solid var(--line); display:flex; flex-direction:column; z-index:1050; transition:width .2s ease, transform .25s ease; }
    .sb a { text-decoration:none; }

    .sb-brand { height:var(--tb-h); display:flex; align-items:center; gap:.5rem; padding:0 1.1rem; border-bottom:1px solid var(--line); flex:none; }
    .sb-brand > a { display:flex; align-items:center; gap:.7rem; color:var(--ink); min-width:0; }
    .sb-logo { width:40px; height:40px; border-radius:12px; background:var(--green-light); display:grid; place-items:center; font-size:1.25rem; flex:none; }
    .sb-name { font-weight:800; letter-spacing:.3px; line-height:1.15; white-space:nowrap; }
    .sb-sub { display:block; color:var(--mute); font-size:.74rem; font-weight:500; }
    .sb-close { display:none; margin-left:auto; border:0; background:#f3f7f4; color:var(--mute); width:34px; height:34px; border-radius:10px; flex:none; }
    .sb-close:hover { background:var(--green-light); color:var(--green-dark); }

    .sb-menu { flex:1; overflow-y:auto; overflow-x:hidden; padding:1rem .75rem; scrollbar-width:thin; scrollbar-color:#d5ddd8 transparent; }
    .sb-title { font-size:.68rem; font-weight:700; letter-spacing:1.1px; color:#8a978f; padding:0 .8rem; margin:1.2rem 0 .4rem; white-space:nowrap; }
    .sb-section:first-child .sb-title { margin-top:0; }

    .sb-link { position:relative; display:flex; align-items:center; gap:.8rem; padding:.62rem .8rem; border-radius:12px; color:var(--mute); font-weight:600; font-size:.92rem; margin-bottom:2px; white-space:nowrap; transition:background .15s, color .15s; }
    .sb-link i { font-size:1.15rem; width:22px; text-align:center; flex:none; }
    .sb-link:hover { background:#f3f7f4; color:var(--ink); }
    .sb-link.active { background:var(--green-light); color:var(--green-dark); }
    .sb-link.active::before { content:""; position:absolute; left:-.75rem; top:22%; bottom:22%; width:4px; border-radius:0 4px 4px 0; background:var(--green); }
    .sb-text { flex:1; overflow:hidden; text-overflow:ellipsis; }
    .sb-badge { background:#ffc107; color:#4a3500; border-radius:999px; font-size:.7rem; font-weight:800; min-width:21px; height:21px; padding:0 .4rem; display:inline-flex; align-items:center; justify-content:center; }

    .sb-bottom { flex:none; padding:.8rem; border-top:1px solid var(--line); }
    .sb-site { display:flex; align-items:center; gap:.7rem; padding:.58rem .8rem; border-radius:12px; border:1px solid var(--line); color:var(--ink); font-weight:600; font-size:.88rem; white-space:nowrap; transition:border-color .15s, background .15s; }
    .sb-site:hover { border-color:#b9d8c7; background:#f3f8f4; color:var(--green-dark); }
    .sb-site i { color:var(--green); }

    .sb a:focus-visible, .sb button:focus-visible { outline:3px solid #7fd1a0; outline-offset:2px; }

    /* ===== Desktop: mode ikon ===== */
    @media (min-width:992px) {
        .adm-collapsed .sb { width:var(--sb-w-mini); }
        .adm-collapsed .sb-brand { padding:0; justify-content:center; }
        .adm-collapsed .sb-brand-text,
        .adm-collapsed .sb-text,
        .adm-collapsed .sb-site span { display:none; }
        .adm-collapsed .sb-title { font-size:0; padding:0; margin:.9rem .6rem .6rem; height:1px; background:var(--line); }
        .adm-collapsed .sb-section:first-child .sb-title { display:none; }
        .adm-collapsed .sb-link { justify-content:center; padding:.72rem 0; }
        .adm-collapsed .sb-link.active::before { left:-.75rem; }
        .adm-collapsed .sb-badge { position:absolute; top:3px; right:9px; min-width:17px; height:17px; font-size:.62rem; padding:0 .25rem; }
        .adm-collapsed .sb-site { justify-content:center; padding:.65rem 0; }
    }

    /* ===== HP / tablet: laci dari kiri ===== */
    @media (max-width:991.98px) {
        .sb { width:min(300px, 86vw); transform:translateX(-100%); }
        .sb-open .sb { transform:translateX(0); box-shadow:12px 0 36px rgba(15,25,19,.22); }
        .sb-close { display:grid; place-items:center; }
        .sb-link { padding:.78rem .85rem; }
    }

    @media (prefers-reduced-motion:reduce) {
        .sb, .sb-link { transition:none; }
    }
</style>

<aside class="sb" id="admSidebar" aria-label="Menu admin">

    <div class="sb-brand">
        <a href="{{ $href('dashboard') }}">
            <span class="sb-logo">🍱</span>
            <span class="sb-brand-text">
                <span class="sb-name">KEDAI BANGJO</span>
                <span class="sb-sub">Admin Panel</span>
            </span>
        </a>
        <button type="button" class="sb-close" data-adm-toggle aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
    </div>

    <nav class="sb-menu">
        @foreach ($groups as $title => $links)
            <div class="sb-section">
                <div class="sb-title">{{ $title }}</div>

                @foreach ($links as [$label, $icon, $route, $pattern, $badge])
                    @php $active = request()->routeIs($pattern); @endphp
                    <a href="{{ $href($route) }}" class="sb-link {{ $active ? 'active' : '' }}" title="{{ $label }}" @if ($active) aria-current="page" @endif>
                        <i class="bi {{ $icon }}"></i>
                        <span class="sb-text">{{ $label }}</span>
                        @if ($badge > 0)<span class="sb-badge">{{ $badge > 99 ? '99+' : $badge }}</span>@endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="sb-bottom">
        <a href="{{ $href('home') }}" target="_blank" rel="noopener" class="sb-site" title="Lihat situs">
            <i class="bi bi-box-arrow-up-right"></i><span>Lihat situs</span>
        </a>
    </div>
</aside>