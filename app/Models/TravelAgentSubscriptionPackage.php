<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAgentSubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
        'max_packages',
        'durasi_bulan',
        'fitur',
        'status',
    ];

    protected $casts = [
        'fitur' => 'array',
    ];

    public function subscriptions()
    {
        return $this->hasMany(TravelAgentSubscription::class, 'package_id');
    }
}