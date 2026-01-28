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

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('wisatawan.login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('wisatawan.loginPost');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('wisatawan.register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('wisatawan.registerPost');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgetPasswordForm'])
        ->name('wisatawan.password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('wisatawan.password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
        ->name('wisatawan.password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('wisatawan.password.update');
});

/*
|--------------------------------------------------------------------------
| GOOGLE OAUTH
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| PUBLIC BERANDA (TANPA LOGIN)
|--------------------------------------------------------------------------
*/
Route::get('/', [WisatawanController::class, 'beranda'])
    ->name('beranda');

Route::get('/wisatawan/beranda', [WisatawanController::class, 'beranda'])
    ->name('wisatawan.beranda');

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
| WISATAWAN PRIVATE ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wisatawan'])
    ->prefix('wisatawan')
    ->group(function () {

        Route::get('/profil', [WisatawanController::class, 'profile'])
            ->name('wisatawan.profile');
});

/*
|--------------------------------------------------------------------------
| Wisatawan Password Reset
|--------------------------------------------------------------------------
*/
Route::get('/wisatawan/forgot-password', function () {
    return view('auth.forgot-password');
})
->middleware('guest')
->name('wisatawan.password.request');

Route::post('/wisatawan/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('wisatawan.password.email');

Route::get('/wisatawan/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})
->middleware('guest')
->name('wisatawan.password.reset');

Route::post('/wisatawan/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('wisatawan.password.update');
