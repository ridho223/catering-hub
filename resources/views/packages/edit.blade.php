@extends('layouts.admin')

@section('title', 'Tambah Paket')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <h4 class="fw-bold mb-1">
            Tambah Paket Catering
        </h4>

        <p class="text-muted mb-0">
            Buat paket catering dan tentukan isi paketnya.
        </p>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form
                    action="{{ route('packages.update', $package) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    >

                @csrf   
                @method('PUT')


                {{-- NAMA --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Paket
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $package->name) }}"
                        class="form-control"                        
                        required
                    >

                    @error('name')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- DESCRIPTION --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"                        
                    >{{ old('description', $package->description) }}</textarea>

                </div>


                {{-- PRICE --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Harga per Porsi
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Rp
                        </span>

                        <input
                            type="number"
                            name="price"
                            value="{{ old('price', $package->price) }}"
                            class="form-control"
                            min="0"                           
                            required
                        >

                    </div>

                    @error('price')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- STATUS --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                        required
                    >

                       <option value="Tersedia"
                            {{ old('status', $package->status) === 'Tersedia' ? 'selected' : '' }}>
                            Tersedia
                        </option>

                        <option value="Tidak Tersedia"
                            {{ old('status', $package->status) === 'Tidak Tersedia' ? 'selected' : '' }}>
                            Tidak Tersedia
                        </option>

                    </select>

                </div>


                {{-- IMAGE --}}
                <div class="mb-4">

               @if ($package->image)

                        <div class="mb-3">

                            <img
                                src="{{ asset('storage/' . $package->image) }}"
                                width="180"
                                height="120"
                                style="object-fit: cover;"
                                class="rounded"
                            >

                        </div>

                    @endif

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/*"
                    >

                    <small class="text-muted">
                        JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                    </small>

                </div>


                {{-- ISI PAKET --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Isi Paket
                    </label>

                    <div class="border rounded p-3">

                        @forelse ($menus as $menu)

                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="menus[]"
                                    value="{{ $menu->id }}"
                                    id="menu{{ $menu->id }}"
                                     {{ in_array($menu->id, old('menus', $selectedMenus)) ? 'checked' : '' }}
                                >

                                <label
                                    class="form-check-label"
                                    for="menu{{ $menu->id }}"
                                >

                                    {{ $menu->name }}

                                </label>

                            </div>

                        @empty

                            <p class="text-muted mb-0">
                                Belum ada menu yang tersedia.
                            </p>

                        @endforelse

                    </div>

                    @error('menus')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                <div class="d-flex gap-2">

                    <a
                        href="{{ route('packages.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection