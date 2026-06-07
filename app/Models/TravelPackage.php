<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_agent_id',
        'nama_paket',
        'deskripsi',
        'thumbnail',
        'harga_per_orang',
        'durasi_hari',
        'tanggal_keberangkatan',
        'destinasi',
        'fasilitas_include',
        'fasilitas_exclude',
        'itinerary',
        'min_peserta',
        'max_peserta',
        'meeting_point',
        'whatsapp',
        'email',
        'instagram',
        'website',
        'status',
    ];

    protected $casts = [
        'destinasi' => 'array',
        'fasilitas_include' => 'array',
        'fasilitas_exclude' => 'array',
        'itinerary' => 'array',
        'tanggal_keberangkatan' => 'date',
    ];

    public function travelAgent()
    {
        return $this->belongsTo(User::class, 'travel_agent_id');
    }

    public function getActiveSubscription()
    {
        return TravelAgentSubscription::where('travel_agent_id', $this->travel_agent_id)
            ->whereHas('package', function($q) {
                // Get highest tier active subscription
            })
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->latest()
            ->first();
    }
}