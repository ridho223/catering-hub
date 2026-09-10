@extends('layouts.admin')

@section('title', 'Ubah Password')

@section('content')

<div class="profile-page">

    <div class="profile-card">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">Ubah Password</h4>

            <p class="text-muted mb-0">
                Pastikan password baru kamu aman dan mudah diingat.
            </p>
        </div>

        <form
            action="{{ route('admin.password.update') }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Password Lama
                </label>

                <input
                    type="password"
                    name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    required
                >

                @error('current_password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Password Baru
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

                <small class="text-muted">
                    Minimal 8 karakter.
                </small>

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                >

            </div>

            <div class="d-flex justify-content-end gap-2">

                <a
                    href="{{ route('admin.profile') }}"
                    class="btn btn-outline-secondary"
                >
                    Batal
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-shield-check me-1"></i>
                    Ubah Password
                </button>

            </div>

        </form>

    </div>

</div>

@endsection