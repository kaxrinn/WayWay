<?php
// File: app/Notifications/ResetPasswordNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\ResetPasswordMail;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Kirim email menggunakan Mailable class
     */
    public function toMail($notifiable)
    {
        $url = url(route('wisatawan.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        // Pakai Mailable class yang sudah kita buat
        return new ResetPasswordMail(
            $url,
            $notifiable->name,
            $notifiable->email
        );
    }
}