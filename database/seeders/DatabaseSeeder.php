<?php

namespace Database\Seeders;

// database/seeders/DatabaseSeeder.php

use App\Models\User;
use App\Models\RouteCategory;
use App\Models\Motorcycle;
use App\Models\Route;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREAR CATEGORIES REALS
        $catEnduro = RouteCategory::create(['name' => 'Enduro', 'slug' => 'enduro']);
        $catTrail = RouteCategory::create(['name' => 'Trail', 'slug' => 'trail']);
        $catRoad = RouteCategory::create(['name' => 'Carretera', 'slug' => 'road']);
        $categories = [$catEnduro, $catTrail, $catRoad];

        // 2. CREAR EL TEU USUARI (Per fer login després)
        $me = User::factory()->create([
            'name' => 'Jan Fernandez',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'), // La contrasenya serà "password"
            'role' => 'admin',
        ]);
        
        // Et regalo una moto
        Motorcycle::factory()->count(2)->for($me)->create();

        // 3. CREAR 10 USUARIS NORMALS
        $users = User::factory(10)->create();

        foreach ($users as $user) {
            // A cada usuari li posem 1 o 2 motos
            $motos = Motorcycle::factory(rand(1, 2))->for($user)->create();

            // A cada usuari li creem 3 rutes
            foreach ($motos as $moto) {
                Route::factory(rand(1, 3))->create([
                    'user_id' => $user->id,
                    'motorcycle_id' => $moto->id,
                    'category_id' => $categories[rand(0, 2)]->id, // Categoria random
                ]);
            }

            // Alguns usuaris creen un event
            if (rand(0, 1)) {
                Event::factory()->create(['user_id' => $user->id]);
            }
        }
    }
}
