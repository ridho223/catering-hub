
@extends('layouts.app')

@section('title', 'Paket Catering')

@section('content')
<style>
    .pkg-page { background: #f8faf9; min-height: 80vh; }
    .pkg-card { border: 1px solid #edf0ed; border-radius: 18px; overflow: hidden; transition: transform .2s, box-shadow .2s; }
    .pkg-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(20, 50, 30, .09) !important; }
    .pkg-image { height: 205px; background: #edf4ee; position: relative; overflow: hidden; }
    .pkg-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .pkg-card:hover .pkg-image img { transform: scale(1.035); }
    .pkg-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: #72947a; font-size: 3rem; }
    .pkg-price { color: #187447; letter-spacing: -.4px; }
    .pkg-content-list { display: flex; flex-wrap: wrap; gap: 6px; }
    .pkg-content-chip { background: #f2f7f3; color: #45644c; padding: 5px 9px; border-radius: 7px; font-size: .76rem; }
    .pkg-heading { background: linear-gradient(120deg, #eaf5ed, #f8faf9); border-radius: 20px; }
</style>

<div class="pkg-page py-4 py-lg-5">
    <div class="container">

        <div class="pkg-heading p-4 p-lg-5 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <span class="text-success fw-semibold small">
                        <i class="bi bi-basket2 me-1"></i> PILIHAN CATERING
                    </span>
                    <h1 class="fw-bold mt-2 mb-2">Paket catering untuk acaramu</h1>
                    <p class="text-secondary mb-0">
                        Pilih paket yang sesuai kebutuhan. Lihat isi paket dan tentukan jumlah porsi sebelum memesan.
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Daftar paket</h5>
                <small class="text-muted">Pilih paket, lalu lihat detailnya.</small>
            </div>
            <span class="badge rounded-pill text-bg-light border px-3 py-2">
                {{ $packages->count() }} paket
            </span>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse($packages as $package)
                <div class="col-sm-6 col-xl-4">
                    <article class="card pkg-card shadow-sm h-100 bg-white">

                        <div class="pkg-image">
                            @if($package->image)
                                <img src="{{ asset('storage/' . $package->image) }}"
                                     alt="{{ $package->name }}"
                                     loading="lazy">
                            @else
                                <div class="pkg-placeholder">
                                    <i class="bi bi-egg-fried"></i>
                                </div>
                            @endif

                            <span class="badge bg-white text-success shadow-sm position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Paket tersedia
                            </span>
                        </div>

                        <div class="card-body p-3 p-lg-4 d-flex flex-column">
                            <h5 class="fw-bold mb-2">{{ $package->name }}</h5>

                            <p class="text-muted small mb-3">
                                {{ \Illuminate\Support\Str::limit($package->description ?: 'Paket catering untuk berbagai kebutuhan acara.', 95) }}
                            </p>

                            <div class="mb-3">
                                <small class="text-muted d-block">Harga per porsi</small>
                                <div class="pkg-price fw-bold fs-4">
                                    Rp{{ number_format($package->price, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="border-top pt-3 mb-4">
                                <div class="small fw-semibold mb-2">
                                    <i class="bi bi-card-checklist text-success me-1"></i>
                                    Isi paket
                                </div>

                                @if($package->details->isNotEmpty())
                                    <div class="pkg-content-list">
                                        @foreach($package->details->take(4) as $detail)
                                            @if($detail->menu)
                                                <span class="pkg-content-chip">
                                                    {{ $detail->menu->name }}
                                                </span>
                                            @endif
                                        @endforeach

                                        @if($package->details->count() > 4)
                                            <span class="pkg-content-chip">
                                                +{{ $package->details->count() - 4 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted">Detail isi paket belum tersedia.</small>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <a href="{{ route('customer.packages.show', $package) }}"
                                   class="btn btn-success rounded-3 w-100 py-2">
                                    Lihat Detail Paket
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="bg-white border rounded-4 text-center py-5 px-3">
                        <i class="bi bi-basket3 text-success" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold mt-3">Belum ada paket catering</h5>
                        <p class="text-muted mb-0">Paket yang tersedia akan ditampilkan di halaman ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
