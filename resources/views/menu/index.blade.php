
@extends('layouts.admin')

@section('title', 'Data Menu')

@section('content')
<style>
    .menu-admin {
        --ma-green: #198754;
        --ma-border: #e7ece8;
        color: #253129;
    }

    .menu-admin .page-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .menu-admin .page-heading h2 {
        font-size: 1.55rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .menu-admin .page-heading p {
        margin: 0;
        color: #748078;
    }

    .menu-admin .stat-card {
        height: 100%;
        padding: 20px;
        border: 1px solid var(--ma-border);
        border-radius: 15px;
        background: #fff;
    }

    .menu-admin .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        background: #eaf6ed;
        color: var(--ma-green);
        font-size: 1.25rem;
    }

    .menu-admin .stat-label {
        display: block;
        margin-top: 14px;
        color: #748078;
        font-size: .84rem;
    }

    .menu-admin .stat-number {
        display: block;
        margin-top: 3px;
        font-size: 1.6rem;
        font-weight: 800;
    }

    .menu-admin .panel {
        border: 1px solid var(--ma-border);
        border-radius: 16px;
        background: #fff;
        overflow: hidden;
    }

    .menu-admin .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 20px 22px;
        border-bottom: 1px solid var(--ma-border);
    }

    .menu-admin .panel-header h5 {
        margin: 0;
        font-weight: 750;
    }

    .menu-admin .filter-area {
        padding: 18px 22px;
        border-bottom: 1px solid var(--ma-border);
        background: #fbfcfb;
    }

    .menu-admin .form-control,
    .menu-admin .form-select {
        min-height: 42px;
        border-color: #dfe6e1;
        border-radius: 9px;
        font-size: .9rem;
    }

    .menu-admin .form-control:focus,
    .menu-admin .form-select:focus {
        border-color: #86b99a;
        box-shadow: 0 0 0 .18rem rgba(25, 135, 84, .10);
    }

    .menu-admin .table {
        margin-bottom: 0;
    }

    .menu-admin .table thead th {
        padding: 14px 16px;
        background: #f4f8f5;
        color: #536258;
        font-size: .77rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: nowrap;
        border-bottom: 1px solid var(--ma-border);
    }

    .menu-admin .table tbody td {
        padding: 15px 16px;
        border-color: #eef1ee;
        vertical-align: middle;
    }

    .menu-admin .table tbody tr:hover {
        background: #fbfdfb;
    }

    .menu-admin .menu-photo {
        width: 64px;
        height: 58px;
        object-fit: cover;
        border: 1px solid #e8ede9;
        border-radius: 10px;
    }

    .menu-admin .photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 58px;
        border: 1px solid #e8ede9;
        border-radius: 10px;
        background: #f5f7f5;
        color: #9aa59c;
        font-size: 1.3rem;
    }

    .menu-admin .menu-name {
        display: block;
        margin-bottom: 4px;
        color: #26342a;
        font-weight: 750;
    }

    .menu-admin .menu-description {
        display: block;
        max-width: 260px;
        color: #7b867e;
        font-size: .8rem;
        line-height: 1.5;
    }

    .menu-admin .category-badge {
        display: inline-block;
        max-width: 190px;
        padding: 6px 10px;
        border: 1px solid #dcece0;
        border-radius: 8px;
        background: #f0f8f2;
        color: #236b3e;
        font-size: .78rem;
        font-weight: 650;
        white-space: normal;
    }

    .menu-admin .stock-badge,
    .menu-admin .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .menu-admin .stock-good,
    .menu-admin .status-available {
        background: #e9f7ed;
        color: #187447;
    }

    .menu-admin .stock-low {
        background: #fff4d9;
        color: #8b6200;
    }

    .menu-admin .stock-empty,
    .menu-admin .status-empty {
        background: #fdecec;
        color: #b42323;
    }

    .menu-admin .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 9px;
    }

    .menu-admin .empty-state {
        padding: 55px 20px;
        text-align: center;
    }

    .menu-admin .empty-state i {
        color: #a3b2a6;
        font-size: 2.6rem;
    }

    .menu-admin .pagination {
        margin-bottom: 0;
    }

    .menu-admin .pagination .page-link {
        color: #267548;
        border-color: #e2e9e3;
    }

    .menu-admin .pagination .active .page-link {
        background: var(--ma-green);
        border-color: var(--ma-green);
        color: #fff;
    }

    @media (max-width: 767px) {
        .menu-admin .page-heading h2 {
            font-size: 1.35rem;
        }

        .menu-admin .panel-header,
        .menu-admin .filter-area {
            padding: 16px;
        }
    }
</style>

<div class="container-fluid menu-admin py-2">

    {{-- HEADER --}}
    <div class="page-heading">
        <div>
            <h2>Data Menu Catering</h2>
            <p>Kelola menu, kategori, harga, stok, dan ketersediaan menu.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('categories.index') }}"
               class="btn btn-outline-success">
                <i class="bi bi-tags me-1"></i>
                Kelola Kategori
            </a>

            <a href="{{ route('menu.create') }}"
               class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Menu
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"
             role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-grid"></i>
                </div>
                <span class="stat-label">Total menu</span>
                <span class="stat-number">{{ $menus->total() }}</span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <span class="stat-label">Total kategori</span>
                <span class="stat-number">{{ $categories->count() }}</span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <span class="stat-label">Menu tersedia</span>
                <span class="stat-number">{{ $availableCount }}</span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <span class="stat-label">Stok habis</span>
                <span class="stat-number">{{ $outOfStockCount }}</span>
            </div>
        </div>

    </div>

    {{-- TABEL MENU --}}
    <div class="panel shadow-sm">

        <div class="panel-header">
            <div>
                <h5>Daftar Menu</h5>
                <small class="text-muted">
                    Gunakan pencarian atau filter untuk menemukan menu.
                </small>
            </div>
            <span class="badge bg-light text-dark border">
                {{ $menus->firstItem() ?? 0 }}–{{ $menus->lastItem() ?? 0 }}
                dari {{ $menus->total() }} data
            </span>
        </div>

        {{-- FILTER SERVER-SIDE --}}
        <div class="filter-area">
            <form action="{{ route('menu.index') }}" method="GET">
                <div class="row g-2 align-items-end">

                    <div class="col-lg-5">
                        <label for="search" class="form-label small fw-semibold">
                            Cari menu
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Nama menu atau deskripsi...">
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <label for="category" class="form-label small fw-semibold">
                            Kategori
                        </label>
                        <select name="category" id="category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected((string) request('category') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 col-lg-2">
                        <label for="status" class="form-label small fw-semibold">
                            Status
                        </label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="Tersedia"
                                @selected(request('status') === 'Tersedia')>
                                Tersedia
                            </option>
                            <option value="Habis"
                                @selected(request('status') === 'Habis')>
                                Habis
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="{{ route('menu.index') }}"
                           class="btn btn-outline-secondary"
                           title="Reset filter"
                           aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Foto</th>
                        <th>Informasi Menu</th>
                        <th>Kategori</th>
                        <th>Harga / Porsi</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($menus as $menu)
                        <tr>
                            <td class="text-muted">
                                {{ $menus->firstItem() + $loop->index }}
                            </td>

                            <td>
                                @if($menu->image)
                                    <img
                                        src="{{ asset('uploads/menu/' . $menu->image) }}"
                                        alt="{{ $menu->name }}"
                                        class="menu-photo"
                                        loading="lazy">
                                @else
                                    <div class="photo-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="menu-name">{{ $menu->name }}</span>
                                <span class="menu-description">
                                    {{ \Illuminate\Support\Str::limit($menu->description ?: 'Belum ada deskripsi menu.', 85) }}
                                </span>
                            </td>

                            <td>
                                <span class="category-badge">
                                    {{ $menu->category->name ?? 'Belum dikategorikan' }}
                                </span>
                            </td>

                            <td class="fw-semibold text-nowrap">
                                Rp{{ number_format($menu->price, 0, ',', '.') }}
                            </td>

                            <td>
                                @if($menu->stock <= 0)
                                    <span class="stock-badge stock-empty">0 porsi</span>
                                @elseif($menu->stock <= 5)
                                    <span class="stock-badge stock-low">
                                        {{ $menu->stock }} porsi
                                    </span>
                                @else
                                    <span class="stock-badge stock-good">
                                        {{ $menu->stock }} porsi
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($menu->status === 'Tersedia' && $menu->stock > 0)
                                    <span class="status-badge status-available">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="status-badge status-empty">
                                        <i class="bi bi-x-circle-fill"></i>
                                        Habis
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('menu.edit', $menu->id) }}"
                                       class="btn btn-outline-warning btn-sm action-btn"
                                       title="Edit menu"
                                       aria-label="Edit {{ $menu->name }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('menu.destroy', $menu->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus menu {{ addslashes($menu->name) }}?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm action-btn"
                                                title="Hapus menu"
                                                aria-label="Hapus {{ $menu->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-search"></i>
                                    <h5 class="fw-bold mt-3">Menu tidak ditemukan</h5>
                                    <p class="text-muted mb-3">
                                        Coba ubah kata pencarian atau filter kategori.
                                    </p>
                                    <a href="{{ route('menu.index') }}"
                                       class="btn btn-outline-success">
                                        Reset Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($menus->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-3 border-top">
                <small class="text-muted">
                    Menampilkan {{ $menus->firstItem() }} sampai
                    {{ $menus->lastItem() }} dari {{ $menus->total() }} menu.
                </small>
                {{ $menus->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
