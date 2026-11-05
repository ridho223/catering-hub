@extends('layouts.admin')

@section('title', 'Profile')

@push('styles')
<style>
    .pf-me { display:flex; flex-direction:column; align-items:center; text-align:center; }
    .pf-av { width:104px; height:104px; border-radius:30px; object-fit:cover; margin-bottom:1rem; box-shadow:0 0 0 4px #fff, 0 0 0 5px var(--line); }
    .pf-av.ph { background:var(--green); color:#fff; font-size:2rem; font-weight:700; display:grid; place-items:center; }
    .pf-me h2 { margin:0 0 .15rem; font-size:1.25rem; font-weight:800; color:var(--ink); overflow-wrap:anywhere; }
    .pf-me p { margin:0 0 .8rem; color:var(--mute); overflow-wrap:anywhere; }
    .pf-chips { display:flex; gap:.4rem; flex-wrap:wrap; justify-content:center; margin-bottom:1.2rem; }
    .pf-actions { display:grid; gap:.5rem; width:100%; }
    .pf-actions .btn { border-radius:12px; font-weight:600; }

    .pf-row { display:flex; align-items:center; gap:.9rem; padding:.9rem 0; border-top:1px solid var(--line); }
    .pf-row.first { border-top:0; padding-top:0; }
    .pf-row .ui-ic { width:40px; height:40px; border-radius:12px; font-size:1.05rem; }
    .pf-row small { display:block; color:var(--mute); font-size:.8rem; }
    .pf-row strong { color:var(--ink); font-weight:600; overflow-wrap:anywhere; }
    .pf-row .grow { flex:1; min-width:0; }
    .pf-sec { margin:1.4rem 0 .2rem; font-weight:700; color:var(--ink); font-size:.95rem; }

    /* modal */
    .pf-modal .modal-content { border:0; border-radius:20px; box-shadow:0 24px 60px rgba(15,25,19,.22); }
    .pf-modal .modal-header, .pf-modal .modal-footer { padding:1.2rem 1.4rem; }
    .pf-modal .modal-body { padding:.4rem 1.4rem 1rem; }
    .pf-modal .form-label { font-weight:600; font-size:.9rem; }
    .pf-modal .form-control { border-color:var(--line); border-radius:12px; padding:.6rem .85rem; }
    .pf-modal .form-control:focus { border-color:var(--green); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .pf-modal .input-group .form-control { border-radius:12px 0 0 12px; }
    .pf-modal .input-group .btn { border-color:var(--line); color:var(--mute); border-radius:0 12px 12px 0; }
    .pf-modal .btn { border-radius:12px; font-weight:600; }
    .pf-hint { color:var(--mute); font-size:.82rem; }
    .pf-upload { display:flex; align-items:center; gap:1rem; }
    .pf-prev { width:72px; height:72px; border-radius:20px; overflow:hidden; background:var(--green); color:#fff; display:grid; place-items:center; font-weight:700; font-size:1.3rem; flex:none; }
    .pf-prev img { width:100%; height:100%; object-fit:cover; display:block; }
</style>
@endpush

@section('content')
@php
    $isActive   = in_array(mb_strtolower((string) $user->status), ['aktif', 'active', '1', 'true'], true);
    $openEdit   = $errors->has('name') || $errors->has('email') || $errors->has('avatar');
    $openPw     = $errors->has('current_password') || $errors->has('password');
    $autoOpen   = $openEdit ? 'editProfileModal' : ($openPw ? 'passwordModal' : null);
@endphp

<div class="ui-page">
    <div class="row g-3">

        {{-- KARTU PROFIL --}}
        <div class="col-lg-4">
            <section class="ui-card pf-me">
                @if ($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="pf-av" alt="{{ $user->name }}">
                @else
                    <div class="pf-av ph">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</div>
                @endif

                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>

                <div class="pf-chips">
                    <span class="ui-chip ready"><i class="bi bi-shield-check"></i>{{ $user->level }}</span>
                    <span class="ui-chip {{ $isActive ? 'ok' : 'off' }}">{{ $user->status }}</span>
                </div>

                <div class="pf-actions">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-square me-1"></i> Edit profile
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        <i class="bi bi-key me-1"></i> Ubah password
                    </button>
                </div>
            </section>
        </div>

        {{-- DETAIL AKUN --}}
        <div class="col-lg-8">
            <section class="ui-card">
                <div class="ui-head"><div><h2>Informasi akun</h2><small>Data yang dipakai untuk masuk ke panel admin</small></div></div>

                <div class="pf-row first">
                    <span class="ui-ic t-green"><i class="bi bi-person"></i></span>
                    <div class="grow"><small>Nama</small><strong>{{ $user->name }}</strong></div>
                </div>
                <div class="pf-row">
                    <span class="ui-ic t-blue"><i class="bi bi-envelope"></i></span>
                    <div class="grow"><small>Email</small><strong>{{ $user->email }}</strong></div>
                </div>
                <div class="pf-row">
                    <span class="ui-ic t-purple"><i class="bi bi-shield-check"></i></span>
                    <div class="grow"><small>Level</small><strong>{{ $user->level }}</strong></div>
                </div>
                <div class="pf-row">
                    <span class="ui-ic {{ $isActive ? 't-green' : 't-gray' }}"><i class="bi bi-circle-fill"></i></span>
                    <div class="grow"><small>Status</small><strong>{{ $user->status }}</strong></div>
                </div>

                <div class="pf-sec">Keamanan</div>
                <div class="pf-row first" style="border-top:0">
                    <span class="ui-ic t-amber"><i class="bi bi-key"></i></span>
                    <div class="grow"><small>Password</small><strong>••••••••</strong></div>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#passwordModal">Ubah</button>
                </div>
            </section>
        </div>
    </div>
</div>

{{-- MODAL: EDIT PROFILE --}}
<div class="modal fade pf-modal" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" data-once>
            @csrf
            @method('PUT')

            <div class="modal-header border-0 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="ui-ic t-green"><i class="bi bi-pencil-square"></i></span>
                    <div>
                        <h2 class="modal-title fs-5 fw-bold mb-0" id="editProfileTitle">Edit profile</h2>
                        <small class="text-muted">Perbarui informasi akun kamu.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="pf-upload mb-3">
                    <div class="pf-prev" id="pfPreview">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="">
                        @else
                            <span>{{ strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div>
                        <label class="btn btn-outline-secondary btn-sm" for="pfAvatar"><i class="bi bi-camera me-1"></i> Pilih foto</label>
                        <input type="file" id="pfAvatar" name="avatar" accept="image/png,image/jpeg,image/webp" class="d-none">
                        @if ($user->avatar)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="pfRemove">
                                <label class="form-check-label small" for="pfRemove">Hapus foto saat ini</label>
                            </div>
                        @endif
                        <div class="pf-hint mt-1">JPG, PNG, atau WebP. Maksimal 2 MB.</div>
                        <div class="text-danger small mt-1" id="pfAvatarMsg">@error('avatar'){{ $message }}@enderror</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pfName">Nama</label>
                    <input type="text" id="pfName" name="name" autocomplete="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-2">
                    <label class="form-label" for="pfEmail">Email</label>
                    <input type="email" id="pfEmail" name="email" autocomplete="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: UBAH PASSWORD --}}
<div class="modal fade pf-modal" id="passwordModal" tabindex="-1" aria-labelledby="passwordTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('admin.password.update') }}" method="POST" data-once>
            @csrf
            @method('PUT')

            <div class="modal-header border-0 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="ui-ic t-amber"><i class="bi bi-key"></i></span>
                    <div>
                        <h2 class="modal-title fs-5 fw-bold mb-0" id="passwordTitle">Ubah password</h2>
                        <small class="text-muted">Pastikan password baru aman dan mudah diingat.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="pwCurrent">Password lama</label>
                    <div class="input-group has-validation">
                        <input type="password" id="pwCurrent" name="current_password" autocomplete="current-password" data-pw
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        <button class="btn btn-outline-secondary" type="button" data-toggle-pw aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pwNew">Password baru</label>
                    <div class="input-group has-validation">
                        <input type="password" id="pwNew" name="password" autocomplete="new-password" minlength="8" data-pw
                               class="form-control @error('password') is-invalid @enderror" required>
                        <button class="btn btn-outline-secondary" type="button" data-toggle-pw aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <span class="pf-hint">Minimal 8 karakter.</span>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="pwConfirm">Konfirmasi password baru</label>
                    <div class="input-group">
                        <input type="password" id="pwConfirm" name="password_confirmation" autocomplete="new-password" data-pw
                               class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" data-toggle-pw aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-shield-check me-1"></i> Ubah password</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hashMap = { '#edit': 'editProfileModal', '#password': 'passwordModal' };
    var prev     = document.getElementById('pfPreview');
    var prevOrig = prev.innerHTML;
    var fileIn   = document.getElementById('pfAvatar');
    var fileMsg  = document.getElementById('pfAvatarMsg');
    var removeCb = document.getElementById('pfRemove');

    function openModal(id) {
        var el = id && document.getElementById(id);
        if (el) bootstrap.Modal.getOrCreateInstance(el).show();
    }

    // Buka otomatis: ada error validasi (server) atau datang dari menu dropdown (#edit / #password)
    openModal(@json($autoOpen) || hashMap[location.hash]);
    window.addEventListener('hashchange', function () { openModal(hashMap[location.hash]); });

    // Bersihkan #hash dan isian password saat modal ditutup
    document.querySelectorAll('.pf-modal').forEach(function (m) {
        m.addEventListener('hidden.bs.modal', function () {
            if (location.hash) history.replaceState(null, '', location.pathname + location.search);
            if (m.id === 'editProfileModal') {
                m.querySelector('form').reset();
                prev.innerHTML = prevOrig;
                fileMsg.textContent = '';
            }
            if (m.id === 'passwordModal') {
                m.querySelector('form').reset();
                setPwVisible(m.querySelector('form'), false);
            }
        });
    });

    // Tampilkan / sembunyikan password: satu tombol mata mengatur ketiga kolom sekaligus
    function setPwVisible(form, show) {
        form.querySelectorAll('[data-pw]').forEach(function (i) { i.type = show ? 'text' : 'password'; });
        form.querySelectorAll('[data-toggle-pw]').forEach(function (b) {
            b.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            b.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    }
    document.querySelectorAll('[data-toggle-pw]').forEach(function (b) {
        b.addEventListener('click', function () {
            var form = b.closest('form');
            setPwVisible(form, form.querySelector('[data-pw]').type === 'password');
        });
    });

    // Pratinjau foto yang dipilih
    fileIn.addEventListener('change', function () {
        var f = fileIn.files[0];
        fileMsg.textContent = '';
        if (!f) return;
        if (f.size > 2 * 1024 * 1024) {
            fileIn.value = '';
            fileMsg.textContent = 'Ukuran foto maksimal 2 MB.';
            return;
        }
        prev.innerHTML = '';
        var img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(f);
        prev.appendChild(img);
        if (removeCb) removeCb.checked = false;
    });

    // Cegah klik ganda saat mengirim
    document.querySelectorAll('form[data-once]').forEach(function (f) {
        f.addEventListener('submit', function () {
            var btn = f.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; }
        });
    });
});
</script>
@endpush
@endsection