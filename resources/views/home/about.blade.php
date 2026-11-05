{{-- FILE: resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'Tentang Kami - Kedai BangJo Catering')

@section('content')
@php
    $kedai = [
        'name'     => 'Kedai BangJo',
        'whatsapp' => '6281234567890',
        'address'  => 'Jl. Kauman I, Turen, Kec. Turen, Kabupaten Malang, Jawa Timur 65175',
    ];
    $waUrl   = 'https://wa.me/' . $kedai['whatsapp'] . '?text=' . urlencode('Halo Kedai BangJo, saya mau tanya soal pesanan catering.');
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($kedai['address']);

    $values = [
        ['bi-basket',        'Bahan segar pilihan', 'Seluruh bahan masakan dipilih segar setiap pagi dari pemasok terpercaya demi kualitas rasa terbaik.'],
        ['bi-clock-history', 'Tepat waktu',         'Jadwal pengiriman dan penyajian dipastikan akurat agar acara Anda berjalan lancar tanpa kendala.'],
        ['bi-chat-heart',    'Pelayanan ramah',     'Tim kami siap membantu kebutuhan dan penyesuaian menu sesuai selera serta budget Anda.'],
    ];
@endphp

<style>
    .ab {
        --green:#198754; --green-dark:#126640; --green-deep:#13261b; --green-light:#eaf5ed;
        --ink:#1e2922; --mute:#64746b; --line:#dfe6e1;
        color:var(--ink); background:#fff;
    }
    .ab h1, .ab h2, .ab h3, .ab .ab-num { font-family:'Iowan Old Style','Palatino Linotype',Georgia,serif; letter-spacing:-.3px; }
    .ab a:focus-visible { outline:3px solid #7fd1a0; outline-offset:2px; }

    /* HERO: teks saja, tanpa foto & tombol (itu sudah ada di Home) */
    .ab-hero { padding:84px 0 64px; text-align:center; background:radial-gradient(circle at 10% 20%, #e1f3e5 0, transparent 40%), linear-gradient(135deg,#f8faf8 0%,#edf6ef 100%); }
    .ab-hero h1 { font-size:clamp(2.1rem,4.4vw,3.5rem); font-weight:700; line-height:1.15; max-width:820px; margin:0 auto 1.1rem; }
    .ab-hero p { color:var(--mute); font-size:1.05rem; line-height:1.8; max-width:640px; margin:0 auto; }

    .ab-sec { padding:80px 0; }
    .ab-sec h2 { font-size:clamp(1.6rem,3vw,2.2rem); font-weight:700; line-height:1.2; margin-bottom:1rem; }
    .ab-text { color:var(--mute); line-height:1.85; margin-bottom:1rem; }

    /* CERITA */
    .ab-photo { border-radius:28px 28px 28px 80px; overflow:hidden; box-shadow:0 22px 44px rgba(20,92,56,.12); }
    .ab-photo img { width:100%; height:460px; object-fit:cover; display:block; }
    .ab-facts { display:flex; gap:2.2rem; margin-top:1.8rem; padding-top:1.4rem; border-top:1px solid var(--line); }
    .ab-num { display:block; font-size:2.1rem; font-weight:700; line-height:1.1; color:var(--green-dark); }
    .ab-facts span.lbl { color:var(--mute); font-size:.9rem; }

    /* KOMITMEN: kolom bergaris, tanpa kotak */
    .ab-band { background:#f8faf8; }
    .ab-vals { display:grid; grid-template-columns:repeat(3,1fr); gap:2.4rem; margin-top:2.2rem; }
    .ab-val { border-top:3px solid var(--green); padding-top:1.3rem; }
    .ab-val i { color:var(--green); font-size:1.6rem; display:block; margin-bottom:.6rem; }
    .ab-val h3 { font-size:1.2rem; font-weight:700; margin-bottom:.4rem; }
    .ab-val p { color:var(--mute); font-size:.94rem; line-height:1.75; margin:0; }

    /* LOKASI */
    .ab-loc { display:grid; grid-template-columns:1fr 1fr; gap:3rem; align-items:center; }
    .ab-addr { font-style:normal; font-size:1.15rem; line-height:1.7; padding-left:1.2rem; border-left:3px solid var(--green); }
    .ab-addr b { display:block; font-family:'Iowan Old Style','Palatino Linotype',Georgia,serif; font-size:1.3rem; }
    .ab-link { display:inline-flex; align-items:center; gap:.4rem; color:var(--green-dark); font-weight:700; text-decoration:none; border-bottom:2px solid #b9d8c7; padding-bottom:2px; }
    .ab-link:hover { color:var(--green); border-color:var(--green); }

    /* PENUTUP: satu baris, bukan banner */
    .ab-end { border-top:1px solid var(--line); padding:2.2rem 0 4rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
    .ab-end p { margin:0; font-size:1.1rem; font-family:'Iowan Old Style','Palatino Linotype',Georgia,serif; }
    .ab-end div { display:flex; gap:1.4rem; flex-wrap:wrap; }

    @media (max-width:991.98px) {
        .ab-hero { padding:56px 0 44px; }
        .ab-sec { padding:52px 0; }
        .ab-photo img { height:300px; border-radius:22px 22px 22px 56px; }
        .ab-vals { grid-template-columns:1fr; gap:1.8rem; }
        .ab-loc { grid-template-columns:1fr; gap:1.4rem; }
    }
</style>

<div class="ab">

    {{-- HERO --}}
    <section class="ab-hero">
        <div class="container">
            <h1>Sajian otentik dari dapur Kedai BangJo</h1>
            <p>
                Menciptakan momen berkesan di setiap acara melalui hidangan catering dan prasmanan
                yang higienis, lezat, dan terjangkau.
            </p>
        </div>
    </section>

    {{-- CERITA --}}
    <section class="ab-sec">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="ab-photo">
                        <img src="{{ asset('assets/img/hero.jpeg') }}" alt="Tentang Kedai BangJo" loading="lazy">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2>Menghadirkan kehangatan dalam setiap suapan</h2>
                    <p class="ab-text">
                        Berawal dari keinginan menyajikan makanan lezat bermutu tinggi,
                        <b>Kedai BangJo Catering</b> tumbuh menjadi mitra terpercaya untuk berbagai kebutuhan
                        konsumsi di Malang dan sekitarnya.
                    </p>
                    <p class="ab-text">
                        Untuk hidangan harian, syukuran keluarga, acara kantor, hingga prasmanan berkapasitas besar,
                        kami menjaga hal yang sama: kebersihan, kesegaran bahan baku, dan ketepatan waktu pengiriman.
                    </p>

                    <div class="ab-facts">
                        <div><span class="ab-num">100%</span><span class="lbl">Halal dan higienis</span></div>
                        <div><span class="ab-num">50+</span><span class="lbl">Pilihan menu</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KOMITMEN --}}
    <section class="ab-sec ab-band">
        <div class="container">
            <h2>Komitmen layanan kami</h2>
            <div class="ab-vals">
                @foreach ($values as [$icon, $title, $desc])
                    <div class="ab-val">
                        <i class="bi {{ $icon }}"></i>
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- LOKASI --}}
    <section class="ab-sec">
        <div class="container">
            <div class="ab-loc">
                <div>
                    <h2>Dapur kami ada di Turen</h2>
                    <p class="ab-text mb-3">
                        Semua hidangan dimasak di sini. Pesanan juga bisa diambil langsung di lokasi.
                    </p>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="ab-link">
                        Buka di Google Maps <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
                <address class="ab-addr mb-0">
                    <b>{{ $kedai['name'] }}</b>
                    {{ $kedai['address'] }}
                </address>
            </div>

            <div class="ab-end mt-5">
                <p>Ada pertanyaan soal menu atau acara Anda?</p>
                <div>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="ab-link"><i class="bi bi-whatsapp"></i> Chat WhatsApp</a>
                    <a href="{{ route('contact') }}" class="ab-link">Halaman kontak <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection