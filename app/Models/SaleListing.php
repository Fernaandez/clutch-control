<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleListing extends Model
{
    public const STATE_ACTIVE   = 'actiu';
    public const STATE_RESERVED = 'reservat';
    public const STATE_SOLD     = 'venuda';
    public const STATE_PAUSED   = 'pausat';

    /** Estats vàlids d'un anunci. */
    public const STATES = [
        self::STATE_ACTIVE,
        self::STATE_RESERVED,
        self::STATE_SOLD,
        self::STATE_PAUSED,
    ];

    /** Estats que es mostren al mercat públic. */
    public const PUBLIC_STATES = [
        self::STATE_ACTIVE,
        self::STATE_RESERVED,
    ];

    protected $fillable = [
        'motorcycle_id', 
        'title', 
        'description', 
        'price', 
        'location', 
        'state',
        'views_count',
        'show_history'
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

    public function reports() {
        return $this->morphMany(Report::class, 'reportable');
    }
}

