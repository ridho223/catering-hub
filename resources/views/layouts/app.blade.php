    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{asset('assets/css/bootstrap/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/bootstrap/bootstrap-icons.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">

        {{-- <link rel="stylesheet" href="/assets/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css"> --}}
        
    </head>


    <body class="d-flex flex-column min-vh-100">

    
    @include('partials.navbar')
    
        <main class="flex-grow-1">

        @yield('content')

        </main>

        @include('partials.footer')        
        <script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>    
        <script src="{{asset('assets/js/script.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $flashSuccess = session()->pull('success');
    $flashError   = session()->pull('error');
@endphp

@if ($flashSuccess)
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json($flashSuccess),
        confirmButtonColor: '#198754',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>
@endif

@if ($flashError)
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: @json($flashError),
        confirmButtonColor: '#198754'
    });
</script>
@endif

        {{-- <script src="/assets/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/script.js"></script> --}}

    </body>
    </html>