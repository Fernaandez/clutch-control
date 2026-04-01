<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type', 'name', 'motorcycle_id', 'event_id'];

    // Participants (tant per direct com per group)
    public function participants() {
        return $this->belongsToMany(User::class, 'conversation_user')->withTimestamps();
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }
    
    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
