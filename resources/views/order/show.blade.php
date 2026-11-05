@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
@php
    use Illuminate\Support\Carbon;

    $customer  = $order->customer;
    $isPickup  = ($order->delivery_method ?? 'pickup') === 'pickup';
    $code      = $order->order_code ?? ('#' . $order->id);

    // ---- alur status ----
    $flow = [
        ['Pending',             'Menunggu bayar', 'bi-hourglass-split'],
        ['Diproses',            'Diproses',       'bi-fire'],
        ['Siap_diambil_atau_diantar', $isPickup ? 'Siap diambil' : 'Siap diantar', 'bi-bag-check'],
        ['Selesai',             'Selesai',        'bi-check2-all'],
    ];
    $currentIdx = collect($flow)->search(fn ($s) => $s[0] === $order->status);   // false jika status tidak dikenal
    $chipClass  = ['Pending' => 'warn', 'Diproses' => 'info', 'Siap_diambil_atau_diantar' => 'ready', 'Selesai' => 'ok'];
    $statusCls   = $chipClass[$order->status] ?? 'off';
    $statusLabel = $currentIdx !== false ? $flow[$currentIdx][1] : $order->status;

    // ---- isi paket per pesanan paket (menu bawaan vs menu aktual) ----
    $actualDetails = $order->details->where('source', 'package')->values();
    $cursor = 0;
    $packageItems = [];
    foreach ($order->packages as $op) {
        $rows = collect();
        foreach (($op->package?->details ?? collect()) as $component) {
            $actual = $actualDetails->get($cursor++);
            if ($actual) {
                $rows->push([
                    'original' => $component->menu,
                    'actual'   => $actual->menu,
                    'quantity' => $actual->quantity,
                ]);
            }
        }
        $packageItems[$op->id] = $rows;
    }

    // ---- menu tambahan / custom ----
    $extraDetails = $order->packages->isNotEmpty()
        ? $order->details->where('source', 'custom')
        : $order->details;
    $extraLabel = $order->packages->isNotEmpty() ? 'Menu tambahan' : 'Menu custom';

    // ---- biaya ----
    $subtotalPaket = $order->packages->sum('subtotal');
    $subtotalMenu  = $extraDetails->sum('subtotal');

    // ---- pembayaran ----
    $payment       = $order->payment;
    $paidAmount    = $order->paidAmount();
    $orderTotal    = (float) $order->total;
    $remaining     = $order->remainingAmount();
    $paymentStatus = $order->paymentState();
    $payClass = match ($paymentStatus) {
        'Dibayar'  => 'ok',
        'Sebagian' => 'info',
        'Ditolak'  => 'bad',
        default    => 'warn',
    };

    // ---- kontak pelanggan ----
    $digits = preg_replace('/\D/', '', $customer->phone ?? '');
    $wa = $digits ? (str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits) : null;

    $needShipping = !$isPickup && $order->delivery_distance === null;
@endphp

