@extends('layouts.app')
@section('title','Menu')
@section('content')

@php
    use Illuminate\Support\Str;
@endphp

<section class="menu-page-header">
    <div class="container">

        <span class="section-label">
            Tara Catering
        </span>

        <h1>
            Pilihan Menu
        </h1>

        <p>
            Pilih menu catering yang sesuai dengan kebutuhan Anda.
        </p>

    </div>
</section>

<section class="menu-list-section">
    
<div class="container">
 
    <div class="row g-4">

        @forelse ($menus as $menu )
            
            <div class="col-md-6 col-lg-4">

                <div class="customer-menu-card">

                    {{-- Image --}}
                    <div class="customer-menu-image">

                        @if ($menu->image)
                            
                            <img src="{{asset('uploads/menu/' . $menu->image)}}" alt="{{$menu->name}}">
                        
                        @else

                            <div class="menu-no-image">
                                <i class="bi bi-image"></i>
                            </div>

                        @endif

                    </div>

                    <div class="customer-menu-body">

                        @if ($menu->category)

                            <small class="menu-category">
                                {{$menu->category->name}}
                            </small>
                            
                        @endif

                        <h5>
                            {{$menu->name}}
                        </h5>

                        <p>
                            {{str::limit($menu->description, 90)}}
                        </p>

                        <div class="mt-auto menu-info">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>
                                    <small>
                                        Harga
                                    </small>

                                    <strong>
                                        Rp.{{number_format($menu->price,0,',','.')}}
                                    </strong>
                                </div>

                                <div class="text-end">
                                    <small>
                                        Stok
                                    </small>

                                    <span>
                                        {{$menu->stock}}
                                    </span>
                                </div>
                            </div>

                            <a href="{{route('menu.show',$menu)}}" class="btn btn-success w-100 mt-3">
                                <i class="bi bi-eye me-1"></i>
                                Lihat Detail
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                
                <div class="empty-menu">

                    <i class="bi bi-inbox"></i>

                    <h5>
                        Belum ada menu
                    </h5>

                    <p >
                        Menu catering belum tersedia saat ini.
                    </p>

                </div>

            </div>
            
        @endforelse

    </div>

</div>

</section>

@endsection
