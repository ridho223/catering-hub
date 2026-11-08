@extends('layouts.app')

@section('title', 'Checkout Pesanan')

@section('content')
{{-- Tambahkan CDN Leaflet CSS di sini --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    :root {
        --co-primary: #198754;
        --co-primary-dark: #145c38;
        --co-primary-light: #eaf5ed;
        --co-text: #212529;
        --co-muted: #6c757d;
        --co-border: #e2e8e3;
        --co-bg: #f8faf8;
        --co-card-bg: #ffffff;
    }

    .checkout-page {
        background-color: var(--co-bg);
        min-height: 85vh;
        padding: 18px 0 45px;
        color: var(--co-text);
    }

    .checkout-page a {
        text-decoration: none;
    }

    .checkout-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--co-muted);
        font-size: 0.82rem;
        font-weight: 700;
        transition: color 0.2s ease;
    }

    .checkout-back-btn:hover {
        color: var(--co-primary-dark);
    }

    /* Hero Banner Konsisten dengan Katalog & Pesanan Saya */
    .checkout-hero-card {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border: 1px solid var(--co-border);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .checkout-hero-card h1 {
        color: var(--co-text) !important;
    }

    .checkout-hero-card p {
        color: var(--co-muted) !important;
    }

    .hero-step-badge {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: var(--co-primary-dark);
        text-transform: uppercase;
        background: var(--co-primary-light);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Progress Stepper */
    .checkout-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 420px;
        margin: 0 auto 18px;
    }

    .checkout-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        color: var(--co-muted);
    }

    .checkout-step-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2ede5;
        color: var(--co-muted);
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .checkout-step.completed .checkout-step-icon {
        background: var(--co-primary);
        color: #ffffff;
    }

    .checkout-step.current .checkout-step-icon {
        background: var(--co-primary-dark);
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2);
    }

    .checkout-step.current {
        color: var(--co-primary-dark);
        font-weight: 800;
    }

    .checkout-step-label {
        font-size: 0.72rem;
        font-weight: 700;
    }

    .checkout-progress-line {
        height: 2px;
        flex: 1;
        background: #e2ede5;
        margin: 0 10px 14px;
    }

    .checkout-progress-line.active {
        background: var(--co-primary);
    }

    /* Cards Layout Compact Disamakan */
    .checkout-card {
        background: var(--co-card-bg);
        border: 1px solid var(--co-border);
        border-radius: 16px;
        padding: 16px 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .checkout-card:hover {
        border-color: #cde2d3;
        box-shadow: 0 10px 22px rgba(20, 92, 56, 0.08);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--co-border);
    }

    .section-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--co-primary-light);
        color: var(--co-primary);
        display: grid;
        place-items: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    .section-title {
        font-size: 0.92rem;
        font-weight: 850;
        margin: 0;
        color: var(--co-text);
    }

    /* Form Controls Compact */
    .checkout-page .form-control {
        border: 1px solid var(--co-border);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.83rem;
        background-color: #fbfcfb;
        transition: all 0.2s ease;
    }

    .checkout-page .form-control:focus {
        border-color: var(--co-primary);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
    }

    /* Delivery Option Radio Cards */
    .delivery-option {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        cursor: pointer;
        border: 1px solid var(--co-border);
        border-radius: 10px;
        padding: 10px 12px;
        background: #fbfcfb;
        transition: all 0.2s ease;
    }

    .delivery-radio {
        accent-color: var(--co-primary);
    }

    .delivery-option:has(.delivery-radio:checked) {
        border-color: var(--co-primary);
        background: var(--co-primary-light);
        box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.12);
    }

    .delivery-symbol {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #ffffff;
        color: var(--co-primary-dark);
        display: grid;
        place-items: center;
        font-size: 1rem;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .delivery-price {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--co-primary-dark);
    }

    /* Quick Date Shortcuts */
    .quick-date-wrapper {
        display: flex;
        gap: 4px;
        margin-top: 6px;
    }

    .quick-date-btn {
        background: #ffffff;
        border: 1px solid var(--co-border);
        color: var(--co-text);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quick-date-btn:hover,
    .quick-date-btn.active {
        background: var(--co-primary-light);
        border-color: var(--co-primary);
        color: var(--co-primary-dark);
    }

    .delivery-info-box {
        background: #eef6ff;
        color: #2b5677;
        border: 1px solid #d4e7f8;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.76rem;
        line-height: 1.35;
    }

    /* Summary Sidebar Sticky */
    .summary-card {
        position: sticky;
        top: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        padding: 6px 0;
        border-bottom: 1px dashed var(--co-border);
        font-size: 0.8rem;
    }

    .summary-item:last-child {
        border-bottom: 0;
    }

    .total-panel {
        background: var(--co-primary-light);
        border: 1px solid #cde2d3;
        border-radius: 10px;
        padding: 10px 12px;
    }

    .total-value {
        color: var(--co-primary-dark);
        font-size: 1.35rem;
        font-weight: 900;
    }

    .btn-submit-order {
        background: var(--co-primary);
        border: 1px solid var(--co-primary);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 10px 14px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-submit-order:hover:not(:disabled) {
        background: var(--co-primary-dark);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(20, 92, 56, 0.2);
    }

    .edit-cart-link {
        font-size: 0.75rem;
        font-weight: 750;
        color: var(--co-primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        background: var(--co-primary-light);
        transition: background 0.2s ease;
    }

    .edit-cart-link:hover {
        background: #d8ebd9;
        color: var(--co-primary-dark);
    }

    .summary-trust-badge {
        background: #fffdf5;
        border: 1px solid #f2e7c4;
        border-radius: 10px;
        padding: 9px 11px;
        font-size: 0.74rem;
        color: #6c5a20;
    }

    @media (max-width: 991.98px) {
        .summary-card { position: static; }
    }
</style>

<div class="checkout-page">
    <div class="container" style="max-width: 960px;">

        {{-- HERO BANNER ATAS --}}
        <div class="checkout-hero-card">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <span class="hero-step-badge mb-1">
                        LANGKAH 2 DARI 3
                    </span>
                    <h1 class="fs-4 fw-black mb-1">Checkout Pesanan</h1>
                    <p class="mb-0 small">
                        Lengkapi detail acara dan pengiriman untuk memproses pesanan catering Anda.
                    </p>
                </div>
                <div class="text-md-end opacity-75 d-none d-md-block text-success">
                    <i class="bi bi-cart-check fs-1"></i>
                </div>
            </div>
        </div>

        {{-- PROGRESS STEPPER --}}
        <div class="checkout-progress">
            <div class="checkout-step completed">
                <div class="checkout-step-icon"><i class="bi bi-check-lg"></i></div>
                <span class="checkout-step-label">Keranjang</span>
            </div>
            <div class="checkout-progress-line active"></div>
            <div class="checkout-step current">
                <div class="checkout-step-icon">2</div>
                <span class="checkout-step-label">Checkout</span>
            </div>
            <div class="checkout-progress-line"></div>
            <div class="checkout-step">
                <div class="checkout-step-icon">3</div>
                <span class="checkout-step-label">Selesai</span>
            </div>
        </div>

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-2 small py-2">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-2 small py-2">
                <div class="fw-bold mb-1">Periksa kembali data Anda:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                {{-- KOLOM KIRI: DATA PEMESAN & PENGIRIMAN COMPACT --}}
                <div class="col-lg-7">

                    {{-- 1. DATA PEMESAN --}}
                    <div class="checkout-card mb-2">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            <div>
                                <h2 class="section-title">Data Pemesan</h2>
                                <p class="text-muted small mb-0" style="font-size: 0.72rem;">Informasi kontak utama.</p>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Nama Lengkap</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $customer->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Nama pemesan"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Nomor WhatsApp</label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $customer->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="08xxxxxxxxxx"
                                       required>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Email</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ Auth::guard('customer')->user()->email }}"
                                       readonly
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="nama@email.com"
                                       required>
                            </div>
                        </div>
                    </div>

                    {{-- 2. PENGIRIMAN & PENERIMAAN --}}
                    <div class="checkout-card">
                        <div class="section-heading">
                            <div class="section-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <h2 class="section-title">Pengambilan & Pengiriman</h2>
                                <p class="text-muted small mb-0" style="font-size: 0.72rem;">Opsi pengantaran dan lokasi acara.</p>
                            </div>
                        </div>

                        {{-- METODE PENERIMAAN HORIZONTAL --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Metode Penerimaan</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="pickup" class="delivery-option h-100">
                                        <input class="form-check-input delivery-radio"
                                               type="radio"
                                               name="delivery_method"
                                               id="pickup"
                                               value="pickup"
                                               {{ old('delivery_method', 'pickup') === 'pickup' ? 'checked' : '' }}
                                               required>
                                        <div class="delivery-symbol">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Ambil Sendiri</div>
                                            <span class="delivery-price">Gratis (Di Tempat)</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <label for="delivery" class="delivery-option h-100">
                                        <input class="form-check-input delivery-radio"
                                               type="radio"
                                               name="delivery_method"
                                               id="delivery"
                                               value="delivery"
                                               {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}
                                               required>
                                        <div class="delivery-symbol">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Diantar Kurir</div>
                                            <span class="delivery-price">Gratis s/d 5 km</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- INTERACTIVE MAP WRAPPER (Hanya muncul jika pilih Diantar Kurir) --}}
                        <div id="map-section-wrapper" class="mb-3 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.78rem;">
                                    <i class="bi bi-geo-alt-fill text-success me-1"></i> Pilih Titik Lokasi Peta
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.7rem;" id="btn-my-location">
                                    <i class="bi bi-crosshair"></i> Lokasi Saya
                                </button>
                            </div>
                            <p class="text-muted small mb-2" style="font-size: 0.7rem;">Geser pin atau klik pada peta untuk menentukan lokasi akurat pengantaran.</p>
                            
                            {{-- Kotak Peta --}}
                            <div id="checkout-map" style="height: 220px; width: 100%; border-radius: 12px; border: 1px solid var(--co-border); z-index: 1;"></div>
                            
                            {{-- Hidden input untuk simpan Koordinat GPS --}}
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                        </div>

                        {{-- ALAMAT ACARA --}}
                        <div class="mb-2">
                            <label for="address" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Alamat Lengkap / Patokan</label>
                            <textarea name="address"
                                      id="address"
                                      rows="2"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, patokan lokasi"
                                      required>{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="delivery-info-box mb-2">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Ongkir di atas 5 km dikonfirmasi oleh admin berdasarkan titik peta Anda.
                        </div>

                        {{-- TANGGAL & CATATAN --}}
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="delivery_date" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Tgl Dibutuhkan</label>
                                <input type="date"
                                       name="delivery_date"
                                       id="delivery_date"
                                       value="{{ old('delivery_date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="form-control @error('delivery_date') is-invalid @enderror"
                                       required>

                                <div class="quick-date-wrapper">
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate(1, this)">Besok</button>
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate(3, this)">+3 Hari</button>
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate(7, this)">+1 Mgg</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="notes" class="form-label fw-bold text-dark mb-1" style="font-size: 0.78rem;">Catatan <span class="text-muted fw-normal">(Opsional)</span></label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="2"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Contoh: Sambal dipisah, jam 10 pagi">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: RINGKASAN PESANAN (SIDEBAR STICKY) --}}
                <div class="col-lg-5">
                    <aside class="checkout-card summary-card">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <h2 class="section-title">Ringkasan Pesanan</h2>
                                <p class="text-muted small mb-0" style="font-size: 0.72rem;">Item pilihan keranjang</p>
                            </div>

                            {{-- LINK UBAH KERANJANG DI ATAS --}}
                            <a href="{{ route('cart.index') }}" class="edit-cart-link" title="Ubah isi keranjang">
                                <i class="bi bi-pencil-square"></i> Ubah
                            </a>
                        </div>

                        {{-- LIST ITEM --}}
                        <div class="summary-items mb-2" style="max-height: 180px; overflow-y: auto; padding-right: 4px;">
                            @forelse ($cart as $item)
                                <div class="summary-item">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold text-dark text-truncate" title="{{ $item['name'] }}">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.74rem;">
                                            {{ $item['quantity'] }} Porsi
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark text-nowrap ms-2">
                                        Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <i class="bi bi-cart-x fs-2 text-muted"></i>
                                    <p class="text-muted small mb-0 mt-1">Keranjang kamu kosong.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Subtotal Item</span>
                            <span class="fw-bold text-dark">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="fw-bold text-success" id="delivery-label">Belum dikonfirmasi</span>
                        </div>

                        <div class="total-panel mb-2">
                            <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem;">TOTAL ESTIMASI</div>
                            <div class="total-value">
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                Ongkir tambahan (jika ada) dikonfirmasi oleh admin.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit-order w-100 mb-2" @disabled(count($cart) === 0)>
                            <i class="bi bi-check-circle me-1"></i> Buat Pesanan
                        </button>

                        {{-- NOTIFIKASI PEMERIKSAAN PESANAN DI BAWAH SIDEBAR --}}
                        <div class="summary-trust-badge d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check fs-5 text-warning flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold">Pemeriksaan Pesanan</div>
                                <div style="font-size: 0.7rem; opacity: 0.85;">Periksa kembali pesanan, tanggal & alamat lokasi sebelum membuat pesanan.</div>
                            </div>
                        </div>
                    </aside>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Leaflet JS Library --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="delivery_method"]');
    const deliveryLabel = document.getElementById('delivery-label');

    function updateDeliveryLabel() {
        const selected = document.querySelector('input[name="delivery_method"]:checked');

        if (!selected) {
            deliveryLabel.textContent = 'Belum dikonfirmasi';
            deliveryLabel.className = 'fw-bold text-muted';
            return;
        }

        if (selected.value === 'pickup') {
            deliveryLabel.textContent = 'Gratis (Ambil Sendiri)';
            deliveryLabel.className = 'fw-bold text-success';
        } else {
            deliveryLabel.textContent = 'Dikonfirmasi Admin';
            deliveryLabel.className = 'fw-bold text-warning';
        }
    }

    radios.forEach(radio => radio.addEventListener('change', updateDeliveryLabel));
    updateDeliveryLabel();
});

