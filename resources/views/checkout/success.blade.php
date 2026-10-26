
@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="container py-5">

    <div class="order-success-wrap mx-auto">

        {{-- SUCCESS HEADER --}}
        <div class="text-center mb-4">
            <div class="success-check mx-auto mb-3">
                <i class="bi bi-check-lg"></i>
            </div>

            <span class="badge rounded-pill text-bg-success mb-3">
                Pesanan berhasil dibuat
            </span>

            <h2 class="fw-bold mb-2">Terima kasih sudah memesan!</h2>

            <p class="text-muted mb-0">
                Pesanan kamu sudah tercatat. Simpan kode pesanan
                untuk memudahkan pengecekan status.
            </p>
        </div>

        {{-- ORDER CODE --}}
        <div class="card success-card mb-4">
            <div class="card-body p-4 text-center">
                <div class="text-muted small mb-2">KODE PESANAN</div>

                <div class="order-code">
                    {{ $order->order_code }}
                </div>

                <div class="small text-muted mt-2">
                    Dibuat pada
                    {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        {{-- STATUS --}}
        <div class="card success-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Status pesanan</h5>

                <div class="d-flex align-items-start gap-3">
                    <div class="status-symbol">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $order->status }}</div>

                        <p class="text-muted small mb-0 mt-1">
                            Pantau halaman detail pesanan untuk melihat
                            status pembayaran dan konfirmasi pengiriman.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFORMASI --}}
        <div class="card success-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Informasi pesanan</h5>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="detail-label">Nama pemesan</div>
                        <div class="fw-semibold">
                            {{ $order->customer->name ?? '-' }}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="detail-label">Nomor HP</div>
                        <div class="fw-semibold">
                            {{ $order->customer->phone ?? '-' }}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="detail-label">Tanggal pesanan</div>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="detail-label">Tanggal dibutuhkan</div>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="detail-label">Metode penerimaan</div>
                        <div class="fw-semibold">
                            @if ($order->delivery_method === 'pickup')
                                <i class="bi bi-shop text-success me-1"></i>
                                Ambil sendiri
                            @elseif ($order->delivery_method === 'delivery')
                                <i class="bi bi-truck text-success me-1"></i>
                                Diantar ke alamat
                            @else
                                Belum ditentukan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ITEM PESANAN --}}
        <div class="card success-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Rincian item</h5>

                @forelse ($order->packages as $orderPackage)
                    <div class="order-line">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $orderPackage->package->name ?? 'Paket catering' }}
                            </div>

                            <div class="text-muted small mt-1">
                                {{ $orderPackage->quantity }} paket ×
                                Rp{{ number_format($orderPackage->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="fw-semibold text-nowrap">
                            Rp{{ number_format($orderPackage->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                @endforelse

                @forelse ($order->details as $detail)
                    <div class="order-line">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $detail->menu->name ?? 'Menu tambahan' }}
                            </div>

                            <div class="text-muted small mt-1">
                                {{ $detail->quantity }} porsi ×
                                Rp{{ number_format($detail->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="fw-semibold text-nowrap">
                            Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                @endforelse

                @if ($order->packages->isEmpty() && $order->details->isEmpty())
                    <p class="text-muted small mb-0">
                        Rincian item belum tersedia.
                    </p>
                @endif

                <hr class="my-4">

                <div class="d-flex justify-content-between gap-3 mb-2">
                    <span class="text-muted">Subtotal item</span>
                    <span class="fw-semibold">
                        Rp{{ number_format(
                            $order->packages->sum('subtotal')
                            + $order->details->sum('subtotal'),
                            0, ',', '.'
                        ) }}
                    </span>
                </div>

                <div class="d-flex justify-content-between gap-3 mb-3">
                    <span class="text-muted">Biaya pengiriman</span>
                    <span class="fw-semibold">
                        @if ($order->delivery_method === 'pickup')
                            Gratis
                        @elseif ($order->delivery_distance === null)
                            Menunggu konfirmasi
                        @else
                            Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                        @endif
                    </span>
                </div>

                <div class="total-order">
                    <span>Total pesanan</span>
                    <strong>
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </strong>
                </div>

                @if ($order->notes)
                    <div class="notes-box mt-4">
                        <div class="detail-label mb-1">Catatan pesanan</div>
                        {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- NEXT STEP --}}
        <div class="next-step-box mb-4">
            <i class="bi bi-info-circle-fill text-success fs-5"></i>
            <div>
                <div class="fw-bold mb-1">Langkah berikutnya</div>
                <p class="text-muted small mb-0">
                    Buka detail pesanan untuk mengecek konfirmasi ongkir
                    dan melanjutkan pembayaran jika pesanan sudah siap dibayar.
                </p>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="d-grid gap-2">
            <a href="{{ route('customer.orders.show', $order->id) }}"
               class="btn btn-success btn-lg rounded-3">
                <i class="bi bi-receipt me-2"></i>
                Lihat Detail & Pembayaran
            </a>

            <a href="{{ route('customer.orders.index') }}"
               class="btn btn-outline-success rounded-3">
                Lihat Semua Pesanan
            </a>

            <a href="{{ route('home') }}"
               class="btn btn-link text-decoration-none text-muted">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

<style>
    .order-success-wrap {
        max-width: 760px;
    }

    .success-check {
        width: 78px;
        height: 78px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e3f5e9;
        color: #198754;
        font-size: 2.6rem;
    }

    .success-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 5px 24px rgba(24, 45, 32, .055);
    }

    .order-code {
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 800;
        color: #198754;
        overflow-wrap: anywhere;
    }

    .status-symbol {
        width: 44px;
        height: 44px;
        min-width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff3cd;
        color: #946c00;
        border-radius: 13px;
        font-size: 1.2rem;
    }

    .detail-label {
        color: #7a8580;
        font-size: .82rem;
        margin-bottom: 5px;
    }

    .order-line {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 15px 0;
        border-bottom: 1px solid #edf0ee;
    }

    .total-order {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .total-order strong {
        color: #198754;
        font-size: 1.35rem;
        overflow-wrap: anywhere;
    }

    .notes-box {
        background: #f6f8f7;
        border-radius: 12px;
        padding: 14px;
        overflow-wrap: anywhere;
    }

    .next-step-box {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        background: #eef8f1;
        padding: 18px;
        border-radius: 14px;
    }
</style>
@endsection
