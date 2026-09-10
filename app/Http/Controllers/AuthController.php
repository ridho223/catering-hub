<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' =>  'required|email',
            'password' => 'required',
        ]);
        
        if(!Auth::attempt($credentials)){
            return back()->withInput()->with('error','Email atau password salah.');
        }

        $user = Auth::user();

        if($user->status !== 'Aktif') {
            Auth::logout();

            return back()->withInput()->with('error','Akun kamu sedang tidak aktif.');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success','Berhasil logout');
    }

    public function profile()
    {
        $user = Auth::user();

        return view('admin.profile', compact('user'));
    }

    public function editProfile() 
    {
        $user = Auth::user();
        
        return view('admin.profile-edit',compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile')->with('success','Profile berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('admin.password-edit');
    }

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = User::findOrFail(Auth::id());


    if (!Hash::check($request->current_password, $user->password)) {
        return back()
            ->withErrors([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return redirect()
        ->route('admin.profile')
        ->with('success', 'Password berhasil diubah.');
}
}
