<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomerVerifyEmail extends VerifyEmail
{
    // Link bertanda tangan (signed) ke halaman verifikasi customer, bukan route bawaan Laravel.
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'customer.verification.verify',
            Carbon::now()->addMinutes(
                Config::get('auth.verification.expire', 60)
            ),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    // Email verifikasi: berbahasa Indonesia dengan tampilan yang sama seperti email lainnya.
    public function toMail($notifiable)
    {
        $minutes = (int) Config::get('auth.verification.expire', 60);

        return (new MailMessage)
            ->subject('Verifikasi email ' . config('app.name'))
            ->view('emails.action', [
                'greeting' => 'Halo!',
                'lines' => [
                    'Klik tombol di bawah untuk memverifikasi alamat emailmu di ' . config('app.name') . ' supaya kamu bisa memesan dan membayar.',
                ],
                'actionText' => 'Verifikasi Email',
                'url' => $this->verificationUrl($notifiable),
                'notes' => [
                    'Link ini berlaku ' . $minutes . ' menit.',
                    'Kalau kamu tidak merasa membuat akun, abaikan saja email ini.',
                ],
            ]);
    }
}