@extends('layouts.admin')

@section('title' , 'Data Pesanan')

@section('content')

<div class="container-fluid py-3">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-success text-white rounded-top-4">
            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    <i class="bi bi-cart me-2"></i>
                    Data Pesanan
                </h5>

                <a href="{{route('orders.create')}}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>
                    Tambah Pesanan
                </a>

            </div>
        </div>

        <div class="card-body">

            @if (session('success'))                
                <div class="alert alert-success">
                    {{session('success')}}
                </div>                        
            @endif

            @if (session('error'))                
                <div class="alert alert-danger">
                    {{session('error')}}
                </div>                        
            @endif

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>
                            <th>NO</th>
                            <th>Customer</th>
                            <th>Tanggal Pesanan</th>
                            <th>Tanggal Kirim</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($orders as $order )
                        
                            <tr>

                                <td>{{$loop->iteration}}</td>

                                <td>{{$order->customer->name}}</td>

                                <td>{{date('d-m-Y' , strtotime($order->order_date))}}</td>

                                <td>{{date('d-m-Y' , strtotime($order->delivery_date))}}</td>

                                <td>Rp {{number_format($order->total, 0, ',', '.')}}</td>

                                <td>

                                    @if ($order->status == 'Pending')

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif ($order->status =='Diproses')
                                        
                                        <span class="badge bg-info">
                                            Diproses
                                        </span>

                                    @elseif ($order->status =='Selesai')
                                        
                                        <span class="badge bg-success">
                                            Selesai
                                        </span>

                                    @else
                                        
                                        <span class="badge bg-secondary">
                                            {{$order->status}}
                                        </span>
                                        
                                    @endif

                                </td>

                                <td>
                                
                                    <div class="d-flex gap-1">

                                            <a href="{{route('orders.show', $order->id)}}" class="btn btn-primary btn-sm" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{route('orders.edit',$order->id)}}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{route('orders.destroy', $order->id)}}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pesanan ini')">

                                        @csrf
                                        @method('DELETE')
                                        
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">

                                            <i class="bi bi-trash"></i>

                                        </button>
                                        
                                    </form>

                                    </div>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Belum Ada data Pesanan
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