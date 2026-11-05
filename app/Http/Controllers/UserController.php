<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'level' => 'required|in:Admin,Superadmin',
            'status' => 'required|in:Aktif,Non-Aktif',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $avatar = null;

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar')->store('avatars','public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'status' => $validated['status'],
            'avatar' => $avatar,
        ]);

        return redirect()->route('users.index')->with('success','User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.,
     */
    public function show(string $id)
    {
        return view('admin.profile.show', ['user' => Auth::user()]);   // sesuaikan nama view        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */     
 
    public function update(Request $request)
    {
        $user = Auth::user();
 
        // Hanya nama, email, dan foto. Level dan status sengaja tidak bisa diubah dari sini.
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);
 
        $user->name  = $validated['name'];
        $user->email = $validated['email'];
 
        if ($request->hasFile('avatar')) {
            // foto baru: hapus yang lama lalu simpan
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
 
        } elseif ($request->boolean('remove_avatar') && $user->avatar) {
            // hanya hapus foto
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        }
 
        $user->save();
 
        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }
 
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);
 
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
 
        return redirect()->route('admin.profile')->with('success', 'Password berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if($user->id === Auth::id()){
            return back()->with('error','Kamu tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success','User berhasil dihapus');
    }
}
