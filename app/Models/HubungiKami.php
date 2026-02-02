<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubungiKami extends Model
{
    use HasFactory;
    protected $table = 'hubungi_kami';
    protected $fillable = [
        'nama',
        'email',
        'subjek',
        'pesan',
        'status',
        'user_id'
    ];

    /**
     * Get user yang mengirim pesan (jika ada)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}