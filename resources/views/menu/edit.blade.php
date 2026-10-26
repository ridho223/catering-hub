@extends('layouts.admin')
 @section('title','Tambah Menu')
 @section('content')
    
    <div class="container py-4">

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-success text-white rounded-top-4">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Menu Catering
                </h5>
            </div>

            <div class="card-body py-4">

                <form action="{{route('menu.update',$menu->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-md-6 mb-3">

                            <label>Nama Menu</label>

                            <input type="text" name="name" class="form-control" value="{{old('name', $menu->name)}}" required>
                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Kategori</label>

                            <select name="category_id" class="form-select" required>

                                @foreach ($categories as $category )
                                                   
                                    <option value="{{$category->id}}"

                                        {{ $menu->category_id == $category->id ? 'selected' : ''}}>

                                        {{$category->name}}

                                    </option>
                                    
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Harga</label>

                            <input type="number" name="price" class="form-control" value="{{old('price',$menu->price)}}" required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Stok</label>

                            <input type="number" name="stock" class="form-control" value="{{old('stock',$menu->stock)}}" required>

                        </div>

                        <div class="col-md-12 mb-3">

                            <label>Deskripsi</label>

                            <textarea name="description" class="form-control" rows="4">{{old('description',$menu->description)}}</textarea>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Status</label>

                            <select name="status" class="form-select">

                                <option value="Tersedia" {{$menu->status=='Tersedia'?'selected':''}}>
                                    Tersedia
                                </option>

                                <option value="Habis" {{$menu->status=='Habis'?'selected':''}}>
                                    Habis
                                </option>

                            </select>

                        </div>

                     <div class="col-md-6">

                        <label class="form-label">Foto Menu</label>

                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">

                        @if ($menu->image)

                            <div class="mt-3">
                                <img src="{{ asset('uploads/menu/' . $menu->image) }}"
                                     width="120"
                                     height="100"
                                     class="rounded-3 object-fit-cover">
                            </div>

                        @endif                    

                      </div>  

                    </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                

                    
                    <a href="{{route('menu.index')}}" class="btn btn-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>


                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-circle me-1">
                            Update
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