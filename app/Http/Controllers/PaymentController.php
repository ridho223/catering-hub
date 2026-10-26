<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create(Order $order)
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        abort_if(!$customer, 404);

        $order = $customer->orders()->with('payment')->findOrFail($order->id);

        if($order->payment && $order->payment->status === 'Dibayar'){
            return redirect()->route('customer.orders.show', $order->id)->with('success','Pesanan ini sudah dibayar');
        }

        if (
            $order->delivery_method === 'delivery'
            && $order->delivery_distance === null
        ) {
            return redirect()
                ->route('customer.orders.show', $order->id)
                ->with(
                    'error',
                    'Pembayaran belum tersedia karena pengiriman belum dikonfirmasi admin.'
                );
        }

        return view('customer.payment.create', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Order $order)
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        abort_if(!$customer, 404);

        $order = $customer->orders()->with('payment')->findOrFail($order->id);

        $validated = $request->validate([
            'method' => 'required|in:transfer,qris',
            'proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'notes' => 'nullable|string|max:255',
        ]);

        $payment = $order->payment;

        if($payment && $payment->status === 'Dibayar'){
            return redirect()->route('customer.orders.show', $order->id)->with('error','Pesanan ini sudah dibayar');
        }

        $proof = $request->file('proof')->store('payments','public');

        if($payment){

            if(
                $payment->proof && Storage::disk('public')->exists($payment->proof)
            ){
                Storage::disk('public')->delete($payment->proof);
            }

            $payment->update([
                'method' => $validated['method'],
                'amount' => $order->total,
                'status' => 'Menunggu Verifikasi',
                'proof' => $proof,
                'paid_at' => null,
                'notes' => $validated['notes'] ?? null,
            ]);
            
        }else{
            Payment::create([
                'order_id' => $order->id,
                'method' => $validated['method'],
                'amount' => $order->total,
                'status' => 'Menunggu Verifikasi',
                'proof' => $proof,
                'paid_at' => null,
                'notes' => $validated['notes'] ?? null,
            ]);
            
        }

        return redirect()->route('customer.orders.show', $order->id)->with('success','Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
