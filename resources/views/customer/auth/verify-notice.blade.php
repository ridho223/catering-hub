@extends('customer.auth.layout')

@section('title', 'Verifikasi Email')
@section('side-pill')<i class="bi bi-envelope-check"></i> Satu langkah lagi @endsection
@section('side-title', 'Cek email kamu ✉️')
@section('side-text', 'Verifikasi email membantu kami memastikan pesanan dan pembayaranmu aman.')

@section('side-extra')
    <div class="auth-steps">
        <div class="auth-steps-title">Tinggal 3 langkah</div>
        <ol>
            <li><span>1</span><div><b>Buka email kamu</b><small>Cari pesan dari kami, cek folder spam</small></div></li>
            <li><span>2</span><div><b>Klik tombol Aktifkan Akun</b><small>Link berlaku 60 menit</small></div></li>
            <li><span>3</span><div><b>Mulai pesan catering</b><small>Akun langsung aktif dan kamu masuk</small></div></li>
        </ol>
    </div>
@endsection

@section('form')
    <div class="auth-icon"><i class="bi bi-envelope-check"></i></div>
    <h3 class="mb-1">Verifikasi Email</h3>
    <p class="sub mb-4">
        Kami sudah mengirim link verifikasi ke <b class="text-dark">{{ $email }}</b>.
        Buka emailnya (cek juga folder spam), lalu klik tombol di dalamnya.
        @if (! empty($pending))
            Akunmu baru dibuat setelah link itu diklik.
        @endif
    </p>

    @if (! empty($pending))
        <form action="{{ route('customer.register.resend') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-auth w-100" data-cooldown="45">Kirim Ulang Link</button>
        </form>

        <p class="auth-switch">
            Salah ketik email?
            <a href="{{ route('customer.register') }}" class="auth-link">Daftar ulang</a>
        </p>
    @else
        <form action="{{ route('customer.verification.send') }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-auth w-100" data-cooldown="45">Kirim Ulang Link</button>
        </form>

        <form action="{{ route('customer.logout') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" class="btn btn-link auth-link small">Keluar / ganti akun</button>
        </form>
    @endif
@endsection