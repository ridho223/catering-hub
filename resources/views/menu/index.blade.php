@extends('layouts.admin')

@section('title', 'Data Menu')

@section('content')
@php
    use Illuminate\Support\Str;

    $filtered = request()->filled('search') || request()->filled('category') || request()->filled('status');
    $statusOpts = ['' => 'Semua', 'Tersedia' => 'Tersedia', 'Habis' => 'Habis'];
@endphp

<style>
    .mnx { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .mnx-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .mnx-top p { margin:0; color:var(--mute); }

    .mnx-sum { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1.1rem; }
    .mnx-tile { background:#fff; border:1px solid var(--line); border-radius:14px; padding:.9rem 1.1rem; display:flex; gap:.8rem; align-items:center; }
    .mnx-tile i { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex:none; }
    .mnx-tile small { color:var(--mute); display:block; line-height:1.2; }
    .mnx-tile strong { font-size:1.35rem; color:var(--ink); line-height:1.2; }
    .i-g { background:#e3f4ea; color:#146c43; } .i-b { background:#e2f0fb; color:#0b5a8a; } .i-r { background:#fde8e8; color:#a52834; } .i-n { background:#eef2ef; color:#4b5a52; }

    /* toolbar */
    .mnx-bar { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; margin-bottom:1rem; }
    .mnx-search { position:relative; flex:1 1 240px; max-width:360px; }
    .mnx-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .mnx-bar .form-control, .mnx-bar .form-select { border-color:var(--line); border-radius:12px; }
    .mnx-search input { padding-left:2.2rem; }
    .mnx-bar .form-select { width:auto; min-width:170px; }
    .mnx-pills { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:3px; position:relative; }
    .mnx-pills > label { position:relative; margin:0; }
    .mnx-pills input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .mnx-pills span { display:block; padding:.35rem .85rem; border-radius:9px; color:var(--mute); font-size:.88rem; font-weight:500; cursor:pointer; }
    .mnx-pills input:checked + span { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .mnx-pills input:focus-visible + span { box-shadow:0 0 0 .2rem rgba(25,135,84,.25); }
    .mnx-toggle { display:inline-flex; border:1px solid var(--line); border-radius:12px; overflow:hidden; background:#fff; margin-left:auto; }
    .mnx-toggle button { border:0; background:transparent; padding:.5rem .75rem; color:var(--mute); }
    .mnx-toggle button.on { background:var(--brand); color:#fff; }
    .mnx-reset { color:var(--mute); font-size:.88rem; text-decoration:none; }
    .mnx-reset:hover { color:#a52834; }

    /* tabel */
    .mnx-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .mnx-table { width:100%; border-collapse:collapse; }
    .mnx-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .mnx-table td { padding:.75rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .mnx-table tbody tr:hover { background:#fafcfa; }
    .mnx-thumb { width:56px; height:56px; border-radius:12px; object-fit:cover; background:var(--soft); flex:none; display:flex; align-items:center; justify-content:center; color:#9aa59c; font-size:1.3rem; }
    .mnx-name { font-weight:600; color:var(--ink); display:block; line-height:1.25; }
    .mnx-desc { color:var(--mute); font-size:.82rem; display:block; max-width:300px; }
    .mnx-price { font-weight:700; color:var(--ink); white-space:nowrap; }
    .mnx-cat { display:inline-block; background:#f0f8f2; color:#236b3e; border-radius:8px; padding:.2rem .6rem; font-size:.8rem; font-weight:600; }

    .mnx-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .mnx-chip--ok { background:#e3f4ea; color:#146c43; }
    .mnx-chip--low { background:#fff1d6; color:#8a5a00; }
    .mnx-chip--bad { background:#fde8e8; color:#a52834; }
    .mnx-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

    .mnx-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .mnx-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    /* grid */
    .mnx-grid { display:none; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:1rem; }
    .mnx[data-view="grid"] .mnx-grid { display:grid; }
    .mnx[data-view="grid"] .mnx-wrap { display:none; }
    .mnx-card { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; display:flex; flex-direction:column; }
    .mnx-card-img { position:relative; aspect-ratio:4/3; background:var(--soft); display:flex; align-items:center; justify-content:center; color:#9aa59c; font-size:2rem; }
    .mnx-card-img img { width:100%; height:100%; object-fit:cover; }
    .mnx-card-img .mnx-chip { position:absolute; top:.6rem; left:.6rem; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.12); }
    .mnx-card-body { padding:.85rem 1rem 1rem; display:flex; flex-direction:column; gap:.35rem; flex:1; }
    .mnx-card-foot { margin-top:auto; display:flex; justify-content:space-between; align-items:center; padding-top:.65rem; border-top:1px dashed var(--line); gap:.5rem; }

    .mnx-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .mnx-empty i { font-size:2.5rem; }

    .mnx-page { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-top:1rem; }
    .mnx-page small { color:var(--mute); }

    /* pagination Bootstrap 5: sembunyikan teks "Showing ... results" bawaan, rata kanan */
    .mnx-page nav .flex-sm-fill { justify-content:flex-end !important; }
    .mnx-page nav .flex-sm-fill > div:first-child { display:none; }
    .mnx-page .pagination { margin:0; }
    .mnx-page .pagination .page-link { color:#267548; border-color:#e6ebe7; }
    .mnx-page .pagination .active .page-link { background:#198754; border-color:#198754; color:#fff; }

    @media (max-width:767px){
        .mnx-table thead { display:none; }
        .mnx-table, .mnx-table tbody, .mnx-table tr, .mnx-table td { display:block; width:100%; }
        .mnx-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .mnx-table td { border:0; padding:.3rem 1rem; }
        .mnx-act { justify-content:flex-start; }
        .mnx-toggle { margin-left:0; }
    }
</style>

<div class="container-fluid mnx" id="mnx" data-view="table">

    <div class="mnx-top">
        <p>Kelola menu, kategori, harga, stok, dan ketersediaan.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-success"><i class="bi bi-tags me-1"></i> Kelola kategori</a>
            <a href="{{ route('menu.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah menu</a>
        </div>
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

    <div class="mnx-sum">
        <div class="mnx-tile"><i class="bi bi-grid i-n"></i><div><small>Total menu</small><strong>{{ $menus->total() }}</strong></div></div>
        <div class="mnx-tile"><i class="bi bi-tags i-b"></i><div><small>Kategori</small><strong>{{ $categories->count() }}</strong></div></div>
        <div class="mnx-tile"><i class="bi bi-check-circle i-g"></i><div><small>Menu tersedia</small><strong>{{ $availableCount }}</strong></div></div>
        <div class="mnx-tile"><i class="bi bi-exclamation-circle i-r"></i><div><small>Stok habis</small><strong>{{ $outOfStockCount }}</strong></div></div>
    </div>

    {{-- FILTER (server-side) --}}
    <form action="{{ route('menu.index') }}" method="GET" class="mnx-bar" id="mnxFilter">
        <div class="mnx-search">
            <i class="bi bi-search"></i>
            <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi, lalu Enter" aria-label="Cari menu">
        </div>

        <select name="category" class="form-select" aria-label="Kategori" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <div class="mnx-pills" role="group" aria-label="Status">
            @foreach ($statusOpts as $value => $label)
                <label>
                    <input type="radio" name="status" value="{{ $value }}" {{ (string) request('status') === (string) $value ? 'checked' : '' }} onchange="this.form.submit()">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>

        @if ($filtered)
            <a href="{{ route('menu.index') }}" class="mnx-reset"><i class="bi bi-x-circle me-1"></i>Reset</a>
        @endif

        <div class="mnx-toggle" role="group" aria-label="Tampilan">
            <button type="button" data-set="table" class="on" title="Tampilan tabel" aria-label="Tampilan tabel"><i class="bi bi-list-ul"></i></button>
            <button type="button" data-set="grid" title="Tampilan kartu" aria-label="Tampilan kartu"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
    </form>

    @if ($menus->isEmpty())
        <div class="mnx-wrap">
            <div class="mnx-empty">
                <i class="bi bi-search"></i>
                <h5 class="mt-2 mb-1 text-dark">{{ $filtered ? 'Menu tidak ditemukan' : 'Belum ada menu' }}</h5>
                <p>{{ $filtered ? 'Coba ubah kata pencarian atau filter.' : 'Tambahkan menu pertama untuk mulai menyusun paket.' }}</p>
                @if ($filtered)
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-success">Reset filter</a>
                @else
                    <a href="{{ route('menu.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah menu</a>
                @endif
            </div>
        </div>
    @else

        {{-- TABEL --}}
        <div class="mnx-wrap">
            <table class="mnx-table">
                <thead>
                    <tr><th>Menu</th><th>Kategori</th><th>Harga / porsi</th><th>Stok</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($menus as $menu)
                    @php
                        $img   = $menu->image ? asset('uploads/menu/' . $menu->image) : null;
                        $ok    = $menu->status === 'Tersedia' && $menu->stock > 0;
                        $stock = $menu->stock <= 0 ? 'bad' : ($menu->stock <= 5 ? 'low' : 'ok');
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($img)
                                    <img class="mnx-thumb" src="{{ $img }}" alt="{{ $menu->name }}" loading="lazy">
                                @else
                                    <span class="mnx-thumb"><i class="bi bi-image"></i></span>
                                @endif
                                <div>
                                    <span class="mnx-name">{{ $menu->name }}</span>
                                    <span class="mnx-desc">{{ Str::limit($menu->description ?: 'Belum ada deskripsi.', 70) }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="mnx-cat">{{ $menu->category->name ?? 'Tanpa kategori' }}</span></td>
                        <td class="mnx-price">Rp{{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td><span class="mnx-chip mnx-chip--{{ $stock }}">{{ $menu->stock > 0 ? $menu->stock . ' porsi' : 'Kosong' }}</span></td>
                        <td>
                            <span class="mnx-chip {{ $ok ? 'mnx-chip--ok' : 'mnx-chip--bad' }}">
                                <span class="mnx-dot"></span>{{ $ok ? 'Tersedia' : 'Habis' }}
                            </span>
                        </td>
                        <td>
                            <div class="mnx-act">
                                <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $menu->name }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('menu.destroy', $menu->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus menu {{ e($menu->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" aria-label="Hapus {{ $menu->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- KARTU --}}
        <div class="mnx-grid">
            @foreach ($menus as $menu)
                @php
                    $img   = $menu->image ? asset('uploads/menu/' . $menu->image) : null;
                    $ok    = $menu->status === 'Tersedia' && $menu->stock > 0;
                    $stock = $menu->stock <= 0 ? 'bad' : ($menu->stock <= 5 ? 'low' : 'ok');
                @endphp
                <article class="mnx-card">
                    <div class="mnx-card-img">
                        @if ($img)<img src="{{ $img }}" alt="{{ $menu->name }}" loading="lazy">@else<i class="bi bi-image"></i>@endif
                        <span class="mnx-chip {{ $ok ? 'mnx-chip--ok' : 'mnx-chip--bad' }}"><span class="mnx-dot"></span>{{ $ok ? 'Tersedia' : 'Habis' }}</span>
                    </div>
                    <div class="mnx-card-body">
                        <div><span class="mnx-cat">{{ $menu->category->name ?? 'Tanpa kategori' }}</span></div>
                        <span class="mnx-name">{{ $menu->name }}</span>
                        <span class="mnx-desc">{{ Str::limit($menu->description ?: 'Belum ada deskripsi.', 60) }}</span>
                        <div class="mnx-card-foot">
                            <div>
                                <div class="mnx-price">Rp{{ number_format($menu->price, 0, ',', '.') }}</div>
                                <span class="mnx-chip mnx-chip--{{ $stock }}">{{ $menu->stock > 0 ? $menu->stock . ' porsi' : 'Kosong' }}</span>
                            </div>
                            <div class="mnx-act">
                                <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $menu->name }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('menu.destroy', $menu->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus menu {{ e($menu->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" aria-label="Hapus {{ $menu->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($menus->hasPages())
            <div class="mnx-page">
                <small>Menampilkan {{ $menus->firstItem() }}–{{ $menus->lastItem() }} dari {{ $menus->total() }} menu</small>
                {{ $menus->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

<script>
(function () {
    const root = document.getElementById('mnx');
    if (!root) return;
    const buttons = root.querySelectorAll('.mnx-toggle button');
    function setView(v) {
        root.dataset.view = v;
        buttons.forEach(b => b.classList.toggle('on', b.dataset.set === v));
        try { localStorage.setItem('mnx-view', v); } catch (e) {}
    }
    buttons.forEach(b => b.addEventListener('click', () => setView(b.dataset.set)));
    try { setView(localStorage.getItem('mnx-view') || 'table'); } catch (e) { setView('table'); }
})();
</script>
@endsection