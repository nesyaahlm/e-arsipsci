<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordSementaraMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $passwordSementara)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Akun E-Arsip PT Sucofindo Anda');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-sementara');
    }
}