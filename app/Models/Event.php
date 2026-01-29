<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'title', 
        'description', 
        'start_time',  
        'end_time',    
        'location',   
        'is_public',  
        'latitude', 
        'longitude', 
        'max_participants', 
        'photo'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime', 
        'is_public' => 'boolean',
    ];

    public function organizer() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacio amb rutes per saber quines rutes es faran
    public function routes() {
        return $this->belongsToMany(Route::class, 'event_routes')
                    ->withPivot('day_order') // per saber l'ordre
                    ->orderByPivot('day_order');
    }

    // Relacio amb participants per saber qui va
    public function participants() {
        return $this->belongsToMany(User::class, 'event_participants')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
