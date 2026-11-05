<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class AdminAlertData
{
    public static function for(User $user): array
    {
        $data = [
            'payments' => Payment::where('status', 'Menunggu Verifikasi')->count(),
            'shipping' => Order::where('status', 'Pending')
                ->where('delivery_method', 'delivery')
                ->whereNull('delivery_distance')->count(),
            'unread' => 0,
            'items'  => [],
        ];

        try {
            $data['unread'] = $user->unreadNotifications()->count();

            $data['items'] = $user->notifications()->latest()->limit(8)->get()
                ->map(fn ($n) => [
                    'id'     => $n->id,
                    'kind'   => $n->data['kind'] ?? '',
                    'title'  => $n->data['title'] ?? '',
                    'body'   => $n->data['body'] ?? '',
                    'url'    => route('admin.notifications.open', $n->id),
                    'unread' => $n->read_at === null,
                    'time'   => $n->created_at->locale('id')->diffForHumans(),
                ])->values()->all();
        } catch (\Throwable $e) {
            report($e);
             dd($e->getMessage(), $e->getFile(), $e->getLine());
        }

        return $data;
    }
}