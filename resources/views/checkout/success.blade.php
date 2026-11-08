@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
@php
    $isDelivery = $order->delivery_method === 'delivery';
    $ongkirOk   = !$isDelivery || $order->delivery_distance !== null;

    $total         = (float) $order->total;
    $terverifikasi = $order->paidAmount();
    $menunggu      = $order->pendingAmount();
    $belumBayar    = max(0, $order->remainingAmount() - $menunggu);
    $lunas         = $order->isFullyPaid();
    $adaBukti      = $order->payments->isNotEmpty();
    $pctPaid       = $total > 0 ? min(100, round($terverifikasi / $total * 100, 1)) : 0;
    $pctPending    = $total > 0 ? min(100 - $pctPaid, round($menunggu / $total * 100, 1)) : 0;

    // ---- status pesanan dalam bahasa pelanggan ----
    $st = strtolower(trim((string) $order->status));

    $tahap = match (true) {
        $st === 'selesai'            => 4,
        str_starts_with($st, 'siap') => 3,
        $st === 'diproses'           => 2,
        $st === 'pending'            => 1,
        default                      => 0,
    };

    if ($st === 'dibatalkan') {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Pesanan dibatalkan', 'Pesanan ini tidak dilanjutkan.', 'bi-x-circle-fill', 'danger'];
    } elseif ($tahap === 4) {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Pesanan selesai', 'Terima kasih telah memesan di Kedai BangJo.', 'bi-check-circle-fill', 'success'];
    } elseif ($tahap === 3) {
        [$stJudul, $stKet, $stIkon, $stWarna] = $isDelivery
            ? ['Siap diantar', 'Pesananmu sedang menuju alamat tujuan.', 'bi-truck', 'success']
            : ['Siap diambil', 'Silakan ambil pesananmu di Kedai BangJo.', 'bi-shop', 'success'];
    } elseif ($tahap === 2) {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Sedang diproses', 'Admin sedang menyiapkan pesananmu.', 'bi-gear-fill', 'primary'];
    } elseif ($isDelivery && !$ongkirOk) {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Menunggu konfirmasi ongkir', 'Admin akan menghitung jarak dan ongkir, lalu kamu bisa membayar.', 'bi-truck', 'warning'];
    } elseif ($menunggu > 0) {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Menunggu verifikasi pembayaran', 'Admin sedang memeriksa bukti pembayaranmu.', 'bi-hourglass-split', 'warning'];
    } else {
        [$stJudul, $stKet, $stIkon, $stWarna] = ['Menunggu pembayaran', 'Selesaikan pembayaran agar pesanan mulai diproses.', 'bi-wallet2', 'warning'];
    }

    $langkah = [1 => 'Dipesan', 2 => 'Diproses', 3 => $isDelivery ? 'Diantar' : 'Siap diambil', 4 => 'Selesai'];

    // ---- item & biaya ----
    $extras       = $order->details->where('source', 'custom');
    $komponen     = $order->details->where('source', 'package');
    $subtotalItem = (float) $order->packages->sum('subtotal') + (float) $extras->sum('subtotal');

    $ongkirText = !$isDelivery
        ? 'Gratis (ambil sendiri)'
        : (!$ongkirOk
            ? 'Menunggu konfirmasi admin'
            : ((float) $order->delivery_fee > 0
                ? 'Rp' . number_format($order->delivery_fee, 0, ',', '.')
                : 'Gratis'));

    $payChip  = ['Dibayar' => 'ok', 'Menunggu Verifikasi' => 'warn', 'Ditolak' => 'bad'];
    $payLabel = ['Dibayar' => 'Terverifikasi', 'Menunggu Verifikasi' => 'Menunggu', 'Ditolak' => 'Ditolak'];
    $rp       = fn ($v) => 'Rp' . number_format((float) $v, 0, ',', '.');
@endphp

