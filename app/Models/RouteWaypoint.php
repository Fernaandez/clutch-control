<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteWaypoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'order',
    ];

    // Relació inversa: Un waypoint pertany a una ruta
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}