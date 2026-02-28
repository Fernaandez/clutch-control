<?php

namespace Database\Seeders;

use App\Models\RouteCategory;
use Illuminate\Database\Seeder;

class RouteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Asfalt / Carretera', 'slug' => 'asfalt'],
            ['name' => 'Corbes / Ratera', 'slug' => 'corbes'],
            ['name' => 'Enduro', 'slug' => 'enduro'],
            ['name' => 'Trail', 'slug' => 'trail'],
            ['name' => 'Motocròs', 'slug' => 'motocros'],
            ['name' => 'Trial', 'slug' => 'trial'],
            ['name' => 'Ruta Llarga / Turística', 'slug' => 'ruta-llarga'],
            ['name' => 'Esportiva / Circuit', 'slug' => 'circuit'],
            ['name' => 'Urbana', 'slug' => 'urbana'],
            ['name' => 'Mixta (On/Off)', 'slug' => 'mixta'],
        ];

        foreach ($categories as $category) {
            RouteCategory::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
