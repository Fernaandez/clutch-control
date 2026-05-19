<?php

namespace App\Console\Commands;

use App\Jobs\SendExpiryReminderJob;
use App\Models\Motorcycle;
use Illuminate\Console\Command;

class CheckMotorcycleExpirationsCommand extends Command
{
    protected $signature = 'motorcycles:check-expirations';

    protected $description = 'Envia recordatoris de caducitat d\'assegurança i ITV (30, 7 i 0 dies)';

    private const REMINDER_DAYS = [30, 7, 0];

    public function handle(): int
    {
        $sent = 0;

        foreach (self::REMINDER_DAYS as $days) {
            $targetDate = now()->addDays($days)->toDateString();

            $motorcycles = Motorcycle::with('user')
                ->where(function ($q) use ($targetDate) {
                    $q->whereDate('insurance_expires_at', $targetDate)
                        ->orWhereDate('itv_expires_at', $targetDate);
                })
                ->get();

            foreach ($motorcycles as $motorcycle) {
                if (!$motorcycle->user) {
                    continue;
                }

                if ($motorcycle->insurance_expires_at?->toDateString() === $targetDate) {
                    SendExpiryReminderJob::dispatch($motorcycle->user, $motorcycle, 'insurance', $days);
                    $sent++;
                }

                if ($motorcycle->itv_expires_at?->toDateString() === $targetDate) {
                    SendExpiryReminderJob::dispatch($motorcycle->user, $motorcycle, 'itv', $days);
                    $sent++;
                }
            }
        }

        $this->info("Recordatoris enviats: {$sent}");

        return self::SUCCESS;
    }
}
