@extends('layouts.admin')
 @section('title' , 'Data Menu')
 @section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success')}}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">

            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center ">

                <h5 class="mb-0">
                    <i class="bi bi-grid"></i>
                    Data Menu Catering
                </h5>

                <a href="{{route('menu.create')}}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Menu
                </a>

            </div>

            <div class="card-body">

              <div class="table-responsive">

                <table  class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>
                            <th width="60">No</th>
                            <th width="100">Foto</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th width="130">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($menus as $menu )

                            <tr>

                                <td>{{$loop->iteration}}</td>
                                <td>
                                    @if ($menu->image)
                                    <img src="{{asset('uploads/menu/'. $menu->image)}}" 
                                    width="70" class="rounded menu-thumbnail">
                                    
                                    @else

                                    <div class="no-image">
                                        <i class="bi bi-image"></i>
                                    </div>

                                    @endif
                                </td>
                                <td>
                                    
                                    <strong>
                                        {{$menu->name}}
                                    </strong>

                                    <small class="d-block text-muted">
                                        {{ Str::limit($menu->description, 45)}}
                                    </small>

                                </td>
                                
                                <td>
                                    <span class="badge bg-info text-dark"> 
                                        {{$menu->category->name ?? '-'}}
                                    </span>
                                </td>

                                <td>
                                    Rp{{number_format($menu->price,0,',','.')}}
                                </td>

                                <td>
                                    
                                    @if ($menu->stock <= 0)

                                        <span class="badge bg-danger">
                                            Habis
                                        </span>
                                   
                                    @elseif($menu->stock <= 5)

                                        <span class="badge bg-warning text-dark">
                                            {{$menu->stock}}
                                        </span>
                                        
                                    @else                                

                                        <span class="badge bg-success">
                                            {{$menu->stock}}
                                        </span>

                                    @endif
                                </td>
                                <td>
                                    @if ($menu->status == 'Tersedia')
                                        <span class="badge bg-success">
                                            Tersedia
                                        </span>
                                        
                                    @else
                                        <span class="badge badge-secondary">
                                            Habis
                                        </span>
                                        
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">

                                        <a href="{{route('menu.edit',$menu->id)}}"
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{route('menu.destroy',$menu->id)}}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>
                            
                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5">

                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mt-2 mb-0 text-muted">
                                        Belum ada menu
                                    </p>
                                </td>

                            </tr>
                            
                        @endforelse

                    </tbody>
                </table>

              </div>    

            </div>

        </div>
    </div>
    
 @endsection