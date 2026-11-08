<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PrivatizePaymentProofs extends Command
{
    protected $signature = 'payments:privatize-proofs';

    protected $description = 'Pindahkan bukti pembayaran lama dari disk public ke disk private';

    public function handle(): int
    {
        $moved = 0;
        $public = Storage::disk('public');
        $private = Storage::disk('local');

        foreach (Payment::whereNotNull('proof')->get() as $payment) {
            $path = $payment->proof;

            if ($public->exists($path) && ! $private->exists($path)) {
                $private->put($path, $public->get($path));
                $public->delete($path);
                $moved++;
            }
        }

        $this->info("{$moved} bukti pembayaran dipindahkan ke penyimpanan private.");

        return self::SUCCESS;
    }
}