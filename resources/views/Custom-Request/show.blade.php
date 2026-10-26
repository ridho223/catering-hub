@extends('layouts.admin')

@section('title', 'Detail Permintaan Custom')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <a href="{{ route('admin.custom-requests.index') }}"
                class="text-muted text-decoration-none">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali

            </a>

            <h3 class="fw-bold mt-2 mb-1">
                {{ $customRequest->title }}
            </h3>

            <p class="text-muted mb-0">
                Request #{{ $customRequest->id }}
            </p>

        </div>


            <div class="d-flex align-items-center gap-2">

            <span class="badge {{ $statusClass }} px-3 py-2">
                {{ $customRequest->status }}
            </span>

            @if (in_array($customRequest->status, ['Diajukan', 'Ditinjau']))
                <a href="{{ route('admin.custom-requests.edit', $customRequest->id) }}"
                    class="btn btn-primary">

                    <i class="bi bi-pencil-square me-1"></i>
                    Review & Tentukan Harga

                </a>
            @endif

        </div>

    </div>
    


    <div class="row g-4">

        {{-- Informasi Customer --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        Data Customer
                    </h5>

                    <div class="mb-3">

                        <small class="text-muted">
                            Nama
                        </small>

                        <div class="fw-semibold">
                            {{ $customRequest->customer->name ?? '-' }}
                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted">
                            Email
                        </small>

                        <div>
                            {{ $customRequest->customer->email ?? '-' }}
                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted">
                            Nomor HP
                        </small>

                        <div>
                            {{ $customRequest->customer->phone ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Informasi Acara --}}
            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        Informasi Acara
                    </h5>

                    <div class="mb-3">

                        <small class="text-muted">
                            Jumlah Tamu
                        </small>

                        <div class="fw-semibold">
                            {{ $customRequest->guest_count }} orang
                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted">
                            Tanggal Acara
                        </small>

                        <div class="fw-semibold">
                            {{ $customRequest->event_date->format('d M Y') }}
                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted">
                            Alamat
                        </small>

                        <div>
                            {{ $customRequest->address }}
                        </div>

                    </div>

                    <div>

                        <small class="text-muted">
                            Catatan Customer
                        </small>

                        <div>
                            {{ $customRequest->description ?: '-' }}
                        </div>

                    </div>

                </div>

            </div>

                    {{-- Ringkasan --}}
            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        Ringkasan Harga
                    </h5>

                    <div class="row">

                        <div class="col-md-4">

                            <small class="text-muted">
                                Budget Customer
                            </small>

                            <div class="fw-semibold">

                                @if ($customRequest->estimated_budget)
                                    Rp{{ number_format($customRequest->estimated_budget, 0, ',', '.') }}
                                @else
                                    -
                                @endif

                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Harga Final
                            </small>

                            <div class="fw-bold fs-5">

                                @if ($customRequest->final_price)
                                    Rp{{ number_format($customRequest->final_price, 0, ',', '.') }}
                                @else
                                    Belum ditentukan
                                @endif                                

                            </div>

                                @if($customRequest->admin_notes)
                                    <hr>
                                    <div>
                                        <small class="text-muted">
                                            Catatan Admin
                                        </small>

                                        <div class="mt-1">
                                            {{$customRequest->admin_notes}}
                                        </div>
                                    </div>
                                @endif                

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- Menu --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Rincian Menu Permintaan</h5>

                        <span class="badge bg-light text-dark border">
                            {{ $customRequest->details->count() }} perubahan/menu
                        </span>
                    </div>

                    {{-- PAKET YANG DIPILIH --}}
                    @if ($customRequest->packages->isNotEmpty())
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-box-seam text-success me-2"></i>
                            Paket yang Dipilih
                        </h6>

                        @foreach ($customRequest->packages as $package)
                            <div class="border rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-bold">
                                            {{ $package->name }}
                                        </div>
                                        <small class="text-muted">
                                            Harga dasar per paket:
                                            Rp{{ number_format($package->pivot->price, 0, ',', '.') }}
                                        </small>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-semibold">
                                            {{ $package->pivot->quantity }} paket
                                        </div>
                                        <small class="text-muted">
                                            Subtotal:
                                            Rp{{ number_format(
                                                $package->pivot->price * $package->pivot->quantity,
                                                0, ',', '.'
                                            ) }}
                                        </small>
                                    </div>
                                </div>

                                <hr>

                                <div class="fw-semibold small mb-2">
                                    Isi paket awal
                                </div>

                                @foreach ($package->details as $packageDetail)                                
                                    @php
                                        $replacement = $customRequest->details->first(function ($detail) use ($packageDetail) {
                                            return $detail->customization_type === 'replacement'
                                                && (int) $detail->replaces_menu_id === (int) $packageDetail->menus_id;
                                        });
                                    @endphp
                                    <div class="border rounded-3 p-3 mb-2
                                        {{ $replacement ? 'border-warning bg-warning-subtle' : 'bg-light' }}">

                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block mb-1">
                                                    {{ $replacement ? 'Menu asli paket' : 'Isi paket' }}
                                                </small>

                                                <div class="fw-semibold">
                                                    {{ $packageDetail->menu->name ?? 'Menu tidak ditemukan' }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $packageDetail->quantity * $package->pivot->quantity }} porsi
                                                </small>
                                            </div>

                                            @if ($replacement)
                                                <span class="badge bg-warning text-dark">
                                                    Diganti
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success-emphasis">
                                                    Tetap digunakan
                                                </span>
                                            @endif
                                        </div>

                                        @if ($replacement)
                                            <div class="text-center my-2">
                                                <i class="bi bi-arrow-down-circle-fill text-success fs-4"></i>
                                            </div>

                                            <div class="rounded-3 border border-success bg-white p-3">
                                                <small class="text-success fw-semibold d-block mb-1">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    DIGANTI MENJADI
                                                </small>

                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="mt-2">
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Pengganti dalam paket
                                                        </span>

                                                        <div class="fw-bold text-success mt-2">
                                                            {{ $replacement->menu->name ?? 'Menu pengganti tidak ditemukan' }}
                                                        </div>

                                                        <small class="text-muted">
                                                            {{ $replacement->quantity }} porsi
                                                        </small>

                                                        <div class="small text-muted mt-1">
                                                            Harga menu ini sudah diperhitungkan dalam harga paket
                                                            setelah penyesuaian admin.
                                                        </div>
                                                    </div>

                                                    <div class="text-end">
                                                        <small class="text-muted d-block">Harga disetujui</small>

                                                        <strong>
                                                            @if ($replacement->approved_price !== null)
                                                                Rp{{ number_format($replacement->approved_price, 0, ',', '.') }}
                                                            @else
                                                                Belum ditentukan
                                                            @endif
                                                        </strong>
                                                        <small class="text-muted d-block">per porsi</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach                                
                            </div>
                        @endforeach
                    @endif

                    {{-- PERUBAHAN DAN MENU TAMBAHAN --}}
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-arrow-left-right text-success me-2"></i>
                        Perubahan dan Menu Tambahan
                    </h6>

                    @php
                        $additionalDetails = $customRequest->details->filter(function ($detail) {
                            return ($detail->customization_type ?? 'additional') !== 'replacement';
                        });
                    @endphp

                    @forelse ($additionalDetails as $detail)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $detail->menu->name ?? 'Menu tidak ditemukan' }}
                                    </div>
                                    <small class="text-muted">
                                        Menu tambahan di luar isi paket
                                    </small>
                                </div>

                                <span class="badge bg-success-subtle text-success-emphasis">
                                    Tambahan
                                </span>
                            </div>

                            <div class="row g-2 small">
                                <div class="col-sm-4">
                                    <span class="text-muted d-block">Jumlah</span>
                                    <strong>{{ $detail->quantity }} porsi</strong>
                                </div>

                                <div class="col-sm-4">
                                    <span class="text-muted d-block">Harga referensi</span>
                                    <strong>
                                        Rp{{ number_format($detail->requested_price, 0, ',', '.') }}
                                    </strong>
                                    <small class="d-block text-muted">per porsi</small>
                                </div>

                                <div class="col-sm-4">
                                    <span class="text-muted d-block">Harga disetujui</span>
                                    <strong>
                                        @if ($detail->approved_price !== null)
                                            Rp{{ number_format($detail->approved_price, 0, ',', '.') }}
                                        @else
                                            Belum ditentukan
                                        @endif
                                    </strong>
                                    <small class="d-block text-muted">per porsi</small>
                                </div>
                            </div>
                        </div>
                    @empty

                        <div class="alert alert-light border mb-0">
                            Belum ada perubahan atau menu tambahan.
                            @if ($customRequest->packages->isNotEmpty())
                                Customer hanya memilih paket tanpa perubahan isi.
                            @endif
                        </div>
                    @endforelse

                </div>
            </div>

        </div>

    </div>

</div>

@endsection