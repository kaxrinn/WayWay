<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;

class DestinasiController extends Controller
{
    public function byKategori($kategoriId)
    {
        return response()->json(
            Destinasi::where('kategori_id', $kategoriId)->get()
        );
    }

    public function getByKategori($kategoriId)
    {
        return $this->byKategori($kategoriId);
    }
}
