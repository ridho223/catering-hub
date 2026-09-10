@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')

<div class="profile-page">

    <div class="profile-card">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">
                Tambah User
            </h4>

            <p class="text-muted mb-0">
                Buat akun Admin atau Superadmin baru.
            </p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    required
                >

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    required
                >

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">
                        Level
                    </label>

                    <select
                        name="level"
                        class="form-select @error('level') is-invalid @enderror"
                        required
                    >

                        <option value="">Pilih Level</option>

                        <option value="Admin" {{ old('level') === 'Admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="Superadmin" {{ old('level') === 'Superadmin' ? 'selected' : '' }}>
                            Superadmin
                        </option>

                    </select>

                    @error('level')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >

                        <option value="">Pilih Status</option>

                        <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="Non-Aktif" {{ old('status') === 'Non-Aktif' ? 'selected' : '' }}>
                            Non-Aktif
                        </option>

                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                >

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                >

            </div>

            <div class="mb-3">
                <label class="form-label">Avatar</label>

                <input type="file" name="avatar" class="form-control" accept="image/*">

                <small class="text-muted">
                    JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                </small>
            </div>

            <div class="d-flex justify-content-end gap-2">

                <a
                    href="{{ route('users.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Batal
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>
                    Simpan User
                </button>

            </div>

        </form>

    </div>

</div>

@endsection