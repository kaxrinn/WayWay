<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgetPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token)
    {
        $email = request('email');

        if (!$email) {
            return redirect()->route('wisatawan.password.request')
                ->withErrors(['email' => 'Link reset password tidak valid. Silakan request ulang.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal harus 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password.regex' => 'Kata sandi harus mengandung huruf dan angka',
        ]);

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
                ->with('success', 'Password berhasil dibuat! Silakan login dengan password baru.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function reset(Request $request)
    {
        return $this->resetPassword($request);
    }
}
