
@extends('layouts.app')

@section('title', 'Laily Catering | Catering untuk Setiap Acara')

@section('content')

<style>
    .lc-home {
        --lc-green: #198754;
        --lc-green-dark: #126640;
        --lc-green-light: #edf7ef;
        --lc-text: #202b24;
        --lc-muted: #6c786f;
        color: var(--lc-text);
        overflow: hidden;
    }

    .lc-home .container {
        position: relative;
    }

    .lc-home a {
        text-decoration: none;
    }

    /* HERO */
    .lc-hero {
        padding: 58px 0 64px;
        background:
            radial-gradient(circle at 85% 15%, rgba(180, 220, 187, .30), transparent 30%),
            linear-gradient(135deg, #f5faf5 0%, #edf6ee 55%, #f9fbf8 100%);
    }

    .lc-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 13px;
        border: 1px solid #d5e9d9;
        border-radius: 999px;
        background: rgba(255,255,255,.75);
        color: var(--lc-green-dark);
        font-size: .82rem;
        font-weight: 700;
    }

    .lc-hero-title {
        margin: 22px 0 18px;
        font-size: clamp(2.3rem, 5vw, 4.1rem);
        line-height: 1.12;
        letter-spacing: -1.7px;
        font-weight: 800;
    }

    .lc-hero-title span {
        color: var(--lc-green);
    }

    .lc-hero-description {
        max-width: 520px;
        margin-bottom: 25px;
        color: var(--lc-muted);
        font-size: 1.04rem;
        line-height: 1.85;
    }

    .lc-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 21px;
        border: 1px solid var(--lc-green);
        border-radius: 12px;
        background: var(--lc-green);
        color: #fff !important;
        font-weight: 600;
        transition: .2s ease;
    }

    .lc-btn-primary:hover {
        background: var(--lc-green-dark);
        border-color: var(--lc-green-dark);
        transform: translateY(-2px);
    }

    .lc-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border: 1px solid #d6e2d8;
        border-radius: 12px;
        background: #fff;
        color: #26372b !important;
        font-weight: 600;
        transition: .2s ease;
    }

    .lc-btn-outline:hover {
        border-color: #8fbea0;
        background: #f9fcf9;
        transform: translateY(-2px);
    }

    .lc-hero-image-wrap {
        position: relative;
        padding: 12px;
        border: 1px solid rgba(255,255,255,.9);
        border-radius: 28px;
        background: rgba(255,255,255,.55);
        box-shadow: 0 22px 60px rgba(35, 75, 45, .12);
    }

    .lc-hero-image {
        display: block;
        width: 100%;
        height: 390px;
        object-fit: cover;
        border-radius: 20px;
    }

    .lc-hero-floating {
        position: absolute;
        right: -12px;
        bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 245px;
        padding: 15px 17px;
        border: 1px solid #edf1ed;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 12px 35px rgba(25, 60, 35, .12);
    }

    .lc-floating-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        flex-shrink: 0;
        border-radius: 12px;
        background: var(--lc-green-light);
        color: var(--lc-green);
        font-size: 1.3rem;
    }

    .lc-hero-floating strong {
        display: block;
        font-size: .92rem;
    }

    .lc-hero-floating small {
        display: block;
        margin-top: 3px;
        color: var(--lc-muted);
        line-height: 1.45;
    }

    .lc-hero-points {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 26px;
        color: #506055;
        font-size: .88rem;
    }

    .lc-hero-points i {
        margin-right: 5px;
        color: var(--lc-green);
    }

    /* SECTION GENERAL */
    .lc-section {
        padding: 68px 0;
    }

    .lc-section-soft {
        background: #f8faf8;
    }

    .lc-section-heading {
        max-width: 650px;
        margin: 0 auto 36px;
        text-align: center;
    }

    .lc-section-label {
        display: inline-block;
        margin-bottom: 10px;
        color: var(--lc-green);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }

    .lc-section-title {
        margin-bottom: 12px;
        font-size: clamp(1.7rem, 3vw, 2.35rem);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .lc-section-description {
        margin: 0;
        color: var(--lc-muted);
        line-height: 1.8;
    }

    /* QUICK ACCESS */
    .lc-quick-card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        height: 100%;
        padding: 23px;
        border: 1px solid #e7eee8;
        border-radius: 17px;
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .lc-quick-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(35, 65, 42, .07);
    }

    .lc-quick-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 49px;
        height: 49px;
        flex-shrink: 0;
        border-radius: 14px;
        background: var(--lc-green-light);
        color: var(--lc-green);
        font-size: 1.35rem;
    }

    .lc-quick-card h3 {
        margin: 2px 0 7px;
        font-size: 1.02rem;
        font-weight: 750;
    }

    .lc-quick-card p {
        margin: 0;
        color: var(--lc-muted);
        font-size: .9rem;
        line-height: 1.7;
    }

    .lc-quick-arrow {
        margin-left: auto;
        color: #91a095;
    }

    /* PACKAGE CARDS */
    .lc-package-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid #e8eee9;
        border-radius: 18px;
        background: #fff;
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .lc-package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 40px rgba(30, 65, 38, .10);
    }

    .lc-package-image {
        position: relative;
        height: 225px;
        overflow: hidden;
        background: #f0f4f0;
    }

    .lc-package-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }

    .lc-package-card:hover .lc-package-image img {
        transform: scale(1.04);
    }

    .lc-package-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: #9eaaa0;
        font-size: 2.5rem;
    }

    .lc-package-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        padding: 7px 11px;
        border: 1px solid rgba(255,255,255,.7);
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        color: var(--lc-green-dark);
        font-size: .76rem;
        font-weight: 700;
    }

    .lc-package-body {
        display: flex;
        flex-direction: column;
        padding: 21px;
    }

    .lc-package-body h3 {
        margin: 8px 0;
        font-size: 1.15rem;
        font-weight: 750;
    }

    .lc-package-description {
        min-height: 48px;
        margin-bottom: 18px;
        color: var(--lc-muted);
        font-size: .9rem;
        line-height: 1.65;
    }

    .lc-package-bottom {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #edf1ed;
    }

    .lc-package-price-label {
        display: block;
        margin-bottom: 4px;
        color: var(--lc-muted);
        font-size: .78rem;
    }

    .lc-package-price {
        color: var(--lc-green-dark);
        font-size: 1.1rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .lc-package-unit {
        display: block;
        margin-top: 3px;
        color: var(--lc-muted);
        font-size: .78rem;
    }

    .lc-btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 13px;
        border-radius: 10px;
        background: var(--lc-green-light);
        color: var(--lc-green-dark) !important;
        font-size: .88rem;
        font-weight: 700;
        white-space: nowrap;
        transition: .2s ease;
    }

    .lc-btn-detail:hover {
        background: #dcefe0;
    }

    .lc-empty-state {
        padding: 45px 20px;
        border: 1px dashed #d7e3d9;
        border-radius: 18px;
        background: #fff;
        text-align: center;
    }

    .lc-empty-state i {
        color: #8eaa95;
        font-size: 2.5rem;
    }

    /* HOW IT WORKS */
    .lc-step {
        position: relative;
        height: 100%;
        padding: 25px;
        border: 1px solid #e7eee8;
        border-radius: 17px;
        background: #fff;
    }

    .lc-step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        margin-bottom: 17px;
        border-radius: 13px;
        background: var(--lc-green-light);
        color: var(--lc-green);
        font-weight: 800;
    }

    .lc-step h3 {
        margin-bottom: 9px;
        font-size: 1.05rem;
        font-weight: 750;
    }

    .lc-step p {
        margin: 0;
        color: var(--lc-muted);
        font-size: .9rem;
        line-height: 1.75;
    }

    /* CUSTOM CTA */
    .lc-custom-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        padding: 35px;
        border: 1px solid #d9eadd;
        border-radius: 22px;
        background: linear-gradient(120deg, #eaf6ec, #f7fbf7);
    }

    .lc-custom-cta h2 {
        margin: 5px 0 10px;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 800;
    }

    .lc-custom-cta p {
        max-width: 630px;
        margin: 0;
        color: var(--lc-muted);
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .lc-hero {
            padding: 45px 0 50px;
        }

        .lc-hero-image {
            height: 330px;
        }

        .lc-hero-floating {
            right: 10px;
        }

        .lc-section {
            padding: 52px 0;
        }
    }

    @media (max-width: 575px) {
        .lc-hero {
            padding: 35px 0 40px;
        }

        .lc-hero-title {
            letter-spacing: -1px;
        }

        .lc-hero-image {
            height: 260px;
        }

        .lc-hero-floating {
            right: 8px;
            bottom: 20px;
            max-width: 225px;
            padding: 11px;
        }

        .lc-hero-points {
            gap: 10px 14px;
        }

        .lc-section {
            padding: 43px 0;
        }

        .lc-package-image {
            height: 210px;
        }

        .lc-package-body {
            padding: 17px;
        }

        .lc-custom-cta {
            align-items: flex-start;
            flex-direction: column;
            padding: 24px;
        }

        .lc-custom-cta .lc-btn-primary {
            width: 100%;
        }
    }
