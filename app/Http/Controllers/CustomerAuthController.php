<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class CustomerAuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('customer')->check()) {
            return redirect('/');
        }

        // Simpan halaman asal hanya jika berasal dari situs ini (cegah open redirect).
        $previous = url()->previous();
        $previousHost = parse_url($previous, PHP_URL_HOST);

        if (
            $previousHost === request()->getHost()
            && ! in_array($previous, [route('customer.login'), route('customer.register')], true)
        ) {
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

        if (! Auth::guard('customer')->attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            return back()->withInput()->with('error', 'Email atau password salah.');
        }

        session(['success' => 'Selamat datang kembali!']);

        return redirect()->intended('/');
    }

    public function register()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('checkout.index');
        }

        return view('customer.auth.register');
    }

    /**
     * Pendaftaran. Dengan verifikasi email aktif, akun BELUM dibuat di sini:
     * data disimpan sementara (60 menit) dan akun baru dibuat setelah link di email diklik.
     */
    public function store(Request $request)
    {
        $request->merge(['phone' => Customer::normalizePhone($request->input('phone'))]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customer_accounts,email',
            'phone' => ['required', 'string', 'regex:/^0[0-9]{8,14}$/'],
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
            'email.unique' => 'Email tersebut sudah memiliki akun. Silakan login.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
        ];

        // Verifikasi dimatikan (mis. SMTP belum siap): daftar langsung seperti biasa.
        if (! config('shop.require_email_verification')) {
            $account = $this->createAccount($data, false);

            Auth::guard('customer')->login($account);
            $request->session()->regenerate();

            return redirect()
                ->route('checkout.index')
                ->with('success', 'Akun berhasil dibuat.');
        }

        if (! $this->sendConfirmation($data)) {
            $this->forgetPending($data['email']);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Email konfirmasi gagal dikirim. Coba lagi beberapa saat, atau hubungi kedai.');
        }

        $request->session()->put('pending_email', $data['email']);

        return redirect()->route('customer.register.check');
    }

    /** Halaman "cek email kamu" (customer belum punya akun, jadi tidak perlu login). */
    public function checkEmail(Request $request)
    {
        $email = $request->session()->get('pending_email');

        if (! $email || $this->pendingData($email) === null) {
            return redirect()->route('customer.register');
        }

        return view('customer.auth.verify-notice', [
            'email' => $email,
            'pending' => true,
        ]);
    }

    /** Kirim ulang email konfirmasi. */
    public function resend(Request $request)
    {
        $email = $request->session()->get('pending_email');
        $data = $email ? $this->pendingData($email) : null;

        if ($data === null) {
            return redirect()->route('customer.register')
                ->with('error', 'Data pendaftaran tidak ditemukan atau sudah kedaluwarsa. Silakan daftar lagi.');
        }

        if (! $this->sendConfirmation($data)) {
            return back()->with('error', 'Email belum bisa dikirim saat ini. Coba lagi beberapa saat.');
        }

        return back()->with('success', 'Link konfirmasi baru sudah dikirim. Cek inbox dan folder spam.');
    }

    /** Link dari email (dilindungi middleware "signed"): di sinilah akun benar-benar dibuat. */
    public function confirm(Request $request, string $token)
    {
        $hash = hash('sha256', $token);
        $data = Cache::pull($this->dataKey($hash));

        if (! is_array($data)) {
            // Link sudah dipakai (mis. diklik dua kali) dan customer sudah login.
            if (Auth::guard('customer')->check()) {
                return redirect()->route('home');
            }

            return redirect()->route('customer.register')
                ->with('error', 'Link konfirmasi tidak valid atau sudah kedaluwarsa. Silakan daftar lagi.');
        }

        Cache::forget($this->emailKey($data['email']));

        if (CustomerAccount::where('email', $data['email'])->exists()) {
            return redirect()->route('customer.login')
                ->with('error', 'Email tersebut sudah memiliki akun. Silakan login.');
        }

        $account = $this->createAccount($data, true);

        $request->session()->forget('pending_email');

        Auth::guard('customer')->login($account);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Email terverifikasi dan akun berhasil dibuat. Selamat datang!');
    }

    /* =========================================================
     |  LOGIN DENGAN GOOGLE
     |========================================================= */

    public function googleRedirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('customer.login')
                ->with('error', 'Login Google belum dikonfigurasi. Hubungi admin.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('customer.login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $email = strtolower((string) $google->getEmail());
        $emailVerified = (bool) ($google->user['email_verified'] ?? $google->user['verified_email'] ?? false);

        // Hanya terima email yang sudah diverifikasi Google (mencegah pengambilalihan akun).
        if ($email === '' || ! $emailVerified) {
            return redirect()->route('customer.login')
                ->with('error', 'Email Google kamu belum terverifikasi.');
        }

        $account = CustomerAccount::where('email', $email)->first();

        if (! $account) {
            // Akun baru lewat Google. No. HP & alamat dilengkapi nanti di halaman profil/checkout.
            $account = DB::transaction(function () use ($google, $email) {
                $customer = Customer::create([
                    'name' => $google->getName() ?: Str::before($email, '@'),
                    'email' => $email,
                    'phone' => '',
                    'address' => '',
                ]);

                $account = new CustomerAccount();
                $account->customer_id = $customer->id;
                $account->email = $email;
                $account->password = Str::random(40); // tidak dipakai; di-hash otomatis oleh cast
                $account->email_verified_at = now();
                $account->save();

                return $account;
            });
        } elseif (! $account->hasVerifiedEmail()) {
            // Email yang sama sudah terbukti milik pengguna lewat Google.
            $account->email_verified_at = now();
            $account->save();
        }

        Auth::guard('customer')->login($account, true);
        $request->session()->regenerate();

        if (($account->customer?->phone ?? '') === '' || ($account->customer?->address ?? '') === '') {
            return redirect()
                ->route('customer.profile')
                ->with('success', 'Selamat datang! Lengkapi nomor HP dan alamat kamu dulu ya.');
        }

        return redirect()->intended('/')->with('success', 'Selamat datang kembali!');
    }

    /* =========================================================
     |  PROFIL CUSTOMER
     |========================================================= */

    public function profile()
    {
        /** @var \App\Models\CustomerAccount $account */
        $account = Auth::guard('customer')->user();
        $customer = $account->customer;

        abort_if(! $customer, 404);

        return view('customer.profile.index', compact('account', 'customer'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\CustomerAccount $account */
        $account = Auth::guard('customer')->user();
        $customer = $account->customer;

        abort_if(! $customer, 404);

        $request->merge(['phone' => Customer::normalizePhone($request->input('phone'))]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^0[0-9]{8,14}$/'],
            'address' => 'required|string|max:500',
        ], [
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\CustomerAccount $account */
        $account = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        if (! Hash::check($request->input('current_password'), $account->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $account->password = $request->input('password'); // di-hash otomatis oleh cast
        $account->save();

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Password berhasil diubah.');
    }

    public function logout(Request $request)
    {
        // Hanya keluarkan customer. Sesi tidak di-invalidate agar login admin
        // di browser yang sama tidak ikut terhapus.
        Auth::guard('customer')->logout();

        $request->session()->forget('cart');
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Berhasil logout.');
    }

    /* =========================================================
     |  HELPER PENDAFTARAN (data sementara disimpan di cache)
     |========================================================= */

    private function dataKey(string $tokenHash): string
    {
        return 'pending_registration:' . $tokenHash;
    }

    private function emailKey(string $email): string
    {
        return 'pending_registration_email:' . strtolower($email);
    }

    /** Data pendaftaran yang menunggu konfirmasi untuk email ini, atau null. */
    private function pendingData(string $email): ?array
    {
        $hash = Cache::get($this->emailKey($email));

        if (! $hash) {
            return null;
        }

        $data = Cache::get($this->dataKey($hash));

        return is_array($data) ? $data : null;
    }

    private function forgetPending(string $email): void
    {
        $hash = Cache::pull($this->emailKey($email));

        if ($hash) {
            Cache::forget($this->dataKey($hash));
        }
    }

    /** Buat customer + akun login dalam satu transaksi. $data['password'] sudah di-hash. */
    private function createAccount(array $data, bool $verified): CustomerAccount
    {
        return DB::transaction(function () use ($data, $verified) {
            $customer = Customer::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            $account = new CustomerAccount();
            $account->customer_id = $customer->id;
            $account->email = $data['email'];
            $account->password = $data['password'];
            $account->email_verified_at = $verified ? now() : null;
            $account->save();

            return $account;
        });
    }

    /** Simpan data sementara dengan token baru lalu kirim email. False bila pengiriman gagal. */
    private function sendConfirmation(array $data): bool
    {
        $token = Str::random(48);
        $hash = hash('sha256', $token);
        $expires = now()->addMinutes(60);

        // Link lama untuk email yang sama dibatalkan.
        $this->forgetPending($data['email']);

        Cache::put($this->dataKey($hash), $data, $expires);
        Cache::put($this->emailKey($data['email']), $hash, $expires);

        $url = URL::temporarySignedRoute('customer.register.confirm', $expires, ['token' => $token]);

        try {
            Mail::html(
                $this->confirmationHtml($data['name'], $url),
                function ($message) use ($data) {
                    $message->to($data['email'])
                        ->subject('Konfirmasi pendaftaran akun ' . config('app.name'));
                }
            );
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return true;
    }

    private function confirmationHtml(string $name, string $url): string
    {
        $app = e(config('app.name'));
        $name = e($name);
        $url = e($url);
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:0;background:#f3f6f4;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6f4;padding:32px 12px;">
  <tr>
    <td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:14px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
        <tr>
          <td style="background:#198754;padding:26px 32px;text-align:center;">
            <div style="color:#ffffff;font-size:22px;font-weight:bold;letter-spacing:0.3px;">{$app}</div>
          </td>
        </tr>
        <tr>
          <td style="padding:32px 32px 8px;color:#212529;font-size:15px;line-height:1.65;">
            <h2 style="margin:0 0 12px;font-size:20px;color:#145c38;">Halo, {$name}!</h2>
            <p style="margin:0 0 12px;">Terima kasih sudah mendaftar di <b>{$app}</b>. Satu langkah lagi: klik tombol di bawah untuk mengaktifkan akunmu.</p>
          </td>
        </tr>
        <tr>
          <td align="center" style="padding:12px 32px 24px;">
            <a href="{$url}" style="background:#198754;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:14px 32px;border-radius:10px;display:inline-block;">Aktifkan Akun</a>
          </td>
        </tr>
        <tr>
          <td style="padding:0 32px 28px;color:#6c757d;font-size:13px;line-height:1.6;">
            <p style="margin:0 0 10px;">Link berlaku <b>60 menit</b>. Akunmu baru dibuat setelah link ini diklik.</p>
            <p style="margin:0 0 10px;">Kalau tombol tidak berfungsi, salin dan tempel link ini di browser:</p>
            <p style="margin:0;word-break:break-all;"><a href="{$url}" style="color:#198754;">{$url}</a></p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8faf8;padding:18px 32px;text-align:center;color:#8a948d;font-size:12px;line-height:1.5;">
            Kalau kamu tidak merasa mendaftar, abaikan saja email ini.<br>
            &copy; {$year} {$app}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
    }
}