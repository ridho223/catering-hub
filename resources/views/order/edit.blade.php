@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
@php
    use Illuminate\Support\Carbon;

    $isPickup = ($order->delivery_method ?? 'pickup') === 'pickup';

    // Alur status: Pending -> Diproses -> Siap diantar/diambil -> Selesai
    $flow = [
        ['Pending',              'Menunggu pembayaran', 'bi-hourglass-split'],
        ['Diproses',             'Diproses',            'bi-fire'],
        ['Siap_diambil_atau_diantar', $isPickup ? 'Siap diambil' : 'Siap diantar', 'bi-bag-check'],
        ['Selesai',              'Selesai',             'bi-check2-all'],
    ];
    $currentIdx = collect($flow)->search(fn ($s) => $s[0] === $order->status);
    if ($currentIdx === false) { $currentIdx = 1; }
    $locked   = $currentIdx === 0;               // masih Pending: status berubah otomatis setelah bayar
    $selected = old('status', $order->status);

    $orderDate    = old('order_date', Carbon::parse($order->order_date)->format('Y-m-d'));
    $deliveryDate = old('delivery_date', Carbon::parse($order->delivery_date)->format('Y-m-d'));

    $packagesJson = $packages->map(fn ($p) => [
        'id'    => $p->id,
        'name'  => $p->name,
        'price' => (float) $p->price,
        'type'  => $p->package_type ?? null,
        'image' => $p->image ?? null,
    ])->values();

    $initialItems = old('packages') ?? $order->packages->map(fn ($r) => [
        'package_id' => $r->package_id,
        'quantity'   => $r->quantity,
    ])->values()->all();

    // paket di pesanan ini yang sudah tidak tersedia di katalog
    $availableIds = $packages->pluck('id')->all();
    $missing = $order->packages
        ->filter(fn ($r) => !in_array($r->package_id, $availableIds))
        ->map(fn ($r) => $r->package?->name ?? ('Paket #' . $r->package_id))
        ->values();

    $customer = $order->customer;
@endphp

