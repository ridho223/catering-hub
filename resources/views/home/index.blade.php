@extends('layouts.app')

@section('title', 'Kedai BangJo Catering | Sajian Istimewa untuk Setiap Momen')

@section('content')
<style>
    .home-bangjo {
        --green: #198754;
        --green-dark: #126640;
        --green-light: #eaf5ed;
        --green-soft: #f4fbf6;
        --text: #1e2922;
        --muted: #64746b;
        color: var(--text);
        background: #ffffff;
        overflow-x: hidden;
    }

    .home-bangjo a {
        text-decoration: none;
    }

    /* MODERN HERO SECTION */
    .bj-hero {
        position: relative;
        padding: 90px 0 100px;
        background: radial-gradient(circle at 90% 10%, #e1f3e5 0, transparent 40%),
                    linear-gradient(135deg, #f8faf8 0%, #edf6ef 100%);
    }

    .bj-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border: 1px solid #cde2d3;
        border-radius: 50px;
        background: #ffffff;
        color: var(--green-dark);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.06);
        margin-bottom: 20px;
    }

    .bj-title {
        font-size: clamp(2.5rem, 4.8vw, 4.2rem);
        line-height: 1.15;
        letter-spacing: -1.2px;
        font-weight: 900;
        color: var(--text);
        margin-bottom: 20px;
    }

    .bj-title span {
        color: var(--green);
        position: relative;
    }

    .bj-description {
        color: var(--muted);
        font-size: 1.05rem;
        line-height: 1.75;
        margin-bottom: 30px;
        max-width: 560px;
    }

    /* Buttons Modern */
    .bj-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 26px;
        background: var(--green);
        border: 1px solid var(--green);
        color: #ffffff !important;
        font-weight: 800;
        font-size: 0.95rem;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(25, 135, 84, 0.25);
        transition: all 0.25s ease;
    }

    .bj-btn-primary:hover {
        background: var(--green-dark);
        border-color: var(--green-dark);
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(25, 135, 84, 0.35);
    }

    .bj-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 24px;
        background: #ffffff;
        border: 1px solid #d2e4d6;
        color: var(--green-dark) !important;
        font-weight: 800;
        font-size: 0.95rem;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
    }

    .bj-btn-secondary:hover {
        background: var(--green-light);
        border-color: var(--green);
        transform: translateY(-2px);
    }

    /* Hero Visual Card */
    .bj-hero-visual-wrapper {
        position: relative;
    }

    .bj-hero-card-img {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(20, 92, 56, 0.12);
        border: 4px solid #ffffff;
        background: #ffffff;
    }

    .bj-hero-card-img img {
        width: 100%;
        height: 440px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .bj-hero-card-img:hover img {
        transform: scale(1.03);
    }

    .bj-floating-badge {
        position: absolute;
        bottom: -20px;
        left: 24px;
        background: #ffffff;
        border: 1px solid #e2e8e3;
        padding: 16px 20px;
        border-radius: 18px;
        box-shadow: 0 15px 35px rgba(20, 92, 56, 0.1);
        display: flex;
        align-items: center;
        gap: 14px;
        max-width: 280px;
        z-index: 2;
    }

    .bj-floating-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--green-light);
        color: var(--green);
        display: grid;
        place-items: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* STATS STRIP */
    .bj-stats-strip {
        background: var(--green);
        color: #ffffff;
        padding: 30px 0;
        border-radius: 20px;
        margin-top: -45px;
        position: relative;
        z-index: 10;
        box-shadow: 0 15px 35px rgba(25, 135, 84, 0.2);
    }

    .bj-stat-item {
        text-align: center;
        padding: 10px;
    }

    .bj-stat-number {
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 2px;
    }

    .bj-stat-label {
        font-size: 0.82rem;
        font-weight: 700;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* SECTION STYLING */
    .bj-section {
        padding: 90px 0;
    }

    .bj-section-soft {
        background: #f8faf8;
    }

    .bj-section-header {
        max-width: 650px;
        margin: 0 auto 50px;
        text-align: center;
    }

    .bj-badge-label {
        color: var(--green);
        font-size: 0.75rem;
        font-weight: 850;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        background: var(--green-light);
        padding: 6px 14px;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 12px;
    }

    .bj-section-title {
        font-size: clamp(2rem, 3.2vw, 2.6rem);
        font-weight: 900;
        letter-spacing: -0.8px;
        color: var(--text);
        margin-bottom: 14px;
    }

    .bj-section-desc {
        color: var(--muted);
        font-size: 0.98rem;
        line-height: 1.7;
        margin: 0;
    }

    /* MODERN CATEGORY CARDS */
    .bj-category-card {
        background: #ffffff;
        border: 1px solid #e2e8e3;
        border-radius: 24px;
        padding: 36px;
        height: 100%;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02);
    }

    .bj-category-card:hover {
        transform: translateY(-6px);
        border-color: #cde2d3;
        box-shadow: 0 16px 36px rgba(25, 92, 56, 0.08);
    }

    .bj-category-icon-box {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        background: var(--green-light);
        color: var(--green);
        display: grid;
        place-items: center;
        font-size: 2rem;
        margin-bottom: 24px;
        transition: transform 0.3s ease;
    }

    .bj-category-card:hover .bj-category-icon-box {
        transform: scale(1.08) rotate(-3deg);
    }

    .bj-category-card h3 {
        font-size: 1.4rem;
        font-weight: 850;
        color: var(--text);
        margin-bottom: 10px;
    }

    .bj-category-card p {
        color: var(--muted);
        font-size: 0.92rem;
        line-height: 1.7;
        margin-bottom: 24px;
    }

    .bj-link-arrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--green-dark);
        font-weight: 800;
        font-size: 0.92rem;
        transition: gap 0.2s ease;
    }

    .bj-category-card:hover .bj-link-arrow {
        gap: 12px;
    }

    /* MODERN FEATURE / BENEFITS CARDS */
    .bj-feature-card {
        background: #ffffff;
        border: 1px solid #e2e8e3;
        border-radius: 20px;
        padding: 30px;
        height: 100%;
        transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .bj-feature-card:hover {
        border-color: #cde2d3;
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(25, 92, 56, 0.06);
    }

    .bj-feature-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: var(--green-light);
        color: var(--green);
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .bj-feature-card h4 {
        font-size: 1.1rem;
        font-weight: 850;
        color: var(--text);
        margin-bottom: 10px;
    }

    .bj-feature-card p {
        color: var(--muted);
        font-size: 0.9rem;
        line-height: 1.7;
        margin: 0;
    }

    /* STEPS CARDS */
    .bj-step-card {
        background: #ffffff;
        border: 1px solid #e2e8e3;
        border-radius: 20px;
        padding: 32px;
        height: 100%;
        position: relative;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .bj-step-num {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--green);
        color: #ffffff;
        font-weight: 900;
        font-size: 1rem;
        display: grid;
        place-items: center;
        margin-bottom: 20px;
        box-shadow: 0 6px 16px rgba(25, 135, 84, 0.2);
    }

    .bj-step-card h4 {
        font-size: 1.1rem;
        font-weight: 850;
        color: var(--text);
        margin-bottom: 10px;
    }

    .bj-step-card p {
        color: var(--muted);
        font-size: 0.9rem;
        line-height: 1.7;
        margin: 0;
    }

    /* MODERN BANNER CTA */
    .bj-banner-cta {
        background: linear-gradient(135deg, #126640 0%, #198754 100%);
        border-radius: 24px;
        padding: 50px 40px;
        color: #ffffff;
        box-shadow: 0 20px 50px rgba(25, 135, 84, 0.25);
        position: relative;
        overflow: hidden;
    }

    .bj-banner-cta h2 {
        font-size: clamp(1.8rem, 3vw, 2.4rem);
        font-weight: 900;
        letter-spacing: -0.5px;
        margin-bottom: 14px;
    }

    .bj-banner-cta p {
        color: #e2f2e6;
        font-size: 1.02rem;
        line-height: 1.7;
        margin: 0;
        max-width: 580px;
    }

    @media (max-width: 991.98px) {
        .bj-hero {
            padding: 50px 0 70px;
        }
        .bj-hero-card-img img {
            height: 320px;
        }
        .bj-floating-badge {
            left: 12px;
            bottom: -15px;
        }
        .bj-stats-strip {
            margin-top: 20px;
        }
        .bj-section {
            padding: 60px 0;
        }
    }
</style>

<div class="home-bangjo">

    {{-- HERO SECTION --}}
    <section class="bj-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="bj-eyebrow">
                        <i class="bi bi-stars"></i> KEDAI BANGJO CATERING
                    </span>

                    <h1 class="bj-title">
                        Sajian Istimewa untuk <span>Setiap Momen.</span>
                    </h1>

                    <p class="bj-description">
                        Lengkapi acara keluarga, kantor, maupun perayaan spesial Anda dengan pilihan paket catering dan prasmanan berkualitas. Higienis, lezat, dan dikelola secara profesional.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('customer.packages.index') }}" class="bj-btn-primary">
                            <span>Lihat Paket Catering</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="{{ route('customer.prasmanan.index') }}" class="bj-btn-secondary">
                            <i class="bi bi-people text-success"></i>
                            <span>Paket Prasmanan</span>
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-4 mt-4 text-muted small fw-bold">
                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Menu Higienis</span>
                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Harga Transparan</span>
                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Real-time Tracking</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bj-hero-visual-wrapper">
                        <div class="bj-hero-card-img">
                            <img src="{{ asset('assets/img/hero.jpeg') }}"
                                 alt="Hidangan Kedai Bangjo Catering"
                                 fetchpriority="high">
                        </div>

                        <div class="bj-floating-badge">
                            <div class="bj-floating-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Kualitas Terjamin</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Dimasak fresh setiap hari oleh koki berpengalaman.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- STATS STRIP --}}
    <div class="container mb-5">
        <div class="bj-stats-strip">
            <div class="row">
                <div class="col-4 bj-stat-item border-end border-success border-opacity-25">
                    <div class="bj-stat-number">100%</div>
                    <div class="bj-stat-label">Bahan Segar Pilihan</div>
                </div>
                <div class="col-4 bj-stat-item border-end border-success border-opacity-25">
                    <div class="bj-stat-number">50+</div>
                    <div class="bj-stat-label">Varian Menu Spesial</div>
                </div>
                <div class="col-4 bj-stat-item">
                    <div class="bj-stat-number">24/7</div>
                    <div class="bj-stat-label">Pemesanan Online</div>
                </div>
            </div>
        </div>
    </div>

    {{-- PILIHAN LAYANAN --}}
    <section class="bj-section">
        <div class="container">
            <div class="bj-section-header">
                <span class="bj-badge-label">Katalog Layanan</span>
                <h2 class="bj-section-title">Solusi Konsumsi untuk Berbagai Acara</h2>
                <p class="bj-section-desc">
                    Pilih jenis layanan catering yang paling sesuai dengan kebutuhan acara Anda saat ini.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <a href="{{ route('customer.packages.index') }}" class="bj-category-card">
                        <div>
                            <div class="bj-category-icon-box">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <h3>Paket Catering Harian & Kotak</h3>
                            <p>
                                Solusi praktis untuk makan siang kantor, syukuran rumah, atau kegiatan harian dengan susunan menu lengkap yang higienis dan lezat.
                            </p>
                        </div>
                        <div class="bj-link-arrow">
                            <span>Jelajahi Paket Catering</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6">
                    <a href="{{ route('customer.prasmanan.index') }}" class="bj-category-card">
                        <div>
                            <div class="bj-category-icon-box">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3>Paket Prasmanan Acara Spesial</h3>
                            <p>
                                Sajian prasmanan mewah dan elegan untuk pernikahan, pesta ulang tahun, reuni, seminar, dan perayaan besar lainnya bersama tamu undangan.
                            </p>
                        </div>
                        <div class="bj-link-arrow">
                            <span>Jelajahi Paket Prasmanan</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- KEUNGGULAN KAMI --}}
    <section class="bj-section bj-section-soft">
        <div class="container">
            <div class="bj-section-header">
                <span class="bj-badge-label">Keunggulan Kami</span>
                <h2 class="bj-section-title">Mengapa Memilih Kedai BangJo?</h2>
                <p class="bj-section-desc">
                    Kami berkomitmen memberikan pengalaman pemesanan catering yang transparan, mudah, dan memuaskan.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="bj-feature-card">
                        <div class="bj-feature-icon">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h4>Informasi Menu Transparan</h4>
                        <p>Daftar komposisi, deskripsi, dan harga per porsi tertera jelas tanpa ada biaya tersembunyi.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bj-feature-card">
                        <div class="bj-feature-icon">
                            <i class="bi bi-calendar-range"></i>
                        </div>
                        <h4>Penjadwalan Fleksibel</h4>
                        <p>Atur tanggal pengiriman atau pengambilan pesanan langsung dengan mudah saat melakukan checkout.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bj-feature-card">
                        <div class="bj-feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>Pantau Status Real-Time</h4>
                        <p>Cek perkembangan pesanan Anda secara berkala mulai dari proses dapur hingga siap diantar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ALUR PEMESANAN --}}
    <section class="bj-section">
        <div class="container">
            <div class="bj-section-header">
                <span class="bj-badge-label">Cara Pemesanan</span>
                <h2 class="bj-section-title">Tiga Langkah Mudah Berbelanja</h2>
                <p class="bj-section-desc">
                    Proses pemesanan cepat dan terstruktur dalam genggaman Anda.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="bj-step-card">
                        <div class="bj-step-num">01</div>
                        <h4>Pilih Menu & Paket</h4>
                        <p>Jelajahi katalog paket catering atau prasmanan lalu tentukan pilihan hidangan favorit Anda.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bj-step-card">
                        <div class="bj-step-num">02</div>
                        <h4>Checkout & Jadwalkan</h4>
                        <p>Masukkan jumlah porsi, alamat pengiriman, serta tanggal acara dengan lengkap.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bj-step-card">
                        <div class="bj-step-num">03</div>
                        <h4>Selesaikan Pembayaran</h4>
                        <p>Lakukan transfer/QRIS, unggah bukti bayar, dan pesanan Anda langsung diproses tim kami.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BANNER CTA --}}
    <section class="pb-5 mb-4">
        <div class="container">
            <div class="bj-banner-cta">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2>Sudah Siap Menentukan Menu untuk Acara Anda?</h2>
                        <p>
                            Temukan paket hidangan terbaik sekarang juga atau konsultasikan kebutuhan katering Anda bersama kami.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-sm-row flex-column justify-content-lg-end gap-2">
                            <a href="{{ route('customer.packages.index') }}" class="bj-btn-primary bg-white text-dark border-white" style="color: #126640 !important;">
                                <span>Lihat Katalog</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="{{ route('cart.index') }}" class="bj-btn-secondary bg-transparent text-white border-white" style="color: #ffffff !important;">
                                <i class="bi bi-cart3"></i>
                                <span>Keranjang</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection