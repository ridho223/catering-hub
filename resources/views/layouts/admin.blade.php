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

    
</head>


<body>

    @if (session('success'))

    <script>

        Swal.fire({
            icon:'success',
            title: 'Berhasil',
            text:'{{session('success')}}',
            timer:1000,
            showConfirmButton:false,
        })

    </script>
    
    @endif

    <div class="wrapper">

        @include('admin.partials.sidebar')

        <div class="main">

            @include('admin.partials.topbar')

            <div class="content">

                @yield('content')

            </div>

        </div>

    </div>

    <script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    {{-- <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script> --}}
    {{-- <script src="{{asset('assets/vendor/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/sweetalert2/sweetalert2.all.min.js')}}"></script> --}}
    <script src="{{asset('assets/js/script.js')}}"></script>

    @stack('scripts')

</body>
</html>