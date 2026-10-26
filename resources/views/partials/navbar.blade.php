  <nav class="navbar navbar-expand bg-white border-bottom sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{route('home')}}">        
            <span>
                🍱 KEDAI BANGJO
            </span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#menu" aria-controls="menu" aria-expanded="false"
        aria-label="Toggle navigation">
    
        <i class="bi bi-list fs-3"></i>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('home') ? 'active' : ''}}" href="{{route('home')}}">
                        <i class="bi bi-house-door me-1"></i>
                        Home
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a class="nav-link {{request()->RouteIs('menu.*') ? 'active' : ''}}" href="{{route('menu.public')}}">
                        <i class="bi bi-grid me-1"></i>
                        Menu
                    </a>
                </li> --}}

                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('customer.packages.*') ? 'active' : ''}}" href="{{route('customer.packages.index')}}">
                        <i class="bi bi-box-seam me-1"></i>
                        Paket
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('customer.custom.*') ? 'active' : '' }}"
                        href="{{ route('customer.custom.index') }}"
                        >
                        <i class="bi bi-sliders me-1"></i>
                        Custom Catering
                    </a>

                </li>

                <li class="nav-item">
                    <a href="{{route('cart.index')}}" class="nav-link {{request()->routeIs('cart.*') ? 'active' : ''}}">
                        <i class="bi bi-cart3"></i>
                        Keranjang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('about')}}">
                        <i class="bi bi-info-circle me-1"></i>
                        Tentang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('contact')}}">
                        <i class="bi bi-telephone me-1"></i>
                        Kontak
                    </a>
                </li>                        

                                @auth('customer')
                
                <li class="nav-item">
                    <a href="{{route('customer.orders.index')}}" class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : ''}}">
                        <i class="bi bi-receipt me-1"></i>
                        Pesanan Saya
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth('customer')->user()->customer->name ?? auth('customer')->user()->email }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        
                        <li>
                            <form action="{{route('customer.logout')}}" method="POST">
                                @csrf

                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>

                            </form>
                        </li>
                    </ul>
                </li>

                @else
                <li class="nav-item">
                    <a class="btn btn-outline-success ms-lg-2" href="{{route('customer.login')}}">
                        <i class="bi bi-arrow-in-right me-1"></i>
                        Login
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-success ms-lg-2 mt-2 mt-lg-0" href="{{route('customer.register')}}">
                        Register
                    </a>
                </li>
                
                @endauth

            </ul>

        </div>

    </div>

   </nav> 