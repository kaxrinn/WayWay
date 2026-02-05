<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use Illuminate\Http\Request;

class DestinasiApiController extends Controller
{
    public function byKategori($kategoriId)
    {
        $destinasi = Destinasi::where('kategori_id', $kategoriId)
            ->where('status', 'active')
            ->get();

        return response()->json($destinasi);
    }
}
