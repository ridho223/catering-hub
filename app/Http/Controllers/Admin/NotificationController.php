<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAlertData;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private const ALLOWED_ROUTES = ['orders.show', 'admin.payments.show'];

    // dipanggil JavaScript tiap 20 detik
    public function poll(Request $request)
    {
        return response()->json(AdminAlertData::for($request->user()));
    }

    // klik notifikasi: tandai dibaca, lalu buka halaman tujuan
    public function open(Request $request, string $id)
    {
        $n = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $n->markAsRead();

        $route = $n->data['route'] ?? null;
        if (in_array($route, self::ALLOWED_ROUTES, true)) {
            return redirect()->route($route, $n->data['params'] ?? []);
        }
        return redirect()->route('dashboard');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }
}