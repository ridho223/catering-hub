@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
@php
    $level  = old('level', $user->level);
    $status = old('status', $user->status);
    $isMe   = $user->id === auth()->id();
@endphp

<style>
    .usf { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .usf-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .usf-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .usf-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .usf .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .usf .form-control { border-color:var(--line); border-radius:10px; }
    .usf .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .usf .input-group-text { background:var(--soft); border-color:var(--line); color:var(--mute); }
    .usf .input-group > .btn { border-color:var(--line); color:var(--mute); }
    .usf-hint { color:var(--mute); font-size:.82rem; margin-top:.3rem; }

    .usf-seg { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .usf-seg > div { position:relative; }
    .usf-seg input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .usf-seg label { border:1px solid var(--line); border-radius:12px; padding:.6rem .75rem; cursor:pointer; display:flex; align-items:center; gap:.5rem; color:var(--mute); font-weight:500; background:#fff; margin:0; }
    .usf-seg input:checked + label { border-color:var(--brand); background:var(--brand-soft); color:#146c43; }
    .usf-seg input:focus-visible + label { box-shadow:0 0 0 .2rem rgba(25,135,84,.25); }

    .usf-meter { display:grid; grid-template-columns:repeat(4,1fr); gap:4px; margin-top:.5rem; }
    .usf-meter i { height:5px; border-radius:3px; background:var(--line); }
    .usf-meter[data-s="1"] i:nth-child(-n+1) { background:#dc3545; }
    .usf-meter[data-s="2"] i:nth-child(-n+2) { background:#f0ad00; }
    .usf-meter[data-s="3"] i:nth-child(-n+3) { background:#7cc47f; }
    .usf-meter[data-s="4"] i { background:var(--brand); }
    .usf-msg { font-size:.82rem; margin-top:.3rem; min-height:1.1em; }

    .usf-profile { text-align:center; }
    .usf-avatar { position:relative; width:128px; height:128px; border-radius:50%; margin:0 auto; background:var(--brand-soft); color:#146c43; font-size:2.2rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; border:3px solid #fff; box-shadow:0 0 0 1px var(--line); }
    .usf-avatar img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; }
    .usf-avatar.has-img img { display:block; }
    .usf-avatar.has-img #avInit { display:none; }
    .usf-cam { position:absolute; left:0; right:0; bottom:0; background:rgba(31,42,36,.55); color:#fff; font-size:.8rem; padding:.3rem 0; }
    .usf-profile h6 { margin:.9rem 0 .1rem; font-weight:700; color:var(--ink); }
    .usf-profile small { color:var(--mute); display:block; word-break:break-all; }
    .usf-chips { display:flex; gap:.4rem; justify-content:center; flex-wrap:wrap; margin-top:.7rem; }
    .usf-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; }
    .usf-chip--super { background:#26342a; color:#fff; }
    .usf-chip--admin { background:#e2f0fb; color:#0b5a8a; }
    .usf-chip--on { background:#e3f4ea; color:#146c43; }
    .usf-chip--off { background:#eceff1; color:#5f6b72; }
    .usf-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }
    .usf-file { display:flex; justify-content:center; gap:.5rem; margin-top:1rem; }

    .usf-bar { position:sticky; bottom:0; z-index:5; background:rgba(255,255,255,.94); backdrop-filter:blur(6px); border:1px solid var(--line); border-radius:16px; padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .usf-bar span { color:var(--mute); font-size:.9rem; }
</style>

<div class="container-fluid pb-3 usf">

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Perubahan belum bisa disimpan.</b> Periksa isian berikut:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- KIRI --}}
            <div class="col-lg-7">
                <section class="usf-card">
                    <h5>Informasi akun</h5>
                    <p class="sub">Nama dan email dipakai untuk masuk ke panel admin.</p>

                    @if ($isMe)
                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Ini akun yang sedang kamu pakai. Hindari menonaktifkan akun atau menurunkan level, supaya kamu tidak terkunci dari panel ini.
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="name">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="form-label d-block">Level</span>
                            <div class="usf-seg">
                                <div>
                                    <input type="radio" name="level" id="lvAdmin" value="Admin" {{ $level === 'Admin' ? 'checked' : '' }}>
                                    <label for="lvAdmin"><i class="bi bi-person-gear"></i> Admin</label>
                                </div>
                                <div>
                                    <input type="radio" name="level" id="lvSuper" value="Superadmin" {{ $level === 'Superadmin' ? 'checked' : '' }}>
                                    <label for="lvSuper"><i class="bi bi-shield-lock"></i> Superadmin</label>
                                </div>
                            </div>
                            @error('level')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <span class="form-label d-block">Status</span>
                            <div class="usf-seg">
                                <div>
                                    <input type="radio" name="status" id="stOn" value="Aktif" {{ $status === 'Aktif' ? 'checked' : '' }}>
                                    <label for="stOn"><i class="bi bi-check-circle"></i> Aktif</label>
                                </div>
                                <div>
                                    <input type="radio" name="status" id="stOff" value="Non-Aktif" {{ $status === 'Non-Aktif' ? 'checked' : '' }}>
                                    <label for="stOff"><i class="bi bi-slash-circle"></i> Non-Aktif</label>
                                </div>
                            </div>
                            @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                <section class="usf-card">
                    <h5>Password</h5>
                    <p class="sub">Kosongkan kedua kolom jika password tidak ingin diubah.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="password">Password baru</label>
                            <div class="input-group has-validation">
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                       class="form-control @error('password') is-invalid @enderror">
                                <button type="button" class="btn" data-eye="password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="usf-meter" id="meter" data-s="0" hidden><i></i><i></i><i></i><i></i></div>
                            <div class="usf-msg" id="meterMsg"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Konfirmasi password baru</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="form-control">
                                <button type="button" class="btn" data-eye="password_confirmation" aria-label="Tampilkan konfirmasi password"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="usf-msg" id="matchMsg"></div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- KANAN --}}
            <div class="col-lg-5">
                <section class="usf-card usf-profile">
                    <h5 class="text-start">Foto profil</h5>
                    <p class="sub text-start">Klik lingkaran untuk mengganti foto.</p>

                    <label class="usf-avatar {{ $user->avatar ? 'has-img' : '' }}" id="avatarBox" for="avatar">
                        <img id="avImg" alt="Foto profil" @if ($user->avatar) src="{{ asset('storage/' . $user->avatar) }}" @endif>
                        <span id="avInit">?</span>
                        <span class="usf-cam"><i class="bi bi-camera me-1"></i>Pilih</span>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" hidden>
                    <div class="usf-file">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="avClear" hidden>Batal ganti</button>
                    </div>
                    <div class="usf-hint">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                    @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                    <h6 id="pvName">{{ $user->name }}</h6>
                    <small id="pvEmail">{{ $user->email }}</small>
                    <div class="usf-chips">
                        <span class="usf-chip" id="pvLevel"></span>
                        <span class="usf-chip" id="pvStatus"></span>
                    </div>
                </section>
            </div>
        </div>

        <div class="usf-bar">
            <span>Perubahan baru tersimpan setelah kamu klik simpan.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan perubahan</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);

    /* pratinjau profil */
    const name = $('name'), email = $('email');
    function chips() {
        const lv = document.querySelector('input[name="level"]:checked')?.value;
        const st = document.querySelector('input[name="status"]:checked')?.value;
        const pl = $('pvLevel'), ps = $('pvStatus');
        pl.className = 'usf-chip ' + (lv === 'Superadmin' ? 'usf-chip--super' : 'usf-chip--admin');
        pl.textContent = lv || 'Level';
        ps.className = 'usf-chip ' + (st === 'Aktif' ? 'usf-chip--on' : 'usf-chip--off');
        ps.innerHTML = '<span class="usf-dot"></span>' + (st || 'Status');
    }
    function profile() {
        const n = name.value.trim();
        $('avInit').textContent = n ? n.substring(0, 2).toUpperCase() : '?';
        $('pvName').textContent = n || 'Nama user';
        $('pvEmail').textContent = email.value.trim() || 'email@contoh.com';
        chips();
    }
    name.addEventListener('input', profile);
    email.addEventListener('input', profile);
    document.querySelectorAll('input[name="level"], input[name="status"]').forEach(r => r.addEventListener('change', chips));
    profile();

    /* avatar */
    const file = $('avatar'), img = $('avImg'), box = $('avatarBox'), clear = $('avClear');
    const originalSrc = img.getAttribute('src');
    file.addEventListener('change', () => {
        const f = file.files[0];
        if (!f) return;
        img.src = URL.createObjectURL(f);
        box.classList.add('has-img');
        clear.hidden = false;
    });
    clear.addEventListener('click', () => {
        file.value = '';
        if (originalSrc) { img.src = originalSrc; box.classList.add('has-img'); }
        else { img.removeAttribute('src'); box.classList.remove('has-img'); }
        clear.hidden = true;
    });

    /* tampilkan / sembunyikan password */
    document.querySelectorAll('[data-eye]').forEach(b => b.addEventListener('click', () => {
        const input = $(b.dataset.eye);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        b.innerHTML = '<i class="bi bi-eye' + (show ? '-slash' : '') + '"></i>';
    }));

    /* kekuatan & kecocokan password */
    const pw = $('password'), pc = $('password_confirmation'), meter = $('meter'), msg = $('meterMsg'), match = $('matchMsg');
    const labels = ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat'];
    function strength() {
        const v = pw.value;
        meter.hidden = !v;
        if (!v) { msg.textContent = ''; return; }
        let s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        s = Math.max(s, 1);
        meter.dataset.s = s;
        msg.textContent = (v.length < 8 ? 'Minimal 8 karakter. ' : '') + labels[s];
        msg.style.color = s <= 1 ? '#dc3545' : (s === 2 ? '#a77700' : '#146c43');
    }
    function same() {
        if (!pc.value) { match.textContent = ''; return; }
        const ok = pw.value === pc.value;
        match.textContent = ok ? 'Password cocok' : 'Password belum sama';
        match.style.color = ok ? '#146c43' : '#dc3545';
    }
    pw.addEventListener('input', () => { strength(); same(); });
    pc.addEventListener('input', same);
})();
</script>
@endsection