<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteReview extends Model
{
    use HasFactory;

    protected $fillable = ['route_id', 'user_id', 'rating', 'comment'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function route() {
        return $this->belongsTo(Route::class);
    }
}
