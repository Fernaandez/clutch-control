<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Compte de proves per a desenvolupament local.
 * No s'executa mai a producció: cal cridar-lo explícitament.
 */
class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@clutch.local'],
            [
                'name' => 'Test Rider',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
