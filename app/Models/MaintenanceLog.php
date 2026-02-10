<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'motorcycle_id', 
        'maintenance_task_id', 
        'type',
        'task_title',
        'location', 
        'date', 
        'km_at_moment', 
        'cost', 
        'invoice_photo'
    ];

    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }

    public function task() {
        return $this->belongsTo(MaintenanceTask::class, 'maintenance_task_id');
    }
}
