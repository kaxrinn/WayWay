<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'destinasi_id',
    'request_type',
    'request_data',
    'keterangan',
    'status',
    'approved_by',
    'admin_notes'
];

protected $casts = [
    'request_data' => 'array',
    'approved_at' => 'datetime',
];


    /**
     * Get user yang request
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get destinasi yang di-request
     */
    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'destinasi_id');
    }

    /**
     * Get admin yang approve
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}