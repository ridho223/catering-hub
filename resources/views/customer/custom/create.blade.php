
@extends('layouts.app')

@section('title', 'Custom Catering')

@section('content')
<style>
    .custom-page {
        background: #f7f9f7;
        min-height: 80vh;
    }

    .custom-hero {
        background: linear-gradient(120deg, #e6f3e9, #f4f8f4);
        border: 1px solid #e1eee4;
        border-radius: 20px;
    }

    .custom-section {
        border: 1px solid #e7ede8;
        border-radius: 16px;
    }

    .custom-section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .custom-section-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #e8f4eb;
        color: #187447;
        font-size: 1.2rem;
    }

    .custom-page .form-control {
        border-color: #dfe6df;
        padding: .7rem .85rem;
        border-radius: 10px;
    }

    .custom-page .form-control:focus {
        border-color: #6bad7c;
        box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .10);
    }

    /* Tab kategori */
    .category-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 3px 2px 12px;
        scrollbar-width: thin;
    }

    .category-tab {
        flex: 0 0 auto;
        border: 1px solid #dfe8e1;
        background: #fff;
        color: #526257;
        border-radius: 999px;
        padding: 9px 16px;
        font-size: .9rem;
        font-weight: 600;
        white-space: nowrap;
        transition: .2s ease;
    }

    .category-tab:hover {
        background: #f0f8f1;
        border-color: #a7cfb0;
    }

    .category-tab.active {
        background: #198754;
        border-color: #198754;
        color: #fff;
    }

    /* Area daftar menu yang bisa scroll */
    .menu-list-scroll {
        max-height: 590px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 2px 6px 8px 2px;
        scrollbar-width: thin;
        scrollbar-color: #b7cdbb #f3f6f3;
    }

    .menu-list-scroll::-webkit-scrollbar {
        width: 7px;
    }

    .menu-list-scroll::-webkit-scrollbar-thumb {
        background: #b7cdbb;
        border-radius: 20px;
    }

    .custom-menu {
        height: 100%;
        border: 1px solid #e5ebe6;
        border-radius: 13px;
        padding: 15px;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
    }

    .custom-menu:focus-within {
        border-color: #75b78a;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, .06);
    }

    .custom-menu-price {
        color: #187447;
        font-weight: 700;
    }

    .custom-menu .menu-quantity {
        max-width: 105px;
    }

    .menu-stock {
        font-size: .72rem;
        white-space: nowrap;
    }

    .menu-search-wrap {
        position: relative;
    }

    .menu-search-wrap > i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #7c8b80;
        z-index: 2;
    }

    .menu-search {
        padding-left: 40px !important;
    }

    .custom-summary {
        position: sticky;
        top: 20px;
        border: 1px solid #e7ede8;
        border-radius: 16px;
    }

    .custom-summary-stat {
        background: #f4f8f4;
        border-radius: 11px;
        padding: 12px;
    }

    .custom-submit {
        background: #198754;
        border-color: #198754;
    }

    .custom-submit:hover {
        background: #146c43;
        border-color: #146c43;
    }

    .menu-empty-search {
        display: none;
        padding: 35px 15px;
        text-align: center;
        color: #77847a;
    }

    @media (max-width: 991px) {
        .custom-summary {
            position: static;
        }

        .menu-list-scroll {
            max-height: 520px;
        }
    }

    @media (max-width: 575px) {
        .custom-hero {
            border-radius: 15px;
        }

        .menu-list-scroll {
            max-height: 470px;
            padding-right: 2px;
        }

        .custom-menu {
            padding: 12px;
        }

        .custom-menu .menu-quantity {
            max-width: 90px;
        }
    }
</style>

