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
        'is_sold'
    ];

    public function motorcycle() {
        return $this->belongsTo(Motorcycle::class);
    }

    public function images() {
        return $this->hasMany(SaleImage::class);
    }
}

