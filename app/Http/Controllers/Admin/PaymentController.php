<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    // Bukti pembayaran hanya bisa dibuka admin yang login (bukan lewat URL publik).
    // Bukti lama di disk public tetap bisa dibuka sampai dipindah: php artisan payments:privatize-proofs
    public function proof(int $id)
    {
        $payment = Payment::findOrFail($id);

        abort_if(! $payment->proof, 404);

        foreach (['local', 'public'] as $diskName) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk($diskName);

            if ($disk->exists($payment->proof)) {
                return $disk->response($payment->proof);
            }
        }

        abort(404);
    }

    public function verify(Request $request, int $id)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:1',
        ]);

        try {
            $payment = DB::transaction(function () use ($id, $validated) {
                // Kunci pembayaran + pesanan: dua klik/tab bersamaan tidak bisa lolos dua kali.
                $payment = Payment::whereKey($id)->lockForUpdate()->firstOrFail();

                if ($payment->status !== 'Menunggu Verifikasi') {
                    throw new \RuntimeException('Pembayaran ini sudah diproses.');
                }

                $order = Order::whereKey($payment->order_id)->lockForUpdate()->first();

                if (! $order) {
                    throw new \RuntimeException('Pesanan untuk pembayaran ini tidak ditemukan.');
                }
                if ($order->status === 'Dibatalkan') {
                    throw new \RuntimeException('Pesanan ini sudah dibatalkan.');
                }

                // Nominal terverifikasi. Kalau admin tidak mengubah, pakai nominal yang diklaim customer.
                $amount = round((float) ($validated['amount'] ?? $payment->amount), 2);

                $sudahDibayar = (float) Payment::where('order_id', $order->id)->where('status', 'Dibayar')->sum('amount');
                $sisa = round((float) $order->total - $sudahDibayar, 2);

                if ($amount <= 0) {
                    throw new \RuntimeException('Nominal pembayaran harus lebih dari Rp0.');
                }
                if ($amount > $sisa) {
                    throw new \RuntimeException('Nominal melebihi sisa tagihan (Rp' . number_format($sisa, 0, ',', '.') . ').');
                }

                $lunas = $amount >= $sisa;
                $type  = $sudahDibayar > 0
                    ? ($lunas ? 'pelunasan' : 'cicilan')
                    : ($lunas ? 'lunas' : 'dp');

                $payment->update([
                    'amount'  => $amount,          // nominal terverifikasi. claimed_amount tetap utuh sebagai catatan
                    'type'    => $type,
                    'status'  => 'Dibayar',
                    'paid_at' => now(),
                ]);

                if ($order->status === 'Pending') {
                    $order->update(['status' => 'Diproses']);
                }

                return $payment;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    // Admin mencatat pembayaran tunai (DP atau pelunasan di tempat)
    public function storeCash(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($order, $validated) {
                $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

                if ($locked->status === 'Dibatalkan') {
                    throw new \RuntimeException('Pesanan ini sudah dibatalkan.');
                }

                $sudahDibayar = (float) Payment::where('order_id', $locked->id)->where('status', 'Dibayar')->sum('amount');
                $sisa = round((float) $locked->total - $sudahDibayar, 2);

                if ($sisa <= 0) {
                    throw new \RuntimeException('Pesanan ini sudah lunas.');
                }
                if (Payment::where('order_id', $locked->id)->where('status', 'Menunggu Verifikasi')->exists()) {
                    throw new \RuntimeException('Masih ada bukti bayar yang menunggu verifikasi. Proses itu dulu.');
                }

                $amount = round((float) $validated['amount'], 2);

                if ($amount > $sisa) {
                    throw new \RuntimeException('Nominal melebihi sisa tagihan (Rp' . number_format($sisa, 0, ',', '.') . ').');
                }

                $lunas = $amount >= $sisa;
                $type  = $sudahDibayar > 0
                    ? ($lunas ? 'pelunasan' : 'cicilan')
                    : ($lunas ? 'lunas' : 'dp');

                Payment::create([
                    'order_id' => $locked->id,
                    'type'     => $type,
                    'method'   => 'tunai',
                    'amount'   => $amount,
                    'status'   => 'Dibayar',
                    'proof'    => null,
                    'paid_at'  => now(),
                    'notes'    => $validated['notes'] ?? 'Pembayaran tunai dicatat admin',
                ]);

                if ($locked->status === 'Pending') {
                    $locked->update(['status' => 'Diproses']);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran tunai berhasil dicatat.');
    }

    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                $payment = Payment::whereKey($id)->lockForUpdate()->firstOrFail();

                if ($payment->status !== 'Menunggu Verifikasi') {
                    throw new \RuntimeException('Pembayaran ini sudah diproses');
                }

                $payment->update([
                    'status'  => 'Ditolak',
                    'notes'   => $validated['notes'],
                    'paid_at' => null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran ditolak. Customer dapat mengirim bukti pembayaran kembali');
    }
}