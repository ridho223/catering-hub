@extends('layouts.app')

@section('title', 'Paket Prasmanan')

@section('content')
<style>
    :root {
        --bc-primary: #198754;
        --bc-primary-dark: #145c38;
        --bc-primary-light: #eaf5ed;
        --bc-text: #212529;
        --bc-muted: #6c757d;
        --bc-border: #e2e8e3;
        --bc-card-bg: #ffffff;
    }

    .buffet-catalog {
        background-color: #f8faf8;
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--bc-text);
    }

    .buffet-catalog a {
        text-decoration: none;
    }

    /* Hero Banner */
    .bc-hero {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--bc-border);
        border-radius: 20px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
    }

    .bc-hero-icon {
        width: 110px;
        height: 110px;
        background: radial-gradient(circle, #d8efdf 0%, #eaf5ed 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.8rem;
        color: var(--bc-primary);
        margin: 0 auto;
    }

    .bc-kicker {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--bc-primary-dark);
        text-transform: uppercase;
    }

    .bc-hero-title {
        font-size: clamp(1.4rem, 2.5vw, 2.1rem);
        font-weight: 850;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin: 6px 0 10px;
    }

    .bc-hero-title span {
        color: var(--bc-primary);
    }

    .bc-hero-desc {
        font-size: 0.88rem;
        color: var(--bc-muted);
        line-height: 1.6;
        max-width: 580px;
        margin-bottom: 0;
    }

    .bc-btn-main {
        background-color: var(--bc-primary);
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

    .bc-btn-main:hover {
        background-color: var(--bc-primary-dark);
    }

    .bc-btn-secondary {
        border: 1px solid var(--bc-border);
        background: #ffffff;
        color: var(--bc-primary-dark) !important;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .bc-btn-secondary:hover {
        background-color: #edf7ef;
    }

    .bc-section {
        margin-top: 36px;
    }

    .bc-heading {
        font-size: 1.45rem;
        font-weight: 850;
        letter-spacing: -0.5px;
        margin: 2px 0 4px;
    }

    .bc-subtitle {
        color: var(--bc-muted);
        font-size: 0.88rem;
        margin: 0;
    }

    /* Search Bar */
    .bc-search-input {
        position: relative;
        max-width: 280px;
        width: 100%;
    }

    .bc-search-input i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--bc-muted);
        font-size: 0.88rem;
    }

    .bc-search-input input {
        padding: 8px 14px 8px 38px;
        border-radius: 10px;
        border: 1px solid var(--bc-border);
        font-size: 0.85rem;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .bc-search-input input:focus {
        border-color: var(--bc-primary);
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
        outline: none;
    }

    /* Compact Modern Cards */
    .bc-card {
        background: var(--bc-card-bg);
        border: 1px solid var(--bc-border);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .bc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
        border-color: #cde2d3;
    }

    .bc-card-photo {
        position: relative;
        height: 140px;
        background: #f0f4f1;
        overflow: hidden;
    }

    .bc-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .bc-card:hover .bc-card-photo img {
        transform: scale(1.05);
    }

    .bc-card-placeholder {
        display: grid;
        place-items: center;
        height: 100%;
        color: #a3b8a8;
        font-size: 2.2rem;
    }

    .bc-badge-tag {
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
        color: var(--bc-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .bc-card-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .bc-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        margin-bottom: 4px;
        line-height: 1.3;
        color: var(--bc-text);
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .bc-card-desc {
        font-size: 0.78rem;
        color: var(--bc-muted);
        line-height: 1.45;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.3em;
    }

    .bc-menu-section {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px dashed var(--bc-border);
    }

    .bc-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 10px;
    }

    .bc-chip-item {
        background-color: var(--bc-primary-light);
        color: var(--bc-primary-dark);
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .bc-card-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        padding-top: 8px;
        border-top: 1px solid #f0f4f1;
        gap: 8px;
    }

    .bc-price-label {
        font-size: 0.65rem;
        color: var(--bc-muted);
        display: block;
        margin-bottom: 2px;
    }

    .bc-price-value {
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--bc-primary-dark);
        line-height: 1.1;
    }

    .bc-btn-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background-color: var(--bc-primary);
        color: #ffffff !important;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 10px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    .bc-btn-detail:hover {
        background-color: var(--bc-primary-dark);
    }

    .bc-empty-state {
        text-align: center;
        padding: 42px 20px;
        background: #ffffff;
        border: 1px dashed var(--bc-border);
        border-radius: 16px;
    }

    .bc-empty-state i {
        font-size: 2.5rem;
        color: var(--bc-primary);
    }

    .bc-no-match {
        display: none;
        padding: 24px;
        color: var(--bc-muted);
        text-align: center;
    }

    @media (max-width: 575.98px) {
        .bc-card-photo {
            height: 125px;
        }
        .bc-card-body {
            padding: 10px;
        }
        .bc-card-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
        }
        .bc-btn-detail {
            width: 100%;
        }
    }
