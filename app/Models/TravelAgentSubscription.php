<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAgentSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_agent_id',
        'package_id',
        'snap_token',
        'payment_method',
        'status',
        'started_at',
        'expired_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function travelAgent()
    {
        return $this->belongsTo(User::class, 'travel_agent_id');
    }

    public function package()
    {
        return $this->belongsTo(TravelAgentSubscriptionPackage::class, 'package_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->expired_at === null || $this->expired_at > now());
    }
}