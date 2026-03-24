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
        'plate',
        'current_km', 
        'photo',
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
            if (!$task->frequency_km) return false;
            return ($this->current_km - $task->last_km_done) >= $task->frequency_km;
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
