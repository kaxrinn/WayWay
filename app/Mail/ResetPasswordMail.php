<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $namaPengguna;
    public $emailPengguna;

    /**
     * Create a new message instance.
     */
    public function __construct($url, $namaPengguna, $emailPengguna)
    {
        $this->url = $url;
        $this->namaPengguna = $namaPengguna;
        $this->emailPengguna = $emailPengguna;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // DEBUG: Log URL lengkap
        \Log::info('Email reset URL', [
            'url' => $this->url,
            'contains_email' => str_contains($this->url, 'email='),
        ]);

        return $this->to($this->emailPengguna)
                    ->subject('Atur Ulang Kata Sandi - Guide Me')
                    ->view('emails.reset-password')
                    ->with([
                        'url' => $this->url,
                        'namaPengguna' => $this->namaPengguna,
                        'emailPengguna' => $this->emailPengguna,
                    ]);
    }
}