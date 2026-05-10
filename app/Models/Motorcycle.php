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
        'extras'
    ];

    protected $appends = ['has_pending_maintenance'];

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
