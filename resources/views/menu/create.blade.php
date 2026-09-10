@extends('layouts.app')
 @section('title','Tambah Menu')
 @section('content')
    
    <div class="container py-5">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-success text-white">
                Tambah Menu Catering
            </div>

            <div class="card-body">

                <form action="{{route('menu.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Nama Menu</label>

                            <input type="text" name="name" class="form-control" value="{{old('name')}}">
                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Kategori</label>

                            <select name="category_id" class="form-select">

                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category )
                                    <option value="{{$category->id}}">
                                        {{$category->name}}
                                    </option>
                                    
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Harga</label>

                            <input type="number" name="price" class="form-control">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Stok</label>

                            <input type="number" name="stock" class="form-control">

                        </div>

                        <div class="col-md-12 mb-3">

                            <label>Deskripsi</label>

                            <textarea name="description" class="form-control" rows="4"></textarea>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Status</label>

                            <select name="status" class="form-select">

                                <option value="Tersedia">
                                    Tersedia
                                </option>

                                <option value="Habis">
                                    Habis
                                </option>

                            </select>

                        </div>

                        <label class="btn btn-success">
                            <i class="bi bi-upload"></i>
                            Upload Foto
                            <input type="file"
                                    name="image"
                                    id="image"
                                    hidden>
                        </label>

                        <div class="image-preview mb-3">

                            <img id="preview" src="{{asset('assets/images/no-image.png')}}" >

                        </div>

                    </div>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    
                    <a href="{{route('menu.index')}}" class="btn btn-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>

                    <button class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-circle">
                            Simpan
                        </i>
                    </button>

                </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        image.onchange = evt => {
            const[file] = image.files;
            
            if(file){
                preview.src = URL.createObjectURL(file);
                preview.style.display='block';
            }
        }
    </script>

 @endsection