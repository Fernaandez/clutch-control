<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        $userName = $notifiable->name;

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('✅ Verifica el teu compte - Clutch Control')
            ->view('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'userName' => $userName,
            ]);
    }
}
