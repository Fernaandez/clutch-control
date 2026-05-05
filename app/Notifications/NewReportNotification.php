<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Report $report)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $report = $this->report->loadMissing(['reporter', 'reportable']);

        return (new MailMessage)
            ->subject('Nova denuncia a Clutch Control')
            ->greeting('Nova denuncia rebuda')
            ->line("Tipus: {$report->type_label}")
            ->line("Contingut: {$report->subject_label}")
            ->line("Motiu: {$report->reason}")
            ->line('Reporter: ' . ($report->reporter?->email ?? $report->contact_email ?? 'Anonim'))
            ->action('Revisar denuncia', route('admin.reports.show', $report))
            ->line('Revisa-la al panell admin abans de prendre cap accio.');
    }
}