</style>

<div class="lc-home">

    {{-- HERO --}}
    <section class="lc-hero">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">
                    <span class="lc-eyebrow">
                        <i class="bi bi-stars"></i>
                        Catering untuk berbagai kebutuhan
                    </span>

                    <h1 class="lc-hero-title">
                        Sajian lezat untuk
                        <span>setiap momen.</span>
                    </h1>

                    <p class="lc-hero-description">
                        Dari acara keluarga hingga kebutuhan kantor,
                        temukan paket catering yang praktis atau susun
                        pilihan menu sesuai kebutuhan acaramu.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('customer.packages.index') }}"
                           class="lc-btn-primary">
                            <i class="bi bi-grid"></i>
                            Jelajahi Paket
                        </a>

                        <a href="{{ url('/custom-catering') }}"
                           class="lc-btn-outline">
                            <i class="bi bi-sliders"></i>
                            Custom Catering
                        </a>
                    </div>

                    <div class="lc-hero-points">
                        <span><i class="bi bi-check-circle-fill"></i>Pilihan menu beragam</span>
                        <span><i class="bi bi-check-circle-fill"></i>Harga dikonfirmasi admin</span>
                        <span><i class="bi bi-check-circle-fill"></i>Pesanan sesuai kebutuhan</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="lc-hero-image-wrap">
                        <img
                            src="{{ asset('assets/img/hero.jpeg') }}"
                            alt="Hidangan catering Laily Catering"
                            class="lc-hero-image"
                            fetchpriority="high"
                        >

                        <div class="lc-hero-floating">
                            <div class="lc-floating-icon">
                                <i class="bi bi-calendar2-check"></i>
                            </div>
                            <div>
                                <strong>Untuk acara spesialmu</strong>
                                <small>Pilih paket atau sesuaikan menu sendiri.</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- AKSES CEPAT --}}
    <section class="lc-section">
        <div class="container">

            <div class="lc-section-heading">
                <span class="lc-section-label">Mulai dari sini</span>
                <h2 class="lc-section-title">Pesan catering dengan cara yang kamu suka</h2>
                <p class="lc-section-description">
                    Mau yang praktis atau ingin menentukan menu sendiri?
                    Pilih cara pemesanan yang paling sesuai.
                </p>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <a href="{{ route('customer.packages.index') }}"
                       class="lc-quick-card">
                        <div class="lc-quick-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h3>Pilih Paket</h3>
                            <p>Pilih paket yang sudah disusun untuk kebutuhan acara.</p>
                        </div>
                        <i class="bi bi-arrow-up-right lc-quick-arrow"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ url('/custom-catering') }}"
                       class="lc-quick-card">
                        <div class="lc-quick-icon">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <h3>Racik Menu Sendiri</h3>
                            <p>Tentukan lauk, sayur, pelengkap, dan jumlah porsinya.</p>
                        </div>
                        <i class="bi bi-arrow-up-right lc-quick-arrow"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('cart.index') }}"
                       class="lc-quick-card">
                        <div class="lc-quick-icon">
                            <i class="bi bi-basket2"></i>
                        </div>
                        <div>
                            <h3>Keranjang</h3>
                            <p>Periksa pilihan paket dan lanjutkan pemesanan.</p>
                        </div>
                        <i class="bi bi-arrow-up-right lc-quick-arrow"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- PAKET CATERING --}}
    <section class="lc-section lc-section-soft">
        <div class="container">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div>
                    <span class="lc-section-label">Pilihan pelanggan</span>
                    <h2 class="lc-section-title mb-2">Paket Catering</h2>
                    <p class="lc-section-description">
                        Lihat paket yang tersedia dan pilih sesuai kebutuhanmu.
                    </p>
                </div>

                <a href="{{ route('customer.packages.index') }}"
                   class="lc-btn-outline align-self-md-end">
                    Lihat Semua Paket
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">

                @forelse($package->take(3) as $item)
                    <div class="col-md-6 col-lg-4">

                        <article class="lc-package-card">

                            <div class="lc-package-image">
                                @if($item->image)
                                    <img
                                        src="{{ asset('storage/' . $item->image) }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="lc-package-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif

                                <span class="lc-package-badge">
                                    <i class="bi bi-box-seam me-1"></i>
                                    Paket Catering
                                </span>
                            </div>

                            <div class="lc-package-body">
                                <h3>{{ $item->name }}</h3>

                                <p class="lc-package-description">
                                    {{ \Illuminate\Support\Str::limit($item->description ?? 'Paket catering untuk kebutuhan acara Anda.', 95) }}
                                </p>

                                <div class="lc-package-bottom">
                                    <div>
                                        <span class="lc-package-price-label">Harga mulai</span>
                                        <div class="lc-package-price">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}
                                        </div>
                                        <span class="lc-package-unit">per porsi</span>
                                    </div>

                                    <a href="{{ route('customer.packages.show', $item) }}"
                                       class="lc-btn-detail">
                                        Lihat Detail
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>

                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="lc-empty-state">
                            <i class="bi bi-box-seam"></i>
                            <h3 class="h5 fw-bold mt-3">Paket belum tersedia</h3>
                            <p class="text-muted mb-3">
                                Saat ini belum ada paket yang bisa ditampilkan.
                            </p>
                            <a href="{{ url('/custom-catering') }}"
                               class="lc-btn-primary">
                                Coba Custom Catering
                            </a>
                        </div>
                    </div>
                @endforelse

            </div>

            <div class="text-center mt-4 d-md-none">
                <a href="{{ route('customer.packages.index') }}"
                   class="lc-btn-outline">
                    Lihat Semua Paket
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>
    </section>

    {{-- CARA MEMESAN --}}
    <section class="lc-section">
        <div class="container">

            <div class="lc-section-heading">
                <span class="lc-section-label">Cara pemesanan</span>
                <h2 class="lc-section-title">Tiga langkah menuju acara yang lebih praktis</h2>
                <p class="lc-section-description">
                    Prosesnya sederhana. Kamu tetap bisa memeriksa pesanan
                    sebelum melanjutkan ke tahap berikutnya.
                </p>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="lc-step">
                        <div class="lc-step-number">01</div>
                        <h3>Pilih menu atau paket</h3>
                        <p>
                            Jelajahi paket catering atau susun menu sendiri
                            berdasarkan kebutuhan acara.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="lc-step">
                        <div class="lc-step-number">02</div>
                        <h3>Isi detail pesanan</h3>
                        <p>
                            Masukkan jumlah porsi, tanggal acara, dan alamat
                            agar kebutuhan pesanan lebih jelas.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="lc-step">
                        <div class="lc-step-number">03</div>
                        <h3>Konfirmasi pesanan</h3>
                        <p>
                            Untuk pesanan custom, admin memeriksa permintaan
                            dan mengonfirmasi harga final terlebih dahulu.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA CUSTOM --}}
    <section class="pb-5">
        <div class="container">
            <div class="lc-custom-cta">
                <div>
                    <span class="lc-section-label">Punya kebutuhan khusus?</span>
                    <h2>Menu catering bisa kamu sesuaikan.</h2>
                    <p>
                        Tentukan menu dan jumlah porsi sesuai acara.
                        Kirim permintaan, lalu tunggu konfirmasi harga dari admin.
                    </p>
                </div>

                <a href="{{ url('/custom-catering') }}"
                   class="lc-btn-primary flex-shrink-0">
                    Mulai Custom
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

</div>

@endsection
