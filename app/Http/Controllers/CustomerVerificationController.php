<?php

namespace App\Http\Controllers;

use App\Models\CustomerAccount;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerVerificationController extends Controller
{
    public function notice()
    {
        $account = Auth::guard('customer')->user();

        /** @var \App\Models\CustomerAccount $account */
        if ($account->hasVerifiedEmail()) {
            return redirect()->route('checkout.index');
        }

        return view('customer.auth.verify-notice', ['email' => $account->email]);
    }

    public function send(Request $request)
    {
        $account = Auth::guard('customer')->user();

        /** @var \App\Models\CustomerAccount $account */
        if ($account->hasVerifiedEmail()) {
            return redirect()->route('checkout.index');
        }

        try {
            $account->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Email belum bisa dikirim saat ini. Coba lagi beberapa saat.');
        }

        return back()->with('success', 'Link verifikasi baru sudah dikirim ke email kamu.');
    }

    // Link dari email (sudah dilindungi middleware "signed"). Bisa dibuka dari perangkat lain.
    public function verify(Request $request, int $id, string $hash)
    {
        $account = CustomerAccount::findOrFail($id);

        if (! hash_equals(sha1($account->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (! $account->hasVerifiedEmail()) {
            $account->markEmailAsVerified();
            event(new Verified($account));
        }

        $target = Auth::guard('customer')->check() ? route('home') : route('customer.login');

        return redirect($target)->with('success', 'Email berhasil diverifikasi. Kamu sudah bisa checkout.');
    }
}