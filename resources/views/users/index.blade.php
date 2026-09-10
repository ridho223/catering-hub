@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">User Management</h4>
            <p class="text-muted mb-0">
                Kelola akun Admin dan Superadmin.
            </p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah User
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">                            
                                         @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                                width="40"
                                                height="40"
                                                class="rounded-circle object-fit-cover"
                                                alt="{{ $user->name }}"
                                                >
                                        @else
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif                                          

                                        <strong>
                                            {{ $user->name }}
                                        </strong>

                                    </div>
                                </td>

                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>

                                    @if ($user->level === 'Superadmin')
                                        <span class="badge bg-dark">
                                            Superadmin
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            Admin
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($user->status === 'Aktif')
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Non-Aktif
                                        </span>
                                    @endif

                                </td>

                                <td class="text-end">

                                  <div class="d-flex gap-1">

                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if ($user->id !== auth()->id())

                                        <form
                                            action="{{ route('users.destroy', $user) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    @endif

                                  </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada user.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection