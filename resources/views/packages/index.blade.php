@extends('layouts.admin')
@section('title','Paket Catering')

@php
    use Illuminate\Support\Str;
@endphp
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Paket Catering
            </h4>

            <p class="text-muted mb-0">
                Kelola paket catering yang tersedia untuk customer.
            </p>
            
        </div>

        <a href="{{route('packages.create')}}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Paket
        </a>
    </div>

    <div class="row g-4">

        @forelse ($packages as $package )
            
            <div class="col-md-6 col-xl-4">

                <div class="card border-0 shadow-sm h-100">

                    {{-- //image --}}
                    <div style="height:220px; overflow:hidden;">

                        @if ($package->image)
                            <img src="{{asset('storage/' . $package->image)}}" alt="{{$package->name}}"
                                class="w-100 h-100" style="object-fit:cover;">
                        @else
                            <div class="bg-light h-100 d-flex align-items-center justify-content-between">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif

                    </div>

                    <div class="card-body p-4 d-flex flex-column">

                        <div class="mb-2">

                            @if ($package->status === 'Tersedia')
                                <span class="badge badge-success">
                                    Tersedia
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    Tidak Tersedia
                                </span>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-2">
                            {{$package->name}}
                        </h5>

                        <p class="text-muted small mb-3">
                            {{Str::limit($package->description,100)}}
                        </p>

                        {{-- Price --}}
                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Harga per porsi
                            </small>

                            <span class="fw-bold text-success fs-5">
                                Rp.{{number_format($package->price, 0,',','.')}}
                            </span>
                        </div>

                        {{-- Package content --}}
                        <div class="mb-4">

                            <small class="text-muted d-block mb-2">
                                Isi Paket
                            </small>

                            <div class="d-flex flex-wrap gap-1">

                                @foreach ($package->details->take(5) as $detail )

                                    <span class="badge bg-light text-dark border">
                                        {{$detail->menu->name}}
                                    </span>

                                @endforeach

                                @if ($package->details->count() > 5)
                                    <span class="badge bg-light text-dark border">
                                        +{{$package->details->count() - 5}}
                                    </span>
                                @endif

                            </div>

                        </div>

                        {{-- Action --}}
                        <div class="mt-auto d-flex gap-2">

                            <a href="{{route('packages.show', $package)}}"
                                class="btn btn-outline-success flex-grow-1">

                                <i class="bi bi-eye me-1"></i>
                                Detail
                            </a>

                            <a href="{{route('packages.edit',$package)}}"
                                class="btn btn-outline-primary">

                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{route('packages.destroy',$package)}}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus paket ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty

            <div class="col-12">

                <div class="text-center py-5">
                    <i class="bi bi-box-seam fs-1 text-muted"></i>

                    <h5 class="fw-bold mt-3">
                        Belum ada paket
                    </h5>

                    <p class="text-muted">
                        Tambahkan paket catering pertama.
                    </p>

                    <a href="{{route('packages.create')}}" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Paket
                    </a>
                </div>
            </div>
            
        @endforelse

    </div>
</div>
@endsection
