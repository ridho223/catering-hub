
@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
@php
    $order = $payment->order;
    $status = $payment->status ?? 'Belum Dibayar';

    $badgeClass = match ($status) {
        'Dibayar' => 'bg-success-subtle text-success',
        'Sebagian' => 'bg-info-subtle text-info-emphasis',
        'Menunggu Verifikasi' => 'bg-warning-subtle text-warning-emphasis',
        'Ditolak' => 'bg-danger-subtle text-danger',
        default => 'bg-secondary-subtle text-secondary',
    };
@endphp

<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a href="{{ route('admin.payments.index') }}"
               class="text-decoration-none text-secondary small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Pembayaran
            </a>

            <h3 class="fw-bold mt-2 mb-1">Detail Pembayaran</h3>
            <p class="text-secondary mb-0">
                Pesanan {{ $order?->order_code ?? '-' }}
            </p>
        </div>

        <span class="badge rounded-pill px-3 py-2 {{ $badgeClass }}">
            <i class="bi bi-circle-fill me-1 small"></i>
            {{ $status }}
        </span>
    </div>

    {{-- Alerts --}}
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

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <div class="fw-semibold mb-1">Periksa kembali input kamu:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Main content --}}
        <div class="col-12 col-xl-8">

            {{-- Payment summary --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="payment-detail-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Informasi Pembayaran</h5>
                            <p class="text-secondary small mb-0">
                                Nominal dan status transaksi customer.
                            </p>
                        </div>
                    </div>

                    <div class="payment-amount-box rounded-4 p-4 mb-4">
                        <p class="text-secondary mb-2">Nominal Pembayaran</p>
                        <h2 class="fw-bold mb-0">
                            Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}
                        </h2>
                        <span class="small text-secondary">
                            Kode pesanan: {{ $order?->order_code ?? '-' }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="detail-item">
                                <span>Nama Customer</span>
                                <strong>{{ $order?->customer?->name ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="detail-item">
                                <span>Metode Pembayaran</span>
                                <strong>{{ $payment->method ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="detail-item">
                                <span>Status Pembayaran</span>
                                <strong>{{ $status }}</strong>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="detail-item">
                                <span>Tanggal Pembayaran</span>
                                <strong>
                                    {{ $payment->created_at?->format('d M Y, H:i') ?? '-' }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="detail-item">
                                <span>Catatan Pembayaran</span>
                                <strong class="fw-normal">
                                    {{ $payment->notes ?: 'Tidak ada catatan.' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Proof --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Bukti Pembayaran</h5>
                    <p class="text-secondary small mb-3">
                        Cocokkan nominal dan bukti sebelum memverifikasi transaksi.
                    </p>

                    @if ($payment->proof)
                        <a href="{{ asset('storage/' . $payment->proof) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="d-block text-center bg-light rounded-4 p-3">
                            <img src="{{ asset('storage/' . $payment->proof) }}"
                                 alt="Bukti pembayaran"
                                 class="img-fluid rounded-3 payment-proof">
                        </a>

                        <div class="text-center mt-3">
                            <a href="{{ asset('storage/' . $payment->proof) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn btn-outline-secondary btn-sm rounded-3">
                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                Buka Bukti Ukuran Penuh
                            </a>
                        </div>
                    @else
                        <div class="text-center bg-light rounded-4 py-5 px-3">
                            <i class="bi bi-file-earmark-x fs-1 text-secondary"></i>
                            <h6 class="fw-bold mt-3">Bukti belum tersedia</h6>
                            <p class="text-secondary small mb-0">
                                Customer belum mengunggah bukti pembayaran.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order details --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Informasi Pesanan</h5>
                            <p class="text-secondary small mb-0">
                                Ringkasan tagihan yang terkait.
                            </p>
                        </div>

                        @if ($order)
                            <a href="{{ route('orders.show', $order->id) }}"
                               class="btn btn-outline-secondary btn-sm rounded-3">
                                Lihat Pesanan
                            </a>
                        @endif
                    </div>

                    <div class="detail-item mb-3">
                        <span>Total Tagihan</span>
                        <strong>
                            Rp{{ number_format((float) ($order?->total ?? 0), 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Status Pesanan</span>
                        <strong>{{ $order?->status ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action panel --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 payment-action-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Tindakan Admin</h5>
                    <p class="text-secondary small mb-4">
                        Verifikasi bukti atau tolak jika tidak sesuai.
                    </p>

                    @if ($status === 'Menunggu Verifikasi')
                        @if ($payment->proof)
                            <form action="{{ route('admin.payments.verify', $payment->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin bukti pembayaran ini valid dan ingin memverifikasinya?')">
                                @csrf

                                <button type="submit"
                                        class="btn btn-success w-100 rounded-3 py-2 mb-3">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Verifikasi Pembayaran
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning small rounded-3">
                                Bukti pembayaran belum tersedia. Verifikasi dinonaktifkan.
                            </div>
                        @endif

                        <form action="{{ route('admin.payments.reject', $payment->id) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menolak pembayaran ini?')">
                            @csrf

                            <label for="notes" class="form-label fw-semibold">
                                Alasan Penolakan
                            </label>

                            <textarea name="notes"
                                      id="notes"
                                      rows="3"
                                      class="form-control rounded-3 mb-3"
                                      placeholder="Contoh: nominal tidak sesuai atau bukti tidak jelas"
                                      required
                                      maxlength="1000">{{ old('notes') }}</textarea>

                            <button type="submit"
                                    class="btn btn-outline-danger w-100 rounded-3">
                                <i class="bi bi-x-circle me-2"></i>
                                Tolak Pembayaran
                            </button>
                        </form>
                    @elseif ($status === 'Dibayar')
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            <h6 class="fw-bold mt-3">Pembayaran Terverifikasi</h6>
                            <p class="text-secondary small mb-0">
                                Pembayaran ini sudah berstatus lunas.
                            </p>
                        </div>
                    @elseif ($status === 'Sebagian')
                        <div class="text-center py-3">
                            <i class="bi bi-pie-chart-fill text-info fs-1"></i>
                            <h6 class="fw-bold mt-3">Pembayaran Sebagian</h6>
                            <p class="text-secondary small mb-0">
                                Masih ada sisa tagihan yang perlu dilunasi.
                            </p>
                        </div>
                    @elseif ($status === 'Ditolak')
                        <div class="text-center py-3">
                            <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                            <h6 class="fw-bold mt-3">Pembayaran Ditolak</h6>
                            <p class="text-secondary small mb-0">
                                Customer perlu mengirim bukti pembayaran yang sesuai.
                            </p>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-hourglass text-secondary fs-1"></i>
                            <p class="text-secondary small mt-3 mb-0">
                                Belum ada tindakan yang tersedia untuk status ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-detail-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        background: #e8f5ed;
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .payment-amount-box {
        background: #f5faf7;
        border: 1px solid #e1eee5;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 12px 0;
        border-bottom: 1px solid #edf0ee;
    }

    .detail-item:last-child {
        border-bottom: 0;
    }

    .detail-item span {
        font-size: .82rem;
        color: #6c757d;
    }

    .detail-item strong {
        overflow-wrap: anywhere;
    }

    .payment-proof {
        max-height: 520px;
        object-fit: contain;
    }

    @media (min-width: 1200px) {
        .payment-action-card {
            position: sticky;
            top: 24px;
        }
    }
</style>
@endsection
