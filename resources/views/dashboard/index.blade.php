@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dh-hero { display:flex; justify-content:space-between; align-items:center; gap:1.2rem; flex-wrap:wrap; padding:1.5rem 1.6rem; border-radius:20px; border:1px solid #d7e9dd; background:radial-gradient(circle at 100% 0, #dff1e5 0, transparent 45%), linear-gradient(135deg,#f7fbf8,#fff); }
    .dh-date { color:var(--mute); font-size:.88rem; margin:0 0 .15rem; }
    .dh-hero h2 { margin:0 0 .3rem; font-weight:800; font-size:1.5rem; color:var(--ink); }
    .dh-hero p.sum { margin:0; color:var(--mute); max-width:560px; }
    .dh-hero .btn { border-radius:12px; font-weight:600; }

    .dh-queue { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:.75rem; }
    .dh-q { display:flex; align-items:center; gap:.8rem; background:#fff; border:1px solid var(--line); border-radius:16px; padding:.85rem 1rem; text-decoration:none; color:inherit; transition:transform .15s, border-color .15s; }
    .dh-q:hover { transform:translateY(-2px); border-color:#b9d8c7; color:inherit; }
    .dh-q .ui-ic { width:40px; height:40px; border-radius:12px; font-size:1.1rem; }
    .dh-q b { font-size:1.35rem; line-height:1.1; display:block; color:var(--ink); }
    .dh-q span { color:var(--mute); font-size:.84rem; line-height:1.2; display:block; }
    .dh-q.has-warn { border-color:#f2d99a; }
    .dh-q.has-bad  { border-color:#f3b9bf; }

    .dh-chart { position:relative; height:290px; }
    .dh-stack { display:flex; height:12px; border-radius:8px; overflow:hidden; background:var(--line); margin-bottom:.9rem; }
    .dh-stack i { display:block; height:100%; }

    @media (max-width:575.98px) {
        .dh-hero { padding:1.2rem; }
        .dh-hero h2 { font-size:1.25rem; }
        .dh-hero > div:last-child { width:100%; }
        .dh-hero .btn { flex:1; }
        .dh-chart { height:230px; }
    }
    @media (prefers-reduced-motion:reduce) { .dh-q { transition:none; } .dh-q:hover { transform:none; } }
</style>
@endpush

@section('content')
@php
    $url = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';

    // Semua variabel opsional: dashboard tetap tampil (nilai 0) jika controller belum mengirimnya.
    $totalCategory    = $totalCategory ?? 0;
    $totalMenu        = $totalMenu ?? 0;
    $totalCustomer    = $totalCustomer ?? 0;
    $revenueThisMonth = $revenueThisMonth ?? 0;
    $revenueTrend     = $revenueTrend ?? 0;
    $ordersThisMonth  = $ordersThisMonth ?? 0;
    $ordersTrend      = $ordersTrend ?? 0;
    $statusCounts     = $statusCounts ?? [];
    $pendingPayments  = $pendingPayments ?? 0;
    $todayCount       = $todayCount ?? 0;
    $lowStockCount    = $lowStockCount ?? 0;
    $lowStock         = $lowStock ?? collect();
    $recentOrders     = $recentOrders ?? collect();
    $upcomingOrders   = $upcomingOrders ?? collect();
    $topPackages      = $topPackages ?? collect();
    $chartLabels      = $chartLabels ?? [];
    $chartData        = $chartData ?? [];
    $hasSales         = array_sum(collect($chartData)->all()) > 0;

    $hour  = now()->hour;
    $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));

    // Urutan alur status pesanan
    $statusMeta = [
        'Pending'              => ['Menunggu bayar',       '#f0ad00', 'warn'],
        'Diproses'             => ['Diproses',             '#2f8fd8', 'info'],
        'Siap_diambil_atau_diantar' => ['Siap_diambil_atau_diantar', '#6b5ce7', 'ready'],
        'Selesai'              => ['Selesai',              '#198754', 'ok'],
    ];
    $statusTotal = max(1, array_sum(array_map(fn ($k) => $statusCounts[$k] ?? 0, array_keys($statusMeta))));

    // Antrean kerja admin: [label, jumlah, ikon, link, nada]
    $actions = [
        ['Verifikasi pembayaran', $pendingPayments,                           'bi-patch-check',    $url('admin.payments.index'), 'warn'],
        ['Sedang diproses',       $statusCounts['Diproses'] ?? 0,             'bi-fire',           $url('orders.index'),         'info'],
        ['Siap_diambil_atau_diantar',  $statusCounts['Siap_diambil_atau_diantar'] ?? 0, 'bi-bag-check',      $url('orders.index'),         'ready'],
        ['Jadwal hari ini',       $todayCount,                                'bi-calendar-event', $url('orders.index'),         'bad'],
        ['Stok menipis',          $lowStockCount,                             'bi-box-seam',       $url('menu.index'),           'warn'],
    ];
    $toneClass = ['warn' => 't-amber', 'info' => 't-blue', 'ready' => 't-purple', 'bad' => 't-red'];

    $parts = [];
    if ($pendingPayments > 0) { $parts[] = $pendingPayments . ' pembayaran menunggu verifikasi'; }
    if ($todayCount > 0)      { $parts[] = $todayCount . ' pesanan dijadwalkan hari ini'; }
    $summary = $parts ? implode(' dan ', $parts) . '.' : 'Tidak ada antrean mendesak. Semua pesanan terkendali.';

    $trendChip = function ($t) {
        if ($t === null) return ['flat', 'Baru bulan ini'];
        if ($t == 0)     return ['flat', 'Sama seperti bulan lalu'];
        return [$t > 0 ? 'up' : 'down', ($t > 0 ? '▲ ' : '▼ ') . abs($t) . '% dari bulan lalu'];
    };
    [$revCls, $revTxt] = $trendChip($revenueTrend);
    [$ordCls, $ordTxt] = $trendChip($ordersTrend);
@endphp

<div class="ui-page">

    {{-- SAPAAN --}}
    <section class="dh-hero">
        <div>
            <p class="dh-date">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h2>{{ $greet }}, {{ \Illuminate\Support\Str::of(auth()->user()->name ?? 'Admin')->before(' ') }}</h2>
            <p class="sum">{{ $summary }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ $url('orders.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Pesanan baru</a>
            <a href="{{ $url('menu.create') }}" class="btn btn-outline-success"><i class="bi bi-cup-hot me-1"></i> Tambah menu</a>
        </div>
    </section>

    {{-- ANTREAN KERJA --}}
    <section class="dh-queue" aria-label="Antrean kerja">
        @foreach ($actions as [$label, $count, $icon, $href, $tone])
            <a href="{{ $href }}" class="dh-q {{ $count > 0 && in_array($tone, ['warn', 'bad']) ? 'has-' . $tone : '' }}">
                <span class="ui-ic {{ $count > 0 ? $toneClass[$tone] : 't-gray' }}"><i class="bi {{ $icon }}"></i></span>
                <div><b>{{ $count }}</b><span>{{ $label }}</span></div>
            </a>
        @endforeach
    </section>

    {{-- KPI --}}
    <section class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-green"><i class="bi bi-cash-stack"></i></span>
                <div>
                    <small>Pemasukan bulan ini</small>
                    <strong>Rp{{ number_format($revenueThisMonth, 0, ',', '.') }}</strong>
                    <span class="ui-trend {{ $revCls }}">{{ $revTxt }}</span>
                </div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-blue"><i class="bi bi-cart-check"></i></span>
                <div>
                    <small>Pesanan bulan ini</small>
                    <strong>{{ number_format($ordersThisMonth, 0, ',', '.') }}</strong>
                    <span class="ui-trend {{ $ordCls }}">{{ $ordTxt }}</span>
                </div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-amber"><i class="bi bi-people"></i></span>
                <div>
                    <small>Pelanggan</small>
                    <strong>{{ number_format($totalCustomer, 0, ',', '.') }}</strong>
                    <a href="{{ $url('customers.index') }}" class="ui-trend flat">Lihat daftar</a>
                </div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-purple"><i class="bi bi-cup-hot"></i></span>
                <div>
                    <small>Menu</small>
                    <strong>{{ number_format($totalMenu, 0, ',', '.') }}</strong>
                    <span class="ui-trend flat">{{ $totalCategory }} kategori</span>
                </div>
            </div></div>
        </div>
    </section>

    {{-- GRAFIK + STATUS --}}
    <section class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-head">
                    <div><h2>Pemasukan</h2><small>Pembayaran terverifikasi per hari</small></div>
                    @if ($hasSales)
                        <div class="ui-seg" id="rangeSeg" role="group" aria-label="Rentang grafik">
                            <button type="button" data-days="7" class="on">7 hari</button>
                            <button type="button" data-days="30">30 hari</button>
                        </div>
                    @endif
                </div>
                @if ($hasSales)
                    <div class="dh-chart"><canvas id="salesChart" aria-label="Grafik pemasukan harian" role="img"></canvas></div>
                @else
                    <div class="ui-empty">
                        <i class="bi bi-bar-chart-line"></i>
                        <b>Belum ada pemasukan</b>
                        Grafik muncul setelah ada pembayaran yang diverifikasi.
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-head"><div><h2>Status pesanan</h2><small>Semua pesanan</small></div></div>

                <div class="dh-stack" aria-hidden="true">
                    @foreach ($statusMeta as $key => [$label, $color])
                        <i style="width:{{ (($statusCounts[$key] ?? 0) / $statusTotal) * 100 }}%; background:{{ $color }}"></i>
                    @endforeach
                </div>

                @foreach ($statusMeta as $key => [$label, $color])
                    <div class="ui-row">
                        <span class="ui-dot" style="background:{{ $color }}"></span>
                        <span class="n">{{ $label }}</span>
                        <b>{{ $statusCounts[$key] ?? 0 }}</b>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PESANAN + SAMPING --}}
    <section class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-head">
                    <div class="ui-seg" id="ordTabs" role="tablist">
                        <button type="button" role="tab" aria-selected="true"  data-pane="recent" class="on">Pesanan terbaru</button>
                        <button type="button" role="tab" aria-selected="false" data-pane="upcoming">Jadwal terdekat</button>
                    </div>
                    <a href="{{ $url('orders.index') }}" class="ui-link">Lihat semua</a>
                </div>

                {{-- terbaru --}}
                <div data-pane="recent" role="tabpanel">
                    @if ($recentOrders->isEmpty())
                        <div class="ui-empty"><i class="bi bi-inbox"></i>Belum ada pesanan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="ui-table ui-table--stack">
                                <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
                                <tbody>
                                @foreach ($recentOrders as $order)
                                    @php $m = $statusMeta[$order->status] ?? [$order->status, '#999', 'off']; @endphp
                                    <tr>
                                        <td data-label="Pesanan"><div>
                                            <a href="{{ $url('orders.show', $order->id) }}">{{ $order->order_code ?? '#' . $order->id }}</a>
                                            <span class="sub">{{ \Illuminate\Support\Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</span>
                                        </div></td>
                                        <td data-label="Pelanggan">{{ $order->customer?->name ?? '-' }}</td>
                                        <td data-label="Total" class="fw-semibold text-nowrap">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td data-label="Status"><span class="ui-chip {{ $m[2] }}">{{ $m[0] }}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- jadwal --}}
                <div data-pane="upcoming" role="tabpanel" hidden>
                    @if ($upcomingOrders->isEmpty())
                        <div class="ui-empty"><i class="bi bi-calendar-check"></i>Tidak ada jadwal yang menunggu.</div>
                    @else
                        <div class="table-responsive">
                            <table class="ui-table ui-table--stack">
                                <thead><tr><th>Jadwal</th><th>Pesanan</th><th>Pelanggan</th><th>Status</th></tr></thead>
                                <tbody>
                                @foreach ($upcomingOrders as $order)
                                    @php
                                        $m    = $statusMeta[$order->status] ?? [$order->status, '#999', 'off'];
                                        $date = \Illuminate\Support\Carbon::parse($order->delivery_date)->startOfDay();
                                        $left = (int) round(\Illuminate\Support\Carbon::today()->diffInDays($date, false));
                                        [$dueTxt, $dueCls] = $left < 0 ? ['Terlambat ' . abs($left) . ' hari', 'bad'] : ($left === 0 ? ['Hari ini', 'warn'] : ($left === 1 ? ['Besok', 'warn'] : [$left . ' hari lagi', 'off']));
                                    @endphp
                                    <tr>
                                        <td data-label="Jadwal"><div>
                                            {{ $date->translatedFormat('d M Y') }}
                                            <span class="ui-chip {{ $dueCls }} mt-1 d-flex" style="width:fit-content">{{ $dueTxt }}</span>
                                        </div></td>
                                        <td data-label="Pesanan"><a href="{{ $url('orders.show', $order->id) }}">{{ $order->order_code ?? '#' . $order->id }}</a></td>
                                        <td data-label="Pelanggan">{{ $order->customer?->name ?? '-' }}</td>
                                        <td data-label="Status"><span class="ui-chip {{ $m[2] }}">{{ $m[0] }}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="ui-card h-auto">
                <div class="ui-head"><div><h2>Paket terlaris</h2><small>30 hari terakhir</small></div></div>
                @forelse ($topPackages as $p)
                    <div class="ui-rank {{ $loop->first ? 'top' : '' }}">
                        <em>{{ $loop->iteration }}</em>
                        <b title="{{ $p->name }}">{{ $p->name }}</b>
                        <small>{{ number_format($p->sold, 0, ',', '.') }} paket</small>
                    </div>
                @empty
                    <div class="ui-empty"><i class="bi bi-box-seam"></i>Belum ada data penjualan paket.</div>
                @endforelse
            </div>

            <div class="ui-card h-auto">
                <div class="ui-head">
                    <div><h2>Stok menipis</h2><small>5 porsi atau kurang</small></div>
                    <a href="{{ $url('menu.index') }}" class="ui-link">Kelola</a>
                </div>
                @forelse ($lowStock as $menu)
                    <div class="ui-rank">
                        <em><i class="bi bi-exclamation-lg"></i></em>
                        <b title="{{ $menu->name }}">{{ $menu->name }}</b>
                        <small class="{{ $menu->stock <= 0 ? 'text-danger fw-semibold' : '' }}">{{ $menu->stock }} porsi</small>
                    </div>
                @empty
                    <div class="ui-empty"><i class="bi bi-check2-circle"></i>Semua stok aman.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // tab pesanan terbaru / jadwal
    var tabs = document.querySelectorAll('#ordTabs button');
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            tabs.forEach(function (x) {
                x.classList.toggle('on', x === t);
                x.setAttribute('aria-selected', x === t ? 'true' : 'false');
            });
            document.querySelectorAll('[data-pane][role="tabpanel"]').forEach(function (p) {
                p.hidden = p.dataset.pane !== t.dataset.pane;
            });
        });
    });
});
</script>

