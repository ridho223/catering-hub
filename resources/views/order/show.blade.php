@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-receipt text-success me-2"></i>
                Detail Pesanan
            </h4>

            <p class="text-muted mb-0">
                Informasi lengkap pesanan #{{ $order->id }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    {{-- Informasi Customer --}}
    <div class="row g-4 mb-4">

        <div class="col-md-6">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-header bg-success text-white rounded-top-4">

                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-person me-2"></i>
                        Data Customer
                    </h6>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Nama Customer
                        </small>

                        <div class="fw-semibold">
                            {{ $order->customer->name }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            No. Telepon
                        </small>

                        <div class="fw-semibold">
                            {{ $order->customer->phone }}
                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Alamat
                        </small>

                        <div class="fw-semibold">
                            {{ $order->customer->address }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Informasi Order --}}
        <div class="col-md-6">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-header bg-success text-white rounded-top-4">

                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-cart me-2"></i>
                        Informasi Pesanan
                    </h6>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Tanggal Pesanan
                        </small>

                        <div class="fw-semibold">

                            {{ date('d-m-Y', strtotime($order->order_date)) }}

                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Tanggal Kirim
                        </small>

                        <div class="fw-semibold">

                            {{ date('d-m-Y', strtotime($order->delivery_date)) }}

                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Status
                        </small>

                        <div class="mt-1">

                            @if($order->status == 'Pending')

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @elseif($order->status == 'Diproses')

                                <span class="badge bg-info">
                                    Diproses
                                </span>

                            @elseif($order->status == 'Selesai')

                                <span class="badge bg-success">
                                    Selesai
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ $order->status }}
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Detail Menu --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <h6 class="fw-bold mb-1">

                <i class="bi bi-basket text-success me-2"></i>
                Detail Menu

            </h6>

            <small class="text-muted">
                Daftar menu yang dipesan
            </small>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                Menu
                            </th>

                            <th>
                                Harga
                            </th>

                            <th>
                                Jumlah
                            </th>

                            <th class="text-end">
                                Subtotal
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($order->details as $detail)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $detail->menu->name }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->price, 0, ',', '.') }}
                                </td>

                                <td>
                                    {{ $detail->quantity }}
                                </td>

                                <td class="text-end fw-semibold">

                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center text-muted py-4">

                                    Belum ada detail menu.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    <tfoot>

                        <tr>

                            <td colspan="4"
                                class="text-end fw-bold">

                                Total

                            </td>

                            <td class="text-end">

                                <span class="fw-bold text-success fs-5">

                                    Rp {{ number_format($order->total, 0, ',', '.') }}

                                </span>

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>


            {{-- Catatan --}}
            @if($order->notes)

                <div class="mt-4">

                    <h6 class="fw-bold">
                        <i class="bi bi-chat-left-text me-2"></i>
                        Catatan
                    </h6>

                    <div class="alert alert-light border">

                        {{ $order->notes }}

                    </div>

                </div>

            @endif


            {{-- Tombol --}}
            <div class="d-flex justify-content-end gap-2 mt-4">

                <a href="{{ route('orders.edit', $order->id) }}"
                   class="btn btn-warning">

                    <i class="bi bi-pencil me-1"></i>
                    Edit

                </a>


                <form action="{{ route('orders.destroy', $order->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">

                        <i class="bi bi-trash me-1"></i>
                        Hapus

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection