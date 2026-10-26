@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
@php
    $selectedType = old('order_type', $order->order_type);

    if (!in_array($selectedType, ['paket', 'paket_custom', 'custom'], true)) {
        $selectedType = $order->packages->isNotEmpty()
            ? (
                $order->details->where('source', 'custom')->isNotEmpty()
                    ? 'paket_custom'
                    : 'paket'
            )
            : 'custom';
    }

    $initialPackages = old('packages', $order->packages->map(function ($row) {
        return [
            'package_id' => $row->package_id,
            'quantity' => $row->quantity,
            'price' => $row->price
        ];
    })->values()->all());

    // Pilihan pengganti yang dikirim kembali setelah validasi gagal.
    $initialReplacements = old('replacements', []);
    $initialItems = old('items', []);


    /*
    |--------------------------------------------------------------------------
    | Pemetaan menu pengganti yang tersimpan pada detail pesanan
    |--------------------------------------------------------------------------
    | Solusi sementara: mencocokkan komponen paket berdasarkan urutan.
    | Untuk pesanan dengan beberapa paket, pemetaan ini perlu diperkuat
    | menggunakan order_package_id dan original_menu_id.
    */

   $savedReplacements = [];

    $actualPackageDetails = $order->details
        ->where('source', 'package')
        ->values();

    $actualIndex = 0;

    foreach ($order->packages as $packageIndex => $orderPackage) {
        foreach ($orderPackage->package?->details ?? [] as $packageDetail) {
            $actualDetail = $actualPackageDetails->get($actualIndex);

            $originalMenuId = (int) $packageDetail->menus_id;

            if (
                $actualDetail &&
                (int) $actualDetail->menu_id !== $originalMenuId
            ) {
                $savedReplacements[$packageIndex][$originalMenuId] =
                    (int) $actualDetail->menu_id;
            }

            $actualIndex++;
        }
    }
    
    // Ambil menu yang sudah tersimpan pada pesanan.
    if (empty(old('items'))) {
        if ($order->packages->isNotEmpty()) {
            // Paket + Custom: tampilkan hanya menu tambahan.
            $existingItems = $order->details->where('source', 'custom');
        } else {
            // Custom saja: tampilkan seluruh detail menu pesanan.
            $existingItems = $order->details;
        }

        $initialItems = $existingItems->map(function ($row) {
            return [
                'menu_id' => $row->menu_id,
                'quantity' => $row->quantity,
                'price' => $row->price,
            ];
        })->values()->all();

    }

    $initialPackages = count($initialPackages)
        ? $initialPackages
        : [['package_id' => '', 'quantity' => 1]];

    $initialItems = count($initialItems)
        ? $initialItems
        : [['menu_id' => '', 'quantity' => 1]];

        // Data paket dan komponen paket untuk JavaScript.
        $packageData = $packages->map(function ($package) {
            return [
                'id' => $package->id,
                'details' => $package->details->map(function ($detail) {
                    $menu = $detail->menu;

                    return [
                        'id' => $detail->id,
                        'menu_id' => $detail->menus_id,
                        'menu_name' => $menu->name ?? 'Menu tidak ditemukan',
                        'category_id' => $menu->category_id ?? null,
                        'category_name' => $menu->category->name
                            ?? 'Tanpa kategori',
                    ];
                })->values()->all(),
            ];
        })->values()->all();

    // Data menu beserta kategori untuk dropdown Edit Pesanan.
    $availableMenus = $menus->map(function ($menu) {
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'price' => (float) $menu->price,
            'stock' => $menu->stock,
            'category_id' => $menu->category_id,
            'category_name' => $menu->category->name ?? 'Tanpa kategori',
        ];
    })->values()->all();

@endphp

