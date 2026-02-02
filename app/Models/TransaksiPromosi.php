<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPromosi extends Model
{
    use HasFactory;
    protected $table = 'transaksi_promosi';
    protected $fillable = [
        'promosi_id',
        'user_id',
        'paket_id',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'bukti_pembayaran',
        'tanggal_transaksi'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    /**
     * Get promosi dari transaksi
     */
    public function promosi()
    {
        return $this->belongsTo(Promosi::class, 'promosi_id');
    }

    /**
     * Get user yang melakukan transaksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get paket promosi
     */
    public function paket()
    {
        return $this->belongsTo(PaketPromosi::class, 'paket_id');
    }
}