<aside class="sidebar">

    {{-- BRAND --}}
    <div class="sidebar-brand">

        <a href="{{ route('dashboard') }}" class="brand-link">

            <div class="brand-icon">
                🍱
            </div>

            <div>
                <div class="brand-name">
                    KEDAI BANGJO
                </div>

                <small>
                    Admin Panel
                </small>
            </div>

        </a>

    </div>


    {{-- MENU --}}
    <div class="sidebar-menu">

        {{-- MAIN --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title">
                MAIN
            </div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-1x2-fill"></i>

                <span>Dashboard</span>

            </a>
        </div>


        {{-- KATALOG --}}
        <div class="sidebar-section">

            <div class="sidebar-section-title">
                KATALOG
            </div>


            <a href="{{ route('categories.index') }}"
               class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">

                <i class="bi bi-tags"></i>

                <span>Kategori</span>

            </a>


            <a href="{{ route('menu.index') }}"
               class="sidebar-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">

                <i class="bi bi-grid"></i>

                <span>Menu</span>

            </a>


            <a href="{{ route('packages.index') }}"
               class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">

                <i class="bi bi-box-seam"></i>

                <span>Paket</span>

            </a>                        

        </div>


        {{-- TRANSAKSI --}}
        <div class="sidebar-section">

            <div class="sidebar-section-title">
                TRANSAKSI
            </div>


            <a href="{{ route('orders.index') }}"
               class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">

                <i class="bi bi-cart3"></i>

                <span>Pesanan</span>

            </a>


            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">

                <i class="bi bi-people"></i>

                <span>Customer</span>

            </a>

            <a href="{{route('admin.payments.index')}}" 
                class="sidebar-link {{request()->routeIs('admin.payments.*') ? 'active' : ''}}">

                <i class="bi bi-credit-card me-2"></i>
                
                <span>Pembayaran</span>
            </a>

        </div>


        {{-- LAINNYA --}}
        <div class="sidebar-section">

            <div class="sidebar-section-title">
                LAINNYA
            </div>


            <a href="#" class="sidebar-link">

                <i class="bi bi-file-earmark-bar-graph"></i>

                <span>Laporan</span>

            </a>

        </div>


        {{-- SISTEM --}}
        @if (Auth::user()->level === 'Superadmin')

            <div class="sidebar-section">

                <div class="sidebar-section-title">
                    SISTEM
                </div>


                <a href="{{ route('users.index') }}"
                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">

                    <i class="bi bi-person-gear"></i>

                    <span>User</span>

                </a>

            </div>

        @endif

    </div>


    {{-- BOTTOM --}}
    <div class="sidebar-bottom">

        <div class="sidebar-version">
            <i class="bi bi-shield-check"></i>
            Admin Panel
        </div>

    </div>

</aside>