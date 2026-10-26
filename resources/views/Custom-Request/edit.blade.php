@extends('layouts.admin')

@section('title', 'Review Permintaan Custom')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Review Permintaan Custom</h4>
            <p class="text-muted mb-0">
                Tentukan harga untuk pesanan custom customer.
            </p>
        </div>

        <a href="{{ route('admin.custom-requests.show', $customRequest->id) }}"
           class="btn btn-secondary">
            Kembali
        </a>
    </div>


    {{-- INFORMASI CUSTOMER --}}

    <div class="card mb-4">
        <div class="card-header">
            <strong>Informasi Customer</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Nama</label>
                    <div>
                        {{ $customRequest->customer->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">No. HP</label>
                    <div>
                        {{ $customRequest->customer->phone ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Judul</label>
                    <div>
                        {{ $customRequest->title }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Jumlah Tamu</label>
                    <div>
                        {{ $customRequest->guest_count }} orang
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Tanggal Acara</label>
                    <div>
                        {{ $customRequest->event_date->format('d M Y') }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Alamat</label>
                    <div>
                        {{ $customRequest->address }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- MENU --}}

    <form action="{{ route('admin.custom-requests.update', $customRequest->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        {{-- paket+custom --}}        
        {{-- HARGA PAKET SETELAH PENYESUAIAN --}}
        @if ($customRequest->packages->isNotEmpty())
            @foreach ($customRequest->packages as $package)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-success-subtle border-0 p-3">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-box-seam me-2"></i>
                            Harga Paket Setelah Penyesuaian
                        </h5>
                        <small class="text-muted">
                            {{ $package->name }} · {{ $package->pivot->quantity }} paket
                        </small>
                    </div>

                    <div class="card-body p-4">
                        <label for="package_price" class="form-label fw-semibold">
                            Harga per paket (Rp)
                        </label>

                        <input
                            type="number"
                            name="package_price"
                            id="package_price"
                            class="form-control form-control-lg"
                            min="0"
                            step="1000"
                            value="{{ old('package_price', $package->pivot->price) }}"
                            required
                        >

                        <div class="form-text">
                            Harga ini mencakup seluruh isi paket setelah perubahan,
                            termasuk menu pengganti. Jangan masukkan harga menu
                            pengganti sebagai tambahan lagi.
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <span class="text-muted">Jumlah paket</span>
                            <strong>{{ $package->pivot->quantity }} paket</strong>
                        </div>

                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-muted">Subtotal paket</span>
                            <strong id="packageSubtotal">Rp0</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- MENU TAMBAHAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-3">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-plus-circle text-success me-2"></i>
                    Menu Tambahan
                </h5>
                <small class="text-muted">
                    Menu di luar isi paket. Menu pengganti tidak dihitung di bagian ini.
                </small>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nama Menu</th>
                                <th>Jumlah</th>
                                <th>Harga Referensi</th>
                                <th style="min-width:160px">Harga Disetujui / Porsi</th>
                                <th class="pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $additionalDetails = $customRequest->details
                                    ->where('customization_type', '!=', 'replacement');
                            @endphp

                            @forelse ($additionalDetails as $detail)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">
                                            {{ $detail->menu->name ?? 'Menu tidak ditemukan' }}
                                        </div>
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            Menu tambahan
                                        </span>
                                    </td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>
                                        Rp{{ number_format($detail->requested_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="approved_price[{{ $detail->id }}]"
                                            class="form-control additional-price"
                                            data-quantity="{{ $detail->quantity }}"
                                            min="0"
                                            step="100"
                                            value="{{ old(
                                                'approved_price.'.$detail->id,
                                                $detail->approved_price ?? $detail->requested_price ?? 0
                                            ) }}"
                                            required
                                        >
                                    </td>
                                    <td class="pe-3 fw-semibold">
                                        <span class="additional-subtotal">0</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Tidak ada menu tambahan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TOTAL PENAWARAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal paket</span>
                    <strong id="summaryPackage">Rp0</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total menu tambahan</span>
                    <strong id="summaryAdditional">Rp0</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">Total Penawaran</span>
                    <strong class="fs-4 text-success" id="grandTotal">Rp0</strong>
                </div>
            </div>
        </div>


        {{-- //custom --}}
        {{-- <div class="card mb-4">

            <div class="card-header">
                <strong>Menu yang Diminta</strong>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th width="100">Jumlah</th>
                                <th>Harga Awal</th>
                                <th width="220">Harga Disetujui</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($customRequest->details as $detail)

                            <tr>

                                <td>
                                    {{ $detail->menu->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->quantity }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->requested_price ?? 0, 0, ',', '.') }}
                                </td>

                                <td>

                                    <input
                                        type="number"
                                        name="approved_price[{{ $detail->id }}]"
                                        class="form-control"
                                        min="0"
                                        step="100"
                                        value="{{ old('approved_price.' . $detail->id, number_format($detail->approved_price ?? $detail->requested_price ?? 0, 0, '.','')) }}"
                                        required
                                    >

                                </td>

                                <td>
                                    Rp
                                    <span class="subtotal"
                                          data-quantity="{{ $detail->quantity }}"
                                          data-detail="{{ $detail->id }}">
                                        {{ number_format(
                                            ($detail->approved_price ?? $detail->requested_price ?? 0)
                                            * $detail->quantity,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </span>
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                        <tfoot>

                            <tr>
                                <th colspan="4" class="text-end">
                                    Total
                                </th>

                                <th>
                                    Rp <span id="grandTotal">0</span>
                                </th>
                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div> --}}


        {{-- CATATAN ADMIN --}}

        <div class="card mb-4">

            <div class="card-header">
                <strong>Catatan Admin</strong>
            </div>

            <div class="card-body">

                <textarea
                    name="admin_notes"
                    class="form-control"
                    rows="4"
                    placeholder="Tambahkan catatan untuk customer jika diperlukan..."
                >{{ old('admin_notes', $customRequest->admin_notes) }}</textarea>

            </div>

        </div>


        {{-- STATUS --}}

        <div class="card mb-4">

            <div class="card-header">
                <strong>Status Permintaan</strong>
            </div>

            <div class="card-body">

                <select name="status" class="form-select" required>

                    <option value="Menunggu Persetujuan"
                        {{ old('status', $customRequest->status) == 'Menunggu Persetujuan' ? 'selected' : '' }}>
                        Menunggu Persetujuan Customer
                    </option>

                    <option value="Ditolak"
                        {{ old('status', $customRequest->status) == 'Ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>

                    <option value="Disetujui"
                        {{ old('status', $customRequest->status) == 'Disetujui' ? 'selected' : '' }}>
                        Disetujui
                    </option>        

                </select>

            </div>

        </div>



        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('admin.custom-requests.show', $customRequest->id) }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                Simpan Review
            </button>

        </div>

    </form>

</div>




<script>
document.addEventListener('DOMContentLoaded', function () {
    const packageInput = document.getElementById('package_price');
    const packageQuantity = Number(@json(
        $customRequest->packages->first()?->pivot->quantity ?? 0
    ));

    const additionalInputs = document.querySelectorAll('.additional-price');

    const formatRupiah = (value) =>
        'Rp' + new Intl.NumberFormat('id-ID').format(value);

    function calculateTotal() {
        const packageSubtotal =
            (Number(packageInput?.value) || 0) * packageQuantity;

        let additionalTotal = 0;

        additionalInputs.forEach(function (input) {
            const quantity = Number(input.dataset.quantity) || 0;
            const price = Number(input.value) || 0;
            const subtotal = quantity * price;

            additionalTotal += subtotal;

            const output = input.closest('tr')
                ?.querySelector('.additional-subtotal');

            if (output) {
                output.textContent = formatRupiah(subtotal);
            }
        });

        const packageOutput = document.getElementById('packageSubtotal');
        const summaryPackage = document.getElementById('summaryPackage');
        const summaryAdditional = document.getElementById('summaryAdditional');
        const grandTotal = document.getElementById('grandTotal');

        if (packageOutput) {
            packageOutput.textContent = formatRupiah(packageSubtotal);
        }

        if (summaryPackage) {
            summaryPackage.textContent = formatRupiah(packageSubtotal);
        }

        if (summaryAdditional) {
            summaryAdditional.textContent = formatRupiah(additionalTotal);
        }

        if (grandTotal) {
            grandTotal.textContent =
                formatRupiah(packageSubtotal + additionalTotal);
        }
    }

    packageInput?.addEventListener('input', calculateTotal);

    additionalInputs.forEach(function (input) {
        input.addEventListener('input', calculateTotal);
    });

    calculateTotal();
});
</script>


@endsection