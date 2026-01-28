<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ], [
            'password.min' => 'Kata sandi minimal harus 8 karakter',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (Auth::attempt($request->only('email','password'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'pemilik_wisata') {
                return redirect()->route('pemilik.dashboard');
            } else {
                return redirect()->route('wisatawan.beranda');
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal harus 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password.regex' => 'Kata sandi harus mengandung huruf dan angka',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'wisatawan',
        ]);

        return redirect()->route('wisatawan.login')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)
                ->orWhere('google_id', $googleUser->id)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role' => 'wisatawan',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            return redirect()->route('wisatawan.beranda');

        } catch (\Exception $e) {
            return redirect()->route('wisatawan.login')
                ->with('error', 'Gagal login dengan Google.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('wisatawan.login');
    }
}
