<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PemilikWisataController;
use App\Http\Controllers\WisatawanController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\Api\DestinasiApiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\PemilikPromosiController;


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

/* ===== DESTINASI PUBLIK (WISATAWAN) ===== */
Route::get('/destinasi', [DestinasiController::class, 'index'])
    ->name('destinasi.index');

Route::get('/destinasi/{destinasi}', 
    [WisatawanController::class, 'show']
)->name('destinasi.show');

Route::get('/destinasi', [WisatawanController::class, 'index'])
    ->name('destinasi.index');

//ulasan destinasi
Route::post('/ulasan', [UlasanController::class, 'store'])
    ->name('ulasan.store')
    ->middleware('auth');
//kontak
Route::post('/kontak', [WisatawanController::class, 'kirimPesan'])
    ->name('hubungi.kami.store');

    // Toggle favorit (add/remove)
    Route::post('/favorit/toggle', [WisatawanController::class, 'toggle'])
        ->name('wisatawan.favorit.toggle');
    
    // Halaman daftar favorit
    Route::get('/wisatawan/favorit', [WisatawanController::class, 'indexFavorit'])
        ->name('wisatawan.favorit.index');

    Route::put('/wisatawan/profile', [WisatawanController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('wisatawan.profile.update');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard - menggunakan AdminDashboardController yang baru
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Pemilik Wisata Management - menggunakan resource controller
        Route::resource('pemilik', PemilikWisataController::class);
        
        // Wisatawan Management
        Route::get('/wisatawan', function () {
            $wisatawan = \App\Models\User::where('role', 'wisatawan')->latest()->get();
            return view('admin.wisatawan.index', compact('wisatawan'));
        })->name('wisatawan.index');
        
        // Destinasi Management - menggunakan resource controller
        Route::resource('destinasi', DestinasiController::class);
        
        // Kategori Management
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{kategori}/data', [KategoriController::class, 'getData'])->name('kategori.getData');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        
        // Promosi Management
        Route::get('/promosi', function () {
            $promosi = \App\Models\Promosi::with(['destinasi.kategori', 'paket'])->latest()->get();
            $paketPromosi = \App\Models\PaketPromosi::where('status', 'active')->orderBy('harga')->get();
            return view('admin.promosi.index', compact('promosi', 'paketPromosi'));
        })->name('promosi.index');
        
        // Transaksi Management
        Route::get('/transaksi', function () {
            $transaksis = \App\Models\TransaksiPromosi::with(['user', 'paket', 'promosi.destinasi'])->latest()->paginate(10);
            $stats = [
                'total' => \App\Models\TransaksiPromosi::count(),
                'pending' => \App\Models\TransaksiPromosi::where('status_pembayaran', 'pending')->sum('total_harga'),
                'success' => \App\Models\TransaksiPromosi::where('status_pembayaran', 'success')->sum('total_harga'),
            ];
            return view('admin.transaksi.index', compact('transaksis', 'stats'));
        })->name('transaksi.index');

        Route::post('/transaksi/{id}/approve', function($id) {
    $t = \App\Models\TransaksiPromosi::findOrFail($id);
    $t->update(['status_pembayaran' => 'success']);
    if ($t->promosi) {
        $t->promosi->update(['status' => 'active']);
        if ($t->user) {
            $t->user->update([
                'current_paket_id' => $t->paket_id,
                'paket_expired_at' => $t->promosi->tanggal_selesai,
            ]);
        }
    }
    return back()->with('success', 'Approved!');
})->name('transaksi.approve');

Route::post('/transaksi/{id}/reject', function($id) {
    $t = \App\Models\TransaksiPromosi::findOrFail($id);
    $t->update(['status_pembayaran' => 'failed']);
    if ($t->promosi) $t->promosi->update(['status' => 'expired']);
    return back()->with('success', 'Rejected!');
})->name('transaksi.reject');

Route::delete('/transaksi/{id}', function ($id) {
    $t = \App\Models\TransaksiPromosi::findOrFail($id);

    // kalau mau sekalian hapus relasi promosi (opsional)
    if ($t->promosi) {
        $t->promosi->delete();
    }

    $t->delete();

    return back()->with('success', 'Transaksi berhasil dihapus!');
})->name('transaksi.destroy');

        
        // Bantuan (Hubungi Kami) Management
        Route::get('/bantuan', function () {
            $messages = \App\Models\HubungiKami::with('user')->orderByRaw("
                CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'processed' THEN 2 
                    WHEN 'resolved' THEN 3 
                END
            ")->latest()->paginate(10);
            
            $stats = [
                'pending' => \App\Models\HubungiKami::where('status', 'pending')->count(),
                'processed' => \App\Models\HubungiKami::where('status', 'processed')->count(),
                'resolved' => \App\Models\HubungiKami::where('status', 'resolved')->count(),
            ];
            
            return view('admin.bantuan.index', compact('messages', 'stats'));
        })->name('bantuan.index');
        
        Route::post('/bantuan/{id}/update-status', function ($id) {
            $message = \App\Models\HubungiKami::findOrFail($id);
            
            $newStatus = match($message->status) {
                'pending' => 'processed',
                'processed' => 'resolved',
                default => $message->status,
            };
            
            $message->update(['status' => $newStatus]);
            
            return redirect()->route('admin.bantuan.index')->with('success', 'Status berhasil diupdate!');
        })->name('bantuan.update-status');

        Route::delete('/bantuan/{id}', function ($id) {
            \App\Models\HubungiKami::findOrFail($id)->delete();
            return back()->with('success', 'Pesan berhasil dihapus!');
        })->name('bantuan.destroy');

        Route::delete('/bantuan/{id}', function ($id) {
    \App\Models\HubungiKami::findOrFail($id)->delete();

    return redirect()
        ->route('admin.bantuan.index')
        ->with('success', 'Pesan berhasil dihapus');
})->name('bantuan.destroy');
        
        // Edit Requests Management
        Route::get('/edit-requests', [\App\Http\Controllers\AdminEditRequestController::class, 'index'])
            ->name('edit-requests.index');
        Route::get('/edit-requests/{id}', [\App\Http\Controllers\AdminEditRequestController::class, 'show'])
            ->name('edit-requests.show');
        Route::post('/edit-requests/{id}/approve', [\App\Http\Controllers\AdminEditRequestController::class, 'approve'])
            ->name('edit-requests.approve');
        Route::post('/edit-requests/{id}/reject', [\App\Http\Controllers\AdminEditRequestController::class, 'reject'])
            ->name('edit-requests.reject');     
        Route::delete('/edit-requests/{id}', function ($id) {
    \App\Models\EditRequest::findOrFail($id)->delete();
    return back()->with('success', 'Edit request berhasil dihapus!');
})->name('edit-requests.destroy');


       // Admin Profile (PAKAI CONTROLLER)
Route::put('/profile', [AdminProfileController::class, 'updateProfile'])
    ->name('profile.update');
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
        // Dashboard
        Route::get('/dashboard', [PemilikDashboardController::class, 'dashboard'])
            ->name('dashboard');
        
        // Override with new dashboard controller
        Route::get('/dashboard', [\App\Http\Controllers\PemilikDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Destinasi Management
        Route::resource('destinasi', \App\Http\Controllers\PemilikDestinasiController::class);
        
        // Paket Promosi
Route::get('/paket', [\App\Http\Controllers\PaketController::class, 'index'])
    ->name('paket.index');
Route::post('/paket/{id}/checkout', [\App\Http\Controllers\PaketController::class, 'checkout'])
    ->name('paket.checkout');
Route::get('/paket/callback', [\App\Http\Controllers\PaketController::class, 'callback'])
    ->name('paket.callback');
Route::post('/transaksi/{id}/confirm', [\App\Http\Controllers\PaketController::class, 'confirmPayment'])
    ->name('transaksi.confirm');

    
        
        // Edit Requests (untuk Basic users)
        Route::get('/edit-request', [\App\Http\Controllers\EditRequestController::class, 'index'])
            ->name('edit-request.index');
        Route::get('/edit-request/create', [\App\Http\Controllers\EditRequestController::class, 'create'])
            ->name('edit-request.create');
        Route::post('/edit-request', [\App\Http\Controllers\EditRequestController::class, 'store'])
            ->name('edit-request.store');
        
        // Profile
        Route::get('/profile', function() {
            return view('pemilik.profile');
        })->name('profile');
        
        Route::put('/profile', function(\Illuminate\Http\Request $request) {
            $user = auth()->user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'no_telepon' => 'nullable|string',
                'current_password' => 'nullable|required_with:password',
                'password' => 'nullable|min:8|confirmed',
            ]);
            
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->no_telepon = $validated['no_telepon'] ?? $user->no_telepon;
            
            if ($request->filled('password')) {
                if (!\Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Password lama salah']);
                }
                $user->password = bcrypt($request->password);
            }
            
            $user->save();
            
            return back()->with('success', 'Profil berhasil diupdate!');
        })->name('profile.update');
    });

//Pemilik Promosi iklan
Route::middleware(['auth', /* middleware role pemilik */])->prefix('pemilik')->name('pemilik.')->group(function () {

    // Banner Promosi Premium
    Route::get('/promosi',               [PemilikPromosiController::class, 'index'])->name('promosi.index');
    Route::post('/promosi',              [PemilikPromosiController::class, 'store'])->name('promosi.store');
    Route::get('/promosi/{promosi}/edit',[PemilikPromosiController::class, 'edit'])->name('promosi.edit');
    Route::put('/promosi/{promosi}',     [PemilikPromosiController::class, 'update'])->name('promosi.update');
    Route::delete('/promosi/{promosi}',  [PemilikPromosiController::class, 'destroy'])->name('promosi.destroy');

});
// pemilik ulasan
Route::get('/ulasan', [UlasanController::class, 'indexPemilik'])->name('pemilik.ulasan.index');

/*
|--------------------------------------------------------------------------
| WISATAWAN PRIVATE ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:wisatawan'])
    ->prefix('wisatawan')
    ->name('wisatawan.')
    ->group(function () {
        Route::get('/profil', [WisatawanController::class, 'profile'])
            ->name('profile');
    });

/*
|--------------------------------------------------------------------------
| Wisatawan Password Reset (Additional Routes - Alternative paths)
|--------------------------------------------------------------------------
*/
Route::get('/wisatawan/forgot-password', function () {
    return view('auth.forgot-password');
})
->middleware('guest')
->name('wisatawan.password.request.alt');

Route::post('/wisatawan/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('wisatawan.password.email.alt');

Route::get('/wisatawan/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})
->middleware('guest')
->name('wisatawan.password.reset.alt');

Route::post('/wisatawan/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('wisatawan.password.update.alt');

require __DIR__.'/auth.php';