<style>
    .osh { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .osh-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .osh-card:hover { border-color: #cde2d3; box-shadow: 0 8px 24px rgba(25, 92, 56, 0.05); }
    .osh-card > h5, .osh-head-title { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .osh-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .osh-cardhead { display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; margin-bottom:.9rem; }
    .osh-cardhead h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .osh-cardhead p { margin:.1rem 0 0; color:var(--mute); font-size:.85rem; }

    /* header */
    .osh-head { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
    .osh-code { font-size:1.4rem; font-weight:800; color:var(--ink); line-height:1.2; }
    .osh-meta { color:var(--mute); font-size:.88rem; margin-top:.25rem; display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
    .osh-actions { display:flex; gap:.5rem; flex-wrap:wrap; }

    .osh-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .osh-chip::before { content:""; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .osh-chip--warn { background:#fff1d6; color:#8a5a00; }
    .osh-chip--info { background:#e2f0fb; color:#0b5a8a; }
    .osh-chip--ready { background:#e8e6fb; color:#4a3fb0; }
    .osh-chip--ok { background:#e3f4ea; color:#146c43; }
    .osh-chip--bad { background:#fde8e8; color:#a52834; }
    .osh-chip--off { background:#eceff1; color:#5f6b72; }

    /* alur status */
    .osh-flow { display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; margin-top:1.1rem; padding-top:1.1rem; border-top:1px solid var(--line); }
    .osh-step { position:relative; display:flex; flex-direction:column; align-items:center; text-align:center; gap:.35rem; font-size:.85rem; color:var(--mute); }
    .osh-step::before { content:""; position:absolute; top:17px; left:-50%; width:100%; height:3px; background:var(--line); z-index:0; }
    .osh-step:first-child::before { display:none; }
    .osh-ic { position:relative; z-index:1; width:36px; height:36px; border-radius:50%; background:#fff; border:2px solid var(--line); display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .osh-step.done::before, .osh-step.now::before { background:var(--brand); }
    .osh-step.done .osh-ic { background:var(--brand); border-color:var(--brand); color:#fff; }
    .osh-step.now .osh-ic { border-color:var(--brand); color:var(--brand); box-shadow:0 0 0 4px rgba(25,135,84,.15); }
    .osh-step.now, .osh-step.done { color:var(--ink); font-weight:600; }

    /* rincian */
    .osh-pkg { border:1px solid var(--line); border-radius:14px; padding:.9rem 1rem; margin-bottom:.75rem; background: #fbfcfb; }
    .osh-pkg-top { display:flex; justify-content:space-between; gap:1rem; }
    .osh-pkg-top b { color:var(--ink); }
    .osh-pkg-top small { color:var(--mute); display:block; }
    .osh-pkg-sub { font-weight:700; color:var(--ink); white-space:nowrap; }
    .osh-pkg details { margin-top:.6rem; }
    .osh-pkg summary { cursor:pointer; color:var(--brand); font-size:.86rem; font-weight:600; list-style:none; }
    .osh-pkg summary::-webkit-details-marker { display:none; }
    .osh-pkg summary::before { content:"\F282"; font-family:"bootstrap-icons"; margin-right:.35rem; font-size:.75rem; display:inline-block; transition:transform .15s; }
    .osh-pkg details[open] summary::before { transform:rotate(180deg); }
    .osh-menus { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:.4rem; margin-top:.6rem; }
    .osh-menu { background:#fff; border: 1px solid var(--line); border-radius:10px; padding:.45rem .7rem; font-size:.88rem; }
    .osh-menu b { display:block; color:var(--ink); font-weight:600; }
    .osh-menu span { color:var(--mute); font-size:.8rem; }
    .osh-menu .swap { color:#146c43; display:block; margin-top:.15rem; }
    .osh-sectitle { font-weight:700; color:var(--ink); margin:1.1rem 0 .6rem; font-size:.95rem; }

    .osh-table { width:100%; border-collapse:collapse; font-size:.9rem; }
    .osh-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.8rem; padding:.5rem .75rem; text-align:left; }
    .osh-table td { padding:.55rem .75rem; border-top:1px solid var(--line); }
    .osh-note { background:#fff8e6; border-radius:12px; padding:.7rem .9rem; color:#6b5200; font-size:.9rem; margin-top:1rem; border: 1px solid #fdecc8; }
    .osh-note b { display:block; margin-bottom:.1rem; }

    /* info list */
    .osh-kv { display:flex; justify-content:space-between; gap:1rem; padding:.45rem 0; border-top:1px solid var(--line); font-size:.9rem; }
    .osh-kv:first-of-type { border-top:0; }
    .osh-kv span { color:var(--mute); }
    .osh-kv b { color:var(--ink); font-weight:600; text-align:right; }

    .osh-cust { display:flex; gap:.8rem; align-items:center; margin-bottom:.8rem; }
    .osh-av { width:46px; height:46px; border-radius:50%; background:var(--brand); color:#fff; font-weight:700; font-size:1.1rem; display:flex; align-items:center; justify-content:center; flex:none; }
    .osh-cust b { color:var(--ink); display:block; }
    .osh-cust small { color:var(--mute); }
    .osh-addr { background:var(--soft); border-radius:12px; padding:.6rem .8rem; font-size:.88rem; color:var(--ink); display:flex; gap:.5rem; border:1px solid var(--line); }
    .osh-addr i { color:var(--mute); margin-top:.15rem; }

    .osh-warn { background:#fff8e6; color:#7a5a00; border-radius:10px; padding:.55rem .75rem; font-size:.85rem; margin-top:.75rem; border:1px solid #fdecc8; }

    /* biaya & pembayaran */
    .osh-total { display:flex; justify-content:space-between; align-items:baseline; border-top:1px dashed #cfd8d2; margin-top:.5rem; padding-top:.8rem; }
    .osh-total strong { font-size:1.55rem; color:var(--brand); }
    .osh-pay-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:.6rem; }
    .osh-pay-box { background:var(--soft); border-radius:12px; padding:.7rem .9rem; border:1px solid var(--line); }
    .osh-pay-box small { color:var(--mute); display:block; }
    .osh-pay-box strong { font-size:1.05rem; color:var(--ink); }
    .osh-foot { display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--line); }
    .osh-foot span { color:var(--mute); font-size:.85rem; flex:1 1 220px; }

    /* Modern Modal Styling */
    .modal-content.osh-modal {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .osh-modal-header {
        background: linear-gradient(135deg, #eaf5ed 0%, #ffffff 100%);
        border-bottom: 1px solid var(--line);
        padding: 1.25rem 1.5rem;
    }
    .osh-modal-body {
        padding: 1.5rem;
    }
    .osh-modal-footer {
        background: #fbfcfb;
        border-top: 1px solid var(--line);
        padding: 1rem 1.5rem;
    }
    .osh-form-group {
        margin-bottom: 1.15rem;
    }
    .osh-form-label {
        font-weight: 700;
        color: var(--ink);
        font-size: 0.88rem;
        margin-bottom: 0.4rem;
        display: block;
    }
    .osh-form-control, .osh-form-select {
        border-radius: 12px;
        border: 1px solid var(--line);
        padding: 0.65rem 0.9rem;
        font-size: 0.9rem;
        background-color: #fbfcfb;
        transition: all 0.2s ease;
    }
    .osh-form-control:focus, .osh-form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.12);
        background-color: #fff;
    }

    @media (max-width:575px){
        .osh-flow { grid-template-columns:repeat(2,1fr); row-gap:1rem; }
        .osh-step:nth-child(3)::before { display:none; }
    }
</style>

<div class="container-fluid pb-3 osh">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- HEADER + ALUR STATUS --}}
    <section class="osh-card">
        <div class="osh-head">
            <div>
                <div class="osh-code">{{ $code }}</div>
                <div class="osh-meta">
                    <span class="osh-chip osh-chip--{{ $statusCls }}">{{ $statusLabel }}</span>
                    <span class="osh-chip osh-chip--{{ $payClass }}"><i class="bi bi-credit-card"></i> {{ $paymentStatus }}</span>
                    <span>Dipesan {{ Carbon::parse($order->order_date)->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="osh-actions">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-3 px-3"><i class="bi bi-arrow-left me-1"></i> Daftar</a>
                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-success rounded-3 px-3"><i class="bi bi-pencil-square me-1"></i> Edit pesanan</a>
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                      onsubmit="return confirm('Hapus pesanan {{ e($code) }}? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3" title="Hapus pesanan"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>

        @if ($currentIdx !== false)
            <div class="osh-flow" aria-label="Alur status pesanan">
                @foreach ($flow as $i => [$value, $label, $icon])
                    @php $state = $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'now' : 'next'); @endphp
                    <div class="osh-step {{ $state }}">
                        <span class="osh-ic"><i class="bi {{ $state === 'done' ? 'bi-check-lg' : $icon }}"></i></span>
                        {{ $label }}
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="row g-4">

        {{-- ================= KIRI ================= --}}
        <div class="col-xl-8">

            {{-- RINCIAN --}}
            <section class="osh-card">
                <div class="osh-cardhead">
                    <div>
                        <h5>Rincian pesanan</h5>
                        <p>Paket, isi paket, dan menu tambahan.</p>
                    </div>
                </div>

                @foreach ($order->packages as $orderPackage)
                    @php $menus = $packageItems[$orderPackage->id] ?? collect(); @endphp
                    <div class="osh-pkg">
                        <div class="osh-pkg-top">
                            <div>
                                <b>{{ $orderPackage->package?->name ?? 'Paket catering' }}</b>
                                <small>{{ $orderPackage->quantity }} paket × Rp{{ number_format($orderPackage->price, 0, ',', '.') }}</small>
                            </div>
                            <div class="osh-pkg-sub">Rp{{ number_format($orderPackage->subtotal, 0, ',', '.') }}</div>
                        </div>

                        @if ($menus->isNotEmpty())
                            <details>
                                <summary>Isi paket ({{ $menus->count() }} menu)</summary>
                                <div class="osh-menus">
                                    @foreach ($menus as $item)
                                        @php
                                            $orig = $item['original'];
                                            $act  = $item['actual'];
                                            $swapped = $orig && $act && (int) $orig->id !== (int) $act->id;
                                        @endphp
                                        <div class="osh-menu">
                                            <b>{{ $act?->name ?? 'Menu tidak ditemukan' }}</b>
                                            <span>{{ $item['quantity'] }} porsi</span>
                                            @if ($swapped)
                                                <span class="swap"><i class="bi bi-arrow-repeat me-1"></i>Pengganti {{ $orig->name }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @endforeach

                @if ($extraDetails->isNotEmpty())
                    <div class="osh-sectitle">{{ $extraLabel }}</div>
                    <div class="table-responsive">
                        <table class="osh-table">
                            <thead>
                                <tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($extraDetails as $detail)
                                    <tr>
                                        <td class="fw-semibold">{{ $detail->menu?->name ?? 'Menu tidak ditemukan' }}</td>
                                        <td>Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td class="text-end fw-semibold">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($order->packages->isEmpty() && $order->details->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada rincian item untuk pesanan ini.
                    </div>
                @endif

                @if ($order->notes)
                    <div class="osh-note"><b><i class="bi bi-chat-left-text me-1"></i>Catatan pesanan</b>{{ $order->notes }}</div>
                @endif
            </section>

            {{-- PEMBAYARAN --}}
            <section class="osh-card">
                <div class="osh-cardhead">
                    <div>
                        <h5>Pembayaran</h5>
                        <p>Pembayaran yang diterima memulai proses pesanan.</p>
                    </div>
                    <span class="osh-chip osh-chip--{{ $payClass }}">{{ $paymentStatus }}</span>
                </div>

                <div class="osh-pay-grid">
                    <div class="osh-pay-box"><small>Total tagihan</small><strong>Rp{{ number_format($orderTotal, 0, ',', '.') }}</strong></div>
                    <div class="osh-pay-box"><small>Sudah dibayar</small><strong class="text-success">Rp{{ number_format($paidAmount, 0, ',', '.') }}</strong></div>
                    <div class="osh-pay-box"><small>Sisa pembayaran</small><strong class="{{ $remaining > 0 ? 'text-danger' : 'text-success' }}">Rp{{ number_format($remaining, 0, ',', '.') }}</strong></div>
                </div>

                @if ($order->pendingAmount() > 0)
                    <div class="small fw-semibold mt-2" style="color:#8a5a00;">
                        <i class="bi bi-hourglass-split"></i>
                        Menunggu verifikasi: Rp{{ number_format($order->pendingAmount(), 0, ',', '.') }}
                    </div>
                @endif

                <div class="mt-3">
                    <div class="osh-kv"><span>Metode</span><b>{{ $payment?->method ? strtoupper($payment->method) : 'Belum dipilih' }}</b></div>
                    <div class="osh-kv">
                        <span>Bukti pembayaran</span>
                        <b>
                            @if ($payment?->proof)
                                <a href="{{ asset('storage/' . $payment->proof) }}" target="_blank" rel="noopener noreferrer" class="text-success text-decoration-none">
                                    <i class="bi bi-image me-1"></i>Lihat bukti
                                </a>
                            @else
                                <span class="fw-normal">Belum ada bukti</span>
                            @endif
                        </b>
                    </div>
                    @if ($payment?->notes)
                        <div class="osh-kv"><span>Catatan</span><b>{{ $payment->notes }}</b></div>
                    @endif
                </div>

                <div class="mt-3">
                    <div class="small fw-bold mb-1">Riwayat pembayaran</div>
                    @forelse ($order->payments->sortByDesc('id') as $p)
                        <div class="osh-kv">
                            <span>{{ $p->kindLabel()[$p->type] ?? 'Pembayaran' }} · {{ strtoupper($p->method) }}</span>
                            <b>
                                Rp{{ number_format($p->amount, 0, ',', '.') }}
                                <a href="{{ route('admin.payments.show', $p->id) }}" class="ms-1 text-decoration-none">{{ $p->status }}</a>
                            </b>
                        </div>
                    @empty
                        <span class="small text-muted">Belum ada pembayaran.</span>
                    @endforelse
                </div>

                @if ($remaining > 0 && !$order->hasPendingPayment())
                    <form method="POST" action="{{ route('admin.orders.cash', $order->id) }}"
                        class="mt-3 d-flex gap-2 flex-wrap align-items-end"
                        onsubmit="return confirm('Catat pembayaran tunai ini?')">
                        @csrf
                        <div>
                            <label class="form-label small mb-1">Catat pembayaran tunai (Rp)</label>
                            <input type="number" name="amount" min="1" max="{{ (int) $remaining }}" value="{{ (int) $remaining }}" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-success"><i class="bi bi-cash-coin me-1"></i>Catat tunai</button>
                    </form>
                @endif

                <div class="osh-foot">
                    <span>Nominal, status, dan verifikasi diatur di halaman pembayaran.</span>
                    @if ($payment)
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-success rounded-3 px-3">
                            <i class="bi bi-sliders me-1"></i> Atur pembayaran
                        </a>
                    @else
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-success rounded-3 px-3">
                            <i class="bi bi-credit-card me-1"></i> Kelola pembayaran
                        </a>
                    @endif
                </div>
            </section>

        </div>

        {{-- ================= KANAN ================= --}}
        <div class="col-xl-4">

            {{-- PELANGGAN --}}
            <section class="osh-card">
                <div class="osh-cardhead"><div><h5>Pelanggan</h5></div></div>

                <div class="osh-cust">
                    <div class="osh-av">{{ strtoupper(mb_substr($customer->name ?? '?', 0, 1)) }}</div>
                    <div>
                        <b>{{ $customer->name ?? 'Pelanggan dihapus' }}</b>
                        <small>{{ $customer->phone ?? 'Belum ada nomor telepon' }}</small>
                    </div>
                </div>

                @if ($customer?->address)
                    <div class="osh-addr"><i class="bi bi-geo-alt"></i><span>{{ $customer->address }}</span></div>
                @endif

                @if ($digits)
                    <div class="d-flex gap-2 mt-3">
                        <a href="tel:{{ $digits }}" class="btn btn-outline-secondary btn-sm flex-fill rounded-3"><i class="bi bi-telephone me-1"></i>Telepon</a>
                        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm flex-fill rounded-3"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
                    </div>
                @endif
            </section>

            {{-- JADWAL & PENGIRIMAN --}}
            <section class="osh-card">
                <div class="osh-cardhead">
                    <div><h5>Jadwal dan pengiriman</h5></div>
                    <button type="button" class="btn btn-outline-success btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#shippingModal">
                        <i class="bi bi-pencil-square me-1"></i>Atur
                    </button>
                </div>

                <div class="osh-kv"><span>Tanggal pesanan</span><b>{{ Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</b></div>
                <div class="osh-kv"><span>{{ $isPickup ? 'Tanggal pengambilan' : 'Tanggal pengiriman' }}</span><b>{{ Carbon::parse($order->delivery_date)->translatedFormat('d M Y') }}</b></div>
                <div class="osh-kv"><span>Metode</span><b>{{ $isPickup ? 'Ambil sendiri' : 'Diantar catering' }}</b></div>
                <div class="osh-kv">
                    <span>Jarak</span>
                    <b>
                        @if ($isPickup) -
                        @elseif ($order->delivery_distance !== null) {{ number_format($order->delivery_distance, 1, ',', '.') }} km
                        @else Belum diatur
                        @endif
                    </b>
                </div>
                <div class="osh-kv">
                    <span>Ongkir</span>
                    <b>
                        @if ($isPickup) Gratis
                        @elseif ($order->delivery_distance === null) Belum diatur
                        @else Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                        @endif
                    </b>
                </div>

                @if ($needShipping)
                    <div class="osh-warn"><i class="bi bi-exclamation-triangle me-1"></i>Jarak dan ongkir belum diatur. Atur pengiriman sebelum pesanan diselesaikan.</div>
                @endif
            </section>

            {{-- RINGKASAN BIAYA --}}
            <section class="osh-card">
                <div class="osh-cardhead"><div><h5>Ringkasan biaya</h5></div></div>

                <div class="osh-kv"><span>Subtotal paket</span><b>Rp{{ number_format($subtotalPaket, 0, ',', '.') }}</b></div>
                @if ($subtotalMenu > 0 || $extraDetails->isNotEmpty())
                    <div class="osh-kv"><span>Subtotal {{ strtolower($extraLabel) }}</span><b>Rp{{ number_format($subtotalMenu, 0, ',', '.') }}</b></div>
                @endif
                <div class="osh-kv">
                    <span>Biaya pengiriman</span>
                    <b>
                        @if ($isPickup) Gratis
                        @elseif ($order->delivery_distance === null) Belum dikonfirmasi
                        @else Rp{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}
                        @endif
                    </b>
                </div>

                <div class="osh-total">
                    <span class="text-muted">Total pesanan</span>
                    <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
            </section>
        </div>
    </div>

    {{-- MODAL ATUR PENGIRIMAN MODERN --}}
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content osh-modal">
                <form action="{{ route('orders.shipping.update', $order->id) }}" method="POST" id="shippingForm">
                    @csrf
                    @method('PATCH')

                    <div class="osh-modal-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="modal-title fw-bold text-dark m-0" id="shippingModalLabel">Atur Pengiriman</h5>
                            <p class="text-muted small m-0 mt-1">Kelola metode, jarak, dan biaya kirim untuk pesanan <b>{{ $code }}</b></p>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="osh-modal-body">
                        <div class="osh-form-group">
                            <label for="deliveryMethod" class="osh-form-label">Metode Pengiriman</label>
                            <select name="delivery_method" id="deliveryMethod" class="osh-form-select" required>
                                <option value="pickup" @selected(old('delivery_method', $order->delivery_method ?? 'pickup') === 'pickup')>🏪 Ambil Sendiri di Tempat</option>
                                <option value="delivery" @selected(old('delivery_method', $order->delivery_method ?? 'pickup') === 'delivery')>🛵 Diantar oleh Catering</option>
                            </select>
                        </div>

                        <div class="osh-form-group" id="distanceWrapper">
                            <label for="deliveryDistance" class="osh-form-label">Jarak Pengiriman (km)</label>
                            <div class="input-group">
                                <input type="number" name="delivery_distance" id="deliveryDistance" class="osh-form-control form-control"
                                       min="0" step="0.1" value="{{ old('delivery_distance', $order->delivery_distance) }}" placeholder="Contoh: 7">
                                <span class="input-group-text bg-light border-start-0 text-muted small rounded-end-3">KM</span>
                            </div>
                            @error('delivery_distance')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="osh-form-group" id="feeWrapper">
                            <label for="deliveryFee" class="osh-form-label">Biaya Pengiriman / Ongkir (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted small rounded-start-3">Rp</span>
                                <input type="number" name="delivery_fee" id="deliveryFee" class="osh-form-control form-control border-start-0 ps-1"
                                       min="0" step="1" value="{{ old('delivery_fee', $order->delivery_fee ?? 0) }}" placeholder="0">
                            </div>
                            <small class="text-muted d-block mt-1" id="feeHint"><i class="bi bi-info-circle me-1"></i>Ongkir gratis untuk jarak sampai 5 km.</small>
                            @error('delivery_fee')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="osh-modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold text-muted" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold text-white" style="background: var(--brand); border-color: var(--brand);">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Pengiriman
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
            feeHint.innerHTML = '<i class="bi bi-info-circle me-1"></i>Tidak ada ongkir untuk ambil sendiri.';
            return;
        }

        if (distance.value !== '' && Number(distance.value) <= 5) {
            fee.value = '0';
            feeHint.innerHTML = '<i class="bi bi-info-circle me-1"></i>Gratis ongkir untuk jarak sampai 5 km.';
        } else {
            feeHint.innerHTML = '<i class="bi bi-info-circle me-1"></i>Jarak di atas 5 km: tentukan ongkir yang sesuai.';
        }
    }

    method.addEventListener('change', updateShippingFields);
    distance.addEventListener('input', updateShippingFields);
    updateShippingFields();

    // Jika validasi Laravel gagal, buka lagi modal agar pesan error terlihat.
    @if ($errors->has('delivery_method') || $errors->has('delivery_distance') || $errors->has('delivery_fee'))
        new bootstrap.Modal(document.getElementById('shippingModal')).show();
    @endif
});
</script>
@endsection