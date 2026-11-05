@extends('layouts.admin')

@section('title', 'Detail Paket')

@section('content')
@php
    $isPrasmanan = $package->package_type === 'prasmanan';
    $available   = $package->status === 'Tersedia';

    $validDetails   = $package->details->filter(fn ($detail) => $detail->menu);
    $groupedDetails = $validDetails->groupBy(
        fn ($detail) => $detail->menu->category?->name ?? 'Tanpa kategori'
    );
    $unit = $isPrasmanan ? 'orang' : 'porsi';
@endphp

<style>
    .pkd { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .pkd-back { color:var(--mute); text-decoration:none; font-size:.9rem; }
    .pkd-back:hover { color:var(--brand); }

    /* ===== HEADER HORIZONTAL ===== */
    .pkd-top { display:grid; grid-template-columns:300px 1fr; gap:1.5rem; background:#fff; border:1px solid var(--line); border-radius:20px; padding:1rem; margin:.75rem 0 1.25rem; }
    .pkd-photo { border-radius:14px; overflow:hidden; background:var(--soft); min-height:220px; position:relative; }
    .pkd-photo img { width:100%; height:100%; object-fit:cover; display:block; position:absolute; inset:0; }
    .pkd-photo-empty { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.3rem; color:var(--mute); font-size:.85rem; }
    .pkd-photo-empty i { font-size:2rem; }

    .pkd-main { display:flex; flex-direction:column; padding:.4rem .5rem .4rem 0; min-width:0; }
    .pkd-tags { display:flex; gap:.4rem; flex-wrap:wrap; margin-bottom:.6rem; }
    .pkd-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; }
    .pkd-chip--ok { background:#e3f4ea; color:#146c43; }
    .pkd-chip--off { background:#eceff1; color:#5f6b72; }
    .pkd-chip--pras { background:#fff1d6; color:#8a5a00; }
    .pkd-chip--paket { background:#e2f0fb; color:#0b5a8a; }
    .pkd-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

    .pkd-title { font-size:1.6rem; font-weight:800; color:var(--ink); margin:0 0 .35rem; line-height:1.2; }
    .pkd-desc { color:var(--mute); margin:0; max-width:70ch; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }

    .pkd-bottom { margin-top:auto; padding-top:1rem; display:flex; flex-wrap:wrap; gap:1rem 1.5rem; align-items:flex-end; justify-content:space-between; }
    .pkd-price small { display:block; color:var(--mute); font-size:.82rem; }
    .pkd-price strong { font-size:1.9rem; font-weight:800; color:var(--brand); line-height:1.1; }
    .pkd-meta { display:flex; gap:1.25rem; color:var(--mute); font-size:.9rem; }
    .pkd-meta b { color:var(--ink); font-size:1.1rem; display:block; line-height:1.2; }
    .pkd-calc { display:flex; align-items:center; gap:.5rem; background:#f3faf6; border:1px dashed #b9d8c7; border-radius:12px; padding:.5rem .75rem; }
    .pkd-calc input { width:80px; }
    .pkd-calc .total { font-weight:700; color:var(--ink); min-width:110px; text-align:right; }

    /* ===== ISI PAKET ===== */
    .pkd-sec-head { display:flex; justify-content:space-between; align-items:baseline; gap:1rem; margin-bottom:.75rem; flex-wrap:wrap; }
    .pkd-sec-head h5 { margin:0; font-weight:700; color:var(--ink); }
    .pkd-sec-head span { color:var(--mute); font-size:.9rem; }

    .pkd-cols { column-count:3; column-gap:1rem; }
    .pkd-cat { break-inside:avoid; background:#fff; border:1px solid var(--line); border-radius:16px; padding:.9rem 1rem; margin-bottom:1rem; }
    .pkd-cat-title { display:flex; align-items:center; justify-content:space-between; margin-bottom:.5rem; }
    .pkd-cat-title h6 { margin:0; font-weight:700; color:var(--ink); }
    .pkd-cat-title span { background:var(--soft); color:var(--mute); border-radius:999px; font-size:.75rem; padding:.05rem .55rem; }

    .pkd-list { list-style:none; margin:0; padding:0; }
    .pkd-list li { display:flex; gap:.55rem; padding:.45rem 0; border-top:1px solid var(--line); }
    .pkd-list li:first-child { border-top:0; }
    .pkd-list i { color:var(--brand); font-size:.85rem; margin-top:.2rem; flex:none; }
    .pkd-list strong { color:var(--ink); font-weight:600; font-size:.92rem; display:block; line-height:1.3; }
    .pkd-list small { color:var(--mute); display:block; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

    .pkd-empty { text-align:center; padding:2.5rem 1rem; color:var(--mute); background:#fff; border:1px solid var(--line); border-radius:16px; }
    .pkd-empty i { font-size:2.2rem; }

    @media (max-width:1199px){ .pkd-cols { column-count:2; } }
    @media (max-width:767px){
        .pkd-top { grid-template-columns:1fr; }
        .pkd-photo { min-height:200px; }
        .pkd-main { padding:0 .25rem .5rem; }
        .pkd-cols { column-count:1; }
    }
</style>

<div class="container-fluid pkd pb-4">

    <a href="{{ route('packages.index') }}" class="pkd-back"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar paket</a>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- HEADER: foto | nama, deskripsi, harga --}}
    <div class="pkd-top">
        <div class="pkd-photo">
            @if ($package->image)
                <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}">
            @else
                <div class="pkd-photo-empty"><i class="bi bi-image"></i><span>Foto belum ditambahkan</span></div>
            @endif
        </div>

        <div class="pkd-main">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="pkd-tags">
                    <span class="pkd-chip {{ $isPrasmanan ? 'pkd-chip--pras' : 'pkd-chip--paket' }}">
                        <i class="bi {{ $isPrasmanan ? 'bi-egg-fried' : 'bi-box-seam' }}"></i>
                        {{ $isPrasmanan ? 'Prasmanan' : 'Paket catering' }}
                    </span>
                    <span class="pkd-chip {{ $available ? 'pkd-chip--ok' : 'pkd-chip--off' }}">
                        <span class="pkd-dot"></span>{{ $available ? 'Tersedia' : 'Tidak tersedia' }}
                    </span>
                </div>

                <div class="d-flex gap-2 flex-none">
                    <a href="{{ route('packages.edit', $package) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Ubah
                    </a>
                    <form action="{{ route('packages.destroy', $package) }}" method="POST"
                          onsubmit="return confirm('Hapus paket {{ e($package->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus paket"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>

            <h3 class="pkd-title">{{ $package->name }}</h3>
            <p class="pkd-desc">{{ $package->description ?: 'Belum ada deskripsi paket.' }}</p>

            <div class="pkd-bottom">
                <div class="pkd-price">
                    <small>Harga per {{ $unit }}</small>
                    <strong>Rp{{ number_format($package->price, 0, ',', '.') }}</strong>
                </div>

                <div class="pkd-meta">
                    <div><b>{{ $validDetails->count() }}</b>menu</div>
                    <div><b>{{ $groupedDetails->count() }}</b>kategori</div>
                </div>

                <div class="pkd-calc">
                    <label for="pkdQty" class="small text-muted mb-0">Estimasi</label>
                    <input type="number" id="pkdQty" class="form-control form-control-sm" min="1" value="50" inputmode="numeric" aria-label="Jumlah {{ $unit }}">
                    <span class="small text-muted">{{ $unit }}</span>
                    <span class="total" id="pkdTotal">Rp0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ISI PAKET: kolom per kategori, tidak memanjang ke bawah --}}
    <div class="pkd-sec-head">
        <h5>Isi paket</h5>
        <span>{{ $validDetails->count() }} menu dalam {{ $groupedDetails->count() }} kategori</span>
    </div>

    @forelse ($groupedDetails as $categoryName => $details)
        @if ($loop->first)<div class="pkd-cols">@endif

        <section class="pkd-cat">
            <div class="pkd-cat-title">
                <h6>{{ $categoryName }}</h6>
                <span>{{ $details->count() }}</span>
            </div>
            <ul class="pkd-list">
                @foreach ($details as $detail)
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>{{ $detail->menu->name }}</strong>
                            @if ($detail->menu->description)
                                <small title="{{ $detail->menu->description }}">{{ $detail->menu->description }}</small>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        @if ($loop->last)</div>@endif
    @empty
        <div class="pkd-empty">
            <i class="bi bi-inbox"></i>
            <h6 class="mt-2 mb-1 text-dark">Paket ini belum punya menu</h6>
            <p>Pilih menu lewat halaman ubah paket.</p>
            <a href="{{ route('packages.edit', $package) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah menu
            </a>
        </div>
    @endforelse
</div>

<script>
(function () {
    const price = {{ (float) $package->price }};
    const qty = document.getElementById('pkdQty');
    const total = document.getElementById('pkdTotal');
    const fmt = n => 'Rp' + Math.round(n).toLocaleString('id-ID');
    function calc() { total.textContent = fmt(price * Math.max(parseInt(qty.value) || 0, 0)); }
    qty.addEventListener('input', calc);
    calc();
})();
</script>
@endsection