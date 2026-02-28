<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reimposta la tua password - Gestionale CV')
            ->greeting('Ciao ' . ($notifiable->name ?? '') . ',')
            ->line('Hai richiesto il reset della password del tuo account.')
            ->line('Clicca il pulsante qui sotto per reimpostare la tua password:')
            ->action('Reimposta Password', $url)
            ->line('Questo link scadrà tra 60 minuti.')
            ->line('Se non hai richiesto il reset della password, ignora questa email.')
            ->salutation('Gestionale CV - Calabria Verde');
    }
}
