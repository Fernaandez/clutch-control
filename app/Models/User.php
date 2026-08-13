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

    // 'role' NO és aquí a propòsit: és un camp de privilegis i no s'ha de poder
    // assignar mai en massa des d'un formulari. S'assigna explícitament.
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_motorcycle_id', 
        'google_id',
        'avatar',
        'fcm_token',
    ];

    // L'usuari sencer es comparteix amb el frontend a cada petició Inertia, per
    // tant el token de push i l'id de Google no han de sortir mai del servidor.
    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
        'google_id',
    ];

    // El frontend ha de saber si el compte té contrasenya (per decidir si pot
    // demanar-la o si cal confirmar amb el correu), però no l'id de Google.
    // Atenció: "té contrasenya" i "és de Google" no són oposats. Qui es va
    // registrar amb correu i després va entrar amb Google té les dues coses.
    protected $appends = [
        'has_password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Un compte creat només amb Google no té contrasenya per confirmar. */
    public function hasPassword(): bool
    {
        return ! empty($this->getAttributes()['password'] ?? null);
    }

    public function getHasPasswordAttribute(): bool
    {
        return $this->hasPassword();
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

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function submittedReports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
}