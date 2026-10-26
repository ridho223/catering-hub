@extends('layouts.app')

@section('title', 'Detail Permintaan Custom')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">


        @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

        <div>

            <a href="{{ route('customer.custom.index') }}"
               class="text-decoration-none text-muted small">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Permintaan Saya

            </a>

            <h2 class="fw-bold mt-2 mb-1">
                {{ $customerRequest->title }}
            </h2>

            <p class="text-muted mb-0">
                Detail pengajuan custom catering
            </p>

        </div>


        @php
            $statusClass = match($customerRequest->status) {
                'Diajukan' => 'bg-primary',
                'Ditinjau' => 'bg-info text-dark',
                'Menunggu Persetujuan' => 'bg-warning text-dark',
                'Disetujui' => 'bg-success',
                'Ditolak' => 'bg-danger',
                'Selesai' => 'bg-dark',
                default => 'bg-secondary',
            };
        @endphp

        <span class="badge {{ $statusClass }} px-3 py-2 fs-6">
            {{ $customerRequest->status }}
        </span>

    </div>


    <div class="row g-4">

        {{-- KIRI --}}
        <div class="col-lg-8">

            {{-- Informasi Acara --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Informasi Acara
                    </h5>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <div class="text-muted small mb-1">
                                Jumlah Tamu
                            </div>

                            <div class="fw-semibold">
                                <i class="bi bi-people me-2"></i>
                                {{ $customerRequest->guest_count }} orang
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small mb-1">
                                Tanggal Acara
                            </div>

                            <div class="fw-semibold">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ $customerRequest->event_date->format('d M Y') }}
                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted small mb-1">
                                Alamat Acara
                            </div>

                            <div class="fw-semibold">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $customerRequest->address }}
                            </div>

                        </div>          


                        <div class="col-12">

                            <div class="text-muted small mb-1">
                                Catatan
                            </div>

                            <div>
                                {{ $customerRequest->description ?: '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Menu yang Dipilih --}}
            {{-- ISI PAKET DAN MENU CUSTOM --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-basket2 me-2 text-success"></i>
                        Detail Menu Pesanan
                    </h5>

                    {{-- PAKET --}}
                    @if ($customerRequest->packages->isNotEmpty())
                        @foreach ($customerRequest->packages as $package)
                            <div class="border rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            {{ $package->name }}
                                        </h6>
                                        <small class="text-muted">
                                            Paket catering
                                        </small>
                                    </div>

                                    <span class="badge bg-success-subtle text-success">
                                        {{ $package->pivot->quantity }} paket
                                    </span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Menu dalam Paket</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($package->details as $packageDetail)
                                                @php
                                                    $replacement = $customerRequest->details
                                                        ->first(function ($detail) use ($packageDetail) {
                                                            return $detail->customization_type === 'replacement'
                                                                && (int) $detail->replaces_menu_id
                                                                    === (int) $packageDetail->menus_id;
                                                        });
                                                @endphp

                                                <tr>
                                                    <td>
                                                        @if ($replacement)
                                                            <div class="text-muted text-decoration-line-through">
                                                                {{ $packageDetail->menu->name ?? 'Menu asli tidak ditemukan' }}
                                                            </div>

                                                            <div class="fw-semibold text-success mt-1">
                                                                <i class="bi bi-arrow-down-right me-1"></i>
                                                                {{ $replacement->menu->name ?? 'Menu pengganti tidak ditemukan' }}
                                                            </div>

                                                            <small class="text-muted">
                                                                Menu pengganti
                                                            </small>
                                                        @else
                                                            <div class="fw-semibold">
                                                                {{ $packageDetail->menu->name ?? 'Menu tidak ditemukan' }}
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ (int) $packageDetail->quantity
                                                            * (int) $package->pivot->quantity }}
                                                        porsi
                                                    </td>

                                                    <td>
                                                        @if ($replacement)
                                                            <span class="badge bg-success">
                                                                Diganti
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-dark border">
                                                                Menu paket
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="border-top mt-3 pt-3 d-flex justify-content-between gap-3">
                                    <span class="text-muted">Harga paket</span>
                                    <strong>
                                        Rp{{ number_format(
                                            (float) $package->pivot->price
                                            * (int) $package->pivot->quantity,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>
                                </div>

                                <small class="text-muted d-block mt-2">
                                    Penggantian menu sudah termasuk dalam penyesuaian harga paket
                                    dan tidak dihitung sebagai menu tambahan.
                                </small>
                            </div>
                        @endforeach
                    @endif

                    {{-- MENU TAMBAHAN --}}
                    @php
                        $additionalDetails = $customerRequest->details->filter(
                            fn ($detail) => $detail->customization_type !== 'replacement'
                        );
                    @endphp

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-plus-circle me-2 text-success"></i>
                        Menu Tambahan
                    </h6>

                    @forelse ($additionalDetails as $detail)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $detail->menu->name ?? 'Menu tidak ditemukan' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $detail->quantity }} porsi
                                    </small>
                                </div>

                                <div class="text-end">
                                    <small class="text-muted d-block">
                                        Harga per porsi
                                    </small>
                                    <strong>
                                        Rp{{ number_format(
                                            (float) ($detail->approved_price
                                                ?? $detail->requested_price
                                                ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>
                                </div>
                            </div>

                            <div class="border-top mt-3 pt-3 d-flex justify-content-between">
                                <span class="text-muted">Subtotal</span>
                                <strong>
                                    Rp{{ number_format(
                                        (float) ($detail->approved_price
                                            ?? $detail->requested_price
                                            ?? 0)
                                        * (int) $detail->quantity,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">
                            Tidak ada menu tambahan di luar isi paket.
                        </p>
                    @endforelse

                    @if ($customerRequest->packages->isEmpty()
                        && $additionalDetails->isEmpty())
                        <p class="text-muted mb-0">
                            Belum ada menu yang dipilih.
                        </p>
                    @endif

                </div>
            </div>

        </div>


        {{-- KANAN --}}
        <div class="col-lg-4">

            {{-- Ringkasan Harga --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Ringkasan
                    </h5>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Budget
                        </span>

                        <span class="fw-semibold">

                            @if ($customerRequest->estimated_budget)
                                Rp{{ number_format($customerRequest->estimated_budget, 0, ',', '.') }}
                            @else
                                -
                            @endif

                        </span>

                    </div>


                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Harga Final
                        </span>

                        <strong class="fs-5">

                            @if ($customerRequest->final_price)
                                Rp{{ number_format($customerRequest->final_price, 0, ',', '.') }}
                            @else
                                Belum ditentukan
                            @endif

                        </strong>

                    </div>

                </div>

            </div>


            {{-- Status --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Status Pengajuan
                    </h5>

                    <div class="d-flex align-items-start gap-3 mb-3">

                        <i class="bi bi-check-circle-fill text-success fs-5"></i>

                        <div>

                            <div class="fw-semibold">
                                Pengajuan dibuat
                            </div>

                            <small class="text-muted">
                                {{ $customerRequest->created_at->format('d M Y H:i') }}
                            </small>

                        </div>

                    </div>


                    @if (in_array($customerRequest->status, [
                        'Ditinjau',
                        'Menunggu Persetujuan',
                        'Disetujui',
                        'Selesai'
                    ]))

                        <div class="d-flex align-items-start gap-3 mb-3">

                            <i class="bi bi-check-circle-fill text-success fs-5"></i>

                            <div>
                                <div class="fw-semibold">
                                    Pengajuan ditinjau
                                </div>

                                <small class="text-muted">
                                    Pengajuan sedang diproses oleh admin.
                                </small>
                            </div>

                        </div>

                    @endif


                    @if ($customerRequest->status === 'Menunggu Persetujuan')

                        <div class="alert alert-warning mb-0 small">

                            <i class="bi bi-info-circle me-1"></i>

                            Admin sudah menentukan harga.
                            Silakan periksa detail pengajuan dan lakukan persetujuan.

                        </div>

                    @elseif ($customerRequest->status === 'Disetujui')

                        <div class="alert alert-success mb-0 small">

                            <i class="bi bi-check-circle me-1"></i>

                            Pengajuan kamu sudah disetujui.

                        </div>

                    @elseif ($customerRequest->status === 'Ditolak')

                        <div class="alert alert-danger mb-0 small">

                            @if ($customerRequest->rejected_by === 'customer')
                                <i class="bi bi-x-circle me-1"></i>
                                Kamu menolak peawaran harga ini
                            @elseif ($customerRequest->rejected_by === 'admin')
                                <i class="bi bi-x-circle me-1"></i>
                                Permintaan custom ini ditolak oleh admin
                            @else
                                <i class="bi bi-x-circle me-1"></i>
                                Permintaan custom ini ditolak.
                            @endif


                        </div>

                    @elseif ($customerRequest->status === 'Diajukan')

                        <div class="alert alert-info mb-0 small">

                            <i class="bi bi-info-circle me-1"></i>

                            Pengajuan berhasil dikirim dan sedang menunggu ditinjau admin.

                        </div>

                    @endif

                </div>

            </div>


            {{-- Catatan Admin --}}
            @if ($customerRequest->admin_notes)

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">
                            Catatan Admin
                        </h5>

                        <p class="mb-0 text-muted">
                            {{ $customerRequest->admin_notes }}
                        </p>

                        

                    </div>

                </div>

            @endif

  
            @if ($customerRequest->status === 'Menunggu Persetujuan')
                <div class="card border-0 shadow-sm mt-4 overflow-hidden">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-start gap-3 mb-4">
                            <div class="rounded-3 bg-success-subtle text-success p-3">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">Konfirmasi Pesanan</h5>
                                <p class="text-muted small mb-0">
                                    Periksa harga dan pilih cara menerima pesanan sebelum menyetujui.
                                </p>
                            </div>
                        </div>

                        {{-- Ringkasan harga --}}
                        <div class="rounded-3 border p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold">Total harga catering</div>
                                    <small class="text-muted">
                                        Belum termasuk ongkir jika berlaku
                                    </small>
                                </div>

                                <div class="text-end">
                                    <div class="text-success fw-bold fs-4">
                                        Rp{{ number_format($customerRequest->final_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form
                            action="{{ route('customer.custom.approve', $customerRequest->id) }}"
                            method="POST"
                        >
                            @csrf

                            <label class="form-label fw-bold mb-3">
                                <i class="bi bi-truck me-2"></i>
                                Bagaimana kamu ingin menerima pesanan?
                            </label>

                            <div class="row g-3 mb-3">

                                {{-- Ambil sendiri --}}
                                <div class="col-md-6">
                                    <label
                                        for="custom_pickup"
                                        class="border rounded-3 p-3 d-block h-100"
                                        style="cursor: pointer;"
                                    >
                                        <div class="d-flex gap-3 align-items-start">
                                            <input
                                                class="form-check-input mt-1"
                                                type="radio"
                                                name="delivery_method"
                                                id="custom_pickup"
                                                value="pickup"
                                                {{ old('delivery_method', 'pickup') === 'pickup' ? 'checked' : '' }}
                                                required
                                            >

                                            <div>
                                                <div class="fw-bold mb-1">
                                                    <i class="bi bi-shop me-1"></i>
                                                    Ambil sendiri
                                                </div>

                                                <div class="text-success fw-semibold small mb-2">
                                                    Gratis
                                                </div>

                                                <small class="text-muted">
                                                    Kamu mengambil pesanan langsung di tempat catering.
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                {{-- Diantar --}}
                                <div class="col-md-6">
                                    <label
                                        for="custom_delivery"
                                        class="border rounded-3 p-3 d-block h-100"
                                        style="cursor: pointer;"
                                    >
                                        <div class="d-flex gap-3 align-items-start">
                                            <input
                                                class="form-check-input mt-1"
                                                type="radio"
                                                name="delivery_method"
                                                id="custom_delivery"
                                                value="delivery"
                                                {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}
                                                required
                                            >

                                            <div>
                                                <div class="fw-bold mb-1">
                                                    <i class="bi bi-truck me-1"></i>
                                                    Diantar ke alamat
                                                </div>

                                                <div class="text-success fw-semibold small mb-2">
                                                    Gratis hingga 5 km
                                                </div>

                                                <small class="text-muted">
                                                    Untuk jarak lebih dari 5 km, admin akan menentukan ongkir.
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                            </div>

                            @error('delivery_method')
                                <div class="text-danger small mb-3">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="alert alert-info border-0 small mb-4">
                                <i class="bi bi-info-circle me-1"></i>
                                Setelah menyetujui harga, pesanan akan dibuat.
                                Jika memilih pengantaran, pembayaran menunggu konfirmasi jarak
                                dan ongkir dari admin.
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">

                                <a
                                    href="{{ route('customer.custom.index') }}"
                                    class="btn btn-light border"
                                >
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Kembali
                                </a>

                                <div class="d-flex gap-2">

                                    <button
                                        type="submit"
                                        form="reject-custom-{{ $customerRequest->id }}"
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Yakin ingin menolak penawaran harga ini?')"
                                    >
                                        Tolak Penawaran
                                    </button>

                                    <button
                                        type="submit"
                                        class="btn btn-success px-4"
                                        onclick="return confirm('Setujui harga dan metode penerimaan pesanan ini?')"
                                    >
                                        <i class="bi bi-check-lg me-1"></i>
                                        Setujui Pesanan
                                    </button>

                                </div>
                            </div>
                        </form>

                        {{-- Form penolakan dipisahkan agar tidak bertumpuk dengan form persetujuan --}}
                        <form
                            id="reject-custom-{{ $customerRequest->id }}"
                            action="{{ route('customer.custom.reject', $customerRequest->id) }}"
                            method="POST"
                            class="d-none"
                        >
                            @csrf
                        </form>

                    </div>
                </div>
            @endif
        
        </div>

    </div>

</div>

@endsection