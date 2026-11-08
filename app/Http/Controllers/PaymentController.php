<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        $order = $this->ownedOrder($order);

        if (in_array($order->status, ['Selesai', 'Dibatalkan'], true) && ! $order->isFullyPaid()) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Pesanan ini sudah ' . strtolower($order->status) . ' dan tidak bisa dibayar.');
        }

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

        if ($order->status === 'Dibatalkan') {
            return $back->with('error', 'Pesanan ini sudah dibatalkan dan tidak bisa dibayar.');
        }
        if ($order->isFullyPaid()) {
            return $back->with('error', 'Pesanan ini sudah lunas');
        }
        if ($order->hasPendingPayment()) {
            return $back->with('error', 'Masih ada pembayaran yang menunggu verifikasi admin.');
        }
        if ($order->delivery_method === 'delivery' && $order->delivery_distance === null) {
            return $back->with('error', 'Pembayaran belum tersedia karena pengiriman belum dikonfirmasi admin.');
        }

        $min = max(1, (int) round($order->minPayment()));
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

        // Bukti bayar disimpan di disk private (tidak bisa dibuka lewat URL publik).
        $proof = $request->file('proof')->store('payments', 'local');

        try {
            $payment = DB::transaction(function () use ($order, $validated, $proof) {
                // Kunci pesanan: klik ganda / dua tab tidak bisa membuat dua pembayaran sekaligus.
                $fresh = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                $fresh->load('payments');

                if (in_array($fresh->status, ['Selesai', 'Dibatalkan'], true) && ! $fresh->isFullyPaid()) {
                    throw new \RuntimeException('Pesanan ini sudah ' . strtolower($fresh->status) . ' dan tidak bisa dibayar.');
                }
                if ($fresh->isFullyPaid()) {
                    throw new \RuntimeException('Pesanan ini sudah lunas');
                }
                if ($fresh->hasPendingPayment()) {
                    throw new \RuntimeException('Masih ada pembayaran yang menunggu verifikasi admin.');
                }

                $remaining = (float) $fresh->remainingAmount();
                $amount    = (float) $validated['amount'];

                if ($amount > $remaining + 0.5) {
                    throw new \RuntimeException('Nominal tidak boleh melebihi sisa tagihan Rp' . number_format($remaining, 0, ',', '.') . '.');
                }

                $lunas = $amount >= $remaining - 0.5;

                if ($lunas) {
                    $amount = $remaining;
                }

                // Jenis ditentukan server dari nominal, bukan dipilih customer
                $type = $fresh->paidAmount() > 0
                    ? ($lunas ? 'pelunasan' : 'cicilan')
                    : ($lunas ? 'lunas' : 'dp');

                return Payment::create([
                    'order_id'       => $fresh->id,
                    'type'           => $type,
                    'method'         => $validated['method'],
                    'amount'         => $amount,
                    'claimed_amount' => $amount,
                    'status'         => 'Menunggu Verifikasi',
                    'proof'          => $proof,
                    'paid_at'        => null,
                    'notes'          => $validated['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            Storage::disk('local')->delete($proof);

            return $back->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($proof);
            report($e);

            return $back->with('error', 'Pembayaran gagal dikirim. Silakan coba lagi.');
        }

        app(\App\Services\AdminNotifier::class)->paymentProof($payment);

        return redirect()->route('checkout.success', $order->id)->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin');
    }

    private function ownedOrder(Order $order): Order
    {
        $customer = Auth::guard('customer')->user()->customer;
        abort_if(! $customer, 404);

        return $customer->orders()->with('payments')->findOrFail($order->id);
    }
}