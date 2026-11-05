@extends('layouts.admin')

@section('title', 'Tambah Customer')

@section('content')
<style>
    .csf { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .csf-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .csf-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .csf-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .csf .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .csf .form-control { border-color:var(--line); border-radius:10px; }
    .csf .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .csf .input-group-text { background:var(--soft); border-color:var(--line); color:var(--mute); }
    .csf textarea { resize:vertical; min-height:110px; }

    .csf-pv { border:1px dashed #b9d8c7; background:#f7fbf8; border-radius:14px; padding:1rem; }
    .csf-pv-head { display:flex; gap:.8rem; align-items:center; }
    .csf-av { width:48px; height:48px; border-radius:50%; background:var(--brand); color:#fff; font-weight:700; font-size:1.15rem; display:flex; align-items:center; justify-content:center; flex:none; }
    .csf-pv b { color:var(--ink); display:block; line-height:1.25; }
    .csf-pv small { color:var(--mute); }
    .csf-pv-row { display:flex; gap:.55rem; color:var(--ink); font-size:.9rem; margin-top:.7rem; }
    .csf-pv-row i { color:var(--mute); margin-top:.15rem; }
    .csf-pv-row.muted { color:var(--mute); }
    .csf-tips { list-style:none; margin:1rem 0 0; padding:0; color:var(--mute); font-size:.88rem; }
    .csf-tips li { display:flex; gap:.5rem; padding:.25rem 0; }
    .csf-tips i { color:var(--brand); margin-top:.15rem; }

    .csf-bar { position:sticky; bottom:0; z-index:5; background:rgba(255,255,255,.94); backdrop-filter:blur(6px); border:1px solid var(--line); border-radius:16px; padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .csf-bar span { color:var(--mute); font-size:.9rem; }
</style>

<div class="container-fluid pb-3 csf">

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Customer belum bisa disimpan.</b> Periksa isian berikut:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="csf-card">
                    <h5>Data customer</h5>
                    <p class="sub">Nomor telepon dipakai untuk menghubungi pelanggan lewat telepon atau WhatsApp.</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="name">Nama customer</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nama lengkap" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phone">No. HP / WhatsApp</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="nama@email.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="address">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Alamat lengkap untuk pengiriman pesanan">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-5">
                <section class="csf-card">
                    <h5>Pratinjau kontak</h5>
                    <p class="sub">Begini data customer akan terlihat di pesanan.</p>

                    <div class="csf-pv">
                        <div class="csf-pv-head">
                            <span class="csf-av" id="pvAv">?</span>
                            <div>
                                <b id="pvName">Nama customer</b>
                                <small id="pvEmail">Tanpa email</small>
                            </div>
                        </div>
                        <div class="csf-pv-row muted" id="pvPhoneRow"><i class="bi bi-telephone"></i><span id="pvPhone">Belum ada nomor telepon</span></div>
                        <div class="csf-pv-row muted" id="pvAddrRow"><i class="bi bi-geo-alt"></i><span id="pvAddr">Alamat belum diisi</span></div>
                    </div>

                    <ul class="csf-tips">
                        <li><i class="bi bi-check-circle-fill"></i>Pastikan nomor aktif, supaya mudah dihubungi saat pesanan siap.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Alamat lengkap membantu pengantaran dan penentuan jarak ongkir.</li>
                    </ul>
                </section>
            </div>
        </div>

        <div class="csf-bar">
            <span>Customer baru langsung bisa dipilih saat membuat pesanan.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan customer</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    const f = { name: $('name'), phone: $('phone'), email: $('email'), address: $('address') };

    function sync() {
        const name = f.name.value.trim(), phone = f.phone.value.trim(), email = f.email.value.trim(), addr = f.address.value.trim();
        $('pvAv').textContent = name ? name.charAt(0).toUpperCase() : '?';
        $('pvName').textContent = name || 'Nama customer';
        $('pvEmail').textContent = email || 'Tanpa email';
        $('pvPhone').textContent = phone || 'Belum ada nomor telepon';
        $('pvAddr').textContent = addr || 'Alamat belum diisi';
        $('pvPhoneRow').classList.toggle('muted', !phone);
        $('pvAddrRow').classList.toggle('muted', !addr);
    }
    Object.values(f).forEach(el => el.addEventListener('input', sync));
    sync();
})();
</script>
@endsection