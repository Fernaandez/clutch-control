<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // <-- 1. DESCOMENTAT
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;

// 2. AFEGIM "implements MustVerifyEmail" AQUÍ SOTA
class User extends Authenticatable implements MustVerifyEmail 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',               
        'last_motorcycle_id', 
        'google_id',
        'avatar',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELACIONS ---
    public function motorcycles()
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    // --- EMAIL VERIFICACIÓ PERSONALITZADA ---
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function favoriteSales()
    {
        return $this->belongsToMany(SaleListing::class, 'sale_favorites')->withTimestamps();
    }
}