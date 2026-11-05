@php
    $kedai = [
        'name'      => 'Kedai BangJo',
        'tagline'   => 'Layanan catering untuk kebutuhan rumah, kantor, acara, dan berbagai kebutuhan lainnya.',
        'phone'     => '0812-3456-7890',
        'whatsapp'  => '6281234567890',
        'email'     => 'info@kedaibangjo.com',
        'instagram' => 'kedaibangjo', // GANTI dengan username Instagram asli (tanpa @)
        'address'   => 'Jl. Kauman I, Turen, Kec. Turen, Kabupaten Malang, Jawa Timur 65175',
    ];
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($kedai['address']);
    $waUrl   = 'https://wa.me/' . $kedai['whatsapp'];
    $igUrl   = 'https://www.instagram.com/' . $kedai['instagram'];

    $nav = [
        ['home',                     'Home'],
        ['customer.packages.index',  'Paket catering'],
        ['customer.prasmanan.index', 'Prasmanan'],
        ['cart.index',               'Keranjang'],
        ['about',                    'Tentang kami'],
        ['contact',                  'Kontak'],
    ];
@endphp

<style>
    .bjf { background:#1f2326; color:#c4cbd0; margin-top:2.5rem; font-size:.92rem; }
    .bjf a { color:#c4cbd0; text-decoration:none; }
    .bjf a:hover { color:#fff; }

    .bjf-top { padding:2rem 0 1.6rem; display:grid; grid-template-columns:1.4fr 1fr 1.3fr; gap:2.2rem; }

    .bjf a.bjf-brand { display:inline-flex; align-items:center; gap:.55rem; color:#fff; font-weight:800; font-size:1.05rem; margin-bottom:.55rem; }
    .bjf-logo { width:34px; height:34px; border-radius:10px; background:rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; font-size:1.05rem; }
    .bjf-desc { margin:0 0 .9rem; line-height:1.6; max-width:340px; color:#97a0a7; }

    /* Tombol WhatsApp: warna dikunci supaya tidak tertimpa warna link footer */
    .bjf a.bjf-wa { display:inline-flex; align-items:center; gap:.5rem; background:#25d366; color:#06250f; font-weight:700; border-radius:10px; padding:.5rem 1rem; font-size:.9rem; }
    .bjf a.bjf-wa:hover { background:#3be07b; color:#06250f; }

    .bjf h6 { color:#fff; font-weight:700; font-size:.9rem; margin:0 0 .65rem; }

    .bjf-links { list-style:none; margin:0; padding:0; display:grid; grid-template-columns:1fr 1fr; gap:.35rem 1.2rem; }
    .bjf-links a { display:inline-block; }

    .bjf-contact { list-style:none; margin:0; padding:0; }
    .bjf-contact li { display:flex; gap:.6rem; padding:.22rem 0; line-height:1.5; }
    .bjf-contact i { color:#e5e9ec; margin-top:.15rem; }
    .bjf a.bjf-map { color:#fff; font-weight:600; white-space:nowrap; text-decoration:underline; text-underline-offset:3px; text-decoration-color:rgba(255,255,255,.35); }
    .bjf a.bjf-map:hover { text-decoration-color:#fff; }

    .bjf-bottom { border-top:1px solid rgba(255,255,255,.12); padding:.9rem 0; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; color:#8a949b; font-size:.84rem; }
    .bjf-bottom p { margin:0; }
    .bjf-social { display:flex; gap:.45rem; }
    .bjf-social a { width:34px; height:34px; border-radius:9px; background:rgba(255,255,255,.08); color:#e5e9ec; display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .bjf-social a:hover { background:rgba(255,255,255,.2); color:#fff; }

    .bjf a:focus-visible { outline:2px solid #7fd1a0; outline-offset:2px; }

    @media (max-width:991.98px) {
        .bjf-top { grid-template-columns:1fr; gap:1.5rem; padding:1.6rem 0 1.2rem; }
    }
</style>

<footer class="bjf">
    <div class="container">

        <div class="bjf-top">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="bjf-brand">
                    <span class="bjf-logo">🍱</span>
                    <span>KEDAI BANGJO</span>
                </a>
                <p class="bjf-desc">{{ $kedai['tagline'] }}</p>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="bjf-wa">
                    <i class="bi bi-whatsapp"></i> Pesan lewat WhatsApp
                </a>
            </div>

            {{-- Navigasi --}}
            <div>
                <h6>Navigasi</h6>
                <ul class="bjf-links">
                    @foreach ($nav as [$route, $label])
                        <li><a href="{{ route($route) }}">{{ $label }}</a></li>
                    @endforeach
                    @auth('customer')
                        <li><a href="{{ route('customer.orders.index') }}">Pesanan saya</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h6>Hubungi kami</h6>
                <ul class="bjf-contact">
                    <li>
                        <i class="bi bi-telephone"></i>
                        <a href="tel:+{{ $kedai['whatsapp'] }}">{{ $kedai['phone'] }}</a>
                    </li>
                    <li>
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:{{ $kedai['email'] }}">{{ $kedai['email'] }}</a>
                    </li>
                    <li>
                        <i class="bi bi-geo-alt"></i>
                        <span>
                            {{ $kedai['address'] }}
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="bjf-map">Lihat peta</a>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bjf-bottom">
            <p>© {{ date('Y') }} {{ $kedai['name'] }}. Semua hak dilindungi.</p>
            <div class="bjf-social">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="mailto:{{ $kedai['email'] }}" aria-label="Email"><i class="bi bi-envelope"></i></a>
                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Lokasi"><i class="bi bi-geo-alt"></i></a>
            </div>
        </div>
    </div>
</footer>