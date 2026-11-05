@extends('layouts.admin')

@section('title', 'Ubah Paket')

@section('content')
@php
    use Illuminate\Support\Str;

    $type       = old('package_type', $package->package_type);
    $status     = old('status', $package->status);
    $checked    = collect(old('menus', $selectedMenus))->map(fn ($v) => (int) $v)->all();
    $totalMenus = $menus->flatten()->count();
@endphp

<style>
    .pkf { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .pkf-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; }
    .pkf-card h5 { margin:0; font-weight:700; color:var(--ink); }
    .pkf-card .sub { color:var(--mute); font-size:.88rem; margin:.15rem 0 1rem; }
    .pkf .form-label { font-weight:600; color:var(--ink); margin-bottom:.35rem; }
    .pkf .form-control, .pkf .form-select { border-color:var(--line); border-radius:10px; }
    .pkf .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }

    .pkf-seg { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .pkf-seg input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .pkf-seg label { border:1px solid var(--line); border-radius:12px; padding:.6rem .75rem; cursor:pointer; display:flex; align-items:center; gap:.5rem; color:var(--mute); font-weight:500; background:#fff; }
    .pkf-seg input:checked + label { border-color:var(--brand); background:var(--brand-soft); color:#146c43; }
    .pkf-seg input:focus-visible + label { box-shadow:0 0 0 .2rem rgba(25,135,84,.25); }

    .pkf-drop { border:1.5px dashed #b9d8c7; background:#f7fbf8; border-radius:14px; padding:.9rem; display:flex; gap:.9rem; align-items:center; cursor:pointer; }
    .pkf-drop:hover { background:#f0f8f3; }
    .pkf-drop img { width:84px; height:64px; object-fit:cover; border-radius:10px; display:none; flex:none; }
    .pkf-drop img.on { display:block; }
    .pkf-drop-icon { width:84px; height:64px; border-radius:10px; background:#fff; border:1px solid var(--line); display:flex; align-items:center; justify-content:center; color:var(--mute); font-size:1.5rem; flex:none; }
    .pkf-drop-icon.off { display:none; }
    .pkf-drop b { color:var(--ink); display:block; }
    .pkf-drop small { color:var(--mute); }
    .pkf-drop input { display:none; }

    .pkf-tools { display:flex; gap:.6rem; margin-bottom:.75rem; }
    .pkf-search { position:relative; flex:1; }
    .pkf-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .pkf-search input { padding-left:2.2rem; }
    .pkf-total { background:var(--brand-soft); color:#146c43; border-radius:999px; padding:.3rem .85rem; font-weight:600; font-size:.85rem; white-space:nowrap; height:fit-content; }

    .pkf-tabs { display:flex; gap:.4rem; overflow-x:auto; padding-bottom:.5rem; margin-bottom:.5rem; scrollbar-width:thin; }
    .pkf-tab { flex:none; border:1px solid var(--line); background:#fff; color:var(--mute); border-radius:999px; padding:.3rem .85rem; font-size:.86rem; display:inline-flex; gap:.4rem; align-items:center; }
    .pkf-tab.on { background:var(--brand); border-color:var(--brand); color:#fff; }
    .pkf-tab em { font-style:normal; font-size:.74rem; background:rgba(0,0,0,.08); border-radius:999px; padding:0 .45rem; min-width:1.3rem; text-align:center; }
    .pkf-tab.on em { background:rgba(255,255,255,.25); }
    .pkf-tab em.has { background:var(--brand); color:#fff; }
    .pkf-tab.on em.has { background:#fff; color:var(--brand); }

    .pkf-scroll { max-height:380px; overflow-y:auto; padding-right:.35rem; border-top:1px solid var(--line); padding-top:.75rem; }
    .pkf-group { margin-bottom:.9rem; }
    .pkf-group h6 { font-size:.88rem; color:var(--mute); font-weight:600; margin:0 0 .5rem; }
    .pkf-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:.5rem; }
    .pkf-menu-wrap, .pkf-seg > div { position:relative; }
    .pkf-menu-wrap input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .pkf-menu { display:flex; gap:.55rem; align-items:flex-start; border:1px solid var(--line); border-radius:12px; padding:.55rem .7rem; cursor:pointer; background:#fff; height:100%; }
    .pkf-menu:hover { border-color:#b9d8c7; }
    .pkf-menu .tick { width:18px; height:18px; border-radius:6px; border:1.5px solid #c5cfc9; flex:none; margin-top:.1rem; display:flex; align-items:center; justify-content:center; color:transparent; font-size:.72rem; }
    .pkf-menu strong { display:block; font-weight:600; color:var(--ink); font-size:.9rem; line-height:1.3; }
    .pkf-menu small { color:var(--mute); }
    .pkf-menu-wrap input:checked + .pkf-menu { border-color:var(--brand); background:var(--brand-soft); }
    .pkf-menu-wrap input:checked + .pkf-menu .tick { background:var(--brand); border-color:var(--brand); color:#fff; }
    .pkf-menu-wrap input:focus-visible + .pkf-menu { box-shadow:0 0 0 .2rem rgba(25,135,84,.25); }

    .pkf-chosen { margin-top:.9rem; padding-top:.9rem; border-top:1px solid var(--line); }
    .pkf-chosen > small { color:var(--mute); display:block; margin-bottom:.4rem; }
    .pkf-chips { display:flex; flex-wrap:wrap; gap:.35rem; max-height:96px; overflow-y:auto; }
    .pkf-chip { border:0; background:var(--brand-soft); color:#146c43; border-radius:999px; padding:.25rem .55rem .25rem .75rem; font-size:.82rem; font-weight:500; display:inline-flex; gap:.35rem; align-items:center; }
    .pkf-chip:hover { background:#d3ecdd; }
    .pkf-none { color:var(--mute); font-size:.88rem; }
    .pkf-empty { text-align:center; color:var(--mute); padding:2rem 1rem; display:none; }

    .pkf-bar { position:sticky; bottom:0; z-index:5; margin-top:1.25rem; background:rgba(255,255,255,.94); backdrop-filter:blur(6px); border:1px solid var(--line); border-radius:16px; padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .pkf-bar span { color:var(--mute); font-size:.9rem; }
</style>

<div class="container-fluid pb-3">


    <form id="pkf" class="pkf" action="{{ route('packages.update', $package) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <b>Perubahan belum bisa disimpan.</b> Periksa isian berikut:
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">

            {{-- KIRI: informasi paket --}}
            <div class="col-lg-5">
                <div class="pkf-card">
                    <h5>Informasi paket</h5>
                    <p class="sub">Data yang dilihat customer di halaman pemesanan.</p>

                    <div class="mb-3">
                        <label class="form-label" for="pkfName">Nama paket</label>
                        <input type="text" id="pkfName" name="name" value="{{ old('name', $package->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Nasi Kotak A" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <span class="form-label d-block">Jenis paket</span>
                        <div class="pkf-seg">
                            <div>
                                <input type="radio" name="package_type" id="typePaket" value="paket" {{ $type === 'paket' ? 'checked' : '' }}>
                                <label for="typePaket"><i class="bi bi-box-seam"></i> Paket catering</label>
                            </div>
                            <div>
                                <input type="radio" name="package_type" id="typePras" value="prasmanan" {{ $type === 'prasmanan' ? 'checked' : '' }}>
                                <label for="typePras"><i class="bi bi-egg-fried"></i> Prasmanan</label>
                            </div>
                        </div>
                        @error('package_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pkfPrice">Harga per <span id="pkfUnit">{{ $type === 'prasmanan' ? 'orang' : 'porsi' }}</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="pkfPrice" name="price" value="{{ old('price', $package->price) }}"
                                   class="form-control @error('price') is-invalid @enderror" min="0" placeholder="25000" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="form-label d-block">Status</span>
                        <div class="pkf-seg">
                            <div>
                                <input type="radio" name="status" id="stOn" value="Tersedia" {{ $status === 'Tersedia' ? 'checked' : '' }}>
                                <label for="stOn"><i class="bi bi-check-circle"></i> Tersedia</label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="stOff" value="Tidak Tersedia" {{ $status === 'Tidak Tersedia' ? 'checked' : '' }}>
                                <label for="stOff"><i class="bi bi-slash-circle"></i> Tidak tersedia</label>
                            </div>
                        </div>
                        @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pkfDesc">Deskripsi</label>
                        <textarea id="pkfDesc" name="description" class="form-control" rows="3"
                                  placeholder="Ceritakan singkat isi dan keunggulan paket ini">{{ old('description', $package->description) }}</textarea>
                    </div>

                    <div>
                        <span class="form-label d-block">Foto paket</span>
                        <label class="pkf-drop" for="pkfImage">
                            <img id="pkfPreview" alt="Pratinjau foto"
                                 @if ($package->image) src="{{ asset('storage/' . $package->image) }}" class="on" @endif>
                            <span class="pkf-drop-icon {{ $package->image ? 'off' : '' }}" id="pkfIcon"><i class="bi bi-cloud-arrow-up"></i></span>
                            <span>
                                <b id="pkfFileName">{{ $package->image ? 'Ganti foto' : 'Pilih foto' }}</b>
                                <small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                            </span>
                            <input type="file" id="pkfImage" name="image" accept="image/*">
                        </label>
                        @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- KANAN: isi paket --}}
            <div class="col-lg-7">
                <div class="pkf-card" id="pkfMenuCard">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <h5>Isi paket</h5>
                            <p class="sub">Pilih menu yang masuk ke paket ini.</p>
                        </div>
                        <span class="pkf-total"><span id="pkfCount">0</span> dipilih</span>
                    </div>

                    @if ($totalMenus === 0)
                        <div class="alert alert-warning mb-0">Belum ada menu. Tambahkan menu dan kategorinya terlebih dahulu.</div>
                    @else
                        <div class="pkf-tools">
                            <div class="pkf-search">
                                <i class="bi bi-search"></i>
                                <input type="search" id="pkfSearch" class="form-control" placeholder="Cari menu" aria-label="Cari menu">
                            </div>
                        </div>

                        <div class="pkf-tabs" id="pkfTabs" aria-label="Kategori menu">
                            <button type="button" class="pkf-tab on" data-cat="all">Semua <em>{{ $totalMenus }}</em></button>
                            @foreach ($menus as $categoryName => $categoryMenus)
                                <button type="button" class="pkf-tab" data-cat="{{ Str::slug($categoryName) }}">
                                    {{ $categoryName }} <em data-count-for="{{ Str::slug($categoryName) }}">0</em>
                                </button>
                            @endforeach
                        </div>

                        <div class="pkf-scroll">
                            @foreach ($menus as $categoryName => $categoryMenus)
                                <div class="pkf-group" data-cat="{{ Str::slug($categoryName) }}">
                                    <h6>{{ $categoryName }}</h6>
                                    <div class="pkf-grid">
                                        @foreach ($categoryMenus as $menu)
                                            <div class="pkf-menu-wrap" data-cat="{{ Str::slug($categoryName) }}" data-name="{{ Str::lower($menu->name) }}">
                                                <input type="checkbox" name="menus[]" value="{{ $menu->id }}" id="menu{{ $menu->id }}"
                                                       data-label="{{ $menu->name }}" {{ in_array($menu->id, $checked) ? 'checked' : '' }}>
                                                <label class="pkf-menu" for="menu{{ $menu->id }}">
                                                    <span class="tick"><i class="bi bi-check-lg"></i></span>
                                                    <span>
                                                        <strong>{{ $menu->name }}</strong>
                                                        <small>Rp{{ number_format($menu->price, 0, ',', '.') }}</small>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="pkf-empty" id="pkfNoMatch">
                                <i class="bi bi-search fs-3"></i>
                                <p class="mb-0 mt-1">Tidak ada menu yang cocok.</p>
                            </div>
                        </div>

                        <div class="pkf-chosen">
                            <small>Menu terpilih</small>
                            <div class="pkf-chips" id="pkfChips"></div>
                        </div>
                    @endif

                    <div class="text-danger small mt-2 {{ ($errors->has('menus') || $errors->has('menus.*')) ? '' : 'd-none' }}" id="pkfMenuErr">
                        {{ $errors->first('menus') ?: ($errors->first('menus.*') ?: 'Pilih minimal 1 menu untuk paket ini.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="pkf-bar">
            <span>Perubahan baru tersimpan setelah kamu klik simpan.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('packages.show', $package) }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan perubahan</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const root = document.getElementById('pkf');
    if (!root) return;

    // label satuan harga mengikuti jenis paket
    const unit = document.getElementById('pkfUnit');
    root.querySelectorAll('input[name="package_type"]').forEach(r => r.addEventListener('change', () => {
        unit.textContent = r.value === 'prasmanan' ? 'orang' : 'porsi';
    }));

    // pratinjau foto
    const file = document.getElementById('pkfImage');
    const prev = document.getElementById('pkfPreview');
    const icon = document.getElementById('pkfIcon');
    const fname = document.getElementById('pkfFileName');
    file.addEventListener('change', () => {
        const f = file.files[0];
        if (!f) return;
        prev.src = URL.createObjectURL(f);
        prev.classList.add('on');
        icon.classList.add('off');
        fname.textContent = f.name;
    });

    // pemilih menu
    const boxes  = [...root.querySelectorAll('input[name="menus[]"]')];
    const wraps  = [...root.querySelectorAll('.pkf-menu-wrap')];
    const groups = [...root.querySelectorAll('.pkf-group')];
    const tabs   = [...root.querySelectorAll('.pkf-tab')];
    const search = document.getElementById('pkfSearch');
    const noMatch = document.getElementById('pkfNoMatch');
    const chips  = document.getElementById('pkfChips');
    const count  = document.getElementById('pkfCount');
    const err    = document.getElementById('pkfMenuErr');
    let cat = 'all';

    function applyFilter() {
        const q = search ? search.value.trim().toLowerCase() : '';
        wraps.forEach(w => {
            const okCat = cat === 'all' || w.dataset.cat === cat;
            const okQ = !q || w.dataset.name.includes(q);
            w.style.display = (okCat && okQ) ? '' : 'none';
        });
        let any = false;
        groups.forEach(g => {
            const visible = [...g.querySelectorAll('.pkf-menu-wrap')].some(w => w.style.display !== 'none');
            g.style.display = visible ? '' : 'none';
            if (visible) any = true;
        });
        if (noMatch) noMatch.style.display = any ? 'none' : 'block';
    }

    function sync() {
        const chosen = boxes.filter(b => b.checked);
        count.textContent = chosen.length;
        if (chosen.length) err.classList.add('d-none');

        root.querySelectorAll('[data-count-for]').forEach(em => {
            const n = chosen.filter(b => b.closest('.pkf-menu-wrap').dataset.cat === em.dataset.countFor).length;
            em.textContent = n;
            em.classList.toggle('has', n > 0);
        });

        if (!chips) return;
        chips.innerHTML = '';
        if (!chosen.length) {
            const p = document.createElement('span');
            p.className = 'pkf-none';
            p.textContent = 'Belum ada menu dipilih.';
            chips.appendChild(p);
            return;
        }
        chosen.forEach(b => {
            const c = document.createElement('button');
            c.type = 'button';
            c.className = 'pkf-chip';
            c.title = 'Hapus dari paket';
            c.append(b.dataset.label + ' ', Object.assign(document.createElement('i'), { className: 'bi bi-x-lg' }));
            c.addEventListener('click', () => { b.checked = false; sync(); });
            chips.appendChild(c);
        });
    }

    tabs.forEach(t => t.addEventListener('click', () => {
        cat = t.dataset.cat;
        tabs.forEach(x => x.classList.toggle('on', x === t));
        applyFilter();
    }));
    if (search) search.addEventListener('input', applyFilter);
    boxes.forEach(b => b.addEventListener('change', sync));
    sync();

    // minimal 1 menu
    root.addEventListener('submit', e => {
        if (!boxes.some(b => b.checked)) {
            e.preventDefault();
            err.classList.remove('d-none');
            document.getElementById('pkfMenuCard').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (!root.checkValidity()) {
            e.preventDefault();
            root.reportValidity();
        }
    });
})();
</script>
@endsection