<style>
    .ord { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .ord-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .ord-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .ord-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .ord .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .ord .form-control, .ord .form-select { border-color:var(--line); border-radius:10px; }
    .ord .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }

    /* alur status */
    .ost { display:grid; grid-template-columns:repeat(4,1fr); gap:.6rem; }
    .ost-step { position:relative; }
    .ost-step input { position:absolute; top:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
    .ost-box, .ost-step label { display:flex; flex-direction:column; align-items:center; gap:.3rem; text-align:center; border:1px solid var(--line); border-radius:14px; padding:.8rem .4rem; background:#fff; color:var(--mute); font-size:.85rem; font-weight:500; height:100%; }
    .ost-step label { cursor:pointer; }
    .ost-step label:hover { border-color:#b9d8c7; }
    .ost-ic { width:36px; height:36px; border-radius:50%; background:var(--soft); display:flex; align-items:center; justify-content:center; font-size:1.05rem; }
    .ost-step.done .ost-box { background:#f4f7f5; }
    .ost-step.done .ost-ic { background:var(--brand); color:#fff; }
    .ost-step.done .ost-box { color:#3c6e55; }
    .ost-step.now .ost-box, .ost-step.now label { border-color:var(--brand); }
    .ost-step input:checked + label { border-color:var(--brand); background:var(--brand-soft); color:#146c43; box-shadow:0 0 0 2px rgba(25,135,84,.18); }
    .ost-step input:checked + label .ost-ic { background:var(--brand); color:#fff; }
    .ost-step input:focus-visible + label { box-shadow:0 0 0 .2rem rgba(25,135,84,.3); }
    .ost small { font-size:.72rem; font-weight:400; opacity:.85; }
    .ost-note { margin-top:.8rem; font-size:.86rem; color:var(--mute); }
    .ost-note.warn { background:#fff8e6; color:#7a5a00; border-radius:10px; padding:.55rem .75rem; }

    /* pelanggan terkunci */
    .ord-chosen { display:flex; align-items:center; gap:.8rem; background:var(--soft); border:1px solid var(--line); border-radius:14px; padding:.75rem .9rem; }
    .ord-avatar { width:42px; height:42px; border-radius:50%; background:var(--brand); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; flex:none; }
    .ord-chosen b { color:var(--ink); display:block; }
    .ord-chosen small { color:var(--mute); display:block; line-height:1.35; }
    .ord-lock { margin-left:auto; color:var(--mute); font-size:.8rem; display:flex; align-items:center; gap:.3rem; white-space:nowrap; }

    /* katalog paket */
    .ord-tools { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; margin-bottom:.8rem; }
    .ord-search { position:relative; flex:1 1 200px; }
    .ord-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .ord-search input { padding-left:2.2rem; }
    .ord-tabs { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:3px; }
    .ord-tabs button { border:0; background:transparent; padding:.3rem .8rem; border-radius:9px; color:var(--mute); font-size:.86rem; }
    .ord-tabs button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .ord-catalog { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:.7rem; max-height:340px; overflow-y:auto; padding:8px 8px 2px 2px; }
    .ord-pkg { position:relative; display:flex; gap:.7rem; align-items:center; border:1px solid var(--line); border-radius:14px; padding:.55rem; background:#fff; cursor:pointer; text-align:left; width:100%; }
    .ord-pkg:hover { border-color:#b9d8c7; }
    .ord-pkg.in { border-color:var(--brand); background:var(--brand-soft); }
    .ord-pkg-img { width:54px; height:54px; border-radius:11px; background:var(--soft); object-fit:cover; flex:none; display:flex; align-items:center; justify-content:center; color:var(--mute); }
    .ord-pkg b { display:block; color:var(--ink); font-size:.9rem; line-height:1.25; font-weight:600; }
    .ord-pkg small { color:var(--brand); font-weight:700; }
    .ord-pkg .qty { position:absolute; top:-7px; right:-7px; background:var(--brand); color:#fff; border-radius:999px; font-size:.75rem; font-weight:700; min-width:22px; height:22px; display:none; align-items:center; justify-content:center; padding:0 .35rem; box-shadow:0 2px 6px rgba(0,0,0,.2); }
    .ord-pkg.in .qty { display:flex; }
    .ord-empty { grid-column:1/-1; text-align:center; color:var(--mute); padding:1.5rem 0; }

    /* ringkasan */
    .ord-sum { position:sticky; top:88px; }
    .ord-recap { background:var(--soft); border-radius:12px; padding:.7rem .85rem; font-size:.88rem; margin-bottom:1rem; }
    .ord-recap div { display:flex; justify-content:space-between; gap:.75rem; padding:.15rem 0; }
    .ord-recap span { color:var(--mute); }
    .ord-recap b { color:var(--ink); text-align:right; font-weight:600; }
    .ord-cart { max-height:280px; overflow-y:auto; margin-bottom:.5rem; }
    .ord-line { padding:.7rem 0; border-top:1px solid var(--line); }
    .ord-line:first-child { border-top:0; }
    .ord-line-top { display:flex; justify-content:space-between; gap:.5rem; align-items:flex-start; }
    .ord-line-top b { color:var(--ink); font-weight:600; font-size:.92rem; line-height:1.3; }
    .ord-line-top button { border:0; background:none; color:#b0b8b3; padding:0 .15rem; }
    .ord-line-top button:hover { color:#dc3545; }
    .ord-line-bot { display:flex; justify-content:space-between; align-items:center; margin-top:.4rem; }
    .ord-step { display:inline-flex; align-items:center; border:1px solid var(--line); border-radius:10px; overflow:hidden; }
    .ord-step button { border:0; background:var(--soft); width:30px; height:30px; color:var(--ink); }
    .ord-step button:hover { background:var(--brand-soft); }
    .ord-step input { width:46px; text-align:center; border:0; height:30px; font-weight:600; -moz-appearance:textfield; }
    .ord-step input::-webkit-outer-spin-button, .ord-step input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
    .ord-step input:focus { outline:none; }
    .ord-line-sub { font-weight:700; color:var(--ink); }
    .ord-cart-empty { text-align:center; color:var(--mute); padding:1.2rem 0; font-size:.9rem; }
    .ord-cart-empty i { font-size:1.8rem; display:block; }
    .ord-total { border-top:1px dashed #cfd8d2; padding-top:.85rem; margin-top:.4rem; }
    .ord-total .row-l { display:flex; justify-content:space-between; color:var(--mute); font-size:.9rem; }
    .ord-total .big { display:flex; justify-content:space-between; align-items:baseline; margin-top:.3rem; }
    .ord-total .big strong { font-size:1.6rem; color:var(--brand); }
    .ord-err { background:#fdecee; color:#a52834; border-radius:10px; padding:.55rem .75rem; font-size:.88rem; margin-top:.9rem; display:none; }
    .ord-err.on { display:block; }

    @media (max-width:575px){ .ost { grid-template-columns:repeat(2,1fr); } }
</style>

<div class="container-fluid pb-3 ord">


    @if (session('error'))
        <div class="alert alert-danger border-0"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Periksa kembali data berikut:</b>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if ($missing->isNotEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Paket berikut sudah tidak tersedia dan <b>tidak ikut disimpan</b> jika kamu menyimpan perubahan:
            {{ $missing->implode(', ') }}.
        </div>
    @endif

    <form action="{{ route('orders.update', $order->id) }}" method="POST" id="editOrderForm" novalidate>
        @csrf
        @method('PUT')
        <div id="cartInputs"></div>

        <div class="row g-4">

            {{-- ================= KIRI ================= --}}
            <div class="col-xl-8">

                {{-- STATUS --}}
                <section class="ord-card">
                    <h5>Status pesanan</h5>
                    <p class="sub">Status hanya bisa maju ke tahap berikutnya, tidak bisa mundur.</p>

                    <div class="ost">
                        @foreach ($flow as $i => [$value, $label, $icon])
                            @php
                                $state = $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'now' : 'next');
                                $selectable = !$locked && $i >= 1 && $i >= $currentIdx;
                            @endphp
                            <div class="ost-step {{ $state }}">
                                @if ($selectable)
                                    <input type="radio" name="status" id="status{{ $i }}" value="{{ $value }}"
                                           data-label="{{ $label }}" {{ $selected === $value ? 'checked' : '' }}>
                                    <label for="status{{ $i }}">
                                        <span class="ost-ic"><i class="bi {{ $icon }}"></i></span>
                                        {{ $label }}
                                        @if ($i === $currentIdx)<small>Saat ini</small>@endif
                                    </label>
                                @else
                                    <div class="ost-box">
                                        <span class="ost-ic"><i class="bi {{ $state === 'done' ? 'bi-check-lg' : $icon }}"></i></span>
                                        {{ $label }}
                                        @if ($i === $currentIdx)<small>Saat ini</small>@endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($locked)
                        <div class="ost-note warn"><i class="bi bi-info-circle me-1"></i>Pesanan masih menunggu pembayaran. Status otomatis berubah ke Diproses setelah pembayaran diterima.</div>
                    @else
                        <div class="ost-note">
                            Metode: <b>{{ $isPickup ? 'Diambil sendiri oleh pelanggan' : 'Diantar ke alamat pelanggan' }}</b>.
                            Pilih <b>{{ $isPickup ? 'Siap diambil' : 'Siap diantar' }}</b> saat pesanan sudah selesai disiapkan, dan <b>Selesai</b> setelah diterima pelanggan.
                        </div>
                    @endif
                </section>

                {{-- PELANGGAN & JADWAL --}}
                <section class="ord-card">
                    <h5>Pelanggan dan jadwal</h5>
                    <p class="sub">Pelanggan tidak bisa diganti. Buat pesanan baru jika pemesannya berbeda.</p>

                    <div class="ord-chosen mb-3">
                        <div class="ord-avatar">{{ strtoupper(mb_substr($customer->name ?? '?', 0, 1)) }}</div>
                        <div class="min-w-0">
                            <b>{{ $customer->name ?? 'Pelanggan tidak ditemukan' }}</b>
                            <small>{{ $customer->phone ?? 'Belum ada nomor telepon' }}</small>
                            <small>{{ $customer->address ?? 'Alamat belum tersedia' }}</small>
                        </div>
                        <span class="ord-lock"><i class="bi bi-lock-fill"></i>Terkunci</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="orderDate">Tanggal pesanan</label>
                            <input type="date" id="orderDate" name="order_date" class="form-control" value="{{ $orderDate }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="deliveryDate">Tanggal {{ $isPickup ? 'pengambilan' : 'pengiriman' }}</label>
                            <input type="date" id="deliveryDate" name="delivery_date" class="form-control" min="{{ $orderDate }}" value="{{ $deliveryDate }}">
                        </div>
                    </div>
                </section>

                {{-- PAKET --}}
                <section class="ord-card">
                    <h5>Paket pesanan</h5>
                    <p class="sub">Klik paket untuk menambahkannya. Ubah jumlah lewat ringkasan di kanan.</p>

                    @if ($packages->isEmpty())
                        <div class="alert alert-warning mb-0">Belum ada paket yang tersedia.</div>
                    @else
                        <div class="ord-tools">
                            <div class="ord-search">
                                <i class="bi bi-search"></i>
                                <input type="search" id="pkgSearch" class="form-control" placeholder="Cari paket" aria-label="Cari paket">
                            </div>
                            <div class="ord-tabs" id="pkgTabs" hidden>
                                <button type="button" class="on" data-type="all">Semua</button>
                                <button type="button" data-type="paket">Paket</button>
                                <button type="button" data-type="prasmanan">Prasmanan</button>
                            </div>
                        </div>
                        <div class="ord-catalog" id="pkgCatalog"></div>
                    @endif
                </section>

                {{-- CATATAN --}}
                <section class="ord-card mb-0">
                    <h5>Catatan</h5>
                    <p class="sub">Opsional.</p>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan untuk pesanan ini">{{ old('notes', $order->notes) }}</textarea>
                </section>
            </div>

            {{-- ================= KANAN ================= --}}
            <div class="col-xl-4">
                <div class="ord-sum">
                    <section class="ord-card mb-0">
                        <h5>Ringkasan pesanan <span class="text-muted fw-normal">#{{ $order->order_code ?? $order->id }}</span></h5>
                        <p class="sub">Periksa sebelum menyimpan.</p>

                        <div class="ord-recap">
                            <div><span>Pelanggan</span><b>{{ $customer->name ?? '-' }}</b></div>
                            <div><span>Status</span><b id="rcStatus">{{ $locked ? 'Menunggu pembayaran' : collect($flow)->firstWhere(0, $selected)[1] ?? $order->status }}</b></div>
                            <div><span>{{ $isPickup ? 'Pengambilan' : 'Pengiriman' }}</span><b id="rcDate">-</b></div>
                        </div>

                        <div class="ord-cart" id="cartList"></div>

                        <div class="ord-total">
                            <div class="row-l"><span>Subtotal paket</span><span id="subTotal">Rp0</span></div>
                            <div class="row-l"><span>Ongkos kirim</span><span id="shipTotal">Rp0</span></div>
                            <div class="big"><span class="text-muted">Total</span><strong id="grandTotal">Rp0</strong></div>
                        </div>

                        <div class="ord-err" id="ordErr" role="alert"></div>

                        <button type="submit" class="btn btn-success w-100 mt-3 py-2">
                            <i class="bi bi-check2-circle me-1"></i>Simpan perubahan
                        </button>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-light border w-100 mt-2">Batal</a>
                    </section>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const packages    = @json($packagesJson);
    const initial     = @json(array_values($initialItems));
    const storage     = @json(asset('storage'));
    const shippingFee = Number(@json((float) ($order->delivery_fee ?? 0)));
    const statusLocked = @json($locked);
    const $ = id => document.getElementById(id);

    const rupiah = v => 'Rp' + Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    function el(tag, cls, text) {
        const e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    }

    /* ---------- TANGGAL & STATUS (ringkasan) ---------- */
    const orderDate = $('orderDate'), deliveryDate = $('deliveryDate');

    function recap() {
        deliveryDate.min = orderDate.value || '';
        $('rcDate').textContent = deliveryDate.value
            ? new Date(deliveryDate.value + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
            : 'Belum diisi';
        const checked = document.querySelector('input[name="status"]:checked');
        if (checked) $('rcStatus').textContent = checked.dataset.label;
    }
    orderDate.addEventListener('input', recap);
    deliveryDate.addEventListener('input', recap);
    document.querySelectorAll('input[name="status"]').forEach(r => r.addEventListener('change', recap));

    /* ---------- KATALOG & KERANJANG ---------- */
    const cart = new Map();
    const catalog = $('pkgCatalog');
    const pkgSearch = $('pkgSearch');
    let typeFilter = 'all';

    const hasTypes = new Set(packages.map(p => p.type).filter(Boolean)).size > 1;
    if ($('pkgTabs')) $('pkgTabs').hidden = !hasTypes;

    function addToCart(id) { cart.set(id, Math.min(9999, (cart.get(id) || 0) + 1)); refresh(); }
    function setQty(id, q) { if (q < 1) cart.delete(id); else cart.set(id, Math.min(9999, q)); refresh(); }

    function renderCatalog() {
        if (!catalog) return;
        const q = pkgSearch.value.trim().toLowerCase();
        catalog.innerHTML = '';
        const shown = packages.filter(p => (typeFilter === 'all' || p.type === typeFilter) && (!q || p.name.toLowerCase().includes(q)));
        shown.forEach(p => {
            const card = el('button', 'ord-pkg' + (cart.has(p.id) ? ' in' : ''));
            card.type = 'button';
            let thumb;
            if (p.image) { thumb = el('img', 'ord-pkg-img'); thumb.src = storage + '/' + p.image; thumb.alt = ''; }
            else { thumb = el('span', 'ord-pkg-img'); thumb.innerHTML = '<i class="bi bi-box-seam"></i>'; }
            const info = el('span');
            info.append(el('b', '', p.name), el('small', '', rupiah(p.price)));
            card.append(thumb, info, el('span', 'qty', cart.get(p.id) || ''));
            card.addEventListener('click', () => addToCart(p.id));
            catalog.appendChild(card);
        });
        if (!shown.length) catalog.appendChild(el('div', 'ord-empty', 'Paket tidak ditemukan.'));
    }
    if (pkgSearch) pkgSearch.addEventListener('input', renderCatalog);
    document.querySelectorAll('#pkgTabs button').forEach(b => b.addEventListener('click', () => {
        typeFilter = b.dataset.type;
        document.querySelectorAll('#pkgTabs button').forEach(x => x.classList.toggle('on', x === b));
        renderCatalog();
    }));

    function renderCart() {
        const box = $('cartList'), inputs = $('cartInputs');
        box.innerHTML = ''; inputs.innerHTML = '';
        let total = 0, i = 0;

        if (!cart.size) {
            const empty = el('div', 'ord-cart-empty');
            empty.innerHTML = '<i class="bi bi-basket"></i>';
            empty.append('Belum ada paket dipilih.');
            box.appendChild(empty);
        }

        cart.forEach((qty, id) => {
            const p = packages.find(x => x.id === id);
            if (!p) return;
            const sub = p.price * qty;
            total += sub;

            const line = el('div', 'ord-line');
            const top = el('div', 'ord-line-top');
            const rm = el('button'); rm.type = 'button'; rm.title = 'Hapus'; rm.setAttribute('aria-label', 'Hapus ' + p.name);
            rm.innerHTML = '<i class="bi bi-x-lg"></i>';
            rm.addEventListener('click', () => setQty(id, 0));
            top.append(el('b', '', p.name), rm);

            const bot = el('div', 'ord-line-bot');
            const step = el('div', 'ord-step');
            const minus = el('button', '', '−'); minus.type = 'button'; minus.setAttribute('aria-label', 'Kurangi');
            const plus = el('button', '', '+'); plus.type = 'button'; plus.setAttribute('aria-label', 'Tambah');
            const num = el('input'); num.type = 'number'; num.min = 1; num.value = qty; num.setAttribute('aria-label', 'Jumlah');
            minus.addEventListener('click', () => setQty(id, qty - 1));
            plus.addEventListener('click', () => setQty(id, qty + 1));
            num.addEventListener('change', () => setQty(id, parseInt(num.value) || 0));
            step.append(minus, num, plus);
            bot.append(step, el('span', 'ord-line-sub', rupiah(sub)));

            line.append(top, bot);
            box.appendChild(line);

            [['package_id', id], ['quantity', qty]].forEach(([k, v]) => {
                const h = document.createElement('input');
                h.type = 'hidden'; h.name = `packages[${i}][${k}]`; h.value = v;
                inputs.appendChild(h);
            });
            i++;
        });

        $('subTotal').textContent = rupiah(total);
        $('shipTotal').textContent = rupiah(shippingFee);
        $('grandTotal').textContent = rupiah(total + shippingFee);
    }

    function refresh() { renderCart(); renderCatalog(); $('ordErr').classList.remove('on'); }

    /* ---------- AWAL ---------- */
    (initial || []).forEach(it => {
        const id = Number(it.package_id), q = Number(it.quantity);
        if (packages.some(p => p.id === id) && q > 0) cart.set(id, q);
    });
    recap();
    refresh();

    /* ---------- VALIDASI ---------- */
    $('editOrderForm').addEventListener('submit', function (e) {
        let msg = '', target = null;
        if (!orderDate.value) { msg = 'Tanggal pesanan wajib diisi.'; target = orderDate; }
        else if (!deliveryDate.value) { msg = 'Tanggal pengiriman/pengambilan wajib diisi.'; target = deliveryDate; }
        else if (deliveryDate.value < orderDate.value) { msg = 'Tanggal pengiriman tidak boleh sebelum tanggal pesanan.'; target = deliveryDate; }
        else if (!cart.size) { msg = 'Tambahkan minimal satu paket.'; target = catalog; }

        if (msg) {
            e.preventDefault();
            const box = $('ordErr');
            box.textContent = msg;
            box.classList.add('on');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (target.focus) target.focus({ preventScroll: true });
            }
        }
    });
});
</script>
@endpush