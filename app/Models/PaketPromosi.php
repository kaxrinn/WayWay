<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketPromosi extends Model
{
    use HasFactory;
    protected $table = 'paket_promosi';
    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
        'durasi_hari',
        'fitur',
        'status'
    ];

    /**
     * Get promosi dengan paket ini
     */
    public function promosi()
    {
        return $this->hasMany(Promosi::class, 'paket_id');
    }

    /**
     * Get transaksi dengan paket ini
     */
    public function transaksiPromosi()
    {
        return $this->hasMany(TransaksiPromosi::class, 'paket_id');
    }
}