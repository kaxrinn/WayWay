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
    // ========== TAMPILKAN HALAMAN LOGIN ==========
    public function showLogin()
    {
        return view('auth.login');
    }

    // ========== PROSES LOGIN ==========
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Coba login
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'pemilik_wisata') {
                return redirect()->route('pemilik.dashboard');
            } else {
                return redirect()->route('wisatawan.beranda');
            }
        }

        // Login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput();
    }

    // ========== TAMPILKAN HALAMAN REGISTER ==========
    public function showRegister()
    {
        return view('auth.register');
    }

    // ========== PROSES REGISTER ==========
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf dan angka',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Buat user baru (role default: wisatawan)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'wisatawan',
        ]);

        // Auto login setelah register
        Auth::login($user);

        return redirect()->route('auth.login')
            ->with('success', 'Registrasi berhasil! Selamat datang di Guide Me.');
    }

    // ========== REDIRECT KE GOOGLE ==========
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // ========== CALLBACK DARI GOOGLE ==========
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user sudah ada (berdasarkan email atau google_id)
            $user = User::where('email', $googleUser->email)
                       ->orWhere('google_id', $googleUser->id)
                       ->first();

            if ($user) {
                // User sudah ada, update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                }
            } else {
                // User baru, buat akun baru
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role' => 'wisatawan',
                    'email_verified_at' => now(),
                ]);
            }

            // Login user
            Auth::login($user);

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'pemilik_wisata') {
                return redirect()->route('pemilik.dashboard');
            } else {
                return redirect()->route('wisatawan.beranda');
            }

        } catch (\Exception $e) {
            return redirect()->route('wisatawan.login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

// ========== LOGOUT ==========
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('beranda');
}
}