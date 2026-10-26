@extends('layouts.admin')
 @section('title','Edit Kategori')
 @section('content')
  
    <div class="container py-5">

        <div class="card shadow-sm rounded-4">

            <div class="card-header bg-success text-white">
                Edit Kategori
            </div>

            <div class="card-body">

                <form action="{{route('categories.update',$category->id)}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">Nama</label>

                        <input type="text" name="name" class="form-control rounded-3"
                                value="{{old('name',$category->name)}}">
                    </div>

                    <div class="mb-3">

                        <label class="form-label">Deskripsi</label>

                        <textarea name="description" class="form-control rounded-3" style="height:120px;">
                            {{old('description',$category->description)}}
                        </textarea>

                    </div>

                    <button class="btn btn-success rounded-pill">
                        <i class="bi bi-pencil-square">Update</i>
                    </button>

                    <a href="{{route('categories.index')}}" class="btn btn-secondary">
                        Kembali
                    </a>

                </form>

            </div>
            
        </div>

    </div>

 @endsection