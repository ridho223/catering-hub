@extends('layouts.app')

    @section('title' , 'Tavi Catering')

    @section('content')

       <div class="container py-5">
        
        <div class="p-5 bg-light rounded-4">
            <h1 class=" display-4 fw-bold">
                Selamat Datang di Tavi Catering
            </h1>

            <p class="lead">
                Solusi Catering Rumahan,Kantor,dan Acara Spesial
            </p>

            <a href="/category" class="btn btn-success">Liat Menu</a>
        </div>

        <div class="text-center mb-5">
            <h1>Menu Catering</h1>
            <p>Pilih menu yang kamu mau</p>
        </div>

        <div class="row g-4">

            @forelse ($menus as $menu )

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm">

                        @if ($menu->image)
                            <img src="{{asset('/uploads/menu' . $menu->image)}}" class="card-img-top"
                                    style="height:220px; object-fit:cover;">                            
                        @endif

                        <div class="card-body">

                            <h5 class="card-title">
                                {{$menu->name}}
                            </h5>

                            <p class="text-muted">
                                {{$menu->description}}
                            </p>

                            <h5 class="text-success">
                                Rp {{number_format($menu->price,0, ',', '.')}}
                            </h5>

                            <p class="mb-3">
                                Stock: {{$menu->stock}}
                            </p>

                           <form action="{{route('cart.add',$menu->id)}}" method="POST">
                                @csrf

                                <input type="hidden" name="quantity" value="1">

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-cart-plus me-1"></i>
                                    Tambah Ke keranjang
                                </button>
                           </form>

                        </div>

                    </div>

                </div>
                
            @empty

                <div class="col-12 text-center">
                    <p>Belum ada menu tersedia</p>
                </div>
                
            @endforelse
            
        </div>


       </div>
    @endsection