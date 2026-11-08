@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
@php
    $initial = strtoupper(mb_substr($customer->name ?: $account->email, 0, 1));
    $incomplete = blank($customer->phone) || blank($customer->address);
    $openProfile = $errors->hasAny(['name', 'phone', 'address']);
    $openPassword = $errors->hasAny(['current_password', 'password']);
@endphp

<style>
    .pf { background:#f6f8f7; min-height:85vh; padding:32px 0 64px; color:#17231d; }
    .pf-card { background:#fff; border:1px solid #e3ebe6; border-radius:18px; box-shadow:0 1px 2px rgba(16,24,20,.04), 0 10px 28px rgba(16,24,20,.05); }
    .pf-avatar { width:72px; height:72px; border-radius:50%; background:#e9f6ef; color:#198754; display:grid; place-items:center; font-size:1.8rem; font-weight:700; flex-shrink:0; }
    .pf-name { font-size:1.3rem; font-weight:700; margin:0; letter-spacing:-.01em; }
    .pf-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.74rem; font-weight:600; padding:.22rem .65rem; border-radius:999px; }
    .pf-badge.ok { background:#e9f6ef; color:#146c43; }
    .pf-badge.warn { background:#fff4db; color:#8a5a00; }
    .pf-label { font-size:.74rem; text-transform:uppercase; letter-spacing:.06em; color:#7a8a81; font-weight:600; margin-bottom:.2rem; }
    .pf-value { font-size:.97rem; margin:0; word-break:break-word; }
    .pf-value.empty { color:#a0aea6; font-style:italic; }
    .pf-row { padding:1rem 1.5rem; border-bottom:1px solid #eef3f0; }
    .pf-row:last-child { border-bottom:0; }
    .pf-btn { border-radius:12px; font-weight:600; font-size:.9rem; padding:.55rem 1.1rem; }
    .pf-notice { background:#fff9e8; border:1px solid #f6e3a6; color:#7a5700; border-radius:14px; font-size:.9rem; }
    .pf-link { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.5rem; color:#17231d; text-decoration:none; }
    .pf-link:hover { background:#f8fbf9; color:#17231d; border-radius:18px; }
    .pf-link i.lead { color:#198754; }

    /* Modal */
    .pf-modal .modal-content { border:0; border-radius:20px; box-shadow:0 24px 60px rgba(16,24,20,.2); }
    .pf-modal .modal-header { border:0; padding:1.5rem 1.5rem .5rem; }
    .pf-modal .modal-title { font-weight:700; font-size:1.15rem; }
    .pf-modal .modal-body { padding:.5rem 1.5rem 1rem; }
    .pf-modal .modal-footer { border:0; padding:.5rem 1.5rem 1.5rem; gap:.5rem; }
    .pf-modal .form-label { font-size:.82rem; font-weight:500; color:#3d4f46; margin-bottom:.3rem; }
    .pf-modal .form-control { border:1px solid #e3ebe6; border-radius:12px; padding:.65rem .9rem; font-size:.93rem; }
    .pf-modal .form-control:focus { border-color:#198754; box-shadow:0 0 0 4px rgba(25,135,84,.12); }
    .pf-modal .form-control[disabled] { background:#f6f8f7; }
    .pf-eye-wrap { position:relative; }
    .pf-eye-wrap .form-control { padding-right:2.8rem; }
    .pf-eye { position:absolute; right:6px; top:50%; transform:translateY(-50%); width:34px; height:34px; border:0; background:transparent; color:#94a69c; border-radius:10px; }
    .pf-eye:hover { background:#e9f6ef; color:#198754; }
</style>

<div class="pf">
    <div class="container" style="max-width: 760px;">

        {{-- Header --}}
        <div class="pf-card p-4 mb-3">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                <div class="pf-avatar">{{ $initial }}</div>

                <div class="flex-grow-1">
                    <h1 class="pf-name">{{ $customer->name }}</h1>
                    <div class="text-muted small mb-2">{{ $account->email }}</div>
                    @if ($account->hasVerifiedEmail())
                        <span class="pf-badge ok"><i class="bi bi-patch-check-fill"></i> Email terverifikasi</span>
                    @else
                        <span class="pf-badge warn"><i class="bi bi-exclamation-circle"></i> Email belum terverifikasi</span>
                    @endif
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-success pf-btn" data-bs-toggle="modal" data-bs-target="#modalProfile">
                        <i class="bi bi-pencil me-1"></i>Edit profil
                    </button>
                    <button type="button" class="btn btn-outline-secondary pf-btn" data-bs-toggle="modal" data-bs-target="#modalPassword">
                        <i class="bi bi-shield-lock me-1"></i>Password
                    </button>
                </div>
            </div>
        </div>

        @if ($incomplete)
            <div class="pf-notice p-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle"></i>
                <span>Lengkapi nomor HP dan alamat supaya pesananmu bisa diproses dan diantar.</span>
            </div>
        @endif

        {{-- Data diri --}}
        <div class="pf-card mb-3">
            <div class="pf-row">
                <div class="pf-label">Nama lengkap</div>
                <p class="pf-value">{{ $customer->name }}</p>
            </div>
            <div class="pf-row">
                <div class="pf-label">Email</div>
                <p class="pf-value">{{ $account->email }}</p>
            </div>
            <div class="pf-row">
                <div class="pf-label">No. HP / WhatsApp</div>
                @if (filled($customer->phone))
                    <p class="pf-value">{{ $customer->phone }}</p>
                @else
                    <p class="pf-value empty">Belum diisi</p>
                @endif
            </div>
            <div class="pf-row">
                <div class="pf-label">Alamat</div>
                @if (filled($customer->address))
                    <p class="pf-value">{{ $customer->address }}</p>
                @else
                    <p class="pf-value empty">Belum diisi</p>
                @endif
            </div>
        </div>

        {{-- Pintasan --}}
        <div class="pf-card">
            <a href="{{ route('customer.orders.index') }}" class="pf-link">
                <span class="d-flex align-items-center gap-3">
                    <i class="bi bi-receipt lead"></i>
                    <span><b>Pesanan saya</b><br><small class="text-muted">Lihat riwayat dan status pesanan</small></span>
                </span>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        </div>

    </div>
</div>

{{-- MODAL: EDIT PROFIL --}}
<div class="modal fade pf-modal" id="modalProfile" tabindex="-1" aria-labelledby="modalProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('customer.profile.update') }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="modalProfileLabel">Edit profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ $account->email }}" disabled>
                    <div class="form-text">Email dipakai untuk login, jadi tidak bisa diubah.</div>
                </div>

                <div class="mb-3">
                    <label for="pf_name" class="form-label">Nama lengkap</label>
                    <input type="text" id="pf_name" name="name" maxlength="255"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $customer->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="pf_phone" class="form-label">No. HP / WhatsApp</label>
                    <input type="tel" id="pf_phone" name="phone" inputmode="tel"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $customer->phone) }}" placeholder="081234567890" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-1">
                    <label for="pf_address" class="form-label">Alamat</label>
                    <textarea id="pf_address" name="address" rows="3" maxlength="500"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="Jalan, nomor, kecamatan, kota" required>{{ old('address', $customer->address) }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light pf-btn" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success pf-btn px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: GANTI PASSWORD --}}
<div class="modal fade pf-modal" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('customer.profile.password') }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="modalPasswordLabel">Ganti password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="pf_current" class="form-label">Password lama</label>
                    <div class="pf-eye-wrap">
                        <input type="password" id="pf_current" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               autocomplete="current-password" required>
                        <button type="button" class="pf-eye" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="pf_new" class="form-label">Password baru</label>
                    <div class="pf-eye-wrap">
                        <input type="password" id="pf_new" name="password" minlength="8"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password" required>
                        <button type="button" class="pf-eye" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-2">
                    <label for="pf_confirm" class="form-label">Ulangi password baru</label>
                    <div class="pf-eye-wrap">
                        <input type="password" id="pf_confirm" name="password_confirmation" minlength="8"
                               class="form-control" autocomplete="new-password" required>
                        <button type="button" class="pf-eye" aria-label="Lihat password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <p class="text-muted small mt-3 mb-0">
                    Daftar lewat Google dan belum punya password?
                    <a href="{{ route('customer.password.request') }}" class="text-success fw-semibold">Buat lewat "Lupa password"</a>.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light pf-btn" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success pf-btn px-4">Ubah password</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Lihat / sembunyikan password di modal
    document.querySelectorAll('.pf-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('input');
            var icon = btn.querySelector('i');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // Buka kembali modal yang formnya gagal validasi (script bootstrap dimuat setelah konten)
    window.addEventListener('load', function () {
        @if ($openProfile)
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalProfile')).show();
        @elseif ($openPassword)
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPassword')).show();
        @endif
    });
</script>
@endsection