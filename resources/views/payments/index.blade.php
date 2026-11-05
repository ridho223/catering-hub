@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
@php
    use Illuminate\Support\Str;

    $items = $orders instanceof \Illuminate\Pagination\AbstractPaginator
        ? $orders->getCollection()
        : collect($orders);

    // status per pesanan; tanpa data pembayaran = Belum Dibayar
    $statusOf = fn ($order) => $order->paymentState();
    $counts   = $items->countBy($statusOf);

    $tabs = [
        'all'                 => 'Semua',
        'Menunggu Verifikasi' => 'Perlu diverifikasi',
        'Sebagian'            => 'Sebagian',
        'Dibayar'             => 'Lunas',
        'Ditolak'             => 'Ditolak',
        'Belum Dibayar'       => 'Belum dibayar',
    ];

    $chipClass = [
        'Menunggu Verifikasi' => 'warn',
        'Sebagian'            => 'info',
        'Dibayar'             => 'ok',
        'Ditolak'             => 'bad',
        'Belum Dibayar'       => 'off',
    ];
@endphp

<style>
    .pyx { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .pyx-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .pyx-top p { margin:0; color:var(--mute); }

    .pyx-sum { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1.1rem; }
    .pyx-tile { background:#fff; border:1px solid var(--line); border-radius:14px; padding:.9rem 1.1rem; display:flex; gap:.8rem; align-items:center; }
    .pyx-tile i { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex:none; }
    .pyx-tile small { color:var(--mute); display:block; line-height:1.2; }
    .pyx-tile strong { font-size:1.35rem; color:var(--ink); line-height:1.2; }
    .pyx-tile.hot { border-color:#f2d99a; background:#fffbf0; }
    .i-w { background:#fff1d6; color:#8a5a00; } .i-b { background:#e2f0fb; color:#0b5a8a; } .i-g { background:#e3f4ea; color:#146c43; } .i-n { background:#eef2ef; color:#4b5a52; }

    .pyx-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .pyx-tabs { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:4px; gap:2px; overflow-x:auto; max-width:100%; }
    .pyx-tabs button { border:0; background:transparent; padding:.4rem .8rem; border-radius:9px; color:var(--mute); font-size:.87rem; font-weight:500; white-space:nowrap; display:inline-flex; gap:.4rem; align-items:center; }
    .pyx-tabs button:hover { color:var(--ink); }
    .pyx-tabs button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .pyx-tabs em { font-style:normal; font-size:.74rem; background:rgba(0,0,0,.07); border-radius:999px; padding:0 .45rem; min-width:1.3rem; text-align:center; }
    .pyx-tabs button.on em { background:var(--brand); color:#fff; }
    .pyx-search { position:relative; flex:1 1 240px; max-width:340px; }
    .pyx-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .pyx-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--line); }

    .pyx-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .pyx-table { width:100%; border-collapse:collapse; }
    .pyx-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .pyx-table td { padding:.8rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .pyx-table tbody tr:hover { background:#fafcfa; }
    .pyx-table tr.need td:first-child { box-shadow:inset 3px 0 0 #f0ad00; }
    .pyx-code { font-weight:700; color:var(--ink); }
    .pyx-sub { display:block; color:var(--mute); font-size:.8rem; }
    .pyx-cust b { color:var(--ink); font-weight:600; display:block; line-height:1.25; }
    .pyx-money { font-weight:700; color:var(--ink); white-space:nowrap; }
    .pyx-paid { min-width:150px; }
    .pyx-prog { height:6px; background:var(--line); border-radius:4px; overflow:hidden; margin-top:.35rem; }
    .pyx-prog i { display:block; height:100%; background:var(--brand); border-radius:4px; }
    .pyx-proof { color:var(--brand); font-size:.78rem; font-weight:600; }

    .pyx-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .pyx-chip::before { content:""; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .pyx-chip--warn { background:#fff1d6; color:#8a5a00; }
    .pyx-chip--info { background:#e2f0fb; color:#0b5a8a; }
    .pyx-chip--ok { background:#e3f4ea; color:#146c43; }
    .pyx-chip--bad { background:#fde8e8; color:#a52834; }
    .pyx-chip--off { background:#eceff1; color:#5f6b72; }

    .pyx-act { display:flex; justify-content:flex-end; }
    .pyx-act .btn { --bs-btn-padding-x:.7rem; --bs-btn-padding-y:.3rem; border-radius:10px; white-space:nowrap; }

    .pyx-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .pyx-empty i { font-size:2.5rem; }
    #pyxNoResult { display:none; }
    .pyx-note { color:var(--mute); font-size:.84rem; margin-top:.9rem; }

    .pyx-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .pyx-pg nav .flex-sm-fill > div:first-child { display:none; }
    .pyx-pg .pagination { margin:0; }
    .pyx-pg .pagination .page-link { color:#267548; border-color:var(--line); }
    .pyx-pg .pagination .active .page-link { background:var(--brand); border-color:var(--brand); color:#fff; }

    @media (max-width:767px){
        .pyx-table thead { display:none; }
        .pyx-table, .pyx-table tbody, .pyx-table tr, .pyx-table td { display:block; width:100%; }
        .pyx-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .pyx-table td { border:0; padding:.3rem 1rem; }
        .pyx-act { justify-content:flex-start; }
    }

    .pyx-prog { display:flex; }
    .pyx-prog i.pend { background:#f0ad00; }
    .pyx-pending { display:block; margin-top:.3rem; color:#8a5a00; font-size:.78rem; font-weight:600; }
</style>

<div class="container-fluid pyx" id="pyx">

    <div class="pyx-top">
        <p>Periksa bukti transfer dan verifikasi pembayaran pelanggan.</p>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-receipt me-1"></i> Daftar pesanan</a>
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

    <div class="pyx-sum">
        <div class="pyx-tile {{ ($counts['Menunggu Verifikasi'] ?? 0) > 0 ? 'hot' : '' }}"><i class="bi bi-hourglass-split i-w"></i><div><small>Perlu diverifikasi</small><strong>{{ $counts['Menunggu Verifikasi'] ?? 0 }}</strong></div></div>
        <div class="pyx-tile"><i class="bi bi-pie-chart i-b"></i><div><small>Dibayar sebagian</small><strong>{{ $counts['Sebagian'] ?? 0 }}</strong></div></div>
        <div class="pyx-tile"><i class="bi bi-check-circle i-g"></i><div><small>Lunas</small><strong>{{ $counts['Dibayar'] ?? 0 }}</strong></div></div>
        <div class="pyx-tile"><i class="bi bi-wallet2 i-n"></i><div><small>Belum ada pembayaran</small><strong>{{ $counts['Belum Dibayar'] ?? 0 }}</strong></div></div>
    </div>

    @if ($items->isNotEmpty())
        <div class="pyx-bar">
            <div class="pyx-tabs" id="pyxTabs" role="tablist" aria-label="Filter status pembayaran">
                @foreach ($tabs as $key => $label)
                    <button type="button" data-status="{{ $key }}" class="{{ $key === 'all' ? 'on' : '' }}">
                        {{ $label }} <em>{{ $key === 'all' ? $items->count() : ($counts[$key] ?? 0) }}</em>
                    </button>
                @endforeach
            </div>
            <div class="pyx-search">
                <i class="bi bi-search"></i>
                <input type="search" id="pyxSearch" class="form-control" placeholder="Cari kode atau nama customer" aria-label="Cari pembayaran">
            </div>
        </div>
    @endif

    <div class="pyx-wrap">
        @if ($items->isEmpty())
            <div class="pyx-empty">
                <i class="bi bi-wallet2"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada data pembayaran</h5>
                <p class="mb-0">Pesanan akan tampil di sini setelah dibuat.</p>
            </div>
        @else
            <table class="pyx-table">
                <thead>
                    <tr><th>Pesanan</th><th>Customer</th><th>Total tagihan</th><th>Dibayar</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($items as $order)

                    @php
                        $payment    = $order->payments->firstWhere('status', 'Menunggu Verifikasi') ?? $order->payments->sortByDesc('id')->first();
                        $status     = $statusOf($order);
                        $total      = (float) $order->total;
                        $paid       = $order->paidAmount();
                        $pending    = $order->pendingAmount();
                        $pct        = $total > 0 ? min(100, (int) round($paid / $total * 100)) : 0;
                        $pctPending = $total > 0 ? min(100 - $pct, (int) round($pending / $total * 100)) : 0;
                        $code       = $order->order_code ?? ('ORD-' . $order->id);
                        $cName      = $order->customer?->name ?? 'Customer tidak tersedia';
                        $need       = $status === 'Menunggu Verifikasi';
                        $completes  = $need && $payment && ($paid + (float) $payment->amount) >= $total - 0.5;
                    @endphp

                    <tr class="pyx-item {{ $need ? 'need' : '' }}" data-status="{{ $status }}" data-search="{{ Str::lower($code . ' ' . $cName) }}">
                        <td>
                            <span class="pyx-code">{{ $code }}</span>
                            <span class="pyx-sub">Pesanan #{{ $order->id }}</span>
                        </td>
                        <td class="pyx-cust"><b>{{ $cName }}</b></td>
                        <td class="pyx-money">Rp{{ number_format($total, 0, ',', '.') }}</td>
                        <td class="pyx-paid">
                            @if ($payment)
                                <span class="pyx-money">Rp{{ number_format($paid, 0, ',', '.') }}</span>
                                <div class="pyx-prog" title="{{ $pct }}% terverifikasi">
                                    <i style="width:{{ $pct }}%"></i><i class="pend" style="width:{{ $pctPending }}%"></i>
                                </div>
                                @if ($pending > 0)
                                    <span class="pyx-pending"><i class="bi bi-hourglass-split"></i> +Rp{{ number_format($pending, 0, ',', '.') }} menunggu verifikasi</span>
                                @endif
                                @php $nBukti = $order->payments->whereNotNull('proof')->count(); @endphp
                                @if ($nBukti > 0)
                                    <span class="pyx-proof"><i class="bi bi-paperclip"></i> {{ $nBukti }} bukti</span>
                                @endif
                            @else
                                <span class="pyx-sub">-</span>
                            @endif
                        </td>
                        <td><span class="pyx-chip pyx-chip--{{ $chipClass[$status] ?? 'off' }}">{{ $tabs[$status] ?? $status }}</span></td>
                        <td>
                            <div class="pyx-act">
                                @if ($payment)
                                   <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn {{ $need ? 'btn-success' : 'btn-outline-success' }}">
                                        <i class="bi {{ $need ? 'bi-patch-check' : 'bi-eye' }} me-1"></i>
                                        {{ $need ? ($completes ? 'Verifikasi lunas' : 'Verifikasi pembayaran') : 'Detail' }}
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary"><i class="bi bi-receipt me-1"></i>Pesanan</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pyx-empty" id="pyxNoResult">
                <i class="bi bi-search"></i>
                <h6 class="mt-2 mb-1 text-dark">Tidak ada yang cocok</h6>
                <p class="mb-0">Coba ganti filter status atau kata kunci.</p>
            </div>
        @endif
    </div>

    @if (method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="pyx-pg mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif

    @if (method_exists($orders, 'hasPages'))
        <p class="pyx-note">Ringkasan, filter, dan pencarian di atas berlaku untuk data pada halaman yang sedang dibuka.</p>
    @endif
</div>

<script>
(function () {
    const root = document.getElementById('pyx');
    const search = document.getElementById('pyxSearch');
    if (!search) return;
    const rows = root.querySelectorAll('.pyx-item');
    const tabs = root.querySelectorAll('#pyxTabs button');
    const none = document.getElementById('pyxNoResult');
    let status = 'all';

    function apply() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const ok = (status === 'all' || r.dataset.status === status) && (!q || r.dataset.search.includes(q));
            r.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        none.style.display = rows.length && !visible ? 'block' : 'none';
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