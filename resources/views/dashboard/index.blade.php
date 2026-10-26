@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

@php
    // Variabel ini opsional. Selama controller belum mengirimnya, dashboard tetap tampil dengan nilai 0.
    $totalCategory = $totalCategory ?? 0;
    $totalMenu     = $totalMenu ?? 0;
    $totalOrder    = $totalOrder ?? 0;
    $revenue       = $revenue ?? 0;
    $recentOrders  = $recentOrders ?? collect();
    $statusCounts  = $statusCounts ?? [];
    $salesLabels   = $salesLabels ?? [];
    $salesData     = $salesData ?? [];
    $hasSales      = count($salesData) > 0 && array_sum($salesData) > 0;

    $orderStatuses = [
        'waiting_payment' => ['Menunggu pembayaran', 'warning'],
        'processing'      => ['Sedang diproses',     'primary'],
        'ready'           => ['Siap diambil/diantar', 'info'],
        'completed'       => ['Selesai',             'success'],
    ];

    $checklist = [
        ['Tambah kategori menu',   $totalCategory > 0],
        ['Tambah menu pertama',    $totalMenu > 0],
        ['Terima pesanan pertama', $totalOrder > 0],
    ];
    $doneCount = collect($checklist)->where(1, true)->count();

    // Antrean kerja admin (sesuai FR-11, FR-19, FR-25 di dokumen)
    $pendingRequests = $pendingRequests ?? 0;
    $pendingPayments = $pendingPayments ?? 0;
    $topMenus        = $topMenus ?? collect();
    $actions = [
        ['Request menunya perlu direview',  $pendingRequests,            'bi-chat-left-text', 'admin.requests.index'],
        ['Bukti bayar perlu diverifikasi',  $pendingPayments,            'bi-patch-check',    'admin.payments.index'],
        ['Pesanan siap diantar/diambil',    $statusCounts['ready'] ?? 0, 'bi-truck',          'admin.orders.index'],
    ];
@endphp

{{-- ===== Banner sambutan ===== --}}
<div class="dash-hero mb-4">
    <div>
        <p class="dash-hero-date mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        <h4 class="dash-hero-title mb-1">Halo, {{ auth()->user()->name ?? 'Admin' }}</h4>
        <p class="dash-hero-text mb-0">
            @if($totalOrder > 0)
                Ada {{ $totalOrder }} pesanan tercatat di Kedai Catering BangJo.
            @else
                Belum ada pesanan masuk. Lengkapi menu dulu supaya pelanggan bisa mulai memesan.
            @endif
        </p>
    </div>
    <div class="dash-hero-actions">
        <a href="{{ Route::has('admin.menus.create') ? route('admin.menus.create') : '#' }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah menu
        </a>
        <a href="{{ Route::has('admin.orders.index') ? route('admin.orders.index') : '#' }}" class="btn btn-outline-light btn-sm">
            <i class="bi bi-receipt"></i> Lihat pesanan
        </a>
    </div>
</div>

{{-- ===== Perlu ditangani ===== --}}
<div class="row g-3 mb-4">
    @foreach($actions as [$label, $count, $icon, $routeName])
        <div class="col-md-4">
            <a href="{{ Route::has($routeName) ? route($routeName) : '#' }}"
               class="action-tile {{ $count > 0 ? 'has-task' : '' }}">
                <i class="bi {{ $icon }}"></i>
                <div class="flex-grow-1">
                    <div class="action-label">{{ $label }}</div>
                    <div class="action-sub">{{ $count > 0 ? 'Klik untuk menangani' : 'Tidak ada antrean' }}</div>
                </div>
                <span class="action-count">{{ $count }}</span>
            </a>
        </div>
    @endforeach
</div>

