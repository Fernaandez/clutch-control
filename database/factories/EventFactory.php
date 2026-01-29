<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Calculem una data d'inici
        $start = fake()->dateTimeBetween('now', '+2 months');
        
        // Calculem una data de final (unes hores després de l'inici)
        $end = (clone $start)->modify('+' . rand(2, 6) . ' hours');

        return [
            'title' => 'Sortida a ' . fake()->city(),
            'description' => fake()->paragraph(),
            
            // --- CANVIS AQUÍ ---
            'start_time' => $start,
            'end_time' => $end,
            'location' => 'Benzinera ' . fake()->company(), // Abans location_name
            'is_public' => fake()->boolean(80), // 80% probabilitat que sigui públic
            // -------------------

            'latitude' => 41.38,
            'longitude' => 2.16,
            'max_participants' => fake()->numberBetween(5, 20),
        ];
    }
}
