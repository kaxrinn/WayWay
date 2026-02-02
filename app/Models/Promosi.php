<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    use HasFactory;
    protected $table = 'promosi';
    protected $fillable = [
        'destinasi_id',
        'paket_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

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
}