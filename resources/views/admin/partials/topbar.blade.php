@php
    $tbPending  = $admAlerts['payments'] ?? 0;
    $tbUnread   = $admAlerts['unread'] ?? 0;
    $tbItems    = $admAlerts['items'] ?? [];
    $tbPayments = \Illuminate\Support\Facades\Route::has('admin.payments.index') ? route('admin.payments.index') : '#';
    $tbUser     = Auth::user();
@endphp

<style>
    .tb { position:sticky; top:0; z-index:1030; height:var(--tb-h); display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0 1.5rem; background:rgba(255,255,255,.88); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border-bottom:1px solid var(--line); }
    .tb-left { display:flex; align-items:center; gap:.9rem; min-width:0; }
    .tb-right { display:flex; align-items:center; gap:.5rem; flex:none; }

    .tb-btn { position:relative; width:42px; height:42px; border:1px solid var(--line); background:#fff; color:var(--mute); border-radius:12px; display:grid; place-items:center; font-size:1.15rem; transition:border-color .15s, color .15s, background .15s; }
    .tb-btn:hover { border-color:#b9d8c7; color:var(--green-dark); background:#f3f8f4; }
    .tb-badge { position:absolute; top:-5px; right:-5px; background:#dc3545; color:#fff; border:2px solid #fff; border-radius:999px; min-width:20px; height:20px; padding:0 .3rem; font-size:.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; }

    .tb-title { margin:0; font-size:1.15rem; font-weight:800; color:var(--ink); line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tb-crumb { display:flex; align-items:center; gap:.35rem; color:var(--mute); font-size:.8rem; white-space:nowrap; }
    .tb-crumb i { font-size:.65rem; }
    .tb-titles { min-width:0; }

    .tb-sep { width:1px; height:28px; background:var(--line); margin:0 .35rem; }

    .tb-user { display:flex; align-items:center; gap:.65rem; border:1px solid transparent; background:transparent; border-radius:14px; padding:.3rem .55rem .3rem .3rem; transition:background .15s, border-color .15s; }
    .tb-user:hover, .tb-user[aria-expanded="true"] { background:#f3f8f4; border-color:var(--line); }
    .tb-avatar { width:38px; height:38px; border-radius:12px; object-fit:cover; flex:none; }
    .tb-avatar.ph { background:var(--green); color:#fff; font-weight:700; font-size:.85rem; display:grid; place-items:center; }
    .tb-info { text-align:left; line-height:1.25; }
    .tb-info strong { display:block; font-size:.9rem; color:var(--ink); max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tb-info small { color:var(--mute); font-size:.76rem; }
    .tb-chev { color:var(--mute); font-size:.75rem; }

    .tb .dropdown-menu { border:1px solid var(--line); border-radius:16px; box-shadow:0 18px 40px rgba(15,25,19,.12); padding:.45rem; min-width:240px; margin-top:.6rem !important; }
    .tb .dropdown-item { border-radius:10px; padding:.55rem .75rem; font-size:.92rem; display:flex; align-items:center; gap:.65rem; }
    .tb .dropdown-item i { color:var(--mute); width:18px; text-align:center; }
    .tb .dropdown-item:hover { background:#f3f8f4; }
    .tb .dropdown-item.text-danger i { color:inherit; }
    .tb .dropdown-item:active { background:var(--green); color:#fff; }
    .tb .dropdown-item:active i { color:#fff; }
    .tb .dropdown-divider { margin:.35rem 0; border-color:var(--line); }
    .tb-dd-head { padding:.55rem .75rem .65rem; }
    .tb-dd-head strong { display:block; color:var(--ink); }
    .tb-dd-head small { color:var(--mute); word-break:break-all; }
    .tb-notif { min-width:290px; }
    .tb-notif-empty { padding:1.1rem .75rem; text-align:center; color:var(--mute); font-size:.9rem; }
    .tb-notif-empty i { display:block; font-size:1.6rem; color:#b9c7be; margin-bottom:.3rem; }
    .tb-notif-item { align-items:flex-start !important; }
    .tb-notif-item .ic { width:34px; height:34px; border-radius:10px; background:#fff4d6; color:#8a6100 !important; display:grid; place-items:center; flex:none; }
    .tb-notif-item b { display:block; font-size:.9rem; color:var(--ink); }
    .tb-notif-item span.d { font-size:.8rem; color:var(--mute); }

    /* ===== notifikasi (tampilan baru) ===== */
    .tb .tb-notif { width:380px; max-width:calc(100vw - 1.5rem); padding:0; overflow:hidden; }
    .tb-nh { display:flex; align-items:center; gap:.5rem; padding:.9rem 1rem .75rem; }
    .tb-nh h6 { margin:0; font-size:1rem; font-weight:800; color:var(--ink); }
    .tb-nh .cnt { background:#fdecee; color:#c62828; border-radius:999px; font-size:.7rem; font-weight:800; padding:.1rem .55rem; }
    .tb-nh .act { margin-left:auto; display:flex; align-items:center; gap:.35rem; }
    .tb-ic-btn { width:32px; height:32px; padding:0; border:1px solid var(--line); background:#fff; color:var(--mute); border-radius:10px; display:grid; place-items:center; font-size:.95rem; transition:background .15s, color .15s, border-color .15s; }
    .tb-ic-btn:hover { background:#f3f8f4; color:var(--green-dark); border-color:#b9d8c7; }
    .tb-ic-btn.off { color:#b0b8b3; background:#f6f7f6; }

    .tb-nlist { max-height:360px; overflow-y:auto; padding:.2rem .5rem .5rem; }
    .tb .dropdown-item.tb-notif-item { position:relative; white-space:normal; padding:.7rem .75rem; margin-bottom:.2rem; border:1px solid transparent; }
    .tb-notif-item.unread { background:#f1f8f3; border-color:#dcefe2; }
    .tb-notif-item.unread::after { content:''; position:absolute; top:.85rem; right:.75rem; width:9px; height:9px; border-radius:50%; background:#dc3545; box-shadow:0 0 0 3px #f1f8f3; }
    .tb-notif-item .ic { width:40px; height:40px; border-radius:12px; font-size:1.1rem; }
    .tb-notif-item .ic.order { background:#e6f4ea; color:var(--green-dark) !important; }
    .tb-notif-item .ic.pay { background:#e7f0fd; color:#1a5fd0 !important; }
    .tb .tb-notif-item .ic i { color:inherit; width:auto; }
    .tb-notif-item b { font-size:.92rem; padding-right:1.2rem; }
    .tb-notif-item .d { display:block; font-size:.82rem; color:var(--mute); margin-top:.1rem; line-height:1.35; }
    .tb-notif-item .t { display:flex; align-items:center; gap:.3rem; font-size:.74rem; color:#8a978f; margin-top:.3rem; }
    .tb .tb-notif-item .t i { width:auto; font-size:.72rem; color:inherit; }

    .tb-nf { display:grid; grid-template-columns:1fr 1fr; gap:.4rem; padding:.6rem .75rem .75rem; border-top:1px solid var(--line); background:#fafcfb; }
    .tb-nf a { display:flex; align-items:center; justify-content:center; gap:.4rem; padding:.5rem; border-radius:10px; font-size:.85rem; font-weight:600; color:var(--green-dark); text-decoration:none; background:#fff; border:1px solid var(--line); }
    .tb-nf a:hover { background:#f3f8f4; border-color:#b9d8c7; }

    .tb a:focus-visible, .tb button:focus-visible { outline:3px solid #7fd1a0; outline-offset:2px; }

    @media (max-width:767.98px) {
        .tb { padding:0 .9rem; }
        .tb-info, .tb-chev { display:none; }
        .tb-user { padding:.2rem; }
        .tb-sep { display:none; }
    }
    @media (max-width:575.98px) {
        .tb-crumb { display:none; }
        .tb-title { font-size:1.05rem; }
    }
</style>

<header class="tb">

    {{-- KIRI --}}
    <div class="tb-left">
        <button type="button" class="tb-btn" data-adm-toggle aria-label="Buka atau tutup menu" aria-controls="admSidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="tb-titles">
            <h1 class="tb-title">@yield('title')</h1>
            <div class="tb-crumb">
                Admin Panel <i class="bi bi-chevron-right"></i> @yield('title')
            </div>
        </div>
    </div>

    {{-- KANAN --}}
    <div class="tb-right">

        {{-- Notifikasi --}}
        <div class="dropdown">
            <button type="button" class="tb-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notifikasi">
                <i class="bi bi-bell"></i>
                <span class="tb-badge" id="admBellBadge" @if ($tbUnread < 1) hidden @endif>{{ $tbUnread > 99 ? '99+' : $tbUnread }}</span>
            </button>

            <div class="dropdown-menu dropdown-menu-end tb-notif">
                <div class="tb-nh">
                    <h6>Notifikasi</h6>
                    <span class="cnt" id="admNotifCount" @if ($tbUnread < 1) hidden @endif>{{ $tbUnread }} baru</span>
                    <div class="act">
                        <button type="button" class="tb-ic-btn" id="admSoundBtn" title="Suara notifikasi" aria-label="Suara notifikasi">
                            <i class="bi bi-volume-up"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="m-0">
                            @csrf
                            <button type="submit" class="tb-ic-btn" title="Tandai semua dibaca" aria-label="Tandai semua dibaca">
                                <i class="bi bi-check2-all"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="tb-nlist" id="admNotifList">
                    @forelse ($tbItems as $n)
                        @php $isPay = $n['kind'] === 'bukti_bayar'; @endphp
                        <a class="dropdown-item tb-notif-item {{ $n['unread'] ? 'unread' : '' }}" href="{{ $n['url'] }}">
                            <span class="ic {{ $isPay ? 'pay' : 'order' }}"><i class="bi {{ $isPay ? 'bi-credit-card-2-front' : 'bi-bag-plus' }}"></i></span>
                            <span class="flex-grow-1">
                                <b>{{ $n['title'] }}</b>
                                <span class="d">{{ $n['body'] }}</span>
                                <span class="t"><i class="bi bi-clock"></i>{{ $n['time'] }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="tb-notif-empty">
                            <i class="bi bi-bell-slash"></i>
                            Belum ada notifikasi
                        </div>
                    @endforelse
                </div>

                <div class="tb-nf">
                    <a href="{{ route('orders.index') }}"><i class="bi bi-cart3"></i>Pesanan</a>
                    <a href="{{ $tbPayments }}"><i class="bi bi-credit-card"></i>Pembayaran</a>
                </div>
            </div>
        </div>

        <div class="tb-sep"></div>

        {{-- User --}}
        <div class="dropdown">
            <button class="tb-user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                @if ($tbUser->avatar)
                    <img src="{{ asset('storage/' . $tbUser->avatar) }}" class="tb-avatar" alt="{{ $tbUser->name }}">
                @else
                    <span class="tb-avatar ph">{{ strtoupper(mb_substr($tbUser->name, 0, 2)) }}</span>
                @endif

                <span class="tb-info">
                    <strong>{{ $tbUser->name }}</strong>
                    <small>{{ $tbUser->level }}</small>
                </span>

                <i class="bi bi-chevron-down tb-chev"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li class="tb-dd-head">
                    <strong>{{ $tbUser->name }}</strong>
                    <small>{{ $tbUser->email }}</small>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person"></i>Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}#edit"><i class="bi bi-pencil-square"></i>Edit Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}#password"><i class="bi bi-key"></i>Ubah Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
(function () {
    var POLL_URL = @json(route('admin.notifications.poll'));
    var INTERVAL = 10000; // cek tiap 10 detik

    var badge    = document.getElementById('admBellBadge');
    var list     = document.getElementById('admNotifList');
    var soundBtn = document.getElementById('admSoundBtn');
    var cnt      = document.getElementById('admNotifCount');
    if (!badge || !list) { return; }

    var lastUnread = @json((int) $tbUnread);
    var baseTitle  = document.title.replace(/^\(\d+\)\s*/, '');

    // ---------- SUARA ----------
    var soundOn = true;
    try { soundOn = localStorage.getItem('admSound') !== 'off'; } catch (e) {}
    var ctx = null;
    var chip = null;

    function soundReady() { return !!ctx && ctx.state === 'running'; }

    // Browser memblokir suara sampai halaman diklik/diketik sekali.
    function unlock() {
        try {
            var C = window.AudioContext || window.webkitAudioContext;
            if (C) {
                if (!ctx) { ctx = new C(); }
                if (ctx.state === 'suspended') { ctx.resume(); }
            }
        } catch (e) {}
        setTimeout(refreshChip, 150);
    }
    ['pointerdown', 'keydown', 'touchstart'].forEach(function (ev) {
        document.addEventListener(ev, unlock, { passive: true });
    });

    // Tombol kecil di pojok kanan bawah selama suara belum aktif.
    function refreshChip() {
        var perlu = soundOn && !soundReady();
        if (perlu && !chip) {
            chip = document.createElement('button');
            chip.type = 'button';
            chip.textContent = 'Klik di sini untuk mengaktifkan suara notifikasi';
            chip.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:2000;border:0;border-radius:999px;' +
                'padding:.55rem 1rem;background:#146c43;color:#fff;font-size:.85rem;font-weight:600;' +
                'box-shadow:0 8px 24px rgba(0,0,0,.2);cursor:pointer';
            chip.addEventListener('click', unlock);
            document.body.append(chip);
        } else if (!perlu && chip) {
            chip.remove();
            chip = null;
        }
    }

    // bunyi "ting-ting" dua nada
    function ding() {
        if (!soundOn || !soundReady()) { return; }
        var now = ctx.currentTime;
        [880, 1175].forEach(function (freq, i) {
            var t = now + i * 0.18;
            var osc = ctx.createOscillator(), gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.0001, t);
            gain.gain.exponentialRampToValueAtTime(0.25, t + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, t + 0.16);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(t);
            osc.stop(t + 0.2);
        });
    }

    // suara ucapan: "Ada pesanan masuk" / "Ada pembayaran masuk"
    function speak(kinds) {
        if (!soundOn || !('speechSynthesis' in window)) { return; }
        var parts = [];
        if (kinds.order_baru)  { parts.push('Ada pesanan masuk'); }
        if (kinds.bukti_bayar) { parts.push('Ada pembayaran masuk'); }
        if (!parts.length) { return; }
        try {
            var u = new SpeechSynthesisUtterance(parts.join('. '));
            u.lang = 'id-ID';
            u.rate = 1;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(u);
        } catch (e) {}
    }

    function paintSound() {
        if (!soundBtn) { return; }
        soundBtn.classList.toggle('off', !soundOn);
        soundBtn.title = soundOn ? 'Suara nyala (klik untuk mematikan)' : 'Suara mati (klik untuk menyalakan)';
        var i = soundBtn.querySelector('i');
        if (i) { i.className = 'bi ' + (soundOn ? 'bi-volume-up' : 'bi-volume-mute'); }
    }
    if (soundBtn) {
        soundBtn.addEventListener('click', function () {
            soundOn = !soundOn;
            try { localStorage.setItem('admSound', soundOn ? 'on' : 'off'); } catch (e) {}
            unlock();
            paintSound();
            if (soundOn) { setTimeout(function () { ding(); speak({ order_baru: true }); }, 80); }
        });
        paintSound();
    }
    refreshChip();

    // ---------- TAMPILAN ----------
    // pakai textContent, bukan innerHTML, supaya nama customer tidak bisa menyisipkan script
    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) { e.className = cls; }
        if (text !== undefined) { e.textContent = text; }
        return e;
    }

    function render(items) {
        list.replaceChildren();
        if (!items.length) {
            var empty = el('div', 'tb-notif-empty');
            empty.append(el('i', 'bi bi-bell-slash'), 'Belum ada notifikasi');
            list.append(empty);
            return;
        }
        items.forEach(function (n) {
            var pay = n.kind === 'bukti_bayar';
            var a = el('a', 'dropdown-item tb-notif-item' + (n.unread ? ' unread' : ''));
            a.href = n.url;

            var ic = el('span', 'ic ' + (pay ? 'pay' : 'order'));
            ic.append(el('i', 'bi ' + (pay ? 'bi-credit-card-2-front' : 'bi-bag-plus')));

            var t = el('span', 't');
            t.append(el('i', 'bi bi-clock'), n.time);

            var box = el('span', 'flex-grow-1');
            box.append(el('b', '', n.title), el('span', 'd', n.body), t);

            a.append(ic, box);
            list.append(a);
        });
    }

    function toast(item, count) {
        if (!window.admToast) { return; }
        var pay = item.kind === 'bukti_bayar';
        window.admToast({
            type: pay ? 'info' : 'success',
            icon: pay ? 'bi-credit-card-2-front' : 'bi-bag-plus',
            title: count > 1 ? count + ' notifikasi baru' : item.title,
            message: count > 1 ? 'Klik lonceng untuk melihat semuanya.' : item.body,
            url: item.url,
            duration: 9000
        });
    }

    function apply(d) {
        var n = d.unread || 0;
        var items = d.items || [];

        badge.textContent = n > 99 ? '99+' : String(n);
        badge.hidden = n < 1;
        if (cnt) { cnt.textContent = n + ' baru'; cnt.hidden = n < 1; }
        document.title = (n > 0 ? '(' + n + ') ' : '') + baseTitle;
        render(items);

        if (n > lastUnread) {
            var jumlah = n - lastUnread;
            // notifikasi terbaru yang belum dibaca (daftar sudah urut dari yang terbaru)
            var baru = items.filter(function (i) { return i.unread; }).slice(0, jumlah);
            var kinds = {};
            baru.forEach(function (i) { kinds[i.kind] = true; });

            if (baru.length) {
                toast(baru[0], jumlah);
            }
            ding();
            setTimeout(function () { speak(kinds); }, 450);
        }
        lastUnread = n;
        refreshChip();
    }

    function poll() {
        fetch(POLL_URL, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) { if (d) { apply(d); } })
            .catch(function () {});
    }

    setInterval(poll, INTERVAL);
})();
</script>