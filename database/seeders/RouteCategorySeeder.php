<?php

namespace Database\Seeders;

use App\Models\RouteCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RouteCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('route_categories')) {
            return;
        }

        $categories = [
            ['name' => 'Carretera',       'slug' => 'carretera'],
            ['name' => 'Port de muntanya','slug' => 'port-muntanya'],
            ['name' => 'Costa',           'slug' => 'costa'],
            ['name' => 'Touring',         'slug' => 'touring'],
            ['name' => 'Aventura',        'slug' => 'aventura'],
            ['name' => 'Off-road',        'slug' => 'off-road'],
            ['name' => 'Enduro',          'slug' => 'enduro'],
            ['name' => 'Trail',           'slug' => 'trail'],
            ['name' => 'Circuit',         'slug' => 'circuit'],
            ['name' => 'Urbana',          'slug' => 'urbana'],
            ['name' => 'Mixta',           'slug' => 'mixta'],
            ['name' => 'Cap de setmana',  'slug' => 'cap-de-setmana'],
        ];

        foreach ($categories as $category) {
            RouteCategory::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
