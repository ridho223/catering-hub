@extends('layouts.admin')
@section('title','Customer')
@section('content')

 <div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-success text-white
                    d-flex justify-content-between align-items-center ">

            <h5 class="mb-0">
                <i class="bi bi-people me-2"></i>
                Data Customer
            </h5>

            <a href="{{route('customers.create')}}"
                class="btn btn-light btn-sm">

                <i class="bi bi-plus-circle me-1"></i>
                Tambah Customer

            </a>
        </div>

    
        <div class="card-body">

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{session('success')}}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
            @endif

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>
                            <th width="60">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. Hp</th>
                            <th>Alamat</th>
                            <th width="130">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($customers as $customer )

                            <tr>
                                
                                <td>{{$loop->iteration}}</td>

                                <td>
                                    <strong>
                                        {{$customer->name}}
                                    </strong>
                                </td>

                                <td>
                                    {{$customer->email ?? '-'}}
                                </td>

                                <td>
                                    {{$customer->phone}}
                                </td>

                                <td>
                                    {{$customer->address}}
                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a href="{{route('customers.edit', $customer->id)}}"
                                            class="btn btn-warning btn-sm">
                                            
                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                        <form action="{{route('customers.destroy', $customer->id)}}"
                                            method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm btn-delete">

                                                <i class="bi bi-trash3"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>
                            
                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-5">

                                    <i class="bi bi-people fs-1 text-muted"></i>
                                    
                                    <p class="text-muted mt-2 mb-0">
                                        Belum ada customer.
                                    </p>

                                </td>

                            </tr>
                            
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


    </div>

 </div>

 @endsection