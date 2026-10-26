@extends('layouts.admin')

@section('title','Tambah Kategori')

@section('content')

    <div class="container py-5">

        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-success text-white">
                Tambah Kategori
            </div>

            <div class="card-body">
                <form action="{{route('categories.store')}}" method="POST">
                    @csrf
                    <div class="mb-3">

                        <label class="form-label">Nama Kategori</label>

                        <input type="text" name="name" 
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                value="{{old('name')}}">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="mb-3">

                            <label class="form-label">Deskripsi</label>

                            <textarea name="description"
                                      class="form-control rounded-3 @error('description') is-invalid @enderror"
                                      placeholder="Masukan deskripsi kategori..."
                                      style="height=:120px; resize:none;">
                                    {{old('description')}}
                            </textarea>

                        </div>

                        <button class="btn btn-success rounded-pill">
                            Simpan
                        </button>

                        <a href="{{route('categories.index')}}" class="btn btn-secondary">
                            Kembali
                        </a>

                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection