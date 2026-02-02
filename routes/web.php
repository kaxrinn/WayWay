<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PemilikWisataController;
use App\Http\Controllers\WisatawanController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\Api\DestinasiController as ApiDestinasiController;
use App\Http\Controllers\TranksaksiPromosiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AdminProfileController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (BELUM LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('wisatawan.login');
    Route::post('/login', [AuthController::class, 'login'])->name('wisatawan.loginPost');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('wisatawan.register');
    Route::post('/register', [AuthController::class, 'register'])->name('wisatawan.registerPost');

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
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| PUBLIC BERANDA
|--------------------------------------------------------------------------
*/
Route::get('/', [WisatawanController::class, 'beranda'])->name('beranda');
Route::get('/wisatawan/beranda', [WisatawanController::class, 'beranda'])->name('wisatawan.beranda');

/*
|--------------------------------------------------------------------------
| API ROUTES (AJAX)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get(
        '/destinasi/kategori/{kategoriId}',
        [ApiDestinasiController::class, 'byKategori']
    );
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::put('/profile', [\App\Http\Controllers\AdminProfileController::class, 'update'])
            ->name('profile.update');

        // Pemilik Wisata
        Route::resource('pemilik', PemilikWisataController::class);

        // Wisatawan
        Route::get('/wisatawan', function () {
            $wisatawan = \App\Models\User::where('role', 'wisatawan')->latest()->get();
            return view('admin.wisatawan.index', compact('wisatawan'));
        })->name('wisatawan.index');

        // Destinasi
        Route::resource('destinasi', DestinasiController::class);

        // API ambil destinasi berdasarkan kategori (UNTUK FILTER)
        Route::get('/destinasi/kategori/{id}', [DestinasiController::class, 'byKategori'])
            ->name('destinasi.byKategori');

        // ================= KATEGORI =================
        Route::get('/kategori', [KategoriController::class, 'index'])
            ->name('kategori.index');

        Route::post('/kategori', [KategoriController::class, 'store'])
            ->name('kategori.store');

        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])
            ->name('kategori.update');

        Route::get('/kategori/{kategori}/data', [KategoriController::class, 'getData'])
            ->name('kategori.data');
        // ============================================


        // ==============================
        // PROMOSI
        // ==============================
        Route::get('/promosi', function () {
            $promosi = \App\Models\Promosi::with(['destinasi.kategori', 'paket'])
                ->latest()->get();
            $paketPromosi = \App\Models\PaketPromosi::where('status', 'active')->get();

            return view('admin.promosi.index', compact('promosi', 'paketPromosi'));
        })->name('promosi.index');

        // ==============================
        // TRANSAKSI PROMOSI
        // ==============================
        Route::get('/transaksi', function () {
            $transaksi = \App\Models\TransaksiPromosi::with([
                'user',
                'paket',
                'promosi.destinasi'
            ])->latest()->get();

            $stats = [
                'total' => \App\Models\TransaksiPromosi::count(),
                'pending' => \App\Models\TransaksiPromosi::where('status_pembayaran', 'pending')->sum('total_harga'),
                'success' => \App\Models\TransaksiPromosi::where('status_pembayaran', 'success')->sum('total_harga'),
            ];

            return view('admin.transaksi.index', compact('transaksi', 'stats'));
        })->name('transaksi.index');

        // ==============================
        // BANTUAN (HUBUNGI KAMI)
        // ==============================
        Route::get('/bantuan', function () {
            $messages = \App\Models\HubungiKami::with('user')
                ->orderByRaw("
                    CASE status 
                        WHEN 'pending' THEN 1 
                        WHEN 'processed' THEN 2 
                        WHEN 'resolved' THEN 3 
                    END
                ")
                ->latest()
                ->get();

            $stats = [
                'pending' => \App\Models\HubungiKami::where('status', 'pending')->count(),
                'processed' => \App\Models\HubungiKami::where('status', 'processed')->count(),
                'resolved' => \App\Models\HubungiKami::where('status', 'resolved')->count(),
            ];

            return view('admin.bantuan.index', compact('messages', 'stats'));
        })->name('bantuan.index');

        Route::post('/bantuan/{id}/update-status', function ($id) {
            $message = \App\Models\HubungiKami::findOrFail($id);

            if ($message->status === 'pending') {
                $newStatus = 'processed';
            } elseif ($message->status === 'processed') {
                $newStatus = 'resolved';
            } else {
                $newStatus = $message->status;
            }

            $message->update(['status' => $newStatus]);

            return redirect()
                ->route('admin.bantuan.index')
                ->with('success', 'Status berhasil diupdate!');
        })->name('bantuan.update-status');
    });

    

/*
|--------------------------------------------------------------------------
| PEMILIK WISATA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pemilik_wisata'])
    ->prefix('pemilik')
    ->name('pemilik.')
    ->group(function () {
        Route::get('/dashboard', [PemilikWisataController::class, 'dashboard'])->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| WISATAWAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wisatawan'])
    ->prefix('wisatawan')
    ->name('wisatawan.')
    ->group(function () {
        Route::get('/profil', [WisatawanController::class, 'profile'])->name('profile');
    });

require __DIR__.'/auth.php';
