@extends('layouts.admin')

@section('title', 'Laporan')

@push('styles')
<style>
    .rp-filter { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1rem 1.2rem; display:flex; flex-wrap:wrap; gap:1rem 1.4rem; align-items:flex-end; justify-content:space-between; }
    .rp-label { display:block; font-size:.8rem; font-weight:600; color:var(--mute); margin-bottom:.3rem; }
    .rp-pres { display:flex; gap:.35rem; flex-wrap:wrap; }
    .rp-pres a { border:1px solid var(--line); background:#fff; border-radius:999px; padding:.28rem .85rem; font-size:.84rem; color:var(--mute); text-decoration:none; font-weight:500; }
    .rp-pres a:hover { border-color:#b9d8c7; color:var(--green-dark); }
    .rp-pres a.on { background:var(--green-light); border-color:#b9d8c7; color:var(--green-dark); font-weight:700; }
    .rp-dates { display:flex; gap:.6rem; align-items:flex-end; flex-wrap:wrap; }
    .rp-dates .form-control { border-color:var(--line); border-radius:10px; }
    .rp-dates .btn { border-radius:10px; font-weight:600; }
    .rp-period { color:var(--mute); font-size:.9rem; margin:0; }
    .rp-period b { color:var(--ink); }

    .rp-chart { position:relative; height:280px; }
    .rp-sec { font-weight:700; color:var(--ink); font-size:.9rem; margin:1.1rem 0 .3rem; }
    .rp-m { padding:.55rem 0; border-top:1px solid var(--line); font-size:.92rem; }
    .rp-m .top { display:flex; justify-content:space-between; gap:.6rem; }
    .rp-bar { height:6px; background:var(--line); border-radius:4px; overflow:hidden; margin-top:.35rem; }
    .rp-bar i { display:block; height:100%; background:var(--green); border-radius:4px; }

    @media (max-width:575.98px) {
        .rp-dates > div { flex:1 1 140px; }
        .rp-dates .btn { flex:1 1 auto; }
        .rp-chart { height:230px; }
    }
</style>
@endpush

@section('content')
@php
    $url = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $rp  = fn ($v) => 'Rp' . number_format((float) $v, 0, ',', '.');

    $span        = (int) $from->diffInDays($to) + 1;
    $periodLabel = $from->translatedFormat('d M Y') . ($from->isSameDay($to) ? '' : ' – ' . $to->translatedFormat('d M Y'));

    $statusMeta = [
        'Pending'              => ['Menunggu bayar',       '#f0ad00', 'warn'],
        'Diproses'             => ['Diproses',             '#2f8fd8', 'info'],
        'Siap_diambil_atau_diantar' => ['Siap_diambil_atau_diantar', '#6b5ce7', 'ready'],
        'Selesai'              => ['Selesai',              '#198754', 'ok'],
    ];
    $payClass = ['Dibayar' => 'ok', 'Sebagian' => 'info', 'Menunggu Verifikasi' => 'warn', 'Ditolak' => 'bad'];

    $statusByKey = $statusBreakdown->keyBy('status');
    $hasChart    = array_sum(collect($chartData)->all()) > 0;
    $methodTotal = max(1, (float) $methodBreakdown->sum('t'));
    $exportUrl   = $url('reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]);

    // Periode cepat: dibuat di server supaya tombol yang cocok dengan filter aktif bisa ditandai
    $today = \Illuminate\Support\Carbon::today();
    $lastM = $today->copy()->subMonthNoOverflow();
    $presets = [
        'Hari ini'   => [$today->copy(), $today->copy()],
        '7 hari'     => [$today->copy()->subDays(6), $today->copy()],
        'Bulan ini'  => [$today->copy()->startOfMonth(), $today->copy()],
        'Bulan lalu' => [$lastM->copy()->startOfMonth(), $lastM->copy()->endOfMonth()],
        'Tahun ini'  => [$today->copy()->startOfYear(), $today->copy()],
    ];
@endphp

<div class="ui-page">

    {{-- FILTER PERIODE --}}
    <form method="GET" action="{{ url()->current() }}" class="rp-filter no-print">
        <div>
            <span class="rp-label">Periode cepat</span>
            <div class="rp-pres">
                @foreach ($presets as $label => [$f, $t])
                    @php $on = $from->isSameDay($f) && $to->isSameDay($t); @endphp
                    <a href="{{ url()->current() . '?' . http_build_query(['from' => $f->toDateString(), 'to' => $t->toDateString()]) }}"
                       class="{{ $on ? 'on' : '' }}" @if ($on) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="rp-dates">
            <div>
                <label class="rp-label" for="from">Dari</label>
                <input type="date" id="from" name="from" class="form-control" value="{{ $from->toDateString() }}">
            </div>
            <div>
                <label class="rp-label" for="to">Sampai</label>
                <input type="date" id="to" name="to" class="form-control" value="{{ $to->toDateString() }}">
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-funnel me-1"></i> Terapkan</button>
            <a href="{{ $exportUrl }}" class="btn btn-outline-success"><i class="bi bi-download me-1"></i> CSV</a>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak</button>
        </div>
    </form>

    <p class="rp-period">Periode <b>{{ $periodLabel }}</b> · {{ $span }} hari</p>

    {{-- RINGKASAN --}}
    <section class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-green"><i class="bi bi-cash-stack"></i></span>
                <div><small>Pemasukan</small><strong>{{ $rp($revenue) }}</strong><span class="sub">Pembayaran terverifikasi</span></div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-blue"><i class="bi bi-cart-check"></i></span>
                <div><small>Jumlah pesanan</small><strong>{{ number_format($ordersCount, 0, ',', '.') }}</strong><span class="sub">{{ $finished }} selesai</span></div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-purple"><i class="bi bi-receipt"></i></span>
                <div><small>Nilai seluruh pesanan</small><strong>{{ $rp($grossOrders) }}</strong><span class="sub">Rata-rata {{ $rp($averageOrder) }} per pesanan</span></div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ui-card"><div class="ui-kpi">
                <span class="ui-ic t-red"><i class="bi bi-hourglass-split"></i></span>
                <div><small>Belum lunas</small><strong>{{ $rp($unpaid) }}</strong><span class="sub">Pesanan tanpa pembayaran terverifikasi</span></div>
            </div></div>
        </div>
    </section>

    {{-- GRAFIK + RINCIAN --}}
    <section class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-head"><div><h2>Pemasukan harian</h2><small>Pembayaran terverifikasi per hari</small></div></div>
                @if ($hasChart)
                    <div class="rp-chart"><canvas id="reportChart" role="img" aria-label="Grafik pemasukan harian"></canvas></div>
                @else
                    <div class="ui-empty"><i class="bi bi-bar-chart-line"></i>Belum ada pemasukan pada periode ini.</div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-head"><div><h2>Rincian</h2><small>Pesanan dan pembayaran</small></div></div>

                @foreach ($statusMeta as $key => [$label, $color])
                    @php $row = $statusByKey->get($key); @endphp
                    <div class="ui-row">
                        <span class="ui-dot" style="background:{{ $color }}"></span>
                        <span class="n">{{ $label }}</span>
                        <b>{{ $row->c ?? 0 }}</b>
                        <small class="text-end" style="min-width:84px">{{ $rp($row->t ?? 0) }}</small>
                    </div>
                @endforeach

                <div class="rp-sec">Metode pembayaran</div>
                @forelse ($methodBreakdown as $m)
                    <div class="rp-m">
                        <div class="top">
                            <span>{{ $m->method ? strtoupper($m->method) : 'Lainnya' }} <small class="text-muted">· {{ $m->c }}x</small></span>
                            <b>{{ $rp($m->t) }}</b>
                        </div>
                        <div class="rp-bar"><i style="width:{{ min(100, ($m->t / $methodTotal) * 100) }}%"></i></div>
                    </div>
                @empty
                    <div class="small text-muted">Belum ada pembayaran terverifikasi.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- TERLARIS --}}
    <section class="row g-3">
        <div class="col-md-6">
            <div class="ui-card">
                <div class="ui-head"><div><h2>Paket terlaris</h2><small>Berdasarkan jumlah paket dipesan</small></div></div>
                @forelse ($topPackages as $p)
                    <div class="ui-rank {{ $loop->first ? 'top' : '' }}">
                        <em>{{ $loop->iteration }}</em>
                        <b title="{{ $p->name }}">{{ $p->name }}</b>
                        <small>{{ number_format($p->sold, 0, ',', '.') }} paket · {{ $rp($p->amount) }}</small>
                    </div>
                @empty
                    <div class="ui-empty">Belum ada data.</div>
                @endforelse
            </div>
        </div>
        <div class="col-md-6">
            <div class="ui-card">
                <div class="ui-head"><div><h2>Menu paling banyak disiapkan</h2><small>Termasuk isi paket, dalam porsi</small></div></div>
                @forelse ($topMenus as $m)
                    <div class="ui-rank {{ $loop->first ? 'top' : '' }}">
                        <em>{{ $loop->iteration }}</em>
                        <b title="{{ $m->name }}">{{ $m->name }}</b>
                        <small>{{ number_format($m->sold, 0, ',', '.') }} porsi</small>
                    </div>
                @empty
                    <div class="ui-empty">Belum ada data.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- DAFTAR PESANAN --}}
    <section class="ui-card h-auto">
        <div class="ui-head">
            <div><h2>Daftar pesanan</h2><small>{{ number_format($orders->total(), 0, ',', '.') }} pesanan pada periode ini</small></div>
        </div>

        @if ($orders->isEmpty())
            <div class="ui-empty"><i class="bi bi-inbox"></i>Tidak ada pesanan pada periode ini.</div>
        @else
            <div class="table-responsive">
                <table class="ui-table ui-table--stack">
                    <thead>
                        <tr><th>Pesanan</th><th>Tanggal</th><th>Pelanggan</th><th>Total</th><th>Status pesanan</th><th>Pembayaran</th></tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        @php
                            $m   = $statusMeta[$order->status] ?? [$order->status, '#999', 'off'];
                            $pay = $order->payment?->status ?? 'Belum Dibayar';
                        @endphp
                        <tr>
                            <td data-label="Pesanan"><a href="{{ $url('orders.show', $order->id) }}">{{ $order->order_code ?? '#' . $order->id }}</a></td>
                            <td data-label="Tanggal" class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</td>
                            <td data-label="Pelanggan">{{ $order->customer?->name ?? '-' }}</td>
                            <td data-label="Total" class="fw-semibold text-nowrap">{{ $rp($order->total) }}</td>
                            <td data-label="Status pesanan"><span class="ui-chip {{ $m[2] }}">{{ $m[0] }}</span></td>
                            <td data-label="Pembayaran"><span class="ui-chip {{ $payClass[$pay] ?? 'off' }}">{{ $pay }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="ui-pg mt-3 no-print">{{ $orders->links('pagination::bootstrap-5') }}</div>
            @endif
        @endif
    </section>
</div>

@if ($hasChart)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('reportChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{ data: @json($chartData), backgroundColor: '#198754', borderRadius: 6, maxBarThickness: 36 }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function (c) { return window.adm.rp(c.parsed.y); } } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 12 } },
                y: { beginAtZero: true, grid: { color: '#eef1ee' }, ticks: { callback: function (v) { return 'Rp' + window.adm.short(v); } } }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection