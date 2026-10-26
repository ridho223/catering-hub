
@extends('layouts.app')

@section('title', 'Permintaan Custom Saya')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row
                justify-content-between align-items-md-center
                gap-3 mb-4">

        <div>          

            <h2 class="fw-bold mb-2">
                Permintaan Custom Saya
            </h2>

            <p class="text-muted mb-0">
                Pantau permintaan catering, penawaran harga,
                dan status persetujuan kamu.
            </p>
        </div>

        <a href="{{ route('customer.custom.create') }}"
           class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i>
            Buat Permintaan
        </a>
    </div>


    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif


    {{-- INFO ALUR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-start gap-3">
                <div class="custom-icon bg-success-subtle text-success">
                    <i class="bi bi-info-circle fs-4"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-1">
                        Bagaimana proses custom catering?
                    </h6>

                    <p class="text-muted small mb-0">
                        Admin akan memeriksa permintaan kamu dan
                        menentukan harga. Setelah penawaran tersedia,
                        kamu bisa menyetujui atau menolaknya.
                        Jika memilih pengantaran, ongkir akan
                        dikonfirmasi sesuai jarak.
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- DAFTAR PERMINTAAN --}}
    @forelse ($customRequests as $customRequest)

        @php
            $statusConfig = match ($customRequest->status) {
                'Diajukan' => [
                    'class' => 'text-bg-primary',
                    'icon' => 'bi-send',
                    'title' => 'Menunggu pemeriksaan admin',
                ],
                'Menunggu Persetujuan' => [
                    'class' => 'text-bg-warning',
                    'icon' => 'bi-clock',
                    'title' => 'Periksa penawaran harga',
                ],
                'Disetujui' => [
                    'class' => 'text-bg-success',
                    'icon' => 'bi-check-circle',
                    'title' => 'Permintaan disetujui',
                ],
                'Ditolak' => [
                    'class' => 'text-bg-danger',
                    'icon' => 'bi-x-circle',
                    'title' => 'Permintaan ditolak',
                ],
                'Selesai' => [
                    'class' => 'text-bg-dark',
                    'icon' => 'bi-flag',
                    'title' => 'Permintaan selesai',
                ],
                default => [
                    'class' => 'text-bg-secondary',
                    'icon' => 'bi-three-dots',
                    'title' => 'Status permintaan',
                ],
            };
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-3 custom-request-card">
            <div class="card-body p-3 p-md-4">

                {{-- JUDUL DAN STATUS --}}
                <div class="d-flex flex-column flex-md-row
                            justify-content-between gap-3">

                    <div class="d-flex gap-3">
                        <div class="custom-icon bg-light text-success">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>

                        <div>
                            <div class="text-muted small mb-1">
                                Permintaan #{{ $customRequest->id }}
                                <span class="mx-1">·</span>
                                {{ $customRequest->created_at->format('d M Y, H:i') }}
                            </div>

                            <h5 class="fw-bold mb-2">
                                {{ $customRequest->title }}
                            </h5>

                            <p class="text-muted small mb-0">
                                {{ $customRequest->description
                                    ? \Illuminate\Support\Str::limit($customRequest->description, 110)
                                    : 'Tidak ada catatan tambahan.' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-md-end">
                        <span class="badge {{ $statusConfig['class'] }}
                                     rounded-pill px-3 py-2">
                            <i class="bi {{ $statusConfig['icon'] }} me-1"></i>
                            {{ $customRequest->status }}
                        </span>

                        <div class="small text-muted mt-2">
                            {{ $statusConfig['title'] }}
                        </div>
                    </div>

                </div>


                <hr class="my-4">


                {{-- INFORMASI ACARA --}}
                <div class="row g-3">

                    <div class="col-6 col-lg-3">
                        <div class="info-box h-100">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-people me-1"></i>
                                Jumlah tamu
                            </div>

                            <div class="fw-bold">
                                {{ number_format($customRequest->guest_count, 0, ',', '.') }}
                                orang
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="info-box h-100">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-calendar-event me-1"></i>
                                Tanggal acara
                            </div>

                            <div class="fw-bold">
                                {{ \Carbon\Carbon::parse($customRequest->event_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="info-box h-100">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-wallet2 me-1"></i>
                                Budget
                            </div>

                            <div class="fw-bold">
                                @if ($customRequest->estimated_budget !== null)
                                    Rp{{ number_format($customRequest->estimated_budget, 0, ',', '.') }}
                                @else
                                    <span class="text-muted fw-normal">Tidak diisi</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="info-box h-100">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-receipt me-1"></i>
                                Harga penawaran
                            </div>

                            <div class="fw-bold text-success">
                                @if ($customRequest->final_price !== null)
                                    Rp{{ number_format($customRequest->final_price, 0, ',', '.') }}
                                @else
                                    <span class="text-muted fw-normal">
                                        Belum tersedia
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                
                
         

                {{-- AKSI --}}
                <div class="d-flex flex-column flex-sm-row
                            justify-content-between align-items-sm-center
                            gap-2 mt-4 pt-3 border-top">

                    <div class="d-flex flex-wrap gap-2">

                        @if (in_array($customRequest->status, [
                            'Diajukan',
                            'Menunggu Persetujuan',
                            'Ditolak'
                        ]))

                            <a href="{{ route('customer.custom.edit', $customRequest->id) }}"
                               class="btn btn-outline-success rounded-3">
                                <i class="bi bi-pencil me-1"></i>
                                Edit
                            </a>

                            <form
                                action="{{ route('customer.custom.destroy', $customRequest->id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus permintaan custom ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-outline-danger rounded-3">
                                    <i class="bi bi-trash me-1"></i>
                                    Hapus
                                </button>
                            </form>

                        @endif

                    </div>

                    <a href="{{ route('customer.custom.show', $customRequest) }}"
                       class="btn btn-success rounded-3 px-4">
                        Lihat Detail
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                </div>

            </div>
        </div>

    @empty

        {{-- EMPTY STATE --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5 px-4">

                <div class="empty-icon mx-auto mb-3">
                    <i class="bi bi-clipboard2-plus"></i>
                </div>

                <h5 class="fw-bold mb-2">
                    Belum Ada Permintaan Custom
                </h5>

                <p class="text-muted mx-auto mb-4"
                   style="max-width: 430px;">
                    Buat permintaan sesuai kebutuhan acara kamu.
                    Pilih menu, isi jumlah tamu, dan biarkan admin
                    membantu menentukan penawaran catering.
                </p>

                <a href="{{ route('customer.custom.create') }}"
                   class="btn btn-success rounded-3 px-4">
                    <i class="bi bi-plus-lg me-2"></i>
                    Buat Permintaan Pertama
                </a>

            </div>
        </div>

    @endforelse

</div>


<style>
    .custom-request-card {
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .custom-request-card:hover {
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .07) !important;
    }

    .custom-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }

    .info-box {
        padding: 14px;
        background: #f8f9fa;
        border-radius: 12px;
        overflow-wrap: anywhere;
    }

    .empty-icon {
        width: 76px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e8f5ec;
        color: #198754;
        border-radius: 22px;
        font-size: 2rem;
    }

    @media (max-width: 575.98px) {
        .info-box {
            padding: 12px;
        }
    }
</style>
@endsection
