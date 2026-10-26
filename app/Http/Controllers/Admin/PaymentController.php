<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer','payment'])->latest()->paginate(15);
        return view('payments.index', compact('orders'));
    }

    public function show(int $id)
    {
        $payment = Payment::with([
            'order.customRequest',
            'order.packages.package',
            'order.details.menu',
        ])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }


public function verify(int $id)
{
    $payment = Payment::with('order')->findOrFail($id);

    if ($payment->status !== 'Menunggu Verifikasi') {
        return back()->with('error', 'Pembayaran ini sudah diproses.');
    }

    $order = $payment->order;

    if (!$order) {
        return back()->with('error', 'Pesanan untuk pembayaran ini tidak ditemukan.');
    }

    $amount = (float) $payment->amount;
    $total = (float) $order->total;

    if ($amount <= 0) {
        return back()->with('error', 'Nominal pembayaran harus lebih dari Rp0.');
    }

    if ($amount > $total) {
        return back()->with(
            'error',
            'Nominal pembayaran melebihi total pesanan. Periksa nominal sebelum verifikasi.'
        );
    }

    DB::transaction(function () use ($payment, $order, $amount, $total) {
        $payment->update([
            'status' => $amount >= $total ? 'Dibayar' : 'Sebagian',
            'paid_at' => now(),
        ]);

        // Pesanan masuk proses setelah pembayaran diverifikasi.
        $order->update([
            'status' => 'Diproses',
        ]);
    });

    return redirect()
        ->route('admin.payments.show', $payment->id)
        ->with('success', 'Pembayaran berhasil diverifikasi.');
}


    public function reject(Request $request, int $id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        if($payment->status !== 'Menunggu Verifikasi'){
            return back()->with('error','Pembayaran ini sudah diproses');
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $payment->update([
            'status' => 'Ditolak',
            'notes' => $validated['notes'],
            'paid_at' => null,
        ]);

        return redirect()->route('admin.payments.index')->with('success','Pembayaran ditolak. Customer dapat mengirim bukti pembayaran kembali');
    }
}
