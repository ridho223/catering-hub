@extends('layouts.admin')

@section('title', 'Data Pesanan')

@section('content')
@php
    use Illuminate\Support\Carbon;

    $items = $orders instanceof \Illuminate\Pagination\AbstractPaginator
        ? $orders->getCollection()
        : collect($orders);

    $today  = Carbon::today();
    $counts = $items->countBy('status');

    $todayCount = $items->filter(
        fn ($o) => $o->status !== 'Selesai' && Carbon::parse($o->delivery_date)->isSameDay($today)
    )->count();

    $working = ($counts['Diproses'] ?? 0) + ($counts['Siap_diambil_atau_diantar'] ?? 0);

    $tabs = [
        'all'                  => 'Semua',
        'Pending'              => 'Menunggu bayar',
        'Diproses'             => 'Diproses',
        'Siap_diambil_atau_diantar' => 'Siap',
        'Selesai'              => 'Selesai',
    ];

    // kelas warna per status
    $chipClass = [
        'Pending'              => 'warn',
        'Diproses'             => 'info',
        'Siap_diambil_atau_diantar' => 'ready',
        'Selesai'              => 'ok',
    ];
@endphp

<style>
    .oix { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .oix-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .oix-top p { margin:0; color:var(--mute); }

    .oix-sum { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1.1rem; }
    .oix-tile { background:#fff; border:1px solid var(--line); border-radius:14px; padding:.9rem 1.1rem; display:flex; gap:.8rem; align-items:center; }
    .oix-tile i { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex:none; }
    .oix-tile small { color:var(--mute); display:block; line-height:1.2; }
    .oix-tile strong { font-size:1.35rem; color:var(--ink); line-height:1.2; }
    .oix-tile .i-all { background:#eef2ef; color:#4b5a52; }
    .oix-tile .i-warn { background:#fff1d6; color:#8a5a00; }
    .oix-tile .i-info { background:#e2f0fb; color:#0b5a8a; }
    .oix-tile .i-today { background:#fde8e8; color:#a52834; }

    .oix-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .oix-tabs { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:4px; gap:2px; overflow-x:auto; max-width:100%; }
    .oix-tabs button { border:0; background:transparent; padding:.4rem .85rem; border-radius:9px; color:var(--mute); font-size:.88rem; font-weight:500; white-space:nowrap; display:inline-flex; gap:.4rem; align-items:center; }
    .oix-tabs button:hover { color:var(--ink); }
    .oix-tabs button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .oix-tabs em { font-style:normal; font-size:.74rem; background:rgba(0,0,0,.07); border-radius:999px; padding:0 .45rem; min-width:1.3rem; text-align:center; }
    .oix-tabs button.on em { background:var(--brand); color:#fff; }
    .oix-search { position:relative; flex:1 1 240px; max-width:340px; }
    .oix-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .oix-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--line); }

    .oix-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .oix-table { width:100%; border-collapse:collapse; }
    .oix-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .oix-table td { padding:.8rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .oix-table tbody tr:hover { background:#fafcfa; }
    .oix-code { font-weight:700; color:var(--ink); text-decoration:none; }
    .oix-code:hover { color:var(--brand); }
    .oix-sub { display:block; color:var(--mute); font-size:.8rem; }
    .oix-cust { display:flex; align-items:center; gap:.65rem; }
    .oix-av { width:36px; height:36px; border-radius:50%; background:#e3f4ea; color:#146c43; font-weight:700; display:flex; align-items:center; justify-content:center; flex:none; }
    .oix-cust b { color:var(--ink); font-weight:600; display:block; line-height:1.2; }
    .oix-total { font-weight:700; color:var(--ink); white-space:nowrap; }

    .oix-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .oix-chip::before { content:""; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .oix-chip--warn { background:#fff1d6; color:#8a5a00; }
    .oix-chip--info { background:#e2f0fb; color:#0b5a8a; }
    .oix-chip--ready { background:#e8e6fb; color:#4a3fb0; }
    .oix-chip--ok { background:#e3f4ea; color:#146c43; }
    .oix-chip--off { background:#eceff1; color:#5f6b72; }

    .oix-due { display:inline-block; font-size:.74rem; font-weight:600; border-radius:6px; padding:.05rem .45rem; margin-top:.15rem; }
    .oix-due--late { background:#fde8e8; color:#a52834; }
    .oix-due--soon { background:#fff1d6; color:#8a5a00; }
    .oix-due--ok { background:var(--soft); color:var(--mute); }

    .oix-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .oix-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    .oix-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .oix-empty i { font-size:2.5rem; }
    #oixNoResult { display:none; }
    /* pagination Bootstrap 5: sembunyikan teks "Showing ... results" bawaan, rata kanan */
    .oix-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .oix-pg nav .flex-sm-fill > div:first-child { display:none; }
    .oix-pg .pagination { margin:0; }
    .oix-pg .pagination .page-link { color:#267548; border-color:#e6ebe7; }
    .oix-pg .pagination .active .page-link { background:#198754; border-color:#198754; color:#fff; }


    @media (max-width:767px){
        .oix-table thead { display:none; }
        .oix-table, .oix-table tbody, .oix-table tr, .oix-table td { display:block; width:100%; }
        .oix-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .oix-table td { border:0; padding:.3rem 1rem; }
        .oix-act { justify-content:flex-start; }
    }
</style>

<div class="container-fluid oix" id="oix">

    <div class="oix-top">
        <p>Pantau pesanan dari pembayaran sampai selesai diantar atau diambil.</p>
        <a href="{{ route('orders.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah pesanan
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

    <div class="oix-sum">
        <div class="oix-tile"><i class="bi bi-receipt i-all"></i><div><small>Total pesanan</small><strong>{{ $items->count() }}</strong></div></div>
        <div class="oix-tile"><i class="bi bi-hourglass-split i-warn"></i><div><small>Menunggu pembayaran</small><strong>{{ $counts['Pending'] ?? 0 }}</strong></div></div>
        <div class="oix-tile"><i class="bi bi-fire i-info"></i><div><small>Sedang dikerjakan</small><strong>{{ $working }}</strong></div></div>
        <div class="oix-tile"><i class="bi bi-calendar-event i-today"></i><div><small>Jadwal hari ini</small><strong>{{ $todayCount }}</strong></div></div>
    </div>

    <div class="oix-bar">
        <div class="oix-tabs" id="oixTabs" role="tablist" aria-label="Filter status">
            @foreach ($tabs as $key => $label)
                <button type="button" data-status="{{ $key }}" class="{{ $key === 'all' ? 'on' : '' }}">
                    {{ $label }} <em>{{ $key === 'all' ? $items->count() : ($counts[$key] ?? 0) }}</em>
                </button>
            @endforeach
        </div>

        <div class="oix-search">
            <i class="bi bi-search"></i>
            <input type="search" id="oixSearch" class="form-control" placeholder="Cari kode atau nama pelanggan" aria-label="Cari pesanan">
        </div>
    </div>

    <div class="oix-wrap">
        @if ($items->isEmpty())
            <div class="oix-empty">
                <i class="bi bi-cart"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada pesanan</h5>
                <p>Buat pesanan pertama untuk pelanggan.</p>
                <a href="{{ route('orders.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah pesanan</a>
            </div>
        @else
            <table class="oix-table">
                <thead>
                    <tr>
                        <th>Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Jadwal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $order)
                    @php
                        $isPickup  = ($order->delivery_method ?? 'pickup') === 'pickup';
                        $cName     = $order->customer->name ?? 'Pelanggan dihapus';
                        $code      = $order->order_code ?? ('#' . $order->id);
                        $delivery  = Carbon::parse($order->delivery_date)->startOfDay();
                        $days      = (int) round($today->diffInDays($delivery, false));
                        $finished  = $order->status === 'Selesai';

                        if ($finished)       { $due = null; }
                        elseif ($days < 0)   { $due = ['late', 'Terlambat ' . abs($days) . ' hari']; }
                        elseif ($days === 0) { $due = ['soon', 'Hari ini']; }
                        elseif ($days === 1) { $due = ['soon', 'Besok']; }
                        else                 { $due = ['ok', $days . ' hari lagi']; }

                        $cls   = $chipClass[$order->status] ?? 'off';
                        $label = match ($order->status) {
                            'Pending'              => 'Menunggu bayar',
                            'Siap_diambil_atau_diantar' => $isPickup ? 'Siap diambil' : 'Siap diantar',
                            default                => $order->status,
                        };
                        $search = \Illuminate\Support\Str::lower($code . ' ' . $cName . ' ' . ($order->customer->phone ?? ''));
                    @endphp
                    <tr class="oix-item" data-status="{{ $order->status }}" data-search="{{ $search }}">
                        <td>
                            <a class="oix-code" href="{{ route('orders.show', $order->id) }}">{{ $code }}</a>
                            <span class="oix-sub">Dipesan {{ Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</span>
                        </td>
                        <td>
                            <div class="oix-cust">
                                <span class="oix-av">{{ strtoupper(mb_substr($cName, 0, 1)) }}</span>
                                <div>
                                    <b>{{ $cName }}</b>
                                    <span class="oix-sub">{{ $order->customer->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $delivery->translatedFormat('d M Y') }}
                            <span class="oix-sub">{{ $isPickup ? 'Diambil sendiri' : 'Diantar' }}</span>
                            @if ($due)<span class="oix-due oix-due--{{ $due[0] }}">{{ $due[1] }}</span>@endif
                        </td>
                        <td class="oix-total">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="oix-chip oix-chip--{{ $cls }}">{{ $label }}</span></td>
                        <td>
                            <div class="oix-act">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-success" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus pesanan {{ e($code) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="oix-empty" id="oixNoResult">
                <i class="bi bi-search"></i>
                <h6 class="mt-2 mb-1 text-dark">Tidak ada pesanan yang cocok</h6>
                <p class="mb-0">Coba ganti filter status atau kata kunci.</p>
            </div>
        @endif
    </div>

    @if (method_exists($orders, 'links'))
        <div class="oix-pg mt-3">{{ $orders->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
(function () {
    const root = document.getElementById('oix');
    if (!root) return;
    const rows = root.querySelectorAll('.oix-item');
    const tabs = root.querySelectorAll('#oixTabs button');
    const search = document.getElementById('oixSearch');
    const noResult = document.getElementById('oixNoResult');
    let status = 'all';

    function apply() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const ok = (status === 'all' || r.dataset.status === status) && (!q || r.dataset.search.includes(q));
            r.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        if (noResult) noResult.style.display = rows.length && !visible ? 'block' : 'none';
    }
    tabs.forEach(t => t.addEventListener('click', () => {
        status = t.dataset.status;
        tabs.forEach(x => x.classList.toggle('on', x === t));
        apply();
    }));
    search.addEventListener('input', apply);
})();
</script>
@endsection