<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabitualRoute extends Model
{
    protected $fillable = [
        'user_id',
        'route_id',
        'motorcycle_id',
        'label',
        'round_trip',
        'sort_order',
    ];

    protected $casts = [
        'round_trip' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function distanceKm(): ?float
    {
        $route = $this->relationLoaded('route') ? $this->route : $this->route()->first();
        if (! $route) {
            return null;
        }

        $base = (float) ($route->planned_distance_km ?? $route->distance_km ?? 0);
        if ($base <= 0) {
            return null;
        }

        return $this->round_trip ? $base * 2 : $base;
    }

    public function displayTitle(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return $this->route?->title ?? 'Ruta';
    }
}
