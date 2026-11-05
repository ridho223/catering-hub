@extends('layouts.app')

@section('title', 'Pembayaran Pesanan')

@section('content')
<style>
    :root {
        --pay-primary: #198754;
        --pay-primary-dark: #145c38;
        --pay-primary-light: #eaf5ed;
        --pay-text: #212529;
        --pay-muted: #6c757d;
        --pay-border: #e2e8e3;
        --pay-bg: #f8faf8;
        --pay-card-bg: #ffffff;
    }

    .payment-page {
        background-color: var(--pay-bg);
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--pay-text);
    }

    .payment-page a {
        text-decoration: none;
    }

    .payment-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--pay-muted);
        font-size: 0.82rem;
        font-weight: 700;
        transition: color 0.2s ease;
        margin-bottom: 16px;
    }

    .payment-back-btn:hover {
        color: var(--pay-primary-dark);
    }

    /* Hero Banner Konsisten */
    .payment-hero-card {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--pay-border);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .payment-hero-card h1 {
        color: var(--pay-text) !important;
    }

    .payment-hero-card p {
        color: var(--pay-muted) !important;
    }

    .hero-badge {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--pay-primary-dark);
        text-transform: uppercase;
        background: var(--pay-primary-light);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    .order-code-tag {
        background: var(--pay-primary-light);
        border: 1px solid var(--pay-border);
        padding: 6px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--pay-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Cards Disamakan */
    .payment-card {
        background: var(--pay-card-bg);
        border: 1px solid var(--pay-border);
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .payment-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px dashed var(--pay-border);
    }

    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--pay-primary-light);
        color: var(--pay-primary);
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .section-title {
        font-size: 0.98rem;
        font-weight: 850;
        margin: 0;
        color: var(--pay-text);
    }

    .section-subtitle {
        color: var(--pay-muted);
        font-size: 0.75rem;
        margin: 0;
    }

    /* Info Tile */
    .info-tile {
        height: 100%;
        padding: 10px 12px;
        border: 1px solid var(--pay-border);
        border-radius: 10px;
        background: #fbfcfb;
    }

    .info-label {
        color: var(--pay-muted);
        font-size: 0.68rem;
        font-weight: 750;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .info-value {
        font-size: 0.84rem;
        font-weight: 750;
        color: var(--pay-text);
        overflow-wrap: anywhere;
    }

    /* Payment Method Cards */
    .payment-method-card {
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fbfcfb;
        border: 1px solid var(--pay-border) !important;
        border-radius: 12px !important;
        padding: 14px;
    }

    .payment-method-card:hover {
        border-color: var(--pay-primary) !important;
        background-color: #f4fbf6;
    }

    .btn-check:checked + .payment-method-card {
        border-color: var(--pay-primary) !important;
        background-color: var(--pay-primary-light);
        box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.15);
    }

    /* Modern File Dropzone */
    .upload-dropzone {
        border: 2px dashed var(--pay-border);
        border-radius: 12px;
        background: #fbfcfb;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .upload-dropzone:hover {
        border-color: var(--pay-primary);
        background: #f4fbf6;
    }

    .upload-dropzone input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    /* Total Box */
    .total-panel {
        background: var(--pay-primary-light);
        border: 1px solid #cde2d3;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 14px;
    }

    .total-value {
        color: var(--pay-primary-dark);
        font-size: 1.35rem;
        font-weight: 900;
    }
    /* Input nominal */
    .amt-box { border:1px solid var(--pay-border); border-radius:14px; background:#fbfcfb; padding:14px; }
    .amt-input { display:flex; align-items:center; gap:.6rem; background:#fff; border:2px solid var(--pay-border); border-radius:12px; padding:.5rem .9rem; transition:border-color .15s, box-shadow .15s; }
    .amt-input:focus-within { border-color:var(--pay-primary); box-shadow:0 0 0 4px rgba(25,135,84,.12); }
    .amt-input.invalid { border-color:#dc3545; box-shadow:0 0 0 4px rgba(220,53,69,.1); }
    .amt-input .cur { font-weight:800; color:var(--pay-muted); font-size:1.05rem; }
    .amt-input input { border:0; outline:0; width:100%; background:transparent; font-size:1.7rem; font-weight:900; color:var(--pay-primary-dark); }
    .amt-input input::placeholder { color:#c3cdc6; }
    .amt-chips { display:flex; flex-wrap:wrap; gap:.45rem; margin-top:.75rem; }
    .amt-chip { border:1px solid var(--pay-border); background:#fff; color:var(--pay-text); border-radius:12px; padding:.4rem .85rem; text-align:left; line-height:1.2; cursor:pointer; transition:all .15s; }
    .amt-chip strong { display:block; font-size:.8rem; }
    .amt-chip small { display:block; font-size:.7rem; color:var(--pay-muted); font-weight:600; }
    .amt-chip:hover { border-color:var(--pay-primary); }
    .amt-chip.on { background:var(--pay-primary); border-color:var(--pay-primary); color:#fff; }
    .amt-chip.on small { color:rgba(255,255,255,.85); }
    .amt-bar { display:flex; height:8px; border-radius:6px; overflow:hidden; background:#e6ece7; margin-top:.9rem; }
    .amt-bar i { display:block; height:100%; transition:width .25s; }
    .amt-bar .a { background:var(--pay-primary-dark); }
    .amt-bar .b { background:#5fc08c; }
    .amt-legend { display:flex; justify-content:space-between; gap:.5rem; flex-wrap:wrap; font-size:.75rem; color:var(--pay-muted); margin-top:.45rem; }
    .amt-legend b { color:var(--pay-text); }
    .amt-msg { font-size:.78rem; font-weight:700; margin-top:.5rem; min-height:1.1rem; }
    .amt-msg.err { color:#dc3545; }
    .amt-msg.ok  { color:var(--pay-primary); }
</style>

<div class="payment-page">
    <div class="container" style="max-width: 960px;">

        {{-- HERO BANNER --}}
        <div class="payment-hero-card">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="hero-badge mb-1">
                        <i class="bi bi-shield-check me-1"></i> PEMBAYARAN PESANAN
                    </span>
                    <h1 class="fs-4 fw-black mb-1">Selesaikan Pembayaran</h1>
                    <p class="mb-0 small">
                        Periksa rincian tagihan, pilih metode pembayaran, lalu kirimkan bukti transaksi Anda.
                    </p>
                </div>
                <div class="text-md-end">
                    <div class="small text-muted fw-bold mb-1" style="font-size: 0.7rem;">KODE PESANAN</div>
                    <div class="order-code-tag">
                        <i class="bi bi-receipt text-success"></i> {{ $order->order_code }}
                    </div>
                </div>
            </div>
        </div>

        {{-- NOTIFIKASI FLASH & VALIDASI --}}
        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3 small py-2">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-3 small py-2">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3 small">
                <div class="fw-bold mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Ada data yang perlu diperbaiki:
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $pengirimanTerkonfirmasi =
                $order->delivery_method === 'pickup' ||
                (
                    $order->delivery_method === 'delivery' &&
                    $order->delivery_distance !== null
                );

            $statusPembayaran        = $order->paymentState();
            $sudahMenungguVerifikasi = $order->hasPendingPayment();
            $sudahDibayar            = $order->isFullyPaid();
            $sudahDp                 = $order->paidAmount() > 0;   // DP sudah masuk, tinggal pelunasan
            $min     = (int) round($order->minPayment());
            $max     = (int) round($order->maxPayment());
            $paidPct = (float) $order->total > 0 ? min(100, round($order->paidAmount() / (float) $order->total * 100, 1)) : 0;
            $quick   = collect([40, 50, 75])
                ->map(fn ($p) => ['label' => $p . '%', 'value' => (int) ceil((float) $order->total * $p / 100)])
                ->filter(fn ($q) => $q['value'] >= $min && $q['value'] < $max)
                ->values();
        @endphp

        <div class="row g-3">

            {{-- KOLOM KIRI: RINGKASAN & TIPS --}}
            <div class="col-lg-5">
                <div class="payment-card">
                    <div class="section-heading">
                        <div class="section-icon"><i class="bi bi-calculator"></i></div>
                        <div>
                            <h2 class="section-title">Ringkasan Tagihan</h2>
                            <p class="section-subtitle">Rincian pesanan dan total bayar.</p>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Tgl Pemesanan</div>
                                <div class="info-value">
                                    {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Tgl Pengiriman</div>
                                <div class="info-value text-success">
                                    {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') : 'Belum ditentukan' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-tile">
                                <div class="info-label">Metode Penerimaan</div>
                                <div class="info-value">
                                    @if ($order->delivery_method === 'pickup')
                                        <i class="bi bi-shop text-success me-1"></i> Ambil Sendiri (Di Tempat)
                                    @elseif ($order->delivery_method === 'delivery')
                                        <i class="bi bi-truck text-success me-1"></i> Diantar ke Alamat
                                    @else
                                        Belum ditentukan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($order->delivery_method === 'delivery' && !$pengirimanTerkonfirmasi)
                        <div class="alert alert-info border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-info-circle-fill me-1"></i> Biaya pengiriman masih menunggu konfirmasi admin. Pembayaran belum dapat dilanjutkan.
                        </div>
                    @else
                        <div class="total-panel">
                            <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem;">TOTAL TAGIHAN</div>
                            <div class="total-value">Rp{{ number_format($order->total, 0, ',', '.') }}</div>
                            @if ($order->paidAmount() > 0)
                                <div class="small mt-2">Sudah dibayar: <b class="text-success">Rp{{ number_format($order->paidAmount(), 0, ',', '.') }}</b></div>
                                <div class="small">Sisa tagihan: <b class="text-danger">Rp{{ number_format($order->remainingAmount(), 0, ',', '.') }}</b></div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- TIPS PEMBAYARAN --}}
                <div class="payment-card">
                    <div class="section-heading mb-2">
                        <div class="section-icon" style="background: #fff3cd; color: #995c0b;"><i class="bi bi-lightbulb"></i></div>
                        <div>
                            <h2 class="section-title">Tips Pembayaran</h2>
                            <p class="section-subtitle">Agar proses verifikasi lebih cepat.</p>
                        </div>
                    </div>
                    <ul class="text-muted small mb-0 ps-3" style="font-size: 0.78rem;">
                        <li class="mb-1">Pastikan nominal transfer sama persis dengan nominal yang kamu isi diatas.</li>
                        <li class="mb-1">Unggah bukti transfer atau screenshot QRIS yang jelas.</li>
                        <li>Simpan bukti sampai status berubah menjadi lunas.</li>
                    </ul>
                </div>
            </div>

            {{-- KOLOM KANAN: FORM PEMBAYARAN --}}
            <div class="col-lg-7">
                <div class="payment-card">
                    @if ($sudahDibayar)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 64px; height: 64px;">
                                <i class="bi bi-check-circle-fill fs-2"></i>
                            </div>
                            <h4 class="fw-bold fs-5">Pembayaran Sudah Diverifikasi</h4>
                            <p class="text-muted small mb-3">
                                Pesanan ini sudah lunas. Anda tidak perlu mengirim bukti pembayaran lagi.
                            </p>
                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-success rounded-3 px-4 py-2 fw-bold small" style="background: var(--pay-primary); border-color: var(--pay-primary);">
                                Lihat Detail Pesanan
                            </a>
                        </div>

                    @elseif ($sudahMenungguVerifikasi)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 64px; height: 64px;">
                                <i class="bi bi-hourglass-split fs-2"></i>
                            </div>
                            <h4 class="fw-bold fs-5">Bukti Sedang Diperiksa</h4>
                            <p class="text-muted small mb-3">
                                Bukti pembayaran Anda sudah dikirim. Mohon tunggu proses verifikasi oleh admin.
                            </p>
                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-success rounded-3 px-4 py-2 fw-bold small" style="background: var(--pay-primary); border-color: var(--pay-primary);">
                                Lihat Status Pesanan
                            </a>
                        </div>

                    @elseif (!$pengirimanTerkonfirmasi)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 64px; height: 64px;">
                                <i class="bi bi-truck fs-2"></i>
                            </div>
                            <h4 class="fw-bold fs-5">Menunggu Konfirmasi Pengiriman</h4>
                            <p class="text-muted small mb-3">
                                Admin perlu menghitung biaya pengiriman terlebih dahulu sebelum pembayaran dapat dibuka.
                            </p>
                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-bold small">
                                Kembali ke Detail Pesanan
                            </a>
                        </div>

                    @else
                        <div class="section-heading">
                            <div class="section-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <div>
                                <h2 class="section-title">
                                    {{ $statusPembayaran === 'Ditolak' ? 'Kirim Ulang Bukti Pembayaran' : 'Form Konfirmasi Pembayaran' }}
                                </h2>
                                <p class="section-subtitle">Pilih metode dan unggah bukti transfer.</p>
                            </div>
                        </div>

                        @if ($statusPembayaran === 'Ditolak')
                            <div class="alert alert-danger border-0 rounded-3 small mb-3 py-2">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Pembayaran Sebelumnya Ditolak</div>
                                @if ($order->payment?->notes)
                                    <div>Alasan: {{ $order->payment->notes }}</div>
                                @endif
                            </div>
                        @endif

                        <form action="{{ route('customer.payment.store', $order->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            {{-- Nominal pembayaran --}}
                            <div class="mb-3">
                                <label for="amount-display" class="form-label fw-bold text-dark small mb-2">1. Nominal yang Dibayar</label>

                                <div class="amt-box">
                                    <div class="amt-input" id="amt-input">
                                        <span class="cur">Rp</span>
                                        <input type="text" id="amount-display" inputmode="numeric" autocomplete="off" placeholder="0" required>
                                        <input type="hidden" name="amount" id="amount" value="{{ old('amount', $max) }}">
                                    </div>

                                    <div class="amt-chips">
                                        @foreach ($quick as $q)
                                            <button type="button" class="amt-chip" data-amount="{{ $q['value'] }}">
                                                <strong>{{ $q['label'] }}</strong>
                                                <small>Rp{{ number_format($q['value'], 0, ',', '.') }}</small>
                                            </button>
                                        @endforeach
                                        <button type="button" class="amt-chip" data-amount="{{ $max }}">
                                            <strong>{{ $sudahDp ? 'Lunasi sisa' : 'Bayar lunas' }}</strong>
                                            <small>Rp{{ number_format($max, 0, ',', '.') }}</small>
                                        </button>
                                    </div>

                                    <div class="amt-bar" aria-hidden="true">
                                        <i class="a" style="width: {{ $paidPct }}%"></i>
                                        <i class="b" id="amt-bar-now" style="width: 0%"></i>
                                    </div>
                                    <div class="amt-legend">
                                        <span>Dibayar sekarang <b id="amt-now">Rp0</b></span>
                                        <span>Sisa setelah ini <b id="amt-left">Rp0</b></span>
                                    </div>
                                    <div class="amt-msg" id="amount-msg"></div>
                                </div>

                                <div class="text-muted mt-2" style="font-size: 0.74rem;">
                                    Minimal Rp{{ number_format($min, 0, ',', '.') }}
                                    @unless ($sudahDp) ({{ \App\Models\Order::MIN_PAYMENT_PERCENT }}% dari total pesanan) @endunless
                                    · maksimal Rp{{ number_format($max, 0, ',', '.') }}.
                                </div>
                                @error('amount')
                                    <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small mb-2">2. Pilih Metode Pembayaran</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input class="btn-check" type="radio" name="method" id="transfer" value="transfer"
                                               {{ old('method') === 'transfer' ? 'checked' : '' }} required>
                                        <label for="transfer" class="payment-method-card d-flex gap-2 align-items-center h-100 w-100">
                                            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                                <i class="bi bi-bank"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">Transfer Bank</div>
                                                <div class="text-muted" style="font-size: 0.68rem;">Rekening Catering</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input class="btn-check" type="radio" name="method" id="qris" value="qris"
                                               {{ old('method') === 'qris' ? 'checked' : '' }} required>
                                        <label for="qris" class="payment-method-card d-flex gap-2 align-items-center h-100 w-100">
                                            <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                                <i class="bi bi-qr-code"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">QRIS</div>
                                                <div class="text-muted" style="font-size: 0.68rem;">Scan & Bayar</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('method')
                                    <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Unggah Bukti Modern Dropzone --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small mb-2">3. Unggah Bukti Pembayaran</label>
                                <div class="upload-dropzone">
                                    <input type="file" name="proof" id="proof"
                                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                                    <div id="dropzone-content">
                                        <i class="bi bi-cloud-arrow-up text-success fs-3 mb-1 d-block"></i>
                                        <div class="fw-bold small text-dark" id="proof-file-name">Klik atau seret file gambar ke sini</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Format JPG, PNG, WEBP (Maks. 2MB)</div>
                                    </div>
                                    <img id="proof-preview" src="" alt="Pratinjau"
                                         class="img-fluid rounded-3 mt-2 d-none mx-auto shadow-sm"
                                         style="max-height: 180px; object-fit: contain;">
                                </div>
                                @error('proof')
                                    <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan Opsional --}}
                            <div class="mb-3">
                                <label for="notes" class="form-label fw-bold text-dark small mb-1">
                                    4. Catatan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <textarea name="notes" id="notes" rows="2" maxlength="1000"
                                          class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                          placeholder="Contoh: Transfer atas nama Budi Santoso...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" id="submit-payment"
                                    class="btn btn-success w-100 rounded-3 py-2 fw-bold text-white small"
                                    style="background: var(--pay-primary); border-color: var(--pay-primary);">
                                <i class="bi bi-upload me-1"></i>
                                {{ $statusPembayaran === 'Ditolak' ? 'Kirim Ulang Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}
                            </button>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-outline-secondary w-100 rounded-3 mt-2 py-2 small fw-bold">
                                Batal dan kembali
                            </a>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const proofInput = document.getElementById('proof');
        const fileName = document.getElementById('proof-file-name');
        const preview = document.getElementById('proof-preview');
        const dropzoneContent = document.getElementById('dropzone-content');
        const form = proofInput ? proofInput.closest('form') : null;
        const submitButton = document.getElementById('submit-payment');             

        if (proofInput) {
            proofInput.addEventListener('change', function () {
                const file = this.files && this.files.length ? this.files[0] : null;

                if (!file) {
                    fileName.textContent = 'Klik atau seret file gambar ke sini';
                    preview.src = '';
                    preview.classList.add('d-none');
                    dropzoneContent.classList.remove('d-none');
                    return;
                }

                fileName.textContent = file.name;

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        preview.src = event.target.result;
                        preview.classList.remove('d-none');
                        dropzoneContent.classList.add('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (form && submitButton) {
            form.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim bukti...';
            });
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('amount-display');
    const hidden  = document.getElementById('amount');
    if (display && hidden) {
        const box    = document.getElementById('amt-input');
        const msgEl  = document.getElementById('amount-msg');
        const nowEl  = document.getElementById('amt-now');
        const leftEl = document.getElementById('amt-left');
        const barNow = document.getElementById('amt-bar-now');
        const chips  = document.querySelectorAll('.amt-chip');

        const TOTAL = {{ (int) round($order->total) }};
        const SISA  = {{ (int) round($order->remainingAmount()) }};
        const MIN   = {{ $min }};
        const MAX   = {{ $max }};
        const rp = function (n) { return 'Rp' + Number(n).toLocaleString('id-ID'); };

        function refresh() {
            const v    = parseInt(hidden.value, 10) || 0;
            const left = Math.max(0, SISA - v);

            nowEl.textContent  = rp(v);
            leftEl.textContent = rp(left);
            barNow.style.width = (TOTAL > 0 ? Math.min(100, v / TOTAL * 100) : 0) + '%';

            chips.forEach(function (c) {
                c.classList.toggle('on', parseInt(c.dataset.amount, 10) === v);
            });

            let msg = '', cls = 'ok';
            if (v < MIN)       { msg = 'Nominal minimal ' + rp(MIN) + '.'; cls = 'err'; }
            else if (v > MAX)  { msg = 'Nominal tidak boleh melebihi sisa tagihan ' + rp(MAX) + '.'; cls = 'err'; }
            else if (left < 1) { msg = 'Dengan nominal ini pesanan menjadi lunas.'; }
            else               { msg = 'Sisa ' + rp(left) + ' bisa dilunasi nanti.'; }

            msgEl.textContent = msg;
            msgEl.className   = 'amt-msg ' + cls;
            box.classList.toggle('invalid', cls === 'err');
            display.setCustomValidity(cls === 'err' ? msg : '');
        }

        function setValue(n) {
            n = Math.max(0, parseInt(n, 10) || 0);
            hidden.value  = n;
            display.value = n ? n.toLocaleString('id-ID') : '';
            refresh();
        }

        display.addEventListener('input', function () { setValue(display.value.replace(/\D/g, '')); });
        chips.forEach(function (c) { c.addEventListener('click', function () { setValue(c.dataset.amount); }); });
        setValue(hidden.value);
    }
});
</script>
@endsection