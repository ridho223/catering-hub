@extends('layouts.admin')

@section('title', 'Tambah Pesanan')

@section('content')
@php
    $customersJson = $customers->map(fn ($c) => [
        'id'      => $c->id,
        'name'    => $c->name,
        'phone'   => $c->phone ?? '',
        'address' => $c->address ?? '',
    ])->values();

    $packagesJson = $packages->map(fn ($p) => [
        'id'    => $p->id,
        'name'  => $p->name,
        'price' => (float) $p->price,
        'type'  => $p->package_type ?? null,
        'image' => $p->image ?? null,
    ])->values();
@endphp

<style>
    .ord { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-soft:#e3f4ea; }
    .ord-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.25rem; margin-bottom:1.25rem; }
    .ord-card h5 { margin:0; font-weight:700; color:var(--ink); font-size:1.05rem; }
    .ord-card .sub { color:var(--mute); font-size:.87rem; margin:.1rem 0 1rem; }
    .ord .form-label { font-weight:600; color:var(--ink); margin-bottom:.3rem; font-size:.9rem; }
    .ord .form-control, .ord .form-select { border-color:var(--line); border-radius:10px; }
    .ord .form-control:focus { border-color:var(--brand); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }

    /* toggle pelanggan */
    .ord-seg { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:4px; gap:2px; margin-bottom:1rem; }
    .ord-seg button { border:0; background:transparent; padding:.4rem .95rem; border-radius:9px; color:var(--mute); font-weight:500; font-size:.9rem; }
    .ord-seg button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }

    /* combobox pelanggan */
    .ord-combo { position:relative; }
    .ord-combo > i { position:absolute; left:.85rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .ord-combo input { padding-left:2.3rem; }
    .ord-list { position:absolute; left:0; right:0; top:calc(100% + 6px); background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:0 12px 28px rgba(0,0,0,.1); max-height:280px; overflow-y:auto; z-index:20; list-style:none; margin:0; padding:.35rem; display:none; }
    .ord-list.open { display:block; }
    .ord-list li { padding:.55rem .7rem; border-radius:10px; cursor:pointer; }
    .ord-list li:hover, .ord-list li.hl { background:var(--soft); }
    .ord-list li b { display:block; color:var(--ink); font-weight:600; }
    .ord-list li small { color:var(--mute); }
    .ord-list li.add { color:var(--brand); font-weight:600; border-top:1px solid var(--line); border-radius:0 0 10px 10px; margin-top:.25rem; }
    .ord-chosen { display:flex; align-items:center; gap:.8rem; background:var(--brand-soft); border-radius:14px; padding:.75rem .9rem; }
    .ord-avatar { width:40px; height:40px; border-radius:50%; background:var(--brand); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; flex:none; }
    .ord-chosen b { color:var(--ink); display:block; }
    .ord-chosen small { color:#3c6e55; display:block; line-height:1.35; }
    .ord-hint { color:var(--mute); font-size:.82rem; margin-top:.4rem; }
    .ord-newinfo { background:#fff8e6; color:#7a5a00; border-radius:10px; padding:.5rem .75rem; font-size:.84rem; margin-top:.75rem; }

    .ord-quick { display:flex; gap:.35rem; flex-wrap:wrap; margin-top:.45rem; }
    .ord-quick button { border:1px solid var(--line); background:#fff; border-radius:999px; font-size:.78rem; color:var(--mute); padding:.15rem .65rem; }
    .ord-quick button:hover { border-color:var(--brand); color:var(--brand); }

    /* katalog paket */
    .ord-tools { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; margin-bottom:.8rem; }
    .ord-search { position:relative; flex:1 1 200px; }
    .ord-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .ord-search input { padding-left:2.2rem; }
    .ord-tabs { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:3px; }
    .ord-tabs button { border:0; background:transparent; padding:.3rem .8rem; border-radius:9px; color:var(--mute); font-size:.86rem; }
    .ord-tabs button.on { background:#fff; color:var(--brand); box-shadow:0 1px 3px rgba(0,0,0,.08); }

    .ord-catalog { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:.7rem; max-height:360px; overflow-y:auto; padding:2px 4px 2px 2px; }
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

    .ord-cart { max-height:300px; overflow-y:auto; margin-bottom:.5rem; }
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
</style>

<div class="container-fluid pb-3 ord">


    @if (session('error'))
        <div class="alert alert-danger border-0"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <b>Data belum valid.</b>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm" novalidate>
        @csrf
        <input type="hidden" name="customer_mode" id="custMode" value="{{ old('customer_mode', 'existing') }}">
        <input type="hidden" name="customer_id" id="custId" value="{{ old('customer_id') }}">
        <div id="cartInputs"></div>

        <div class="row g-4">

            {{-- ================= KIRI ================= --}}
            <div class="col-xl-8">

                {{-- PELANGGAN & JADWAL --}}
                <section class="ord-card">
                    <h5>Pelanggan dan jadwal</h5>
                    <p class="sub">Belum terdaftar? Buat pelanggan baru langsung dari sini.</p>

                    <div class="ord-seg" id="custTabs">
                        <button type="button" data-mode="existing"><i class="bi bi-person-check me-1"></i>Pelanggan terdaftar</button>
                        <button type="button" data-mode="new"><i class="bi bi-person-plus me-1"></i>Pelanggan baru</button>
                    </div>

                    {{-- terdaftar --}}
                    <div id="paneExisting">
                        <div id="custSearchWrap">
                            <div class="ord-combo">
                                <i class="bi bi-search"></i>
                                <input type="text" id="custSearch" class="form-control" placeholder="Cari nama atau nomor telepon" autocomplete="off" aria-label="Cari pelanggan">
                                <ul class="ord-list" id="custList" role="listbox"></ul>
                            </div>
                            <div class="ord-hint">{{ $customers->count() }} pelanggan terdaftar.</div>
                        </div>

                        <div class="ord-chosen" id="custChosen" hidden>
                            <div class="ord-avatar" id="custAvatar">A</div>
                            <div class="flex-grow-1 min-w-0">
                                <b id="custName">-</b>
                                <small id="custPhone">-</small>
                                <small id="custAddress">-</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" id="custChange">Ganti</button>
                        </div>
                    </div>

                    {{-- baru --}}
                    <div id="paneNew" hidden>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ncName">Nama pelanggan</label>
                                <input type="text" id="ncName" name="new_customer[name]" value="{{ old('new_customer.name') }}" class="form-control" placeholder="Nama lengkap">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ncPhone">Nomor telepon / WhatsApp</label>
                                <input type="tel" id="ncPhone" name="new_customer[phone]" value="{{ old('new_customer.phone') }}" class="form-control" placeholder="08xxxxxxxxxx" inputmode="tel">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="ncAddress">Alamat <span class="text-muted fw-normal">(opsional)</span></label>
                                <textarea id="ncAddress" name="new_customer[address]" class="form-control" rows="2" placeholder="Alamat pengiriman">{{ old('new_customer.address') }}</textarea>
                            </div>
                        </div>
                        <div class="ord-newinfo"><i class="bi bi-info-circle me-1"></i>Pelanggan baru otomatis tersimpan saat pesanan disimpan.</div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label" for="orderDate">Tanggal pesanan</label>
                            <input type="date" id="orderDate" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="deliveryDate">Tanggal acara / pengiriman</label>
                            <input type="date" id="deliveryDate" name="delivery_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('delivery_date') }}">
                            <div class="ord-quick" id="quickDates">
                                <button type="button" data-days="1">Besok</button>
                                <button type="button" data-days="3">3 hari lagi</button>
                                <button type="button" data-days="7">Seminggu lagi</button>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PAKET --}}
                <section class="ord-card">
                    <h5>Pilih paket</h5>
                    <p class="sub">Klik paket untuk menambahkannya. Klik lagi untuk menambah jumlah.</p>

                    @if ($packages->isEmpty())
                        <div class="alert alert-warning mb-0">
                            Belum ada paket tersedia dengan menu yang memiliki stok. Periksa data paket dan stok menu terlebih dahulu.
                        </div>
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
                    <p class="sub">Opsional. Contoh: sambal dipisah, antar sebelum jam 10.</p>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Tulis catatan untuk pesanan ini">{{ old('notes') }}</textarea>
                </section>
            </div>

            {{-- ================= KANAN ================= --}}
            <div class="col-xl-4">
                <div class="ord-sum">
                    <section class="ord-card mb-0">
                        <h5>Ringkasan pesanan</h5>
                        <p class="sub">Periksa sebelum menyimpan.</p>

                        <div class="ord-recap">
                            <div><span>Pelanggan</span><b id="rcCustomer">Belum dipilih</b></div>
                            <div><span>Tanggal acara</span><b id="rcDate">Belum diisi</b></div>
                        </div>

                        <div class="ord-cart" id="cartList"></div>

                        <div class="ord-total">
                            <div class="row-l"><span>Jumlah porsi/paket</span><span id="totQty">0</span></div>
                            <div class="row-l"><span>Ongkos kirim</span><span>Diatur di detail pesanan</span></div>
                            <div class="big"><span class="text-muted">Subtotal</span><strong id="grandTotal">Rp0</strong></div>
                        </div>

                        <div class="ord-err" id="ordErr" role="alert"></div>

                        <button type="submit" class="btn btn-success w-100 mt-3 py-2" @disabled($packages->isEmpty())>
                            <i class="bi bi-check2-circle me-1"></i>Simpan pesanan
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-light border w-100 mt-2">Batal</a>
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
    const customers = @json($customersJson);
    const packages  = @json($packagesJson);
    const oldItems  = @json(old('packages', []));
    const storage   = @json(asset('storage'));
    const $ = id => document.getElementById(id);

    const rupiah = v => 'Rp' + Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    function el(tag, cls, text) {
        const e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    }

    /* ---------- PELANGGAN ---------- */
    const modeInput = $('custMode'), idInput = $('custId');
    const paneExisting = $('paneExisting'), paneNew = $('paneNew');
    const search = $('custSearch'), list = $('custList');
    let mode = modeInput.value === 'new' ? 'new' : 'existing';

    function setMode(m) {
        mode = m;
        modeInput.value = m;
        document.querySelectorAll('#custTabs button').forEach(b => b.classList.toggle('on', b.dataset.mode === m));
        paneExisting.hidden = m !== 'existing';
        paneNew.hidden = m !== 'new';
        recap();
    }
    document.querySelectorAll('#custTabs button').forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));

    function selectCustomer(c) {
        idInput.value = c ? c.id : '';
        $('custSearchWrap').hidden = !!c;
        $('custChosen').hidden = !c;
        if (c) {
            $('custAvatar').textContent = (c.name || '?').trim().charAt(0).toUpperCase();
            $('custName').textContent = c.name;
            $('custPhone').textContent = c.phone || 'Belum ada nomor telepon';
            $('custAddress').textContent = c.address || 'Alamat belum tersedia';
        }
        list.classList.remove('open');
        recap();
    }

    function renderList() {
        const q = search.value.trim().toLowerCase();
        const found = customers.filter(c => !q || c.name.toLowerCase().includes(q) || (c.phone || '').includes(q)).slice(0, 8);
        list.innerHTML = '';
        found.forEach(c => {
            const li = el('li');
            li.append(el('b', '', c.name), el('small', '', c.phone || 'Tanpa nomor telepon'));
            li.addEventListener('mousedown', e => { e.preventDefault(); selectCustomer(c); });
            list.appendChild(li);
        });
        if (!found.length) list.appendChild(el('li', '', 'Pelanggan tidak ditemukan.'));
        const add = el('li', 'add');
        add.innerHTML = '<i class="bi bi-person-plus me-1"></i>';
        add.append(q ? `Buat pelanggan baru "${search.value.trim()}"` : 'Buat pelanggan baru');
        add.addEventListener('mousedown', e => {
            e.preventDefault();
            if (q && !/^[\d+\s-]+$/.test(q)) $('ncName').value = search.value.trim();
            else if (q) $('ncPhone').value = search.value.trim();
            list.classList.remove('open');
            setMode('new');
            $('ncName').focus();
        });
        list.appendChild(add);
        list.classList.add('open');
    }
    search.addEventListener('focus', renderList);
    search.addEventListener('input', renderList);
    search.addEventListener('blur', () => setTimeout(() => list.classList.remove('open'), 120));
    $('custChange').addEventListener('click', () => { selectCustomer(null); search.value = ''; search.focus(); });

    ['ncName', 'ncPhone'].forEach(id => $(id).addEventListener('input', recap));

    /* ---------- TANGGAL ---------- */
    const deliveryDate = $('deliveryDate');
    deliveryDate.addEventListener('input', recap);
    document.querySelectorAll('#quickDates button').forEach(b => b.addEventListener('click', () => {
        const d = new Date();
        d.setDate(d.getDate() + Number(b.dataset.days));
        const pad = n => String(n).padStart(2, '0');
        deliveryDate.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        recap();
    }));

    function recap() {
        let name = 'Belum dipilih';
        if (mode === 'existing') {
            const c = customers.find(x => String(x.id) === String(idInput.value));
            if (c) name = c.name;
        } else if ($('ncName').value.trim()) {
            name = $('ncName').value.trim() + ' (baru)';
        }
        $('rcCustomer').textContent = name;

        $('rcDate').textContent = deliveryDate.value
            ? new Date(deliveryDate.value + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
            : 'Belum diisi';
    }

    /* ---------- KATALOG & KERANJANG ---------- */
    const cart = new Map(); // package id -> qty
    const catalog = $('pkgCatalog');
    const pkgSearch = $('pkgSearch');
    let typeFilter = 'all';

    const hasTypes = new Set(packages.map(p => p.type).filter(Boolean)).size > 1;
    if ($('pkgTabs')) $('pkgTabs').hidden = !hasTypes;

    function addToCart(id, n = 1) {
        cart.set(id, Math.min(9999, (cart.get(id) || 0) + n));
        refresh();
    }
    function setQty(id, q) {
        if (q < 1) cart.delete(id); else cart.set(id, Math.min(9999, q));
        refresh();
    }

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
        let total = 0, qtyAll = 0, i = 0;

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
            total += sub; qtyAll += qty;

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

        $('totQty').textContent = qtyAll;
        $('grandTotal').textContent = rupiah(total);
    }

    function refresh() { renderCart(); renderCatalog(); $('ordErr').classList.remove('on'); }

    /* ---------- AWAL ---------- */
    (oldItems || []).forEach(it => {
        const id = Number(it.package_id), q = Number(it.quantity);
        if (packages.some(p => p.id === id) && q > 0) cart.set(id, q);
    });
    const preset = customers.find(c => String(c.id) === String(idInput.value));
    if (preset) selectCustomer(preset);
    setMode(mode);
    refresh();

    /* ---------- VALIDASI ---------- */
    $('orderForm').addEventListener('submit', function (e) {
        let msg = '', target = null;
        if (mode === 'existing' && !idInput.value) { msg = 'Pilih pelanggan terlebih dahulu, atau buat pelanggan baru.'; target = search; }
        else if (mode === 'new' && !$('ncName').value.trim()) { msg = 'Nama pelanggan baru wajib diisi.'; target = $('ncName'); }
        else if (mode === 'new' && !$('ncPhone').value.trim()) { msg = 'Nomor telepon pelanggan baru wajib diisi.'; target = $('ncPhone'); }
        else if (!$('orderDate').value) { msg = 'Tanggal pesanan wajib diisi.'; target = $('orderDate'); }
        else if (!deliveryDate.value) { msg = 'Tanggal acara wajib diisi.'; target = deliveryDate; }
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