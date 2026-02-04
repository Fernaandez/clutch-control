<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Motorcycle;
// use App\Models\RouteCategory; // Encara no existeix
// use App\Models\Route;         // Encara no existeix
// use App\Models\Event;         // Encara no existeix
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREAR CATEGORIES (COMENTAT FINS QUE TINGUEM LA TAULA)
        /*
        $catEnduro = RouteCategory::create(['name' => 'Enduro', 'slug' => 'enduro']);
        $catTrail = RouteCategory::create(['name' => 'Trail', 'slug' => 'trail']);
        $catRoad = RouteCategory::create(['name' => 'Carretera', 'slug' => 'road']);
        $categories = [$catEnduro, $catTrail, $catRoad];
        */

        // 2. CREAR EL TEU USUARI ADMIN
        $me = User::factory()->create([
            'name' => 'Jan Fernandez',
            'email' => 'admin@admin.com',
            'phone_number' => '666666666', // Ara sí que funciona!
            'role' => 'admin',             // Ara sí que funciona!
            'password' => Hash::make('password'),
        ]);
        
        // Et regalo 2 motos
        Motorcycle::factory()->count(2)->for($me)->create();

        // 3. CREAR 10 USUARIS NORMALS
        $users = User::factory(10)->create();

        foreach ($users as $user) {
            // A cada usuari li posem 1 o 2 motos
            $motos = Motorcycle::factory(rand(1, 2))->for($user)->create();

            /* ---------------------------------------------------------
               BLOQUEJAT FINS QUE PROGRAMEM EL MÒDUL DE RUTES I EVENTS
               (Així no et donarà errors ara)
               ---------------------------------------------------------
            
            // A cada usuari li creem 3 rutes
            foreach ($motos as $moto) {
                Route::factory(rand(1, 3))->create([
                    'user_id' => $user->id,
                    'motorcycle_id' => $moto->id,
                    'category_id' => $categories[rand(0, 2)]->id,
                ]);
            }

            // Alguns usuaris creen un event
            if (rand(0, 1)) {
                Event::factory()->create(['user_id' => $user->id]);
            }
            */
        }
    }
}