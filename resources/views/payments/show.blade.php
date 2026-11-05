@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
@php
    use Illuminate\Support\Str;

    $order  = $payment->order;
    $status = $payment->status ?? 'Belum Dibayar';
    $code   = $order?->order_code ?? ('ORD-' . ($order?->id ?? '-'));

    $chipClass = [
        'Menunggu Verifikasi' => 'warn',
        'Sebagian'            => 'info',
        'Dibayar'             => 'ok',
        'Ditolak'             => 'bad',
    ];
    $cls = $chipClass[$status] ?? 'off';

    $total = (float) ($order?->total ?? 0);

    $paid         = (float) $payment->amount;                               // nominal pembayaran INI
    $claimed      = (float) ($payment->claimed_amount ?? $payment->amount);  // klaim customer
    $typeLabel    = $payment->kindLabel();
    $paidBefore   = $order ? (float) $order->payments()->where('status', 'Dibayar')->where('id', '!=', $payment->id)->sum('amount') : 0;
    $otherPending = $order ? $order->payments()->where('status', 'Menunggu Verifikasi')->where('id', '!=', $payment->id)->get() : collect();
    $pendingOther = (float) $otherPending->sum('amount');
    $sisaSebelum  = max(0, $total - $paidBefore);
    $verifiedAll = $order ? $order->paidAmount() : 0;   // total semua pembayaran yang sudah terverifikasi
    $completes    = ($paidBefore + $claimed) >= $total - 0.5;               // apakah pembayaran ini melunasi?
    $counted      = in_array($status, ['Menunggu Verifikasi', 'Dibayar'], true) ? $paid : 0;
    $sisaSetelah  = max(0, $total - $paidBefore - $counted);
    $diff         = $sisaSetelah;
    $pctPaid      = $total > 0 ? min(100, (int) round($paidBefore / $total * 100)) : 0;
    $pctThis      = $total > 0 ? min(100 - $pctPaid, (int) round($counted / $total * 100)) : 0;

    $proofUrl = $payment->proof ? asset('storage/' . $payment->proof) : null;
    $isPdf    = $payment->proof && Str::endsWith(Str::lower($payment->proof), '.pdf');
    $rupiah   = fn ($v) => 'Rp' . number_format(abs($v), 0, ',', '.');
@endphp

