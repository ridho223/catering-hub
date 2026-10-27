<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        $orders = $customer->orders()->with([
            'packages.package',
            'details.menu',
            'payment',            
        ])->latest()->get();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(string $order)
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        
        $order = $customer->orders()->with([
            'customer',
            'packages.package.details.menu',
            'details.menu',
            'payment',      
        ])->findOrFail($order);

        // Paket catering selalu dihitung.
        if($order->packages->isNotEmpty()){
            $subtotalPaket = $order->packages->sum('subtotal');

            $subtotalCustom = $order->details->where('source', 'custom')->sum('subtotal');

            $subtotalPesanan = $subtotalPaket + $subtotalCustom;
        }else{
            $subtotalPesanan = $order->details->sum('subtotal');
        }


        return view('customer.orders.show', compact('order', 'subtotalPesanan'));
    }
}
