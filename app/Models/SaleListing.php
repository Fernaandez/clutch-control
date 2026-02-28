<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleListing extends Model
{
    protected $fillable = [
        'motorcycle_id', 
        'title', 
        'description', 
        'price', 
        'location', 
        'is_active', 
        'is_sold',
        'views_count'
    ];

    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }

    public function images() {
        return $this->hasMany(SaleImage::class);
    }

    public function favoritedBy() {
        return $this->belongsToMany(User::class, 'sale_favorites')->withTimestamps();
    }

    public function isFavoritedBy(?User $user) {
        if (!$user) return false;
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }
}

