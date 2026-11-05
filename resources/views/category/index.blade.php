@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
@php
    $items = $categories instanceof \Illuminate\Pagination\AbstractPaginator
        ? $categories->getCollection()
        : collect($categories);

    // Jumlah menu per kategori tampil jika controller memakai ->withCount('menus')
    $hasCounts = $items->isNotEmpty() && array_key_exists('menus_count', $items->first()->getAttributes());
@endphp

<style>
    .ctg { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .ctg-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .ctg-top p { margin:0; color:var(--mute); }

    .ctg-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .ctg-search { position:relative; flex:1 1 240px; max-width:360px; }
    .ctg-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .ctg-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--line); }
    .ctg-count { background:var(--soft); border:1px solid var(--line); border-radius:999px; padding:.3rem .85rem; color:var(--mute); font-size:.86rem; }

    .ctg-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .ctg-table { width:100%; border-collapse:collapse; }
    .ctg-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .ctg-table td { padding:.8rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .ctg-table tbody tr:hover { background:#fafcfa; }
    .ctg-name { display:flex; align-items:center; gap:.75rem; }
    .ctg-av { width:40px; height:40px; border-radius:12px; background:#e3f4ea; color:#146c43; font-weight:700; display:flex; align-items:center; justify-content:center; flex:none; }
    .ctg-name b { color:var(--ink); font-weight:600; }
    .ctg-desc { color:var(--mute); font-size:.9rem; max-width:420px; }
    .ctg-chip { display:inline-flex; background:#e2f0fb; color:#0b5a8a; border-radius:999px; padding:.2rem .7rem; font-size:.8rem; font-weight:600; white-space:nowrap; }
    .ctg-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .ctg-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    .ctg-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .ctg-empty i { font-size:2.5rem; }
    #ctgNoResult { display:none; }

    .ctg-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .ctg-pg nav .flex-sm-fill > div:first-child { display:none; }
    .ctg-pg .pagination { margin:0; }
    .ctg-pg .pagination .page-link { color:#267548; border-color:var(--line); }
    .ctg-pg .pagination .active .page-link { background:var(--brand); border-color:var(--brand); color:#fff; }

    @media (max-width:767px){
        .ctg-table thead { display:none; }
        .ctg-table, .ctg-table tbody, .ctg-table tr, .ctg-table td { display:block; width:100%; }
        .ctg-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .ctg-table td { border:0; padding:.3rem 1rem; }
        .ctg-act { justify-content:flex-start; }
    }
</style>

<div class="container-fluid ctg" id="ctg">

    <div class="ctg-top">
        <p>Kelompokkan menu agar mudah dicari dan dipilih saat menyusun paket.</p>
        <a href="{{ route('categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah kategori
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($items->isNotEmpty())
        <div class="ctg-bar">
            <div class="ctg-search">
                <i class="bi bi-search"></i>
                <input type="search" id="ctgSearch" class="form-control" placeholder="Cari kategori" aria-label="Cari kategori">
            </div>
            <span class="ctg-count">{{ $items->count() }} kategori</span>
        </div>
    @endif

    <div class="ctg-wrap">
        @if ($items->isEmpty())
            <div class="ctg-empty">
                <i class="bi bi-tags"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada kategori</h5>
                <p>Buat kategori pertama, misalnya Nasi, Lauk, atau Minuman.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah kategori</a>
            </div>
        @else
            <table class="ctg-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        @if ($hasCounts)<th>Menu</th>@endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $category)
                    <tr class="ctg-item" data-search="{{ \Illuminate\Support\Str::lower($category->name . ' ' . $category->description) }}">
                        <td>
                            <div class="ctg-name">
                                <span class="ctg-av">{{ strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                                <b>{{ $category->name }}</b>
                            </div>
                        </td>
                        <td class="ctg-desc">{{ \Illuminate\Support\Str::limit($category->description ?: 'Belum ada deskripsi.', 90) }}</td>
                        @if ($hasCounts)
                            <td><span class="ctg-chip">{{ $category->menus_count }} menu</span></td>
                        @endif
                        <td>
                            <div class="ctg-act">
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $category->name }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus kategori {{ e($category->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" aria-label="Hapus {{ $category->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="ctg-empty" id="ctgNoResult">
                <i class="bi bi-search"></i>
                <h6 class="mt-2 mb-1 text-dark">Kategori tidak ditemukan</h6>
                <p class="mb-0">Coba kata kunci lain.</p>
            </div>
        @endif
    </div>

    @if (method_exists($categories, 'links'))
        <div class="ctg-pg mt-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
(function () {
    const input = document.getElementById('ctgSearch');
    if (!input) return;
    const rows = document.querySelectorAll('.ctg-item');
    const none = document.getElementById('ctgNoResult');
    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const ok = !q || r.dataset.search.includes(q);
            r.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        none.style.display = visible ? 'none' : 'block';
    });
})();
</script>
@endsection