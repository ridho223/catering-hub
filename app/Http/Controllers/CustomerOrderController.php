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

        abort_if(!$customer, 404);

        $order = $customer->orders()
            ->with([
                'customer',
                'packages.package.details.menu',
                'details.menu',
                'payment',
            ])
            ->findOrFail($order);

        // Jika pesanan memakai paket, gunakan subtotal paket.
        // Jika pesanan lama tidak memiliki data paket, gunakan detail menu.
        $subtotalPesanan = $order->packages->isNotEmpty()
            ? $order->packages->sum('subtotal')
            : $order->details->sum('subtotal');

        return view(
            'customer.orders.show',
            compact('order', 'subtotalPesanan')
        );
    }
}
