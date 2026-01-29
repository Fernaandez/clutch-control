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
        'photo'
    ];

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
