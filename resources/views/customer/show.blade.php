@extends('layouts.admin')

@section('title', 'Detail Customer')

@section('content')
@php
    $digits = preg_replace('/\D/', '', $customer->phone ?? '');
    $wa     = $digits ? (str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits) : null;
    $maps   = $customer->address ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($customer->address) : null;
    $avg    = ($stats['valid'] ?? 0) > 0 ? $stats['spent'] / $stats['valid'] : 0;

    $statusLabel = [
        'Pending'                   => 'Menunggu bayar',
        'Diproses'                  => 'Diproses',
        'Siap_diambil_atau_diantar' => 'Siap',
        'Selesai'                   => 'Selesai',
        'Dibatalkan'                => 'Dibatalkan',
    ];
    $statusChip = [
        'Pending'                   => 'warn',
        'Diproses'                  => 'info',
        'Siap_diambil_atau_diantar' => 'ready',
        'Selesai'                   => 'ok',
        'Dibatalkan'                => 'bad',
    ];
@endphp

<style>
    .csd { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --brand:#198754; --brand-dark:#126640; }
    .csd-back { display:inline-flex; align-items:center; gap:.4rem; color:var(--mute); text-decoration:none; font-size:.9rem; font-weight:600; }
    .csd-back:hover { color:var(--brand-dark); }

    .csd-head { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
    .csd-av { width:64px; height:64px; border-radius:50%; background:#e3f4ea; color:#146c43; font-weight:700; font-size:1.6rem; display:grid; place-items:center; flex:none; }
    .csd-head h1 { margin:0; font-size:1.3rem; font-weight:700; color:var(--ink); letter-spacing:-.01em; }
    .csd-meta { display:flex; flex-wrap:wrap; gap:.4rem .5rem; align-items:center; margin-top:.35rem; color:var(--mute); font-size:.84rem; }
    .csd-actions { margin-left:auto; display:flex; gap:.5rem; flex-wrap:wrap; }
    .csd-actions .btn { border-radius:12px; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; }

    .csd-list { list-style:none; margin:0; padding:0; }
    .csd-list li { display:flex; gap:.8rem; padding:.8rem 0; border-top:1px solid var(--line); align-items:flex-start; }
    .csd-list li:first-child { border-top:0; padding-top:0; }
    .csd-list .ic { width:38px; height:38px; border-radius:12px; display:grid; place-items:center; flex:none; font-size:1rem; }
    .csd-list small { display:block; color:var(--mute); font-size:.76rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.1rem; }
    .csd-list .v, .csd-list a.v { color:var(--ink); font-size:.94rem; text-decoration:none; word-break:break-word; }
    .csd-list a.v:hover { color:var(--brand); text-decoration:underline; }
    .csd-list .v.empty { color:#a3afa8; font-style:italic; }

    @media (max-width:575.98px) {
        .csd-actions { width:100%; margin-left:0; }
        .csd-actions .btn { flex:1; justify-content:center; }
    }
</style>

<div class="container-fluid pb-3 csd">
    <div class="ui-page">

        <div>
            <a href="{{ route('customers.index') }}" class="csd-back"><i class="bi bi-arrow-left"></i>Kembali ke daftar customer</a>
        </div>

        {{-- Identitas --}}
        <section class="ui-card csd-head">
            <div class="csd-av">{{ strtoupper(mb_substr($customer->name, 0, 1)) }}</div>

            <div>
                <h1>{{ $customer->name }}</h1>
                <div class="csd-meta">
                    @if ($customer->account)
                        @if ($customer->account->hasVerifiedEmail())
                            <span class="ui-chip ok"><i class="bi bi-patch-check-fill"></i>Akun terverifikasi</span>
                        @else
                            <span class="ui-chip warn">Akun belum verifikasi</span>
                        @endif
                    @else
                        <span class="ui-chip off">Tanpa akun login</span>
                    @endif
                    <span>Terdaftar {{ $customer->created_at?->format('d M Y') ?? '-' }}</span>
                </div>
            </div>

            <div class="csd-actions">
                @if ($wa)
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success">
                        <i class="bi bi-whatsapp"></i>WhatsApp
                    </a>
                @endif
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-success">
                    <i class="bi bi-pencil"></i>Edit
                </a>
            </div>
        </section>

        {{-- Ringkasan --}}
        <div class="row g-3">
            <div class="col-6 col-xl-3">
                <div class="ui-card ui-kpi">
                    <span class="ui-ic t-green"><i class="bi bi-receipt"></i></span>
                    <div><small>Total pesanan</small><strong>{{ $stats['total'] }}</strong></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="ui-card ui-kpi">
                    <span class="ui-ic t-amber"><i class="bi bi-hourglass-split"></i></span>
                    <div><small>Sedang berjalan</small><strong>{{ $stats['active'] }}</strong></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="ui-card ui-kpi">
                    <span class="ui-ic t-blue"><i class="bi bi-check2-circle"></i></span>
                    <div><small>Selesai</small><strong>{{ $stats['done'] }}</strong></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="ui-card ui-kpi">
                    <span class="ui-ic t-purple"><i class="bi bi-wallet2"></i></span>
                    <div>
                        <small>Total belanja</small>
                        <strong>Rp{{ number_format($stats['spent'], 0, ',', '.') }}</strong>
                        @if ($avg > 0)<span class="sub">Rata-rata Rp{{ number_format($avg, 0, ',', '.') }} / pesanan</span>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- Kontak --}}
            <div class="col-lg-4">
                <section class="ui-card">
                    <div class="ui-head">
                        <div>
                            <h2>Kontak</h2>
                            <small>Untuk menghubungi dan mengantar pesanan</small>
                        </div>
                    </div>

                    <ul class="csd-list">
                        <li>
                            <span class="ic t-green"><i class="bi bi-telephone"></i></span>
                            <div>
                                <small>No. HP</small>
                                @if ($customer->phone)
                                    <a class="v" href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                                @else
                                    <span class="v empty">Belum diisi</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <span class="ic t-blue"><i class="bi bi-envelope"></i></span>
                            <div>
                                <small>Email</small>
                                @if ($customer->email)
                                    <a class="v" href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                @else
                                    <span class="v empty">Tanpa email</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <span class="ic t-amber"><i class="bi bi-geo-alt"></i></span>
                            <div>
                                <small>Alamat</small>
                                @if ($customer->address)
                                    <span class="v">{{ $customer->address }}</span>
                                    @if ($maps)
                                        <div><a href="{{ $maps }}" target="_blank" rel="noopener noreferrer" class="ui-link"><i class="bi bi-map me-1"></i>Buka di Google Maps</a></div>
                                    @endif
                                @else
                                    <span class="v empty">Belum diisi</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </section>
            </div>

            {{-- Riwayat pesanan --}}
            <div class="col-lg-8">
                <section class="ui-card">
                    <div class="ui-head">
                        <div>
                            <h2>Riwayat pesanan</h2>
                            <small>10 pesanan terbaru dari customer ini</small>
                        </div>
                        <a href="{{ route('orders.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus-lg me-1"></i>Buat pesanan</a>
                    </div>

                    @if ($orders->isEmpty())
                        <div class="ui-empty">
                            <i class="bi bi-receipt"></i>
                            <b>Belum ada pesanan</b>
                            Pesanan customer ini akan muncul di sini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="ui-table ui-table--stack">
                                <thead>
                                    <tr><th>Kode</th><th>Tanggal acara</th><th>Total</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td data-label="Kode"><a href="{{ route('orders.show', $order->id) }}">{{ $order->order_code }}</a></td>
                                        <td data-label="Tanggal acara">{{ $order->delivery_date ? \Illuminate\Support\Carbon::parse($order->delivery_date)->format('d M Y') : '-' }}</td>
                                        <td data-label="Total">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td data-label="Status">
                                            <span class="ui-chip {{ $statusChip[$order->status] ?? 'off' }}">{{ $statusLabel[$order->status] ?? $order->status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>
        </div>

    </div>
</div>
@endsection