<?php

namespace Database\Seeders;

use App\Models\MaintenanceLog;
use App\Models\MaintenanceTask;
use App\Models\Motorcycle;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Contingut realista per treballar el disseny en local.
 * Mai s'executa a producció: cal cridar-lo explícitament.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@clutch.local')->first();

        if (!$user) {
            $this->command?->warn('DevUserSeeder primer: falta test@clutch.local');

            return;
        }

        $moto = $user->motorcycles()->first();

        if (!$moto) {
            $moto = Motorcycle::create([
                'user_id'     => $user->id,
                'brand'       => 'Triumph',
                'model'       => 'Street Triple 765',
                'year'        => 2021,
                'current_km'  => 37000,
                'cc'          => 765,
                'power_cv'    => 123,
                'type'        => 'naked',
            ]);
        }

        $moto->update([
            'itv_expires_at'       => now()->addDays(18),
            'insurance_expires_at' => now()->addMonths(7),
            'insurance_company'    => 'Mutua Motera',
        ]);

        $km = (float) $moto->current_km;

        $tasks = [
            ['title' => "Canvi d'oli i filtre", 'frequency_km' => 10000, 'last_km_done' => $km - 9600],
            ['title' => 'Pastilles de fre davanteres', 'frequency_km' => 15000, 'last_km_done' => $km - 15400],
            ['title' => 'Cadena i corones', 'frequency_km' => 20000, 'last_km_done' => $km - 4200],
            ['title' => 'Filtre d\'aire', 'frequency_km' => 18000, 'last_km_done' => $km - 2000],
        ];

        foreach ($tasks as $task) {
            MaintenanceTask::updateOrCreate(
                ['motorcycle_id' => $moto->id, 'title' => $task['title'], 'type' => 'maintenance'],
                [
                    'frequency_km'   => $task['frequency_km'],
                    'last_km_done'   => $task['last_km_done'],
                    'last_date_done' => now()->subMonths(5),
                    'is_recurring'   => true,
                ]
            );
        }

        foreach ([['Fuita de líquid refrigerant', 'Taller Motos Vic'], ['Retrovisor esquerre trencat', 'Casa']] as [$title, $where]) {
            MaintenanceTask::updateOrCreate(
                ['motorcycle_id' => $moto->id, 'title' => $title, 'type' => 'repair'],
                ['location' => $where, 'is_recurring' => false, 'frequency_km' => null, 'last_km_done' => 0]
            );
        }

        foreach ([['Escapament Arrow', 'Motos Bosch'], ['Maneta regulable', 'Casa'], ['Quickshifter', 'Taller Motos Vic']] as [$title, $where]) {
            MaintenanceTask::updateOrCreate(
                ['motorcycle_id' => $moto->id, 'title' => $title, 'type' => 'upgrade'],
                ['location' => $where, 'is_recurring' => false, 'frequency_km' => null, 'last_km_done' => 0]
            );
        }

        $logs = [
            ['maintenance', "Canvi d'oli i filtre", 88.50, 27400, 14],
            ['maintenance', 'Pastilles de fre davanteres', 62.00, 21600, 20],
            ['repair', 'Fuita de líquid refrigerant', 145.90, 31200, 8],
            ['upgrade', 'Escapament Arrow', 620.00, 29000, 11],
            ['upgrade', 'Quickshifter', 310.00, 33500, 5],
            ['maintenance', 'Cadena i corones', 210.75, 32800, 6],
        ];

        foreach ($logs as [$type, $title, $cost, $atKm, $monthsAgo]) {
            MaintenanceLog::updateOrCreate(
                ['motorcycle_id' => $moto->id, 'task_title' => $title, 'km_at_moment' => $atKm],
                [
                    'type'     => $type,
                    'location' => 'Taller Motos Vic',
                    'date'     => now()->subMonths($monthsAgo),
                    'cost'     => $cost,
                ]
            );
        }

        $routes = [
            ['Coll de la Bonaigua', 'Vielha', 142, 'hard', true],
            ['Volta al Montseny', 'Sant Celoni', 96, 'medium', true],
            ['Costa Brava fins Begur', 'Palamós', 78, 'easy', true],
            ['Escapada al Port del Cantó', 'La Seu d\'Urgell', 118, 'hard', false],
        ];

        foreach ($routes as [$title, $city, $distance, $difficulty, $public]) {
            Route::updateOrCreate(
                ['user_id' => $user->id, 'title' => $title],
                [
                    'motorcycle_id'       => $moto->id,
                    'description'         => 'Ruta de demo per treballar el disseny en local.',
                    'planned_distance_km' => $distance,
                    'distance_km'         => $distance,
                    'duration_seconds'    => (int) ($distance / 55 * 3600),
                    'location_city'       => $city,
                    'difficulty'          => $difficulty,
                    'is_public'           => $public,
                    'is_recorded'         => false,
                    'starting_lat'        => 42.0 + mt_rand(-40, 40) / 100,
                    'starting_lng'        => 1.8 + mt_rand(-40, 40) / 100,
                    'geo_json'            => $this->fakeGeoJson(),
                ]
            );
        }

        $lastRoute = Route::where('user_id', $user->id)->first();

        foreach ([[142, 3], [78, 12], [96, 26]] as $i => [$distance, $daysAgo]) {
            Trip::updateOrCreate(
                ['user_id' => $user->id, 'motorcycle_id' => $moto->id, 'started_at' => now()->subDays($daysAgo)->setTime(9, 30)],
                [
                    'route_id'         => $i === 0 ? $lastRoute?->id : null,
                    'distance_km'      => $distance,
                    'duration_seconds' => (int) ($distance / 52 * 3600),
                    'starting_lat'     => 41.98,
                    'starting_lng'     => 2.25,
                    'waypoints'        => [[41.98, 2.25], [42.05, 2.31], [42.12, 2.4]],
                    'manual_entry'     => false,
                ]
            );
        }
    }

    private function fakeGeoJson(): array
    {
        $lat = 42.0 + mt_rand(-30, 30) / 100;
        $lng = 1.9 + mt_rand(-30, 30) / 100;

        $coords = [];
        $twist = mt_rand(2, 5);
        for ($i = 0; $i < 90; $i++) {
            $t = $i / 89;
            $coords[] = [
                round($lng + $t * 0.35 + sin($t * $twist * M_PI) * 0.09, 6),
                round($lat + $t * 0.18 + cos($t * ($twist + 1) * M_PI) * 0.11, 6),
            ];
        }

        return $coords;
    }
}
