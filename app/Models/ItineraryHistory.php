<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: ItineraryHistory
 * Merepresentasikan satu sesi generate itinerary oleh seorang user.
 * Relasi: banyak history dimiliki satu user.
 */
class ItineraryHistory extends Model
{
    protected $table = 'itinerary_histories';

    protected $fillable = [
        'user_id',
        'params',
        'result',
        'tanggal_kunjungan',
        'companion',
        'origin_label',
        'stop_count',
        'total_distance',
        'total_minutes',
        'budget',
    ];

    // Cast otomatis: kolom JSON di-parse jadi array PHP
    protected $casts = [
        'params' => 'array',
        'result' => 'array',
    ];

    /**
     * Relasi: history ini milik satu user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: format total_minutes jadi "X jam Y mnt"
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours   = intdiv($this->total_minutes, 60);
        $minutes = $this->total_minutes % 60;

        if ($hours > 0 && $minutes > 0) return "{$hours}h {$minutes}m";
        if ($hours > 0)                 return "{$hours}h";
        return "{$minutes}m";
    }

    /**
     * Helper: ambil daftar nama destinasi dari result JSON
     */
    public function getDestinationNamesAttribute(): array
    {
        $schedule = $this->result['schedule'] ?? [];
        return collect($schedule)->pluck('stop.nama')->toArray();
    }
}