{{-- ===== Kartu statistik ===== --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-tile">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-tags-fill"></i></div>
            <div>
                <div class="stat-label">Kategori</div>
                <div class="stat-value">{{ number_format($totalCategory, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-tile">
            <div class="stat-icon stat-icon-green"><i class="bi bi-cup-hot-fill"></i></div>
            <div>
                <div class="stat-label">Menu</div>
                <div class="stat-value">{{ number_format($totalMenu, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-tile">
            <div class="stat-icon stat-icon-amber"><i class="bi bi-cart-fill"></i></div>
            <div>
                <div class="stat-label">Pesanan</div>
                <div class="stat-value">{{ number_format($totalOrder, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-tile">
            <div class="stat-icon stat-icon-rose"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-label">Pendapatan</div>
                <div class="stat-value">Rp{{ number_format($revenue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

</div>

{{-- ===== Grafik + panel samping ===== --}}
<div class="row g-3 mb-4">

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Grafik penjualan</span>
                <small class="text-muted fw-normal">7 hari terakhir</small>
            </div>
            <div class="card-body">
                @if($hasSales)
                    <div class="chart-box"><canvas id="salesChart"></canvas></div>
                @else
                    <div class="dash-empty">
                        <i class="bi bi-bar-chart-line"></i>
                        <h6>Belum ada data penjualan</h6>
                        <p>Grafik muncul setelah ada pesanan yang selesai.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Status pesanan --}}
        <div class="card">
            <div class="card-header">Status pesanan</div>
            <div class="card-body py-2">
                @foreach($orderStatuses as $key => [$label, $color])
                    <div class="status-row">
                        <span class="status-dot bg-{{ $color }}"></span>
                        <span class="status-name">{{ $label }}</span>
                        <strong>{{ $statusCounts[$key] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Persiapan toko: hilang otomatis kalau semua langkah sudah selesai --}}
        @if($doneCount < count($checklist))
        <div class="card flex-grow-1">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Persiapan toko</span>
                <small class="text-muted fw-normal">{{ $doneCount }}/{{ count($checklist) }}</small>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height:6px;">
                    <div class="progress-bar bg-success" style="width: {{ ($doneCount / count($checklist)) * 100 }}%"></div>
                </div>
                @foreach($checklist as [$text, $done])
                    <div class="check-row {{ $done ? 'is-done' : '' }}">
                        <i class="bi {{ $done ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                        <span>{{ $text }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ===== Pesanan terbaru + Menu terlaris ===== --}}
<div class="row g-3">
<div class="col-lg-8">
<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Pesanan terbaru</span>
        <a href="{{ Route::has('admin.orders.index') ? route('admin.orders.index') : '#' }}" class="small text-success text-decoration-none">
            Lihat semua
        </a>
    </div>
    <div class="card-body p-0">
        @if($recentOrders->isEmpty())
            <div class="dash-empty">
                <i class="bi bi-inbox"></i>
                <h6>Belum ada pesanan</h6>
                <p>Pesanan dari pelanggan akan tampil di sini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Sesuaikan nama field dengan model Order kamu --}}
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->code ?? $order->id }}</td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>Rp{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                                <td><span class="badge text-bg-secondary">{{ $order->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>

<div class="col-lg-4">
    <div class="card h-100">
        <div class="card-header">Menu terlaris</div>
        <div class="card-body">
            @forelse($topMenus as $menu)
                <div class="top-menu-row">
                    <span class="top-menu-rank">{{ $loop->iteration }}</span>
                    <span class="flex-grow-1 text-truncate">{{ $menu->name }}</span>
                    <small class="text-muted">{{ $menu->sold ?? 0 }} porsi</small>
                </div>
            @empty
                <div class="dash-empty py-4">
                    <i class="bi bi-cup-hot"></i>
                    <h6>Belum ada data</h6>
                    <p>Menu yang paling sering dipesan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>

{{-- Chart.js hanya dimuat kalau ada data --}}
@if($hasSales)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(25, 135, 84, .28)');
        gradient.addColorStop(1, 'rgba(25, 135, 84, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesLabels),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json($salesData),
                    borderColor: '#198754',
                    backgroundColor: gradient,
                    fill: true,
                    tension: .35,
                    pointRadius: 3,
                    pointBackgroundColor: '#198754'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp' + v.toLocaleString('id-ID') }
                    }
                }
            }
        });
    </script>
@endif

@endsection