<style>
    .pys { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .pys-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .pys-cardhead { display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; margin-bottom:.9rem; }
    .pys-cardhead h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .pys-cardhead p { margin:.1rem 0 0; color:var(--mute); font-size:.85rem; }

    .pys-head { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
    .pys-code { font-size:1.4rem; font-weight:800; color:var(--ink); line-height:1.2; }
    .pys-meta { color:var(--mute); font-size:.9rem; margin-top:.3rem; display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
    .pys-actions { display:flex; gap:.5rem; flex-wrap:wrap; }

    .pys-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; white-space:nowrap; }
    .pys-chip::before { content:""; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .pys-chip--warn { background:#fff1d6; color:#8a5a00; }
    .pys-chip--info { background:#e2f0fb; color:#0b5a8a; }
    .pys-chip--ok { background:#e3f4ea; color:#146c43; }
    .pys-chip--bad { background:#fde8e8; color:#a52834; }
    .pys-chip--off { background:#eceff1; color:#5f6b72; }

    /* bukti */
    .pys-proof { display:block; background:var(--soft); border:1px solid var(--line); border-radius:14px; padding:.75rem; text-align:center; }
    .pys-proof img { max-width:100%; max-height:560px; object-fit:contain; border-radius:10px; }
    .pys-nofile { text-align:center; background:var(--soft); border-radius:14px; padding:2.5rem 1rem; color:var(--mute); }
    .pys-nofile i { font-size:2.4rem; }

    /* info */
    .pys-kv { display:flex; justify-content:space-between; gap:1rem; padding:.55rem 0; border-top:1px solid var(--line); font-size:.92rem; }
    .pys-kv:first-of-type { border-top:0; }
    .pys-kv span { color:var(--mute); }
    .pys-kv b { color:var(--ink); font-weight:600; text-align:right; overflow-wrap:anywhere; }

    /* cek nominal */
    .pys-check { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
    .pys-box { background:var(--soft); border-radius:12px; padding:.7rem .9rem; }
    .pys-box small { color:var(--mute); display:block; }
    .pys-box strong { font-size:1.12rem; color:var(--ink); }
    .pys-prog { height:8px; background:var(--line); border-radius:5px; overflow:hidden; margin:.9rem 0 .6rem; }
    .pys-prog i { display:block; height:100%; background:var(--brand); }
    .pys-verdict { display:flex; gap:.55rem; align-items:flex-start; border-radius:12px; padding:.65rem .8rem; font-size:.9rem; font-weight:600; }
    .pys-verdict--ok { background:#e3f4ea; color:#146c43; }
    .pys-verdict--warn { background:#fff1d6; color:#8a5a00; }
    .pys-verdict i { margin-top:.1rem; }

    /* tindakan */
    .pys-act-card { border-color:#cde2d3; }
    .pys-done { text-align:center; padding:.6rem 0; }
    .pys-done i { font-size:2.3rem; }
    .pys-done h6 { margin:.6rem 0 .2rem; font-weight:700; color:var(--ink); }
    .pys-done p { margin:0; color:var(--mute); font-size:.88rem; }
    .pys-or { display:flex; align-items:center; gap:.6rem; color:var(--mute); font-size:.8rem; margin:1rem 0; }
    .pys-or::before, .pys-or::after { content:""; flex:1; height:1px; background:var(--line); }
    .pys-act-card textarea { border-color:var(--line); border-radius:10px; }
    .pys-act-card textarea:focus { border-color:#dc3545; box-shadow:0 0 0 .2rem rgba(220,53,69,.12); }

    @media (min-width:1200px){ .pys-sticky { position:sticky; top:88px; } }

    .pys-prog--seg { display:flex; }
    .pys-prog--seg i.a { background:#146c43; }
    .pys-prog--seg i.b { background:#f0ad00; }
    .pys-prog--seg i.b.ok { background:#5fc08c; }
    .pys-legend { display:flex; gap:1rem; flex-wrap:wrap; font-size:.78rem; color:var(--mute); margin:-.2rem 0 .8rem; }
    .pys-legend span::before { content:""; display:inline-block; width:9px; height:9px; border-radius:3px; margin-right:.35rem; background:var(--c); }
    .pys-hist { display:flex; justify-content:space-between; align-items:center; gap:.75rem; padding:.65rem .8rem; border:1px solid var(--line); border-radius:12px; margin-top:.5rem; }
    .pys-hist.now { border-color:#cde2d3; background:#f4fbf6; }
    .pys-hist small { color:var(--mute); display:block; }

    .pys-thumb { width:48px; height:48px; object-fit:cover; border-radius:10px; border:1px solid var(--line); background:var(--soft); display:block; }
    .pys-thumb--none { display:grid; place-items:center; color:var(--mute); font-size:1.2rem; }
</style>

<div class="container-fluid pb-3 pys">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Periksa kembali isian berikut:</b>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <section class="pys-card">
        <div class="pys-head">
            <div>
                <div class="pys-code">{{ $code }}</div>
                <div class="pys-meta">
                    <span class="pys-chip pys-chip--{{ $cls }}">{{ $status === 'Dibayar' ? 'Pembayaran terverifikasi' : $status }}</span>
                    @if ($order)
                        <span class="pys-chip pys-chip--{{ $order->isFullyPaid() ? 'ok' : 'info' }}">
                            {{ $order->isFullyPaid() ? 'Pesanan lunas' : 'Belum lunas' }}
                        </span>
                    @endif
                    <span>{{ $order?->customer?->name ?? 'Customer tidak tersedia' }}</span>
                    <span>·</span>
                    <span>{{ $payment->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                </div>
            </div>
            <div class="pys-actions">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Daftar</a>
                @if ($order)
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-success"><i class="bi bi-receipt me-1"></i> Lihat pesanan</a>
                @endif
            </div>
        </div>
    </section>

    @if ($otherPending->isNotEmpty())
        <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Pesanan ini punya pembayaran lain yang masih menunggu verifikasi ({{ $rupiah($pendingOther) }}).
            @foreach ($otherPending as $op)
                <a href="{{ route('admin.payments.show', $op->id) }}" class="fw-bold ms-1">Buka pembayaran #{{ $op->id }}</a>
            @endforeach
        </div>
    @endif

    <div class="row g-4">

        {{-- ============ KIRI ============ --}}
        <div class="col-xl-7">

            @if ($order)
                <section class="pys-card">
                    <div class="pys-cardhead">
                        <div>
                            <h5>Pembayaran pesanan ini ({{ $order->payments->count() }})</h5>
                            <p>Klik gambar untuk melihat bukti, atau "Buka" untuk pindah ke pembayaran lain.</p>
                        </div>
                    </div>

                    @foreach ($order->payments->sortBy('id') as $p)
                        @php
                            $pc   = $chipClass[$p->status] ?? 'off';
                            $pUrl = $p->proof ? asset('storage/' . $p->proof) : null;
                        @endphp
                        <div class="pys-hist {{ $p->id === $payment->id ? 'now' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                @if ($pUrl)
                                    <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" title="Lihat bukti">
                                        <img src="{{ $pUrl }}" alt="Bukti pembayaran #{{ $p->id }}" class="pys-thumb">
                                    </a>
                                @else
                                    <span class="pys-thumb pys-thumb--none"><i class="bi bi-cash-coin"></i></span>
                                @endif
                                <div>
                                    <b>{{ $p->kindLabel() }}</b> · {{ strtoupper($p->method) }}
                                    <small>{{ $p->created_at?->translatedFormat('d M Y, H:i') }}@if ($p->id === $payment->id) · sedang dibuka @endif</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <b>{{ $rupiah($p->amount) }}</b>
                                <span class="pys-chip pys-chip--{{ $pc }} ms-1">{{ $p->status }}</span>
                                @if ($p->id !== $payment->id)
                                    <a href="{{ route('admin.payments.show', $p->id) }}" class="ms-1 small fw-semibold">Buka</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </section>
            @endif

            {{-- BUKTI --}}
            <section class="pys-card">
                <div class="pys-cardhead">
                    <div>
                        <h5>Bukti {{ strtolower($typeLabel) }} · #{{ $payment->id }}</h5>
                        <p>Cocokkan nominal dan identitas pengirim sebelum memverifikasi.</p>
                    </div>
                    @if ($proofUrl)
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Ukuran penuh
                        </a>
                    @endif
                </div>

                @if ($proofUrl && !$isPdf)
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer" class="pys-proof">
                        <img src="{{ $proofUrl }}" alt="Bukti pembayaran {{ $code }}">
                    </a>
                @elseif ($proofUrl && $isPdf)
                    <div class="pys-nofile">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <h6 class="fw-bold mt-2">Bukti berupa file PDF</h6>
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm mt-1">Buka PDF</a>
                    </div>
                @else
                    <div class="pys-nofile">
                        <i class="bi bi-file-earmark-x"></i>
                        <h6 class="fw-bold mt-2 text-dark">Bukti belum tersedia</h6>
                        <p class="small mb-0">Customer belum mengunggah bukti pembayaran.</p>
                    </div>
                @endif
            </section>

            {{-- RINCIAN --}}
            <section class="pys-card">
                <div class="pys-cardhead"><div><h5>Rincian pembayaran</h5></div></div>

                <div class="pys-kv"><span>Customer</span><b>{{ $order?->customer?->name ?? '-' }}</b></div>
                <div class="pys-kv"><span>Metode pembayaran</span><b>{{ $payment->method ? strtoupper($payment->method) : '-' }}</b></div>
                <div class="pys-kv"><span>Jenis pembayaran</span><b>{{ $typeLabel }}</b></div>
                <div class="pys-kv"><span>Nominal diklaim customer</span><b>{{ $rupiah($claimed) }}</b></div>
                @if ($status === 'Dibayar' && abs($claimed - $paid) > 0.5)
                    <div class="pys-kv"><span>Dikoreksi admin menjadi</span><b class="text-danger">{{ $rupiah($paid) }}</b></div>
                @endif
                <div class="pys-kv"><span>Tanggal pembayaran</span><b>{{ $payment->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</b></div>
                <div class="pys-kv"><span>Status pesanan</span><b>{{ $order?->status ?? '-' }}</b></div>
                <div class="pys-kv"><span>Catatan</span><b class="fw-normal">{{ $payment->notes ?: 'Tidak ada catatan.' }}</b></div>
            </section>
        </div>

        {{-- ============ KANAN ============ --}}
        <div class="col-xl-5">
            <div class="pys-sticky">

                {{-- CEK NOMINAL --}}
                <section class="pys-card">
                    <div class="pys-cardhead"><div><h5>Cek nominal</h5><p>Posisi tagihan seluruh pesanan ini.</p></div></div>

                    <div class="pys-check">
                        <div class="pys-box"><small>Total tagihan</small><strong>{{ $rupiah($total) }}</strong></div>
                        <div class="pys-box"><small>Total terverifikasi</small><strong>{{ $rupiah($verifiedAll) }}</strong></div>
                        <div class="pys-box">
                            <small>{{ $status === 'Dibayar' ? 'Pembayaran ini (terverifikasi)' : 'Pembayaran ini (klaim customer)' }}</small>
                            <strong>{{ $rupiah($status === 'Dibayar' ? $paid : $claimed) }}</strong>
                        </div>
                        <div class="pys-box">
                            <small>{{ $status === 'Menunggu Verifikasi' ? 'Sisa setelah diverifikasi' : 'Sisa tagihan' }}</small>
                            <strong>{{ $rupiah($sisaSetelah) }}</strong>
                        </div>
                    </div>

                    @if ($status === 'Dibayar' && $paidBefore > 0)
                        <div class="small text-muted mt-2">
                            {{ $rupiah($paidBefore) }} (pembayaran sebelumnya) + {{ $rupiah($paid) }} (pembayaran ini)
                            = <b>{{ $rupiah($verifiedAll) }}</b>
                        </div>
                    @endif

                    <div class="pys-prog pys-prog--seg mt-3" title="{{ $pctPaid + $pctThis }}% dari tagihan">
                        <i class="a" style="width:{{ $pctPaid }}%"></i>
                        <i class="b {{ $status === 'Dibayar' ? 'ok' : '' }}" style="width:{{ $pctThis }}%"></i>
                    </div>
                    <div class="pys-legend">
                        <span style="--c:#146c43">Terverifikasi sebelumnya</span>
                        <span style="--c:{{ $status === 'Dibayar' ? '#5fc08c' : '#f0ad00' }}">Pembayaran ini</span>
                    </div>

                    @if ($status === 'Menunggu Verifikasi')
                        @if ($completes)
                            <div class="pys-verdict pys-verdict--ok"><i class="bi bi-check-circle-fill"></i><span>Pembayaran ini <b>melunasi</b> pesanan.</span></div>
                        @else
                            <div class="pys-verdict pys-verdict--warn"><i class="bi bi-exclamation-triangle-fill"></i><span>Belum melunasi. Masih ada sisa <b>{{ $rupiah($sisaSetelah) }}</b> setelah pembayaran ini.</span></div>
                        @endif
                    @elseif ($status === 'Dibayar')
                        <div class="pys-verdict {{ $sisaSetelah > 0 ? 'pys-verdict--warn' : 'pys-verdict--ok' }}">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>{{ $sisaSetelah > 0 ? 'Pesanan belum lunas. Sisa ' . $rupiah($sisaSetelah) . '.' : 'Pesanan sudah lunas.' }}</span>
                        </div>
                    @endif
                </section>

                {{-- TINDAKAN --}}
                <section class="pys-card pys-act-card mb-0">
                    <div class="pys-cardhead"><div><h5>Tindakan admin</h5><p>Verifikasi jika bukti valid, atau tolak dengan alasan.</p></div></div>

                    @if ($status === 'Menunggu Verifikasi')

                        @if ($payment->proof)
                            <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST"
                                onsubmit="return confirm('Yakin bukti pembayaran ini valid dan nominalnya sudah sesuai?')">
                                @csrf
                                <label for="verified_amount" class="form-label fw-semibold small">Nominal terverifikasi (Rp)</label>
                                <input type="number" name="amount" id="verified_amount" class="form-control mb-1"
                                    min="1" max="{{ (int) $sisaSebelum }}" value="{{ (int) $claimed }}"
                                    data-total="{{ (int) round($total) }}" data-before="{{ (int) round($paidBefore) }}" required>
                                <div class="form-text mb-3" id="verify-note"></div>
                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="bi bi-patch-check me-1"></i>
                                    <span id="verify-label">{{ $completes ? 'Verifikasi lunas' : 'Verifikasi pembayaran' }}</span>
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning small mb-0">Bukti pembayaran belum tersedia, jadi verifikasi dinonaktifkan.</div>
                        @endif

                        <div class="pys-or">atau</div>

                        <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menolak pembayaran ini?')">
                            @csrf
                            <label for="notes" class="form-label fw-semibold small">Alasan penolakan</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control mb-3" maxlength="1000" required
                                      placeholder="Contoh: nominal tidak sesuai atau bukti tidak jelas">{{ old('notes') }}</textarea>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i> Tolak pembayaran
                            </button>
                        </form>

                    @elseif ($status === 'Dibayar')
                        <div class="pys-done">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <h6>Pembayaran terverifikasi</h6>
                            <p>{{ $order && $order->isFullyPaid() ? 'Pesanan ini sudah lunas.' : 'Sisa tagihan pesanan: ' . $rupiah($order?->remainingAmount() ?? 0) . '.' }}</p>
                        </div>
                    @elseif ($status === 'Sebagian')
                        <div class="pys-done">
                            <i class="bi bi-pie-chart-fill text-info"></i>
                            <h6>Dibayar sebagian</h6>
                            <p>Masih ada sisa tagihan {{ $rupiah(max($diff, 0)) }} yang perlu dilunasi.</p>
                        </div>
                    @elseif ($status === 'Ditolak')
                        <div class="pys-done">
                            <i class="bi bi-x-circle-fill text-danger"></i>
                            <h6>Pembayaran ditolak</h6>
                            <p>Customer perlu mengirim bukti pembayaran yang sesuai.</p>
                            @if ($payment->notes)<p class="mt-2"><b>Alasan:</b> {{ $payment->notes }}</p>@endif
                        </div>
                    @else
                        <div class="pys-done">
                            <i class="bi bi-hourglass text-secondary"></i>
                            <p class="mt-2">Belum ada tindakan yang tersedia untuk status ini.</p>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const input = document.getElementById('verified_amount');
    const label = document.getElementById('verify-label');
    const note  = document.getElementById('verify-note');
    if (!input || !label || !note) { return; }

    const TOTAL  = Number(input.dataset.total);
    const BEFORE = Number(input.dataset.before);
    const rp = function (n) { return 'Rp' + Number(n).toLocaleString('id-ID'); };

    function refresh() {
        const v    = Number(input.value) || 0;
        const left = Math.max(0, TOTAL - BEFORE - v);
        label.textContent = left < 1 ? 'Verifikasi lunas' : 'Verifikasi pembayaran';
        note.textContent  = left < 1
            ? 'Dengan nominal ini pesanan menjadi lunas.'
            : 'Sisa tagihan setelah ini: ' + rp(left) + '.';
    }

    input.addEventListener('input', refresh);
    refresh();
})();
</script>
@endsection