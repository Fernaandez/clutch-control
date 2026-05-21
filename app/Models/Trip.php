<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'motorcycle_id',
        'route_id',
        'distance_km',
        'duration_seconds',
        'starting_lat',
        'starting_lng',
        'waypoints',
        'started_at',
        'manual_entry',
        'notes',
    ];

    protected $casts = [
        'waypoints'    => 'array',
        'started_at'   => 'datetime',
        'manual_entry' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
