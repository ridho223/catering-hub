<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('shop.require_email_verification')) {
            $account = Auth::guard('customer')->user();

            /** @var \App\Models\CustomerAccount|null $account */
            if ($account && ! $account->hasVerifiedEmail()) {
                return redirect()->route('customer.verification.notice');
            }
        }

        return $next($request);
    }
}