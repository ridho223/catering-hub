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


<body class="d-flex flex-column min-vh-100">

   @include('partials.navbar')
   
    <main class="flex-grow-1">

    @yield('content')

    </main>

    @include('partials.footer')        

    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/script.js')}}"></script>

</body>
</html>