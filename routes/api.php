<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DestinasiApiController;

Route::get('/destinasi/kategori/{kategoriId}', 
    [DestinasiApiController::class, 'byKategori']
);
