@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $statusOrder = strtolower(trim((string) $order->status));
    $metodePengiriman = strtolower(trim((string) ($order->delivery_method ?? '')));
    $statusPembayaran = $order->paymentState();

    $pesananDibatalkan = in_array($statusOrder, [
        'dibatalkan', 'cancelled', 'canceled', 'batal'
    ], true);

    $tahapPesanan = match ($statusOrder) {
        'pending' => 1,
        'diproses' => 2,
        'siap_diambil_atau_diantar',
        'siap diambil/diantar',
        'siap diambil atau diantar' => 3,
        'selesai' => 4,
        default => 0,
    };

    if ($pesananDibatalkan) {
        $judulStatus = 'Pesanan dibatalkan';
        $deskripsiStatus = 'Pesanan ini dibatalkan dan tidak melanjutkan proses berikutnya.';
        $ikonStatus = 'bi-x-circle';
        $warnaStatus = 'danger';
    } elseif ($tahapPesanan === 1) {
        $judulStatus = 'Menunggu konfirmasi admin';
        $deskripsiStatus = 'Admin akan memeriksa pesananmu sebelum melanjutkan proses.';
        $ikonStatus = 'bi-clipboard-check';
        $warnaStatus = 'warning';
    } elseif ($tahapPesanan === 2) {
        $judulStatus = 'Pesanan sedang diproses';
        $deskripsiStatus = 'Admin sedang menyiapkan pesananmu.';
        $ikonStatus = 'bi-gear';
        $warnaStatus = 'primary';
    } elseif ($tahapPesanan === 3 && $metodePengiriman === 'pickup') {
        $judulStatus = 'Pesanan siap diambil';
        $deskripsiStatus = 'Pesanan sudah siap. Silakan mengikuti informasi pengambilan dari admin.';
        $ikonStatus = 'bi-shop';
        $warnaStatus = 'success';
    } elseif ($tahapPesanan === 3 && $metodePengiriman === 'delivery') {
        $judulStatus = 'Pesanan siap diantar';
        $deskripsiStatus = 'Pesanan sudah memasuki tahap pengiriman. Pantau informasi dari admin.';
        $ikonStatus = 'bi-truck';
        $warnaStatus = 'success';
    } elseif ($tahapPesanan === 4) {
        $judulStatus = 'Pesanan selesai';
        $deskripsiStatus = 'Terima kasih telah memesan di Catering BangJo.';
        $ikonStatus = 'bi-check-circle';
        $warnaStatus = 'success';
    } else {
        $judulStatus = 'Status sedang diperiksa';
        $deskripsiStatus = 'Status pesanan belum dikenali. Silakan hubungi admin.';
        $ikonStatus = 'bi-info-circle';
        $warnaStatus = 'secondary';
    }

    $langkahStatus = [
        1 => [
            'judul' => 'Menunggu konfirmasi',
            'deskripsi' => 'Admin memeriksa detail pesanan.',
            'ikon' => 'bi-clipboard-check',
        ],
        2 => [
            'judul' => 'Sedang diproses',
            'deskripsi' => 'Pesanan sedang disiapkan.',
            'ikon' => 'bi-gear',
        ],
        3 => [
            'judul' => $metodePengiriman === 'pickup' ? 'Siap diambil' : 'Siap diantar',
            'deskripsi' => $metodePengiriman === 'pickup'
                ? 'Pesanan menunggu untuk diambil.'
                : 'Pesanan memasuki tahap pengiriman.',
            'ikon' => $metodePengiriman === 'pickup' ? 'bi-shop' : 'bi-truck',
        ],
        4 => [
            'judul' => 'Selesai',
            'deskripsi' => 'Pesanan telah diselesaikan.',
            'ikon' => 'bi-check-circle',
        ],
    ];

    $pengirimanTerkonfirmasi = $metodePengiriman === 'pickup'
        || ($metodePengiriman === 'delivery' && $order->delivery_distance !== null);

    $alamatPengiriman = $order->customRequest?->address
        ?? $order->delivery_address
        ?? $order->customer?->address
        ?? '-';

    $namaCustomer = $order->customer?->name ?? '-';
    $teleponCustomer = $order->customer?->phone ?? '-';
