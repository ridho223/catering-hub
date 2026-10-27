
@extends('layouts.app')

@section('title', $package->name)

@section('content')
<style>
    .package-page {
        min-height: 80vh;
        background: #f6f8f6;
        color: #253129;
    }

    .package-breadcrumb {
        color: #718077;
        text-decoration: none;
        font-size: .9rem;
    }

    .package-breadcrumb:hover {
        color: #198754;
    }

    .package-photo {
        height: 390px;
        overflow: hidden;
        border-radius: 20px;
        background: #e8f0e9;
    }

    .package-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .package-photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #83a18a;
        font-size: 4rem;
    }

    .package-card {
        background: #fff;
        border: 1px solid #e7ece8;
        border-radius: 18px;
    }

    .package-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #eaf6ed;
        color: #187447;
        font-size: .8rem;
        font-weight: 700;
    }

    .package-price-box {
        padding: 18px;
        border: 1px solid #e1eee4;
        border-radius: 14px;
        background: #f5faf6;
    }

    .package-menu-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #edf1ed;
    }

    .package-menu-row:last-child {
        border-bottom: 0;
    }

    .package-menu-icon {
        display: flex;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eaf6ed;
        color: #198754;
    }

    .package-order-card {
        position: sticky;
        top: 90px;
    }

    .package-quantity {
        display: flex;
        width: 150px;
        overflow: hidden;
        align-items: center;
        border: 1px solid #dce5dd;
        border-radius: 12px;
    }

    .package-quantity button {
        width: 43px;
        height: 43px;
        border: 0;
        background: #f1f6f2;
        color: #187447;
        font-size: 1.2rem;
    }

    .package-quantity input {
        width: 64px;
        min-width: 0;
        height: 43px;
        border: 0;
        outline: none;
        text-align: center;
        font-weight: 700;
    }

    .package-benefit {
        height: 100%;
        padding: 16px;
        border: 1px solid #e7ece8;
        border-radius: 13px;
        background: #fff;
    }

    @media(max-width: 991px) {
        .package-order-card {
            position: static;
        }
    }

    @media(max-width: 767px) {
        .package-photo {
            height: 260px;
        }
    }
</style>