<div class="custom-page py-4 py-lg-5">
    <div class="container">

        {{-- HEADER --}}
        <div class="custom-hero p-4 p-lg-5 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <span class="text-success fw-semibold small">
                        <i class="bi bi-sliders me-1"></i>
                        PESAN SESUAI KEBUTUHAN
                    </span>

                    @if ($includePackages)
                        <h1 class="fw-bold mt-2 mb-2">Custom Paket</h1>

                        <p class="text-secondary mb-0">
                            Ganti isi paket atau tambahkan menu sesuai kebutuhan.
                            Harga final akan dikonfirmasi oleh admin
                        </p>
                    
                    @else
                        <h1 class="fw-bold mt-2 mb-2">Racik Catering Sendiri</h1>

                        <p class="text-secondary mb-0">
                            Pilih menu dan jumlah porsi sesuai kebutuhan acaramu.
                            Admin akan memeriksa permintaan dan mengonfirmasi harga final.
                        </p>                        
                    @endif
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('customer.custom.index') }}"
                       class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-list-check me-2"></i>
                        Permintaan Saya
                    </a>
                </div>
            </div>

            <div class="row g-2 mt-3">
                <div class="col-sm-4">
                    <small class="text-success fw-semibold">
                        <i class="bi bi-1-circle-fill me-1"></i> Data acara
                    </small>
                </div>
                <div class="col-sm-4">
                    <small class="text-success fw-semibold">
                        <i class="bi bi-2-circle-fill me-1"></i> Pilih menu
                    </small>
                </div>
                <div class="col-sm-4">
                    <small class="text-success fw-semibold">
                        <i class="bi bi-3-circle-fill me-1"></i> Konfirmasi admin
                    </small>
                </div>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger rounded-3">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <div class="fw-semibold mb-1">
                    Ada data yang perlu diperiksa:
                </div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($includePackages)
            <div class="alert alert-success rounded-3">
                <h6 class="fw-bold mb-2">
                    <i class="bi bi-bag-check me-1"></i>
                    Paket yang akan digabungkan
                </h6>

                @foreach ($cartPackages as $item)
                    <div class="d-flex justify-content-between gap-3">
                        <span>{{ $item['name'] }}</span>
                        <span class="text-nowrap">
                            {{number_format($item['quantity']) }} porsi
                        </span>
                    </div>
                @endforeach

                <small class="d-block mt-2">
                    Kamu bisa menambahkan menu lain di bawah ini.
                </small>
            </div>
        @endif

        @php
            $menuCategories = $categories->filter(function ($category) {
                return $category->menus->isNotEmpty();
            })->values();

            $hasMenus = $menuCategories->isNotEmpty();
        @endphp

        <form action="{{ route('customer.custom.store') }}" method="POST">
            @csrf

            @if ($includePackages)
                <input type="hidden" name="package_id" value="{{$package->id}}">
                <input type="hidden" name="quantity" value="{{$quantity}}">
                <input type="hidden" name="include_packages" value="1">
            @endif

            <div class="row g-4 align-items-start">

                {{-- KOLOM KIRI --}}
                <div class="col-lg-8">

                {{-- DATA ACARA --}}
                @if (!$includePackages)
                    <section class="card custom-section shadow-sm mb-4">
                        <div class="card-body p-3 p-md-4">

                            <div class="custom-section-title mb-4">
                                <div class="custom-section-icon">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Informasi Acara</h5>
                                    <small class="text-muted">
                                        Lengkapi informasi pesananmu.
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-semibold">
                                        Nama acara <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="title"
                                           name="title"
                                           class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}"
                                           placeholder="Contoh: Syukuran keluarga"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="guest_count" class="form-label fw-semibold">
                                        Jumlah tamu <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               id="guest_count"
                                               name="guest_count"
                                               class="form-control @error('guest_count') is-invalid @enderror"
                                               value="{{ old('guest_count') }}"
                                               min="1"
                                               placeholder="Contoh: 50"
                                               required>
                                        <span class="input-group-text bg-light">orang</span>
                                    </div>
                                    @error('guest_count')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="event_date" class="form-label fw-semibold">
                                        Tanggal acara <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           id="event_date"
                                           name="event_date"
                                           class="form-control @error('event_date') is-invalid @enderror"
                                           value="{{ old('event_date') }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('event_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="estimated_budget" class="form-label fw-semibold">
                                        Anggaran perkiraan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number"
                                               id="estimated_budget"
                                               name="estimated_budget"
                                               class="form-control @error('estimated_budget') is-invalid @enderror"
                                               value="{{ old('estimated_budget') }}"
                                               min="0"
                                               placeholder="Contoh: 1500000">
                                    </div>
                                    @error('estimated_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Opsional, untuk membantu admin menyusun penawaran.
                                    </small>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">
                                        Alamat acara <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="address"
                                              name="address"
                                              rows="3"
                                              class="form-control @error('address') is-invalid @enderror"
                                              placeholder="Nama jalan, nomor rumah/gedung, kelurahan, dan patokan lokasi"
                                              required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">
                                        Catatan tambahan
                                    </label>
                                    <textarea id="description"
                                              name="description"
                                              rows="3"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Contoh: sambal dipisah atau makanan tidak terlalu pedas">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </section>
                @endif

                {{-- Tanggal acara dan alamat --}}                
                @if ($includePackages && $package)
                    <section class="card custom-section shadow-sm mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div class="custom-section-title mb-3">
                                <div class="custom-section-icon">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Jadwal dan Alamat Acara</h5>
                                    <small class="text-muted">
                                        Lengkapi jadwal dan lokasi pengantaran.
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="event_date" class="form-label fw-semibold">
                                    Tanggal acara <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="event_date"
                                    name="event_date"
                                    class="form-control @error('event_date') is-invalid @enderror"
                                    value="{{ old('event_date', now()->addDay()->toDateString()) }}"
                                    min="{{ now()->toDateString() }}"
                                    required
                                >
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="form-label fw-semibold">
                                    Alamat acara <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="3"
                                    class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Masukkan alamat lengkap acara"
                                    required
                                >{{ old('address', auth('customer')->user()->customer->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>
                @endif

                {{-- paket --}}
                @if ($includePackages && $package)
                    <section class="card custom-section shadow-sm mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div class="custom-section-title mb-3">
                                <div class="custom-section-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        {{$includePackages ? 'Tambah Menu ke Paket' : 'Pilih Menu Catering'}}
                                    </h5>
                                    <small class="text-muted">
                                        {{$includePackages ? 'Pilih menu tambahan di luar komposisi paket.' : 'Cari menu, lalu tentukan jumlah porsinya.'}}
                                    </small>
                                </div>
                            </div>

                            <div class="alert alert-light border small">
                                <strong>{{ $package->name }}</strong>
                                <div>
                                    Harga dasar:
                                    Rp{{ number_format($package->price, 0, ',', '.') }}
                                    × {{ $quantity }} paket
                                </div>
                            </div>

                            @foreach ($package->details as $detail)
                                @if ($detail->menu)
                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $detail->menu->name }}
                                                </div>
                                                <small class="text-muted">
                                                    Isi asli: {{ $detail->quantity }} per paket
                                                </small>
                                            </div>

                                            <span class="badge bg-light text-dark border align-self-start">
                                                Menu paket
                                            </span>
                                        </div>

                                        <label class="form-label small fw-semibold mt-3">
                                            Ganti menu ini
                                        </label>

                                        <select
                                            name="replacements[{{ $detail->id }}]"
                                            class="form-select"
                                        >
                                            <option value="">Tetap gunakan {{ $detail->menu->name }}</option>

                                            @foreach ($categories as $category)
                                                @foreach ($category->menus as $menu)
                                                    @if ($menu->id != $detail->menus_id)
                                                        <option
                                                            value="{{ $menu->id }}"
                                                            @selected(
                                                                old('replacements.'.$detail->id) == $menu->id
                                                            )
                                                        >
                                                            {{ $menu->name }} · Rp{{ number_format($menu->price, 0, ',', '.') }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                    {{-- PILIH MENU --}}
                    <section class="card custom-section shadow-sm">
                        <div class="card-body p-3 p-md-4">

                            <div class="custom-section-title mb-2">
                                <div class="custom-section-icon">
                                    <i class="bi bi-egg-fried"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Pilih Menu Catering</h5>
                                    <small class="text-muted">
                                        Cari menu, lalu tentukan jumlah porsinya.
                                    </small>
                                </div>
                            </div>

                            @error('menus')
                                <div class="alert alert-danger py-2 mt-3">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if($hasMenus)

                                {{-- PENCARIAN --}}
                                <div class="menu-search-wrap mt-3 mb-3">
                                    <i class="bi bi-search"></i>
                                    <input type="search"
                                           id="menu-search"
                                           class="form-control menu-search"
                                           placeholder="Cari menu pada kategori yang dipilih..."
                                           autocomplete="off">
                                </div>

                                {{-- TAB KATEGORI --}}
                                <div class="category-tabs" role="tablist"
                                     aria-label="Kategori menu">

                                    @foreach($menuCategories as $category)
                                        <button type="button"
                                                class="category-tab {{ $loop->first ? 'active' : '' }}"
                                                data-category-tab="{{ $category->id }}"
                                                role="tab"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $category->name }}
                                            <span class="ms-1 opacity-75">
                                                {{ $category->menus->count() }}
                                            </span>
                                        </button>
                                    @endforeach

                                </div>

                                <div class="alert alert-light border rounded-3 small mb-3">
                                    <i class="bi bi-info-circle text-success me-2"></i>
                                    Isi <strong>0</strong> jika tidak ingin memilih menu.
                                    Harga di bawah ini adalah estimasi dan akan diperiksa admin.
                                </div>

                                {{-- DAFTAR MENU SCROLL --}}
                                <div class="menu-list-scroll">

                                    @foreach($menuCategories as $category)
                                        <div class="category-panel"
                                             data-category-panel="{{ $category->id }}"
                                             @if(!$loop->first) hidden @endif>

                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span class="text-success">
                                                    <i class="bi bi-grid"></i>
                                                </span>
                                                <div>
                                                    <h6 class="fw-bold mb-0">
                                                        {{ $category->name }}
                                                    </h6>
                                                    @if($category->description)
                                                        <small class="text-muted">
                                                            {{ $category->description }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row g-3 menu-grid">

                                                @foreach($category->menus as $menu)
                                                    <div class="col-md-6 menu-card"
                                                         data-menu-name="{{ strtolower($menu->name) }}">

                                                        <div class="custom-menu">
                                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                                                <div class="pe-1">
                                                                    <div class="fw-semibold">
                                                                        {{ $menu->name }}
                                                                    </div>
                                                                    <div class="custom-menu-price mt-1">
                                                                        Rp{{ number_format($menu->price, 0, ',', '.') }}
                                                                    </div>
                                                                    <small class="text-muted">per porsi</small>
                                                                </div>

                                                                @if($menu->stock > 0)
                                                                    <span class="badge rounded-pill bg-light text-secondary border menu-stock">
                                                                        Stok {{ $menu->stock }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill bg-danger-subtle text-danger menu-stock">
                                                                        Habis
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                                <label for="menu-{{ $menu->id }}"
                                                                       class="form-label small fw-semibold mb-0">
                                                                    Jumlah porsi
                                                                </label>

                                                                <input type="number"
                                                                       id="menu-{{ $menu->id }}"
                                                                       name="menus[{{ $menu->id }}][quantity]"
                                                                       class="form-control menu-quantity"
                                                                       min="0"
                                                                       max="{{ $menu->stock }}"
                                                                       value="{{ old('menus.' . $menu->id . '.quantity', 0) }}"
                                                                       data-price="{{ $menu->price }}"
                                                                       data-menu-id="{{ $menu->id }}"
                                                                       aria-label="Jumlah porsi {{ $menu->name }}"
                                                                       @if($menu->stock < 1) disabled @endif>

                                                                <input type="hidden"
                                                                       name="menus[{{ $menu->id }}][menu_id]"
                                                                       value="{{ $menu->id }}">
                                                            </div>

                                                            @error('menus.' . $menu->id . '.quantity')
                                                                <small class="text-danger d-block mt-2">
                                                                    {{ $message }}
                                                                </small>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                            <div class="menu-empty-search">
                                                <i class="bi bi-search fs-3 d-block mb-2"></i>
                                                Menu tidak ditemukan dalam kategori ini.
                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                            @else

                                <div class="text-center py-5">
                                    <i class="bi bi-egg-fried text-muted"
                                       style="font-size: 2.5rem;"></i>
                                    <h6 class="fw-bold mt-3">Menu belum tersedia</h6>
                                    <p class="text-muted small mb-0">
                                        Silakan kembali setelah admin menambahkan menu.
                                    </p>
                                </div>

                            @endif

                        </div>
                    </section>

                </div>

                {{-- KOLOM KANAN: RINGKASAN --}}
                <div class="col-lg-4">
                    <aside class="card custom-summary shadow-sm">
                        <div class="card-body p-3 p-md-4">

                            <div class="custom-section-title mb-3">
                                <div class="custom-section-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Ringkasan Pilihan</h5>
                                    <small class="text-muted">Diperbarui otomatis</small>
                                </div>
                            </div>

                            

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="custom-summary-stat">
                                        <small class="text-muted d-block">Jenis menu</small>
                                        <div class="fs-4 fw-bold">
                                            <span id="selected-menu-count">0</span>
                                            <small class="fs-6 fw-normal">menu</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="custom-summary-stat">
                                        <small class="text-muted d-block">Total porsi</small>
                                        <div class="fs-4 fw-bold">
                                            <span id="total-portion-count">0</span>
                                            <small class="fs-6 fw-normal">porsi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 mb-3">
                                <small class="text-muted d-block mb-1">
                                    Estimasi harga menu
                                </small>
                                <div class="fs-4 fw-bold text-success" id="estimated-total">
                                    Rp0
                                </div>
                                <small class="text-muted">
                                    Belum termasuk ongkir atau penyesuaian harga admin.
                                </small>
                            </div>

                            <div class="small text-muted mb-4">
                                <div class="d-flex gap-2 mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <span>Admin memeriksa ketersediaan menu dan kebutuhan acara.</span>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <i class="bi bi-chat-dots text-success"></i>
                                    <span>Harga final dikonfirmasi sebelum kamu menyetujui pesanan.</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <i class="bi bi-truck text-success"></i>
                                    <span>Pilih ambil sendiri atau diantar pada tahap berikutnya.</span>
                                </div>
                            </div>

                            <button type="submit"
                                    class="btn custom-submit btn-success btn-lg rounded-3 w-100"
                                    @if(!$hasMenus && !($includePackages && $package)) disabled @endif>
                                Kirim Permintaan
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>

                            <a href="{{ route('customer.custom.index') }}"
                               class="btn btn-light border rounded-3 w-100 mt-2">
                                Batal
                            </a>

                            <small class="text-muted d-block text-center mt-3">
                                <i class="bi bi-shield-check me-1"></i>
                                Periksa data acara sebelum mengirim.
                            </small>

                        </div>
                    </aside>
                </div>

            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityInputs = document.querySelectorAll('.menu-quantity');
    const selectedMenuCount = document.getElementById('selected-menu-count');
    const totalPortionCount = document.getElementById('total-portion-count');
    const estimatedTotal = document.getElementById('estimated-total');

    const categoryTabs = document.querySelectorAll('[data-category-tab]');
    const categoryPanels = document.querySelectorAll('[data-category-panel]');
    const menuSearch = document.getElementById('menu-search');

    function updateSummary() {
        let selectedMenus = 0;
        let totalPortions = 0;
        let totalPrice = 0;

        quantityInputs.forEach(function (input) {
            let quantity = Number.parseInt(input.value, 10);

            if (!Number.isFinite(quantity) || quantity < 0) {
                quantity = 0;
            }

            const max = Number.parseInt(input.max, 10);

            if (Number.isFinite(max)) {
                quantity = Math.min(quantity, max);
            }

            const price = Math.max(0, Number(input.dataset.price) || 0);

            if (quantity > 0 && !input.disabled) {
                selectedMenus++;
                totalPortions += quantity;
                totalPrice += quantity * price;
            }
        });

        selectedMenuCount.textContent =
            selectedMenus.toLocaleString('id-ID');

        totalPortionCount.textContent =
            totalPortions.toLocaleString('id-ID');

        estimatedTotal.textContent =
            'Rp' + totalPrice.toLocaleString('id-ID');
    }

    function filterActiveCategory() {
        const activeTab = document.querySelector('.category-tab.active');

        if (!activeTab) return;

        const activeCategoryId = activeTab.dataset.categoryTab;
        const activePanel = document.querySelector(
            '[data-category-panel="' + activeCategoryId + '"]'
        );

        if (!activePanel) return;

        const keyword = menuSearch
            ? menuSearch.value.trim().toLocaleLowerCase('id-ID')
            : '';

        const cards = activePanel.querySelectorAll('.menu-card');
        let visibleCount = 0;

        cards.forEach(function (card) {
            const name = card.dataset.menuName || '';
            const matches = name.includes(keyword);

            card.hidden = !matches;

            if (matches) visibleCount++;
        });

        const emptyMessage = activePanel.querySelector('.menu-empty-search');

        if (emptyMessage) {
            emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    categoryTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const categoryId = this.dataset.categoryTab;

            categoryTabs.forEach(function (item) {
                const active = item === tab;
                item.classList.toggle('active', active);
                item.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            categoryPanels.forEach(function (panel) {
                panel.hidden = panel.dataset.categoryPanel !== categoryId;
            });

            if (menuSearch) {
                menuSearch.value = '';
            }

            filterActiveCategory();
        });
    });

    if (menuSearch) {
        menuSearch.addEventListener('input', filterActiveCategory);
    }

    quantityInputs.forEach(function (input) {
        input.addEventListener('input', updateSummary);
        input.addEventListener('change', updateSummary);
    });

    updateSummary();
    filterActiveCategory();
});
</script>
@endsection
