<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Notification;

class AdminNotifier
{
    public function newOrder(Order $order): void
    {
        $this->safely(function () use ($order) {
            $order->loadMissing('customer');
            $antar = $order->delivery_method === 'delivery';

        $this->send(
            'order_baru',
            'Pesanan baru ' . $order->order_code,
            ($order->customer->name ?? 'Customer')
                . ' · ' . $this->rupiah($order->total)
                . ' · ' . ($antar ? 'Diantar, perlu isi jarak/ongkos' : 'Ambil sendiri'),
            'orders.show',
            [$order->id]
        );
        });
    }


    public function paymentProof(Payment $payment): void
    {
        $this->safely(function () use ($payment) {
            $payment->loadMissing('order.customer');
            $order = $payment->order;

        $label = $payment->kindLabel();

        $this->send(
            'bukti_bayar',
            'Bukti pembayaran masuk (' . $label . ')',
            ($order->order_code ?? '-') . ' · ' . ($order->customer->name ?? 'Customer') . ' · ' . $this->rupiah($payment->amount),
            'admin.payments.show',
            [$payment->id]
        );
        });
    }

    private function send(string $kind, string $title, string $body, string $route, array $params): void
    {
        $admins = User::where('status', 'Aktif')->get();
        Notification::send($admins, new AdminAlert($kind, $title, $body, $route, $params));        
    }

    private function rupiah($v): string
    {
        return 'Rp ' . number_format((float) $v, 0, ',', '.');
    }

    private function safely(callable $fn): void
    {
        try { $fn ();} catch (\Throwable $e) { report($e); }
    }
}