<div class="container-fluid py-4 order-page order-edit-page">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill text-bg-success">ADMIN</span>
                <span class="text-muted small">Manajemen pesanan</span>
            </div>

            <h3 class="fw-bold mb-1">Edit Pesanan</h3>

            <p class="text-muted mb-0">
                Perbarui pesanan
                <strong>#{{ $order->order_code ?? $order->id }}</strong>
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    {{-- PESAN ERROR / SUKSES --}}
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-bold mb-2">Periksa kembali data berikut:</div>
            <ul class="mb-0">
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
        enctype="multipart/form-data"
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
                    Informasi utama dan status pesanan pelanggan.
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
                        <label class="form-label fw-semibold">Tanggal Pengiriman</label>

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
                        <label class="form-label fw-semibold">Jenis Pesanan</label>

                        <input
                            type="hidden"
                            name="order_type"
                            id="orderType"
                            value="{{ $selectedType }}"
                        >

                    <div class="row g-3">
                        @foreach ([
                            [
                                'value' => 'paket',
                                'title' => 'Paket Catering',
                                'desc' => 'Pilih paket catering yang sudah tersedia.',
                                'icon' => 'bi-box-seam'
                            ],
                            [
                                'value' => 'paket_custom',
                                'title' => 'Paket + Custom',
                                'desc' => 'Pilih paket dan tambahkan menu sesuai kebutuhan.',
                                'icon' => 'bi-basket2'
                            ],
                            [
                                'value' => 'custom',
                                'title' => 'Custom Saja',
                                'desc' => 'Susun pesanan dari menu yang tersedia.',
                                'icon' => 'bi-list-check'
                            ],
                        ] as $type)
                            <div class="col-md-4">
                                <label class="order-type-card w-100 h-100
                                    {{ $selectedType === $type['value'] ? 'active' : '' }}">

                                    <input
                                        type="radio"
                                        name="order_type_choice"
                                        class="order-type-choice"
                                        value="{{ $type['value'] }}"
                                        @checked($selectedType === $type['value'])
                                    >

                                    <div class="type-icon">
                                        <i class="bi {{ $type['icon'] }}"></i>
                                    </div>

                                    <div class="type-content">
                                        <div class="type-title">{{ $type['title'] }}</div>

                                        <p class="type-description">{{ $type['desc'] }}</p>

                                        <span class="type-status">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span class="type-status-text">
                                                {{ $selectedType === $type['value'] ? 'Jenis dipilih' : 'Pilih jenis ini' }}
                                            </span>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan Pesanan</label>

                        <textarea
                            name="notes"
                            rows="3"
                            class="form-control"
                            placeholder="Catatan khusus untuk pesanan ini..."
                        >{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- PILIHAN PAKET --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" id="packageSection">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-box-seam text-success me-2"></i>
                            Paket Catering
                        </h5>
                        <p class="text-muted small mb-0">
                            Pilih paket dan tentukan jumlahnya.
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
                <div class="text-muted small mt-2" id="noPackageMessage" hidden>
                    Belum ada paket yang ditambahkan.
                </div>
            </div>
        </div>

        {{-- PILIHAN MENU CUSTOM --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" id="customSection">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-basket text-success me-2"></i>
                            Menu Custom / Tambahan
                        </h5>
                        <p class="text-muted small mb-0">
                            Harga menu dan subtotal akan dihitung ulang dari database.
                        </p>
                    </div>

                    <button type="button" class="btn btn-outline-success btn-sm" id="addMenu">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Menu
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <div id="menuRows" class="d-flex flex-column gap-3"></div>
                <div class="text-muted small mt-2" id="noMenuMessage" hidden>
                    Belum ada menu custom yang ditambahkan.
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

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal Menu Custom</span>
                    <strong id="menuSubtotal">Rp0</strong>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Biaya Pengiriman</span>
                    <strong id="shippingSubtotal">Belum ditentukan</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">Subtotal Pesanan</span>
                    <strong class="text-success fs-4" id="grandTotal">Rp0</strong>
                </div>

                <div class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Total di atas belum termasuk ongkos kirim.
                    Metode pengiriman dan biaya final diatur pada Detail Pesanan.
                </div>
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="d-flex flex-wrap justify-content-end gap-2 mb-4">
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

{{-- TEMPLATE BARIS PAKET --}}
<template id="packageTemplate">
    <div class="line-item package-row">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Paket Catering</label>
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

            <div class="col-md-2">
                <label class="form-label fw-semibold">Jumlah</label>
                <input
                    type="number"
                    class="form-control package-quantity"
                    min="1"
                    value="1"
                    required
                >
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Harga per Paket (Rp)</label>
                <input
                    type="number"
                    class="form-control package-price"
                    min="0"
                    step="1"
                    value="0"
                    required
                >

            </div>

            <div class="col-md-2 ">
                <button type="button" class="btn btn-outline-danger w-100 remove-line">
                    <i class="bi bi-trash"></i>
                    Hapus
                </button>
            </div>

            {{-- GANTI MENU KOMPONEN PAKET --}}
            <div class="col-12 replacement-section">
                <div class="border rounded-3 p-3 bg-light">
                    <h6 class="fw-bold mb-1">Ganti Isi Paket</h6>
                    <p class="text-muted small mb-3">
                        Pilih menu pengganti untuk komponen paket ini.
                        Kosongkan pilihan untuk memakai menu bawaan.
                    </p>

                    <div class="replacement-rows">
                        <div class="text-muted small">
                            Pilih paket terlebih dahulu.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="alert alert-light border mb-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Informasi harga:</strong>
                    harga awal diambil dari harga paket yang terdaftar di katalog.
                    Jika ada penyesuaian harga untuk pesanan tertentu,
                    admin dapat mengubahnya sebelum menyimpan.
                    Subtotal dihitung dari harga paket × jumlah paket.
                </div>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Subtotal paket</span>
                    <strong class="line-subtotal">Rp0</strong>
                </div>
            </div>
            
        </div>
        
    </div>
</template>


{{-- TEMPLATE BARIS MENU --}}
<template id="menuTemplate">
    <div class="line-item menu-row">
        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label fw-semibold">Kategori</label>
                <select class="form-select menu-category-select" required>
                    <option value="">-- Pilih Kategori --</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Menu</label>
                <select class="form-select menu-select" required disabled>
                    <option value="">-- Pilih Kategori Dahulu --</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Jumlah</label>
                <input
                    type="number"
                    class="form-control menu-quantity"
                    min="1"
                    value="1"
                    required
                >                
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Harga/Menu (Rp)</label>
                <input
                    type="number"
                    class="form-control menu-price"
                    min="0"
                    step="1"
                    value="0"
                    required
                >
            </div>

            <div class="col-md-2">
                <button
                    type="button"
                    class="btn btn-outline-danger w-100 remove-line"
                >
                    <i class="bi bi-trash"></i>
                    Hapus
                </button>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Subtotal menu</span>
                    <strong class="line-subtotal">Rp0</strong>
                </div>
            </div>

        </div>
    </div>
</template>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const packageRows = document.getElementById('packageRows');
    const menuRows = document.getElementById('menuRows');

    const packageTemplate = document.getElementById('packageTemplate');
    const menuTemplate = document.getElementById('menuTemplate');

    const packageData = @json($packageData);
    const availableMenus = @json($availableMenus);

    const currentReplacements = @json($savedReplacements ?? []);
    const initialReplacements = @json($initialReplacements ?? []);

    const orderTypeInput = document.getElementById('orderType');
    const packageSection = document.getElementById('packageSection');
    const customSection = document.getElementById('customSection');

    let packageIndex = 0;
    let menuIndex = 0;

    function clearPackagesRows(){
        packageRows.innerHTML = '';
        updateEmptyMessages();
        calculateTotal();
    }

    function clearMenuRows(){
        menuRows.innerHTML = '';
        updateEmptyMessages();
        calculateTotal();
    }

    const money = value => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(value || 0);



    function updateTypeSections() {
        const type = orderTypeInput.value;

        document.querySelectorAll('.order-type-choice').forEach(radio => {
            const active = radio.value === type;
            radio.checked = active;

            const card = radio.closest('.order-type-card');
            if (card) {
                card.classList.toggle('active', active);

                const statusText = card.querySelector('.type-status-text');

                if (statusText) {
                    statusText.textContent = active
                        ? 'Jenis dipilih'
                        : 'Pilih jenis ini';
                }
            }
        });

        packageSection.hidden = type === 'custom';
        customSection.hidden = type === 'paket';

        packageRows.querySelectorAll('select, input').forEach(input => {
            input.disabled = type === 'custom';
        });

        menuRows.querySelectorAll('select, input').forEach(input => {
            input.disabled = type === 'paket';
        });

        packageRows.querySelectorAll('.package-row').forEach(row => {
            renderReplacements(row, row.querySelector('.package-select').value,
                    row.dataset.packageIndex);

                row.querySelectorAll('.replacement-select').forEach(select => {
                    select.disabled = type !== 'paket_custom';
                });
        })


        updateEmptyMessages();
        calculateTotal();
    }

    function changeOrderType(newType) {
        if (!['paket', 'paket_custom', 'custom'].includes(newType)) {
            return;
        }

        orderTypeInput.value = newType;

        // Custom saja tidak boleh menyimpan pilihan paket.
        if (newType === 'custom') {
            clearPackagesRows();
        }

        // Paket saja tidak boleh menyimpan menu tambahan.
        if (newType === 'paket') {
            clearMenuRows();
        }

        updateTypeSections();
    }

    document.querySelectorAll('.order-type-choice').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                changeOrderType(this.value);
            }
        });
    });

    function renderReplacements(row, packageId, packageIndex) {
        const container = row.querySelector('.replacement-rows');

        const packageInfo = packageData.find(
            item => String(item.id) === String(packageId)
        );

        container.innerHTML = '';

        if (orderTypeInput.value !== 'paket_custom') {
            row.querySelector('.replacement-section').hidden = true;
            return;
        }

        row.querySelector('.replacement-section').hidden = false;

        if (!packageInfo || !packageInfo.details.length) {
            container.innerHTML =
                '<div class="text-muted small">' +
                'Paket ini belum memiliki komponen menu.' +
                '</div>';
            return;
        }

        const currentPackageReplacements =
            currentReplacements[packageIndex] ?? {};

        const submittedPackageReplacements =
            initialReplacements[packageIndex] ?? {};

        packageInfo.details.forEach(detail => {
            const originalMenuId = String(detail.menu_id);

            const currentReplacementId =
                currentPackageReplacements[originalMenuId] ?? null;

            const submittedReplacementId =
                submittedPackageReplacements[originalMenuId];

            // Jika validasi sebelumnya gagal, gunakan pilihan lama dari form.
            // Jika tidak, tampilkan menu yang benar-benar tersimpan.
            const selectedMenuId =
                submittedReplacementId !== undefined
                    ? String(submittedReplacementId)
                    : String(currentReplacementId ?? originalMenuId);

            const currentMenu = availableMenus.find(
                menu => String(menu.id) === selectedMenuId
            );

            const currentMenuName = currentMenu
                ? currentMenu.name
                : detail.menu_name;

            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 align-items-center mb-3';

            const labelCol = document.createElement('div');
            labelCol.className = 'col-md-5';

            const label = document.createElement('label');
            label.className = 'form-label mb-0 fw-semibold';
            label.textContent =
                `${detail.category_name}: ${detail.menu_name}`;

            labelCol.appendChild(label);

            if (selectedMenuId !== originalMenuId) {
                const note = document.createElement('small');
                note.className = 'text-success d-block mt-1';

                note.innerHTML =
                    '<i class="bi bi-arrow-repeat me-1"></i>' +
                    `Menu bawaan: ${detail.menu_name}. ` +
                    `Diganti menjadi: ${currentMenuName}.`;

                labelCol.appendChild(note);
            }

            const selectCol = document.createElement('div');
            selectCol.className = 'col-md-7';

            const select = document.createElement('select');
            select.className = 'form-select replacement-select';

            // Format dibuat sama dengan Create: replacements[index][menu_id].
            select.name =
                `replacements[${packageIndex}][${originalMenuId}]`;

            // Opsi menu bawaan sekaligus cara membatalkan penggantian.
            const originalOption = document.createElement('option');
            originalOption.value = originalMenuId;
            originalOption.textContent =
                `Gunakan menu bawaan (${detail.menu_name})`;

            select.appendChild(originalOption);

            availableMenus.forEach(menu => {
                // Menu pengganti harus berasal dari kategori yang sama.
                if (
                    String(menu.id) === originalMenuId ||
                    String(menu.category_id) !== String(detail.category_id)
                ) {
                    return;
                }

                const option = document.createElement('option');
                option.value = String(menu.id);
                option.textContent =
                    `${menu.name} · Rp${Number(menu.price).toLocaleString('id-ID')}`;

                select.appendChild(option);
            });

            select.value = selectedMenuId;

            selectCol.appendChild(select);
            wrapper.appendChild(labelCol);
            wrapper.appendChild(selectCol);
            container.appendChild(wrapper);
        });
    }

