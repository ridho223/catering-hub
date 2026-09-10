@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2 text-warning"></i>
                Edit Pesanan
            </h4>

            <p class="text-muted mb-0">
                Perbarui informasi pesanan #{{ $order->id }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    {{-- Error --}}
    @if ($errors->any())

        <div class="alert alert-danger border-0 shadow-sm">

            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terjadi kesalahan
            </div>

            <ul class="mb-0 ps-4">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('orders.update', $order->id) }}"
          method="POST">

        @csrf
        @method('PUT')


        {{-- Informasi Pesanan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-header bg-success text-white rounded-top-4">

                <h6 class="mb-0 fw-bold">

                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Pesanan

                </h6>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    {{-- Customer --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Customer
                        </label>

                        <select name="customer_id"
                                class="form-select"
                                required>

                            <option value="">
                                -- Pilih Customer --
                            </option>

                            @foreach ($customers as $customer)

                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>

                                    {{ $customer->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Status Pesanan
                        </label>

                        <select name="status"
                                class="form-select"
                                required>

                            <option value="Pending"
                                {{ old('status', $order->status) == 'Pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="Diproses"
                                {{ old('status', $order->status) == 'Diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>

                            <option value="Selesai"
                                {{ old('status', $order->status) == 'Selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>

                        </select>

                    </div>


                    {{-- Tanggal Pesanan --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Tanggal Pesanan
                        </label>

                        <input type="date"
                               name="order_date"
                               class="form-control"
                               value="{{ old('order_date', date('Y-m-d', strtotime($order->order_date))) }}"
                               required>

                    </div>


                    {{-- Tanggal Kirim --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Tanggal Kirim
                        </label>

                        <input type="date"
                               name="delivery_date"
                               class="form-control"
                               value="{{ old('delivery_date', date('Y-m-d', strtotime($order->delivery_date))) }}"
                               required>

                    </div>


                    {{-- Total --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Total Pesanan
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input type="text"
                                   class="form-control fw-bold text-success"
                                   value="{{ number_format($order->total, 0, ',', '.') }}"
                                   disabled>

                        </div>

                        <small class="text-muted">
                            Total dihitung berdasarkan detail menu.
                        </small>

                    </div>


                    {{-- Catatan --}}
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Catatan
                        </label>

                        <textarea name="notes"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Masukkan catatan pesanan...">{{ old('notes', $order->notes) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- Detail Pesanan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-basket me-2 text-success"></i>
                            Detail Pesanan
                        </h6>

                        <small class="text-muted">
                            Menu yang terdapat dalam pesanan ini
                        </small>

                    </div>

                    <span class="badge bg-success">
                        {{ $order->details->count() }} Menu
                    </span>

                </div>

            </div>


            <div class="card-body px-4">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

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

                            @forelse ($order->details as $detail)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            {{ $detail->menu->name }}
                                        </div>

                                    </td>

                                    <td>
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                    </td>

                                    <td>

                                        <span class="badge bg-secondary">
                                            {{ $detail->quantity }} ×
                                        </span>

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

                                <td class="text-end fw-bold text-success fs-5">

                                    Rp {{ number_format($order->total, 0, ',', '.') }}

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>


        {{-- Tombol --}}
        <div class="d-flex justify-content-end gap-2 mb-4">

            <a href="{{ route('orders.index') }}"
               class="btn btn-secondary">

                <i class="bi bi-x-circle me-1"></i>
                Batal

            </a>


            <button type="submit"
                    class="btn btn-success px-4">

                <i class="bi bi-check-circle me-1"></i>
                Simpan Perubahan

            </button>

        </div>

    </form>

</div>

@endsection