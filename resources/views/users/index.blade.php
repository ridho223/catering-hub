@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
@php
    use Illuminate\Support\Str;

    $items = $users instanceof \Illuminate\Pagination\AbstractPaginator
        ? $users->getCollection()
        : collect($users);

    $total      = method_exists($users, 'total') ? $users->total() : $items->count();
    $superCount = $items->where('level', 'Superadmin')->count();
    $adminCount = $items->where('level', 'Admin')->count();
    $offCount   = $items->where('status', '!=', 'Aktif')->count();

    $tabs = ['all' => 'Semua', 'Superadmin' => 'Superadmin', 'Admin' => 'Admin'];
@endphp

<style>
    .usr { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .usr-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .usr-top p { margin:0; color:var(--mute); }

    .usr-sum { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1.1rem; }
    .usr-tile { background:#fff; border:1px solid var(--line); border-radius:14px; padding:.9rem 1.1rem; display:flex; gap:.8rem; align-items:center; }
    .usr-tile i { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex:none; }
    .usr-tile small { color:var(--mute); display:block; line-height:1.2; }
    .usr-tile strong { font-size:1.35rem; color:var(--ink); line-height:1.2; }
    .i-n { background:#eef2ef; color:#4b5a52; } .i-d { background:#e4e8e5; color:#26342a; } .i-b { background:#e2f0fb; color:#0b5a8a; } .i-o { background:#fde8e8; color:#a52834; }

    .usr-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .usr-tabs { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:4px; gap:2px; }
    .usr-tabs button { border:0; background:transparent; padding:.4rem .85rem; border-radius:9px; color:var(--mute); font-size:.88rem; font-weight:500; white-space:nowrap; }
    .usr-tabs button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .usr-search { position:relative; flex:1 1 240px; max-width:360px; }
    .usr-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .usr-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--line); }

    .usr-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .usr-table { width:100%; border-collapse:collapse; }
    .usr-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .usr-table td { padding:.8rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .usr-table tbody tr:hover { background:#fafcfa; }
    .usr-person { display:flex; align-items:center; gap:.75rem; }
    .usr-av { width:42px; height:42px; border-radius:50%; object-fit:cover; background:#e3f4ea; color:#146c43; font-weight:700; font-size:.9rem; display:flex; align-items:center; justify-content:center; flex:none; }
    .usr-person b { color:var(--ink); font-weight:600; display:block; line-height:1.25; }
    .usr-me { display:inline-block; background:var(--soft); color:var(--mute); border:1px solid var(--line); border-radius:6px; font-size:.7rem; font-weight:600; padding:0 .4rem; margin-left:.35rem; vertical-align:middle; }
    .usr-mail { color:var(--mute); font-size:.9rem; }

    .usr-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .usr-chip--super { background:#26342a; color:#fff; }
    .usr-chip--admin { background:#e2f0fb; color:#0b5a8a; }
    .usr-chip--on { background:#e3f4ea; color:#146c43; }
    .usr-chip--off { background:#eceff1; color:#5f6b72; }
    .usr-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

    .usr-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .usr-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    .usr-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .usr-empty i { font-size:2.5rem; }
    #usrNoResult { display:none; }

    .usr-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .usr-pg nav .flex-sm-fill > div:first-child { display:none; }
    .usr-pg .pagination { margin:0; }
    .usr-pg .pagination .page-link { color:#267548; border-color:var(--line); }
    .usr-pg .pagination .active .page-link { background:var(--brand); border-color:var(--brand); color:#fff; }

    @media (max-width:767px){
        .usr-table thead { display:none; }
        .usr-table, .usr-table tbody, .usr-table tr, .usr-table td { display:block; width:100%; }
        .usr-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .usr-table td { border:0; padding:.3rem 1rem; }
        .usr-act { justify-content:flex-start; }
    }
</style>

<div class="container-fluid usr" id="usr">

    <div class="usr-top">
        <p>Kelola akun Admin dan Superadmin yang bisa masuk ke panel ini.</p>
        <a href="{{ route('users.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah user
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="usr-sum">
        <div class="usr-tile"><i class="bi bi-people i-n"></i><div><small>Total user</small><strong>{{ $total }}</strong></div></div>
        <div class="usr-tile"><i class="bi bi-shield-lock i-d"></i><div><small>Superadmin</small><strong>{{ $superCount }}</strong></div></div>
        <div class="usr-tile"><i class="bi bi-person-gear i-b"></i><div><small>Admin</small><strong>{{ $adminCount }}</strong></div></div>
        <div class="usr-tile"><i class="bi bi-person-slash i-o"></i><div><small>Non-aktif</small><strong>{{ $offCount }}</strong></div></div>
    </div>

    @if ($items->isNotEmpty())
        <div class="usr-bar">
            <div class="usr-tabs" id="usrTabs" role="tablist" aria-label="Filter level">
                @foreach ($tabs as $key => $label)
                    <button type="button" data-level="{{ $key }}" class="{{ $key === 'all' ? 'on' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="usr-search">
                <i class="bi bi-search"></i>
                <input type="search" id="usrSearch" class="form-control" placeholder="Cari nama atau email" aria-label="Cari user">
            </div>
        </div>
    @endif

    <div class="usr-wrap">
        @if ($items->isEmpty())
            <div class="usr-empty">
                <i class="bi bi-people"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada user</h5>
                <p>Tambahkan akun pertama untuk mengelola panel admin.</p>
                <a href="{{ route('users.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah user</a>
            </div>
        @else
            <table class="usr-table">
                <thead>
                    <tr><th>User</th><th>Level</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($items as $user)
                    @php
                        $isMe    = $user->id === auth()->id();
                        $isSuper = $user->level === 'Superadmin';
                        $active  = $user->status === 'Aktif';
                    @endphp
                    <tr class="usr-item" data-level="{{ $user->level }}" data-search="{{ Str::lower($user->name . ' ' . $user->email) }}">
                        <td>
                            <div class="usr-person">
                                @if ($user->avatar)
                                    <img class="usr-av" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" loading="lazy">
                                @else
                                    <span class="usr-av">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                                @endif
                                <div>
                                    <b>{{ $user->name }}@if ($isMe)<span class="usr-me">Anda</span>@endif</b>
                                    <span class="usr-mail">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="usr-chip {{ $isSuper ? 'usr-chip--super' : 'usr-chip--admin' }}">{{ $isSuper ? 'Superadmin' : 'Admin' }}</span></td>
                        <td><span class="usr-chip {{ $active ? 'usr-chip--on' : 'usr-chip--off' }}"><span class="usr-dot"></span>{{ $active ? 'Aktif' : 'Non-Aktif' }}</span></td>
                        <td>
                            <div class="usr-act">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $user->name }}"><i class="bi bi-pencil"></i></a>
                                @unless ($isMe)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Hapus user {{ e($user->name) }}? Akun ini tidak akan bisa masuk lagi.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus" aria-label="Hapus {{ $user->name }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="usr-empty" id="usrNoResult">
                <i class="bi bi-search"></i>
                <h6 class="mt-2 mb-1 text-dark">User tidak ditemukan</h6>
                <p class="mb-0">Coba ganti filter atau kata kunci.</p>
            </div>
        @endif
    </div>

    @if (method_exists($users, 'links'))
        <div class="usr-pg mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
(function () {
    const root = document.getElementById('usr');
    const search = document.getElementById('usrSearch');
    if (!search) return;
    const rows = root.querySelectorAll('.usr-item');
    const tabs = root.querySelectorAll('#usrTabs button');
    const none = document.getElementById('usrNoResult');
    let level = 'all';

    function apply() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const ok = (level === 'all' || r.dataset.level === level) && (!q || r.dataset.search.includes(q));
            r.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        none.style.display = visible ? 'none' : 'block';
    }
    tabs.forEach(t => t.addEventListener('click', () => {
        level = t.dataset.level;
        tabs.forEach(x => x.classList.toggle('on', x === t));
        apply();
    }));
    search.addEventListener('input', apply);
})();
</script>
@endsection