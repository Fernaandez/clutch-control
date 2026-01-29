<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    protected $fillable = [
        'motorcycle_id', 
        'title', 
        'description', 
        'frequency_km', 
        'last_km_done', 
        'last_date_done', 
        'is_recurring', 
        'affiliate_link'
    ];

    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }
}
