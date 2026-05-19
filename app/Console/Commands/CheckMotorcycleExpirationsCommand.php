<?php

namespace App\Console\Commands;

use App\Jobs\SendExpiryReminderJob;
use App\Models\Motorcycle;
use Illuminate\Console\Command;

class CheckMotorcycleExpirationsCommand extends Command
{
    protected $signature = 'motorcycles:check-expirations';

    protected $description = 'Envia recordatoris de caducitat d\'ITV (30, 7 i 0 dies)';

    private const REMINDER_DAYS = [30, 7, 0];

    public function handle(): int
    {
        $sent = 0;

        foreach (self::REMINDER_DAYS as $days) {
            $targetDate = now()->addDays($days)->toDateString();

            $motorcycles = Motorcycle::with('user')
                ->whereDate('itv_expires_at', $targetDate)
                ->get();

            foreach ($motorcycles as $motorcycle) {
                if (!$motorcycle->user) {
                    continue;
                }

                SendExpiryReminderJob::dispatch($motorcycle->user, $motorcycle, 'itv', $days);
                $sent++;
            }
        }

        $this->info("Recordatoris enviats: {$sent}");

        return self::SUCCESS;
    }
}
