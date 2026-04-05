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
    ];

    protected $casts = [
        'waypoints'  => 'array',
        'started_at' => 'datetime',
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
