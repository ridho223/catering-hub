@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<style>
    .ctf { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .ctf-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .ctf-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .ctf-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .ctf .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .ctf .form-control { border-color:var(--line); border-radius:10px; }
    .ctf .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .ctf textarea { resize:vertical; min-height:120px; }

    .ctf-preview { border:1px dashed #b9d8c7; background:#f7fbf8; border-radius:14px; padding:1rem; }
    .ctf-preview small { color:var(--mute); display:block; margin-bottom:.5rem; }
    .ctf-chip { display:inline-block; background:#f0f8f2; color:#236b3e; border-radius:8px; padding:.25rem .7rem; font-weight:600; font-size:.9rem; }
    .ctf-pdesc { color:var(--mute); font-size:.88rem; margin:.6rem 0 0; }
    .ctf-tips { list-style:none; margin:1rem 0 0; padding:0; color:var(--mute); font-size:.88rem; }
    .ctf-tips li { display:flex; gap:.5rem; padding:.25rem 0; }
    .ctf-tips i { color:var(--brand); margin-top:.15rem; }

    .ctf-bar { position:sticky; bottom:0; z-index:5; background:rgba(255,255,255,.94); backdrop-filter:blur(6px); border:1px solid var(--line); border-radius:16px; padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .ctf-bar span { color:var(--mute); font-size:.9rem; }
</style>

<div class="container-fluid pb-3 ctf">

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Perubahan belum bisa disimpan.</b> Periksa isian berikut:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.update', $category->id) }}" method="POST" id="categoryForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="ctf-card">
                    <h5>Informasi kategori</h5>
                    <p class="sub">Nama kategori tampil di daftar menu dan saat memilih isi paket.</p>

                    <div class="mb-3">
                        <label class="form-label" for="name">Nama kategori</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Lauk Pauk" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">Deskripsi <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Jelaskan menu apa saja yang masuk kategori ini">{{ old('description', $category->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </section>
            </div>

            <div class="col-lg-5">
                <section class="ctf-card">
                    <h5>Pratinjau</h5>
                    <p class="sub">Begini kategori ini akan terlihat di daftar menu.</p>

                    <div class="ctf-preview">
                        <small>Label kategori</small>
                        <span class="ctf-chip" id="pvName">Nama kategori</span>
                        <p class="ctf-pdesc" id="pvDesc">Belum ada deskripsi.</p>
                    </div>

                    <ul class="ctf-tips">
                        <li><i class="bi bi-check-circle-fill"></i>Pakai nama singkat dan jelas, misalnya Nasi, Lauk, Sayur, atau Minuman.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Satu menu hanya masuk ke satu kategori.</li>
                    </ul>
                </section>
            </div>
        </div>

        <div class="ctf-bar">
            <span>Perubahan nama langsung berlaku di semua menu pada kategori ini.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan perubahan</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const name = document.getElementById('name'), desc = document.getElementById('description');
    const pvName = document.getElementById('pvName'), pvDesc = document.getElementById('pvDesc');
    function sync() {
        pvName.textContent = name.value.trim() || 'Nama kategori';
        pvDesc.textContent = desc.value.trim() || 'Belum ada deskripsi.';
    }
    name.addEventListener('input', sync);
    desc.addEventListener('input', sync);
    sync();
})();
</script>
@endsection