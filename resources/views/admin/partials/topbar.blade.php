<nav class="navbar navbar-expand bg-white shadow-sm px-4">

    <h5 class="mb-0 fw-bold">@yield('title')</h5>

    <div class="ms-auto d-flex align-items-center">

        <div class="me-4 position-relative">
            <i class="bi bi-bell fs-5 me-4"></i>
        </div>
        
        <div class="dropdown">

            <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent p-0"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">

                  @if (Auth::user()->avatar)
                    
                    <img src="{{asset('storage/' . Auth::user()->avatar)}}"
                    width="42"
                    height="42" class="rounded-circle object-fit-cover" 
                    alt="{{Auth::user()->name}}">
                  @else

                    <div class="user-avatar">
                        {{strtoupper(substr(Auth::user()->name, 0, 2))}}
                    </div>

                  @endif


                    <div class="text-start d-none d-md-block">
                        <div class="fw-semibold">
                            {{Auth::user()->name}}
                        </div>

                        <small class="text-muted">
                            {{Auth::user()->level}}
                        </small>
                    </div>

                    <i class="bi bi-chevron-down small"></i>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">

                <li>
                    <h6 class="dropdown-header">
                        Akun Saya
                    </h6>
                </li>

                <li>
                    <a class="dropdown-item" href="{{route('admin.profile')}}">
                        <i class="bi bi-person me-2"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{route('admin.profile.edit')}}">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{route('admin.password.edit')}}">
                        <i class="bi bi-key me-2"></i>
                        Ubah password
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form action="{{route('logout')}}" method="POST">
                        @csrf

                        <button type="submit" class="dropdown-item text-danger">
                            
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>

</nav>