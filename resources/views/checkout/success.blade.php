@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')

@php
    $adaBukti    = $order->payments->isNotEmpty();
    $statusBayar = $order->paymentState();
    $menunggu    = $order->pendingAmount();
    $belumBayar  = max(0, $order->remainingAmount() - $menunggu);
    $bisaBayar   = $order->delivery_method === 'pickup' || $order->delivery_distance !== null;
@endphp

<style>
    :root {
        --suc-primary: #198754;
        --suc-primary-dark: #145c38;
        --suc-primary-light: #eaf5ed;
        --suc-text: #212529;
        --suc-muted: #6c757d;
        --suc-border: #e2e8e3;
        --suc-bg: #f8faf8;
        --suc-card-bg: #ffffff;
    }

    .success-page {
        background-color: var(--suc-bg);
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--suc-text);
    }

    .success-page a {
        text-decoration: none;
    }

    /* Top Hero Banner Screen Konsisten dengan Halaman Lain */
    .success-hero-card {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--suc-border);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .success-hero-card h1 {
        color: var(--suc-text) !important;
    }

    .success-hero-card p {
        color: var(--suc-muted) !important;
    }

    .success-check-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--suc-primary-light);
        color: var(--suc-primary);
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .order-code-badge {
        background: var(--suc-primary-light);
        border: 1px solid var(--suc-border);
        padding: 4px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        display: inline-block;
        color: var(--suc-primary-dark);
    }

    .success-card {
        background: var(--suc-card-bg);
        border: 1px solid var(--suc-border);
        border-radius: 16px;
        padding: 16px 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .success-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
    }

    .detail-grid-box {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .detail-item {
        background: #fbfcfb;
        border: 1px solid var(--suc-border);
        padding: 8px 12px;
        border-radius: 10px;
    }

    .detail-label {
        font-size: 0.68rem;
        color: var(--suc-muted);
        text-transform: uppercase;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .detail-val {
        font-size: 0.84rem;
        font-weight: 750;
        color: var(--suc-text);
    }

    /* Digital Receipt Style (Screen) Disamakan */
    .receipt-card {
        background: var(--suc-card-bg);
        border: 1px solid var(--suc-border);
        border-radius: 16px;
        padding: 16px 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .receipt-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
    }

    .receipt-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px dashed var(--suc-border);
        font-size: 0.84rem;
    }

    .receipt-line:last-child {
        border-bottom: 0;
    }

    .receipt-total-box {
        background: var(--suc-primary-light);
        border: 1px solid #cde2d3;
        border-radius: 10px;
        padding: 10px 12px;
        margin-top: 14px;
    }

    /* Action Buttons */
    .btn-act-primary {
        background: var(--suc-primary);
        border: 1px solid var(--suc-primary);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 10px 14px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-act-primary:hover {
        background: var(--suc-primary-dark);
        color: #ffffff;
    }

    .btn-act-outline {
        background: #ffffff;
        border: 1px solid var(--suc-border);
        color: var(--suc-text);
        font-weight: 750;
        font-size: 0.88rem;
        padding: 10px 14px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-act-outline:hover {
        background: var(--suc-primary-light);
        color: var(--suc-primary-dark);
        border-color: var(--suc-primary);
    }

    /* Element khusus cetak disembunyikan di layar */
    .print-only-receipt {
        display: none;
    }

    /* ===================================================
        CSS CETAK STRUK KHUSUS (PASTI 1 HALAMAN PAS)
        =================================================== */
    @media print {
        @page {
            size: portrait;
            margin: 5mm;
        }

        html, body {
            height: 100% !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        /* Sembunyikan seluruh tampilan website */
        body * {
            visibility: hidden !important;
        }

        /* Hanya tampilkan container struk print khusus */
        .print-only-receipt, .print-only-receipt * {
            visibility: visible !important;
        }

        .print-only-receipt {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            padding: 15px !important;
            color: #000000 !important;
            font-family: Arial, sans-serif !important;
        }

        .print-header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .print-title {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .print-subtitle {
            font-size: 8.5pt;
            margin-top: 2px;
        }

        .print-code {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .print-table-info {
            width: 100%;
            font-size: 8.5pt;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .print-table-info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .print-divider {
            border-top: 1px dashed #000000;
            margin: 6px 0;
        }

        .print-items-table {
            width: 100%;
            font-size: 8.5pt;
            border-collapse: collapse;
        }

        .print-items-table th {
            border-bottom: 1px solid #000000;
            text-align: left;
            padding: 3px 0;
            font-size: 8pt;
            text-transform: uppercase;
        }

        .print-items-table td {
            padding: 4px 0;
            border-bottom: 1px dashed #e0e0e0;
        }

        .print-total-section {
            margin-top: 8px;
            border-top: 1.5px solid #000000;
            padding-top: 6px;
            font-size: 8.5pt;
        }

        .print-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .print-grand-total {
            font-size: 10.5pt;
            font-weight: bold;
            border-top: 1px dashed #000000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .print-footer-note {
            text-align: center;
            font-size: 7.5pt;
            margin-top: 12px;
            border-top: 1px dashed #000000;
            padding-top: 6px;
        }
    }
</style>

<div class="success-page">
    <div class="container" style="max-width: 960px;">

        {{-- TOP HERO BANNER (LAYAR WEB) --}}
        <div class="success-hero-card mb-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="success-check-circle">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-2 mb-1" style="font-size: 0.7rem;">
                            {{ $adaBukti ? 'PEMBAYARAN TERKIRIM' : 'PESANAN DITERIMA' }}
                        </span>
                        <h1 class="fs-4 fw-black mb-1">
                            {{ $adaBukti ? 'Pembayaran Berhasil Dikirim!' : 'Terima Kasih Atas Pesanan Anda!' }}
                        </h1>
                        <p class="mb-0 small">
                            {{ $adaBukti ? 'Admin akan memverifikasi bukti pembayaranmu, lalu pesanan diproses.' : 'Pesanan berhasil dicatat. Tim kami siap memproses pesanan Anda.' }}
                        </p>
                    </div>
                </div>

                <div class="text-md-end flex-shrink-0">
                    <div class="small text-muted fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">KODE PESANAN</div>
                    <div class="order-code-badge">
                        {{ $order->order_code }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TAMPILAN WEB 2 KOLOM --}}
        <div class="row g-3">

            {{-- KOLOM KIRI: STATUS & DATA PEMESAN --}}
            <div class="col-lg-5">

                {{-- STATUS BOX --}}
                <div class="success-card mb-3">
                    <div class="fw-bold text-dark mb-2 small d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-success"></i> Status Pesanan
                    </div>
                    <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-warning fs-5 ms-1"></i>
                        <div>
                            <div class="fw-bold small text-dark">{{ $order->status ?? 'Menunggu Konfirmasi Admin' }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Jadwal & pengiriman sedang diverifikasi.</div>
                        </div>
                    </div>
                </div>

                {{-- STATUS PEMBAYARAN --}}
                <div class="success-card mb-3">
                    <div class="fw-bold text-dark mb-2 small d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card text-success"></i> Status Pembayaran
                    </div>

                    <div class="small mb-2">
                        <div class="d-flex justify-content-between"><span class="text-muted">Total tagihan</span><b>Rp{{ number_format($order->total, 0, ',', '.') }}</b></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Terverifikasi</span><b class="text-success">Rp{{ number_format($order->paidAmount(), 0, ',', '.') }}</b></div>
                        @if ($menunggu > 0)
                            <div class="d-flex justify-content-between"><span class="text-muted">Menunggu verifikasi</span><b style="color:#8a5a00;">Rp{{ number_format($menunggu, 0, ',', '.') }}</b></div>
                        @endif
                        <div class="d-flex justify-content-between"><span class="text-muted">Belum dibayar</span><b class="text-danger">Rp{{ number_format($belumBayar, 0, ',', '.') }}</b></div>
                    </div>

                    @if ($statusBayar === 'Dibayar')
                        <div class="alert alert-success border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Pesanan sudah lunas.
                        </div>
                    @elseif ($order->hasPendingPayment())
                        <div class="alert alert-warning border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-hourglass-split me-1"></i> Bukti pembayaran terkirim dan menunggu verifikasi admin.
                            @if ($belumBayar > 0)
                                Sisa Rp{{ number_format($belumBayar, 0, ',', '.') }} bisa dilunasi setelah itu.
                            @endif
                        </div>
                    @elseif ($belumBayar > 0 && $bisaBayar)
                        <a href="{{ route('customer.payment.create', $order->id) }}" class="btn-act-primary w-100">
                            <i class="bi bi-credit-card"></i> {{ $adaBukti ? 'Bayar Pelunasan' : 'Bayar Sekarang' }}
                        </a>
                    @elseif ($belumBayar > 0)
                        <div class="alert alert-info border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-truck me-1"></i> Menunggu admin mengonfirmasi ongkir sebelum kamu bisa membayar.
                        </div>
                    @endif
                </div>

                {{-- DATA PEMESAN & PENGIRIMAN --}}
                <div class="success-card mb-3">
                    <div class="fw-bold text-dark mb-3 small d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-success"></i> Informasi Pemesan
                    </div>

                    <div class="detail-grid-box">
                        <div class="detail-item">
                            <div class="detail-label">Nama Pemesan</div>
                            <div class="detail-val text-truncate" title="{{ $order->customer->name ?? '-' }}">
                                {{ $order->customer->name ?? '-' }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">WhatsApp</div>
                            <div class="detail-val">{{ $order->customer->phone ?? '-' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tgl Dibuat</div>
                            <div class="detail-val">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tgl Dibutuhkan</div>
                            <div class="detail-val text-success">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</div>
                        </div>

                        <div class="detail-item" style="grid-column: span 2 / span 2;">
                            <div class="detail-label">Metode Penerimaan</div>
                            <div class="detail-val">
                                @if ($order->delivery_method === 'pickup')
                                    <i class="bi bi-shop text-success me-1"></i> Ambil Sendiri (Di Tempat)
                                @elseif ($order->delivery_method === 'delivery')
                                    <i class="bi bi-truck text-success me-1"></i> Diantar ke Alamat
                                @else
                                    Belum ditentukan
                                @endif
                            </div>
                        </div>

                        @if($order->address)
                            <div class="detail-item" style="grid-column: span 2 / span 2;">
                                <div class="detail-label">Alamat Pengiriman</div>
                                <div class="detail-val small">{{ $order->address }}</div>
                            </div>
                        @endif

                        @if($order->notes)
                            <div class="detail-item" style="grid-column: span 2 / span 2;">
                                <div class="detail-label">Catatan Pesanan</div>
                                <div class="detail-val small text-muted">"{{ $order->notes }}"</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TOMBOL AKSI LAYAR --}}
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('customer.packages.index') }}" class="btn-act-primary flex-grow-1">
                        <i class="bi bi-house-door-fill"></i> Beranda
                    </a>
                    <button onclick="window.print()" class="btn-act-outline">
                        <i class="bi bi-printer"></i> Cetak Struk
                    </button>
                    <a href="{{ route('customer.orders.show', $order->id) }}" class="btn-act-outline">
                        <i class="bi bi-receipt"></i> Detail
                    </a>
                </div>

            </div>

            {{-- KOLOM KANAN: RINCIAN ITEM (TAMPILAN WEB) --}}
            <div class="col-lg-7">
                <div class="receipt-card">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">Rincian Item</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Daftar menu & paket yang dipesan</div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">Nota Digital</span>
                    </div>

                    {{-- MENU TANPA HARGA SATUAN --}}
                    <div class="mb-2">
                        @forelse ($order->packages as $orderPackage)
                            <div class="receipt-line">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark small text-truncate">
                                        {{ $orderPackage->package->name ?? 'Paket Catering' }}
                                    </div>
                                </div>
                                <div class="badge bg-success-subtle text-success fw-bold">
                                    {{ $orderPackage->quantity }} Porsi
                                </div>
                            </div>
                        @empty
                        @endforelse

                        @forelse ($order->details as $detail)
                            <div class="receipt-line">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark small text-truncate">
                                        {{ $detail->menu->name ?? 'Menu Satuan' }}
                                    </div>
                                </div>
                                <div class="badge bg-success-subtle text-success fw-bold">
                                    {{ $detail->quantity }} Porsi
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    {{-- TOTAL RINCIAN HARGA --}}
                    <div class="receipt-total-box">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-bold">Subtotal Pesanan</span>
                            <span class="fw-bold text-dark">
                                Rp{{ number_format($order->total_price ?? $order->total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.78rem;">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="text-success fw-bold">
                                {{ $order->delivery_method === 'pickup' ? 'Gratis (Ambil Sendiri)' : 'Sesuai Konfirmasi Admin' }}
                            </span>
                        </div>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-baseline">
                            <span class="fw-extrabold text-dark" style="font-size: 0.9rem;">TOTAL ESTIMASI</span>
                            <span class="fw-black text-success fs-5">
                                Rp{{ number_format($order->total_price ?? $order->total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-center mt-3 pt-2 border-top">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-check text-success me-1"></i> Simpan tangkapan layar atau cetak nota ini sebagai bukti pemesanan.
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- CONTAINER STRUK KHUSUS UNTUK HANYA DICETAK 1 HALAMAN FISIK --}}
{{-- ========================================================== --}}
<div class="print-only-receipt">
    <div class="print-header">
        <div class="print-title">KEDAI BANGJO CATERING</div>
        <div class="print-subtitle">STRUK BUKTI PEMESANAN & PENGAMBILAN RESMI</div>
        <div class="print-code">NO. PESANAN: {{ $order->order_code }}</div>
    </div>

    <table class="print-table-info">
        <tr>
            <td style="width: 50%;"><strong>Nama Pemesan:</strong> {{ $order->customer->name ?? '-' }}</td>
            <td style="width: 50%;"><strong>Tgl Pemesanan:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>No. WhatsApp:</strong> {{ $order->customer->phone ?? '-' }}</td>
            <td><strong>Tgl Dibutuhkan:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Metode Penerimaan:</strong>
                {{ $order->delivery_method === 'pickup' ? 'AMBIL SENDIRI (DI TEMPAT)' : 'DIANTAR KE ALAMAT' }}
            </td>
        </tr>
        @if($order->address)
        <tr>
            <td colspan="2"><strong>Alamat Pengiriman:</strong> {{ $order->address }}</td>
        </tr>
        @endif
        @if($order->notes)
        <tr>
            <td colspan="2"><strong>Catatan:</strong> {{ $order->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="print-divider"></div>

    <table class="print-items-table">
        <thead>
            <tr>
                <th>Nama Menu / Paket</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->packages as $orderPackage)
                <tr>
                    <td>
                        <strong>{{ $orderPackage->package->name ?? 'Paket Catering' }}</strong>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $orderPackage->quantity }} Porsi
                    </td>
                </tr>
            @empty
            @endforelse

            @forelse ($order->details as $detail)
                <tr>
                    <td>
                        <strong>{{ $detail->menu->name ?? 'Menu Satuan' }}</strong>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $detail->quantity }} Porsi
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>

    <div class="print-total-section">
        <div class="print-total-row">
            <span>Subtotal Item:</span>
            <span>Rp{{ number_format($order->total_price ?? $order->total ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="print-total-row">
            <span>Ongkos Kirim:</span>
            <span>{{ $order->delivery_method === 'pickup' ? 'Gratis (Ambil Sendiri)' : 'Sesuai Konfirmasi Admin' }}</span>
        </div>
        <div class="print-total-row print-grand-total">
            <span>TOTAL ESTIMASI:</span>
            <span>Rp{{ number_format($order->total_price ?? $order->total ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="print-footer-note">
        <strong>*** TERIMA KASIH ***</strong><br>
        Harap tunjukkan struk ini kepada petugas saat mengambil atau menerima pesanan.<br>
        Kedai Bangjo Catering
    </div>
</div>
@endsection