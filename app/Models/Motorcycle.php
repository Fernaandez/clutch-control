<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Motorcycle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'brand', 
        'model', 
        'year', 
        'current_km', 
        'photo',
        'plate',
        'cc',
        'power_cv',
        'license_type',
        'type',
        'extras',
        'insurance_company',
        'insurance_policy_number',
        'insurance_expires_at',
        'itv_expires_at',
        'itv_last_passed_at',
        'doc_alert_acknowledged_for',
    ];

    protected $casts = [
        'insurance_expires_at' => 'date',
        'itv_expires_at' => 'date',
        'itv_last_passed_at' => 'date',
        'doc_alert_acknowledged_for' => 'date',
    ];

    protected $appends = ['has_pending_maintenance', 'itv_status', 'show_documentation_alert'];

    public function getPendingMaintenanceTasksAttribute()
    {
        return $this->maintenanceTasks->filter(function ($task) {
            $freq = $task->frequency_km;
            if ($freq === null || (int) $freq <= 0) {
                return false;
            }
            $last = (int) ($task->last_km_done ?? 0);
            $km = (float) ($this->current_km ?? 0);

            return ($km - $last) >= (float) $freq;
        })->values();
    }

    public function getHasPendingMaintenanceAttribute()
    {
        return $this->pending_maintenance_tasks->count() > 0;
    }

    public function getItvStatusAttribute(): ?string
    {
        return $this->expiryStatus($this->itv_expires_at);
    }

    public function getShowDocumentationAlertAttribute(): bool
    {
        if (!in_array($this->itv_status, ['expiring_soon', 'expired'], true) || !$this->itv_expires_at) {
            return false;
        }

        return $this->doc_alert_acknowledged_for?->toDateString() !== $this->itv_expires_at->toDateString();
    }

    private function expiryStatus($date): ?string
    {
        if (!$date) {
            return null;
        }

        $today = now()->startOfDay();
        $expiry = $date->copy()->startOfDay();

        if ($expiry->lt($today)) {
            return 'expired';
        }

        if ($expiry->lte($today->copy()->addDays(30))) {
            return 'expiring_soon';
        }

        return 'ok';
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function saleListing() {
        return $this->hasOne(SaleListing::class);
    }

    public function maintenanceTasks() {
        return $this->hasMany(MaintenanceTask::class);
    }

    public function maintenanceLogs() {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function routes() {
        return $this->hasMany(Route::class);
    }
}
