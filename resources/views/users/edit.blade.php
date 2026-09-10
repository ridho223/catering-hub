@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')

<div class="profile-page">

    <div class="profile-card">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">
                Edit User
            </h4>

            <p class="text-muted mb-0">
                Perbarui informasi akun {{ $user->name }}.
            </p>
        </div>

        <form
            action="{{ route('users.update', $user) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
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
                    value="{{ old('email', $user->email) }}"
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
                        class="form-select"
                        required
                    >

                        <option
                            value="Admin"
                            {{ old('level', $user->level) === 'Admin' ? 'selected' : '' }}
                        >
                            Admin
                        </option>

                        <option
                            value="Superadmin"
                            {{ old('level', $user->level) === 'Superadmin' ? 'selected' : '' }}
                        >
                            Superadmin
                        </option>

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                        required
                    >

                        <option
                            value="Aktif"
                            {{ old('status', $user->status) === 'Aktif' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="Non-Aktif"
                            {{ old('status', $user->status) === 'Non-Aktif' ? 'selected' : '' }}
                        >
                            Non-Aktif
                        </option>

                    </select>

                </div>

            </div>

            <hr class="my-4">

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                >

                <small class="text-muted">
                    Kosongkan jika password tidak ingin diubah.
                </small>

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                >

            </div>

            <div class="mb-3">
                <label class="form-label">Avatar</label>

                <div class="mb-3">
                    @if ($user->avatar)
                        <img src="{{asset('storage/' . $user->avatar)}}" width="80" height="80" class="rounded-circle object-fit-cover">
                    @else
                        <div class="user-avatar">
                            {{ strtoupper(substr($user->name, 0, 2))}}
                        </div>
                    @endif
                </div>

                <input type="file" name="avatar" class="form-control" accept="image/*">

                <small class="text-muted">
                    Kosongkan jika tidak ingin menganti avatar.
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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection