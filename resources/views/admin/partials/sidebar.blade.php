<div class="sidebar">

    <h3 class="text-white text-center py-4">
        🍱 Tavi Catering
    </h3>

    <a href="{{route('dashboard')}}" class="{{request()->routeIs('dashboard') ? 'active' : ''}}">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="{{route('categories.index')}}" class="{{request()->routeIs('categories.*') ? 'active' : ''}}">
        <i class="bi bi-tags"></i>
        Kategori
    </a>

    <a href="{{route('menu.index')}}" class="{{request()->routeIs('menus.*') ? 'active' : ''}}">
        <i class="bi bi-grid"></i>
        Menu
    </a>

    <a href="{{route('orders.index')}}" class="{{request()->routeIs('orders.*') ? 'active' : ''}}">
        <i class="bi bi-cart"></i>
        Pesanan
    </a>

    <a href="{{route('customers.index')}}"
        class="{{request()->routeIs('customers.*') ? 'active' : ''}}">

        <i class="bi bi-people"></i>

        Customer
    </a>

    <a href="#">
        <i class="bi bi-file-earmark-text"></i>
        Laporan
    </a>

    @if (Auth::user()->level === 'Superadmin')        
    <a href="{{route('users.index')}}">
        <i class="bi bi-people"></i>
        User
    </a>
    @endif
    
</div>