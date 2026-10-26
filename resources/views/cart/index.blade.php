
@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')

<div class="container py-4 py-lg-5">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <span class="text-success fw-semibold small">
                <i class="bi bi-basket2 me-1"></i> BELANJA CATERING
            </span>
            <h2 class="fw-bold mt-2 mb-1">Keranjang Saya</h2>
            <p class="text-muted mb-0">
                Periksa menu dan jumlah pesanan sebelum checkout.
            </p>
        </div>

        <a href="{{ route('customer.packages.index') }}"
           class="btn btn-outline-success rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($cart) > 0)

        @php
            $total = 0;
            $totalItem = 0;

            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
                $totalItem += $item['quantity'];
            }
        @endphp

        <div class="row g-4">

            {{-- DAFTAR MENU --}}
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Menu Pilihan</h5>
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                {{ count($cart) }} jenis menu
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        @foreach($cart as $key => $item)

                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                            @endphp

                            <div class="row g-3 align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                                {{-- GAMBAR --}}
                                <div class="col-4 col-md-3">
                                    @if(!empty($item['image']))
                                        <img
                                            src="{{ asset('storage/' . $item['image']) }}"
                                            alt="{{ $item['name'] }}"
                                            class="w-100 rounded-3"
                                            style="height: 120px; object-fit: cover;"
                                        >
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                             style="height: 120px;">
                                            <i class="bi bi-image text-secondary fs-2"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- INFORMASI --}}
                                <div class="col-8 col-md-4">
                                    <h6 class="fw-bold mb-2">{{ $item['name'] }}</h6>

                                    <div class="text-success fw-semibold">
                                        Rp{{ number_format($item['price'], 0, ',', '.') }}
                                    </div>

                                    <small class="text-muted">Harga per item/porsi</small>
                                </div>

                                {{-- JUMLAH --}}
                                <div class="col-7 col-md-3">
                                    <form action="{{ route('cart.update', $key) }}"
                                          method="POST"
                                          class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            class="form-control"
                                            style="max-width: 85px;"
                                            aria-label="Jumlah {{ $item['name'] }}"
                                            required
                                        >

                                        <button type="submit"
                                                class="btn btn-outline-success"
                                                title="Perbarui jumlah"
                                                aria-label="Perbarui jumlah">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <small class="text-muted d-block mt-2">
                                        Ubah jumlah lalu tekan centang.
                                    </small>
                                </div>

                                {{-- SUBTOTAL DAN HAPUS --}}
                                <div class="col-5 col-md-2 text-end">
                                    <div class="fw-bold mb-3">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </div>

                                    <form action="{{ route('cart.remove', $key) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus {{ addslashes($item['name']) }} dari keranjang?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-trash me-md-1"></i>
                                            <span class="d-none d-md-inline">Hapus</span>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        @endforeach

                    </div>
                </div>

                {{-- KOSONGKAN --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Pastikan jumlah pesanan sudah benar.
                    </small>

                    <form action="{{ route('cart.clear') }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin mengosongkan seluruh keranjang?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i>Kosongkan Keranjang
                        </button>
                    </form>
                </div>

            </div>

            {{-- RINGKASAN --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm rounded-4 sticky-lg-top"
                     style="top: 24px;">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Jenis menu</span>
                            <span class="fw-semibold">{{ count($cart) }} jenis</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total jumlah</span>
                            <span class="fw-semibold">
                                {{ number_format($totalItem, 0, ',', '.') }} item/porsi
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <span class="fw-semibold">Subtotal</span>
                            <span class="fw-bold text-success text-end fs-5">
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>

                        <small class="text-muted d-block mb-4">
                            Biaya pengiriman, jika ada, akan dikonfirmasi sesuai metode pengiriman dan jarak.
                        </small>

                        <a href="{{ route('checkout.index') }}"
                           class="btn btn-success btn-lg w-100 rounded-pill">
                            Lanjut ke Checkout
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Periksa kembali sebelum membuat pesanan.
                            </small>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    @else

        {{-- KERANJANG KOSONG --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5 px-4">
                <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                     style="width: 90px; height: 90px;">
                    <i class="bi bi-cart3" style="font-size: 2.5rem;"></i>
                </div>

                <h4 class="fw-bold">Keranjangmu masih kosong</h4>

                <p class="text-muted mx-auto mb-4" style="max-width: 420px;">
                    Belum ada menu yang dipilih. Jelajahi menu catering dan tambahkan pilihanmu ke keranjang.
                </p>

                <a href="{{ route('customer.packages.index') }}"
                   class="btn btn-success rounded-pill px-4 py-2">
                    <i class="bi bi-grid me-2"></i>Lihat Menu Catering
                </a>
            </div>
        </div>

    @endif

</div>

@endsection
