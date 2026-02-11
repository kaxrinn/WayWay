<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    use HasFactory;

    protected $table = 'promosi'; // FIX: Explicitly set table name (no 's')

    protected $fillable = [
        'user_id',
        'destinasi_id',
        'paket_id',
        'judul_banner',
        'deskripsi_banner',
        'banner_promosi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get user dari promosi (untuk user subscription)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get destinasi dari promosi
     */
    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'destinasi_id');
    }

    /**
     * Get paket promosi
     */
    public function paket()
    {
        return $this->belongsTo(PaketPromosi::class, 'paket_id');
    }

    /**
     * Get transaksi dari promosi ini
     */
    public function transaksiPromosi()
    {
        return $this->hasMany(TransaksiPromosi::class, 'promosi_id');
    }
    public function scopeAktif($query)
    {
        return $query->where('status', 'active')
                     ->whereDate('tanggal_mulai', '<=', now())
                     ->whereDate('tanggal_selesai', '>=', now());
    }

}