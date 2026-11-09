<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $credentials['login'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();

            if ($user->status !== 'aktif') {
                Auth::logout();
                return back()->withErrors(['login' => 'Akun Anda sedang dinonaktifkan. Hubungi admin.'])->withInput();
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'login' => 'Email / Username atau password salah.',
        ])->withInput($request->only('login', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->intended(route('user.dashboard'));
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Alamat email tidak terdaftar dalam sistem kami.',
        ]);

        $email = $request->email;
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $maskedEmail = $this->maskEmail($email);

        return redirect()->route('password.check_email')->with([
            'masked_email' => $maskedEmail,
            'demo_token' => $token,
            'demo_email' => $email,
        ]);
    }

    public function showCheckEmail()
    {
        $maskedEmail = session('masked_email');
        if (!$maskedEmail) {
            return redirect()->route('password.request');
        }
        return view('auth.check-email', compact('maskedEmail'));
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Tautan reset password tidak valid.']);
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(30)->isPast()) {
            return back()->withErrors(['email' => 'Tautan reset password sudah kedaluwarsa (lebih dari 30 menit).']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui! Silakan masuk kembali.');
    }

    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 1) . str_repeat('*', max(strlen($name) - 1, 3));
        return $maskedName . '@' . $domain;
    }
}