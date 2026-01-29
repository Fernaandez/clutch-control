<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'motorcycle_id', 
        'category_id', 
        'title', 
        'description', 
        'distance_km', 
        'estimated_time', 
        'geo_json', 
        'start_lat', 
        'start_lng', 
        'location_city', 
        'is_public', 
        'difficulty', 
        'photo'
    ];

    protected $casts = [
        'geo_json' => 'array', // converteix el JSON a Array sol
        'is_public' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }

    public function category() {
        return $this->belongsTo(RouteCategory::class);
    }

    public function waypoints() {
        return $this->hasMany(RouteWaypoint::class)->orderBy('order');
    }

    public function likes() {
        return $this->belongsToMany(User::class, 'route_likes');
    }

    public function events() {
        return $this->belongsToMany(Event::class, 'event_routes');
    }
}
