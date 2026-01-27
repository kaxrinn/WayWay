<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // ========== TAMPILKAN FORM LUPA PASSWORD ==========
    public function showForgetPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // ========== KIRIM LINK RESET KE EMAIL ==========
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
        ]);

        // Kirim email reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // ========== TAMPILKAN FORM RESET PASSWORD ==========
    public function showResetPasswordForm($token)
    {
        // Ambil email dari query string
        $email = request('email');
        
        // Validasi email ada
        if (!$email) {
            return redirect()->route('wisatawan.password.request')
                ->withErrors(['email' => 'Link reset password tidak valid. Silakan request ulang.']);
        }
        
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // ========== PROSES RESET PASSWORD ==========
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf dan angka',
        ]);

        // Reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('wisatawan.login')
                ->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
    
    // ========== ALIAS METHOD UNTUK ROUTE ==========
    // Biar route bisa pakai @reset atau @resetPassword
    public function reset(Request $request)
    {
        return $this->resetPassword($request);
    }
}