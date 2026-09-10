@extends('layouts.admin')
@section('title','Kategori')
@section('content')

    <div>
        @if(@session('success'))
            
            <div class="alert alert-success alert-dismissible fade show">
                {{session('success')}}

                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
        @endif

      <div class="card shadow-sm border-0 rounded-4">
        
        <div class=" card-header bg-success text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Data Kategori</h5>

            <a href="{{route('categories.create')}}" class="btn btn-light btn-sm">
              <i class="bi bi-plus-circle"></i>
              Tambah Kategori   
            </a>
        </div>

        <div class="card-body">

         <table id="datatable" class="table table-hover align-middle">
            
            <thead class="table-success">

                <tr>

                    <th width="70">NO</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th width="200">Aksi</th>

                </tr>

            </thead>

            <tbody>
                @forelse ($categories as $category )
                    <tr>

                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                            <a href="{{route('categories.edit',$category->id)}}" 
                                class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit
                            </a>

                            <form action="{{route('categories.destroy',$category->id)}}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete">
                                    <i class="bi bi-trash3-fill me-1"></i>
                                    Hapus
                                </button>
                            </form>
                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>

                        <td colspan="4" class="text-center">
                            Belum Ada Data
                        </td>

                    </tr>
                @endforelse

            </tbody>

         </table>

        </div>
      </div>    
    </div>


@endsection