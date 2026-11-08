<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

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

    // Customer membatalkan pesanannya sendiri selama belum ada pembayaran; stok dikembalikan.
    public function cancel(string $order)
    {
        $customer = Auth::guard('customer')->user()->customer;
        abort_if(! $customer, 404);

        $order = $customer->orders()->findOrFail($order);

        if ($order->status !== 'Pending') {
            return back()->with('error', 'Hanya pesanan yang belum diproses yang bisa dibatalkan.');
        }

        if ($order->hasActivePayment()) {
            return back()->with('error', 'Pesanan ini sudah ada pembayaran. Hubungi admin jika ingin membatalkan.');
        }

        if (! $order->cancelAndRestock()) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa dibatalkan.');
        }

        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function receipt(Order $order)
    {
        $customer = Auth::guard('customer')->user()->customer;
        abort_if(!$customer, 404);

        $order = $customer->orders()
            ->with(['customer', 'packages.package.details.menu', 'details.menu', 'payments'])
            ->findOrFail($order->id);

        return view('customer.orders.receipt', compact('order'));
    }
}