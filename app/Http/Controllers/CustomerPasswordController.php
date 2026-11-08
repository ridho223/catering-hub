<?php

namespace App\Http\Controllers;

use App\Models\CustomerAccount;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CustomerPasswordController extends Controller
{
    public function request()
    {
        return view('customer.auth.forgot-password');
    }

    /**
     * Kirim link reset HANYA ke email yang terdaftar. Email yang tidak terdaftar
     * tidak dikirimi apa pun dan customer diberi tahu.
     */
    public function email(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email|max:255']);
        $email = strtolower($validated['email']);

        if (! CustomerAccount::where('email', $email)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Email tidak terdaftar. Periksa lagi penulisannya atau daftar akun baru.');
        }

        try {
            $status = Password::broker('customers')->sendResetLink(['email' => $email]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Email belum bisa dikirim saat ini. Coba lagi beberapa saat.');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput()
                ->with('error', 'Link reset baru saja dikirim. Tunggu sebentar sebelum meminta lagi.');
        }

        return back()->with('success', 'Link reset password sudah dikirim ke ' . $email . '. Cek inbox atau folder spam.');
    }

    public function reset(Request $request, string $token)
    {
        return view('customer.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($account, $password) {
                $account->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ]);

                // Berhasil lewat link email = kepemilikan email terbukti.
                if (! $account->hasVerifiedEmail()) {
                    $account->markEmailAsVerified();
                }

                $account->save();

                event(new PasswordReset($account));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('customer.login')->with('success', 'Password berhasil diubah. Silakan login.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Link reset tidak valid atau sudah kedaluwarsa. Minta link baru.');
    }
}