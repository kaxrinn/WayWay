<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $fillable = [
        'destinasi_id',
        'user_id',
        'rating',
        'komentar',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}