<nav class="admin-topbar">

    {{-- LEFT --}}
    <div class="topbar-left">

        <div>
            <h5 class="topbar-title">
                @yield('title')
            </h5>

            <div class="topbar-breadcrumb">
                Admin Panel
                <i class="bi bi-chevron-right"></i>
                @yield('title')
            </div>
        </div>

    </div>


    {{-- RIGHT --}}
    <div class="topbar-right">


        {{-- NOTIFICATION --}}
        <button class="topbar-icon-btn">

            <i class="bi bi-bell"></i>

            <span class="notification-dot"></span>

        </button>


        {{-- DIVIDER --}}
        <div class="topbar-divider"></div>


        {{-- USER --}}
        <div class="dropdown">

            <button
                class="user-menu"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >

                @if (Auth::user()->avatar)

                    <img
                        src="{{ asset('storage/' . Auth::user()->avatar) }}"
                        class="topbar-avatar"
                        alt="{{ Auth::user()->name }}"
                    >

                @else

                    <div class="topbar-avatar avatar-placeholder">

                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}

                    </div>

                @endif


                <div class="user-info">

                    <strong>
                        {{ Auth::user()->name }}
                    </strong>

                    <small>
                        {{ Auth::user()->level }}
                    </small>

                </div>


                <i class="bi bi-chevron-down user-chevron"></i>

            </button>


            <ul class="dropdown-menu dropdown-menu-end shadow border-0 user-dropdown">

                <li>
                    <div class="dropdown-user-header">

                        <strong>
                            {{ Auth::user()->name }}
                        </strong>

                        <small>
                            {{ Auth::user()->email }}
                        </small>

                    </div>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>


                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route('admin.profile') }}"
                    >

                        <i class="bi bi-person"></i>

                        Profile

                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route('admin.profile.edit') }}"
                    >

                        <i class="bi bi-pencil-square"></i>

                        Edit Profile

                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route('admin.password.edit') }}"
                    >

                        <i class="bi bi-key"></i>

                        Ubah Password

                    </a>
                </li>


                <li>
                    <hr class="dropdown-divider">
                </li>


                <li>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item text-danger"
                        >

                            <i class="bi bi-box-arrow-right"></i>

                            Logout

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</nav>