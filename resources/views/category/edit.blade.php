@extends('layouts.app')
 @section('title','Edit Kategori')
 @section('content')
  
    <div class="container py-5">

        <div class="card shadow-sm">

            <div class="card-header bg-warning">
                Edit Kategori
            </div>

            <div class="card-body">

                <form action="{{route('categories.update',$category->id)}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label>Nama</label>

                        <input type="text" name="name" class="form-control"
                                value="{{old('name',$category->name)}}">
                    </div>

                    <div class="mb-3">

                        <label>Deskripsi</label>

                        <textarea name="description" class="form-control" style="height:120px;">
                            {{old('description',$category-description)}}
                        </textarea>

                    </div>

                    <button class="btn btn-warning">
                        Update
                    </button>

                    <a href="{{route('categories.index')}}" class="btn btn-secondary">
                        Kembali
                    </a>

                </form>

            </div>
            
        </div>

    </div>

 @endsection