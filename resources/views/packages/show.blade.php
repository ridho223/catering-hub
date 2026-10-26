@extends('layouts.admin')

@section('title', 'Detail Paket')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <a
            href="{{ route('packages.index') }}"
            class="text-decoration-none"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Paket
        </a>

    </div>


    <div class="row g-4">

        {{-- FOTO --}}
        <div class="col-lg-5">

            <div class="card border-0 shadow-sm overflow-hidden">

                @if ($package->image)

                    <img
                        src="{{ asset('storage/' . $package->image) }}"
                        alt="{{ $package->name }}"
                        class="w-100"
                        style="height: 350px; object-fit: cover;"
                    >

                @else

                    <div
                        class="bg-light d-flex align-items-center justify-content-center"
                        style="height: 350px;"
                    >

                        <i class="bi bi-image fs-1 text-muted"></i>

                    </div>

                @endif

            </div>

        </div>


        {{-- INFORMASI --}}
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    @if ($package->status === 'Tersedia')

                        <span class="badge bg-success mb-3">
                            Tersedia
                        </span>

                    @else

                        <span class="badge bg-secondary mb-3">
                            Tidak Tersedia
                        </span>

                    @endif


                    <h3 class="fw-bold">
                        {{ $package->name }}
                    </h3>


                    <p class="text-muted">
                        {{ $package->description }}
                    </p>


                    <h4 class="fw-bold text-success mb-4">

                        Rp {{ number_format($package->price, 0, ',', '.') }}

                        <small class="text-muted fs-6">
                            / porsi
                        </small>

                    </h4>


                    <hr>


                    <h5 class="fw-bold mb-3">
                        Isi Paket
                    </h5>


                    <div class="row g-2">

                        @foreach ($package->details as $detail)

                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <i class="bi bi-check-circle text-success me-2"></i>

                                    {{ $detail->menu->name }}

                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div class="mt-4 d-flex gap-2">

                        <a
                            href="{{ route('packages.edit', $package) }}"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-pencil me-1"></i>
                            Edit Paket

                        </a>

                        <a
                            href="{{ route('packages.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Kembali
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection