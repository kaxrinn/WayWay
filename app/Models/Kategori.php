<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    protected $fillable = [
        'nama_kategori',
        'deskripsi_kategori',
        'icon'
    ];

    /**
     * Get destinasi yang memiliki kategori ini
     */
    public function destinasi()
    {
        return $this->hasMany(Destinasi::class, 'kategori_id');
    }
}