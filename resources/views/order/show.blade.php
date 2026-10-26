    @extends('layouts.admin')

    @section('title', 'Detail Pesanan')

    @section('content')

    <div class="container-fluid py-4 order-show-page">

        {{-- Header --}}
        <div class="order-show-header mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="order-show-icon">
                            <i class="bi bi-receipt"></i>
                        </span>
                        <span class="text-success fw-semibold small">MANAJEMEN PESANAN</span>
                    </div>

                    <h3 class="fw-bold mb-1">Detail Pesanan</h3>
                    <p class="text-muted mb-0">
                        Pantau rincian menu, pengiriman, dan pembayaran pesanan.
                    </p>
                </div>
            </div>
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

                {{-- Detail Pesanan --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-basket text-success me-2"></i>
                            Rincian Pesanan
                        </h6>
                        <small class="text-muted">
                            Paket, isi paket, penggantian menu, dan menu tambahan
                        </small>
                    </div>

                    <div class="card-body">

                        {{-- A. Paket yang dipesan --}}
                        @if ($order->packages->isNotEmpty())
                            <h6 class="fw-bold mb-3">Paket Catering</h6>

                            @foreach ($order->packages as $orderPackage)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <div class="fw-bold">
                                                {{ $orderPackage->package?->name ?? 'Paket Catering' }}
                                            </div>

                                            <div class="small text-muted mt-1">
                                                {{ $orderPackage->quantity }} paket ×
                                                Rp{{ number_format($orderPackage->price, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <div class="fw-bold text-end text-nowrap">
                                            Rp{{ number_format($orderPackage->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                        
                                    {{-- Isi paket --}}
                                    @if ($orderPackage->package?->details?->isNotEmpty())
                                        @php
                                            $actualPackageDetails = $order->details
                                                ->where('source', 'package')
                                                ->values();

                                            $originalPackageDetails = $orderPackage->package->details
                                                ->values();

                                            // Ambil detail komponen paket berdasarkan urutan.
                                            // Menu tambahan dengan source "custom" tidak ikut ditampilkan.
                                            $packageDetails = collect();
                                            $usedIndex = 0;

                                            foreach ($order->packages as $currentPackage) {
                                                $componentDetails = $currentPackage->package?->details
                                                    ?? collect();

                                                foreach ($componentDetails as $component) {
                                                    $actualDetail = $actualPackageDetails->get($usedIndex);

                                                    if ($currentPackage->id === $orderPackage->id && $actualDetail) {
                                                        $packageDetails->push([
                                                            'original' => $component->menu,
                                                            'actual' => $actualDetail->menu,
                                                            'quantity' => $actualDetail->quantity,
                                                        ]);
                                                    }

                                                    $usedIndex++;
                                                }

                                                if ($currentPackage->id === $orderPackage->id) {
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <div class="border-top mt-3 pt-3">
                                            <div class="fw-semibold small mb-2">Isi Paket</div>

                                            @forelse ($packageDetails as $item)
                                                @php
                                                    $originalMenu = $item['original'];
                                                    $actualMenu = $item['actual'];

                                                    $isReplaced = $originalMenu
                                                        && $actualMenu
                                                        && (int) $originalMenu->id !== (int) $actualMenu->id;
                                                @endphp

                                                <div class="py-2">
                                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                                        <span class="small fw-semibold">
                                                            {{ $actualMenu?->name ?? 'Menu tidak ditemukan' }}
                                                        </span>

                                                        <span class="small text-muted text-nowrap">
                                                            {{ $item['quantity'] }} porsi
                                                        </span>
                                                    </div>

                                                    @if ($isReplaced)
                                                        <div class="small text-success mt-1">
                                                            <i class="bi bi-arrow-repeat me-1"></i>
                                                            Menu bawaan {{ $originalMenu->name }}
                                                            diganti menjadi {{ $actualMenu->name }}.
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="text-muted small">
                                                    Belum ada detail komponen paket.
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        @endif

                        {{-- B. Menu tambahan atau menu custom mandiri --}}
                        @php
                            $displayDetails = $order->packages->isNotEmpty()
                                ? $order->details->where('source', 'custom')
                                : $order->details;
                        @endphp

                        @if ($displayDetails->isNotEmpty())
                            <h6 class="fw-bold mt-4 mb-3">
                                {{ $order->packages->isNotEmpty()
                                    ? 'Menu Tambahan'
                                    : 'Menu Custom' }}
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-success">
                                        <tr>
                                            <th width="60">No</th>
                                            <th>Menu</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($displayDetails as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td class="fw-semibold">
                                                    {{ $detail->menu?->name ?? 'Menu tidak ditemukan' }}
                                                </td>

                                                <td>
                                                    Rp{{ number_format($detail->price, 0, ',', '.') }}
                                                </td>

                                                <td>{{ $detail->quantity }}</td>

                                                <td class="text-end fw-semibold">
                                                    Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- C. Kondisi pesanan kosong --}}
                        @if ($order->packages->isEmpty() && $order->details->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada rincian item untuk pesanan ini.
                            </div>
                        @endif

                        {{-- Catatan --}}
                        @if ($order->notes)
                            <div class="mt-4">
                                <h6 class="fw-bold">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Catatan Pesanan
                                </h6>

                                <div class="alert alert-light border mb-0">
                                    {{ $order->notes }}
                                </div>
                            </div>
                        @endif


                        {{-- INFORMASI PENGIRIMAN --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-0 px-4 pt-4">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div>
                                        <h5 class="fw-bold mb-1">
                                            <i class="bi bi-truck me-2 text-primary"></i>
                                            Informasi Pengiriman
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            Atur metode pengiriman, jarak, dan ongkir pesanan.
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#shippingModal"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Atur Pengiriman
                                    </button>
                                </div>
                            </div>

                            <div class="card-body px-4 pb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="text-muted small">Metode</div>
                                        <div class="fw-semibold">
                                            {{ $order->delivery_method === 'delivery'
                                                ? 'Diantar Catering'
                                                : 'Ambil Sendiri' }}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-muted small">Jarak</div>
                                        <div class="fw-semibold">
                                            @if ($order->delivery_method === 'pickup')
                                                -
                                            @elseif ($order->delivery_distance !== null)
                                                {{ number_format($order->delivery_distance, 1, ',', '.') }} km
                                            @else
                                                Belum diatur
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-muted small">Ongkir</div>
                                        <div class="fw-semibold">
                                            @if ($order->delivery_method === 'pickup')
                                                -
                                            @elseif ($order->delivery_distance === null)
                                                Belum diatur
                                            @else
                                                Rp{{ number_format($order->delivery_fee, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if (
                                    $order->delivery_method === 'delivery'
                                    && $order->delivery_distance === null
                                )
                                    <div class="alert alert-warning mt-3 mb-0 small">
                                        Jarak dan ongkir belum dikonfigurasi. Atur pengiriman sebelum pesanan diselesaikan.
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- MODAL ATUR PENGIRIMAN --}}
                        <div
                                class="modal fade"
                                id="shippingModal"
                                tabindex="-1"
                                aria-labelledby="shippingModalLabel"
                                aria-hidden="true"
                            >
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4">
                                    <form
                                        action="{{ route('orders.shipping.update', $order->id) }}"
                                        method="POST"
                                        id="shippingForm"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <div class="modal-header border-0 px-4 pt-4">
                                            <div>
                                                <h5 class="modal-title fw-bold" id="shippingModalLabel">
                                                    Atur Pengiriman
                                                </h5>
                                                <p class="text-muted small mb-0">
                                                    Pesanan {{ $order->order_code }}
                                                </p>
                                            </div>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Tutup"
                                            ></button>
                                        </div>

                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label for="deliveryMethod" class="form-label fw-semibold">
                                                    Metode Pengiriman
                                                </label>

                                                <select
                                                    name="delivery_method"
                                                    id="deliveryMethod"
                                                    class="form-select"
                                                    required
                                                >
                                                    <option
                                                        value="pickup"
                                                        @selected(old('delivery_method', $order->delivery_method ?? 'pickup') === 'pickup')
                                                    >
                                                        Ambil Sendiri
                                                    </option>

                                                    <option
                                                        value="delivery"
                                                        @selected(old('delivery_method', $order->delivery_method ?? 'pickup') === 'delivery')
                                                    >
                                                        Diantar Catering
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="mb-3" id="distanceWrapper">
                                                <label for="deliveryDistance" class="form-label fw-semibold">
                                                    Jarak (km)
                                                </label>

                                                <input
                                                    type="number"
                                                    name="delivery_distance"
                                                    id="deliveryDistance"
                                                    class="form-control"
                                                    min="0"
                                                    step="0.1"
                                                    value="{{ old('delivery_distance', $order->delivery_distance) }}"
                                                >

                                                @error('delivery_distance')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3" id="feeWrapper">
                                                <label for="deliveryFee" class="form-label fw-semibold">
                                                    Ongkir (Rp)
                                                </label>

                                                <input
                                                    type="number"
                                                    name="delivery_fee"
                                                    id="deliveryFee"
                                                    class="form-control"
                                                    min="0"
                                                    step="1"
                                                    value="{{ old('delivery_fee', $order->delivery_fee ?? 0) }}"
                                                >

                                                <small class="text-muted" id="feeHint">
                                                    Ongkir gratis untuk jarak sampai 5 km.
                                                </small>

                                                @error('delivery_fee')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="modal-footer border-0 px-4 pb-4">
                                            <button
                                                type="button"
                                                class="btn btn-light"
                                                data-bs-dismiss="modal"
                                            >
                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check2-circle me-1"></i>
                                                Simpan Pengiriman
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        {{-- D. Ringkasan biaya --}}
                        @php
                            $subtotalPaket = $order->packages->sum('subtotal');

                            $subtotalMenu = $order->packages->isNotEmpty()
                                ? $order->details->where('source', 'custom')->sum('subtotal')
                                : $order->details->sum('subtotal');

                            $subtotalPesanan = $subtotalPaket + $subtotalMenu;
                        @endphp

                        <div class="order-total-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal paket</span>
                                <span class="fw-semibold">
                                    Rp{{ number_format($subtotalPaket, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    {{ $order->packages->isNotEmpty()
                                        ? 'Subtotal menu tambahan'
                                        : 'Subtotal menu custom' }}
                                </span>
                                <span class="fw-semibold">
                                    Rp{{ number_format($subtotalMenu, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya pengiriman</span>
                                <span class="fw-semibold">
                                    @if ($order->delivery_method === 'pickup')
                                        Gratis
                                    @elseif ($order->delivery_distance === null)
                                        Belum dikonfirmasi
                                    @else
                                        Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total Pesanan</span>
                                <span class="total-value">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- INFORMASI PEMBAYARAN --}}
                        @php
                            $payment = $order->payment;
                            $paidAmount = (float) ($payment?->amount ?? 0);
                            $orderTotal = (float) $order->total;
                            $remaining = max(0, $orderTotal - $paidAmount);

                            $paymentStatus = $payment?->status ?? 'Belum Dibayar';

                            $paymentBadge = match ($paymentStatus) {
                                'Dibayar' => 'bg-success',
                                'Sebagian' => 'bg-info text-dark',
                                'Ditolak' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp

                        <div class="payment-panel mt-4 mb-4">
                            <div class="payment-panel-header">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="payment-icon">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </div>

                                    <div>
                                        <h5 class="fw-bold mb-1">Informasi Pembayaran</h5>
                                        <p class="text-muted small mb-0">
                                            Ringkasan tagihan dan pembayaran pesanan.
                                        </p>
                                    </div>
                                </div>

                                <span class="badge {{ $paymentBadge }}">
                                    {{ $paymentStatus }}
                                </span>
                            </div>

                            <div class="payment-panel-body">
                                <div class="payment-total-box">
                                    <div>
                                        <span class="text-muted small">Total tagihan</span>
                                        <div class="payment-total-value">
                                            Rp{{ number_format($orderTotal, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <span class="text-muted small">Sisa pembayaran</span>
                                        <div class="payment-remaining-value {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp{{ number_format($remaining, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <div class="payment-info-item">
                                            <span class="payment-info-label">Sudah dibayar</span>
                                            <div class="payment-info-value text-success">
                                                Rp{{ number_format($paidAmount, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="payment-info-item">
                                            <span class="payment-info-label">Metode pembayaran</span>
                                            <div class="payment-info-value">
                                                {{ $payment?->method ? strtoupper($payment->method) : 'Belum dipilih' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="payment-info-item">
                                            <span class="payment-info-label">Bukti pembayaran</span>

                                            @if ($payment?->proof)
                                                <a href="{{ asset('storage/' . $payment->proof) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="payment-proof-link">
                                                    <i class="bi bi-eye me-1"></i> Lihat bukti
                                                </a>
                                            @else
                                                <div class="payment-info-value text-muted">
                                                    Belum ada bukti
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($payment?->notes)
                                    <div class="payment-notes mt-3">
                                        <span class="payment-info-label">Catatan pembayaran</span>
                                        <p class="mb-0 mt-1">{{ $payment->notes }}</p>
                                    </div>
                                @endif

                                <div class="payment-panel-footer">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle text-success mt-1"></i>
                                        <span class="small text-muted">
                                            Kelola nominal pembayaran, status, dan verifikasi melalui halaman pembayaran admin.
                                        </span>
                                    </div>

                                    @if ($payment)
                                        <a href="{{ route('admin.payments.show', $payment->id) }}"
                                        class="btn btn-success payment-action-btn">
                                            <i class="bi bi-sliders me-2"></i>
                                            Atur Pembayaran
                                            <i class="bi bi-arrow-up-right ms-2"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.payments.index') }}"
                                        class="btn btn-success payment-action-btn">
                                            <i class="bi bi-credit-card me-2"></i>
                                            Kelola Pembayaran
                                            <i class="bi bi-arrow-up-right ms-2"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 mb-4">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Kembali
                            </a>

                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>
                                Edit Pesanan
                            </a>

                            <form action="{{ route('orders.destroy', $order->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
 

    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const method = document.getElementById('deliveryMethod');
    const distance = document.getElementById('deliveryDistance');
    const fee = document.getElementById('deliveryFee');
    const distanceWrapper = document.getElementById('distanceWrapper');
    const feeWrapper = document.getElementById('feeWrapper');
    const feeHint = document.getElementById('feeHint');

    function updateShippingFields() {
        const isDelivery = method.value === 'delivery';

        distanceWrapper.classList.toggle('d-none', !isDelivery);
        feeWrapper.classList.toggle('d-none', !isDelivery);

        distance.required = isDelivery;
        fee.required = isDelivery && Number(distance.value || 0) > 5;

        if (!isDelivery) {
            distance.value = '';
            fee.value = '0';
            feeHint.textContent = 'Tidak ada ongkir untuk ambil sendiri.';
            return;
        }

        if (distance.value !== '' && Number(distance.value) <= 5) {
            fee.value = '0';
            feeHint.textContent = 'Gratis ongkir untuk jarak sampai 5 km.';
        } else {
            feeHint.textContent = 'Jarak di atas 5 km: tentukan ongkir yang sesuai.';
        }
    }

    method.addEventListener('change', updateShippingFields);
    distance.addEventListener('input', updateShippingFields);

    updateShippingFields();

    // Jika validasi Laravel gagal, buka lagi modal agar pesan error terlihat.
    @if ($errors->has('delivery_method') || $errors->has('delivery_distance') || $errors->has('delivery_fee'))
        const modal = new bootstrap.Modal(document.getElementById('shippingModal'));
        modal.show();
    @endif
});
</script>
    @endsection