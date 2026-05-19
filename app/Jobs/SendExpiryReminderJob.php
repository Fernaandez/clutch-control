<?php

namespace App\Jobs;

use App\Models\Motorcycle;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpiryReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public Motorcycle $motorcycle,
        public string $docType,
        public int $daysUntil,
    ) {}

    public function handle(): void
    {
        $motoName = trim("{$this->motorcycle->brand} {$this->motorcycle->model}");
        $docLabel = $this->docType === 'insurance' ? 'assegurança' : 'ITV';

        if ($this->daysUntil === 0) {
            $title = "⚠️ {$docLabel} caducada avui";
            $body = "La {$docLabel} de la teva {$motoName} caduca avui. Renova-la el més aviat possible.";
        } elseif ($this->daysUntil === 7) {
            $title = "Recordatori: {$docLabel} en 7 dies";
            $body = "La {$docLabel} de la teva {$motoName} caduca d'aquí a 7 dies.";
        } else {
            $title = "Recordatori: {$docLabel} propera a caducar";
            $body = "La {$docLabel} de la teva {$motoName} caduca d'aquí a {$this->daysUntil} dies.";
        }

        if ($this->user->fcm_token) {
            try {
                $messaging = app('firebase.messaging');
                $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
                $cloudMessage = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $this->user->fcm_token)
                    ->withNotification($notification);
                $messaging->send($cloudMessage);
            } catch (\Exception $e) {
                Log::error('FCM expiry reminder failed: ' . $e->getMessage());
            }
        }

        if ($this->user->email) {
            try {
                Mail::raw($body, function ($message) use ($title) {
                    $message->to($this->user->email)
                        ->subject("[Clutch Control] {$title}");
                });
            } catch (\Exception $e) {
                Log::error('Email expiry reminder failed: ' . $e->getMessage());
            }
        }
    }
}
