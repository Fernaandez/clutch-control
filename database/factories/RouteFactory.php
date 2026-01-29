<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Un quadrat simple al voltant de BCN per fer proves
        $dummyGeoJson = json_encode([
            "type" => "LineString",
            "coordinates" => [
                [2.16, 41.38], [2.17, 41.39], [2.18, 41.38]
            ]
        ]);

        return [
            'title' => 'Ruta ' . fake()->city(),
            'description' => fake()->sentence(),
            'distance_km' => fake()->randomFloat(2, 20, 150),
            'estimated_time' => fake()->time(),
            'geo_json' => $dummyGeoJson,
            'starting_lat' => 41.38 + (fake()->randomFloat(4, -0.1, 0.1)),
            'starting_lng' => 2.16 + (fake()->randomFloat(4, -0.1, 0.1)),
            'location_city' => fake()->city(),
            'is_public' => fake()->boolean(80), // 80% públiques
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'likes_count' => fake()->numberBetween(0, 50),
        ];
    }
}
