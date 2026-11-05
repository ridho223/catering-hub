@extends('layouts.app')

@section('title', 'Paket Catering')

@section('content')
<style>
    :root {
        --cat-primary: #198754;
        --cat-primary-dark: #145c38;
        --cat-primary-light: #eaf5ed;
        --cat-text: #212529;
        --cat-muted: #6c757d;
        --cat-border: #e2e8e3;
        --cat-card-bg: #ffffff;
    }

    .catalog-page {
        background-color: #f8faf8;
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--cat-text);
    }

    .catalog-page a {
        text-decoration: none;
    }

    /* Hero Banner */
    .catalog-hero {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--cat-border);
        border-radius: 20px;
        padding: 28px 36px;
        position: relative;
        overflow: hidden;    
    }

    .catalog-hero-content {
        padding-left: 50px; /* Geser konten teks sedikit ke kanan agar ada jarak */
    }

    .catalog-hero-icon {
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, #d8efdf 0%, #eaf5ed 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: var(--cat-primary);
        margin: 0 auto;
        transform: translateX(-100px)
    }

    .catalog-kicker {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--cat-primary-dark);
        text-transform: uppercase;
    }

    .catalog-hero-title {
        font-size: clamp(1.4rem, 2.5vw, 2.1rem);
        font-weight: 850;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin: 6px 0 10px;
    }

    .catalog-hero-title span {
        color: var(--cat-primary);
    }

    .catalog-hero-desc {
        font-size: 0.88rem;
        color: var(--cat-muted);
        line-height: 1.6;
        max-width: 580px;
        margin-bottom: 0;
    }

    .catalog-btn-main {
        background-color: var(--cat-primary);
        color: #ffffff !important;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .catalog-btn-main:hover {
        background-color: var(--cat-primary-dark);
    }

    .catalog-btn-secondary {
        border: 1px solid var(--cat-border);
        background: #ffffff;
        color: var(--cat-primary-dark) !important;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .catalog-btn-secondary:hover {
        background-color: #edf7ef;
    }

    .catalog-section {
        margin-top: 36px;
    }

    .catalog-heading {
        font-size: 1.45rem;
        font-weight: 850;
        letter-spacing: -0.5px;
        margin: 2px 0 4px;
    }

    .catalog-subtitle {
        color: var(--cat-muted);
        font-size: 0.88rem;
        margin: 0;
    }

    /* Search Bar */
    .catalog-search-input {
        position: relative;
        max-width: 280px;
        width: 100%;
    }

    .catalog-search-input i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--cat-muted);
        font-size: 0.88rem;
    }

    .catalog-search-input input {
        padding: 8px 14px 8px 38px;
        border-radius: 10px;
        border: 1px solid var(--cat-border);
        font-size: 0.85rem;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .catalog-search-input input:focus {
        border-color: var(--cat-primary);
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
        outline: none;
    }

    /* Compact Cards */
    .catalog-card {
        background: var(--cat-card-bg);
        border: 1px solid var(--cat-border);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .catalog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
        border-color: #cde2d3;
    }

    .catalog-card-photo {
        position: relative;
        height: 140px;
        background: #f0f4f1;
        overflow: hidden;
    }

    .catalog-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .catalog-card:hover .catalog-card-photo img {
        transform: scale(1.05);
    }

    .catalog-card-placeholder {
        display: grid;
        place-items: center;
        height: 100%;
        color: #a3b8a8;
        font-size: 2.2rem;
    }

    .catalog-badge-tag {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        padding: 3px 8px;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--cat-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .catalog-card-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .catalog-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        margin-bottom: 4px;
        line-height: 1.3;
        color: var(--cat-text);
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .catalog-card-desc {
        font-size: 0.78rem;
        color: var(--cat-muted);
        line-height: 1.45;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.3em;
    }

    .catalog-menu-section {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px dashed var(--cat-border);
    }

    .catalog-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 10px;
    }

    .catalog-chip-item {
        background-color: var(--cat-primary-light);
        color: var(--cat-primary-dark);
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .catalog-card-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        padding-top: 8px;
        border-top: 1px solid #f0f4f1;
        gap: 8px;
    }

    .catalog-price-label {
        font-size: 0.65rem;
        color: var(--cat-muted);
        display: block;
        margin-bottom: 2px;
    }

    .catalog-price-value {
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--cat-primary-dark);
        line-height: 1.1;
    }

    .catalog-btn-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background-color: var(--cat-primary);
        color: #ffffff !important;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 10px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    .catalog-btn-detail:hover {
        background-color: var(--cat-primary-dark);
    }

    .catalog-empty-state {
        text-align: center;
        padding: 42px 20px;
        background: #ffffff;
        border: 1px dashed var(--cat-border);
        border-radius: 16px;
    }

    .catalog-empty-state i {
        font-size: 2.5rem;
        color: var(--cat-primary);
    }

    .catalog-no-match {
        display: none;
        padding: 24px;
        color: var(--cat-muted);
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .catalog-hero-content {
            padding-left: 0;
        }
    }

    @media (max-width: 575.98px) {
        .catalog-card-photo {
            height: 125px;
        }
        .catalog-card-body {
            padding: 10px;
        }
        .catalog-card-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
        }
        .catalog-btn-detail {
            width: 100%;
        }
    }
</style>

<div class="catalog-page">
    <div class="container">

        {{-- Hero Banner --}}
        <section class="catalog-hero">
            <div class="row align-items-center g-3">
                <div class="col-md-9 col-lg-10 catalog-hero-content">
                    <span class="catalog-kicker">
                        <i class="bi bi-basket2-fill me-1"></i> KEDAI BANGJO · PAKET CATERING
                    </span>

                    <h1 class="catalog-hero-title">
                        Sajian Istimewa untuk <span>Setiap Momen Bersama.</span>
                    </h1>

                    <p class="catalog-hero-desc">
                        Nikmati porsi pas, hidangan higienis, dan cita rasa otentik khas Kedai Bangjo. Pilih paket catering favoritmu untuk acara rapat, syukuran, hingga kumpul keluarga.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="#daftar-paket" class="catalog-btn-main">
                            <i class="bi bi-grid"></i> Lihat Semua Paket
                        </a>
                        <a href="{{ route('customer.prasmanan.index') }}" class="catalog-btn-secondary">
                            <i class="bi bi-people-fill"></i> Paket Prasmanan
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-lg-2 d-none d-md-block text-center">
                    <div class="catalog-hero-icon">
                        <i class="bi bi-basket2-fill"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 mt-3 mb-0">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 mt-3 mb-0">
                {{ session('error') }}
            </div>
        @endif

        {{-- Catalog Section --}}
        <section class="catalog-section" id="daftar-paket">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <span class="catalog-kicker">PILIH SESUAI KEBUTUHAN</span>
                    <h2 class="catalog-heading">Paket Catering</h2>
                    <p class="catalog-subtitle">Lihat isi dan harga sebelum memilih paket.</p>
                </div>

                <div class="catalog-search-input">
                    <i class="bi bi-search"></i>
                    <input type="search" id="catalogSearch" placeholder="Cari nama paket atau menu..." aria-label="Cari paket catering">
                </div>
            </div>

            {{-- Compact Grid Layout --}}
            <div class="row g-2 g-md-3" id="catalogGrid">
                @forelse($packages as $package)
                    @php
                        $menuDetails = $package->details->filter(fn($detail) => $detail->menu);
                        $searchText = $package->name . ' ' . $package->description . ' ' .
                            $menuDetails->map(fn($detail) => $detail->menu->name)->implode(' ');
                    @endphp

                    <div class="col-6 col-md-4 col-lg-3 catalog-item" data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                        <article class="catalog-card">
                            <div class="catalog-card-photo">
                                @if($package->image)
                                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" loading="lazy">
                                @else
                                    <div class="catalog-card-placeholder">
                                        <i class="bi bi-egg-fried"></i>
                                    </div>
                                @endif

                                <span class="catalog-badge-tag">
                                    <i class="bi bi-check-circle-fill"></i> Paket Catering
                                </span>
                            </div>

                            <div class="catalog-card-body">
                                <h3 class="catalog-card-title" title="{{ $package->name }}">{{ $package->name }}</h3>

                                <p class="catalog-card-desc">
                                    {{ $package->description ?: 'Paket catering untuk berbagai kebutuhan acara.' }}
                                </p>

                                <div class="catalog-menu-section">
                                    <div class="catalog-chips-container">
                                        @forelse($menuDetails->take(3) as $detail)
                                            <span class="catalog-chip-item">{{ $detail->menu->name }}</span>
                                        @empty
                                            <span class="text-muted small">Menu belum tersedia</span>
                                        @endforelse

                                        @if($menuDetails->count() > 3)
                                            <span class="catalog-chip-item">+{{ $menuDetails->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="catalog-card-footer">
                                    <div class="catalog-price-box">
                                        <span class="catalog-price-label">Harga / porsi</span>
                                        <div class="catalog-price-value">
                                            Rp{{ number_format($package->price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <a href="{{ route('customer.packages.show', $package) }}" class="catalog-btn-detail">
                                        Detail <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="catalog-empty-state">
                            <i class="bi bi-basket3"></i>
                            <h3 class="fw-bold fs-5 mt-2">Belum ada paket catering</h3>
                            <p class="text-muted small mb-0">Paket yang tersedia akan muncul di halaman ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="catalog-no-match" id="catalogNoMatch">
                <i class="bi bi-search fs-3 d-block mb-2"></i>
                Paket yang kamu cari tidak ditemukan.
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('catalogSearch');
    const items = document.querySelectorAll('.catalog-item');
    const noMatch = document.getElementById('catalogNoMatch');

    if (!search) return;

    search.addEventListener('input', function () {
        const keyword = this.value.trim().toLocaleLowerCase('id-ID');
        let visibleCount = 0;

        items.forEach(function (item) {
            const text = item.dataset.search || '';
            const matched = text.includes(keyword);

            item.style.display = matched ? '' : 'none';

            if (matched) visibleCount++;
        });

        noMatch.style.display = visibleCount === 0 && items.length > 0
            ? 'block'
            : 'none';
    });
});
</script>
@endsection