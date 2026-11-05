<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title') - Admin Kedai BangJo</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- Pulihkan mode sidebar (ikon/penuh) sebelum halaman digambar, supaya tidak berkedip --}}
    <script>
        try { if (window.innerWidth >= 992 && localStorage.getItem('admSbCollapsed') === '1') document.documentElement.classList.add('adm-collapsed'); } catch (e) {}
    </script>

    <style>
        body { margin:0; background:#f4f7f5; }

        .adm-shell { --sb-w:264px; --sb-w-mini:76px; --tb-h:68px; min-height:100vh; color:#1e2922; }
        .adm-main { margin-left:var(--sb-w); min-height:100vh; display:flex; flex-direction:column; min-width:0; transition:margin-left .2s ease; }
        .adm-content { flex:1; padding:1.5rem; min-width:0; }

        .adm-overlay { position:fixed; inset:0; background:rgba(15,25,19,.45); backdrop-filter:blur(2px); opacity:0; visibility:hidden; transition:opacity .25s ease, visibility .25s ease; z-index:1040; }

        @media (min-width:992px) {
            .adm-collapsed .adm-main { margin-left:var(--sb-w-mini); }
        }
        @media (max-width:991.98px) {
            .adm-main { margin-left:0; }
            .adm-content { padding:1rem; }
            .sb-open .adm-overlay { opacity:1; visibility:visible; }
            body.adm-lock { overflow:hidden; }
        }

        /* ===== Toast (bertumpuk, tidak saling menimpa) ===== */
        .adm-toasts { position:fixed; top:calc(var(--tb-h, 68px) + .75rem); right:1rem; z-index:1100; display:flex; flex-direction:column; gap:.6rem; width:min(380px, calc(100vw - 2rem)); pointer-events:none; }
        .adm-toast { --c:#198754; --bg:#e6f4ea; position:relative; overflow:hidden; pointer-events:auto; display:flex; align-items:flex-start; gap:.75rem; background:#fff; border:1px solid #e3ebe5; border-radius:16px; padding:.85rem .9rem 1rem .95rem; box-shadow:0 14px 34px rgba(15,25,19,.14); animation:admToastIn .28s cubic-bezier(.2,.8,.2,1); }
        .adm-toast::before { content:""; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--c); }
        .adm-toast.is-link { cursor:pointer; }
        .adm-toast.is-link:hover { box-shadow:0 16px 38px rgba(15,25,19,.2); }
        .adm-toast .t-ic { flex:none; width:36px; height:36px; border-radius:11px; background:var(--bg); color:var(--c); display:grid; place-items:center; font-size:1.15rem; }
        .adm-toast .t-body { min-width:0; flex:1; display:block; }
        .adm-toast .t-title { display:block; font-weight:800; font-size:.92rem; color:#1f2a24; line-height:1.25; }
        .adm-toast .t-msg { display:block; font-size:.84rem; color:#5c6a62; margin-top:.15rem; line-height:1.35; overflow-wrap:anywhere; }
        .adm-toast .t-x { flex:none; border:0; background:transparent; color:#8a978f; font-size:1.2rem; line-height:1; padding:.1rem .35rem; border-radius:8px; }
        .adm-toast .t-x:hover { background:#f1f4f2; color:#1f2a24; }
        .adm-toast .t-bar { position:absolute; left:0; right:0; bottom:0; height:3px; background:var(--c); opacity:.4; transform-origin:left; animation:admToastBar var(--d, 4500ms) linear forwards; }
        .adm-toast:hover .t-bar { animation-play-state:paused; }
        .adm-toast.is-error   { --c:#dc3545; --bg:#fde8e8; }
        .adm-toast.is-warning { --c:#f0ad00; --bg:#fff1d6; }
        .adm-toast.is-info    { --c:#0b6bcb; --bg:#e2f0fb; }
        .adm-toast.is-hiding { opacity:0; transform:translateX(14px); transition:opacity .25s, transform .25s; }
        @keyframes admToastIn  { from { opacity:0; transform:translateX(16px) scale(.98); } to { opacity:1; transform:none; } }
        @keyframes admToastBar { from { transform:scaleX(1); } to { transform:scaleX(0); } }
        @media (max-width:575.98px) { .adm-toasts { left:.5rem; right:.5rem; width:auto; } }

        @media (prefers-reduced-motion:reduce) {
            .adm-main, .adm-overlay { transition:none; }
            .adm-toast, .adm-toast .t-bar { animation:none; }
        }
    </style>

    @include('admin.partials.ui')

    @stack('styles')
</head>

<body>

    <div class="adm-shell" id="admShell">

        @include('admin.partials.sidebar')

        <div class="adm-overlay" data-adm-overlay></div>

        <div class="adm-main">

            @include('admin.partials.topbar')

            <main class="adm-content">
                @yield('content')
            </main>

        </div>
    </div>

    <div class="adm-toasts" id="admToasts" aria-live="polite"></div>

    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script> --}}
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/confirm-dialog.js') }}"></script>

    <script>
        (function () {
            var root = document.documentElement;
            var mq   = window.matchMedia('(max-width: 991.98px)');

            function setExpanded(open) {
                document.querySelectorAll('[data-adm-toggle]').forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
            }
            function closeDrawer() {
                root.classList.remove('sb-open');
                document.body.classList.remove('adm-lock');
                setExpanded(false);
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-adm-toggle]')) {
                    if (mq.matches) {
                        var open = root.classList.toggle('sb-open');
                        document.body.classList.toggle('adm-lock', open);
                        setExpanded(open);
                    } else {
                        var c = root.classList.toggle('adm-collapsed');
                        try { localStorage.setItem('admSbCollapsed', c ? '1' : '0'); } catch (err) {}
                    }
                    return;
                }
                if (e.target.closest('[data-adm-overlay]')) closeDrawer();
                if (mq.matches && e.target.closest('.sb-link')) closeDrawer();
            });

            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });
            mq.addEventListener('change', closeDrawer);

        })();
    </script>

    @php
    $flashes = [];
    if (session('success')) { $flashes[] = ['type' => 'success', 'message' => session('success')]; }
    if (session('error'))   { $flashes[] = ['type' => 'error',   'message' => session('error')]; }
@endphp
<script>
(function () {
    // ===== Toast: bisa dipanggil dari mana saja lewat window.admToast({...}) =====
    var ICONS  = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
    var TITLES = { success: 'Berhasil', error: 'Gagal', warning: 'Perhatian', info: 'Info' };

    window.admToast = function (opt) {
        var host = document.getElementById('admToasts');
        if (!host) { return null; }

        var type     = opt.type || 'success';
        var duration = opt.duration || (type === 'error' ? 7000 : 4500);

        var t = document.createElement('div');
        t.className = 'adm-toast is-' + type + (opt.url ? ' is-link' : '');
        t.setAttribute('role', type === 'error' ? 'alert' : 'status');
        t.style.setProperty('--d', duration + 'ms');

        var ic = document.createElement('span');
        ic.className = 't-ic';
        var i = document.createElement('i');
        i.className = 'bi ' + (opt.icon || ICONS[type] || ICONS.info);
        ic.appendChild(i);

        var body = document.createElement('span');
        body.className = 't-body';
        var ti = document.createElement('span');
        ti.className = 't-title';
        ti.textContent = opt.title || TITLES[type] || '';
        body.appendChild(ti);
        if (opt.message) {
            var m = document.createElement('span');
            m.className = 't-msg';
            m.textContent = opt.message;
            body.appendChild(m);
        }

        var x = document.createElement('button');
        x.type = 'button';
        x.className = 't-x';
        x.setAttribute('aria-label', 'Tutup');
        x.textContent = '\u00d7';

        var bar = document.createElement('span');
        bar.className = 't-bar';

        t.append(ic, body, x, bar);
        host.appendChild(t);

        var timer;
        function close() {
            clearTimeout(timer);
            t.classList.add('is-hiding');
            setTimeout(function () { t.remove(); }, 280);
        }
        function arm(ms) { clearTimeout(timer); timer = setTimeout(close, ms); }

        x.addEventListener('click', function (e) { e.stopPropagation(); close(); });
        if (opt.url) {
            t.addEventListener('click', function () { window.location.href = opt.url; });
        }
        t.addEventListener('mouseenter', function () { clearTimeout(timer); });
        t.addEventListener('mouseleave', function () { arm(1500); });
        arm(duration);

        while (host.children.length > 4) { host.firstElementChild.remove(); }
        return t;
    };

    // ===== Pesan flash dari controller (->with('success' / 'error', ...)) =====
    var flashes = @json($flashes);
    var norm = function (s) { return (s || '').replace(/\s+/g, ' ').trim(); };

    flashes.forEach(function (f, idx) {
        // buang alert inline di halaman yang isinya sama, supaya tidak muncul dobel
        document.querySelectorAll('.adm-content .alert').forEach(function (a) {
            if (norm(a.textContent).indexOf(norm(f.message)) !== -1) { a.remove(); }
        });
        setTimeout(function () { window.admToast({ type: f.type, message: f.message }); }, idx * 180);
    });
})();
</script>

    @stack('scripts')

</body>
</html>