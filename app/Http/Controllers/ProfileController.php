<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $layout = $user->role === 'admin' ? 'admin.layout' : 'layouts.user';

        return view('profile.index', compact('user', 'layout'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'current_password.required_with' => 'Isi password saat ini untuk mengganti password.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.index')->with('sukses', 'Profil berhasil diperbarui.');
    }
}