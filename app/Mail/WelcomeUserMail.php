<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $userSurname;
    public string $email;
    public string $password;
    public array $areaRoles;
    public string $adminName;
    public string $loginUrl;

    public function __construct(
        string $userName,
        string $userSurname,
        string $email,
        string $password,
        array $areaRoles,
        string $adminName,
        string $loginUrl
    ) {
        $this->userName = $userName;
        $this->userSurname = $userSurname;
        $this->email = $email;
        $this->password = $password;
        $this->areaRoles = $areaRoles;
        $this->adminName = $adminName;
        $this->loginUrl = $loginUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Benvenuto nel Gestionale Calabria Verde - Credenziali di Accesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-user',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
