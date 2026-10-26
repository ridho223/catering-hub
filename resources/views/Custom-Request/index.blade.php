@extends('layouts.admin')

@section('title', 'Permintaan Custom')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">
            Permintaan Custom
        </h3>

        <p class="text-muted mb-0">
            Kelola permintaan catering custom dari customer.
        </p>
    </div>


    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Acara</th>
                            <th>Tanggal</th>
                            <th>Tamu</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($customRequests as $customRequest)

                            @php
                                $statusClass = match($customRequest->status) {
                                    'Diajukan' => 'bg-primary',
                                    'Ditinjau' => 'bg-info text-dark',
                                    'Menunggu Persetujuan' => 'bg-warning text-dark',
                                    'Disetujui' => 'bg-success',
                                    'Ditolak' => 'bg-danger',
                                    'Selesai' => 'bg-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            <tr>

                                <td>
                                    #{{ $customRequest->id }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $customRequest->customer->name ?? '-' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $customRequest->customer->phone ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $customRequest->title }}
                                    </div>

                                    <small class="text-muted">
                                        {{ Str::limit($customRequest->description, 50) }}
                                    </small>
                                </td>

                                <td>
                                    {{ $customRequest->event_date->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $customRequest->guest_count }} orang
                                </td>

                                <td>
                                    @if ($customRequest->estimated_budget)
                                        Rp{{ number_format($customRequest->estimated_budget, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $customRequest->status }}
                                    </span>
                                </td>

                                <td class="text-end">

                                    <a href="{{ route('admin.custom-requests.show', $customRequest->id) }}"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="bi bi-eye me-1"></i>
                                        Detail

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                    Belum ada permintaan custom.

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