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
        'status',
        'max_destinasi',
        'max_foto',
        'max_video',
        'priority_level',
        'can_edit_foto',
        'is_featured_allowed',
    ];

    protected $casts = [
        'can_edit_foto' => 'boolean',
        'is_featured_allowed' => 'boolean',
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

    /**
     * Get users yang menggunakan paket ini
     */
    public function users()
    {
        return $this->hasMany(User::class, 'current_paket_id');
    }

    /**
     * Check apakah unlimited destinasi
     */
    public function isUnlimitedDestinasi()
    {
        return is_null($this->max_destinasi);
    }

    /**
     * Check apakah unlimited foto
     */
    public function isUnlimitedFoto()
    {
        return is_null($this->max_foto);
    }

    /**
     * Check apakah unlimited video
     */
    public function isUnlimitedVideo()
    {
        return is_null($this->max_video);
    }
}