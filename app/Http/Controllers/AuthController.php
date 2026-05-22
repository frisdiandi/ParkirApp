<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByLevel(Auth::user()->level);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput($request->only('username'));
        }

        if (!$user->isAktif()) {
            return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi administrator.'])->withInput($request->only('username'));
        }

        Auth::login($user, $request->boolean('remember'));

        return $this->redirectByLevel($user->level);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    private function redirectByLevel(int $level)
    {
        return match($level) {
            1, 3    => redirect()->route('admin.dashboard'),
            2       => redirect()->route('petugas.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
