@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<style>
    :root {
        --cat-primary: #198754;
        --cat-primary-dark: #145c38;
        --cat-primary-light: #eaf5ed;
        --cat-text: #212529;
        --cat-muted: #6c757d;
        --cat-border: #e2e8e3;
        --cat-card-bg: #ffffff;
    }

    .orders-page {
        background-color: #f8faf8;
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--cat-text);
    }

    .orders-page a {
        text-decoration: none;
    }

    /* Hero Banner Konsisten dengan Katalog */
    .orders-hero {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--cat-border);
        border-radius: 20px;
        padding: 28px 36px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .orders-hero-content {
        position: relative;
        z-index: 1;
    }

    .orders-kicker {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--cat-primary-dark);
        text-transform: uppercase;
    }

    .orders-hero-title {
        font-size: clamp(1.4rem, 2.5vw, 2.1rem);
        font-weight: 850;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin: 6px 0 10px;
        color: var(--cat-text);
    }

    .orders-hero-desc {
        font-size: 0.88rem;
        color: var(--cat-muted);
        line-height: 1.6;
        max-width: 580px;
        margin-bottom: 0;
    }

    .btn-create-order {
        background-color: var(--cat-primary);
        color: #ffffff !important;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-create-order:hover {
        background-color: var(--cat-primary-dark);
    }

    /* Stat Cards */
    .stat-card {
        border: 1px solid var(--cat-border);
        border-radius: 16px;
        background: var(--cat-card-bg);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
        border-color: #cde2d3;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.25rem;
    }

    /* Order Item Card (Disamakan Style-nya dengan Catalog Card) */
    .order-item-card {
        background: var(--cat-card-bg);
        border: 1px solid var(--cat-border);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .order-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
        border-color: #cde2d3;
    }

    .order-card-header {
        padding: 12px 16px;
        background: #ffffff;
        border-bottom: 1px dashed var(--cat-border);
    }

    .order-code-badge {
        background: var(--cat-primary-light);
        border: 1px solid var(--cat-border);
        color: var(--cat-primary-dark);
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .order-card-body {
        padding: 14px 16px;
    }

    .order-info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--cat-primary-dark);
        background: var(--cat-primary-light);
        padding: 3px 8px;
        border-radius: 6px;
    }

    .order-card-footer {
        padding: 12px 16px;
        background: #ffffff;
        border-top: 1px solid #f0f4f1;
    }

    .total-price-text {
        color: var(--cat-primary-dark);
        font-weight: 800;
        font-size: 1.1rem;
        line-height: 1.1;
    }

    .btn-detail-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background-color: var(--cat-primary);
        color: #ffffff !important;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    .btn-detail-order:hover {
        background-color: var(--cat-primary-dark);
    }

    .payment-status-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .orders-empty-state {
        text-align: center;
        padding: 42px 20px;
        background: #ffffff;
        border: 1px dashed var(--cat-border);
        border-radius: 16px;
    }

    .orders-empty-state i {
        font-size: 2.5rem;
        color: var(--cat-primary);
    }

    @media (max-width: 767.98px) {
        .order-card-header, .order-card-body, .order-card-footer {
            padding: 12px;
        }
    }
</style>

