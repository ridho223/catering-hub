
@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
@php
    $initialPackages = old('packages');

    if ($initialPackages === null) {
        $initialPackages = $order->packages->map(function ($row) {
            return [
                'package_id' => $row->package_id,
                'quantity' => $row->quantity,
                'price' => $row->price,
            ];
        })->values()->all();
    }

    if (empty($initialPackages)) {
        $initialPackages = [
            ['package_id' => '', 'quantity' => 1, 'price' => null]
        ];
    }

    $packageData = $packages->map(function ($package) {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'price' => (float) $package->price,
        ];
    })->values()->all();
@endphp

<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="badge text-bg-success mb-2">ADMIN</span>
            <h3 class="fw-bold mb-1">Edit Pesanan</h3>
            <p class="text-muted mb-0">
                Perbarui pesanan #{{ $order->order_code ?? $order->id }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="editOrderForm"
        action="{{ route('orders.update', $order->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        {{-- INFORMASI PESANAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-info-circle text-success me-2"></i>
                    Informasi Pesanan
                </h5>
                <p class="text-muted small mb-0">
                    Informasi pelanggan dan jadwal pesanan.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach ($customers as $customer)
                                <option
                                    value="{{ $customer->id }}"
                                    @selected(old('customer_id', $order->customer_id) == $customer->id)
                                >
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status Pesanan</label>
                        <select name="status" class="form-select" required>
                            @foreach (['Pending', 'Diproses', 'Selesai'] as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(old('status', $order->status) === $status)
                                >
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Pesanan</label>
                        <input
                            type="date"
                            name="order_date"
                            class="form-control"
                            value="{{ old('order_date', \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Pengiriman / Pengambilan</label>
                        <input
                            type="date"
                            name="delivery_date"
                            class="form-control"
                            min="{{ old('order_date', \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d')) }}"
                            value="{{ old('delivery_date', \Illuminate\Support\Carbon::parse($order->delivery_date)->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan Pesanan</label>
                        <textarea
                            name="notes"
                            rows="3"
                            class="form-control"
                            placeholder="Catatan untuk pesanan ini..."
                        >{{ old('notes', $order->notes) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- PILIHAN PAKET --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-box-seam text-success me-2"></i>
                            Paket Catering
                        </h5>
                        <p class="text-muted small mb-0">
                            Pilih paket catering atau paket prasmanan yang tersedia.
                        </p>
                    </div>

                    <button type="button" class="btn btn-outline-success btn-sm" id="addPackage">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Paket
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <div id="packageRows" class="d-flex flex-column gap-3"></div>
                <div id="noPackageMessage" class="text-muted small mt-2" hidden>
                    Belum ada paket yang ditambahkan.
                </div>
            </div>
        </div>

        {{-- RINGKASAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-receipt text-success me-2"></i>
                    Ringkasan Pesanan
                </h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal Paket</span>
                    <strong id="packageSubtotal">Rp0</strong>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Biaya Pengiriman</span>
                    <strong id="shippingSubtotal">Rp0</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">Total Pesanan</span>
                    <strong class="text-success fs-4" id="grandTotal">Rp0</strong>
                </div>

                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Ongkos kirim mengikuti pengaturan pada Detail Pesanan.
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('orders.index') }}" class="btn btn-light border px-4">
                Batal
            </a>

            <button type="submit" class="btn btn-success px-4" id="saveOrderButton">
                <i class="bi bi-check-circle me-1"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- TEMPLATE PAKET --}}
<template id="packageTemplate">
    <div class="border rounded-3 p-3 package-row">
        <div class="row g-3 align-items-end">

            <div class="col-md-6">
                <label class="form-label fw-semibold">Paket Catering / Prasmanan</label>
                <select class="form-select package-select" required>
                    <option value="">-- Pilih Paket --</option>
                        @foreach ($packages as $package)
                            <option
                                value="{{ $package->id }}"
                                data-price="{{ $package->price }}"
                            >
                                {{ $package->name }}
                                (Rp{{ number_format($package->price, 0, ',', '.') }})
                            </option>
                        @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Jumlah Paket</label>
                <input
                    type="number"
                    class="form-control package-quantity"
                    min="1"
                    value="1"
                    required
                >
            </div>

            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger w-100 remove-package">
                    <i class="bi bi-trash me-1"></i>
                    Hapus Paket
                </button>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Harga per paket</span>
                    <strong class="package-price-label">Rp0</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-muted small">Subtotal</span>
                    <strong class="line-subtotal">Rp0</strong>
                </div>
            </div>

            <input type="hidden" class="package-price">
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const packageRows = document.getElementById('packageRows');
    const packageTemplate = document.getElementById('packageTemplate');
    const addPackageButton = document.getElementById('addPackage');
    const form = document.getElementById('editOrderForm');

    const initialPackages = @json(array_values($initialPackages));
    const shippingFee = Number(@json((float) ($order->delivery_fee ?? 0)));

    let packageIndex = 0;

    const money = value => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value) || 0);

    function updateEmptyMessage() {
        document.getElementById('noPackageMessage').hidden =
            packageRows.querySelectorAll('.package-row').length > 0;
    }

    function calculateTotal() {
        let subtotal = 0;

        packageRows.querySelectorAll('.package-row').forEach(row => {
            const quantity = Number(
                row.querySelector('.package-quantity').value || 0
            );

            const price = Number(
                row.querySelector('.package-price').value || 0
            );

            const lineTotal = quantity * price;

            row.querySelector('.line-subtotal').textContent = money(lineTotal);
            subtotal += lineTotal;
        });

        document.getElementById('packageSubtotal').textContent = money(subtotal);
        document.getElementById('shippingSubtotal').textContent = money(shippingFee);
        document.getElementById('grandTotal').textContent =
            money(subtotal + shippingFee);
    }

    function addPackageRow(selectedId = '', quantity = 1, savedPrice = null) {
        const fragment = packageTemplate.content.cloneNode(true);
        const row = fragment.querySelector('.package-row');

        const select = row.querySelector('.package-select');
        const quantityInput = row.querySelector('.package-quantity');
        const priceInput = row.querySelector('.package-price');

        const index = packageIndex++;

        select.name = `packages[${index}][package_id]`;
        quantityInput.name = `packages[${index}][quantity]`;
        priceInput.name = `packages[${index}][price]`;

        select.value = String(selectedId || '');
        quantityInput.value = quantity || 1;

        function updatePrice() {
            const option = select.selectedOptions[0];
            const catalogPrice = Number(option?.dataset.price || 0);

            priceInput.value = catalogPrice;
            row.querySelector('.package-price-label').textContent = money(catalogPrice);
            calculateTotal();
        }

        // Harga paket diambil dari katalog, bukan harga bebas dari browser.
        [...select.options].forEach(option => {
            const packageInfo = @json($packageData).find(
                item => String(item.id) === String(option.value)
            );

            if (packageInfo) {
                option.dataset.price = packageInfo.price;
            }
        });

        if (selectedId) {
            const option = select.selectedOptions[0];
            const catalogPrice = Number(option?.dataset.price || 0);

            // Pertahankan harga pesanan lama jika tersedia.
            const price = savedPrice !== null && savedPrice !== ''
                ? Number(savedPrice)
                : catalogPrice;

            priceInput.value = price;
            row.querySelector('.package-price-label').textContent = money(price);
        } else {
            updatePrice();
        }

        select.addEventListener('change', updatePrice);
        quantityInput.addEventListener('input', calculateTotal);

        packageRows.appendChild(fragment);

        updateEmptyMessage();
        calculateTotal();
    }

    addPackageButton.addEventListener('click', function () {
        addPackageRow();
    });

    packageRows.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-package');

        if (!button) return;

        button.closest('.package-row').remove();
        updateEmptyMessage();
        calculateTotal();
    });

    initialPackages.forEach(item => {
        addPackageRow(
            item.package_id ?? '',
            item.quantity ?? 1,
            item.price ?? null
        );
    });

    form.addEventListener('submit', function (event) {
        const rows = packageRows.querySelectorAll('.package-row');

        if (rows.length === 0) {
            event.preventDefault();
            alert('Tambahkan minimal satu paket catering.');
            return;
        }

        let valid = true;

        rows.forEach(row => {
            const packageId = row.querySelector('.package-select').value;
            const quantity = Number(row.querySelector('.package-quantity').value);

            if (!packageId || quantity < 1) {
                valid = false;
            }
        });

        if (!valid) {
            event.preventDefault();
            alert('Pilih paket dan pastikan jumlahnya minimal 1.');
        }
    });

    updateEmptyMessage();
    calculateTotal();
});
</script>
@endpush