</style>

<div class="buffet-catalog">
    <div class="container">

        {{-- Hero Banner --}}
        <section class="bc-hero">
            <div class="row align-items-center g-3">
                <div class="col-md-3 col-lg-2 d-none d-md-block text-center">
                    <div class="bc-hero-icon">
                        <i class="bi bi-shop-window"></i>
                    </div>
                </div>

                <div class="col-md-9 col-lg-10">
                    <span class="bc-kicker">
                        <i class="bi bi-people-fill me-1"></i> KEDAI BANGJO · PRASMANAN
                    </span>

                    <h1 class="bc-hero-title">
                        Hidangan untuk ramai orang, <span>lebih praktis.</span>
                    </h1>

                    <p class="bc-hero-desc">
                        Pilih paket prasmanan untuk acara keluarga, pertemuan, atau kegiatan lainnya. Hitung susunan menu dan porsi sebelum memesan.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="#daftar-prasmanan" class="bc-btn-main">
                            <i class="bi bi-grid"></i> Lihat Paket Prasmanan
                        </a>
                        <a href="{{ route('customer.packages.index') }}" class="bc-btn-secondary">
                            <i class="bi bi-basket2"></i> Paket Catering
                        </a>
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
        <section class="bc-section" id="daftar-prasmanan">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <span class="bc-kicker">PILIHAN HIDANGAN</span>
                    <h2 class="bc-heading">Paket Prasmanan</h2>
                    <p class="bc-subtitle">Bandingkan menu dan harga sesuai kebutuhan acaramu.</p>
                </div>

                <div class="bc-search-input">
                    <i class="bi bi-search"></i>
                    <input type="search" id="buffetSearch" placeholder="Cari paket atau menu..." aria-label="Cari paket prasmanan">
                </div>
            </div>

            {{-- Compact Grid Layout (4 Columns on Large Screens) --}}
            <div class="row g-2 g-md-3" id="buffetGrid">
                @forelse($packages as $package)
                    @php
                        $menuDetails = $package->details->filter(fn($detail) => $detail->menu);
                        $searchText = $package->name . ' ' . $package->description . ' ' .
                            $menuDetails->map(fn($detail) => $detail->menu->name)->implode(' ');
                    @endphp

                    <div class="col-6 col-md-4 col-lg-3 bc-item" data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                        <article class="bc-card">
                            <div class="bc-card-photo">
                                @if($package->image)
                                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" loading="lazy">
                                @else
                                    <div class="bc-card-placeholder">
                                        <i class="bi bi-egg-fried"></i>
                                    </div>
                                @endif

                                <span class="bc-badge-tag">
                                    <i class="bi bi-check-circle-fill"></i> Prasmanan
                                </span>
                            </div>

                            <div class="bc-card-body">
                                <h3 class="bc-card-title" title="{{ $package->name }}">{{ $package->name }}</h3>

                                <p class="bc-card-desc">
                                    {{ $package->description ?: 'Pilihan hidangan prasmanan untuk berbagai acara.' }}
                                </p>

                                <div class="bc-menu-section">
                                    <div class="bc-chips-container">
                                        @forelse($menuDetails->take(3) as $detail)
                                            <span class="bc-chip-item">{{ $detail->menu->name }}</span>
                                        @empty
                                            <span class="text-muted small">Menu belum tersedia</span>
                                        @endforelse

                                        @if($menuDetails->count() > 3)
                                            <span class="bc-chip-item">+{{ $menuDetails->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="bc-card-footer">
                                    <div class="bc-price-box">
                                        <span class="bc-price-label">Harga / porsi</span>
                                        <div class="bc-price-value">
                                            Rp{{ number_format($package->price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <a href="{{ route('customer.prasmanan.show', $package) }}" class="bc-btn-detail">
                                        Detail <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bc-empty-state">
                            <i class="bi bi-egg-fried"></i>
                            <h3 class="fw-bold fs-5 mt-2">Belum ada paket prasmanan</h3>
                            <p class="text-muted small mb-0">Paket prasmanan yang tersedia akan muncul di sini.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="bc-no-match" id="buffetNoMatch">
                <i class="bi bi-search fs-3 d-block mb-2"></i>
                Paket prasmanan yang kamu cari tidak ditemukan.
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('buffetSearch');
    const items = document.querySelectorAll('.bc-item');
    const noMatch = document.getElementById('buffetNoMatch');

    if (!search) return;

    search.addEventListener('input', function () {
        const keyword = this.value.trim().toLocaleLowerCase('id-ID');
        let visibleCount = 0;

        items.forEach(function (item) {
            const matched = (item.dataset.search || '').includes(keyword);
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