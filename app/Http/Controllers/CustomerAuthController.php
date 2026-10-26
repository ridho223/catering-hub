<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAuthController extends Controller
{
    public function login()
    {
        if(Auth::guard('customer')->check()){
            return redirect('/');
        }

    $previous = url()->previous();
    if (!in_array($previous, [route('customer.login'), route('customer.register')])) {
        session(['url.intended' => $previous]);
    }

        return view('customer.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ]);        

        if(!Auth::guard('customer')->attempt(
            $credentials,
            $request->boolean('remember')
        )){
            return back()->withInput()->with('error','Email atau password salah.');
        }

        session(['success' => 'Selamat datang kembali!']);

        return redirect()->intended('/');
    }

    public function register()
    {
        if(Auth::guard('customer')->check()){
            return redirect()->route('checkout.index');
        }

        return view('customer.auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customer_accounts,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account = DB::transaction(function () use ($validated) {

            if (CustomerAccount::where('email', $validated['email'])->exists()) {
                throw new \Exception('Email tersebut sudah memiliki akun.');
            }

            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            return CustomerAccount::create([
                'customer_id' => $customer->id,
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        });

        Auth::guard('customer')->login($account);

        $request->session()->regenerate();

        return redirect()
            ->route('checkout.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Berhasil logout.');
    }
}
