<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Motorcycle;
use App\Models\Route;
use App\Models\RouteReview;
use App\Models\Event;
use App\Models\SaleListing;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MaintenanceTask;
use App\Models\MaintenanceLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════════
        // 1. USUARIS DEMO
        // ═══════════════════════════════════════════════
        $admin = User::firstOrCreate(
            ['email' => 'admin@clutchcontrol.com'],
            [
                'name' => 'Jan Fernandez',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $users = [];
        $userData = [
            ['name' => 'Marc Puig',      'email' => 'marc@demo.com'],
            ['name' => 'Laura Vidal',    'email' => 'laura@demo.com'],
            ['name' => 'Rafa Moreno',    'email' => 'rafa@demo.com'],
            ['name' => 'Sergi Blanco',   'email' => 'sergi@demo.com'],
            ['name' => 'Núria Soler',    'email' => 'nuria@demo.com'],
        ];
        foreach ($userData as $data) {
            $users[] = User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }

        // ═══════════════════════════════════════════════
        // 2. MOTOS
        // ═══════════════════════════════════════════════
        $motos = [
            ['user_id' => $admin->id,    'brand' => 'Yamaha',   'model' => 'MT-09',         'year' => 2022, 'current_km' => 18340, 'plate' => 'AB1234CD'],
            ['user_id' => $admin->id,    'brand' => 'Honda',    'model' => 'Africa Twin',   'year' => 2021, 'current_km' => 32100, 'plate' => 'XY9876ZW'],
            ['user_id' => $users[0]->id, 'brand' => 'Kawasaki', 'model' => 'Z900',          'year' => 2023, 'current_km' => 5200,  'plate' => 'CD5678EF'],
            ['user_id' => $users[1]->id, 'brand' => 'Ducati',   'model' => 'Monster 821',   'year' => 2020, 'current_km' => 22000, 'plate' => 'EF2345GH'],
            ['user_id' => $users[2]->id, 'brand' => 'BMW',      'model' => 'GS 1250',       'year' => 2022, 'current_km' => 41000, 'plate' => 'GH6789IJ'],
            ['user_id' => $users[3]->id, 'brand' => 'KTM',      'model' => '890 Duke R',    'year' => 2023, 'current_km' => 8700,  'plate' => 'IJ3456KL'],
            ['user_id' => $users[4]->id, 'brand' => 'Triumph',  'model' => 'Speed Triple',  'year' => 2021, 'current_km' => 15600, 'plate' => 'KL7890MN'],
        ];

        $motorcycles = [];
        foreach ($motos as $moto) {
            $motorcycles[] = Motorcycle::firstOrCreate(
                ['plate' => $moto['plate']],
                $moto
            );
        }

        // ═══════════════════════════════════════════════
        // 3. TASQUES DE MANTENIMENT (per la captura 1)
        // ═══════════════════════════════════════════════
        $moto1 = $motorcycles[0]; // Yamaha MT-09 de l'admin
        $maintenanceTasks = [
            ['motorcycle_id' => $moto1->id, 'title' => 'Canvi d\'oli', 'type' => 'oil', 'frequency_km' => 6000, 'last_km_done' => 14000],
            ['motorcycle_id' => $moto1->id, 'title' => 'Pneumàtics', 'type' => 'tires', 'frequency_km' => 15000, 'last_km_done' => 8000],
            ['motorcycle_id' => $moto1->id, 'title' => 'Cadena i pinyons', 'type' => 'chain', 'frequency_km' => 10000, 'last_km_done' => 12000],
            ['motorcycle_id' => $moto1->id, 'title' => 'Pastilles de fre', 'type' => 'brakes', 'frequency_km' => 20000, 'last_km_done' => 5000],
            ['motorcycle_id' => $moto1->id, 'title' => 'Revisió ITV', 'type' => 'inspection', 'frequency_km' => 12000, 'last_km_done' => 16000],
        ];
        foreach ($maintenanceTasks as $task) {
            MaintenanceTask::firstOrCreate(
                ['motorcycle_id' => $task['motorcycle_id'], 'title' => $task['title']],
                $task
            );
        }

        // ═══════════════════════════════════════════════
        // 4. RUTES (per la captura 2)
        // ═══════════════════════════════════════════════
        $routesData = [
            [
                'user_id' => $admin->id,
                'title' => 'Ruta dels Pirineus - Coll de Nargó',
                'description' => 'Ruta espectacular per les carreteres pirinenques. Corbes interminables amb vistes increïbles al Parc Nacional d\'Aigüestortes.',
                'distance_km' => 187,
                'difficulty' => 'medium',
                'duration_min' => 210,
                'is_public' => true,
            ],
            [
                'user_id' => $users[0]->id,
                'title' => 'Circuit Garraf - Sitges',
                'description' => 'Sortida des de Barcelona, baixant per la C-32 fins a Sitges i tornant per la carretera de la costa.',
                'distance_km' => 92,
                'difficulty' => 'easy',
                'duration_min' => 110,
                'is_public' => true,
            ],
            [
                'user_id' => $users[1]->id,
                'title' => 'Volta a la Costa Brava',
                'description' => 'Ruta pel litoral gironí. Des de Blanes fins a Portbou seguint la GI-682 amb els miradors costaners.',
                'distance_km' => 145,
                'difficulty' => 'easy',
                'duration_min' => 165,
                'is_public' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'title' => 'Montseny - Les Agudes',
                'description' => 'Pujada al cim del Montseny per la cara nord. Camins de terra i asfalt, perfecte per trail.',
                'distance_km' => 78,
                'difficulty' => 'hard',
                'duration_min' => 130,
                'is_public' => true,
            ],
            [
                'user_id' => $users[3]->id,
                'title' => 'Berguedà i el Pedraforca',
                'description' => 'Volta a la icònica muntanya del Pedraforca. Baixades tècniques i vistes de 360 graus.',
                'distance_km' => 213,
                'difficulty' => 'hard',
                'duration_min' => 270,
                'is_public' => true,
            ],
            [
                'user_id' => $users[4]->id,
                'title' => 'Ruta de les Terres de l\'Ebre',
                'description' => 'Baixant pel Delta de l\'Ebre fins a Amposta. Carreteres amples i fresques amb paisatge únic.',
                'distance_km' => 310,
                'difficulty' => 'easy',
                'duration_min' => 330,
                'is_public' => true,
            ],
        ];

        $routes = [];
        foreach ($routesData as $routeData) {
            $routes[] = Route::firstOrCreate(
                ['title' => $routeData['title']],
                $routeData
            );
        }

        // Valoracions de rutes
        $reviewComments = [
            ['rating' => 5, 'comment' => 'Brutal! Les millors corbes que he fet mai. Tornaré segur!'],
            ['rating' => 4, 'comment' => 'Molt bona ruta, una mica de tràfic a l\'agost però val molt la pena.'],
            ['rating' => 5, 'comment' => 'Perfecta per estrenar les gomes noves. Recomanada 100%.'],
            ['rating' => 3, 'comment' => 'Estava bé però el ferm no estava molt bo en alguns trams.'],
            ['rating' => 5, 'comment' => 'Increïble! Les vistes als miradors eren espectaculars.'],
        ];

        foreach ($routes as $i => $route) {
            foreach ($users as $j => $user) {
                if ($user->id !== $route->user_id) {
                    RouteReview::firstOrCreate(
                        ['route_id' => $route->id, 'user_id' => $user->id],
                        array_merge($reviewComments[($i + $j) % count($reviewComments)], [
                            'route_id' => $route->id,
                            'user_id' => $user->id,
                        ])
                    );
                }
            }
        }

        // ═══════════════════════════════════════════════
        // 5. ESDEVENIMENTS (per la captura 3)
        // ═══════════════════════════════════════════════
        $eventsData = [
            [
                'user_id' => $admin->id,
                'title' => 'Salida Dominical - Montseny',
                'description' => 'Sortida tranquil·la per les carreteres del Montseny. Quedem a la gasolinera Repsol de la C-17 a les 9:00. Ritme relaxat, aptos para todos los niveles.',
                'event_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'location' => 'Gasolinera Repsol C-17, Montmeló',
                'max_participants' => 15,
                'status' => 'open',
            ],
            [
                'user_id' => $users[0]->id,
                'title' => 'Ruta de Curvas - Pirineus',
                'description' => 'Ruta avançada pels Pirineus. 300km de corbes tècniques. Cal experiència mínima de 2 anys. Sortida a les 7:00 del matí.',
                'event_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
                'location' => 'Àrea de servei AP-7, km 50',
                'max_participants' => 8,
                'status' => 'open',
            ],
            [
                'user_id' => $users[2]->id,
                'title' => 'Concentració Motera Sitges',
                'description' => 'Gran concentració anual de motos a Sitges. Exposició, concerts i test rides. Tots els estils benvinguts!',
                'event_date' => now()->addDays(21)->format('Y-m-d H:i:s'),
                'location' => 'Passeig Marítim de Sitges',
                'max_participants' => 200,
                'status' => 'open',
            ],
            [
                'user_id' => $users[3]->id,
                'title' => 'Trail Aventura - Garrotxa',
                'description' => 'Ruta d\'aventura per camins de terra a la Garrotxa. Porta pneumàtics d\'enduro. Pista de 40km.',
                'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'location' => 'Olot, Plaça Major',
                'max_participants' => 10,
                'status' => 'open',
            ],
        ];

        $events = [];
        foreach ($eventsData as $eventData) {
            $event = Event::firstOrCreate(
                ['title' => $eventData['title']],
                $eventData
            );
            $events[] = $event;

            // Afegim alguns participants
            foreach (array_slice($users, 0, rand(2, 4)) as $user) {
                if (!$event->participants()->where('user_id', $user->id)->exists()) {
                    $event->participants()->attach($user->id);
                }
            }
        }

        // ═══════════════════════════════════════════════
        // 6. XATS (per la captura 4)
        // ═══════════════════════════════════════════════

        // Xat directe entre admin i marc (simulant compra/venda)
        $directChat = Conversation::firstOrCreate([
            'type' => 'direct',
            'motorcycle_id' => $motorcycles[2]->id, // Kawasaki Z900
        ]);
        if (!$directChat->participants()->where('user_id', $admin->id)->exists()) {
            $directChat->participants()->attach([$admin->id, $users[0]->id]);
        }

        $directMessages = [
            ['sender_id' => $users[0]->id, 'body' => 'Hola! He vist la teva Kawasaki Z900 al market. Encara la vens?', 'created_at' => now()->subMinutes(45)],
            ['sender_id' => $admin->id,    'body' => 'Sí! 7.500€, amb les gomes noves de fa 2.000km. Perfecte estat.', 'created_at' => now()->subMinutes(40)],
            ['sender_id' => $users[0]->id, 'body' => 'Quants km té? I ha tingut algun cop?', 'created_at' => now()->subMinutes(35)],
            ['sender_id' => $admin->id,    'body' => '5.200km. Sense cap cop, tota original. Tinc totes les factures.', 'created_at' => now()->subMinutes(30)],
            ['sender_id' => $users[0]->id, 'body' => 'M\'interessa molt! Et puc passar a veure-la este cap de setmana?', 'created_at' => now()->subMinutes(15)],
            ['sender_id' => $admin->id,    'body' => 'Clar! Dissabte al matí bé. Quina hora t\'anirà millor?', 'created_at' => now()->subMinutes(10)],
            ['sender_id' => $users[0]->id, 'body' => '11:00h? Sóc de Badalona, no sé si et queda lluny', 'created_at' => now()->subMinutes(5)],
            ['sender_id' => $admin->id,    'body' => 'Perfecte! Et passo la ubicació ara. Fins dissabte! 🤝', 'created_at' => now()->subMinutes(2)],
        ];
        foreach ($directMessages as $msg) {
            Message::firstOrCreate(
                ['conversation_id' => $directChat->id, 'sender_id' => $msg['sender_id'], 'body' => $msg['body']],
                array_merge($msg, ['conversation_id' => $directChat->id])
            );
        }

        // Xat de grup d'un event (Salida Dominical)
        $groupChat = Conversation::firstOrCreate([
            'type' => 'group',
            'event_id' => $events[0]->id,
            'name' => 'Salida Dominical - Montseny 🏍️',
        ]);
        $groupParticipants = array_merge([$admin->id], array_map(fn($u) => $u->id, array_slice($users, 0, 4)));
        foreach ($groupParticipants as $pid) {
            if (!$groupChat->participants()->where('user_id', $pid)->exists()) {
                $groupChat->participants()->attach($pid);
            }
        }

        $groupMessages = [
            ['sender_id' => $admin->id,    'body' => 'Hola a tots! Confirmeu assistència per a diumenge?', 'created_at' => now()->subHours(5)],
            ['sender_id' => $users[0]->id, 'body' => 'Jo hi seré! Voldria sortir cap 8:45 per agafar bona hora 🚀', 'created_at' => now()->subHours(4)->subMinutes(50)],
            ['sender_id' => $users[1]->id, 'body' => 'Hola! Confirmo. Puc portar a la Núria de paquera?', 'created_at' => now()->subHours(4)->subMinutes(30)],
            ['sender_id' => $admin->id,    'body' => 'Clar! Com més som millor. Quedamos a las 9:00 en la gasolinera 🏍️', 'created_at' => now()->subHours(4)],
            ['sender_id' => $users[2]->id, 'body' => 'Perfecto! Porto l\'esmorzar per fer una parada a mig camí 🥐☕', 'created_at' => now()->subHours(3)->subMinutes(30)],
            ['sender_id' => $users[3]->id, 'body' => 'Genial! Fa setmanes que esperava una sortida com aquesta 🔥', 'created_at' => now()->subHours(2)],
            ['sender_id' => $admin->id,    'body' => 'Previsió de bon temps ☀️ Va a ser una passada!', 'created_at' => now()->subHours(1)],
            ['sender_id' => $users[0]->id, 'body' => '👏👏 Fenomenal! Fins diumenge que fa! 🏁', 'created_at' => now()->subMinutes(30)],
        ];
        foreach ($groupMessages as $msg) {
            Message::firstOrCreate(
                ['conversation_id' => $groupChat->id, 'sender_id' => $msg['sender_id'], 'body' => $msg['body']],
                array_merge($msg, ['conversation_id' => $groupChat->id])
            );
        }

        // ═══════════════════════════════════════════════
        // 7. MARKET / VENDES (per la captura 5)
        // ═══════════════════════════════════════════════
        $salesData = [
            [
                'user_id' => $users[0]->id,
                'motorcycle_id' => $motorcycles[2]->id,
                'title' => 'Kawasaki Z900 2023 - Com nova',
                'description' => 'Venc la meva Z900 per canvi de moto. 5.200km, gomes noves, sense cap cop. Tota la documentació i factures des del concessionari. Revisió feta fa 1.000km.',
                'price' => 7500.00,
                'category' => 'moto',
                'condition' => 'excellent',
                'status' => 'active',
                'location' => 'Barcelona',
            ],
            [
                'user_id' => $users[1]->id,
                'motorcycle_id' => $motorcycles[3]->id,
                'title' => 'Ducati Monster 821 - Roja preciosa',
                'description' => 'Monster 821 en vermell original Ducati. 22.000km amb manteniment oficial complet. Inclou maletes laterals originals Ducati i proteccions de carenats.',
                'price' => 8900.00,
                'category' => 'moto',
                'condition' => 'good',
                'status' => 'active',
                'location' => 'Sabadell',
            ],
            [
                'user_id' => $users[2]->id,
                'motorcycle_id' => null,
                'title' => 'Casc Arai RX-7V - Talla L',
                'description' => 'Casc Arai RX-7V en blanc perla. Talla L (59-60cm). Usat 2 temporades, sense cops. El venc perquè he canviat de mida. Preu original 850€.',
                'price' => 380.00,
                'category' => 'accessori',
                'condition' => 'good',
                'status' => 'active',
                'location' => 'Girona',
            ],
            [
                'user_id' => $users[3]->id,
                'motorcycle_id' => $motorcycles[5]->id,
                'title' => 'KTM 890 Duke R - Edició especial',
                'description' => 'KTM 890 Duke R amb pack Racing. Suspensions WP Apex Pro ajustables, frens Brembo M50. 8.700km certificats. La moto més divertida que he tingut.',
                'price' => 11200.00,
                'category' => 'moto',
                'condition' => 'excellent',
                'status' => 'active',
                'location' => 'Lleida',
            ],
            [
                'user_id' => $users[4]->id,
                'motorcycle_id' => null,
                'title' => 'Guants Dainese Carbon 3 - Talla M',
                'description' => 'Guants d\'estiu Dainese Carbon 3 Long. Talla M. Pràcticament nous, 2 sortides posats. Incompatibles amb la nova moto.',
                'price' => 65.00,
                'category' => 'accessori',
                'condition' => 'excellent',
                'status' => 'active',
                'location' => 'Tarragona',
            ],
        ];

        foreach ($salesData as $sale) {
            SaleListing::firstOrCreate(
                ['title' => $sale['title']],
                $sale
            );
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('👤 Admin: admin@clutchcontrol.com / password');
        $this->command->info('👤 Users: marc@demo.com, laura@demo.com, rafa@demo.com ... / password');
    }
}
