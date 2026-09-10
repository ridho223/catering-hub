@extends('layouts.app')

@section('title','Keranjang')

@section('content')

    <div class="container py-5">

        <div class="d-flex justify-content-between align-itens-center mb-4">
            <h3 class="mb-0">
                <i class="bi bi-cart3 me-2"></i>
                Keranjang
            </h3>

            <a href="{{route('home')}}" class="btn btn-outline-success">
                <i class="bi bi-arrow-left me-1"></i>
                Lanjut Belanja
            </a>
            
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>            
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>            
        @endif
        

        @if(count($cart) > 0)
            
            @php
                $total = 0;
            @endphp

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    @foreach ($cart as $item )

                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp

                        <div class="row align-items-center border-bottom py-3">

                            <div class="col-md-2">

                                @if($item['image'])
                                    <img src="{{asset('uploads/menu/' . $item['image']) }}" class="img-fluid rounded"
                                        style="height: 100px; width:120; object-fit:cover;">
                                @else
                                    <div class="bg-light rounded p-4 text-center">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-3">

                                <h6 class="mb-1">
                                    {{ $item['name'] }}
                                </h6>

                                <small class="text-muted">
                                    Rp {{ number_format($item['price'], 0, ',', '.')}}
                                </small>                                

                            </div>

                            <div class="col-md-3">

                                <form action="{{route('cart.update', $item-['id']) }}" method="POST" class="d-flex gap-2">

                                    @csrf
                                    @method('PUT')

                                    <input type="number" name="quantity" value="{{$item['quantity'] }}" min="1" class="form-controll" style="width:90px">

                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-check"></i>
                                    </button>

                                </form>

                            </div>

                            <div class="col-md">

                                <strong>
                                    Rp{{ number_format($subtotal, 0, ',', '.')}}
                                </strong>

                            </div>

                            <div class="col-md-2 text-end">

                                <form action="{{route('cart.remove', $item['id'])}}" method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </div>

                        </div>
                        
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-4 ">

                        <form action="{{route('cart.clear')}}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-outline-danger">
                                Kosongankan Keranjang
                            </button>

                        </form>

                        <div class="text-end">

                            <small class="text-muted">
                                Total Pesanan
                            </small>

                            <h4 class="text-success">
                                Rp {{ number_format($total, 0, ',', '.')}}
                            </h4>

                            <a href="#" class="btn btn-success">
                                Lanjut Checkout
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @else

            <div class="text-center py-5">

                <i class="bi bi-cart-x fs-1 text-muted"></i>

                <h5 class="mt-3">
                    Keranjang masih kosong
                </h5>

                <p class="text-muted">
                    Belum ada menu yang dipilih
                </p>

                <a href="{{route('home')}}" class="btn btn-success">
                    Lihat Menu
                </a>
                
            </div>

        @endif

    </div>

@endsection