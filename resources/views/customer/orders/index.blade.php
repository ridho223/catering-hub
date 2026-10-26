
@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <span class="text-success fw-semibold small">
                <i class="bi bi-receipt me-1"></i> RIWAYAT PESANAN
            </span>
            <h2 class="fw-bold mt-2 mb-1">Pesanan Saya</h2>
            <p class="text-muted mb-0">
                Pantau pesanan catering dan status pembayaranmu di sini.
            </p>
        </div>

        <a href="{{ route('customer.packages.index') }}"
           class="btn btn-success rounded-pill px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i>Buat Pesanan
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    @php
        $totalPesanan = $orders->count();
        $belumDibayar = $orders->filter(function ($order) {
            return !$order->payment ||
                !in_array($order->payment->status, ['Dibayar']);
        })->count();
    @endphp

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                         style="width: 52px; height: 52px; flex-shrink: 0;">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pesanan pada halaman ini</div>
                        <div class="fs-4 fw-bold">{{ $totalPesanan }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                         style="width: 52px; height: 52px; flex-shrink: 0;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum berstatus lunas</div>
                        <div class="fs-4 fw-bold">{{ $belumDibayar }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            $statusPembayaran = $order->payment?->status ?? 'Belum dibuat';

            $warnaPembayaran = match ($statusPembayaran) {
                'Dibayar' => 'success',
                'Ditolak' => 'danger',
                'Menunggu Verifikasi' => 'warning',
                default => 'secondary',
            };
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
            <div class="card-body p-3 p-md-4">

                <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="text-muted small">Kode pesanan</span>
                            <span class="fw-bold">{{ $order->order_code }}</span>
                        </div>

                        <div class="text-muted small">
                            <i class="bi bi-calendar3 me-2"></i>
                            Dibuat {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        </div>

                        <div class="text-muted small mt-2">
                            <i class="bi bi-truck me-2"></i>
                            Jadwal {{ $order->delivery_date
                                ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y')
                                : 'Belum ditentukan' }}
                        </div>
                    </div>

                    <div class="d-flex flex-row flex-md-column align-items-start align-items-md-end gap-2">
                        <span class="badge rounded-pill bg-{{ $warnaOrder }}-subtle text-{{ $warnaOrder }} px-3 py-2">
                            {{ $order->status }}
                        </span>

                        <span class="badge rounded-pill bg-{{ $warnaPembayaran }}-subtle text-{{ $warnaPembayaran }} px-3 py-2">
                            <i class="bi bi-wallet2 me-1"></i>{{ $statusPembayaran }}
                        </span>
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <div class="text-muted small mb-1">Total pesanan</div>
                        <div class="fs-4 fw-bold text-success">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>

                    <a href="{{ route('customer.orders.show', $order->id) }}"
                       class="btn btn-outline-success rounded-pill px-4">
                        Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>

            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5 px-4">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-receipt fs-1"></i>
                </div>

                <h5 class="fw-bold">Belum ada pesanan</h5>
                <p class="text-muted mx-auto mb-4" style="max-width: 430px;">
                    Pesanan yang kamu buat akan muncul di sini. Yuk, pilih paket catering sesuai kebutuhanmu.
                </p>

                <a href="{{ route('customer.packages.index') }}"
                   class="btn btn-success rounded-pill px-4">
                    <i class="bi bi-grid me-2"></i>Lihat Paket Catering
                </a>
            </div>
        </div>
    @endforelse

    @if (method_exists($orders, 'links') && $orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
