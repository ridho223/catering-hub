@extends('layouts.admin')

@section('title', 'Customer')

@section('content')
@php
    use Illuminate\Support\Str;

    $items = $customers instanceof \Illuminate\Pagination\AbstractPaginator
        ? $customers->getCollection()
        : collect($customers);

    // Jumlah pesanan tampil jika controller memakai ->withCount('orders')
    $hasOrders = $items->isNotEmpty() && array_key_exists('orders_count', $items->first()->getAttributes());
@endphp

<style>
    .cst { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; }
    .cst-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .cst-top p { margin:0; color:var(--mute); }

    .cst-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .cst-search { position:relative; flex:1 1 240px; max-width:380px; }
    .cst-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:var(--mute); }
    .cst-search input { padding-left:2.2rem; border-radius:12px; border-color:var(--line); }
    .cst-count { background:var(--soft); border:1px solid var(--line); border-radius:999px; padding:.3rem .85rem; color:var(--mute); font-size:.86rem; }

    .cst-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .cst-table { width:100%; border-collapse:collapse; }
    .cst-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.82rem; padding:.7rem 1rem; text-align:left; white-space:nowrap; }
    .cst-table td { padding:.8rem 1rem; border-top:1px solid var(--line); vertical-align:middle; }
    .cst-table tbody tr:hover { background:#fafcfa; }
    .cst-person { display:flex; align-items:center; gap:.75rem; }
    .cst-av { width:40px; height:40px; border-radius:50%; background:#e3f4ea; color:#146c43; font-weight:700; display:flex; align-items:center; justify-content:center; flex:none; }
    .cst-person b { color:var(--ink); font-weight:600; }
    .cst-line { display:block; font-size:.9rem; color:var(--ink); }
    .cst-sub { display:block; font-size:.82rem; color:var(--mute); }
    .cst-addr { color:var(--mute); font-size:.88rem; max-width:320px; }
    .cst-chip { display:inline-flex; background:#e2f0fb; color:#0b5a8a; border-radius:999px; padding:.2rem .7rem; font-size:.8rem; font-weight:600; white-space:nowrap; }
    .cst-act { display:flex; gap:.35rem; justify-content:flex-end; }
    .cst-act .btn { --bs-btn-padding-x:.55rem; --bs-btn-padding-y:.3rem; border-radius:10px; }

    .cst-empty { text-align:center; padding:3rem 1rem; color:var(--mute); }
    .cst-empty i { font-size:2.5rem; }
    #cstNoResult { display:none; }

    .cst-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .cst-pg nav .flex-sm-fill > div:first-child { display:none; }
    .cst-pg .pagination { margin:0; }
    .cst-pg .pagination .page-link { color:#267548; border-color:var(--line); }
    .cst-pg .pagination .active .page-link { background:var(--brand); border-color:var(--brand); color:#fff; }

    @media (max-width:767px){
        .cst-table thead { display:none; }
        .cst-table, .cst-table tbody, .cst-table tr, .cst-table td { display:block; width:100%; }
        .cst-table tr { padding:.6rem 0; border-top:1px solid var(--line); }
        .cst-table td { border:0; padding:.3rem 1rem; }
        .cst-act { justify-content:flex-start; }
    }
</style>

<div class="container-fluid cst">

    <div class="cst-top">
        <p>Data pelanggan yang bisa dipilih saat membuat pesanan.</p>
        <a href="{{ route('customers.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah customer
        </a>
    </div>

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

    @if ($items->isNotEmpty())
        <div class="cst-bar">
            <div class="cst-search">
                <i class="bi bi-search"></i>
                <input type="search" id="cstSearch" class="form-control" placeholder="Cari nama, telepon, email, atau alamat" aria-label="Cari customer">
            </div>
            <span class="cst-count">{{ method_exists($customers, 'total') ? $customers->total() : $items->count() }} customer</span>
        </div>
    @endif

    <div class="cst-wrap">
        @if ($items->isEmpty())
            <div class="cst-empty">
                <i class="bi bi-people"></i>
                <h5 class="mt-2 mb-1 text-dark">Belum ada customer</h5>
                <p>Tambahkan customer pertama, atau buat langsung saat membuat pesanan.</p>
                <a href="{{ route('customers.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah customer</a>
            </div>
        @else
            <table class="cst-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        @if ($hasOrders)<th>Pesanan</th>@endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $customer)
                    @php
                        $digits = preg_replace('/\D/', '', $customer->phone ?? '');
                        $wa = $digits ? (str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits) : null;
                    @endphp
                    <tr class="cst-item" data-search="{{ Str::lower($customer->name . ' ' . $customer->phone . ' ' . $customer->email . ' ' . $customer->address) }}">
                        <td>
                            <div class="cst-person">
                                <span class="cst-av">{{ strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                                <b>{{ $customer->name }}</b>
                            </div>
                        </td>
                        <td>
                            <span class="cst-line">{{ $customer->phone ?: '-' }}</span>
                            <span class="cst-sub">{{ $customer->email ?: 'Tanpa email' }}</span>
                        </td>
                        <td class="cst-addr" title="{{ $customer->address }}">{{ Str::limit($customer->address ?: '-', 70) }}</td>
                        @if ($hasOrders)
                            <td><span class="cst-chip">{{ $customer->orders_count }} pesanan</span></td>
                        @endif
                        <td>
                            <div class="cst-act">
                                @if ($wa)
                                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success" title="Chat WhatsApp" aria-label="Chat WhatsApp {{ $customer->name }}"><i class="bi bi-whatsapp"></i></a>
                                @endif
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $customer->name }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus customer {{ e($customer->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" aria-label="Hapus {{ $customer->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="cst-empty" id="cstNoResult">
                <i class="bi bi-search"></i>
                <h6 class="mt-2 mb-1 text-dark">Customer tidak ditemukan</h6>
                <p class="mb-0">Coba kata kunci lain.</p>
            </div>
        @endif
    </div>

    @if (method_exists($customers, 'links'))
        <div class="cst-pg mt-3">{{ $customers->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
(function () {
    const input = document.getElementById('cstSearch');
    if (!input) return;
    const rows = document.querySelectorAll('.cst-item');
    const none = document.getElementById('cstNoResult');
    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const ok = !q || r.dataset.search.includes(q);
            r.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        none.style.display = visible ? 'none' : 'block';
    });
})();
</script>
@endsection