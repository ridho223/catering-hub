@extends('layouts.app')
@section('title','Kategori')
@section('content')

    <div class="container py-5">
        @if (@session('success'))
            
            <div class="alert alert-success alert-dismissible fade-show">
                {{session('success')}}

                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
        @endif

      <div class="card shadow-sm border-0">
        
        <div class=" card-header bg-success text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Data Kategori</h5>

            <a href="{{route('categories.create')}}" class="btn btn-light btn-sm">
                + Tambah Kategori    
            </a>
        </div>

        <div class="card-body">

         <table class="table table-hover align-middle mb-0">
            
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
                                <i class="bi bi-pencil-square">Edit</i>
                            </a>

                            <form action="{{route('categories.destroy',$category->id)}}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash3-fill">Hapus</i>
                                </button>
                            </form>
                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>

                        <td colspan="3" class="text-center">
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