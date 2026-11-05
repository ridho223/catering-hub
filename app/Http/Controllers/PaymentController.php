<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $order = $this->ownedOrder($order);

        if ($order->isFullyPaid()) {
            return redirect()->route('customer.orders.show', $order->id)->with('success', 'Pesanan ini sudah lunas');
        }

        if ($order->delivery_method === 'delivery' && $order->delivery_distance === null) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Pembayaran belum tersedia karena pengiriman belum dikonfirmasi admin.');
        }

        return view('customer.payment.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $order = $this->ownedOrder($order);
        $back  = redirect()->route('customer.orders.show', $order->id);

        if ($order->isFullyPaid()) {
            return $back->with('error', 'Pesanan ini sudah lunas');
        }
        if ($order->hasPendingPayment()) {
            return $back->with('error', 'Masih ada pembayaran yang menunggu verifikasi admin.');
        }
        if ($order->delivery_method === 'delivery' && $order->delivery_distance === null) {
            return $back->with('error', 'Pembayaran belum tersedia karena pengiriman belum dikonfirmasi admin.');
        }

        $min = (int) round($order->minPayment());
        $max = (int) round($order->maxPayment());

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:' . $min, 'max:' . $max],
            'method' => 'required|in:transfer,qris',
            'proof'  => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'notes'  => 'nullable|string|max:255',
        ], [
            'amount.integer' => 'Nominal harus berupa angka bulat, tanpa titik atau koma.',
            'amount.min'     => 'Nominal minimal Rp' . number_format($min, 0, ',', '.')
                                . ($order->paidAmount() > 0 ? '.' : ' (' . Order::MIN_PAYMENT_PERCENT . '% dari total pesanan).'),
            'amount.max'     => 'Nominal tidak boleh melebihi sisa tagihan Rp' . number_format($max, 0, ',', '.') . '.',
        ]);

        $amount = (float) $validated['amount'];
        $lunas  = $amount >= $order->remainingAmount() - 0.5;

        if ($lunas) {
            $amount = $order->remainingAmount();
        }

        // Jenis ditentukan server dari nominal, bukan dipilih customer
        $type = $order->paidAmount() > 0
            ? ($lunas ? 'pelunasan' : 'cicilan')
            : ($lunas ? 'lunas' : 'dp');

        $proof = $request->file('proof')->store('payments', 'public');

        $payment = Payment::create([
            'order_id'       => $order->id,
            'type'           => $type,
            'method'         => $validated['method'],
            'amount'         => $amount,
            'claimed_amount' => $amount,
            'status'         => 'Menunggu Verifikasi',
            'proof'          => $proof,
            'paid_at'        => null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        app(\App\Services\AdminNotifier::class)->paymentProof($payment);

        return redirect()->route('checkout.success', $order->id)->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin');
    }

    private function ownedOrder(Order $order): Order
    {
        $customer = Auth::guard('customer')->user()->customer;
        abort_if(!$customer, 404);

        return $customer->orders()->with('payments')->findOrFail($order->id);
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