<div class="orders-page">
    <div class="container">

        {{-- Hero Banner --}}
        <section class="orders-hero">
            <div class="row align-items-center g-3 orders-hero-content">
                <div class="col-lg-8">
                    <span class="orders-kicker">
                        <i class="bi bi-receipt me-1"></i> CATERING BANGJO · STATUS PESANAN
                    </span>
                    <h1 class="orders-hero-title">Pesanan Saya</h1>
                    <p class="orders-hero-desc">
                        Pantau status pengerjaan pesanan catering, jadwal pengiriman, dan riwayat pembayaran Anda secara real-time.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('customer.packages.index') }}" class="btn-create-order text-decoration-none">
                        <i class="bi bi-plus-lg"></i> Buat Pesanan Baru
                    </a>
                </div>
            </div>
        </section>

        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @php
            $totalPesanan = $orders->count();
            $belumDibayar = $orders->filter(function ($order) {
                return !$order->isFullyPaid();
            })->count();
        @endphp

        {{-- Ringkasan Status (KPI Cards) --}}
        <div class="row g-2 g-md-3 mb-4">
            <div class="col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper bg-success-subtle text-success">
                            <i class="bi bi-bag-check-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total Pesanan Dibuat</div>
                            <div class="fs-4 fw-bold text-dark">{{ $totalPesanan }} <span class="fs-6 text-muted fw-normal">Transaksi</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Belum Status Lunas</div>
                            <div class="fs-4 fw-bold text-dark">{{ $belumDibayar }} <span class="fs-6 text-muted fw-normal">Pesanan</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Pesanan --}}
        @forelse ($orders as $order)
            @php
                $statusOrder = strtolower((string) $order->status);

                $warnaOrder = match ($statusOrder) {
                    'completed', 'selesai', 'completed order' => 'success',
                    'cancelled', 'dibatalkan', 'batal' => 'danger',
                    'ready for pickup/delivery', 'siap diambil', 'siap dikirim' => 'info',
                    'order processing', 'diproses', 'sedang diproses' => 'primary',
                    default => 'warning',
                };

                $statusPembayaran = $order->paymentState();

                $pembayaranBadge = match ($statusPembayaran) {
                    'Dibayar' => ['bg' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'bi-check-circle-fill', 'text' => 'Lunas'],
                    'Ditolak' => ['bg' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'bi-x-circle-fill', 'text' => 'Pembayaran Ditolak'],
                    'Menunggu Verifikasi' => ['bg' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'bi-hourglass-split', 'text' => 'Verifikasi Pembayaran'],
                    'Sebagian' => ['bg' => 'bg-info-subtle text-info-emphasis border border-info-subtle', 'icon' => 'bi-pie-chart-fill', 'text' => 'DP Dibayar'],
                    default => ['bg' => 'bg-secondary-subtle text-secondary border border-secondary-subtle', 'icon' => 'bi-credit-card', 'text' => 'Belum Dibayar'],
                };
            @endphp

            <div class="card order-item-card mb-3">
                {{-- Header Card --}}
                <div class="order-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="order-code-badge">
                            <i class="bi bi-receipt"></i> {{ $order->order_code }}
                        </span>
                        <span class="order-info-chip d-none d-sm-inline-flex">
                            <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="badge rounded-pill bg-{{ $warnaOrder }}-subtle text-{{ $warnaOrder }} px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                            @if(in_array(strtolower($order->status), ['completed', 'selesai', 'completed order']))
                                <i class="bi bi-check-circle-fill me-1"></i> Pesanan Selesai
                            @elseif(in_array(strtolower($order->status), ['ready for pickup/delivery', 'ready for pickup', 'siap diambil', 'siap dikirim']))
                                <i class="bi bi-box-seam-fill me-1"></i> {{ $order->delivery_method === 'pickup' ? 'Siap Diambil' : 'Siap / Sedang Diantar' }}
                            @elseif(in_array(strtolower($order->status), ['order processing', 'processing', 'diproses', 'sedang diproses']))
                                <i class="bi bi-gear-wide-connected me-1"></i> Sedang Diproses
                            @elseif(in_array(strtolower($order->status), ['cancelled', 'canceled', 'dibatalkan', 'batal']))
                                <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
                            @elseif(strtolower($order->payment?->status ?? '') === 'menunggu verifikasi')
                                <i class="bi bi-hourglass-split me-1"></i> Verifikasi Pembayaran
                            @elseif(in_array(strtolower($order->status), ['waiting payment', 'menunggu pembayaran']))
                                <i class="bi bi-wallet2 me-1"></i> Menunggu Pembayaran
                            @else
                                <i class="bi bi-clock-history me-1"></i> Menunggu Konfirmasi
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Body Card --}}
                <div class="order-card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex flex-wrap gap-2 mb-1">
                                <span class="order-info-chip d-sm-none">
                                    <i class="bi bi-calendar3"></i> Tgl Order: {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                </span>
                                <span class="order-info-chip">
                                    <i class="bi bi-truck"></i>
                                    Jadwal: {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') : 'Belum ditentukan' }}
                                </span>
                                <span class="order-info-chip">
                                    @if($order->delivery_method === 'pickup')
                                        <i class="bi bi-shop"></i> Ambil Sendiri
                                    @else
                                        <i class="bi bi-geo-alt"></i> Diantar Kurir
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="col-md-5 text-md-end mt-2 mt-md-0">
                            <span class="payment-status-tag {{ $pembayaranBadge['bg'] }}">
                                <i class="bi {{ $pembayaranBadge['icon'] }}"></i> Status Bayar: {{ $pembayaranBadge['text'] }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Footer Card --}}
                <div class="order-card-footer d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <span class="catalog-price-label">Total Biaya Pesanan</span>
                        <div class="total-price-text">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>

                    <a href="{{ route('customer.orders.show', $order->id) }}" class="btn-detail-order">
                        Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="orders-empty-state">
                <i class="bi bi-receipt-cutoff"></i>
                <h3 class="fw-bold fs-5 mt-2">Belum ada pesanan</h3>
                <p class="text-muted small mb-3">Anda belum memiliki riwayat pesanan catering. Yuk, pilih paket catering favoritmu sekarang!</p>
                <a href="{{ route('customer.packages.index') }}" class="btn-create-order">
                    <i class="bi bi-grid"></i> Lihat Paket Catering
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if (method_exists($orders, 'links') && $orders->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection