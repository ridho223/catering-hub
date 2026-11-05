@extends('layouts.admin')
@section('title', 'Paket Catering')

@php
    use Illuminate\Support\Str;

    $items = $packages instanceof \Illuminate\Pagination\AbstractPaginator
        ? $packages->getCollection()
        : collect($packages);

    $totalAvailable = $items->where('status', 'Tersedia')->count();
    $totalOff       = $items->count() - $totalAvailable;
    $avgPrice       = $items->count() ? $items->avg('price') : 0;

    $filters = [
        'semua'     => ['Semua',     'bi-grid'],
        'paket'     => ['Paket',     'bi-box-seam'],
        'prasmanan' => ['Prasmanan', 'bi-egg-fried'],
    ];
@endphp

@section('content')
<style>
    .pkx { --pkx-ink:#1f2a24; --pkx-mute:#6b7770; --pkx-line:#e6ebe7; --pkx-soft:#f5f8f5; --pkx-brand:#198754; }
    .pkx-top { display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .pkx-top h4 { margin:0; font-weight:700; color:var(--pkx-ink); }
    .pkx-top p { margin:.25rem 0 0; color:var(--pkx-mute); }

    .pkx-summary { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1.25rem; }
    .pkx-sum { background:#fff; border:1px solid var(--pkx-line); border-radius:14px; padding:.9rem 1.1rem; }
    .pkx-sum small { color:var(--pkx-mute); display:block; }
    .pkx-sum strong { font-size:1.35rem; color:var(--pkx-ink); }

    .pkx-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .pkx-tabs { display:inline-flex; background:var(--pkx-soft); border:1px solid var(--pkx-line); border-radius:12px; padding:4px; gap:2px; }
    .pkx-tabs a { padding:.4rem .9rem; border-radius:9px; color:var(--pkx-mute); text-decoration:none; font-size:.9rem; font-weight:500; white-space:nowrap; }
    .pkx-tabs a:hover { color:var(--pkx-ink); }
    .pkx-tabs a.on { background:#fff; color:var(--pkx-brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .pkx-tools { display:flex; gap:.5rem; align-items:center; flex:1 1 280px; justify-content:flex-end; }
    .pkx-search { position:relative; flex:1 1 220px; max-width:340px; }
    .pkx-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--pkx-mute); }
    .pkx-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--pkx-line); }
    .pkx-toggle { display:inline-flex; border:1px solid var(--pkx-line); border-radius:12px; overflow:hidden; background:#fff; }
    .pkx-toggle button { border:0; background:transparent; padding:.5rem .75rem; color:var(--pkx-mute); }
    .pkx-toggle button.on { background:var(--pkx-brand); color:#fff; }

    /* tabel */
    .pkx-tablewrap { background:#fff; border:1px solid var(--pkx-line); border-radius:16px; overflow:hidden; }
    .pkx-table { width:100%; border-collapse:collapse; }
    .pkx-table th { background:var(--pkx-soft); color:var(--pkx-mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .pkx-table td { padding:.8rem 1rem; border-top:1px solid var(--pkx-line); vertical-align:middle; }
    .pkx-table tbody tr:hover { background:#fafcfa; }
    .pkx-thumb { width:56px; height:56px; border-radius:12px; object-fit:cover; background:var(--pkx-soft); flex:none; display:flex; align-items:center; justify-content:center; color:var(--pkx-mute); }
    .pkx-name { font-weight:600; color:var(--pkx-ink); text-decoration:none; }
    .pkx-name:hover { color:var(--pkx-brand); }
    .pkx-desc { color:var(--pkx-mute); font-size:.85rem; margin:0; }
    .pkx-price { font-weight:700; color:var(--pkx-ink); white-space:nowrap; }
    .pkx-price small { display:block; font-weight:400; color:var(--pkx-mute); font-size:.75rem; }
    .pkx-menus { color:var(--pkx-mute); font-size:.85rem; max-width:240px; }
    .pkx-menus b { color:var(--pkx-ink); }

    .pkx-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .6rem; border-radius:999px; font-size:.78rem; font-weight:600; }
    .pkx-chip--ok  { background:#e3f4ea; color:#146c43; }
    .pkx-chip--off { background:#eceff1; color:#5f6b72; }
    .pkx-chip--pras { background:#fff1d6; color:#8a5a00; }
    .pkx-chip--paket { background:#e2f0fb; color:#0b5a8a; }
    .pkx-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

    .pkx-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .pkx-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    /* grid */
    .pkx-grid { display:none; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:1rem; }
    .pkx[data-view="grid"] .pkx-grid { display:grid; }
    .pkx[data-view="grid"] .pkx-tablewrap { display:none; }
    .pkx-card { background:#fff; border:1px solid var(--pkx-line); border-radius:16px; overflow:hidden; display:flex; flex-direction:column; }
    .pkx-card-img { position:relative; aspect-ratio:4/3; background:var(--pkx-soft); display:flex; align-items:center; justify-content:center; color:var(--pkx-mute); font-size:2rem; }
    .pkx-card-img img { width:100%; height:100%; object-fit:cover; }
    .pkx-card-img .pkx-chip { position:absolute; top:.6rem; left:.6rem; }
    .pkx-card-body { padding:.9rem 1rem 1rem; display:flex; flex-direction:column; gap:.4rem; flex:1; }
    .pkx-card-foot { margin-top:auto; display:flex; justify-content:space-between; align-items:center; padding-top:.6rem; border-top:1px dashed var(--pkx-line); }

    .pkx-empty { text-align:center; padding:3rem 1rem; color:var(--pkx-mute); }
    .pkx-empty i { font-size:2.5rem; }
    #pkxNoResult { display:none; }
    /* pagination Bootstrap 5: sembunyikan teks "Showing ... results" bawaan, rata kanan */
    .pkx-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .pkx-pg nav .flex-sm-fill > div:first-child { display:none; }
    .pkx-pg .pagination { margin:0; }
    .pkx-pg .pagination .page-link { color:#267548; border-color:#e6ebe7; }
    .pkx-pg .pagination .active .page-link { background:#198754; border-color:#198754; color:#fff; }


    @media (max-width:767px){
        .pkx-table thead { display:none; }
        .pkx-table, .pkx-table tbody, .pkx-table tr, .pkx-table td { display:block; width:100%; }
        .pkx-table tr { padding:.6rem 0; border-top:1px solid var(--pkx-line); }
        .pkx-table td { border:0; padding:.3rem 1rem; }
        .pkx-act { justify-content:flex-start; }
        .pkx-menus { max-width:none; }
    }
</style>

<div class="container-fluid pkx" id="pkx" data-view="table">

    <div class="pkx-top">
        <div>
            <p>Atur paket dan prasmanan yang bisa dipesan customer.</p>
        </div>
        <a href="{{ route('packages.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah paket
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="pkx-summary">
        <div class="pkx-sum"><small>Ditampilkan</small><strong>{{ $items->count() }} paket</strong></div>
        <div class="pkx-sum"><small>Tersedia</small><strong class="text-success">{{ $totalAvailable }}</strong></div>
        <div class="pkx-sum"><small>Tidak tersedia</small><strong>{{ $totalOff }}</strong></div>
        <div class="pkx-sum"><small>Rata-rata harga</small><strong>Rp{{ number_format($avgPrice, 0, ',', '.') }}</strong></div>
    </div>

    <div class="pkx-bar">
        <nav class="pkx-tabs" aria-label="Filter jenis paket">
            @foreach ($filters as $key => [$label, $icon])
                <a href="{{ route('packages.index', ['type' => $key]) }}" class="{{ $type === $key ? 'on' : '' }}">
                    <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="pkx-tools">
            <div class="pkx-search">
                <i class="bi bi-search"></i>
                <input type="search" id="pkxSearch" class="form-control" placeholder="Cari nama paket atau menu" aria-label="Cari paket">
            </div>
            <div class="pkx-toggle" role="group" aria-label="Tampilan">
                <button type="button" data-set="table" class="on" title="Tampilan tabel" aria-label="Tampilan tabel"><i class="bi bi-list-ul"></i></button>
                <button type="button" data-set="grid" title="Tampilan kartu" aria-label="Tampilan kartu"><i class="bi bi-grid-3x3-gap"></i></button>
            </div>
        </div>
    </div>

    @if ($items->isEmpty())
        <div class="pkx-tablewrap">
            <div class="pkx-empty">
                <i class="bi bi-box-seam"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada paket</h5>
                <p>Buat paket pertama supaya customer bisa mulai memesan.</p>
                <a href="{{ route('packages.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah paket</a>
            </div>
        </div>
    @else

        {{-- TABEL --}}
        <div class="pkx-tablewrap">
            <table class="pkx-table">
                <thead>
                    <tr>
                        <th>Paket</th>
                        <th>Jenis</th>
                        <th>Harga</th>
                        <th>Isi paket</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $package)
                    @php
                        $isPras  = $package->package_type === 'prasmanan';
                        $ok      = $package->status === 'Tersedia';
                        $menus   = $package->details->filter(fn ($d) => $d->menu)->pluck('menu.name');
                        $search  = Str::lower($package->name . ' ' . $menus->implode(' '));
                    @endphp
                    <tr class="pkx-item" data-search="{{ $search }}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($package->image)
                                    <img class="pkx-thumb" src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}">
                                @else
                                    <span class="pkx-thumb"><i class="bi bi-image"></i></span>
                                @endif
                                <div>
                                    <a class="pkx-name" href="{{ route('packages.show', $package) }}">{{ $package->name }}</a>
                                    <p class="pkx-desc">{{ Str::limit($package->description, 55) }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="pkx-chip {{ $isPras ? 'pkx-chip--pras' : 'pkx-chip--paket' }}">
                                {{ $isPras ? 'Prasmanan' : 'Paket' }}
                            </span>
                        </td>
                        <td class="pkx-price">
                            Rp{{ number_format($package->price, 0, ',', '.') }}
                            <small>{{ $isPras ? 'per orang' : 'per porsi' }}</small>
                        </td>
                        <td class="pkx-menus">
                            <b>{{ $menus->count() }} menu</b><br>
                            {{ $menus->take(3)->implode(', ') }}{{ $menus->count() > 3 ? ', +' . ($menus->count() - 3) : '' }}
                        </td>
                        <td>
                            <span class="pkx-chip {{ $ok ? 'pkx-chip--ok' : 'pkx-chip--off' }}">
                                <span class="pkx-dot"></span>{{ $ok ? 'Tersedia' : 'Tidak tersedia' }}
                            </span>
                        </td>
                        <td>
                            <div class="pkx-act">
                                <a href="{{ route('packages.show', $package) }}" class="btn btn-outline-success" title="Lihat detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('packages.edit', $package) }}" class="btn btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('packages.destroy', $package) }}" method="POST"
                                      onsubmit="return confirm('Hapus paket {{ e($package->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- GRID --}}
        <div class="pkx-grid">
            @foreach ($items as $package)
                @php
                    $isPras = $package->package_type === 'prasmanan';
                    $ok     = $package->status === 'Tersedia';
                    $menus  = $package->details->filter(fn ($d) => $d->menu)->pluck('menu.name');
                    $search = Str::lower($package->name . ' ' . $menus->implode(' '));
                @endphp
                <article class="pkx-card pkx-item" data-search="{{ $search }}">
                    <a href="{{ route('packages.show', $package) }}" class="pkx-card-img text-decoration-none">
                        @if ($package->image)
                            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}">
                        @else
                            <i class="bi bi-image"></i>
                        @endif
                        <span class="pkx-chip {{ $ok ? 'pkx-chip--ok' : 'pkx-chip--off' }}" style="background:#fff;">
                            <span class="pkx-dot"></span>{{ $ok ? 'Tersedia' : 'Tidak tersedia' }}
                        </span>
                    </a>
                    <div class="pkx-card-body">
                        <div>
                            <span class="pkx-chip {{ $isPras ? 'pkx-chip--pras' : 'pkx-chip--paket' }}">{{ $isPras ? 'Prasmanan' : 'Paket' }}</span>
                        </div>
                        <a class="pkx-name" href="{{ route('packages.show', $package) }}">{{ $package->name }}</a>
                        <p class="pkx-desc">{{ $menus->count() }} menu · {{ $menus->take(2)->implode(', ') }}{{ $menus->count() > 2 ? '…' : '' }}</p>
                        <div class="pkx-card-foot">
                            <div class="pkx-price">
                                Rp{{ number_format($package->price, 0, ',', '.') }}
                                <small>{{ $isPras ? 'per orang' : 'per porsi' }}</small>
                            </div>
                            <div class="pkx-act">
                                <a href="{{ route('packages.edit', $package) }}" class="btn btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('packages.destroy', $package) }}" method="POST"
                                      onsubmit="return confirm('Hapus paket {{ e($package->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div id="pkxNoResult" class="pkx-empty">
            <i class="bi bi-search"></i>
            <h6 class="mt-2 mb-1 text-dark">Tidak ada yang cocok</h6>
            <p class="mb-0">Coba kata kunci lain atau ganti filter jenis paket.</p>
        </div>

        @if (method_exists($packages, 'links'))
            <div class="pkx-pg mt-3">{{ $packages->withQueryString()->links('pagination::bootstrap-5') }}</div>
        @endif
    @endif
</div>

<script>
(function () {
    const root = document.getElementById('pkx');
    if (!root) return;

    // tampilan tabel / grid (diingat di browser)
    const buttons = root.querySelectorAll('.pkx-toggle button');
    function setView(v) {
        root.dataset.view = v;
        buttons.forEach(b => b.classList.toggle('on', b.dataset.set === v));
        try { localStorage.setItem('pkx-view', v); } catch (e) {}
    }
    buttons.forEach(b => b.addEventListener('click', () => setView(b.dataset.set)));
    try { setView(localStorage.getItem('pkx-view') || 'table'); } catch (e) { setView('table'); }

    // pencarian langsung
    const input = document.getElementById('pkxSearch');
    const noResult = document.getElementById('pkxNoResult');
    const items = root.querySelectorAll('.pkx-item');
    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        items.forEach(el => {
            const hit = !q || el.dataset.search.includes(q);
            el.style.display = hit ? '' : 'none';
            if (hit && el.tagName === 'TR') visible++;
        });
        if (noResult) noResult.style.display = visible ? 'none' : 'block';
    });
})();
</script>
@endsection