@extends('layouts.app')
@section('title','$menu->name')
@section('content')
<div class="menu-detail-page">
    <div class="container py-5">
        <a href="{{route('menu.public')}}" class="back-menu">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali ke menu
        </a>

        <div class="row g-5 align-items-center mt-2">

            {{-- image --}}
            <div class="col-lg-6">

                <div class="menu-detail-image">

                    @if ($menu->image)
                        <img src="{{asset('uploads/menu/')}}" alt="{{$menu->name}}">
                    @else
                        <div class="menu-detail-placeholder">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Detail --}}
            <div class="col-lg-6">

                @if ($menu->category)
                    
                    <span class="menu-category">
                        {{$menu->category->name}}
                    </span>

                @endif

                <h1 class="menu-detail-title">
                    {{$menu->name}}
                </h1>

                <div class="menu-detail-price">
                    Rp.{{number_format($menu->price,0,',','.')}}
                </div>

                <p class="menu-detail-description">
                    {{$menu->description}}
                </p>

                <div class="menu-stock-box">
                    
                    <i class="bi bi-box-seam"></i>

                    <div>
                        <small>Stok Tersedia</small>
                        <strong>{{$menu->stock}}</strong>
                    </div>

                </div>

                <form action="{{route('cart.add', $menu->id)}}" method="POST" class="mt-4">
                    @csrf

                    <label class="form-label fw-semibold">
                        Jumlah
                    </label>
                    
                    <div class="quantity-input">

                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">
                            <i class="bi bi-dash"></i>
                        </button>

                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{$menu->stock}}">

                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 mt-3">
                        <i class="bi bi-cart-plus me-2"></i>
                        Tambah Ke Keranjang
                    </button>
                </form>

            </div>

        </div>

    </div>
</div>

@push('scripts')
    
<script>

function increaseQuantity(){
    const input = document.getElementById('quantity');
    const max = parseInt(imput.max);
    let value = parseInt(input.value);

    if(value < max){
        input.value = value + 1;
    }
}

function decreaseQuantity(){
    const input = document.getElementById('quantity');
    let value = parseInt(input.value);

    if(value > 1) {
        input.value = value - 1;
    }
}

</script>

@endpush

@endsection