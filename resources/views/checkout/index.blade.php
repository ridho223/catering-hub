
@extends('layouts.app')

@section('title', 'Checkout Pesanan')

@section('content')
<div class="checkout-page py-4 py-lg-5">
    <div class="container">

        {{-- HEADER --}}
        <div class="mb-4">
            <a href="{{ route('cart.index') }}"
               class="text-decoration-none text-success small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke keranjang
            </a>

            <h2 class="fw-bold mt-3 mb-2">Checkout Pesanan</h2>

            <p class="text-muted mb-0">
                Lengkapi informasi acara dan pengiriman sebelum membuat pesanan.
            </p>
        </div>

        {{-- PROGRESS --}}
        <div class="checkout-progress mb-4">
            <div class="checkout-step completed">
                <span><i class="bi bi-check-lg"></i></span>
                <small>Keranjang</small>
            </div>
            <div class="progress-line active"></div>
            <div class="checkout-step current">
                <span>2</span>
                <small>Checkout</small>
            </div>
            <div class="progress-line"></div>
            <div class="checkout-step">
                <span>3</span>
                <small>Pesanan dibuat</small>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-3">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3">
                <div class="fw-bold mb-2">
                    Periksa kembali data yang kamu masukkan.
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="row g-4">

                {{-- FORM UTAMA --}}
                <div class="col-lg-7">

                    {{-- DATA PEMBELI --}}
                    <div class="card checkout-card mb-4">
                        <div class="card-body p-4">

                            <div class="section-heading mb-4">
                                <div class="section-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Data Pembeli</h5>
                                    <p class="text-muted small mb-0">
                                        Pastikan informasi kontak kamu benar.
                                    </p>
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        Nama lengkap
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $customer->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Nama penerima"
                                        autocomplete="name"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">
                                        Nomor HP
                                    </label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone', $customer->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="08xxxxxxxxxx"
                                        autocomplete="tel"
                                        required
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $customer->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="nama@email.com"
                                        autocomplete="email"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- PENGIRIMAN --}}
                    <div class="card checkout-card mb-4">
                        <div class="card-body p-4">

                            <div class="section-heading mb-4">
                                <div class="section-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Pengambilan & Pengiriman</h5>
                                    <p class="text-muted small mb-0">
                                        Pilih cara menerima pesanan kamu.
                                    </p>
                                </div>
                            </div>

                            <label for="address" class="form-label fw-semibold">
                                Alamat acara / penerima
                            </label>

                            <textarea
                                name="address"
                                id="address"
                                rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, dan patokan lokasi"
                                autocomplete="street-address"
                                required
                            >{{ old('address', $customer->address) }}</textarea>

                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <div class="form-text mb-4">
                                Masukkan alamat lokasi acara yang benar, bukan hanya alamat kos atau alamat akun jika berbeda.
                            </div>

                            <label class="form-label fw-semibold mb-3">
                                Metode penerimaan
                            </label>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="pickup"
                                           class="delivery-option h-100 w-100">
                                        <input
                                            class="form-check-input delivery-radio"
                                            type="radio"
                                            name="delivery_method"
                                            id="pickup"
                                            value="pickup"
                                            {{ old('delivery_method') === 'pickup' ? 'checked' : '' }}
                                            required
                                        >

                                        <div class="delivery-option-content">
                                            <div class="delivery-symbol">
                                                <i class="bi bi-shop"></i>
                                            </div>

                                            <div class="fw-bold mb-1">
                                                Ambil sendiri
                                            </div>

                                            <p class="small text-muted mb-3">
                                                Ambil pesanan langsung di tempat catering.
                                            </p>

                                            <span class="delivery-price free">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Gratis
                                            </span>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label for="delivery"
                                           class="delivery-option h-100 w-100">
                                        <input
                                            class="form-check-input delivery-radio"
                                            type="radio"
                                            name="delivery_method"
                                            id="delivery"
                                            value="delivery"
                                            {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}
                                            required
                                        >

                                        <div class="delivery-option-content">
                                            <div class="delivery-symbol">
                                                <i class="bi bi-truck"></i>
                                            </div>

                                            <div class="fw-bold mb-1">
                                                Diantar ke alamat
                                            </div>

                                            <p class="small text-muted mb-3">
                                                Pesanan diantar oleh pihak catering.
                                            </p>

                                            <span class="delivery-price free">
                                                Gratis hingga 5 km
                                            </span>
                                        </div>
                                    </label>
                                </div>

                            </div>

                            @error('delivery_method')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                            <div class="delivery-info mt-3">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Jarak lebih dari 5 km akan diperiksa admin.
                                Jika ada ongkir tambahan, kamu perlu menunggu
                                konfirmasi sebelum melakukan pembayaran.
                            </div>

                            <div class="mt-4">
                                <label for="delivery_date"
                                       class="form-label fw-semibold">
                                    Tanggal pesanan dibutuhkan
                                </label>

                                <input
                                    type="date"
                                    name="delivery_date"
                                    id="delivery_date"
                                    value="{{ old('delivery_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="form-control @error('delivery_date') is-invalid @enderror"
                                    required
                                >

                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Pilih tanggal saat pesanan harus siap atau diantarkan.
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="notes" class="form-label fw-semibold">
                                    Catatan pesanan
                                    <span class="text-muted fw-normal">(opsional)</span>
                                </label>

                                <textarea
                                    name="notes"
                                    id="notes"
                                    rows="3"
                                    class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Contoh: sambal dipisah, jangan terlalu pedas, atau patokan lokasi."
                                >{{ old('notes') }}</textarea>

                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="checkout-trust d-flex gap-3 align-items-start">
                        <i class="bi bi-shield-check fs-4 text-success"></i>
                        <div>
                            <div class="fw-semibold">Periksa pesanan sebelum dikirim</div>
                            <p class="text-muted small mb-0">
                                Pastikan tanggal, jumlah, kontak, dan alamat sudah benar
                                agar admin dapat memproses pesanan dengan lancar.
                            </p>
                        </div>
                    </div>

                </div>


                {{-- RINGKASAN --}}
                <div class="col-lg-5">
                    <div class="card checkout-card summary-card">
                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-1">Ringkasan Pesanan</h5>
                            <p class="text-muted small mb-4">
                                Periksa kembali item yang kamu pilih.
                            </p>

                            <div class="summary-items">
                                @forelse ($cart as $item)
                                    <div class="summary-item">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">
                                                {{ $item['name'] }}
                                            </div>

                                            <div class="small text-muted mt-1">
                                                {{ $item['quantity'] }} ×
                                                Rp{{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <div class="fw-semibold text-nowrap">
                                            Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bi bi-cart-x fs-2 text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">
                                            Keranjang kamu kosong.
                                        </p>
                                    </div>
                                @endforelse
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal item</span>
                                <span class="fw-semibold">
                                    Rp{{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Pengiriman</span>
                                <span class="fw-semibold text-success"
                                      id="delivery-label">
                                    Belum dikonfirmasi
                                </span>
                            </div>

                            <div class="total-panel mb-4">
                                <div class="text-muted small mb-1">
                                    Total sementara
                                </div>

                                <div class="total-value">
                                    Rp{{ number_format($total, 0, ',', '.') }}
                                </div>

                                <div class="text-muted small mt-2">
                                    Ongkir tambahan, jika ada, akan dikonfirmasi admin.
                                </div>
                            </div>

                            <button type="submit"
                                    class="btn btn-success btn-lg w-100 rounded-3"
                                    @disabled(count($cart) === 0)>
                                <i class="bi bi-check-circle me-2"></i>
                                Buat Pesanan
                            </button>

                            <a href="{{ route('cart.index') }}"
                               class="btn btn-outline-secondary w-100 rounded-3 mt-2">
                                <i class="bi bi-pencil me-1"></i>
                                Ubah Keranjang
                            </a>

                            <div class="text-center text-muted small mt-3">
                                <i class="bi bi-lock me-1"></i>
                                Pastikan data sudah benar sebelum melanjutkan.
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<style>
    .checkout-page {
        background: #f6f8f7;
        min-height: 70vh;
    }

    .checkout-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 5px 24px rgba(24, 45, 32, .055);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .section-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #e6f4eb;
        color: #198754;
        border-radius: 14px;
        font-size: 1.3rem;
    }

    .checkout-page .form-control {
        border-color: #dfe5e1;
        border-radius: 10px;
        padding: 11px 13px;
    }

    .checkout-page .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .10);
    }

    .delivery-option {
        display: block;
        position: relative;
        cursor: pointer;
        border: 1px solid #dfe5e1;
        border-radius: 14px;
        padding: 17px;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .delivery-radio {
        position: absolute;
        top: 15px;
        right: 15px;
        margin: 0;
        float: none;
    }

    .delivery-option:has(.delivery-radio:checked) {
        border-color: #198754;
        background: #f1faf4;
        box-shadow: 0 0 0 2px rgba(25, 135, 84, .08);
    }

    .delivery-symbol {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #e8f5ec;
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 14px;
    }

    .delivery-price {
        font-size: .8rem;
        font-weight: 700;
    }

    .delivery-price.free {
        color: #198754;
    }

    .delivery-info {
        background: #eef6ff;
        color: #345d80;
        border-radius: 11px;
        padding: 13px;
        font-size: .85rem;
    }

    .summary-card {
        position: sticky;
        top: 100px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding: 15px 0;
        border-bottom: 1px solid #edf0ee;
    }

    .summary-item:first-child {
        padding-top: 0;
    }

    .total-panel {
        padding: 18px;
        border-radius: 13px;
        background: #f0f8f2;
    }

    .total-value {
        color: #198754;
        font-size: 1.65rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .checkout-progress {
        display: flex;
        align-items: flex-start;
        max-width: 520px;
        margin: 0 auto 30px;
    }

    .checkout-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 7px;
        color: #8b9690;
        min-width: 74px;
    }

    .checkout-step span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e5e9e6;
        color: #65716a;
        font-weight: 700;
    }

    .checkout-step small {
        font-size: .75rem;
        text-align: center;
    }

    .checkout-step.completed span,
    .checkout-step.current span {
        background: #198754;
        color: #fff;
    }

    .checkout-step.current {
        color: #198754;
        font-weight: 700;
    }

    .progress-line {
        height: 2px;
        flex: 1;
        margin-top: 16px;
        background: #e0e5e1;
    }

    .progress-line.active {
        background: #198754;
    }

    .checkout-trust {
        padding: 18px;
        background: #fff;
        border-radius: 14px;
        margin-bottom: 24px;
    }

    @media (max-width: 991.98px) {
        .summary-card {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .checkout-page .card-body {
            padding: 20px !important;
        }

        .delivery-option {
            padding: 14px;
        }

        .total-value {
            font-size: 1.4rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll(
        'input[name="delivery_method"]'
    );
    const deliveryLabel = document.getElementById('delivery-label');

    function updateDeliveryLabel() {
        const selected = document.querySelector(
            'input[name="delivery_method"]:checked'
        );

        if (!selected) {
            deliveryLabel.textContent = 'Belum dikonfirmasi';
            deliveryLabel.className = 'fw-semibold text-muted';
            return;
        }

        if (selected.value === 'pickup') {
            deliveryLabel.textContent = 'Gratis';
            deliveryLabel.className = 'fw-semibold text-success';
        } else {
            deliveryLabel.textContent = 'Dikonfirmasi admin';
            deliveryLabel.className = 'fw-semibold text-warning';
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', updateDeliveryLabel);
    });

    updateDeliveryLabel();
});
</script>
@endsection
