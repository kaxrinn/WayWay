<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Override method bawaan Laravel untuk custom email reset password
     * Method ini otomatis dipanggil saat user request reset password
     */
    public function sendPasswordResetNotification($token)
    {
        // Kirim notifikasi custom dengan passing token
        $this->notify(new ResetPasswordNotification($token));
    }

    // Helper method untuk cek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPemilikWisata()
    {
        return $this->role === 'pemilik_wisata';
    }

    public function isWisatawan()
    {
        return $this->role === 'wisatawan';
    }
}