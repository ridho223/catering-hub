@extends('layouts.admin')
 @section('title','Dashboard')
 @section('content')

 <div class="row">

    <x-stat-card
    title="Kategori"
    :value="$totalCategory"
    icon="bi bi-tags-fill"
    color="text-primary"/>

    <x-stat-card
    title="Menu"
    :value="$totalMenu"
    icon="bi-cup-hot-fill"
    color="text-success"/>

    <x-stat-card
    title="Pesanan"
    :value="0"
    icon="bi-cart-fill"
    color="text-warning"/>

    <x-stat-card
    title="Pendapatan"
    value="Rp0"
    icon="bi-cash-stack"
    color="text-danger"/>

    <div class="row mt-3">

        <div class="col-lg-8">

            <div class="card shadow-sm rounded-4">

                <div class="card-header">
                    Grafik Penjualan
                </div>

                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
                
            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow-sm rounded-4">

            <div class="card-header">
                Aktivasi
            </div>

            <div class="card-body">
                Belum diaktivasi.
            </div>

        </div>

    </div>

 </div>
    
 @endsection