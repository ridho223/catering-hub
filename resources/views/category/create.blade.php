@extends('layouts.app')

@section('title','Tambah Kategori')

@section('content')

    <div class="container py-5">

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                Tambah Kategori
            </div>

            <div class="card-body">
                <form action="{{route('categories.store')}}" method="POST">
                    @csrf
                    <div class="mb-3">

                        <label class="form-label">Nama Kategori</label>

                        <input type="text" name="name" 
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{old('name')}}">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="mb-3">

                            <label class="form-label">Deskripsi</label>

                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Masukan deskripsi kategori..."
                                      style="height=:120px; resize:none;">
                                    {{old('description')}}
                            </textarea>

                        </div>

                        <button class="btn btn-success">
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