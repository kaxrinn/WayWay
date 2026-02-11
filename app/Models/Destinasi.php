<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ulasan;
class Destinasi extends Model
{
    use HasFactory;
    
    protected $table = 'destinasi';

    protected $fillable = [
        'nama_destinasi',
        'latitude',
        'longitude',
        'deskripsi',
        'harga',
        'foto',
        'video',
        'kategori_id',
        'status',
        'is_featured',
        'user_id'
    ];

    /**
     * Casting foto dan video jadi array otomatis
     */
    protected $casts = [
        'foto' => 'array',
        'video' => 'array',
        'is_featured' => 'boolean',
    ];

    /**
     * Get kategori dari destinasi ini
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Get pemilik wisata yang mengelola destinasi ini
     */
    public function pemilikWisata()
    {
        return $this->hasOne(User::class, 'destinasi_id')->where('role', 'pemilik_wisata');
    }

    /**
     * Get pemilik dari destinasi (via user_id)
     */
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get promosi untuk destinasi ini
     */
    public function promosi()
    {
        return $this->hasMany(Promosi::class, 'destinasi_id');
    }

    /**
     * Get edit requests untuk destinasi ini
     */
    public function editRequests()
    {
        return $this->hasMany(EditRequest::class, 'destinasi_id');
    }
//ulasan
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class);
    }

    /* AVG rating otomatis */
    public function getAvgRatingAttribute()
    {
        return $this->ulasan()->avg('rating');
    }

public function difavoritkanOleh()
{
    return $this->belongsToMany(
        User::class,
        'favorit',
        'destinasi_id',
        'user_id'
    )->withTimestamps();
}
}