@endphp

<style>
    :root {
        --det-primary: #198754;
        --det-primary-dark: #145c38;
        --det-primary-light: #eaf5ed;
        --det-text: #212529;
        --det-muted: #6c757d;
        --det-border: #e2e8e3;
        --det-bg: #f8faf8;
        --det-card-bg: #ffffff;
    }

    .detail-page {
        background-color: var(--det-bg);
        min-height: 85vh;
        padding: 24px 0 60px;
        color: var(--det-text);
    }

    .detail-page a {
        text-decoration: none;
    }

    .detail-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--det-muted);
        font-size: 0.82rem;
        font-weight: 700;
        transition: color 0.2s ease;
        margin-bottom: 16px;
    }

    .detail-back-btn:hover {
        color: var(--det-primary-dark);
    }

    /* Hero Banner Konsisten */
    .detail-hero-card {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--det-border);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .detail-hero-card h1 {
        color: var(--det-text) !important;
    }

    .detail-hero-card p {
        color: var(--det-muted) !important;
    }

    .hero-step-badge {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--det-primary-dark);
        text-transform: uppercase;
        background: var(--det-primary-light);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    .order-code-tag {
        background: var(--det-primary-light);
        border: 1px solid var(--det-border);
        padding: 6px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--det-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Cards Disamakan Persis */
    .detail-card {
        background: var(--det-card-bg);
        border: 1px solid var(--det-border);
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .detail-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px dashed var(--det-border);
    }

    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--det-primary-light);
        color: var(--det-primary);
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .section-title {
        font-size: 0.98rem;
        font-weight: 850;
        margin: 0;
        color: var(--det-text);
    }

    .section-subtitle {
        color: var(--det-muted);
        font-size: 0.75rem;
        margin: 0;
    }

    /* Status Box Modern */
    .status-box-modern {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border: 1px solid var(--det-border);
        border-radius: 12px;
        background: #fbfcfb;
        margin-bottom: 16px;
    }

    .status-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .status-icon-badge.success { background: #dff3e7; color: #16734a; }
    .status-icon-badge.warning { background: #fff0d9; color: #98600d; }
    .status-icon-badge.primary { background: #e4efff; color: #285ca6; }
    .status-icon-badge.danger { background: #fde8e7; color: #b52c28; }
    .status-icon-badge.secondary { background: #edf0ee; color: #59645d; }

    /* Stepper Modern */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 14px;
    }

    .step-box {
        padding: 10px;
        border: 1px solid var(--det-border);
        border-radius: 12px;
        background: #fbfcfb;
        position: relative;
    }

    .step-box-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #e2ede5;
        color: var(--det-muted);
        display: grid;
        place-items: center;
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .step-box.is-done {
        background: var(--det-primary-light);
        border-color: #cde2d3;
    }

    .step-box.is-done .step-box-icon {
        background: var(--det-primary);
        color: #ffffff;
    }

    .step-box.is-current {
        background: #ffffff;
        border-color: var(--det-primary);
        box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.15);
    }

    .step-box.is-current .step-box-icon {
        background: var(--det-primary-dark);
        color: #ffffff;
    }

    .step-box-title {
        font-size: 0.76rem;
        font-weight: 800;
        color: var(--det-text);
        line-height: 1.2;
    }

    .step-box-desc {
        font-size: 0.68rem;
        color: var(--det-muted);
        margin-top: 2px;
        line-height: 1.3;
    }

    /* Info Grid Tiles */
    .info-tile {
        height: 100%;
        padding: 10px 12px;
        border: 1px solid var(--det-border);
        border-radius: 10px;
        background: #fbfcfb;
    }

    .info-label {
        color: var(--det-muted);
        font-size: 0.68rem;
        font-weight: 750;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .info-value {
        font-size: 0.84rem;
        font-weight: 750;
        color: var(--det-text);
        overflow-wrap: anywhere;
    }

    /* Item Box & Package Items */
    .item-box {
        padding: 12px;
        border: 1px solid var(--det-border);
        border-radius: 12px;
        background: #fbfcfb;
        margin-bottom: 10px;
    }

    .item-box:last-child {
        margin-bottom: 0;
    }

    .package-item-chip {
        background: #ffffff;
        border: 1px solid var(--det-border);
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 0.75rem;
        color: var(--det-text);
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
    }

    /* Total Panel */
    .total-panel {
        background: var(--det-primary-light);
        border: 1px solid #cde2d3;
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 14px;
    }

    .total-value {
        color: var(--det-primary-dark);
        font-size: 1.25rem;
        font-weight: 900;
    }

    @media (max-width: 991.98px) {
        .steps-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detail-page">
    <div class="container" style="max-width: 960px;">

        {{-- HERO BANNER --}}
        <div class="detail-hero-card">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="hero-step-badge mb-1">
                        CATERING BANGJO
                    </span>
                    <h1 class="fs-4 fw-black mb-1">Detail Pesanan</h1>
                    <p class="mb-0 small">
                        Pantau status pesanan, rincian menu, dan informasi pembayaranmu secara real-time.
                    </p>
                </div>
                <div class="text-md-end">
                    <div class="small text-muted fw-bold mb-1" style="font-size: 0.7rem;">KODE PESANAN</div>
                    <div class="order-code-tag">
                        <i class="bi bi-receipt text-success"></i> {{ $order->order_code }}
                    </div>

                    @if ($order->status === 'Pending' && ! $order->hasActivePayment())
                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="mt-2"
                            onsubmit="return confirm('Batalkan pesanan ini? Pesanan yang dibatalkan tidak bisa diaktifkan lagi.')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 fw-bold">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                    <div class="mt-2">
                        <a href="{{ route('customer.orders.receipt', ['order' => $order->id, 'print' => 1]) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-sm btn-outline-success rounded-3 fw-bold">
                            <i class="bi bi-printer me-1"></i> Cetak Struk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- NOTIFIKASI FLASH --}}
        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3 small py-2">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-3 small py-2">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- STATUS & STEPPER CARD --}}
        <div class="detail-card">
            <div class="section-heading">
                <div class="section-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h2 class="section-title">Status Pesanan</h2>
                    <p class="section-subtitle">Perjalanan dan tahapan proses pesanan Anda.</p>
                </div>
            </div>

            <div class="status-box-modern">
                <div class="status-icon-badge {{ $warnaStatus }}">
                    <i class="bi {{ $ikonStatus }}"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Status Saat Ini</div>
                    <div class="fw-bold text-dark fs-5 mt-1">{{ $judulStatus }}</div>
                    <div class="text-muted small mt-1">{{ $deskripsiStatus }}</div>
                </div>
            </div>

            @if ($pesananDibatalkan)
                <div class="alert alert-danger border-0 rounded-3 small mb-0 py-2">
                    <i class="bi bi-x-circle me-1"></i> Pesanan ini dibatalkan dan tidak melanjutkan proses berikutnya.
                </div>
            @elseif ($tahapPesanan === 0)
                <div class="alert alert-warning border-0 rounded-3 small mb-0 py-2">
                    <i class="bi bi-exclamation-triangle me-1"></i> Status pesanan belum dikenali. Hubungi admin dan sertakan kode pesananmu.
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold small text-dark">Perjalanan Pesanan</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Tahap {{ $tahapPesanan }} dari 4</span>
                </div>

                <div class="steps-grid">
                    @foreach ($langkahStatus as $nomor => $langkah)
                        <div class="step-box {{ $tahapPesanan > $nomor ? 'is-done' : '' }} {{ $tahapPesanan === $nomor ? 'is-current' : '' }}">
                            <div class="step-box-icon">
                                @if ($tahapPesanan > $nomor)
                                    <i class="bi bi-check-lg"></i>
                                @else
                                    <i class="bi {{ $langkah['ikon'] }}"></i>
                                @endif
                            </div>
                            <div class="step-box-title">{{ $nomor }}. {{ $langkah['judul'] }}</div>
                            <div class="step-box-desc">{{ $langkah['deskripsi'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="row g-3">
            {{-- KOLOM KIRI --}}
            <div class="col-lg-7">

                {{-- INFORMASI PESANAN --}}
                <div class="detail-card">
                    <div class="section-heading">
                        <div class="section-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h2 class="section-title">Informasi Pesanan</h2>
                            <p class="section-subtitle">Data pemesan dan pengiriman.</p>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Nama Pemesan</div>
                                <div class="info-value text-truncate" title="{{ $namaCustomer }}">{{ $namaCustomer }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Nomor WhatsApp</div>
                                <div class="info-value">{{ $teleponCustomer }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Tanggal Pemesanan</div>
                                <div class="info-value">
                                    {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-tile">
                                <div class="info-label">Tanggal Dibutuhkan</div>
                                <div class="info-value text-success">
                                    {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') : 'Belum ditentukan' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-tile">
                                <div class="info-label">Metode Penerimaan</div>
                                <div class="info-value">
                                    @if ($metodePengiriman === 'pickup')
                                        <i class="bi bi-shop text-success me-1"></i> Ambil Sendiri (Di Tempat)
                                    @elseif ($metodePengiriman === 'delivery')
                                        <i class="bi bi-truck text-success me-1"></i> Diantar ke Alamat
                                    @else
                                        Belum ditentukan
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($metodePengiriman === 'delivery')
                            <div class="col-12">
                                <div class="info-tile">
                                    <div class="info-label">Alamat Pengiriman</div>
                                    <div class="info-value small fw-normal text-dark">{{ $alamatPengiriman }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($order->notes)
                            <div class="col-12">
                                <div class="info-tile">
                                    <div class="info-label">Catatan Pesanan</div>
                                    <div class="info-value small fw-normal text-muted">"{{ $order->notes }}"</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RINCIAN ITEM --}}
                <div class="detail-card">
                    <div class="section-heading">
                        <div class="section-icon"><i class="bi bi-basket2"></i></div>
                        <div>
                            <h2 class="section-title">Rincian Item</h2>
                            <p class="section-subtitle">Daftar paket dan menu yang dipesan.</p>
                        </div>
                    </div>

                    @if ($order->packages->count())
                        <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Paket Catering</div>

                        @foreach ($order->packages as $orderPackage)
                            <div class="item-box">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2 pb-2 border-bottom border-light">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                            {{ $orderPackage->package?->name ?? 'Paket Catering' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.74rem;">
                                            {{ $orderPackage->quantity }} paket × Rp{{ number_format($orderPackage->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark text-nowrap">
                                        Rp{{ number_format($orderPackage->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>

                                @if ($orderPackage->package?->details?->count())
                                    <div class="text-muted fw-bold mb-2" style="font-size: 0.68rem; text-transform: uppercase;">Isi Paket / Menu:</div>
                                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                                        @foreach ($orderPackage->package->details as $packageDetail)
                                            @php
                                                $replacement = $order->customRequest?->details
                                                    ?->first(function ($detail) use ($packageDetail) {
                                                        return $detail->customization_type === 'replacement'
                                                            && (int) $detail->replaces_menu_id === (int) $packageDetail->menus_id;
                                                    });

                                                $menuName = $replacement?->menu?->name
                                                    ?? $packageDetail->menu?->name
                                                    ?? 'Menu tidak ditemukan';

                                                $quantity = (int) $packageDetail->quantity
                                                    * (int) $orderPackage->quantity;
                                            @endphp

                                            <div class="col">
                                                <div class="package-item-chip">
                                                    <div class="min-w-0">
                                                        @if ($replacement)
                                                            <span class="text-muted text-decoration-line-through d-block" style="font-size: 0.65rem;">
                                                                {{ $packageDetail->menu?->name ?? 'Menu asli' }}
                                                            </span>
                                                            <span class="text-success fw-bold text-truncate d-block">{{ $menuName }}</span>
                                                        @else
                                                            <span class="fw-semibold text-dark text-truncate d-block">{{ $menuName }}</span>
                                                        @endif
                                                        <span class="text-muted" style="font-size: 0.65rem;">({{ $quantity }} porsi)</span>
                                                    </div>

                                                    @if ($replacement)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle flex-shrink-0" style="font-size: 0.6rem;">Ganti</span>
                                                    @endif
                                                </div>
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
                        <div class="text-uppercase text-muted fw-bold mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            {{ $order->packages->isNotEmpty() ? 'Menu Tambahan' : 'Menu Custom' }}
                        </div>

                        @foreach ($menuTambahan as $detail)
                            <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom border-light small">
                                <div>
                                    <div class="fw-bold text-dark">{{ $detail->menu?->name ?? 'Menu Catering' }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $detail->quantity }} porsi × Rp{{ number_format($detail->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-bold text-dark text-nowrap">
                                    Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>

            {{-- KOLOM KANAN: STATUS PEMBAYARAN & BIAYA --}}
            <div class="col-lg-5">

                {{-- STATUS PEMBAYARAN --}}
                <div class="detail-card">
                    <div class="section-heading">
                        <div class="section-icon"><i class="bi bi-credit-card"></i></div>
                        <div>
                            <h2 class="section-title">Status Pembayaran</h2>
                            <p class="section-subtitle">Verifikasi dan riwayat bayar.</p>
                        </div>
                    </div>

                    @if ($order->payments->isNotEmpty())
                        <div class="small mb-2">
                            <div class="d-flex justify-content-between"><span class="text-muted">Terverifikasi</span><b class="text-success">Rp{{ number_format($order->paidAmount(), 0, ',', '.') }}</b></div>
                            @if ($order->pendingAmount() > 0)
                                <div class="d-flex justify-content-between"><span class="text-muted">Menunggu verifikasi</span><b style="color:#8a5a00;">Rp{{ number_format($order->pendingAmount(), 0, ',', '.') }}</b></div>
                            @endif
                            <div class="d-flex justify-content-between"><span class="text-muted">Belum dibayar</span><b class="text-danger">Rp{{ number_format(max(0, $order->remainingAmount() - $order->pendingAmount()), 0, ',', '.') }}</b></div>
                        </div>
                    @endif

                    @if (!$order->payment)
                        <div class="alert alert-warning border-0 rounded-3 small mb-2 py-2">
                            <div class="fw-bold mb-1"><i class="bi bi-clock-history me-1"></i>Belum Dibayar</div>
                            <div>
                                {{ $pengirimanTerkonfirmasi
                                    ? 'Pesanan siap dilanjutkan ke pembayaran.'
                                    : 'Tunggu konfirmasi pengiriman dari admin sebelum membayar.' }}
                            </div>
                        </div>

                        @if ($pengirimanTerkonfirmasi)
                            <a href="{{ route('customer.payment.create', $order->id) }}"
                               class="btn btn-success w-100 rounded-3 py-2 fw-bold small" style="background: var(--det-primary); border-color: var(--det-primary);">
                                <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                            </a>
                        @endif

                    @elseif ($statusPembayaran === 'Menunggu Verifikasi')
                        <div class="alert alert-warning border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-hourglass-split me-1"></i> Bukti pembayaran sudah dikirim dan menunggu verifikasi admin.
                        </div>

                    @elseif ($statusPembayaran === 'Dibayar')
                        <div class="alert alert-success border-0 rounded-3 small mb-0 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Pembayaran sudah diverifikasi lunas.
                        </div>

                    @elseif ($statusPembayaran === 'Ditolak')
                        <div class="alert alert-danger border-0 rounded-3 small mb-2 py-2">
                            <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i>Pembayaran Ditolak</div>
                            @if ($order->payment->notes)
                                <div style="font-size: 0.75rem;">Alasan: {{ $order->payment->notes }}</div>
                            @endif
                        </div>

                        @if ($pengirimanTerkonfirmasi)
                            <a href="{{ route('customer.payment.create', $order->id) }}"
                               class="btn btn-warning w-100 rounded-3 fw-bold small py-2">
                                <i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang Pembayaran
                            </a>
                        @endif

                    @elseif ($statusPembayaran === 'Sebagian')
                        <div class="alert alert-info border-0 rounded-3 small mb-2 py-2">
                            <div class="fw-bold mb-1"><i class="bi bi-pie-chart-fill me-1"></i>DP sudah diterima</div>
                            <div>Sudah dibayar Rp{{ number_format($order->paidAmount(), 0, ',', '.') }}. Sisa tagihan <b>Rp{{ number_format($order->remainingAmount(), 0, ',', '.') }}</b>.</div>
                        </div>
                        <a href="{{ route('customer.payment.create', $order->id) }}"
                        class="btn btn-success w-100 rounded-3 py-2 fw-bold small" style="background: var(--det-primary); border-color: var(--det-primary);">
                            <i class="bi bi-credit-card me-1"></i> Bayar Pelunasan
                        </a>

                    @else
                        <div class="alert alert-secondary border-0 rounded-3 small mb-0 py-2">
                            Status pembayaran: {{ $statusPembayaran ?? 'Belum tersedia' }}
                        </div>
                    @endif
                </div>

                {{-- RINCIAN BIAYA --}}
                <div class="detail-card">
                    <div class="section-heading">
                        <div class="section-icon"><i class="bi bi-calculator"></i></div>
                        <div>
                            <h2 class="section-title">Rincian Biaya</h2>
                            <p class="section-subtitle">Subtotal dan estimasi total.</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Subtotal Pesanan</span>
                        <span class="fw-bold text-dark">Rp{{ number_format($subtotalPesanan, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Biaya Pengiriman</span>
                        <span class="fw-bold text-success">
                            @if ($metodePengiriman === 'pickup')
                                Gratis (Ambil Sendiri)
                            @elseif ($metodePengiriman === 'delivery' && !$pengirimanTerkonfirmasi)
                                Menunggu Konfirmasi
                            @else
                                Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>

                    @if ($metodePengiriman === 'delivery' && $order->delivery_distance !== null)
                        <div class="text-muted small text-end mb-2" style="font-size: 0.72rem;">
                            Jarak pengiriman: {{ $order->delivery_distance }} km
                        </div>
                    @elseif ($metodePengiriman === 'delivery')
                        <div class="alert alert-info border-0 rounded-3 small mt-2 mb-2 py-1 px-2" style="font-size: 0.72rem;">
                            <i class="bi bi-info-circle me-1"></i> Admin akan mengonfirmasi jarak & biaya pengiriman.
                        </div>
                    @endif

                    <div class="total-panel">
                        <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem;">TOTAL PEMBAYARAN</div>
                        <div class="total-value">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- BANTUAN --}}
                <div class="detail-card mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="section-icon"><i class="bi bi-headset"></i></div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">Butuh Bantuan?</div>
                            <div class="text-muted small" style="font-size: 0.74rem;">Hubungi admin dan sebutkan kode pesanan Anda.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection