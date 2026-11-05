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
        $orders = Order::with(['customer','payments'])->latest()->paginate(15);
        return view('payments.index', compact('orders'));
    }

    public function show(int $id)
    {
        $payment = Payment::with([            
            'order.packages.package',
            'order.details.menu',
        ])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }


    public function verify(Request $request, int $id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        if ($payment->status !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $order = $payment->order;
        if (!$order) {
            return back()->with('error', 'Pesanan untuk pembayaran ini tidak ditemukan.');
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:1',
        ]);

        // Nominal terverifikasi. Kalau admin tidak mengubah, pakai nominal yang diklaim customer.
        $amount = round((float) ($validated['amount'] ?? $payment->amount), 2);

        $sudahDibayar = (float) $order->payments()->where('status', 'Dibayar')->sum('amount');
        $sisa = round((float) $order->total - $sudahDibayar, 2);

        if ($amount <= 0) {
            return back()->with('error', 'Nominal pembayaran harus lebih dari Rp0.');
        }
        if ($amount > $sisa) {
            return back()->with('error', 'Nominal melebihi sisa tagihan (Rp' . number_format($sisa, 0, ',', '.') . ').');
        }

        $lunas = $amount >= $sisa;
        $type  = $sudahDibayar > 0
            ? ($lunas ? 'pelunasan' : 'cicilan')
            : ($lunas ? 'lunas' : 'dp');

        DB::transaction(function () use ($payment, $order, $amount, $type) {
            $payment->update([
                'amount'  => $amount,          // nominal terverifikasi. claimed_amount tetap utuh sebagai catatan
                'type'    => $type,
                'status'  => 'Dibayar',
                'paid_at' => now(),
            ]);

            if ($order->status === 'Pending') {
                $order->update(['status' => 'Diproses']);
            }
        });

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    // Admin mencatat pembayaran tunai (DP atau pelunasan di tempat)
    public function storeCash(Request $request, Order $order)
    {
        $sisa = $order->remainingAmount();

        if ($sisa <= 0) {
            return back()->with('error', 'Pesanan ini sudah lunas.');
        }
        if ($order->hasPendingPayment()) {
            return back()->with('error', 'Masih ada bukti bayar yang menunggu verifikasi. Proses itu dulu.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];
        if ($amount > $sisa) {
            return back()->withInput()->with('error', 'Nominal melebihi sisa tagihan (Rp' . number_format($sisa, 0, ',', '.') . ').');
        }

        $type = $order->paidAmount() > 0 ? 'pelunasan' : ($amount >= $sisa ? 'lunas' : 'dp');

        DB::transaction(function () use ($order, $amount, $type, $validated) {
            Payment::create([
                'order_id' => $order->id,
                'type'     => $type,
                'method'   => 'tunai',
                'amount'   => $amount,
                'status'   => 'Dibayar',
                'proof'    => null,
                'paid_at'  => now(),
                'notes'    => $validated['notes'] ?? 'Pembayaran tunai dicatat admin',
            ]);

            if ($order->status === 'Pending') {
                $order->update(['status' => 'Diproses']);
            }
        });

        return back()->with('success', 'Pembayaran tunai berhasil dicatat.');
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
