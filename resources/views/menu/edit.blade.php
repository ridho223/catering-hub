@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<style>
    .mnf { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .mnf-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .mnf-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .mnf-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .mnf .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .mnf .form-control, .mnf .form-select { border-color:var(--line); border-radius:10px; }
    .mnf .form-control:focus, .mnf .form-select:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .mnf .input-group-text { background:var(--soft); border-color:var(--line); }
    .mnf-hint { color:var(--mute); font-size:.82rem; margin-top:.3rem; }

    .mnf-stepper { display:flex; }
    .mnf-stepper button { border:1px solid var(--line); background:var(--soft); width:42px; color:var(--ink); font-size:1.1rem; }
    .mnf-stepper button:first-child { border-radius:10px 0 0 10px; }
    .mnf-stepper button:last-child { border-radius:0 10px 10px 0; }
    .mnf-stepper button:hover { background:var(--brand-soft); }
    .mnf-stepper input { border-radius:0; text-align:center; border-left:0; border-right:0; -moz-appearance:textfield; }
    .mnf-stepper input::-webkit-outer-spin-button, .mnf-stepper input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }

    .mnf-seg { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .mnf-seg > div { position:relative; }
    .mnf-seg input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .mnf-seg label { border:1px solid var(--line); border-radius:12px; padding:.6rem .75rem; cursor:pointer; display:flex; align-items:center; gap:.5rem; color:var(--mute); font-weight:500; background:#fff; margin:0; }
    .mnf-seg input:checked + label { border-color:var(--brand); background:var(--brand-soft); color:#146c43; }
    .mnf-seg input:focus-visible + label { box-shadow:0 0 0 .2rem rgba(25,135,84,.25); }

    .mnf-photo { position:relative; border:1.5px dashed #b9d8c7; background:#f7fbf8; border-radius:16px; aspect-ratio:4/3; overflow:hidden; display:flex; align-items:center; justify-content:center; cursor:pointer; }
    .mnf-photo:hover { background:#f0f8f3; }
    .mnf-photo img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; }
    .mnf-photo img.on { display:block; }
    .mnf-photo-ph { text-align:center; color:var(--mute); padding:1rem; }
    .mnf-photo-ph i { font-size:2.2rem; display:block; color:#7fb597; }
    .mnf-photo-ph b { color:var(--ink); display:block; margin-top:.3rem; }
    .mnf-photo input { display:none; }
    .mnf-photo-bar { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-top:.6rem; font-size:.85rem; color:var(--mute); }

    .mnf-bar { position:sticky; bottom:0; z-index:5; background:rgba(255,255,255,.94); backdrop-filter:blur(6px); border:1px solid var(--line); border-radius:16px; padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .mnf-bar span { color:var(--mute); font-size:.9rem; }
</style>

<div class="container-fluid pb-3 mnf">

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Perubahan belum bisa disimpan.</b> Periksa isian berikut:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" id="menuForm">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- KIRI --}}
            <div class="col-lg-7">
                <section class="mnf-card">
                    <h5>Informasi menu</h5>
                    <p class="sub">Nama, kategori, harga, dan stok awal.</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="name">Nama menu</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Ayam Bakar Madu" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="category_id">Kategori</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $menu->category_id) === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="price">Harga per porsi</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rp</span>
                                <input type="number" id="price" name="price" value="{{ old('price', $menu->price) }}" min="0"
                                       class="form-control @error('price') is-invalid @enderror" placeholder="15000" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mnf-hint" id="priceHint">&nbsp;</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="stock">Stok (porsi)</label>
                            <div class="mnf-stepper">
                                <button type="button" data-step="-10" aria-label="Kurangi 10">−</button>
                                <input type="number" id="stock" name="stock" value="{{ old('stock', $menu->stock) }}" min="0"
                                       class="form-control @error('stock') is-invalid @enderror" required>
                                <button type="button" data-step="10" aria-label="Tambah 10">+</button>
                            </div>
                            @error('stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <div class="mnf-hint">Tombol mengubah 10 porsi sekaligus.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="description">Deskripsi <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea id="description" name="description" rows="4" class="form-control"
                                      placeholder="Bahan, rasa, atau keterangan porsi">{{ old('description', $menu->description) }}</textarea>
                        </div>
                    </div>
                </section>
            </div>

            {{-- KANAN --}}
            <div class="col-lg-5">
                <section class="mnf-card">
                    <h5>Foto menu</h5>
                    <p class="sub">Foto tampil di daftar menu dan paket.</p>

                    <label class="mnf-photo" for="image">
                        <img id="preview" alt="Pratinjau foto menu"
                             @if ($menu->image) src="{{ asset('uploads/menu/' . $menu->image) }}" class="on" @endif>
                        <span class="mnf-photo-ph" id="placeholder" @if ($menu->image) style="display:none" @endif>
                            <i class="bi bi-cloud-arrow-up"></i>
                            <b>Pilih foto</b>
                            JPG, PNG, atau WEBP
                        </span>
                        <input type="file" id="image" name="image" accept="image/*">
                    </label>
                    <div class="mnf-photo-bar">
                        <span id="fileName">{{ $menu->image ? 'Foto saat ini. Klik foto untuk menggantinya.' : 'Belum ada foto dipilih' }}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearImage" hidden>Batal ganti</button>
                    </div>
                    @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </section>

                <section class="mnf-card">
                    <h5>Status</h5>
                    <p class="sub">Menu berstok 0 otomatis tampil Habis.</p>

                    @php $status = old('status', $menu->status); @endphp
                    <div class="mnf-seg">
                        <div>
                            <input type="radio" name="status" id="stOn" value="Tersedia" {{ $status === 'Tersedia' ? 'checked' : '' }}>
                            <label for="stOn"><i class="bi bi-check-circle"></i> Tersedia</label>
                        </div>
                        <div>
                            <input type="radio" name="status" id="stOff" value="Habis" {{ $status === 'Habis' ? 'checked' : '' }}>
                            <label for="stOff"><i class="bi bi-slash-circle"></i> Habis</label>
                        </div>
                    </div>
                    @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </section>
            </div>
        </div>

        <div class="mnf-bar">
            <span>Perubahan baru tersimpan setelah kamu klik simpan.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan perubahan</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);

    // harga: tampilkan format rupiah
    const price = $('price'), hint = $('priceHint');
    function showPrice() {
        const v = Number(price.value);
        hint.innerHTML = v > 0 ? 'Tampil sebagai <b>Rp' + v.toLocaleString('id-ID') + '</b>' : '&nbsp;';
    }
    price.addEventListener('input', showPrice);
    showPrice();

    // stok: tombol +/- 10
    const stock = $('stock');
    document.querySelectorAll('.mnf-stepper button').forEach(b => b.addEventListener('click', () => {
        stock.value = Math.max(0, (parseInt(stock.value) || 0) + Number(b.dataset.step));
    }));

    // foto: pratinjau, "Batal ganti" mengembalikan foto lama
    const file = $('image'), prev = $('preview'), ph = $('placeholder'), name = $('fileName'), clear = $('clearImage');
    const originalSrc = prev.getAttribute('src');
    const originalText = name.textContent;
    file.addEventListener('change', () => {
        const f = file.files[0];
        if (!f) return;
        prev.src = URL.createObjectURL(f);
        prev.classList.add('on');
        ph.style.display = 'none';
        name.textContent = f.name;
        clear.hidden = false;
    });
    clear.addEventListener('click', () => {
        file.value = '';
        if (originalSrc) { prev.src = originalSrc; prev.classList.add('on'); ph.style.display = 'none'; }
        else { prev.classList.remove('on'); prev.removeAttribute('src'); ph.style.display = ''; }
        name.textContent = originalText;
        clear.hidden = true;
    });
})();
</script>
@endsection