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
        <script src="{{ asset('assets/js/confirm-dialog.js') }}"></script>

@php
    $flashSuccess = session()->pull('success');
    $flashError   = session()->pull('error');
@endphp

<style>
    .bj-toast { --bj-c:#198754; border-radius:16px !important; padding:.8rem 1rem !important; border:1px solid #e3ebe5; border-left:5px solid var(--bj-c); box-shadow:0 14px 34px rgba(15,25,19,.16) !important; }
    .bj-toast.bj-error { --bj-c:#dc3545; }
    .bj-toast .swal2-title { font-size:.95rem !important; font-weight:800 !important; color:#1f2a24 !important; margin:0 !important; text-align:left; }
    .bj-toast .swal2-html-container { font-size:.84rem !important; color:#5c6a62 !important; margin:.15rem 0 0 !important; text-align:left; }
    .bj-toast .swal2-timer-progress-bar { background:var(--bj-c); opacity:.4; }
</style>

<script>
(function () {
    var success = @json($flashSuccess);
    var error   = @json($flashError);

    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        showCloseButton: true,
        timerProgressBar: true,
        didOpen: function (el) {
            el.addEventListener('mouseenter', Swal.stopTimer);
            el.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    var norm = function (s) { return (s || '').replace(/\s+/g, ' ').trim(); };
    var queue = [];
    if (error)   { queue.push({ text: error,   opts: { icon: 'error',   title: 'Gagal',    text: error,   timer: 6500, customClass: { popup: 'bj-toast bj-error' } } }); }
    if (success) { queue.push({ text: success, opts: { icon: 'success', title: 'Berhasil', text: success, timer: 4000, customClass: { popup: 'bj-toast' } } }); }

    (function next() {
        var q = queue.shift();
        if (!q) { return; }
        // buang alert inline di halaman yang isinya sama, supaya tidak muncul dobel
        document.querySelectorAll('main .alert').forEach(function (a) {
            if (norm(a.textContent).indexOf(norm(q.text)) !== -1) { a.remove(); }
        });
        Toast.fire(q.opts).then(next);
    })();
})();
</script>

        {{-- <script src="/assets/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/script.js"></script> --}}

    </body>
    </html>