function addPackageRow(selectedId = '', quantity = 1, savedPrice = null) {
    const fragment = packageTemplate.content.cloneNode(true);
    const row = fragment.querySelector('.package-row');

    const select = row.querySelector('.package-select');
    const qty = row.querySelector('.package-quantity');
    const price = row.querySelector('.package-price');

    select.value = selectedId;
    qty.value = quantity;

    const catalogPrice = Number(
        select.selectedOptions[0]?.dataset.price || 0
    );

    // Harga lama dipertahankan saat mengedit.
    // Paket baru menggunakan harga katalog sebagai nilai awal.    
    price.value = savedPrice !== null && savedPrice !== ''
        ? Math.round(Number(savedPrice))
        : catalogPrice;

    const currentPackageIndex = packageIndex;

    row.dataset.packageIndex = String(currentPackageIndex);

    select.name = `packages[${currentPackageIndex}][package_id]`;
    qty.name = `packages[${currentPackageIndex}][quantity]`;
    price.name = `packages[${currentPackageIndex}][price]`;

    packageIndex++;

    packageRows.appendChild(fragment);
    renderReplacements(row, select.value,row.dataset.packageIndex);
    updateEmptyMessages();
    calculateTotal();
}

packageRows.addEventListener('change', function (event) {
    if (event.target.matches('.package-select')) {
        const row = event.target.closest('.package-row');
        const priceInput = row.querySelector('.package-price');

        // Saat paket diganti, gunakan harga katalog terbaru
        // sebagai nilai awal yang masih bisa disesuaikan admin.
        priceInput.value = Math.round(
            Number(event.target.selectedOptions[0]?.dataset.price || 0)
        );

        renderReplacements(row, event.target.value,row.dataset.packageIndex);
    }

    calculateTotal();
});

    function addMenuRow(selectedId = '', quantity = 1, savedPrice = null) {
        const fragment = menuTemplate.content.cloneNode(true);
        const row = fragment.querySelector('.menu-row');

        const categorySelect = row.querySelector('.menu-category-select');
        const select = row.querySelector('.menu-select');
        const qty = row.querySelector('.menu-quantity');
        const price = row.querySelector('.menu-price');        

        const index = menuIndex++;

        select.name = `items[${index}][menu_id]`;
        qty.name = `items[${index}][quantity]`;
        price.name = `items[${index}][price]`;

        qty.value = quantity;

        // Cari kategori dari menu yang sudah tersimpan.
        const selectedMenu = availableMenus.find(
            menu => String(menu.id) === String(selectedId)
        );

        // Buat daftar kategori unik.
        const categories = new Map();

        availableMenus.forEach(menu => {
            const id = menu.category_id;
            const name = menu.category_name || 'Tanpa kategori';

            if (id !== null && id !== undefined) {
                categories.set(String(id), name);
            }
        });

        [...categories.entries()]
            .sort((a, b) => a[1].localeCompare(b[1], 'id'))
            .forEach(([id, name]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                categorySelect.appendChild(option);
            });

        // Isi menu berdasarkan kategori yang dipilih.
        function fillMenuOptions(categoryId, menuId = '') {
            select.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = categoryId
                ? '-- Pilih Menu --'
                : '-- Pilih Kategori Dahulu --';

            select.appendChild(placeholder);
            select.disabled = !categoryId;

            if (!categoryId) {
                return;
            }

            availableMenus
                .filter(menu =>
                    String(menu.category_id) === String(categoryId)
                )
                .sort((a, b) => a.name.localeCompare(b.name, 'id'))
                .forEach(menu => {
                    const option = document.createElement('option');

                    option.value = String(menu.id);
                    option.dataset.price = menu.price;
                    option.dataset.stock = menu.stock ?? 0;
                    option.textContent =
                        `${menu.name} · Rp${Math.round(Number(menu.price || 0)).toLocaleString('id-ID')} · Stok ${menu.stock ?? 0}`;

                    select.appendChild(option);
                });

            select.value = String(menuId || '');
        }

        // Saat Edit dibuka, pulihkan kategori dan menu yang tersimpan.
        if (selectedMenu) {
            categorySelect.value = String(selectedMenu.category_id ?? '');
            fillMenuOptions(categorySelect.value, selectedId);
        } else {
            fillMenuOptions('');
        }

        const catalogPrice = Math.round(
            Number(selectedMenu?.price ?? 0)
        );

        // Pertahankan harga pesanan lama jika tersedia.
        price.value = savedPrice !== null && savedPrice !== ''
            ? Math.round(Number(savedPrice))
            : catalogPrice;

        // Ganti kategori berarti menu harus dipilih ulang.
        categorySelect.addEventListener('change', function () {
            fillMenuOptions(this.value);

            price.value = 0;            
            calculateTotal();
        });

        // Saat menu dipilih, ambil harga dari menu tersebut.
        select.addEventListener('change', function () {
            const option = this.selectedOptions[0];

            price.value = Math.round(
                Number(option?.dataset.price || 0)
            );
    
            calculateTotal();
        });

        price.addEventListener('input', calculateTotal);
        qty.addEventListener('input', calculateTotal);

        menuRows.appendChild(fragment);
        
        updateEmptyMessages();
        calculateTotal();
    }

    function updateEmptyMessages() {
        document.getElementById('noPackageMessage').hidden =
            packageRows.querySelectorAll('.package-row').length > 0;

        document.getElementById('noMenuMessage').hidden =
            menuRows.querySelectorAll('.menu-row').length > 0;
    }

    function calculateTotal() {
        let subtotalPackages = 0;
        let subtotalMenus = 0;

        packageRows.querySelectorAll('.package-row').forEach(row => {
            const qty = Number(
                row.querySelector('.package-quantity').value || 0
            );

            const price = Number(
                row.querySelector('.package-price').value || 0
            );

            const subtotal = price * qty;

            row.querySelector('.line-subtotal').textContent = money(subtotal);
            subtotalPackages += subtotal;
        });

        menuRows.querySelectorAll('.menu-row').forEach(row => {
            const select = row.querySelector('.menu-select');
            const qtyInput = row.querySelector('.menu-quantity');
            const priceInput = row.querySelector('.menu-price');

            const price = Math.max(0, Number(priceInput.value || 0));
            const stock = Number(select.selectedOptions[0]?.dataset.stock || 0);
            const qty = Number(qtyInput.value || 0);
            const subtotal = price * qty;

            row.querySelector('.line-subtotal').textContent = money(subtotal);            

            subtotalMenus += subtotal;
        });

        // Ongkir tersimpan di database dan tidak diedit pada halaman ini.
        const shippingFee = @json(
            $order->delivery_fee !== null
                ? (float) $order->delivery_fee
                : null
        );

        document.getElementById('packageSubtotal').textContent =
            money(subtotalPackages);

        document.getElementById('menuSubtotal').textContent =
            money(subtotalMenus);

        document.getElementById('shippingSubtotal').textContent =
            shippingFee === null
                ? 'Belum ditentukan'
                : money(shippingFee);

        document.getElementById('grandTotal').textContent =
            money(
                subtotalPackages +
                subtotalMenus +
                (shippingFee ?? 0)
            );

    }

    packageRows.addEventListener('change', calculateTotal);
    packageRows.addEventListener('input', calculateTotal);
    menuRows.addEventListener('change', calculateTotal);
    menuRows.addEventListener('input', calculateTotal);

    document.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-line');

        if (!removeButton) return;

        const row = removeButton.closest('.line-item');

        if (row.classList.contains('package-row')) {
            row.remove();
        } else if (row.classList.contains('menu-row')) {
            row.remove();
        }

        updateEmptyMessages();
        calculateTotal();
    });

    document.getElementById('addPackage').addEventListener('click', () => {
        addPackageRow();
    });

    document.getElementById('addMenu').addEventListener('click', () => {
        addMenuRow();
    });


    // Isi baris awal dari data pesanan atau input lama saat validasi gagal.
    const initialPackages = @json(array_values($initialPackages));
    const initialItems = @json(array_values($initialItems));

    initialPackages.forEach(item => {
        addPackageRow(
            item.package_id ?? '',
            item.quantity ?? 1,
            item.price ?? null
        );
    });

    initialItems.forEach(item => {
        addMenuRow(item.menu_id ?? '', item.quantity ?? 1, item.price ?? null);
    });

    // Bersihkan baris yang tidak sesuai dengan jenis pesanan awal.
    if (orderTypeInput.value === 'custom') {
        clearPackagesRows();
    }

    if (orderTypeInput.value === 'paket') {
        clearMenuRows();
    }

    updateEmptyMessages();
    updateTypeSections();

    document.getElementById('editOrderForm').addEventListener('submit', function (event) {
        const type = orderTypeInput.value;
        const packageCount = packageRows.querySelectorAll('.package-row').length;
        const menuCount = menuRows.querySelectorAll('.menu-row').length;

        if (type === 'paket' && (packageCount === 0 || menuCount > 0)) {
            event.preventDefault();
            alert('Pesanan paket harus memiliki paket dan tidak boleh memiliki menu custom.');
            return;
        }

        if (type === 'paket_custom' && (packageCount === 0 || menuCount === 0)) {
            event.preventDefault();
            alert('Pesanan paket + custom harus memiliki paket dan menu custom.');
            return;
        }

        if (type === 'custom' && (packageCount > 0 || menuCount === 0)) {
            event.preventDefault();
            alert('Pesanan custom harus memiliki menu tanpa paket.');
        }
    });
});
</script>
@endpush