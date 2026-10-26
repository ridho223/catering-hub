
    @extends('layouts.admin')

    @section('title', 'Tambah Pesanan')

    @section('content')
    <div class="container-fluid py-4 order-page">


        {{-- PAGE HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill text-bg-success">ADMIN</span>
                    <span class="text-muted small">Manajemen pesanan</span>
                </div>
                <h3 class="page-heading mb-1">Buat Pesanan Baru</h3>
                <p class="page-subtitle mb-0">
                    Catat pesanan catering, atur pengiriman, dan kelola pembayaran.
                </p>
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- ALERT --}}
        @if(session('error'))
            <div class="alert alert-danger rounded-3 border-0">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <strong>Data belum valid.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="orderForm">
            @csrf

            <div class="row g-4">
                {{-- KOLOM UTAMA --}}
                <div class="col-12">

                    {{-- INFORMASI PESANAN --}}
                    <section class="order-card">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                            <div>
                                <h5 class="section-title">Informasi Pesanan</h5>
                                <p class="section-description">Pelanggan, jenis pesanan, dan jadwal acara.</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Pelanggan <span class="text-danger">*</span></label>

                                    <div class="customer-select-wrap">
                                        <div class="customer-select-icon">
                                            <i class="bi bi-person"></i>
                                        </div>

                                        <select name="customer_id" id="customerSelect" class="form-select customer-select" required>
                                            <option value="">Cari dan pilih pelanggan</option>

                                            @foreach ($customers as $customer)
                                                <option value="{{$customer->id}}" data-name="{{$customer->name}}"
                                                    data-phone="{{$customer->phone ?? ''}}" data-address="{{$customer->address ?? ''}}" 
                                                    @selected(old('customer_id') == $customer->id)>
                                                    {{$customer->name}} 
                                                    @if ($customer->phone) . {{$customer->phone}}                                               
                                                    @endif
                                                </option>                                            
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="field-hint">
                                        Pilih pelanggan yang sudah terdaftar di sistem.
                                    </div>

                                    <div id="customerPreview" class="customer-preview mt-3" hidden>
                                        <div class="customer-avatar">
                                            <i class="bi bi-person-fill"></i>
                                        </div>

                                        <div class="customer-preview-content">
                                            <div class="small text-muted">Pesanan untuk</div>
                                            <div class="fw-bold" id="customerPreviewName">-</div>

                                            <div class="customer-meta">
                                                <span>
                                                    <i class="bi bi-telephone me-1"></i>
                                                    <span id="customerPreviewPhone">Belum ada nomor telepon</span>
                                                </span>

                                                <span>
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    <span id="customerPreviewAddress">Alamat belum tersedia</span>
                                                </span>
                                            </div>
                                        </div>

                                        <span class="customer-selected-badge">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Dipiih
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-end gap-2 mb-3">
                                        <div>
                                            <label class="form-label mb-1">
                                                Jenis Pesanan <span class="text-danger">*</span>
                                            </label>
                                            <div class="field-hint mt-0">
                                                Pilih jenis pesanan sesuai kebutuhan pelanggan.
                                            </div>
                                        </div>
                                        <span class="step-label">LANGKAH 1</span>
                                    </div>

                                    <input type="hidden" name="order_type" id="orderType"
                                        value="{{ old('order_type', 'paket') }}">

                                    <div class="row g-3">
                                        @php
                                            $orderTypes = [
                                                [
                                                    'value' => 'paket',
                                                    'icon' => 'bi-box-seam',
                                                    'title' => 'Paket Catering',
                                                    'desc' => 'Pesan paket dengan harga yang sudah ditentukan.',
                                                    'tag' => 'Praktis',
                                                ],
                                                [
                                                    'value' => 'paket_custom',
                                                    'icon' => 'bi-basket2',
                                                    'title' => 'Paket + Custom',
                                                    'desc' => 'Paket catering ditambah menu pilihan lainnya.',
                                                    'tag' => 'Kombinasi',
                                                ],
                                                [
                                                    'value' => 'custom',
                                                    'icon' => 'bi-ui-checks-grid',
                                                    'title' => 'Menu Custom',
                                                    'desc' => 'Susun pesanan dari menu satu per satu.',
                                                    'tag' => 'Fleksibel',
                                                ],
                                            ];

                                            $selectedType = old('order_type', 'paket');
                                        @endphp

                                        @foreach($orderTypes as $type)
                                            <div class="col-md-4">
                                                <label class="order-type-card w-100
                                                    {{ $selectedType === $type['value'] ? 'active' : '' }}">

                                                    <input type="radio"
                                                        name="order_type-choice"
                                                        class="order-type-choice"
                                                        value="{{ $type['value'] }}" aria-label="{{ $type['title']}}"
                                                        {{ $selectedType === $type['value'] ? 'checked' : '' }}>

                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="order-type-icon">
                                                            <i class="bi {{ $type['icon'] }}"></i>
                                                        </div>

                                                        <span class="order-type-tag">{{ $type['tag'] }}</span>
                                                    </div>

                                                    <div class="order-type-title">{{ $type['title'] }}</div>

                                                    <p class=" order-text-description mb-0">
                                                        {{ $type['desc'] }}
                                                    </p>

                                                    <div class="order-type-footer">
                                                        <span class="type-radio-indicator">
                                                            <i class="bi bi-check2"></i>
                                                        </span>
                                                        <span class="small type-choice-text">
                                                            {{ $selectedType === $type['value'] ? 'Jenis dipilih' : ' Pilih jenis ini' }}
                                                        </span>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Pesanan <span class="text-danger">*</span></label>
                                    <input type="date" name="order_date" class="form-control"
                                        value="{{ old('order_date', date('Y-m-d')) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                                    <input type="date" name="delivery_date" class="form-control"
                                        min="{{ date('Y-m-d') }}"
                                        value="{{ old('delivery_date') }}" required>
                                    <div class="field-hint">Tanggal pesanan siap diambil atau dikirim.</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- PAKET --}}
                    <section class="order-card" id="packageSection">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h5 class="section-title">Paket Catering</h5>
                                <p class="section-description">Pilih paket dan jumlah yang dipesan.</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div class="table-responsive">
                                <table class="table order-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width:220px">Nama Paket</th>
                                            <th style="min-width:100px">Jumlah</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="packageItems"></tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-outline-success btn-sm rounded-3"
                                    id="addPackage">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Paket
                            </button>

                            @if($packages->isEmpty())
                                <div class="alert alert-warning small mt-3 mb-0">
                                    Belum ada paket tersedia dengan menu penyusun yang memiliki stok.
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- MENU TAMBAHAN --}}
                    <section class="order-card" id="menuSection">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-basket2"></i>
                            </div>

                            <div>
                                <h5 class="section-title">Menu Tambahan / Custom</h5>
                                <p class="section-description">
                                    Pilih kategori terlebih dahulu, kemudian tentukan menu dan jumlahnya.
                                </p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div id="menuItems" class="d-flex flex-column gap-3"></div>

                            <div id="noMenuMessage" class="text-muted small mt-2" hidden>
                                Belum ada menu yang ditambahkan.
                            </div>

                            <button type="button"
                                    class="btn btn-outline-success btn-sm rounded-3 mt-3"
                                    id="addMenu">
                                <i class="bi bi-plus-lg me-1"></i>
                                Tambah Menu
                            </button>
                        </div>
                    </section>

                    {{-- CATATAN --}}
                    <section class="order-card">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-card-text"></i>
                            </div>
                            <div>
                                <h5 class="section-title">Catatan Pesanan</h5>
                                <p class="section-description">Informasi tambahan untuk pesanan ini.</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Contoh: pesanan untuk acara keluarga, sambal dipisah, dan lainnya">{{ old('notes') }}</textarea>
                        </div>
                    </section>
                </div>
            </div>
            
            {{-- RINGKASAN PESANAN --}}
            <section class="order-card mt-4">
                <div class="section-heading">
                    <div class="section-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h5 class="section-title">Ringkasan Pesanan</h5>
                        <p class="section-description">
                            Periksa kembali jumlah dan total pesanan sebelum menyimpan.
                        </p>
                    </div>
                </div>

                <div class="section-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="summary-line">
                                <span>Subtotal Paket</span>
                                <strong id="packageTotal">Rp0</strong>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="summary-line">
                                <span>Subtotal Menu Tambahan</span>
                                <strong id="menuTotal">Rp0</strong>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="summary-line">
                                <span>Ongkos Kirim</span>
                                <strong id="shippingTotal">Belum ditentukan</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="text-muted small">Subtotal Pesanan</div>
                            <strong class="text-success fs-3" id="grandTotal">Rp0</strong>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('orders.index') }}"
                            class="btn btn-light border rounded-3 px-4">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check2-circle me-2"></i>
                                Simpan Pesanan
                            </button>
                        </div>
                    </div>

                    <div class="text-muted small mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Total di atas belum termasuk ongkos kirim.
                        Metode pengiriman dan biaya final diatur pada Detail Pesanan.
                    </div>
                </div>
            </section>

        </form>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const packagesData = @json($packages->values());
            const menusData = @json($menus->values());

            let packageIndex = 0;
            let menuIndex = 0;

            const $ = id => document.getElementById(id);

            const rupiah = value =>
                'Rp' + Number(value || 0).toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });

            const customerSelect = $('customerSelect');

            // Informasi pelanggan
            function updateCustomerPreview() {
                const option = customerSelect.options[customerSelect.selectedIndex];
                const hasCustomer = Boolean(customerSelect.value);

                $('customerPreview').hidden = !hasCustomer;

                if (!hasCustomer) return;

                $('customerPreviewName').textContent =
                    option.dataset.name || '-';

                $('customerPreviewPhone').textContent =
                    option.dataset.phone || 'Belum ada nomor telepon';

                $('customerPreviewAddress').textContent =
                    option.dataset.address || 'Alamat belum tersedia';
            }

            customerSelect.addEventListener('change', updateCustomerPreview);
            updateCustomerPreview();

            // Pilihan paket dan menu
            function fillOptions(select, items, type) {
                const first = document.createElement('option');
                first.value = '';
                first.textContent =
                    type === 'package' ? 'Pilih paket catering' : 'Pilih menu';
                select.appendChild(first);

                if (type === 'package') {
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.dataset.price = item.price;
                        option.textContent =
                            `${item.name} - ${rupiah(item.price)}`;
                        select.appendChild(option);
                    });

                    return;
                }

                const groups = new Map();

                items.forEach(menu => {
                    const categoryName = menu.category?.name || 'Tanpa Kategori';

                    if (!groups.has(categoryName)) {
                        const group = document.createElement('optgroup');
                        group.label = categoryName;
                        groups.set(categoryName, group);
                        select.appendChild(group);
                    }

                    const option = document.createElement('option');
                    option.value = menu.id;
                    option.dataset.price = menu.price;
                    option.dataset.stock = menu.stock;
                    option.textContent =
                        `${menu.name} - ${rupiah(menu.price)} (stok ${menu.stock})`;

                    groups.get(categoryName).appendChild(option);
                });
            }
        
            function fillMenuOptions(select, categoryId) {
                select.replaceChildren();

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = categoryId
                    ? 'Pilih menu'
                    : 'Pilih kategori terlebih dahulu';

                select.appendChild(placeholder);
                select.disabled = !categoryId;

                if (!categoryId) return;

                const filteredMenus = menusData.filter(menu => {
                    const menuCategoryId = menu.category?.id ?? menu.category_id;
                    return String(menuCategoryId) === String(categoryId);
                });

                filteredMenus.forEach(menu => {
                    const option = document.createElement('option');
                    option.value = menu.id;
                    option.dataset.price = menu.price;
                    option.dataset.stock = menu.stock;
                    option.textContent =
                        `${menu.name} - ${rupiah(menu.price)} (stok ${menu.stock})`;

                    select.appendChild(option);
                });
            }

            function fillCategoryOptions(select) {
                const categories = new Map();

                menusData.forEach(menu => {
                    const category = menu.category;

                    if (category?.id != null) {
                        categories.set(String(category.id), category.name);
                    }
                });

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Pilih kategori';
                select.appendChild(placeholder);

                [...categories.entries()]
                    .sort((a, b) => a[1].localeCompare(b[1], 'id'))
                    .forEach(([id, name]) => {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = name;
                        select.appendChild(option);
                    });
            }

            function updatePackagePriceMode() {
                const isCustomPackage = $('orderType').value === 'paket_custom';

                document.querySelectorAll('.package-row').forEach(row => {
                    const priceInput = row.querySelector('.package-price-input');

                    if (!priceInput) return;

                    priceInput.readOnly = !isCustomPackage;
                    priceInput.classList.toggle('bg-light', !isCustomPackage);
                });
            }

            function indexForPackageRow(row) {
                const select = row.querySelector('.package-select');
                const match = select?.name.match(/^packages\[(\d+)\]/);

                return match ? match[1] : '0';
            }

            // Penggantian menu bawaan paket
            function renderPackageReplacements(row, packageId) {
                let replacementRow = row._replacementRow;

                if (!replacementRow) {
                    replacementRow = document.createElement('tr');
                    replacementRow.className = 'package-replacements-row';

                    const cell = document.createElement('td');
                    cell.colSpan = 5;
                    cell.className = 'package-replacements-cell';

                    const container = document.createElement('div');
                    container.className = 'package-replacements';

                    cell.appendChild(container);
                    replacementRow.appendChild(cell);

                    row.after(replacementRow);
                    row._replacementRow = replacementRow;
                }

                const container =
                    replacementRow.querySelector('.package-replacements');

                container.replaceChildren();

                if ($('orderType').value !== 'paket_custom') {
                    replacementRow.hidden = true;
                    return;
                }

                const selectedPackage = packagesData.find(
                    item => String(item.id) === String(packageId)
                );

                if (!selectedPackage?.details?.length) {
                    replacementRow.hidden = true;
                    return;
                }

                replacementRow.hidden = false;

                const panel = document.createElement('div');
                panel.className = 'package-replacement-panel';

                const heading = document.createElement('div');
                heading.className = 'fw-bold small mb-1';
                heading.textContent = 'Penggantian Isi Paket';

                const description = document.createElement('div');
                description.className = 'text-muted small mb-3';
                description.textContent =
                    'Gunakan menu bawaan atau pilih menu pengganti untuk setiap komponen.';

                const list = document.createElement('div');
                list.className = 'package-replacement-list';

                selectedPackage.details.forEach(detail => {
                    const originalMenuId = Number(
                        detail.menu_id ??
                        detail.menus_id ??
                        detail.menu?.id ??
                        0
                    );

                    if (!originalMenuId) {
                        console.error('ID menu bawaan tidak ditemukan:', detail);
                        return;
                    }

                    const originalMenuName =
                        detail.menu?.name ?? 'Menu tidak ditemukan';

                    const item = document.createElement('div');
                    item.className = 'package-replacement-item';

                    const label = document.createElement('label');
                    label.className = 'small fw-semibold';
                    label.textContent =
                        `${originalMenuName} × ${detail.quantity ?? 1}`;

                    const select = document.createElement('select');
                    select.className = 'form-select form-select-sm';
                    select.name =
                        `replacements[${indexForPackageRow(row)}][${originalMenuId}]`;

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = `Tetap: ${originalMenuName}`;
                    select.appendChild(defaultOption);

                    const originalCategoryId = detail.menu?.category_id;

                    menusData.forEach(menu => {
                        if (Number(menu.id) === originalMenuId) return;

                        if (
                            originalCategoryId == null ||
                            String(menu.category_id) !== String(originalCategoryId)
                        ) {
                            return;
                        }

                        const option = document.createElement('option');
                        option.value = menu.id;
                        option.textContent =
                            `${menu.name} - ${rupiah(menu.price)} (stok ${menu.stock})`;

                        select.appendChild(option);
                    });

                    item.append(label, select);
                    list.appendChild(item);
                });

                panel.append(heading, description, list);
                container.appendChild(panel);
            }

            // Tambah baris paket
            function addPackageRow() {
                const index = packageIndex++;
                const row = document.createElement('tr');
                row.className = 'package-row';

                const nameCell = document.createElement('td');

                const select = document.createElement('select');
                select.name = `packages[${index}][package_id]`;
                select.className = 'form-select package-select';
                select.required = true;
                fillOptions(select, packagesData, 'package');

                nameCell.appendChild(select);

                const quantityCell = document.createElement('td');
                const quantity = document.createElement('input');
                quantity.type = 'number';
                quantity.name = `packages[${index}][quantity]`;
                quantity.min = '1';
                quantity.value = '1';
                quantity.required = true;
                quantity.className = 'form-control package-quantity';
                quantityCell.appendChild(quantity);

                const priceCell = document.createElement('td');
                const priceInput = document.createElement('input');
                priceInput.type = 'number';
                priceInput.name = `packages[${index}][price]`;
                priceInput.min = '0';
                priceInput.step = '1';
                priceInput.value = '0';
                priceInput.required = true;
                priceInput.className = 'form-control package-price-input';
                priceCell.appendChild(priceInput);

                const subtotalCell = document.createElement('td');
                subtotalCell.className = 'package-subtotal money';
                subtotalCell.textContent = 'Rp0';

                const removeCell = document.createElement('td');
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'btn btn-outline-danger remove-row';
                removeButton.setAttribute('aria-label', 'Hapus paket');
                removeButton.innerHTML = '<i class="bi bi-trash3"></i>';
                removeCell.appendChild(removeButton);

                row.append(
                    nameCell,
                    quantityCell,
                    priceCell,
                    subtotalCell,
                    removeCell
                );

                $('packageItems').appendChild(row);

                select.addEventListener('change', function () {
                    const option = this.options[this.selectedIndex];

                    priceInput.value = Math.round(
                        Number(option.dataset.price || 0)
                    );

                    renderPackageReplacements(row, this.value);
                    updatePackagePriceMode();
                    updateTotals();
                });

                updatePackagePriceMode();
                updateSections();
            }

            // Tambah baris menu custom
            
            function addMenuRow() {
                const index = menuIndex++;

                const row = document.createElement('div');
                row.className = 'line-item menu-row';

                row.innerHTML = `
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select menu-category-select" required>
                                <option value="">Pilih kategori</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Menu</label>
                            <select name="items[${index}][menu_id]"
                                    class="form-select menu-select"
                                    required disabled>
                                <option value="">Pilih kategori dahulu</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Jumlah</label>
                            <input type="number"
                                name="items[${index}][quantity]"
                                class="form-control menu-quantity"
                                min="1" value="1" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Harga/Menu (Rp)</label>
                            <input type="number"
                                name="items[${index}][price]"
                                class="form-control menu-price-input"
                                min="0" step="1" value="0" required>
                        </div>

                        <div class="col-md-2">
                            <button type="button"
                                    class="btn btn-outline-danger w-100 remove-row">
                                <i class="bi bi-trash3 me-1"></i> Hapus
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Subtotal menu</span>
                                <strong class="menu-subtotal">Rp0</strong>
                            </div>
                        </div>
                    </div>
                `;

                const categorySelect = row.querySelector('.menu-category-select');
                const menuSelect = row.querySelector('.menu-select');
                const quantityInput = row.querySelector('.menu-quantity');
                const priceInput = row.querySelector('.menu-price-input');

                // Ambil kategori unik dari data menu.
                const categories = new Map();

                menusData.forEach(menu => {
                    const categoryId = menu.category?.id ?? menu.category_id;
                    const categoryName =
                        menu.category?.name ?? menu.category_name ?? 'Tanpa Kategori';

                    if (categoryId !== null && categoryId !== undefined) {
                        categories.set(String(categoryId), categoryName);
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

                // Muat menu berdasarkan kategori yang dipilih.
                categorySelect.addEventListener('change', function () {
                    fillMenuOptions(menuSelect, this.value);
                    priceInput.value = '0';
                    updateTotals();
                });

                // Isi harga awal berdasarkan menu dari katalog.
                menuSelect.addEventListener('change', function () {
                    const option = this.selectedOptions[0];

                    priceInput.value = Math.round(
                        Number(option?.dataset.price || 0)
                    );

                    updateTotals();
                });

                quantityInput.addEventListener('input', updateTotals);
                priceInput.addEventListener('input', updateTotals);

                $('menuItems').appendChild(row);

                updateSections();
                updateTotals();
            }

            function getSubtotal(row, selectClass, quantityClass) {
                const select = row.querySelector(selectClass);
                const option = select?.options[select.selectedIndex];

                const priceInput = row.querySelector('.menu-price-input');
                const price = Number(
                    priceInput?.value || option?.dataset.price || 0
                );

                const quantity = Math.max(
                    0,
                    Number(row.querySelector(quantityClass)?.value || 0)
                );

                return { price, subtotal: price * quantity };
            }


            // Hitung ulang ringkasan
            function updateTotals() {
                let packageTotal = 0;
                let menuTotal = 0;

                document.querySelectorAll('.package-row').forEach(row => {
                    const quantity = Number(
                        row.querySelector('.package-quantity')?.value || 0
                    );

                    const price = Number(
                        row.querySelector('.package-price-input')?.value || 0
                    );

                    const subtotal = price * quantity;
                    packageTotal += subtotal;

                    row.querySelector('.package-subtotal').textContent =
                        rupiah(subtotal);
                });

                document.querySelectorAll('.menu-row').forEach(row => {
                    const result = getSubtotal(
                        row,
                        '.menu-select',
                        '.menu-quantity'
                    );

                    menuTotal += result.subtotal;

                    row.querySelector('.menu-subtotal').textContent =
                        rupiah(result.subtotal);
                });

                // Ongkir belum ditentukan pada saat pesanan dibuat.
                $('packageTotal').textContent = rupiah(packageTotal);
                $('menuTotal').textContent = rupiah(menuTotal);
                $('shippingTotal').textContent = 'Belum ditentukan';

                // Ini masih subtotal, belum termasuk ongkir.
                $('grandTotal').textContent =
                    rupiah(packageTotal + menuTotal);
            }

            // Atur bagian yang aktif sesuai jenis pesanan
            function updateSections() {
                const type = $('orderType').value;
                const showPackages = type === 'paket' || type === 'paket_custom';
                const showMenus = type === 'custom' || type === 'paket_custom';

                $('packageSection').hidden = !showPackages;
                $('menuSection').hidden = !showMenus;

                document.querySelectorAll('.package-select, .package-quantity')
                    .forEach(el => {
                        el.disabled = !showPackages;
                        el.required = showPackages;
                    });

                // Harga paket tidak wajib diisi untuk menu custom.
                document.querySelectorAll('.package-price-input')
                    .forEach(el => {
                        el.disabled = !showPackages;
                        el.required = showPackages;
                    });


                document.querySelectorAll(
                    '.menu-category-select, .menu-select, .menu-quantity'
                ).forEach(el => {
                    el.disabled = !showMenus;
                    el.required = showMenus &&
                        el.classList.contains('menu-select');
                });

                document.querySelectorAll('.menu-price-input').forEach(el => {
                    el.disabled = !showMenus;
                    el.required = showMenus;
                });


                updateTotals();
            }

            function resetPackageItems() {
                $('packageItems').replaceChildren();
            }

            function resetMenuItems() {
                $('menuItems').replaceChildren();
            }

            function handleOrderTypeChange(type) {
                $('orderType').value = type;

                if (type === 'custom') {
                    resetPackageItems();
                }

                if (type === 'paket') {
                    resetMenuItems();
                }

                updateSections();

                document.querySelectorAll('.package-row').forEach(row => {
                    const packageId = row.querySelector('.package-select')?.value;
                    renderPackageReplacements(row, packageId);
                });

                updatePackagePriceMode();

                document.querySelectorAll('.order-type-card').forEach(card => {
                    const radio = card.querySelector('.order-type-choice');
                    const label = card.querySelector('.type-choice-text');
                    const active = radio?.checked ?? false;

                    card.classList.toggle('active', active);

                    if (label) {
                        label.textContent = active
                            ? 'Jenis dipilih'
                            : 'Pilih jenis ini';
                    }
                });

                updateTotals();
            }

            $('addPackage').addEventListener('click', addPackageRow);
            $('addMenu').addEventListener('click', addMenuRow);

            document.querySelectorAll('.order-type-choice').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.checked) {
                        handleOrderTypeChange(this.value);
                    }
                });
            });

            document.addEventListener('input', event => {
                if (event.target.matches(
                    '.package-price-input, .package-quantity, .menu-price-input, .menu-quantity'
                )) {
                    updateTotals();
                }
            });

            document.addEventListener('change', event => {
                if (event.target.matches('.package-select, .menu-select')) {
                    updateTotals();
                }
            });

            document.addEventListener('click', event => {
                const button = event.target.closest('.remove-row');
                if (!button) return;

                const row = button.closest('.package-row, .menu-row');
                if (!row) return;

                const isPackage = row.classList.contains('package-row');
                const container = isPackage ? $('packageItems') : $('menuItems');

                const activeRows = container.querySelectorAll(
                    isPackage ? '.package-row' : '.menu-row'
                );

                if (activeRows.length <= 1) {
                    alert('Minimal satu baris harus tersedia pada bagian yang aktif.');
                    return;
                }

                if (row._replacementRow) {
                    row._replacementRow.remove();
                }

                row.remove();
                updateTotals();
            });

            // Buat baris awal sesuai jenis pesanan yang dipilih.
            const initialType = $('orderType').value;

            if (initialType === 'paket' || initialType === 'paket_custom') {
                addPackageRow();
            }

            if (initialType === 'custom' || initialType === 'paket_custom') {
                addMenuRow();
            }

            // Sinkronkan tampilan kartu dan bagian yang aktif saat halaman dibuka.
            document.querySelectorAll('.order-type-card').forEach(card => {
                const radio = card.querySelector('.order-type-choice');
                const label = card.querySelector('.type-choice-text');
                const active = radio?.checked ?? false;

                card.classList.toggle('active', active);

                if (label) {
                    label.textContent = active
                        ? 'Jenis dipilih'
                        : 'Pilih jenis ini';
                }
            });

            updatePackagePriceMode();
            updateSections();
        });
    </script>
    @endpush
