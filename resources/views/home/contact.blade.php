{{-- FILE: resources/views/contact.blade.php --}}
@extends('layouts.app')

@section('title', 'Kontak Kami - Kedai BangJo Catering')

@section('content')
@php
    $kedai = [
        'name'      => 'Kedai BangJo',
        'phone'     => '0819-4491-2922',
        'whatsapp'  => '6281944912922',
        'email'     => 'cateringkedaibangjo.com',
        'address'   => 'Jl. Kauman I, Turen, Kec. Turen, Kabupaten Malang, Jawa Timur 65175',
        'map_query' => 'Catering "Kedai BangJo", Jl. Kauman I, Turen, Malang Regency, East Java 65175',
    ];
    $addr     = $kedai['address'];
    $waUrl    = 'https://wa.me/' . $kedai['whatsapp'];
    $mapEmbed = 'https://www.google.com/maps?q=' . urlencode($kedai['map_query']) . '&z=16&output=embed';
    $mapOpen  = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($kedai['map_query']);
    $mapRoute = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($kedai['map_query']);

    $topics = ['Tanya menu atau paket', 'Pesan prasmanan', 'Kendala pesanan atau pembayaran', 'Kerja sama', 'Lainnya'];
@endphp

<style>
    .ct { --green:#198754; --green-dark:#126640; --green-light:#eaf5ed; --ink:#1e2922; --mute:#64746b; --line:#e2e8e3; color:var(--ink); }

    .ct-hero { padding:56px 0 36px; background:radial-gradient(circle at 90% 0, #e1f3e5 0, transparent 45%), linear-gradient(135deg,#f8faf8 0%,#edf6ef 100%); text-align:center; }
    .ct-badge { display:inline-block; background:var(--green-light); color:var(--green); font-weight:700; font-size:.8rem; padding:.3rem .9rem; border-radius:999px; margin-bottom:.9rem; }
    .ct-hero h1 { font-size:clamp(1.9rem,3.6vw,2.9rem); font-weight:800; letter-spacing:-.5px; margin-bottom:.7rem; }
    .ct-hero h1 span { color:var(--green); }
    .ct-hero p { color:var(--mute); max-width:560px; margin:0 auto; }

    .ct-cards { margin-top:-1.6rem; position:relative; z-index:2; }
    .ct-card { display:flex; gap:.9rem; align-items:flex-start; background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.1rem 1.2rem; height:100%; text-decoration:none; color:inherit; box-shadow:0 6px 20px rgba(25,92,56,.05); transition:transform .2s, border-color .2s, box-shadow .2s; }
    a.ct-card:hover { transform:translateY(-3px); border-color:#b9d8c7; box-shadow:0 14px 30px rgba(25,92,56,.1); color:inherit; }
    .ct-ic { width:46px; height:46px; border-radius:14px; background:var(--green-light); color:var(--green); display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex:none; }
    .ct-ic--wa { background:#25d366; color:#fff; }
    .ct-card small { color:var(--mute); display:block; }
    .ct-card b { display:block; font-size:.98rem; line-height:1.35; margin-top:.1rem; word-break:break-word; }
    .ct-card .go { color:var(--green); font-size:.82rem; font-weight:600; margin-top:.3rem; display:block; }

    .ct-box { background:#fff; border:1px solid var(--line); border-radius:22px; padding:1.4rem; height:100%; }
    .ct-box h2 { font-size:1.15rem; font-weight:800; margin:0 0 .2rem; }
    .ct-box .sub { color:var(--mute); font-size:.9rem; margin-bottom:1.1rem; }
    .ct-box .form-label { font-weight:600; font-size:.9rem; margin-bottom:.3rem; }
    .ct-box .form-control, .ct-box .form-select { border-color:var(--line); border-radius:12px; padding:.6rem .85rem; }
    .ct-box .form-control:focus, .ct-box .form-select:focus { border-color:var(--green); box-shadow:0 0 0 .2rem rgba(25,135,84,.15); }
    .ct-send { background:#25d366; border:0; color:#08301a; font-weight:700; border-radius:12px; padding:.7rem 1rem; display:flex; align-items:center; justify-content:center; gap:.5rem; width:100%; }
    .ct-send:hover { background:#3be07b; color:#08301a; }
    .ct-note { color:var(--mute); font-size:.82rem; text-align:center; margin-top:.6rem; }
    .ct-err { color:#dc3545; font-size:.85rem; margin-top:.3rem; display:none; }

    .ct-map { border-radius:16px; overflow:hidden; border:1px solid var(--line); }
    .ct-map iframe { display:block; width:100%; height:300px; border:0; }
    .ct-addr { display:flex; gap:.7rem; align-items:flex-start; margin-top:1rem; }
    .ct-addr i { color:var(--green); font-size:1.2rem; margin-top:.1rem; }
    .ct-addr b { display:block; }
    .ct-addr span { color:var(--mute); font-size:.92rem; line-height:1.5; }
    .ct-mapbtns { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:1rem; }

    .ct-strip { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; background:var(--green-light); border:1px solid #cde2d3; border-radius:20px; padding:1.1rem 1.4rem; }
    .ct-strip b { display:block; }
    .ct-strip span { color:var(--mute); font-size:.92rem; }
</style>

<div class="ct">

    {{-- HERO --}}
    <section class="ct-hero">
        <div class="container">
            <span class="ct-badge">Hubungi kami</span>
            <h1>Ada pertanyaan? <span>Kami siap membantu</span></h1>
            <p>Tanya soal menu, konsultasi pesanan prasmanan, atau kendala transaksi. Kami balas secepatnya.</p>
        </div>
    </section>

    {{-- KARTU KONTAK --}}
    <section class="ct-cards">
        <div class="container">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <a class="ct-card" href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer">
                        <span class="ct-ic ct-ic--wa"><i class="bi bi-whatsapp"></i></span>
                        <span>
                            <small>WhatsApp</small>
                            <b>{{ $kedai['phone'] }}</b>
                            <span class="go">Chat sekarang <i class="bi bi-arrow-up-right"></i></span>
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="ct-card" href="tel:+{{ $kedai['whatsapp'] }}">
                        <span class="ct-ic"><i class="bi bi-telephone"></i></span>
                        <span>
                            <small>Telepon</small>
                            <b>{{ $kedai['phone'] }}</b>
                            <span class="go">Telepon <i class="bi bi-arrow-up-right"></i></span>
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="ct-card" href="mailto:{{ $kedai['email'] }}">
                        <span class="ct-ic"><i class="bi bi-envelope"></i></span>
                        <span>
                            <small>Email</small>
                            <b>{{ $kedai['email'] }}</b>
                            <span class="go">Kirim email <i class="bi bi-arrow-up-right"></i></span>
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="ct-card" href="{{ $mapOpen }}" target="_blank" rel="noopener noreferrer">
                        <span class="ct-ic"><i class="bi bi-geo-alt"></i></span>
                        <span>
                            <small>Lokasi dapur</small>
                            <b>Turen, Kabupaten Malang</b>
                            <span class="go">Lihat peta <i class="bi bi-arrow-up-right"></i></span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FORM + PETA --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-5">
                    <div class="ct-box">
                        <h2>Kirim pesan lewat WhatsApp</h2>
                        <p class="sub">Isi singkat di bawah, lalu pesan langsung terbuka di WhatsApp.</p>

                        <form id="waForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label" for="waName">Nama kamu</label>
                                <input type="text" id="waName" class="form-control" placeholder="Nama lengkap"
                                       value="{{ auth('customer')->user()?->customer?->name }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="waTopic">Topik</label>
                                <select id="waTopic" class="form-select">
                                    @foreach ($topics as $topic)<option>{{ $topic }}</option>@endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="waMsg">Pesan</label>
                                <textarea id="waMsg" rows="4" class="form-control" placeholder="Tulis pertanyaan atau kebutuhanmu"></textarea>
                                <div class="ct-err" id="waErr">Tulis pesanmu terlebih dahulu.</div>
                            </div>

                            <button type="submit" class="ct-send"><i class="bi bi-whatsapp"></i> Kirim lewat WhatsApp</button>
                            <div class="ct-note">Pesan dikirim dari aplikasi WhatsApp kamu sendiri.</div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="ct-box">
                        <h2>Lokasi kami</h2>
                        <p class="sub">Pengambilan pesanan dan dapur kami ada di sini.</p>

                        <div class="ct-map">
                            <iframe src="{{ $mapEmbed }}" title="Peta lokasi Kedai BangJo" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

                        <div class="ct-addr">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <b>{{ $kedai['name'] }}</b>
                                <span>{{ $addr }}</span>
                            </div>
                        </div>

                        <div class="ct-mapbtns">
                            <a href="{{ $mapRoute }}" target="_blank" rel="noopener noreferrer" class="btn btn-success"><i class="bi bi-signpost-split me-1"></i>Petunjuk arah</a>
                            <a href="{{ $mapOpen }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success"><i class="bi bi-box-arrow-up-right me-1"></i>Buka di Google Maps</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PINTASAN --}}
            <div class="ct-strip mt-4">
                <div>
                    <b>Sudah memesan?</b>
                    <span>Status pesanan dan pembayaran bisa dicek langsung di akun kamu.</span>
                </div>
                @auth('customer')
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-success"><i class="bi bi-receipt me-1"></i>Pesanan saya</a>
                @else
                    <a href="{{ route('customer.login') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk untuk cek pesanan</a>
                @endauth
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('waForm');
    const wa = @json($kedai['whatsapp']);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const name = document.getElementById('waName').value.trim();
        const topic = document.getElementById('waTopic').value;
        const msg = document.getElementById('waMsg').value.trim();
        const err = document.getElementById('waErr');

        if (!msg) { err.style.display = 'block'; document.getElementById('waMsg').focus(); return; }
        err.style.display = 'none';

        const text = 'Halo Kedai BangJo, saya ' + (name || 'calon pelanggan') + '.\n'
                   + 'Topik: ' + topic + '\n\n' + msg;
        window.open('https://wa.me/' + wa + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
    });
});
</script>
@endsection