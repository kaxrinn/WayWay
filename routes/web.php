<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PemilikWisataController;
use App\Http\Controllers\WisatawanController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (BELUM LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('wisatawan.login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('wisatawan.loginPost');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('wisatawan.register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('wisatawan.registerPost');

    // Lupa Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgetPasswordForm'])
        ->name('wisatawan.password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('wisatawan.password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
        ->name('wisatawan.password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('wisatawan.password.update');

});



/*
|--------------------------------------------------------------------------
| GOOGLE OAUTH (JANGAN GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| LOGOUT (SUDAH LOGIN)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/pemilik-wisata', [AdminController::class, 'indexPemilik'])
            ->name('admin.pemilik.index');

        Route::get('/pemilik-wisata/create', [AdminController::class, 'createPemilik'])
            ->name('admin.pemilik.create');

        Route::post('/pemilik-wisata', [AdminController::class, 'storePemilik'])
            ->name('admin.pemilik.store');

        Route::get('/pemilik-wisata/{id}/edit', [AdminController::class, 'editPemilik'])
            ->name('admin.pemilik.edit');

        Route::put('/pemilik-wisata/{id}', [AdminController::class, 'updatePemilik'])
            ->name('admin.pemilik.update');

        Route::delete('/pemilik-wisata/{id}', [AdminController::class, 'destroyPemilik'])
            ->name('admin.pemilik.destroy');
});

/*
|--------------------------------------------------------------------------
| PEMILIK WISATA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pemilik_wisata'])
    ->prefix('pemilik')
    ->group(function () {

        Route::get('/dashboard', [PemilikWisataController::class, 'dashboard'])
            ->name('pemilik.dashboard');
});

/*
|--------------------------------------------------------------------------
| WISATAWAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wisatawan'])
    ->prefix('wisatawan')
    ->group(function () {

        Route::get('/home', [WisatawanController::class, 'home'])
            ->name('wisatawan.home');
});

/*
|--------------------------------------------------------------------------
| DEFAULT REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isPemilikWisata()) {
            return redirect()->route('pemilik.dashboard');
        }

        return redirect()->route('wisatawan.home');
    }

    return redirect()->route('wisatawan.login');
});

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/

// form minta email
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// kirim email reset
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');
    
// form reset password
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// submit password baru
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| Wisatawan Password Reset
|--------------------------------------------------------------------------
*/

// form minta email
Route::get('/wisatawan/forgot-password', function () {
    return view('auth.forgot-password');
})
->middleware('guest')
->name('wisatawan.password.request');

// kirim email reset
Route::post('/wisatawan/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('wisatawan.password.email');

// form reset password
Route::get('/wisatawan/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})
->middleware('guest')
->name('wisatawan.password.reset');

// simpan password baru
Route::post('/wisatawan/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('wisatawan.password.update');