<style>
    .sx { --g:#198754; --gd:#145c38; --gl:#eaf5ed; --ink:#1f2a24; --mute:#6b7770; --line:#e2e8e3; background:#f8faf8; min-height:85vh; padding:24px 0 60px; color:var(--ink); }
    .sx a { text-decoration:none; }

    /* hero (rata tengah) */
    .sx-hero { background:linear-gradient(180deg,#eaf5ed 0%,#fff 100%); border:1px solid var(--line); border-radius:20px; padding:28px 24px 24px; text-align:center; margin-bottom:16px; }
    .sx-check { width:72px; height:72px; margin:0 auto 14px; border-radius:50%; background:var(--g); color:#fff; display:grid; place-items:center; font-size:2.1rem; box-shadow:0 0 0 8px rgba(25,135,84,.12), 0 0 0 16px rgba(25,135,84,.06); }
    .sx-badge { display:inline-block; font-size:.7rem; font-weight:800; letter-spacing:1.2px; color:var(--gd); background:var(--gl); padding:4px 10px; border-radius:999px; }
    .sx-hero h1 { margin:.6rem 0 .25rem; font-size:1.6rem; font-weight:900; }
    .sx-hero p { margin:0 auto; max-width:520px; color:var(--mute); font-size:.92rem; }
    .sx-code { display:inline-flex; align-items:center; gap:.6rem; margin-top:16px; background:#fff; border:1px dashed #9cc7ad; border-radius:12px; padding:.45rem .5rem .45rem .9rem; }
    .sx-code .lbl { font-size:.66rem; font-weight:800; letter-spacing:1px; color:var(--mute); }
    .sx-code b { font-family:monospace; font-size:1rem; color:var(--gd); }
    .sx-copy { border:0; background:var(--gl); color:var(--gd); width:32px; height:32px; border-radius:9px; }
    .sx-copy:hover { background:#d8ebd9; }

    /* tiga ringkasan sama lebar */
    .sx-stats { display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; }
    .sx-stat { background:#fff; border:1px solid var(--line); border-radius:16px; padding:14px 16px; text-align:center; }
    .sx-stat small { display:block; color:var(--mute); font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; }
    .sx-stat strong { display:block; font-size:1.3rem; font-weight:900; margin-top:2px; }
    .sx-stat em { display:block; font-style:normal; font-size:.72rem; color:var(--mute); margin-top:2px; }
    .sx-stat.ok strong { color:var(--g); }
    .sx-stat.due strong { color:#c62828; }
    .sx-bar { display:flex; height:8px; border-radius:6px; overflow:hidden; background:#e6ece7; margin:14px 4px 16px; }
    .sx-bar i { display:block; height:100%; }
    .sx-bar .a { background:var(--gd); }
    .sx-bar .b { background:#f0ad00; }

    /* kartu sama tinggi */
    .sx-card { background:#fff; border:1px solid var(--line); border-radius:16px; padding:16px 18px; height:100%; }
    .sx-card h2 { display:flex; align-items:center; gap:.5rem; margin:0 0 12px; padding-bottom:10px; border-bottom:1px dashed var(--line); font-size:.95rem; font-weight:850; }
    .sx-card h2 i { color:var(--g); }

    .sx-state { display:flex; align-items:center; gap:12px; padding:12px; border:1px solid var(--line); border-radius:12px; background:#fbfcfb; }
    .sx-state .ic { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; font-size:1.3rem; flex:none; }
    .sx-state .ic.success { background:#dff3e7; color:#16734a; }
    .sx-state .ic.warning { background:#fff0d9; color:#98600d; }
    .sx-state .ic.primary { background:#e4efff; color:#285ca6; }
    .sx-state .ic.danger  { background:#fde8e7; color:#b52c28; }
    .sx-state b { display:block; font-size:.95rem; }
    .sx-state span { color:var(--mute); font-size:.8rem; }

    .sx-track { display:grid; grid-template-columns:repeat(4, 1fr); gap:6px; margin-top:14px; }
    .sx-step { text-align:center; font-size:.7rem; font-weight:700; color:var(--mute); }
    .sx-step i { display:grid; place-items:center; width:26px; height:26px; margin:0 auto 4px; border-radius:50%; background:#e6ede8; font-style:normal; font-size:.75rem; }
    .sx-step.done i { background:var(--g); color:#fff; }
    .sx-step.now i { background:var(--gd); color:#fff; box-shadow:0 0 0 4px rgba(25,135,84,.18); }
    .sx-step.now { color:var(--gd); }

    .sx-chip { display:inline-block; padding:.12rem .55rem; border-radius:999px; font-size:.7rem; font-weight:800; }
    .sx-chip.ok { background:#e3f4ea; color:#146c43; }
    .sx-chip.warn { background:#fff1d6; color:#8a5a00; }
    .sx-chip.bad { background:#fde8e8; color:#a52834; }
    .sx-hist { display:flex; justify-content:space-between; align-items:center; gap:8px; padding:7px 0; border-top:1px dashed var(--line); font-size:.82rem; }
    .sx-hist small { display:block; color:var(--mute); font-size:.7rem; }

    .sx-tiles { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
    .sx-tile { background:#fbfcfb; border:1px solid var(--line); border-radius:10px; padding:8px 12px; min-width:0; }
    .sx-tile.full { grid-column:1 / -1; }
    .sx-tile small { display:block; font-size:.66rem; font-weight:800; text-transform:uppercase; color:var(--mute); }
    .sx-tile b { font-size:.85rem; overflow-wrap:anywhere; }

    .sx-item { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-bottom:1px dashed var(--line); font-size:.86rem; }
    .sx-item small { display:block; color:var(--mute); font-size:.74rem; }
    .sx-isi { margin-top:8px; font-size:.8rem; }
    .sx-isi summary { cursor:pointer; color:var(--gd); font-weight:700; }
    .sx-isi ul { list-style:none; margin:6px 0 0; padding:0; }
    .sx-isi li { display:flex; justify-content:space-between; padding:2px 0; color:var(--mute); }
    .sx-total { background:var(--gl); border:1px solid #cde2d3; border-radius:12px; padding:10px 14px; margin-top:12px; }
    .sx-total .row2 { display:flex; justify-content:space-between; font-size:.84rem; padding:2px 0; }
    .sx-total .big { border-top:1px solid #cde2d3; margin-top:6px; padding-top:8px; font-weight:900; color:var(--gd); font-size:1.05rem; }

    /* tombol aksi sama lebar */
    .sx-actions { display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-top:16px; }
    .sx-btn { display:flex; align-items:center; justify-content:center; gap:.45rem; padding:.75rem 1rem; border-radius:12px; font-weight:800; font-size:.9rem; border:1px solid var(--line); background:#fff; color:var(--ink); transition:all .15s; }
    .sx-btn:hover { background:var(--gl); border-color:var(--g); color:var(--gd); }
    .sx-btn.primary { background:var(--g); border-color:var(--g); color:#fff; }
    .sx-btn.primary:hover { background:var(--gd); color:#fff; }

    @media (max-width: 767.98px) { .sx-stats { grid-template-columns:1fr; } }
    @media (max-width: 575.98px) { .sx-actions { grid-template-columns:1fr; } .sx-tiles { grid-template-columns:1fr; } }
</style>

<div class="sx">
    <div class="container" style="max-width: 960px;">

        {{-- HERO --}}
        <section class="sx-hero">
            <div class="sx-check"><i class="bi bi-check-lg"></i></div>
            <span class="sx-badge">{{ $adaBukti ? 'PEMBAYARAN TERKIRIM' : 'PESANAN DITERIMA' }}</span>
            <h1>{{ $adaBukti ? 'Pembayaran Berhasil Dikirim!' : 'Terima Kasih Atas Pesanan Anda!' }}</h1>
            <p>
                {{ $adaBukti
                    ? 'Admin akan memverifikasi bukti pembayaranmu, lalu pesanan diproses.'
                    : 'Pesanan berhasil dicatat. Tim kami siap memproses pesanan Anda.' }}
            </p>

            <div class="sx-code">
                <span class="lbl">KODE PESANAN</span>
                <b id="sx-code">{{ $order->order_code }}</b>
                <button type="button" class="sx-copy" id="sx-copy" title="Salin kode" aria-label="Salin kode pesanan">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </section>

        {{-- RINGKASAN: 3 KOTAK SAMA LEBAR --}}
        <section class="sx-stats">
            <div class="sx-stat">
                <small>Total tagihan</small>
                <strong>{{ $rp($total) }}</strong>
                @if (!$ongkirOk)<em>belum termasuk ongkir</em>@endif
            </div>
            <div class="sx-stat ok">
                <small>Sudah dibayar</small>
                <strong>{{ $rp($terverifikasi) }}</strong>
                @if ($menunggu > 0)<em>+ {{ $rp($menunggu) }} menunggu verifikasi</em>@endif
            </div>
            <div class="sx-stat {{ $lunas ? 'ok' : 'due' }}">
                <small>Sisa tagihan</small>
                <strong>{{ $lunas ? 'Lunas' : $rp($belumBayar) }}</strong>
                @if (!$lunas && $belumBayar <= 0 && $menunggu > 0)<em>menunggu verifikasi</em>@endif
            </div>
        </section>

        <div class="sx-bar" title="{{ $pctPaid }}% terverifikasi">
            <i class="a" style="width: {{ $pctPaid }}%"></i>
            <i class="b" style="width: {{ $pctPending }}%"></i>
        </div>

        {{-- BARIS 1: STATUS PESANAN | STATUS PEMBAYARAN --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="sx-card">
                    <h2><i class="bi bi-clock-history"></i> Status Pesanan</h2>

                    <div class="sx-state">
                        <div class="ic {{ $stWarna }}"><i class="bi {{ $stIkon }}"></i></div>
                        <div>
                            <b>{{ $stJudul }}</b>
                            <span>{{ $stKet }}</span>
                        </div>
                    </div>

                    @if ($tahap > 0)
                        <div class="sx-track">
                            @foreach ($langkah as $no => $label)
                                <div class="sx-step {{ $tahap > $no ? 'done' : '' }} {{ $tahap === $no ? 'now' : '' }}">
                                    <i>@if ($tahap > $no)<span class="bi bi-check-lg"></span>@else{{ $no }}@endif</i>
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="sx-card">
                    <h2><i class="bi bi-credit-card"></i> Status Pembayaran</h2>

                    @if ($lunas)
                        <div class="alert alert-success border-0 rounded-3 small mb-2 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Pesanan sudah lunas.
                        </div>
                    @elseif ($order->hasPendingPayment())
                        <div class="alert alert-warning border-0 rounded-3 small mb-2 py-2">
                            <i class="bi bi-hourglass-split me-1"></i> Bukti pembayaran terkirim dan menunggu verifikasi admin.
                            @if ($belumBayar > 0) Sisa {{ $rp($belumBayar) }} bisa dilunasi setelah itu.@endif
                        </div>
                    @elseif ($belumBayar > 0 && $ongkirOk)
                        <a href="{{ route('customer.payment.create', $order->id) }}" class="sx-btn primary mb-2">
                            <i class="bi bi-credit-card"></i> {{ $adaBukti ? 'Bayar Pelunasan' : 'Bayar Sekarang' }}
                        </a>
                    @elseif ($belumBayar > 0)
                        <div class="alert alert-info border-0 rounded-3 small mb-2 py-2">
                            <i class="bi bi-truck me-1"></i> Menunggu admin mengonfirmasi ongkir sebelum kamu bisa membayar.
                        </div>
                    @endif

                    @forelse ($order->payments->sortBy('id') as $p)
                        <div class="sx-hist">
                            <div>
                                <b>{{ $p->kindLabel() }}</b> · {{ strtoupper($p->method) }}
                                <small>{{ $p->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}</small>
                            </div>
                            <div class="text-end">
                                <b>{{ $rp($p->amount) }}</b><br>
                                <span class="sx-chip {{ $payChip[$p->status] ?? 'warn' }}">{{ $payLabel[$p->status] ?? $p->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">Belum ada pembayaran yang dikirim.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- BARIS 2: INFORMASI PEMESAN | RINCIAN ITEM --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="sx-card">
                    <h2><i class="bi bi-person-badge"></i> Informasi Pemesan</h2>

                    <div class="sx-tiles">
                        <div class="sx-tile"><small>Nama pemesan</small><b>{{ $order->customer->name ?? '-' }}</b></div>
                        <div class="sx-tile"><small>WhatsApp</small><b>{{ $order->customer->phone ?? '-' }}</b></div>
                        <div class="sx-tile"><small>Tgl dipesan</small><b>{{ \Carbon\Carbon::parse($order->order_date)->locale('id')->translatedFormat('d M Y') }}</b></div>
                        <div class="sx-tile"><small>Tgl dibutuhkan</small><b class="text-success">{{ \Carbon\Carbon::parse($order->delivery_date)->locale('id')->translatedFormat('d M Y') }}</b></div>
                        <div class="sx-tile full">
                            <small>Metode penerimaan</small>
                            <b>
                                @if ($isDelivery)
                                    <i class="bi bi-truck text-success me-1"></i> Diantar ke alamat
                                    @if ($order->delivery_distance !== null) · {{ $order->delivery_distance }} km @endif
                                @else
                                    <i class="bi bi-shop text-success me-1"></i> Ambil sendiri (di tempat)
                                @endif
                            </b>
                        </div>
                        @if ($isDelivery && $order->customer?->address)
                            <div class="sx-tile full"><small>Alamat pengiriman</small><b>{{ $order->customer->address }}</b></div>
                        @endif
                        @if ($order->notes)
                            <div class="sx-tile full"><small>Catatan pesanan</small><b class="fw-normal text-muted">"{{ $order->notes }}"</b></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sx-card">
                    <h2><i class="bi bi-basket2"></i> Rincian Item</h2>

                    @foreach ($order->packages as $op)
                        <div class="sx-item">
                            <div>
                                <b>{{ $op->package->name ?? 'Paket Catering' }}</b>
                                <small>{{ $op->quantity }} porsi × {{ $rp($op->price) }}</small>
                            </div>
                            <b>{{ $rp($op->subtotal) }}</b>
                        </div>
                    @endforeach

                    @foreach ($extras as $d)
                        <div class="sx-item">
                            <div>
                                <b>{{ $d->menu->name ?? 'Menu tambahan' }}</b>
                                <small>{{ $d->quantity }} porsi × {{ $rp($d->price) }}</small>
                            </div>
                            <b>{{ $rp($d->subtotal) }}</b>
                        </div>
                    @endforeach

                    @if ($komponen->isNotEmpty())
                        <details class="sx-isi">
                            <summary>Lihat isi paket ({{ $komponen->count() }} menu)</summary>
                            <ul>
                                @foreach ($komponen as $d)
                                    <li><span>{{ $d->menu->name ?? 'Menu' }}</span><b>{{ $d->quantity }} porsi</b></li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    <div class="sx-total">
                        <div class="row2"><span>Subtotal item</span><b>{{ $rp($subtotalItem) }}</b></div>
                        <div class="row2">
                            <span>Ongkos kirim</span>
                            <b class="{{ $isDelivery && !$ongkirOk ? 'text-warning' : 'text-success' }}">{{ $ongkirText }}</b>
                        </div>
                        <div class="row2 big">
                            <span>{{ $ongkirOk ? 'TOTAL TAGIHAN' : 'TOTAL SEMENTARA' }}</span>
                            <span>{{ $rp($total) }}</span>
                        </div>
                        @if (!$ongkirOk)
                            <div class="text-muted mt-1" style="font-size: .72rem;">Belum termasuk ongkir. Total final setelah admin menetapkan jarak dan ongkir.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- AKSI: 3 TOMBOL SAMA LEBAR --}}
        <div class="sx-actions">
            <a href="{{ route('customer.packages.index') }}" class="sx-btn primary">
                <i class="bi bi-house-door-fill"></i> Beranda
            </a>
            <a href="{{ route('customer.orders.receipt', ['order' => $order->id, 'print' => 1]) }}" target="_blank" rel="noopener" class="sx-btn">
                <i class="bi bi-printer"></i> Cetak Struk
            </a>
            <a href="{{ route('customer.orders.show', $order->id) }}" class="sx-btn">
                <i class="bi bi-receipt"></i> Detail Pesanan
            </a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn  = document.getElementById('sx-copy');
    var code = document.getElementById('sx-code');
    if (!btn || !code) { return; }

    var icon = function (name) { btn.innerHTML = '<i class="bi ' + name + '"></i>'; };
    var done = function () { icon('bi-check2'); setTimeout(function () { icon('bi-clipboard'); }, 1500); };

    btn.addEventListener('click', function () {
        var text = code.textContent.trim();

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done);
            return;
        }

        var t = document.createElement('textarea');
        t.value = text;
        document.body.appendChild(t);
        t.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        t.remove();
    });
});
</script>
@endsection