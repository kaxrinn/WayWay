<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DestinasiApiController;
use App\Http\Controllers\PaketController;

Route::get('/destinasi/kategori/{kategoriId}', 
    [DestinasiApiController::class, 'byKategori']
);

// Midtrans notification webhook
Route::post('/midtrans/notification', [PaketController::class, 'notification']);