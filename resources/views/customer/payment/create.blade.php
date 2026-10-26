
@extends('layouts.app')

@section('title', 'Pembayaran Pesanan')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ route('customer.orders.show', $order->id) }}"
           class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke detail pesanan
        </a>

        <div class="mt-3">
            <span class="text-success fw-semibold small">
                <i class="bi bi-shield-check me-1"></i>PEMBAYARAN PESANAN
            </span>
            <h2 class="fw-bold mt-2 mb-1">Selesaikan Pembayaran</h2>
            <p class="text-muted mb-0">
                Periksa total pesanan, pilih metode pembayaran, lalu kirim bukti pembayaran.
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Ada data yang perlu diperbaiki
            </div>
            <ul class="mb-0 ps-4">
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

        $statusPembayaran = $order->payment?->status;

        $sudahMenungguVerifikasi = $statusPembayaran === 'Menunggu Verifikasi';
        $sudahDibayar = $statusPembayaran === 'Dibayar';

        $dapatMengirimPembayaran =
            $pengirimanTerkonfirmasi &&
            !$sudahMenungguVerifikasi &&
            !$sudahDibayar;
    @endphp

    <div class="row g-4">

        {{-- Ringkasan pesanan --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Kode pesanan</div>
                            <div class="fw-bold">{{ $order->order_code }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Ringkasan Pembayaran</h5>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-muted">Tanggal pemesanan</span>
                        <span class="fw-semibold text-end">
                            {{ $order->order_date
                                ? \Carbon\Carbon::parse($order->order_date)->format('d M Y')
                                : '-' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-muted">Tanggal pengiriman</span>
                        <span class="fw-semibold text-end">
                            {{ $order->delivery_date
                                ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y')
                                : 'Belum ditentukan' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-muted">Metode penerimaan</span>
                        <span class="fw-semibold text-end">
                            @if ($order->delivery_method === 'pickup')
                                Ambil sendiri
                            @elseif ($order->delivery_method === 'delivery')
                                Diantar
                            @else
                                Belum ditentukan
                            @endif
                        </span>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                        <span class="text-muted">Total yang harus dibayar</span>
                    </div>

                    <div class="text-success fw-bold mb-3"
                         style="font-size: clamp(1.8rem, 4vw, 2.3rem);">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </div>

                    @if ($order->delivery_method === 'delivery' && !$pengirimanTerkonfirmasi)
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Biaya pengiriman masih menunggu konfirmasi admin. Pembayaran belum dapat dilanjutkan.
                        </div>
                    @else
                        <div class="bg-light rounded-3 p-3">
                            <div class="d-flex gap-2">
                                <i class="bi bi-info-circle text-success"></i>
                                <div class="small text-muted">
                                    Pastikan nominal pembayaran sesuai total yang tertera. Bukti pembayaran akan diperiksa admin.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>Tips pembayaran
                    </h6>

                    <ul class="text-muted small mb-0 ps-3">
                        <li class="mb-2">Pastikan nominal transfer sesuai total pesanan.</li>
                        <li class="mb-2">Gunakan foto atau tangkapan layar bukti transaksi yang jelas.</li>
                        <li>Simpan bukti pembayaran sampai transaksi diverifikasi.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Form pembayaran --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">

                    @if ($sudahDibayar)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 76px; height: 76px;">
                                <i class="bi bi-check-circle-fill fs-1"></i>
                            </div>

                            <h4 class="fw-bold">Pembayaran Sudah Diverifikasi</h4>
                            <p class="text-muted">
                                Pesanan ini sudah berstatus dibayar. Kamu tidak perlu mengirim bukti pembayaran lagi.
                            </p>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-success rounded-pill px-4">
                                Lihat Detail Pesanan
                            </a>
                        </div>

                    @elseif ($sudahMenungguVerifikasi)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 76px; height: 76px;">
                                <i class="bi bi-hourglass-split fs-1"></i>
                            </div>

                            <h4 class="fw-bold">Bukti Sedang Diperiksa</h4>
                            <p class="text-muted">
                                Bukti pembayaranmu sudah dikirim. Tunggu hasil verifikasi admin sebelum mengirim bukti baru.
                            </p>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-success rounded-pill px-4">
                                Lihat Status Pesanan
                            </a>
                        </div>

                    @elseif (!$pengirimanTerkonfirmasi)
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 76px; height: 76px;">
                                <i class="bi bi-truck fs-1"></i>
                            </div>

                            <h4 class="fw-bold">Menunggu Konfirmasi Pengiriman</h4>
                            <p class="text-muted">
                                Admin perlu memastikan jarak dan biaya pengiriman terlebih dahulu. Setelah total final tersedia, pembayaran dapat dilanjutkan.
                            </p>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-outline-success rounded-pill px-4">
                                Kembali ke Detail Pesanan
                            </a>
                        </div>

                    @else
                        <div class="mb-4">
                            <h4 class="fw-bold mb-1">
                                {{ $statusPembayaran === 'Ditolak'
                                    ? 'Kirim Ulang Bukti Pembayaran'
                                    : 'Kirim Bukti Pembayaran' }}
                            </h4>
                            <p class="text-muted mb-0">
                                Isi informasi pembayaran di bawah ini dengan benar.
                            </p>
                        </div>

                        @if ($statusPembayaran === 'Ditolak')
                            <div class="alert alert-danger rounded-3">
                                <div class="fw-semibold">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    Pembayaran sebelumnya ditolak
                                </div>

                                @if ($order->payment?->notes)
                                    <div class="small mt-2">
                                        Alasan penolakan: {{ $order->payment->notes }}
                                    </div>
                                @endif

                                <div class="small mt-2">
                                    Periksa kembali bukti dan nominal pembayaran sebelum mengirim ulang.
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('customer.payment.store', $order->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            {{-- Metode pembayaran --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3">
                                    1. Pilih Metode Pembayaran
                                </label>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <input
                                            class="btn-check"
                                            type="radio"
                                            name="method"
                                            id="transfer"
                                            value="transfer"
                                            autocomplete="off"
                                            {{ old('method') === 'transfer' ? 'checked' : '' }}
                                            required
                                        >

                                        <label for="transfer"
                                               class="payment-method-card d-flex gap-3 align-items-start border rounded-4 p-3 h-100 w-100">
                                            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                 style="width: 46px; height: 46px; flex-shrink: 0;">
                                                <i class="bi bi-bank fs-4"></i>
                                            </div>

                                            <div>
                                                <div class="fw-bold">Transfer Bank</div>
                                                <div class="text-muted small mt-1">
                                                    Transfer ke rekening resmi catering.
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-md-6">
                                        <input
                                            class="btn-check"
                                            type="radio"
                                            name="method"
                                            id="qris"
                                            value="qris"
                                            autocomplete="off"
                                            {{ old('method') === 'qris' ? 'checked' : '' }}
                                            required
                                        >

                                        <label for="qris"
                                               class="payment-method-card d-flex gap-3 align-items-start border rounded-4 p-3 h-100 w-100">
                                            <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                                 style="width: 46px; height: 46px; flex-shrink: 0;">
                                                <i class="bi bi-qr-code fs-4"></i>
                                            </div>

                                            <div>
                                                <div class="fw-bold">QRIS</div>
                                                <div class="text-muted small mt-1">
                                                    Bayar dengan aplikasi yang mendukung QRIS.
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </div>

                                @error('method')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                <div class="form-text mt-2">
                                    Ikuti informasi rekening atau QRIS resmi yang diberikan oleh pihak catering.
                                </div>
                            </div>

                            {{-- Upload bukti --}}
                            <div class="mb-4">
                                <label for="proof" class="form-label fw-semibold">
                                    2. Unggah Bukti Pembayaran
                                </label>

                                <div class="border rounded-4 p-3 p-md-4">
                                    <div class="text-center mb-3">
                                        <i class="bi bi-cloud-arrow-up text-success"
                                           style="font-size: 2.5rem;"></i>

                                        <div class="fw-semibold mt-2">
                                            Pilih gambar bukti pembayaran
                                        </div>

                                        <div class="text-muted small">
                                            JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        name="proof"
                                        id="proof"
                                        class="form-control @error('proof') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        required
                                    >

                                    @error('proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div id="proof-file-name" class="small text-muted mt-2">
                                        Belum ada file dipilih.
                                    </div>

                                    <img id="proof-preview"
                                         src=""
                                         alt="Pratinjau bukti pembayaran"
                                         class="img-fluid rounded-3 mt-3 d-none"
                                         style="max-height: 240px; object-fit: contain;">
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-semibold">
                                    3. Catatan <span class="text-muted fw-normal">(opsional)</span>
                                </label>

                                <textarea
                                    name="notes"
                                    id="notes"
                                    rows="3"
                                    maxlength="1000"
                                    class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Contoh: pembayaran dilakukan atas nama..."
                                >{{ old('notes') }}</textarea>

                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Persetujuan dan tombol --}}
                            <div class="bg-light rounded-3 p-3 mb-4">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-shield-check text-success fs-5"></i>
                                    <div class="small text-muted">
                                        Pastikan bukti yang diunggah sesuai transaksi pesanan ini. Pembayaran baru dianggap berhasil setelah diverifikasi admin.
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                    id="submit-payment"
                                    class="btn btn-success btn-lg w-100 rounded-pill py-3">
                                <i class="bi bi-upload me-2"></i>
                                {{ $statusPembayaran === 'Ditolak'
                                    ? 'Kirim Ulang Bukti Pembayaran'
                                    : 'Kirim Bukti Pembayaran' }}
                            </button>

                            <a href="{{ route('customer.orders.show', $order->id) }}"
                               class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2">
                                Batalkan dan Kembali
                            </a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-method-card {
        cursor: pointer;
        transition: border-color .2s ease, background-color .2s ease;
    }

    .payment-method-card:hover {
        border-color: #198754 !important;
        background-color: #f7fcf8;
    }

    .btn-check:checked + .payment-method-card {
        border-color: #198754 !important;
        background-color: #f0faf3;
        box-shadow: 0 0 0 1px #198754;
    }

    .payment-method-card:focus-within {
        outline: 2px solid #198754;
        outline-offset: 2px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const proofInput = document.getElementById('proof');
        const fileName = document.getElementById('proof-file-name');
        const preview = document.getElementById('proof-preview');
        const form = proofInput ? proofInput.closest('form') : null;
        const submitButton = document.getElementById('submit-payment');

        if (proofInput) {
            proofInput.addEventListener('change', function () {
                const file = this.files && this.files.length ? this.files[0] : null;

                if (!file) {
                    fileName.textContent = 'Belum ada file dipilih.';
                    preview.src = '';
                    preview.classList.add('d-none');
                    return;
                }

                fileName.textContent = file.name;

                if (!file.type.startsWith('image/')) {
                    preview.src = '';
                    preview.classList.add('d-none');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.classList.remove('d-none');
                };

                reader.readAsDataURL(file);
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
@endsection