@if ($hasSales)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var allLabels = @json($chartLabels);
    var allData   = @json($chartData);
    var ctx = document.getElementById('salesChart').getContext('2d');

    var gradient = ctx.createLinearGradient(0, 0, 0, 290);
    gradient.addColorStop(0, 'rgba(25,135,84,.28)');
    gradient.addColorStop(1, 'rgba(25,135,84,0)');

    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels.slice(-7),
            datasets: [{
                data: allData.slice(-7),
                borderColor: '#198754', backgroundColor: gradient, fill: true,
                tension: .35, borderWidth: 2.5, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#198754'
            }]
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function (c) { return window.adm.rp(c.parsed.y); } } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                y: { beginAtZero: true, grid: { color: '#eef1ee' }, ticks: { callback: function (v) { return 'Rp' + window.adm.short(v); } } }
            }
        }
    });

    document.querySelectorAll('#rangeSeg button').forEach(function (b) {
        b.addEventListener('click', function () {
            var n = Number(b.dataset.days);
            document.querySelectorAll('#rangeSeg button').forEach(function (x) { x.classList.toggle('on', x === b); });
            chart.data.labels = allLabels.slice(-n);
            chart.data.datasets[0].data = allData.slice(-n);
            chart.data.datasets[0].pointRadius = n > 7 ? 0 : 3;
            chart.update();
        });
    });
});
</script>
@endif
@endpush
@endsection