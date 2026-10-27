
@extends('layouts.admin')

@section('title', 'Tambah Pesanan')

@section('content')
<div class="container-fluid py-4 order-page">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill text-bg-success">ADMIN</span>
                <span class="text-muted small">Manajemen pesanan</span>
            </div>
            <h3 class="page-heading mb-1">Buat Pesanan Baru</h3>
            <p class="page-subtitle mb-0">
                Pilih paket catering, atur jadwal acara, dan catat pesanan pelanggan.
            </p>
        </div>

        <a href="{{ route('orders.index') }}"
           class="btn btn-outline-secondary rounded-3 px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('error'))
        <div class="alert alert-danger rounded-3 border-0">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
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
          id="orderForm">
        @csrf

        {{-- INFORMASI PESANAN --}}
        <section class="order-card">
            <div class="section-heading">
                <div class="section-icon">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
                <div>
                    <h5 class="section-title">Informasi Pesanan</h5>
                    <p class="section-description">
                        Pelanggan dan jadwal acara.
                    </p>
                </div>
            </div>

            <div class="section-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">
                            Pelanggan <span class="text-danger">*</span>
                        </label>

                        <div class="customer-select-wrap">
                            <div class="customer-select-icon">
                                <i class="bi bi-person"></i>
                            </div>

                            <select name="customer_id"
                                    id="customerSelect"
                                    class="form-select customer-select"
                                    required>
                                <option value="">Pilih pelanggan</option>

                                @foreach($customers as $customer)
                                    <option
                                        value="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-phone="{{ $customer->phone ?? '' }}"
                                        data-address="{{ $customer->address ?? '' }}"
                                        @selected(old('customer_id') == $customer->id)>
                                        {{ $customer->name }}
                                        @if($customer->phone)
                                            - {{ $customer->phone }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-hint">
                            Pilih pelanggan yang sudah terdaftar di sistem.
                        </div>

                        <div id="customerPreview"
                             class="customer-preview mt-3"
                             hidden>
                            <div class="customer-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="customer-preview-content">
                                <div class="small text-muted">Pesanan untuk</div>
                                <div class="fw-bold" id="customerPreviewName">-</div>

                                <div class="customer-meta">
                                    <span>
                                        <i class="bi bi-telephone me-1"></i>
                                        <span id="customerPreviewPhone">-</span>
                                    </span>
                                    <span>
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <span id="customerPreviewAddress">-</span>
                                    </span>
                                </div>
                            </div>

                            <span class="customer-selected-badge">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Dipilih
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Tanggal Pesanan <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="order_date"
                               class="form-control"
                               value="{{ old('order_date', date('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Tanggal Acara <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="delivery_date"
                               class="form-control"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('delivery_date') }}"
                               required>
                        <div class="field-hint">
                            Tanggal pesanan siap diambil atau dikirim.
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- PAKET CATERING --}}
        <section class="order-card">
            <div class="section-heading">
                <div class="section-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h5 class="section-title">Paket Catering</h5>
                    <p class="section-description">
                        Pilih paket yang tersedia dan tentukan jumlah pesanan.
                    </p>
                </div>
            </div>

            <div class="section-body">
                @if($packages->isEmpty())
                    <div class="alert alert-warning rounded-3">
                        Belum ada paket tersedia dengan menu penyusun yang
                        memiliki stok. Periksa data paket dan stok menu terlebih dahulu.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table order-table align-middle">
                        <thead>
                            <tr>
                                <th style="min-width:220px">Nama Paket</th>
                                <th style="min-width:100px">Jumlah</th>
                                <th style="min-width:140px">Harga</th>
                                <th style="min-width:140px">Subtotal</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="packageItems"></tbody>
                    </table>
                </div>

                <button type="button"
                        class="btn btn-outline-success btn-sm rounded-3"
                        id="addPackage"
                        @disabled($packages->isEmpty())>
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Paket
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
                    <p class="section-description">
                        Informasi tambahan untuk pesanan ini.
                    </p>
                </div>
            </div>

            <div class="section-body">
                <textarea name="notes"
                          class="form-control"
                          rows="3"
                          placeholder="Contoh: pesanan untuk acara keluarga, sambal dipisah, dan lainnya">{{ old('notes') }}</textarea>
            </div>
        </section>

        {{-- RINGKASAN --}}
        <section class="order-card mt-4">
            <div class="section-heading">
                <div class="section-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h5 class="section-title">Ringkasan Pesanan</h5>
                    <p class="section-description">
                        Periksa jumlah paket dan subtotal sebelum menyimpan.
                    </p>
                </div>
            </div>

            <div class="section-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="summary-line">
                            <span>Subtotal Paket</span>
                            <strong id="packageTotal">Rp0</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
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

                        <button type="submit"
                                class="btn btn-success px-4"
                                @disabled($packages->isEmpty())>
                            <i class="bi bi-check2-circle me-2"></i>
                            Simpan Pesanan
                        </button>
                    </div>
                </div>

                <div class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Total belum termasuk ongkos kirim. Metode pengiriman dan
                    biaya final diatur melalui Detail Pesanan.
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
    const oldPackages = @json(old('packages', []));

    const $ = id => document.getElementById(id);

    const rupiah = value =>
        'Rp' + Number(value || 0).toLocaleString('id-ID', {
            maximumFractionDigits: 0
        });

    let packageIndex = 0;

    // Informasi pelanggan
    const customerSelect = $('customerSelect');

    function updateCustomerPreview() {
        const option = customerSelect.options[customerSelect.selectedIndex];
        const selected = Boolean(customerSelect.value);

        $('customerPreview').hidden = !selected;

        if (!selected) return;

        $('customerPreviewName').textContent =
            option.dataset.name || '-';

        $('customerPreviewPhone').textContent =
            option.dataset.phone || 'Belum ada nomor telepon';

        $('customerPreviewAddress').textContent =
            option.dataset.address || 'Alamat belum tersedia';
    }

    customerSelect.addEventListener('change', updateCustomerPreview);
    updateCustomerPreview();

    // Membuat pilihan paket dari data database
    function fillPackageOptions(select) {
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Pilih paket catering';
        select.appendChild(placeholder);

        packagesData.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.dataset.price = item.price;
            option.textContent = `${item.name} - ${rupiah(item.price)}`;
            select.appendChild(option);
        });
    }

    function updateTotals() {
        let packageTotal = 0;

        document.querySelectorAll('.package-row').forEach(row => {
            const select = row.querySelector('.package-select');
            const quantityInput = row.querySelector('.package-quantity');
            const priceDisplay = row.querySelector('.package-price');
            const subtotalDisplay = row.querySelector('.package-subtotal');

            const option = select.options[select.selectedIndex];
            const price = Number(option?.dataset.price || 0);
            const quantity = Math.max(
                0,
                Number(quantityInput.value || 0)
            );

            const subtotal = price * quantity;
            packageTotal += subtotal;

            priceDisplay.textContent = rupiah(price);
            subtotalDisplay.textContent = rupiah(subtotal);
        });

        $('packageTotal').textContent = rupiah(packageTotal);
        $('shippingTotal').textContent = 'Belum ditentukan';
        $('grandTotal').textContent = rupiah(packageTotal);
    }

    // Tambah baris paket
    function addPackageRow(initial = {}) {
        const index = packageIndex++;
        const row = document.createElement('tr');
        row.className = 'package-row';

        const nameCell = document.createElement('td');
        const select = document.createElement('select');

        select.name = `packages[${index}][package_id]`;
        select.className = 'form-select package-select';
        select.required = true;
        fillPackageOptions(select);

        if (initial.package_id) {
            select.value = String(initial.package_id);
        }

        nameCell.appendChild(select);

        const quantityCell = document.createElement('td');
        const quantity = document.createElement('input');

        quantity.type = 'number';
        quantity.name = `packages[${index}][quantity]`;
        quantity.min = '1';
        quantity.value = initial.quantity ?? 1;
        quantity.className = 'form-control package-quantity';
        quantity.required = true;

        quantityCell.appendChild(quantity);

        const priceCell = document.createElement('td');
        const price = document.createElement('span');
        price.className = 'package-price money';
        price.textContent = 'Rp0';
        priceCell.appendChild(price);

        const subtotalCell = document.createElement('td');
        subtotalCell.className = 'package-subtotal money';
        subtotalCell.textContent = 'Rp0';

        const removeCell = document.createElement('td');
        const removeButton = document.createElement('button');

        removeButton.type = 'button';
        removeButton.className = 'btn btn-outline-danger remove-package';
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

        select.addEventListener('change', updateTotals);
        quantity.addEventListener('input', updateTotals);

        updateTotals();
    }

    $('addPackage').addEventListener('click', function () {
        addPackageRow();
    });

    $('packageItems').addEventListener('click', function (event) {
        const button = event.target.closest('.remove-package');
        if (!button) return;

        const row = button.closest('.package-row');
        if (row) {
            row.remove();
            updateTotals();
        }
    });

    // Pulihkan paket yang dipilih jika validasi sebelumnya gagal.
    if (oldPackages.length > 0) {
        oldPackages.forEach(item => addPackageRow(item));
    } else if (packagesData.length > 0) {
        addPackageRow();
    }

    // Cegah submit jika admin belum memilih paket.
    $('orderForm').addEventListener('submit', function (event) {
        const rows = document.querySelectorAll('.package-row');

        if (rows.length === 0) {
            event.preventDefault();
            alert('Tambahkan minimal satu paket catering.');
            return;
        }

        const invalidRow = [...rows].some(row => {
            const packageId = row.querySelector('.package-select').value;
            const quantity = Number(
                row.querySelector('.package-quantity').value
            );

            return !packageId || !Number.isInteger(quantity) || quantity < 1;
        });

        if (invalidRow) {
            event.preventDefault();
            alert('Pilih paket dan masukkan jumlah minimal 1 pada setiap baris.');
        }
    });
});
</script>
@endpush
