@extends('layouts.app')

@section('title', $package->name)

@section('content')
@php
    $isBuffet = $package->package_type === 'prasmanan';
    $menuDetails = $package->details->filter(fn($detail) => $detail->menu);
    $groupedDetails = $menuDetails->groupBy(
        fn($detail) => $detail->menu->category?->name ?? 'Lainnya'
    );
@endphp

<style>
    :root {
        --dp-primary: #198754;
        --dp-primary-dark: #145c38;
        --dp-primary-light: #eaf5ed;
        --dp-text: #212529;
        --dp-muted: #6c757d;
        --dp-border: #e2e8e3;
        --dp-bg: #f8faf8;
        --dp-card-bg: #ffffff;
    }

    .detail-page {
        background-color: var(--dp-bg);
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--dp-text);
    }

    .detail-page a {
        text-decoration: none;
    }

    /* Tombol Kembali yang Ditingkatkan */
    .detail-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        background: #f4f7f5;
        border: 1px solid var(--dp-border);
        color: var(--dp-muted);
        font-size: 0.85rem;
        font-weight: 750;
        transition: all 0.2s ease;
        margin-top: 10px;
    }

    .detail-back-btn:hover {
        background: var(--dp-primary-light);
        color: var(--dp-primary-dark);
        border-color: #cde2d3;
    }

    /* Layout & Cards */
    .detail-photo-card {
        position: relative;
        overflow: hidden;
        height: clamp(260px, 35vw, 380px);
        border-radius: 20px;
        border: 1px solid var(--dp-border);
        background: #f0f4f1;
    }

    .detail-photo-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-photo-placeholder {
        display: grid;
        place-items: center;
        width: 100%;
        height: 100%;
        color: #a3b8a8;
        font-size: 3.8rem;
    }

    .detail-badge-tag {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--dp-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .detail-info-card,
    .detail-menu-card {
        background: var(--dp-card-bg);
        border: 1px solid var(--dp-border);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .detail-type-kicker {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        background: var(--dp-primary-light);
        color: var(--dp-primary-dark);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .detail-title {
        font-size: clamp(1.4rem, 2.8vw, 2.1rem);
        font-weight: 850;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin: 10px 0;
        color: var(--dp-text);
    }

    .detail-desc {
        color: var(--dp-muted);
        font-size: 0.88rem;
        line-height: 1.65;
        margin-bottom: 0;
    }

    /* Mini Stats */
    .detail-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed var(--dp-border);
    }

    .detail-stat-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #fbfcfb;
        border: 1px solid #edf2ee;
    }

    .detail-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--dp-primary-light);
        color: var(--dp-primary);
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .detail-stat-text strong {
        display: block;
        font-size: 0.84rem;
        color: var(--dp-text);
        line-height: 1.2;
    }

    .detail-stat-text span {
        font-size: 0.72rem;
        color: var(--dp-muted);
    }

    /* Section Isi Paket & Filter Kategori */
    .detail-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .detail-menu-title {
        font-size: 1.1rem;
        font-weight: 850;
        margin: 0;
    }

    .detail-menu-subtitle {
        font-size: 0.82rem;
        color: var(--dp-muted);
        margin: 2px 0 0;
    }

    .detail-menu-badge {
        background: var(--dp-primary-light);
        color: var(--dp-primary-dark);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Tab Filter Kategori */
    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed var(--dp-border);
    }

    .category-pill-btn {
        border: 1px solid var(--dp-border);
        background: #fbfcfb;
        color: var(--dp-muted);
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .category-pill-btn:hover {
        background: var(--dp-primary-light);
        color: var(--dp-primary-dark);
    }

    .category-pill-btn.active {
        background: var(--dp-primary-dark);
        color: #ffffff;
        border-color: var(--dp-primary-dark);
    }

    /* Container Wrapper & Gradient Overlay untuk Expand */
    .menu-expand-container {
        position: relative;
        overflow: hidden;
        max-height: 280px;
        transition: max-height 0.35s ease-in-out;
    }

    .menu-expand-container.expanded {
        max-height: 3000px;
    }

    .menu-expand-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 70px;
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .menu-expand-container.expanded .menu-expand-overlay {
        opacity: 0;
    }

    /* Grid 2-3 Kolom Compact */
    .detail-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }

    .detail-menu-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 12px;
        background: #fbfcfb;
        border: 1px solid #eef3ef;
        transition: all 0.2s ease;
    }

    .detail-menu-item:hover {
        border-color: #d2e4d6;
        background: #ffffff;
        transform: translateY(-1px);
    }

    .detail-item-check {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--dp-primary-light);
        color: var(--dp-primary);
        display: grid;
        place-items: center;
        font-size: 0.78rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .detail-item-name {
        font-size: 0.84rem;
        font-weight: 750;
        color: var(--dp-text);
        line-height: 1.3;
    }

    .detail-item-desc {
        font-size: 0.73rem;
        color: var(--dp-muted);
        margin-top: 2px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Tombol Toggle Expand */
    .btn-toggle-expand {
        width: 100%;
        margin-top: 10px;
        padding: 8px 14px;
        border-radius: 10px;
        background: var(--dp-primary-light);
        border: 1px solid #cde2d3;
        color: var(--dp-primary-dark);
        font-size: 0.82rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-toggle-expand:hover {
        background: #d8ebd2;
    }

    /* Sidebar Order Box */
    .detail-order-card {
        background: var(--dp-card-bg);
        border: 1px solid var(--dp-border);
        border-radius: 20px;
        overflow: hidden;
        position: sticky;
        top: 24px;
        box-shadow: 0 8px 24px rgba(20, 92, 56, 0.05);
    }

    .detail-order-header {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        padding: 20px;
        border-bottom: 1px solid var(--dp-border);
    }

    .detail-order-kicker {
        font-size: 0.68rem;
        font-weight: 800;
        color: var(--dp-primary-dark);
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .detail-order-price {
        font-size: 1.8rem;
        font-weight: 900;
        color: var(--dp-primary-dark);
        line-height: 1.2;
        margin-top: 4px;
    }

    .detail-order-unit {
        font-size: 0.78rem;
        color: var(--dp-muted);
    }

    .detail-order-body {
        padding: 20px;
    }

    /* Quantity Stepper */
    .detail-stepper {
        display: flex;
        align-items: center;
        border: 1px solid var(--dp-border);
        border-radius: 10px;
        overflow: hidden;
        max-width: 160px;
        background: #ffffff;
    }

    .detail-stepper button {
        width: 38px;
        height: 38px;
        border: 0;
        background: #f4f7f5;
        color: var(--dp-primary-dark);
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .detail-stepper button:hover {
        background: #e2ede5;
    }

    .detail-stepper input {
        width: 100%;
        height: 38px;
        border: 0;
        outline: none;
        text-align: center;
        font-weight: 800;
        font-size: 0.9rem;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .detail-stepper input::-webkit-inner-spin-button,
    .detail-stepper input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Total Box */
    .detail-total-box {
        background: #f8fbf8;
        border: 1px solid var(--dp-border);
        border-radius: 12px;
        padding: 14px;
        margin-top: 16px;
    }

    .detail-total-title {
        font-size: 0.75rem;
        color: var(--dp-muted);
    }

    .detail-total-value {
        font-size: 1.35rem;
        font-weight: 900;
        color: var(--dp-primary-dark);
        margin-top: 2px;
    }

    /* Buttons */
    .detail-btn-checkout {
        width: 100%;
        padding: 11px;
        border-radius: 10px;
        background: var(--dp-primary);
        border: 1px solid var(--dp-primary);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .detail-btn-checkout:hover {
        background: var(--dp-primary-dark);
        border-color: var(--dp-primary-dark);
    }

    .detail-btn-cart {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--dp-border);
        color: var(--dp-primary-dark);
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .detail-btn-cart:hover {
        background: var(--dp-primary-light);
        border-color: #cde2d3;
    }

    .detail-note-text {
        font-size: 0.75rem;
        color: var(--dp-muted);
        line-height: 1.5;
        margin-top: 14px;
        display: flex;
        align-items: flex-start;
        gap: 6px;
    }

    .detail-empty-box {
        text-align: center;
        padding: 24px 12px;
        color: var(--dp-muted);
        border: 1px dashed var(--dp-border);
        border-radius: 12px;
        font-size: 0.85rem;
    }

    @media (max-width: 991.98px) {
        .detail-order-card {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .detail-photo-card {
            height: 220px;
        }
        .detail-info-card,
        .detail-menu-card {
            padding: 16px;
        }
        .detail-menu-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detail-page">
    <div class="container">

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3">
                <div class="fw-bold mb-1 small">Periksa kembali data berikut:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 g-lg-4">
            {{-- Kolom Kiri: Foto, Deskripsi, & Isi Paket Grid --}}
            <div class="col-lg-7 col-xl-8">
                {{-- Foto Utama --}}
                <div class="detail-photo-card mb-3">
                    @if($package->image)
                        <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}">
                    @else
                        <div class="detail-photo-placeholder">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                    @endif

                    <span class="detail-badge-tag">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ $isBuffet ? 'Paket Prasmanan' : 'Paket Catering' }}
                    </span>
                </div>

                {{-- Deskripsi & Informasi Paket --}}
                <section class="detail-info-card mb-3">
                    <span class="detail-type-kicker">
                        <i class="bi {{ $isBuffet ? 'bi-people' : 'bi-basket2' }}"></i>
                        {{ $isBuffet ? 'PRASMANAN' : 'PAKET CATERING' }}
                    </span>

                    <h1 class="detail-title">{{ $package->name }}</h1>

                    <p class="detail-desc">
                        {{ $package->description ?: 'Pilihan hidangan berkualitas dari Kedai Bangjo untuk merayakan berbagai momen spesial Anda bersama porsi yang pas dan segar.' }}
                    </p>

                    <div class="detail-stats-grid">
                        <div class="detail-stat-box">
                            <div class="detail-stat-icon">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                            <div class="detail-stat-text">
                                <strong>{{ $menuDetails->count() }} Pilihan Menu</strong>
                                <span>Lengkap & variatif</span>
                            </div>
                        </div>

                        <div class="detail-stat-box">
                            <div class="detail-stat-icon">
                                <i class="bi bi-calendar2-check"></i>
                            </div>
                            <div class="detail-stat-text">
                                <strong>Bisa Dijadwalkan</strong>
                                <span>Atur tanggal di checkout</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section Isi Paket dengan Tab Filter & Collapsible Expand --}}
                <section class="detail-menu-card">
                    <div class="detail-menu-header">
                        <div>
                            <h2 class="detail-menu-title">Isi Paket</h2>
                            <p class="detail-menu-subtitle">Daftar hidangan yang sudah termasuk dalam paket ini.</p>
                        </div>
                        <span class="detail-menu-badge">{{ $menuDetails->count() }} Menu</span>
                    </div>

                    @if($menuDetails->count() > 0)
                        {{-- Tab Kategori --}}
                        <div class="category-pills">
                            <button type="button" class="category-pill-btn active" data-category="all">
                                Semua ({{ $menuDetails->count() }})
                            </button>
                            @foreach($groupedDetails as $categoryName => $details)
                                <button type="button" class="category-pill-btn" data-category="{{ \Illuminate\Support\Str::slug($categoryName) }}">
                                    {{ $categoryName }} ({{ $details->count() }})
                                </button>
                            @endforeach
                        </div>

                        {{-- Container Menu dengan Max-Height + Toggle --}}
                        <div class="menu-expand-container" id="menuExpandContainer">
                            <div class="detail-menu-grid" id="menuItemsGrid">
                                @foreach($menuDetails as $detail)
                                    @php
                                        $catSlug = \Illuminate\Support\Str::slug($detail->menu->category?->name ?? 'Lainnya');
                                    @endphp
                                    <div class="detail-menu-item" data-category="{{ $catSlug }}">
                                        <div class="detail-item-check">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="detail-item-name">{{ $detail->menu->name }}</div>
                                            @if($detail->menu->description)
                                                <div class="detail-item-desc" title="{{ $detail->menu->description }}">
                                                    {{ $detail->menu->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="menu-expand-overlay" id="menuOverlay"></div>
                        </div>

                        {{-- Tombol Lihat Selengkapnya (Muncul hanya jika isi menu > 8) --}}
                        @if($menuDetails->count() > 8)
                            <button type="button" class="btn-toggle-expand" id="btnToggleExpand">
                                <span>Lihat Selengkapnya</span>
                                <i class="bi bi-chevron-down" id="expandIcon"></i>
                            </button>
                        @endif
                    @else
                        <div class="detail-empty-box">
                            <i class="bi bi-inbox fs-3 d-block mb-1 text-success"></i>
                            Isi paket belum dikonfigurasi.
                        </div>
                    @endif
                </section>
            </div>

            {{-- Kolom Kanan: Order Box (Sticky Sidecard) --}}
            <div class="col-lg-5 col-xl-4">
                <aside class="detail-order-card">
                    <div class="detail-order-header">
                        <div class="detail-order-kicker">
                            <i class="bi bi-receipt me-1"></i> HARGA PAKET
                        </div>
                        <div class="detail-order-price">
                            Rp{{ number_format($package->price, 0, ',', '.') }}
                        </div>
                        <div class="detail-order-unit">
                            per {{ $isBuffet ? 'orang / porsi' : 'porsi' }}
                        </div>
                    </div>

                    <div class="detail-order-body">
                        <form action="{{ route('cart.add', $package) }}" method="POST" id="packageOrderForm">
                            @csrf

                            <label for="packageQuantity" class="form-label fw-bold small text-dark mb-1">
                                {{ $isBuffet ? 'Jumlah Porsi' : 'Jumlah Porsi' }}
                            </label>

                            <div class="detail-stepper">
                                <button type="button" id="qtyMinus" aria-label="Kurangi jumlah">−</button>
                                <input type="number"
                                       id="packageQuantity"
                                       name="quantity"
                                       min="1"
                                       max="10000"
                                       step="1"
                                       value="{{ old('quantity', 1) }}"
                                       required>
                                <button type="button" id="qtyPlus" aria-label="Tambah jumlah">+</button>
                            </div>

                            @error('quantity')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <div class="detail-total-box">
                                <div class="detail-total-title">Estimasi Total Pembelian</div>
                                <div class="detail-total-value" id="packageTotal">
                                    Rp{{ number_format($package->price, 0, ',', '.') }}
                                </div>
                            </div>

                            <button type="submit" name="redirect" value="checkout" class="detail-btn-checkout">
                                <i class="bi bi-lightning-charge-fill"></i> Pesan & Checkout
                            </button>

                            <button type="submit" name="redirect" value="cart" class="detail-btn-cart">
                                <i class="bi bi-cart-plus"></i> + Keranjang
                            </button>

                            {{-- Tombol Kembali yang Diperbaiki dan Dipercantik --}}
                            <div>
                                <a href="{{ $isBuffet ? route('customer.prasmanan.index') : route('customer.packages.index') }}" class="detail-back-btn">
                                    <i class="bi bi-arrow-left"></i>
                                    Kembali ke {{ $isBuffet ? 'Paket Prasmanan' : 'Paket Catering' }}
                                </a>
                            </div>
                        </form>

                        <div class="detail-note-text">
                            <i class="bi bi-info-circle text-success fs-6"></i>
                            <span>Biaya pengiriman dan jadwal pengantaran akan dikonfirmasi saat proses checkout.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // === Quantity Stepper & Price Calculator ===
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
        totalElement.textContent = 'Rp' + (price * getQuantity()).toLocaleString('id-ID');
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

    // === Filter Tab Kategori & Expand/Collapse ===
    const pillBtns = document.querySelectorAll('.category-pill-btn');
    const menuItems = document.querySelectorAll('.detail-menu-item');
    const expandContainer = document.getElementById('menuExpandContainer');
    const btnToggleExpand = document.getElementById('btnToggleExpand');
    const expandIcon = document.getElementById('expandIcon');

    pillBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            pillBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const category = this.dataset.category;

            menuItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    if (btnToggleExpand && expandContainer) {
        let isExpanded = false;

        btnToggleExpand.addEventListener('click', function () {
            isExpanded = !isExpanded;
            expandContainer.classList.toggle('expanded', isExpanded);

            if (isExpanded) {
                btnToggleExpand.querySelector('span').textContent = 'Ciutkan Menu';
                expandIcon.className = 'bi bi-chevron-up';
            } else {
                btnToggleExpand.querySelector('span').textContent = 'Lihat Selengkapnya';
                expandIcon.className = 'bi bi-chevron-down';
                expandContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }
});
</script>
@endsection