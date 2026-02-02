<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'kategori_id',
        'status'
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
