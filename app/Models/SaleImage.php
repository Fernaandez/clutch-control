<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleImage extends Model
{
    protected $fillable = ['sale_listing_id', 'image_path'];

    public function saleListing()
    {
        return $this->belongsTo(SaleListing::class);
    }
}