<div class="package-page py-4 py-lg-5">
    <div class="container">

        {{-- NAVIGASI --}}
        <div class="mb-4">
            <a href="{{ route('customer.packages.index') }}"
               class="package-breadcrumb">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali ke daftar paket
            </a>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <div class="fw-semibold mb-1">Periksa kembali data berikut:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 align-items-start">

            {{-- KOLOM KIRI --}}
            <div class="col-lg-7">

                {{-- FOTO PAKET --}}
                <div class="package-photo shadow-sm">
                    @if($package->image)
                        <img
                            src="{{ asset('storage/' . $package->image) }}"
                            alt="{{ $package->name }}">
                    @else
                        <div class="package-photo-placeholder">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                    @endif
                </div>

                {{-- KEUNGGULAN --}}
                <div class="row g-3 mt-1 mb-4">
                    <div class="col-6">
                        <div class="package-benefit">
                            <i class="bi bi-clipboard-check fs-4 text-success"></i>
                            <div class="fw-bold mt-2">Isi paket jelas</div>
                            <small class="text-muted">
                                Lihat menu yang termasuk dalam paket.
                            </small>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="package-benefit">
                            <i class="bi bi-people fs-4 text-success"></i>
                            <div class="fw-bold mt-2">Porsi fleksibel</div>
                            <small class="text-muted">
                                Tentukan jumlah sesuai kebutuhan acara.
                            </small>
                        </div>
                    </div>
                </div>

                
                <div class="package-card package-order-card shadow-sm p-4">

                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">
                            Harga paket per porsi
                        </small>

                        <div class="d-flex align-items-baseline flex-wrap gap-2">
                            <span class="display-6 fw-bold text-success">
                                Rp{{ number_format($package->price, 0, ',', '.') }}
                            </span>
                            <span class="text-muted">/ porsi</span>
                        </div>

                        <small class="text-muted">
                            Total menyesuaikan jumlah porsi yang dipilih.
                        </small>
                    </div>

                    <hr>

                    {{-- FORM JUMLAH --}}
                    <form action="{{ route('cart.add', $package) }}"
                          method="POST"
                          id="packageOrderForm">
                        @csrf

                        <label for="packageQuantity" class="form-label fw-bold">
                            Berapa porsi yang dibutuhkan?
                        </label>

                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">

                            <div class="package-quantity">
                                <button type="button" id="qtyMinus"
                                        aria-label="Kurangi jumlah">−</button>

                                <input
                                    type="number"
                                    id="packageQuantity"
                                    name="quantity"
                                    min="1"
                                    max="10000"
                                    value="{{ old('quantity', 1) }}"
                                    required>

                                <button type="button" id="qtyPlus"
                                        aria-label="Tambah jumlah">+</button>
                            </div>

                            <div class="text-end">
                                <small class="text-muted d-block">
                                    Estimasi total
                                </small>
                                <strong id="packageTotal"
                                        class="fs-5 text-success">
                                    Rp{{ number_format($package->price, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        @error('quantity')
                            <div class="text-danger small mb-3">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="alert alert-light border rounded-3 small">
                            <i class="bi bi-info-circle text-success me-1"></i>
                            Biaya pengiriman, jika ada, dihitung terpisah saat pemesanan.
                        </div>

                        {{-- PESAN LANGSUNG --}}
                        <button type="submit"
                                name="redirect"
                                value="checkout"
                                class="btn btn-success btn-lg w-100 rounded-3">
                            <i class="bi bi-lightning-charge me-2"></i>
                            Pesan dan Lanjut Checkout
                        </button>

                        {{-- TAMBAH KE KERANJANG --}}
                        <button type="submit"
                                name="redirect"
                                value="cart"
                                class="btn btn-outline-success w-100 rounded-3 mt-2">
                            <i class="bi bi-cart-plus me-2"></i>
                            Tambahkan ke Keranjang
                        </button>

                    </form>

                    <div class="text-center text-muted small mt-3">
                        <i class="bi bi-shield-check me-1"></i>
                        Pesanan akan diproses setelah detailnya dikonfirmasi.
                    </div>

                </div>



            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-lg-5">

                                {{-- DETAIL PAKET --}}
                <div class="package-card p-4">
                    <span class="package-label mb-3">
                        <i class="bi bi-box-seam"></i>
                        Paket Catering
                    </span>

                    <h1 class="h2 fw-bold mb-3">
                        {{ $package->name }}
                    </h1>

                    <p class="text-secondary mb-0">
                        {{ $package->description ?: 'Paket catering untuk kebutuhan acara keluarga, kantor, dan berbagai kegiatan lainnya.' }}
                    </p>

                    <hr class="my-4">

                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                        <h5 class="fw-bold mb-0">Isi paket</h5>
                        <span class="badge bg-light text-secondary border">
                            {{ $package->details->whereNotNull('menu')->count() }} jenis menu
                        </span>
                    </div>

                    @forelse($package->details as $detail)
                        @if($detail->menu)
                            <div class="package-menu-row">
                                <div class="package-menu-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="fw-semibold">
                                        {{ $detail->menu->name }}
                                    </div>

                                    @if($detail->menu->category)
                                        <small class="text-success">
                                            {{ $detail->menu->category->name }}
                                        </small>
                                    @endif

                                    @if($detail->menu->description)
                                        <p class="text-muted small mb-0 mt-1">
                                            {{ $detail->menu->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-2 text-muted"></i>
                            <p class="text-muted mb-0 mt-2">
                                Isi paket belum ditambahkan oleh admin.
                            </p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityInput = document.getElementById('packageQuantity');
    const totalElement = document.getElementById('packageTotal');
    const minusButton = document.getElementById('qtyMinus');
    const plusButton = document.getElementById('qtyPlus');

    const price = Number(@json((float) $package->price));
    const maxQuantity = 10000;

    function getQuantity() {
        const quantity = Number.parseInt(quantityInput.value, 10);

        if (!Number.isFinite(quantity) || quantity < 1) {
            return 1;
        }

        return Math.min(quantity, maxQuantity);
    }

    function updateTotal() {
        const quantity = getQuantity();

        totalElement.textContent =
            'Rp' + (price * quantity).toLocaleString('id-ID');
    }

    minusButton.addEventListener('click', function () {
        quantityInput.value = Math.max(1, getQuantity() - 1);
        updateTotal();
    });

    plusButton.addEventListener('click', function () {
        quantityInput.value = Math.min(maxQuantity, getQuantity() + 1);
        updateTotal();
    });

    quantityInput.addEventListener('input', updateTotal);
    quantityInput.addEventListener('change', function () {
        quantityInput.value = getQuantity();
        updateTotal();
    });

    updateTotal();
});
</script>
@endsection
