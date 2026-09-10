@extends('layouts.admin')

@section('title', 'title')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">
                <i class="bi bi-person-plus me-2"></i>
                Tambah Customer
            </h5>

        </div>

        <div class="card-body">

            <form action="{{route('customers.store')}}" method="POST">
                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Nama Customer
                    </label>

                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="Masukan nama customer">

                    @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>                    
                    @enderror
                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Email
                    </label>

                    <input type="email" name="email" class="form-control @error ('email') is-invalid @enderror" value="{{old('email')}}" placeholder="Masukan email">

                    @error('email')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>                        
                    @enderror                

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        NO. HP
                    </label>

                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{old('phone')}}" placeholder="Contoh: 085854771641">

                    @error('phone')
                        <div class="invalid-feedback">
                            {{$message}}    
                        </div>                        
                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea name="address" rows="4" class="form-control @error('address') is-invalid @enderror" placeholder="Masukan alamat customer">{{old('address')}}</textarea>

                    @error('address')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">

                    <a href="{{route('customers.index')}}" class="btn btn-secondary">

                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali

                    </a>

                    <button type="submit" class="btn btn-success">

                        <i class="bi bi-save me-1"></i>
                        Simpan

                    </button>
                    
                </div>

            </form>
            
        </div>

    </div>

</div>
@endsection

