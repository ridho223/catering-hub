@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<style>
    :root {
        --cart-primary: #198754;
        --cart-primary-dark: #145c38;
        --cart-primary-light: #eaf5ed;
        --cart-text: #212529;
        --cart-muted: #6c757d;
        --cart-border: #e2e8e3;
        --cart-bg: #f8faf8;
        --cart-card-bg: #ffffff;
    }

    .cart-page {
        background-color: var(--cart-bg);
        min-height: 85vh;
        padding: 32px 0 64px;
        color: var(--cart-text);
    }

    .cart-page a {
        text-decoration: none;
    }

    /* Hero Banner Konsisten dengan Halaman Paket */
    .cart-hero-card {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--cart-border);
        border-radius: 20px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(20, 92, 56, 0.04);
    }

    .cart-hero-card h1 {
        color: var(--cart-text) !important;
    }

    .cart-hero-card p {
        color: var(--cart-muted) !important;
    }

    .hero-badge {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--cart-primary-dark);
        text-transform: uppercase;
        background: var(--cart-primary-light);
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Cards Lebih Besar dan Nyaman (Konsisten dengan Halaman Paket) */
    .cart-card {
        background: var(--cart-card-bg);
        border: 1px solid var(--cart-border);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(20, 92, 56, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .cart-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 12px 28px rgba(20, 92, 56, 0.08);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px dashed var(--cart-border);
    }

    .section-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--cart-primary-light);
        color: var(--cart-primary);
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .section-title {
        font-size: 1.15rem;
        font-weight: 850;
        margin: 0;
        color: var(--cart-text);
    }

    .section-subtitle {
        color: var(--cart-muted);
        font-size: 0.82rem;
        margin: 0;
    }

    /* Cart Item Row */
    .cart-item-row {
        padding: 18px 0;
        border-bottom: 1px solid var(--cart-border);
        transition: background-color 0.2s ease;
    }

    .cart-item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    /* Quantity Control Input Group */
    .qty-input-group {
        display: flex;
        align-items: center;
        max-width: 120px;
        border: 1px solid var(--cart-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fbfcfb;
    }

    .qty-input-group input {
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 800;
        font-size: 0.95rem;
        width: 55px;
        padding: 6px 0;
    }

    .qty-input-group input:focus {
        outline: none;
        box-shadow: none;
    }

    .qty-btn {
        background: transparent;
        border: none;
        color: var(--cart-primary);
        font-weight: 800;
        padding: 6px 12px;
        transition: background 0.2s ease;
    }

    .qty-btn:hover {
        background: var(--cart-primary-light);
    }

    /* Tombol Aksi Kustom sesuai Permintaan */
    .detail-btn-checkout {
        background: var(--cart-primary);
        border: 1px solid var(--cart-primary);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 12px 20px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }

    .detail-btn-checkout:hover {
        background: var(--cart-primary-dark);
        border-color: var(--cart-primary-dark);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .detail-btn-cart {
        background: #ffffff;
        border: 1px solid var(--cart-border);
        color: var(--cart-text);
        font-weight: 800;
        font-size: 0.95rem;
        padding: 12px 20px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        transition: all 0.2s ease;
        margin-top: 10px;
    }

    .detail-btn-cart:hover {
        background: var(--cart-primary-light);
        color: var(--cart-primary-dark);
        border-color: var(--cart-primary);
    }

    .detail-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--cart-muted);
        font-size: 0.88rem;
        font-weight: 750;
        transition: color 0.2s ease;
    }

    .detail-back-btn:hover {
        color: var(--cart-primary-dark);
    }

    /* Total Panel */
    .total-panel {
        background: var(--cart-primary-light);
        border: 1px solid #cde2d3;
        border-radius: 14px;
        padding: 16px 20px;
        margin-top: 18px;
    }

    .total-value {
        color: var(--cart-primary-dark);
        font-size: 1.5rem;
        font-weight: 900;
    }
</style>

<div class="cart-page">
    <div class="container" style="max-width: 1080px;">

        {{-- HEADER HERO BANNER --}}
        <div class="cart-hero-card">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="hero-badge mb-2">
                        <i class="bi bi-basket2 me-1"></i> BELANJA CATERING
                    </span>
                    <h1 class="fs-3 fw-black mb-1">Keranjang Saya</h1>
                    <p class="mb-0">
                        Periksa menu dan jumlah porsi pesanan Anda dengan teliti sebelum melanjutkan.
                    </p>
                </div>
                <div>
                    <a href="{{ route('customer.packages.index') }}"
                       class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold small bg-white">
                        <i class="bi bi-arrow-left me-1"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3 py-2 small">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-3 py-2 small">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3 small">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(count($cart) > 0)

            @php
                $total = 0;
                $totalItem = 0;

                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                    $totalItem += $item['quantity'];
                }
            @endphp

            <div class="row g-4">

                {{-- DAFTAR MENU (KOLOM KIRI) --}}
                <div class="col-lg-8">
                    <div class="cart-card">
                        <div class="section-heading">
                            <div class="section-icon"><i class="bi bi-list-check"></i></div>
                            <div>
                                <h2 class="section-title">Menu Pilihan Anda</h2>
                                <p class="section-subtitle">{{ count($cart) }} jenis menu dalam keranjang pesanan.</p>
                            </div>
                        </div>

                        <div>
                            @foreach($cart as $key => $item)
                                @php
                                    $subtotal = $item['price'] * $item['quantity'];
                                @endphp

                                <div class="cart-item-row">
                                    <div class="row align-items-center g-3">
                                        {{-- GAMBAR MENU --}}
                                        <div class="col-3 col-sm-2">
                                            @if(!empty($item['image']))
                                                <img src="{{ asset('storage/' . $item['image']) }}"
                                                     alt="{{ $item['name'] }}"
                                                     class="w-100 rounded-3 shadow-sm"
                                                     style="height: 75px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                                                     style="height: 75px;">
                                                    <i class="bi bi-image text-secondary fs-4"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- NAMA & HARGA SATUAN --}}
                                        <div class="col-9 col-sm-4">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;" title="{{ $item['name'] }}">
                                                {{ $item['name'] }}
                                            </div>
                                            <div class="text-success fw-bold mt-1" style="font-size: 0.85rem;">
                                                Rp{{ number_format($item['price'], 0, ',', '.') }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ porsi</span>
                                            </div>
                                        </div>

                                        {{-- INPUT JUMLAH --}}
                                        <div class="col-7 col-sm-3">
                                            <form action="{{ route('cart.update', $key) }}"
                                                  method="POST"
                                                  class="d-flex align-items-center gap-2">
                                                @csrf
                                                @method('PUT')

                                                <div class="qty-input-group">
                                                    <input type="number"
                                                           name="quantity"
                                                           value="{{ $item['quantity'] }}"
                                                           min="1"
                                                           aria-label="Jumlah {{ $item['name'] }}"
                                                           required>
                                                    <button type="submit" class="qty-btn" title="Simpan jumlah" aria-label="Simpan jumlah">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        {{-- SUBTOTAL & TOMBOL HAPUS --}}
                                        <div class="col-5 col-sm-3 text-end">
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                                Rp{{ number_format($subtotal, 0, ',', '.') }}
                                            </div>

                                            <form action="{{ route('cart.remove', $key) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus {{ addslashes($item['name']) }} dari keranjang?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-link text-danger p-0 fw-bold"
                                                        style="font-size: 0.78rem; text-decoration: none;">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- KOSONGKAN KERANJANG --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-light">
                            <span class="text-muted small" style="font-size: 0.78rem;">
                                <i class="bi bi-info-circle me-1"></i> Klik ikon centang pada jumlah untuk memperbarui porsi.
                            </span>

                            <form action="{{ route('cart.clear') }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin mengosongkan seluruh keranjang?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                                    <i class="bi bi-trash me-1"></i> Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RINGKASAN BELANJA (KOLOM KANAN) --}}
                <div class="col-lg-4">
                    <div class="cart-card sticky-lg-top" style="top: 24px;">
                        <div class="section-heading">
                            <div class="section-icon"><i class="bi bi-receipt"></i></div>
                            <div>
                                <h2 class="section-title">Ringkasan Belanja</h2>
                                <p class="section-subtitle">Total estimasi pembayaran.</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Jenis Menu</span>
                            <span class="fw-bold text-dark">{{ count($cart) }} jenis</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Total Porsi</span>
                            <span class="fw-bold text-dark">{{ number_format($totalItem, 0, ',', '.') }} porsi</span>
                        </div>

                        <div class="total-panel">
                            <div class="text-muted small fw-bold mb-1" style="font-size: 0.72rem;">SUBTOTAL ESTIMASI</div>
                            <div class="total-value">
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="text-muted small my-3" style="font-size: 0.78rem; line-height: 1.5;">
                            <i class="bi bi-shield-check text-success me-1"></i> Biaya pengiriman akan dihitung dan dikonfirmasi pada tahap checkout.
                        </div>

                        {{-- TOMBOL AKSI SESUAI PERMINTAAN --}}
                        <div class="mt-4">
                            <a href="{{ route('checkout.index') }}" class="detail-btn-checkout">
                                <i class="bi bi-lightning-charge-fill"></i> Pesan & Checkout
                            </a>

                            <a href="{{ route('customer.packages.index') }}" class="detail-btn-cart">
                                <i class="bi bi-cart-plus"></i> + Tambah Menu Lain
                            </a>
                        </div>

                        <div class="mb-3 mt-4 text-center">
                            <a href="{{ route('customer.packages.index') }}" class="detail-back-btn">
                                <i class="bi bi-arrow-left"></i>
                                Kembali ke Paket Catering
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        @else

            {{-- KETIKA KERANJANG KOSONG --}}
            <div class="cart-card text-center py-5">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-4 shadow-sm"
                     style="width: 84px; height: 84px;">
                    <i class="bi bi-cart3 fs-1"></i>
                </div>
                <h4 class="fw-bold fs-4 mb-2">Keranjang Belanja Masih Kosong</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 400px; font-size: 0.9rem;">
                    Belum ada menu catering yang Anda pilih. Silakan jelajahi daftar menu kami terlebih dahulu.
                </p>
                <a href="{{ route('customer.packages.index') }}"
                   class="btn btn-success rounded-3 px-4 py-2 fw-bold text-white shadow-sm"
                   style="background: var(--cart-primary); border-color: var(--cart-primary);">
                    <i class="bi bi-grid me-1"></i> Lihat Paket Catering
                </a>
            </div>

        @endif

    </div>
</div>
@endsection