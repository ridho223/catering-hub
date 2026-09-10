<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Login Admin - Catering</title>

    <link rel="stylesheet" href="{{asset('assets/css/bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap/bootstrap-icons.css')}}">

</head>
<body>
    

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-5">

                <div class="card shadow border-0">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <i class="bi bi-person-circle fs-1"></i>

                            <h3 class="mt-2">
                                login Admin
                            </h3>

                            <p class="text-muted">
                                Silakan Masuk Ke dashboard
                            </p>

                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{session('error')}}
                            </div>
                        @endif

                        @if (session('sucess'))
                            <div class="alert alert-success">
                                {{session('success')}}
                            </div>
                        @endif

                        <form action="{{route('login.process')}}" method="POST">
                            @csrf

                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email" name="email" class="form-control" value="{{old('email')}}"
                                    placeholder="Masukan email" required>
                                
                                @error('email')
                                    <small class="text-danger">
                                        {{$message}}    
                                    </small>                                
                                @enderror
                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Password
                                </label>

                                <input type="password" name="password" class="form-control" 
                                placeholder="Masukan password" required>

                                @error('password')
                                    <small class="text-danger">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">

                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>
</html>