// Shortcut Pilih Tanggal Cepat
function setQuickDate(daysToAdd, element) {
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + daysToAdd);

    const year = targetDate.getFullYear();
    const month = String(targetDate.getMonth() + 1).padStart(2, '0');
    const day = String(targetDate.getDate()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}`;
    document.getElementById('delivery_date').value = formattedDate;

    document.querySelectorAll('.quick-date-btn').forEach(btn => btn.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    }
}

// MAPS (Leaflet & OpenStreetMap)
let map = null;
let marker = null;

document.addEventListener('DOMContentLoaded', function () {
    const radioPickup = document.getElementById('pickup');
    const radioDelivery = document.getElementById('delivery');
    const mapWrapper = document.getElementById('map-section-wrapper');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('address');
    const btnMyLocation = document.getElementById('btn-my-location');

    // Koordinat default (Pusat Kota Malang, Jawa Timur)
    const defaultLat = -7.9784695;
    const defaultLng = 112.5617424;

    function toggleMapVisibility() {
        if (radioDelivery.checked) {
            mapWrapper.classList.remove('d-none');
            // Inisialisasi peta jika belum pernah dibuat
            if (!map) {
                initMap();
            } else {
                map.invalidateSize();
            }
        } else {
            mapWrapper.classList.add('d-none');
        }
    }

    if (radioPickup && radioDelivery) {
        radioPickup.addEventListener('change', toggleMapVisibility);
        radioDelivery.addEventListener('change', toggleMapVisibility);
        toggleMapVisibility();
    }

    function initMap() {
        const initialLat = latInput.value ? parseFloat(latInput.value) : defaultLat;
        const initialLng = lngInput.value ? parseFloat(lngInput.value) : defaultLng;

        map = L.map('checkout-map').setView([initialLat, initialLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateLocationData(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateLocationData(e.latlng.lat, e.latlng.lng);
        });

        if (!latInput.value) {
            latInput.value = initialLat;
            lngInput.value = initialLng;
        }
    }

    if (btnMyLocation) {
        btnMyLocation.addEventListener('click', function() {
            if (navigator.geolocation) {
                btnMyLocation.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari...';
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (map && marker) {
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateLocationData(lat, lng);
                    }

                    btnMyLocation.innerHTML = '<i class="bi bi-crosshair"></i> Lokasi Saya';
                }, function(error) {
                    alert('Gagal mendeteksi lokasi GPS Anda. Pastikan izin lokasi aktif.');
                    btnMyLocation.innerHTML = '<i class="bi bi-crosshair"></i> Lokasi Saya';
                });
            } else {
                alert('Browser Anda tidak mendukung fitur geolokasi.');
            }
        });
    }

    function updateLocationData(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
        .then(data => {
            if (!data || !data.display_name) { return; }

            if (!addressInput.value.trim()) {
                addressInput.value = data.display_name;
                return;
            }

            bjConfirm({
                title: 'Perbarui alamat?',
                message: 'Alamat teks akan diganti sesuai titik peta yang kamu pilih.',
                okText: 'Ya, perbarui',
                type: 'warning'
            }).then(function (ok) {
                if (ok) { addressInput.value = data.display_name; }
            });
        })
            .catch(err => console.log('Gagal mengambil nama alamat otomatis', err));
    }
});
</script>
@endsection