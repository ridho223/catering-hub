<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Batalkan pesanan Pending yang tidak dibayar melewati batas waktu dan kembalikan stok menu';

    public function handle(): int
    {
        $hours = max(1, (int) config('shop.pending_expire_hours', 48));

        $orders = Order::where('status', 'Pending')
            ->where('created_at', '<', now()->subHours($hours))
            ->whereDoesntHave('payments', function ($q) {
                $q->whereIn('status', ['Dibayar', 'Menunggu Verifikasi']);
            })
            ->get();

        $done = 0;

        foreach ($orders as $order) {
            try {
                if ($order->cancelAndRestock()) {
                    $done++;
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->info("{$done} pesanan kedaluwarsa dibatalkan (batas {$hours} jam).");

        return self::SUCCESS;
    }
}