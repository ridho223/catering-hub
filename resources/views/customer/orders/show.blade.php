
@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container py-4 py-lg-5">

            @php
            $statusOrder = strtolower((string) $order->status);

            $warnaOrder = match ($statusOrder) {
                'completed', 'selesai', 'completed order' => 'success',
                'cancelled', 'dibatalkan', 'batal' => 'danger',
                'ready for pickup/delivery', 'siap diambil', 'siap dikirim' => 'info',
                'order processing', 'diproses', 'sedang diproses' => 'primary',
                default => 'warning',
            };

            $statusPembayaran = $order->payment?->status ?? 'Belum dibuat';

            $warnaPembayaran = match ($statusPembayaran) {
                'Dibayar' => 'success',
                'Ditolak' => 'danger',
                'Menunggu Verifikasi' => 'warning',
                default => 'secondary',
            };
        @endphp

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('customer.orders.index') }}"
               class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Pesanan Saya
            </a>

            <h2 class="fw-bold mt-3 mb-1">Detail Pesanan</h2>
            <p class="text-muted mb-0">
                Kode pesanan: <strong>{{ $order->order_code }}</strong>
            </p>
        </div>

        <span class="badge rounded-pill bg-{{ $warnaOrder }}-subtle text-{{ $warnaOrder }} px-3 py-2">
            {{ $order->status }}
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    @php
        $metodePengiriman = $order->delivery_method;
        $pengirimanTerkonfirmasi =
            $metodePengiriman === 'pickup' ||
            ($metodePengiriman === 'delivery' && $order->delivery_distance !== null);

        $statusPembayaran = $order->payment?->status;

        $alamatPengiriman = $order->customRequest?->address
            ?? $order->delivery_address
            ?? $order->customer?->address
            ?? '-';

        $namaCustomer = $order->customer?->name ?? '-';
        $teleponCustomer = $order->customer?->phone ?? '-';
    @endphp

    <div class="row g-4">

        {{-- Kolom utama --}}
        <div class="col-lg-8">

            {{-- Informasi pengiriman --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Informasi Pesanan</h5>
                            <p class="text-muted small mb-0">Pastikan data pesananmu sudah benar.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="text-muted small mb-1">Nama penerima/pemesan</div>
                                <div class="fw-semibold">{{ $namaCustomer }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="text-muted small mb-1">Nomor HP</div>
                                <div class="fw-semibold">{{ $teleponCustomer }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="text-muted small mb-1">Tanggal pemesanan</div>
                                <div class="fw-semibold">
                                    {{ $order->order_date
                                        ? \Carbon\Carbon::parse($order->order_date)->format('d M Y')
                                        : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="text-muted small mb-1">Tanggal acara/pengiriman</div>
                                <div class="fw-semibold">
                                    {{ $order->delivery_date
                                        ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y')
                                        : 'Belum ditentukan' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small mb-1">Metode penerimaan</div>
                                <div class="fw-semibold">
                                    @if ($metodePengiriman === 'pickup')
                                        <i class="bi bi-shop me-2 text-success"></i>Ambil sendiri
                                    @elseif ($metodePengiriman === 'delivery')
                                        <i class="bi bi-truck me-2 text-success"></i>Diantar oleh pihak catering
                                    @else
                                        Belum ditentukan
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($metodePengiriman === 'delivery')
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="text-muted small mb-1">Alamat pengiriman</div>
                                    <div class="fw-semibold">{{ $alamatPengiriman }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($order->notes)
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="text-muted small mb-1">Catatan pesanan</div>
                                    <div>{{ $order->notes }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rincian item --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-basket2 me-2 text-success"></i>Rincian Pesanan
                    </h5>

                    @if ($order->packages->count())
                        <h6 class="fw-semibold text-muted mb-3">Paket Catering</h6>

                        @foreach ($order->packages as $orderPackage)
                            <div class="border rounded-3 p-3 mb-3">
                                {{-- Informasi paket --}}
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-bold">
                                            {{ $orderPackage->package?->name ?? 'Paket Catering' }}
                                        </div>

                                        <div class="text-muted small mt-1">
                                            {{ $orderPackage->quantity }} paket ×
                                            Rp{{ number_format($orderPackage->price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="fw-semibold text-end text-nowrap">
                                        Rp{{ number_format($orderPackage->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- Isi paket dan menu pengganti --}}
                                @if ($orderPackage->package?->details?->count())
                                    <div class="small mt-3 pt-3 border-top">
                                        <div class="fw-semibold mb-2">Isi paket</div>

                                        @foreach ($orderPackage->package->details as $packageDetail)
                                            @php
                                                $replacement = $order->customRequest?->details
                                                    ?->first(function ($detail) use ($packageDetail) {
                                                        return $detail->customization_type === 'replacement'
                                                            && (int) $detail->replaces_menu_id
                                                                === (int) $packageDetail->menus_id;
                                                    });

                                                $menuName = $replacement?->menu?->name
                                                    ?? $packageDetail->menu?->name
                                                    ?? 'Menu tidak ditemukan';

                                                $quantity = (int) $packageDetail->quantity
                                                    * (int) $orderPackage->quantity;
                                            @endphp

                                            <div class="d-flex justify-content-between align-items-start gap-2 py-1">
                                                <div>
                                                    @if ($replacement)
                                                        <span class="text-muted text-decoration-line-through">
                                                            {{ $packageDetail->menu?->name ?? 'Menu asli' }}
                                                        </span>

                                                        <i class="bi bi-arrow-right text-success mx-1"></i>

                                                        <span class="text-success fw-semibold">
                                                            {{ $menuName }}
                                                        </span>
                                                    @else
                                                        {{ $menuName }}
                                                    @endif

                                                    <span class="text-muted">
                                                        ({{ $quantity }} porsi)
                                                    </span>
                                                </div>

                                                @if ($replacement)
                                                    <span class="badge bg-success-subtle text-success">
                                                        Pengganti
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    @php
                        $menuTambahan = $order->packages->isNotEmpty()
                            ? $order->details->where('source', 'custom')
                            : $order->details;
                    @endphp

                    @if ($menuTambahan->isNotEmpty())
                        <h6 class="fw-semibold text-muted mt-4 mb-3">
                            {{ $order->packages->isNotEmpty() ? 'Menu Tambahan' : 'Menu Custom' }}
                        </h6>

                        @foreach ($menuTambahan as $detail)
                            <div class="d-flex justify-content-between align-items-start gap-3 py-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $detail->menu?->name ?? 'Menu Catering' }}
                                    </div>

                                    <div class="text-muted small mt-1">
                                        {{ $detail->quantity }} ×
                                        Rp{{ number_format($detail->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="fw-semibold text-end text-nowrap">
                                    Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if (!$order->packages->count() && !($order->customRequest && $order->details->count()))
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Rincian item belum tersedia.
                        </div>
                    @endif

                    <div class="mt-4">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="text-muted">Subtotal pesanan</span>
                            <span class="fw-semibold text-end">
                                Rp{{ number_format($subtotalPesanan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="text-muted">Biaya pengiriman</span>
                            <span class="fw-semibold text-end">
                                @if ($metodePengiriman === 'pickup')
                                    Gratis
                                @elseif ($metodePengiriman === 'delivery' && !$pengirimanTerkonfirmasi)
                                    Menunggu konfirmasi
                                @else
                                    Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>

                        @if ($metodePengiriman === 'delivery' && $order->delivery_distance !== null)
                            <div class="text-muted small text-end mb-3">
                                Jarak pengiriman: {{ $order->delivery_distance }} km
                            </div>
                        @elseif ($metodePengiriman === 'delivery')
                            <div class="alert alert-info small mt-3 mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Admin akan mengonfirmasi jarak dan biaya pengiriman. Biaya belum dianggap final.
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-bold">Total pembayaran</div>
                                <div class="text-muted small">Termasuk biaya yang telah ditetapkan</div>
                            </div>
                            <div class="text-success fs-4 fw-bold text-end text-nowrap">
                                Rp{{ number_format($order->total, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom pembayaran --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Status Pembayaran</h5>

                    @if (!$order->payment)
                        <div class="d-flex gap-3 align-items-start mb-3">
                            <i class="bi bi-clock-history text-warning fs-3"></i>
                            <div>
                                <div class="fw-semibold">Belum dibayar</div>
                                <div class="text-muted small">
                                    {{ $pengirimanTerkonfirmasi
                                        ? 'Pesanan siap dilanjutkan ke pembayaran.'
                                        : 'Tunggu konfirmasi pengiriman dari admin sebelum membayar.' }}
                                </div>
                            </div>
                        </div>

                        @if ($pengirimanTerkonfirmasi)
                            <a href="{{ route('customer.payment.create', $order->id) }}"
                               class="btn btn-success w-100 rounded-pill py-2">
                                <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                            </a>
                        @endif

                    @elseif ($statusPembayaran === 'Menunggu Verifikasi')
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-hourglass-split me-2"></i>
                            Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi admin.
                        </div>

                    @elseif ($statusPembayaran === 'Dibayar')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Pembayaran sudah diverifikasi. Simpan kode pesanan ini untuk referensi.
                        </div>

                    @elseif ($statusPembayaran === 'Ditolak')
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-x-circle me-1"></i>Pembayaran ditolak</strong>
                            @if ($order->payment->notes)
                                <div class="small mt-2">Alasan: {{ $order->payment->notes }}</div>
                            @endif
                        </div>

                        @if ($pengirimanTerkonfirmasi)
                            <a href="{{ route('customer.payment.create', $order->id) }}"
                               class="btn btn-warning w-100 rounded-pill">
                                <i class="bi bi-arrow-repeat me-2"></i>Kirim Ulang Pembayaran
                            </a>
                        @endif

                    @else
                        <div class="alert alert-secondary mb-0">
                            Status pembayaran: {{ $statusPembayaran }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 rounded-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold">
                        <i class="bi bi-headset me-2 text-success"></i>Butuh bantuan?
                    </h6>
                    <p class="text-muted small mb-0">
                        Jika ada data pesanan atau pembayaran yang tidak sesuai, hubungi admin catering dan sertakan kode pesananmu.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
