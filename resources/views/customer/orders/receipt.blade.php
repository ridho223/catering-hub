@php
    $isDelivery = $order->delivery_method === 'delivery';
    $ongkirOk   = !$isDelivery || $order->delivery_distance !== null;

    $total         = (float) $order->total;
    $terverifikasi = $order->paidAmount();
    $menunggu      = $order->pendingAmount();
    $sisa          = max(0, $order->remainingAmount());
    $belumBayar    = max(0, $sisa - $menunggu);
    $lunas         = $order->isFullyPaid();

    if ($lunas) {
        $stamp = ['LUNAS', 'lunas'];
    } elseif ($menunggu > 0) {
        $stamp = ['BELUM LUNAS · MENUNGGU VERIFIKASI', 'tunggu'];
    } elseif ($terverifikasi > 0) {
        $stamp = ['BELUM LUNAS · DP DITERIMA', 'dp'];
    } else {
        $stamp = ['BELUM DIBAYAR', 'belum'];
    }

    $extras       = $order->details->where('source', 'custom');
    $subtotalItem = (float) $order->packages->sum('subtotal') + (float) $extras->sum('subtotal');

    $ongkirText = !$isDelivery
        ? 'Gratis (ambil sendiri)'
        : (!$ongkirOk
            ? 'Belum dikonfirmasi admin'
            : ((float) $order->delivery_fee > 0
                ? 'Rp' . number_format($order->delivery_fee, 0, ',', '.')
                : 'Gratis'));

    $payLabel = ['Dibayar' => 'Terverifikasi', 'Menunggu Verifikasi' => 'Menunggu verifikasi', 'Ditolak' => 'Ditolak'];
    $rp       = fn ($v) => 'Rp' . number_format((float) $v, 0, ',', '.');
    $tgl      = fn ($v, $fmt = 'd F Y') => $v ? \Carbon\Carbon::parse($v)->locale('id')->translatedFormat($fmt) : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $order->order_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2ef; font-family: Arial, Helvetica, sans-serif; color: #1d2420; font-size: 13px; }

        .toolbar { max-width: 720px; margin: 16px auto 0; padding: 0 12px; display: flex; justify-content: space-between; gap: 8px; }
        .toolbar a, .toolbar button { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; border: 1px solid #cfd9d2; background: #fff; color: #1d2420; }
        .toolbar button { background: #198754; border-color: #198754; color: #fff; }

        .rcp { max-width: 720px; margin: 12px auto 32px; background: #fff; border: 1px solid #dde5df; border-radius: 14px; padding: 26px 28px; }
        .rcp-head { text-align: center; border-bottom: 2px solid #1d2420; padding-bottom: 12px; margin-bottom: 14px; }
        .rcp-brand { font-size: 20px; font-weight: 800; letter-spacing: .5px; }
        .rcp-sub { font-size: 11px; letter-spacing: 1.5px; color: #5c6a62; text-transform: uppercase; margin-top: 2px; }
        .rcp-code { margin-top: 8px; font-family: monospace; font-size: 15px; font-weight: 700; }
        .rcp-stamp { display: inline-block; margin-top: 10px; padding: 5px 16px; border: 2px solid; border-radius: 8px; font-weight: 800; letter-spacing: 1px; font-size: 13px; }
        .rcp-stamp.lunas  { color: #146c43; border-color: #146c43; }
        .rcp-stamp.belum  { color: #b02a37; border-color: #b02a37; }
        .rcp-stamp.dp     { color: #0b5a8a; border-color: #0b5a8a; }
        .rcp-stamp.tunggu { color: #8a5a00; border-color: #8a5a00; }

        .rcp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; margin-bottom: 6px; }
        .rcp-grid .full { grid-column: 1 / -1; }
        .rcp-grid span { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: #6c7a72; }
        .rcp-grid b { font-size: 13px; }

        .rcp-sec { margin: 16px 0 4px; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 10px; text-transform: uppercase; text-align: left; border-bottom: 1px solid #1d2420; padding: 5px 0; }
        td { padding: 6px 0; border-bottom: 1px dashed #d5ddd7; vertical-align: top; }
        .r { text-align: right; }
        .rcp-isi { font-size: 11px; color: #5c6a62; margin-top: 2px; }

        .rcp-tot { margin-top: 8px; border-top: 1.5px solid #1d2420; padding-top: 6px; }
        .rcp-row { display: flex; justify-content: space-between; gap: 12px; padding: 2px 0; }
        .rcp-row.big { font-size: 15px; font-weight: 800; border-top: 1px dashed #1d2420; margin-top: 4px; padding-top: 6px; }
        .rcp-row.ok  { color: #146c43; font-weight: 800; }
        .rcp-row.due { color: #b02a37; font-weight: 800; }

        .rcp-note { margin-top: 14px; padding: 8px 10px; border: 1px dashed #9aa79f; border-radius: 8px; font-size: 11.5px; line-height: 1.5; }
        .rcp-foot { text-align: center; margin-top: 16px; padding-top: 8px; border-top: 1px dashed #1d2420; font-size: 11px; color: #46534b; }

        @media print {
            @page { margin: 12mm; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .rcp { border: 0; border-radius: 0; margin: 0; max-width: none; padding: 0; }
            tr, .rcp-note { break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="toolbar no-print">
    <a href="{{ route('customer.orders.show', $order->id) }}">&larr; Kembali ke pesanan</a>
    <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<main class="rcp">

    <header class="rcp-head">
        <div class="rcp-brand">KEDAI BANGJO CATERING</div>
        <div class="rcp-sub">Struk Pemesanan</div>
        <div class="rcp-code">{{ $order->order_code }}</div>
        <div class="rcp-stamp {{ $stamp[1] }}">{{ $stamp[0] }}</div>
    </header>

    <section class="rcp-grid">
        <div><span>Nama pemesan</span><b>{{ $order->customer->name ?? '-' }}</b></div>
        <div><span>WhatsApp</span><b>{{ $order->customer->phone ?? '-' }}</b></div>
        <div><span>Tanggal dipesan</span><b>{{ $tgl($order->order_date) }}</b></div>
        <div><span>Tanggal dibutuhkan</span><b>{{ $tgl($order->delivery_date) }}</b></div>
        <div class="full">
            <span>Metode penerimaan</span>
            <b>
                {{ $isDelivery ? 'Diantar ke alamat' : 'Ambil sendiri (di tempat)' }}
                @if ($isDelivery && $order->delivery_distance !== null) · {{ $order->delivery_distance }} km @endif
            </b>
        </div>
        @if ($isDelivery && $order->customer?->address)
            <div class="full"><span>Alamat pengiriman</span><b>{{ $order->customer->address }}</b></div>
        @endif
        @if ($order->notes)
            <div class="full"><span>Catatan</span><b>{{ $order->notes }}</b></div>
        @endif
    </section>

    <div class="rcp-sec">Rincian pesanan</div>
    <table>
        <thead>
            <tr><th>Item</th><th class="r">Jumlah</th><th class="r">Harga</th><th class="r">Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach ($order->packages as $op)
                @php
                    $isi = collect($op->package?->details ?? [])->map(function ($d) use ($op) {
                        return ($d->menu?->name ?? 'Menu') . ' (' . ((int) $d->quantity * (int) $op->quantity) . ')';
                    })->implode(', ');
                @endphp
                <tr>
                    <td>
                        <b>{{ $op->package->name ?? 'Paket Catering' }}</b>
                        @if ($isi)<div class="rcp-isi">Isi: {{ $isi }}</div>@endif
                    </td>
                    <td class="r">{{ $op->quantity }} paket</td>
                    <td class="r">{{ $rp($op->price) }}</td>
                    <td class="r"><b>{{ $rp($op->subtotal) }}</b></td>
                </tr>
            @endforeach

            @foreach ($extras as $d)
                <tr>
                    <td><b>{{ $d->menu->name ?? 'Menu tambahan' }}</b></td>
                    <td class="r">{{ $d->quantity }} porsi</td>
                    <td class="r">{{ $rp($d->price) }}</td>
                    <td class="r"><b>{{ $rp($d->subtotal) }}</b></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="rcp-tot">
        <div class="rcp-row"><span>Subtotal item</span><span>{{ $rp($subtotalItem) }}</span></div>
        <div class="rcp-row">
            <span>Ongkos kirim</span>
            <span>{{ $ongkirText }}</span>
        </div>
        <div class="rcp-row big">
            <span>{{ $ongkirOk ? 'TOTAL TAGIHAN' : 'TOTAL SEMENTARA' }}</span>
            <span>{{ $rp($total) }}</span>
        </div>
    </div>

    <div class="rcp-sec">Pembayaran</div>
    @if ($order->payments->isNotEmpty())
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Jenis</th><th>Metode</th><th class="r">Nominal</th><th class="r">Status</th></tr>
            </thead>
            <tbody>
                @foreach ($order->payments->sortBy('id') as $p)
                    <tr>
                        <td>{{ $tgl($p->created_at, 'd/m/Y H:i') }}</td>
                        <td>{{ $p->kindLabel() }}</td>
                        <td>{{ strtoupper($p->method) }}</td>
                        <td class="r">{{ $rp($p->amount) }}</td>
                        <td class="r">{{ $payLabel[$p->status] ?? $p->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="color:#5c6a62;">Belum ada pembayaran.</div>
    @endif

    <div class="rcp-tot">
        <div class="rcp-row"><span>Total tagihan</span><span>{{ $rp($total) }}</span></div>
        <div class="rcp-row"><span>Sudah dibayar (terverifikasi)</span><span>{{ $rp($terverifikasi) }}</span></div>
        @if ($menunggu > 0)
            <div class="rcp-row"><span>Menunggu verifikasi</span><span>{{ $rp($menunggu) }}</span></div>
        @endif
        @if ($lunas)
            <div class="rcp-row big ok"><span>STATUS</span><span>LUNAS</span></div>
        @else
            <div class="rcp-row big due"><span>SISA TAGIHAN</span><span>{{ $rp($sisa) }}</span></div>
        @endif
    </div>

    @if (!$lunas || !$ongkirOk)
        <div class="rcp-note">
            @if (!$ongkirOk)
                &bull; Ongkos kirim belum dikonfirmasi admin, jadi total dapat berubah setelah jarak dan ongkir ditetapkan.<br>
            @endif
            @if (!$lunas)
                @if ($menunggu > 0 && $belumBayar <= 0)
                    &bull; Pembayaran {{ $rp($menunggu) }} sedang menunggu verifikasi admin.
                @elseif ($menunggu > 0)
                    &bull; Pembayaran {{ $rp($menunggu) }} sedang menunggu verifikasi. Sisa {{ $rp($belumBayar) }} harap dilunasi sebelum {{ $isDelivery ? 'pengantaran' : 'pengambilan' }}.
                @else
                    &bull; Pesanan belum lunas. Sisa {{ $rp($sisa) }} harap dilunasi sebelum {{ $isDelivery ? 'pengantaran' : 'pengambilan' }}.
                @endif
            @endif
        </div>
    @endif

    <footer class="rcp-foot">
        <strong>*** TERIMA KASIH ***</strong><br>
        Tunjukkan struk ini saat {{ $isDelivery ? 'menerima' : 'mengambil' }} pesanan.<br>
        Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}
    </footer>
</main>

@if (request()->boolean('print'))
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
@endif

</body>
</html>