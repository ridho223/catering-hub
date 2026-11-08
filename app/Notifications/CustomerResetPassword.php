<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPassword extends ResetPassword
{
    protected function resetUrl($notifiable)
    {
        return url(route('customer.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    // Email reset password: berbahasa Indonesia dengan tampilan yang sama seperti email konfirmasi.
    public function toMail($notifiable)
    {
        $minutes = (int) config('auth.passwords.customers.expire', 60);

        return (new MailMessage)
            ->subject('Reset password ' . config('app.name'))
            ->view('emails.action', [
                'greeting' => 'Halo!',
                'lines' => [
                    'Kami menerima permintaan untuk mengatur ulang password akunmu di ' . config('app.name') . '. Klik tombol di bawah untuk membuat password baru.',
                ],
                'actionText' => 'Reset Password',
                'url' => $this->resetUrl($notifiable),
                'notes' => [
                    'Link ini berlaku ' . $minutes . ' menit.',
                    'Kalau kamu tidak meminta reset password, abaikan saja email ini. Passwordmu tidak berubah.',
                ],
            ]);
    }
}