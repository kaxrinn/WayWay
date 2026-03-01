<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPromosi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = TransaksiPromosi::with(['user','paket'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => TransaksiPromosi::count(),
            'pending' => TransaksiPromosi::where('status_pembayaran','pending')->sum('total_harga'),
            'success' => TransaksiPromosi::where('status_pembayaran','success')->sum('total_harga'),
        ];

        return view('admin.transaksi.index', compact('transaksis','stats'));
    }

    public function destroy($id)
    {
        TransaksiPromosi::findOrFail($id)->delete();
        return back()->with('success','Transaksi has been deleted successfully.');
    }
}
