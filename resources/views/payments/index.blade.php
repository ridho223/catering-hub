
@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
@php
    $paymentsOnPage = $orders->getCollection()
        ->filter(fn ($order) => $order->payment !== null);

    $pendingCount = $paymentsOnPage
        ->filter(fn ($order) => $order->payment->status === 'Menunggu Verifikasi')
        ->count();

    $paidCount = $paymentsOnPage
        ->filter(fn ($order) => $order->payment->status === 'Dibayar')
        ->count();

    $unpaidCount = $orders->getCollection()
        ->filter(fn ($order) => !$order->payment)
        ->count();
@endphp

<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Pembayaran</h3>
            <p class="text-secondary mb-0">
                Kelola pembayaran dan verifikasi bukti transfer customer.
            </p>
        </div>

        <a href="{{ route('orders.index') }}"
           class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-receipt me-2"></i>
            Daftar Pesanan
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary small mb-2">Menunggu Verifikasi</p>
                            <h3 class="fw-bold mb-0">{{ $pendingCount }}</h3>
                        </div>
                        <div class="payment-icon bg-warning-subtle text-warning-emphasis">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary small mb-2">Pembayaran Lunas</p>
                            <h3 class="fw-bold mb-0">{{ $paidCount }}</h3>
                        </div>
                        <div class="payment-icon bg-success-subtle text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary small mb-2">Belum Ada Pembayaran</p>
                            <h3 class="fw-bold mb-0">{{ $unpaidCount }}</h3>
                        </div>
                        <div class="payment-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            <div class="p-4 border-bottom">
                <h5 class="fw-bold mb-1">Daftar Pembayaran</h5>
                <p class="text-secondary small mb-3">
                    Periksa nominal, status pembayaran, dan bukti yang dikirim.
                </p>

                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search"
                           id="paymentSearch"
                           class="form-control bg-light border-start-0"
                           placeholder="Cari kode pesanan atau nama customer...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="paymentTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">No.</th>
                            <th class="py-3">Kode Pesanan</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Total Tagihan</th>
                            <th class="py-3">Nominal Pembayaran</th>
                            <th class="py-3">Status</th>
                            <th class="text-center pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $payment = $order->payment;
                                $status = $payment?->status ?? 'Belum Dibayar';

                                $badgeClass = match ($status) {
                                    'Dibayar' => 'bg-success-subtle text-success',
                                    'Sebagian' => 'bg-info-subtle text-info-emphasis',
                                    'Menunggu Verifikasi' => 'bg-warning-subtle text-warning-emphasis',
                                    'Ditolak' => 'bg-danger-subtle text-danger',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp

                            <tr class="payment-row">
                                <td class="px-4 text-secondary">
                                    {{ $orders->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $order->order_code ?? 'ORD-' . $order->id }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $order->customer?->name ?? 'Customer tidak tersedia' }}
                                    </div>
                                    <small class="text-secondary">
                                        Pesanan #{{ $order->id }}
                                    </small>
                                </td>

                                <td class="fw-semibold">
                                    Rp{{ number_format((float) $order->total, 0, ',', '.') }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        Rp{{ number_format((float) ($payment?->amount ?? 0), 0, ',', '.') }}
                                    </span>

                                    @if ($payment?->proof)
                                        <div class="small text-success mt-1">
                                            <i class="bi bi-paperclip"></i>
                                            Bukti tersedia
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $badgeClass }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td class="text-center pe-4">
                                    @if ($payment)
                                        <a href="{{ route('admin.payments.show', $payment->id) }}"
                                           class="btn btn-sm btn-outline-success rounded-3">
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>
                                    @else
                                        <a href="{{ route('orders.show', $order->id) }}"
                                           class="btn btn-sm btn-outline-secondary rounded-3">
                                            <i class="bi bi-receipt me-1"></i>
                                            Pesanan
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-wallet2 fs-1 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">Belum ada data pembayaran</h6>
                                    <p class="text-secondary small mb-0">
                                        Data pesanan akan tampil di sini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="noSearchResult" style="display:none">
                            <td colspan="7" class="text-center py-4 text-secondary">
                                Data yang kamu cari tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-3 border-top">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <p class="text-secondary small mt-3">
        Ringkasan di atas menghitung data pada halaman yang sedang dibuka.
        Pencarian juga berlaku pada halaman tersebut saja.
    </p>
</div>

<style>
    .payment-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    #paymentTable th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .035em;
        white-space: nowrap;
    }

    #paymentTable td {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .payment-row:hover {
        background: #fafcfb;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const search = document.getElementById('paymentSearch');
        const rows = document.querySelectorAll('.payment-row');
        const noResult = document.getElementById('noSearchResult');

        if (!search) return;

        search.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const matched = row.textContent.toLowerCase().includes(keyword);
                row.style.display = matched ? '' : 'none';

                if (matched) visibleCount++;
            });

            if (noResult) {
                noResult.style.display =
                    visibleCount === 0 && rows.length > 0 ? '' : 'none';
            }
        });
    });
</script>
@endsection
