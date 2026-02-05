<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use App\Models\PaketPromosi;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'destinasi_id',
        'no_telepon',
        'current_paket_id',
        'paket_expired_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'paket_expired_at' => 'date',
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

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is pemilik wisata
     */
    public function isPemilikWisata()
    {
        return $this->role === 'pemilik_wisata';
    }

    /**
     * Check if user is wisatawan
     */
    public function isWisatawan()
    {
        return $this->role === 'wisatawan';
    }

    /**
     * Get destinasi yang dikelola pemilik wisata
     */


    /**
     * Get semua destinasi milik pemilik wisata
     */
    public function destinasi()
    {
        return $this->hasMany(Destinasi::class, 'user_id');
    }

    /**
     * Get paket yang sedang aktif
     */
    public function currentPaket()
    {
        return $this->belongsTo(PaketPromosi::class, 'current_paket_id');
    }

    /**
     * Get transaksi promosi dari user
     */
    public function transaksiPromosi()
    {
        return $this->hasMany(TransaksiPromosi::class, 'user_id');
    }

    /**
     * Get edit requests dari user
     */
    public function editRequests()
    {
        return $this->hasMany(EditRequest::class, 'user_id');
    }

    /**
     * Check if paket masih aktif
     */
    public function isPaketActive()
    {
        if (!$this->paket_expired_at) {
            return false;
        }
        return $this->paket_expired_at >= now()->toDateString();
    }

    /**
     * Get batasan berdasarkan paket
     */
  
    public function getPaketLimits()
{
    $paket = $this->activePaket();

    return [
        'max_destinasi' => $paket?->max_destinasi ?? 0,
        'max_foto' => $paket?->max_foto ?? 0,
        'max_video' => $paket?->max_video ?? 0,
        'can_edit_foto' => $paket?->can_edit_foto ?? false,
        'is_featured_allowed' => $paket?->is_featured_allowed ?? false,
    ];
}
    public function activePaket()
{
    if ($this->currentPaket) {
        return $this->currentPaket;
    }

    return PaketPromosi::where('nama_paket', 'Basic')->first();
}


}