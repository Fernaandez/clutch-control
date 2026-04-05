<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorcycleBrandsSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            // ─── HONDA ───────────────────────────────────────────
            ['brand' => 'Honda', 'country' => 'Japó', 'models' => [
                ['name' => 'CB500F',        'cc' => 471,  'cv' => 47,  'type' => 'Naked'],
                ['name' => 'CB500X',        'cc' => 471,  'cv' => 47,  'type' => 'Trail'],
                ['name' => 'CB650R',        'cc' => 649,  'cv' => 95,  'type' => 'Naked'],
                ['name' => 'CB1000R',       'cc' => 998,  'cv' => 143, 'type' => 'Naked'],
                ['name' => 'CBR500R',       'cc' => 471,  'cv' => 47,  'type' => 'Sport'],
                ['name' => 'CBR650R',       'cc' => 649,  'cv' => 95,  'type' => 'Sport'],
                ['name' => 'CBR1000RR-R',   'cc' => 999,  'cv' => 217, 'type' => 'Sport'],
                ['name' => 'Hornet 750',    'cc' => 755,  'cv' => 92,  'type' => 'Naked'],
                ['name' => 'Africa Twin',   'cc' => 1084, 'cv' => 102, 'type' => 'Trail'],
                ['name' => 'NC750X',        'cc' => 745,  'cv' => 58,  'type' => 'Trail'],
                ['name' => 'Forza 750',     'cc' => 745,  'cv' => 58,  'type' => 'Scooter'],
                ['name' => 'Forza 350',     'cc' => 330,  'cv' => 29,  'type' => 'Scooter'],
                ['name' => 'SH350i',        'cc' => 330,  'cv' => 29,  'type' => 'Scooter'],
                ['name' => 'PCX125',        'cc' => 125,  'cv' => 12,  'type' => 'Scooter'],
                ['name' => 'X-ADV 750',     'cc' => 745,  'cv' => 58,  'type' => 'Trail'],
                ['name' => 'Gold Wing',     'cc' => 1833, 'cv' => 126, 'type' => 'Touring'],
                ['name' => 'Rebel 500',     'cc' => 471,  'cv' => 47,  'type' => 'Custom'],
                ['name' => 'CMX1100 Rebel', 'cc' => 1084, 'cv' => 87,  'type' => 'Custom'],
                ['name' => 'CB125R',        'cc' => 125,  'cv' => 15,  'type' => 'Naked'],
            ]],

            // ─── YAMAHA ──────────────────────────────────────────
            ['brand' => 'Yamaha', 'country' => 'Japó', 'models' => [
                ['name' => 'MT-03',         'cc' => 321,  'cv' => 42,  'type' => 'Naked'],
                ['name' => 'MT-07',         'cc' => 689,  'cv' => 75,  'type' => 'Naked'],
                ['name' => 'MT-09',         'cc' => 890,  'cv' => 119, 'type' => 'Naked'],
                ['name' => 'MT-10',         'cc' => 998,  'cv' => 165, 'type' => 'Naked'],
                ['name' => 'R3',            'cc' => 321,  'cv' => 42,  'type' => 'Sport'],
                ['name' => 'R7',            'cc' => 689,  'cv' => 73,  'type' => 'Sport'],
                ['name' => 'R1',            'cc' => 998,  'cv' => 200, 'type' => 'Sport'],
                ['name' => 'Tracer 7',      'cc' => 689,  'cv' => 73,  'type' => 'Touring'],
                ['name' => 'Tracer 9',      'cc' => 890,  'cv' => 119, 'type' => 'Touring'],
                ['name' => 'Ténéré 700',    'cc' => 689,  'cv' => 73,  'type' => 'Trail'],
                ['name' => 'Ténéré 700 World Raid', 'cc' => 689, 'cv' => 73, 'type' => 'Trail'],
                ['name' => 'XMAX 300',      'cc' => 292,  'cv' => 28,  'type' => 'Scooter'],
                ['name' => 'XMAX 125',      'cc' => 125,  'cv' => 15,  'type' => 'Scooter'],
                ['name' => 'TMAX 560',      'cc' => 562,  'cv' => 47,  'type' => 'Scooter'],
                ['name' => 'NMAX 125',      'cc' => 125,  'cv' => 15,  'type' => 'Scooter'],
                ['name' => 'XSR700',        'cc' => 689,  'cv' => 73,  'type' => 'Classic'],
                ['name' => 'XSR900',        'cc' => 890,  'cv' => 119, 'type' => 'Classic'],
                ['name' => 'Yzinger 125',   'cc' => 125,  'cv' => 14,  'type' => 'Naked'],
            ]],

            // ─── KAWASAKI ─────────────────────────────────────────
            ['brand' => 'Kawasaki', 'country' => 'Japó', 'models' => [
                ['name' => 'Z400',          'cc' => 399,  'cv' => 45,  'type' => 'Naked'],
                ['name' => 'Z650',          'cc' => 649,  'cv' => 67,  'type' => 'Naked'],
                ['name' => 'Z900',          'cc' => 948,  'cv' => 125, 'type' => 'Naked'],
                ['name' => 'Z1000',         'cc' => 1043, 'cv' => 142, 'type' => 'Naked'],
                ['name' => 'Ninja 400',     'cc' => 399,  'cv' => 45,  'type' => 'Sport'],
                ['name' => 'Ninja 650',     'cc' => 649,  'cv' => 67,  'type' => 'Sport'],
                ['name' => 'Ninja ZX-6R',   'cc' => 636,  'cv' => 130, 'type' => 'Sport'],
                ['name' => 'Ninja ZX-10R',  'cc' => 998,  'cv' => 210, 'type' => 'Sport'],
                ['name' => 'Versys 650',    'cc' => 649,  'cv' => 67,  'type' => 'Trail'],
                ['name' => 'Versys 1000',   'cc' => 1043, 'cv' => 120, 'type' => 'Trail'],
                ['name' => 'W800',          'cc' => 773,  'cv' => 48,  'type' => 'Classic'],
                ['name' => 'Vulcan S 650',  'cc' => 649,  'cv' => 61,  'type' => 'Custom'],
                ['name' => 'Z125 Pro',      'cc' => 125,  'cv' => 10,  'type' => 'Naked'],
            ]],

            // ─── SUZUKI ───────────────────────────────────────────
            ['brand' => 'Suzuki', 'country' => 'Japó', 'models' => [
                ['name' => 'GSX-8S',        'cc' => 776,  'cv' => 83,  'type' => 'Naked'],
                ['name' => 'GSX-S750',      'cc' => 749,  'cv' => 105, 'type' => 'Naked'],
                ['name' => 'GSX-S1000',     'cc' => 999,  'cv' => 150, 'type' => 'Naked'],
                ['name' => 'SV650',         'cc' => 645,  'cv' => 75,  'type' => 'Naked'],
                ['name' => 'GSX-R600',      'cc' => 599,  'cv' => 120, 'type' => 'Sport'],
                ['name' => 'GSX-R750',      'cc' => 749,  'cv' => 148, 'type' => 'Sport'],
                ['name' => 'GSX-R1000R',    'cc' => 999,  'cv' => 202, 'type' => 'Sport'],
                ['name' => 'V-Strom 650',   'cc' => 645,  'cv' => 71,  'type' => 'Trail'],
                ['name' => 'V-Strom 800DE', 'cc' => 776,  'cv' => 83,  'type' => 'Trail'],
                ['name' => 'V-Strom 1050',  'cc' => 1037, 'cv' => 107, 'type' => 'Trail'],
                ['name' => 'Burgman 400',   'cc' => 400,  'cv' => 31,  'type' => 'Scooter'],
            ]],

            // ─── BMW ──────────────────────────────────────────────
            ['brand' => 'BMW', 'country' => 'Alemanya', 'models' => [
                ['name' => 'G 310 R',       'cc' => 313,  'cv' => 34,  'type' => 'Naked'],
                ['name' => 'G 310 GS',      'cc' => 313,  'cv' => 34,  'type' => 'Trail'],
                ['name' => 'F 900 R',       'cc' => 895,  'cv' => 105, 'type' => 'Naked'],
                ['name' => 'F 900 XR',      'cc' => 895,  'cv' => 105, 'type' => 'Trail'],
                ['name' => 'F 800 GS',      'cc' => 853,  'cv' => 95,  'type' => 'Trail'],
                ['name' => 'F 850 GS',      'cc' => 853,  'cv' => 95,  'type' => 'Trail'],
                ['name' => 'R 1250 GS',     'cc' => 1254, 'cv' => 136, 'type' => 'Trail'],
                ['name' => 'R 1250 GS Adventure', 'cc' => 1254, 'cv' => 136, 'type' => 'Trail'],
                ['name' => 'R 1250 RS',     'cc' => 1254, 'cv' => 136, 'type' => 'Touring'],
                ['name' => 'R 1250 RT',     'cc' => 1254, 'cv' => 136, 'type' => 'Touring'],
                ['name' => 'S 1000 RR',     'cc' => 999,  'cv' => 210, 'type' => 'Sport'],
                ['name' => 'S 1000 R',      'cc' => 999,  'cv' => 165, 'type' => 'Naked'],
                ['name' => 'S 1000 XR',     'cc' => 999,  'cv' => 165, 'type' => 'Trail'],
                ['name' => 'M 1000 RR',     'cc' => 999,  'cv' => 212, 'type' => 'Sport'],
                ['name' => 'CE 04',         'cc' => null, 'cv' => 42,  'type' => 'Scooter'],
            ]],

            // ─── KTM ──────────────────────────────────────────────
            ['brand' => 'KTM', 'country' => 'Àustria', 'models' => [
                ['name' => '125 Duke',      'cc' => 125,  'cv' => 15,  'type' => 'Naked'],
                ['name' => '390 Duke',      'cc' => 373,  'cv' => 44,  'type' => 'Naked'],
                ['name' => '790 Duke',      'cc' => 799,  'cv' => 95,  'type' => 'Naked'],
                ['name' => '890 Duke',      'cc' => 889,  'cv' => 115, 'type' => 'Naked'],
                ['name' => '1290 Super Duke R', 'cc' => 1301, 'cv' => 180, 'type' => 'Naked'],
                ['name' => 'RC 390',        'cc' => 373,  'cv' => 44,  'type' => 'Sport'],
                ['name' => '390 Adventure', 'cc' => 373,  'cv' => 44,  'type' => 'Trail'],
                ['name' => '790 Adventure', 'cc' => 799,  'cv' => 95,  'type' => 'Trail'],
                ['name' => '890 Adventure', 'cc' => 889,  'cv' => 105, 'type' => 'Trail'],
                ['name' => '1290 Super Adventure', 'cc' => 1301, 'cv' => 160, 'type' => 'Trail'],
            ]],

            // ─── DUCATI ───────────────────────────────────────────
            ['brand' => 'Ducati', 'country' => 'Itàlia', 'models' => [
                ['name' => 'Monster',       'cc' => 937,  'cv' => 111, 'type' => 'Naked'],
                ['name' => 'Monster SP',    'cc' => 937,  'cv' => 111, 'type' => 'Naked'],
                ['name' => 'Streetfighter V2', 'cc' => 955, 'cv' => 153, 'type' => 'Naked'],
                ['name' => 'Streetfighter V4', 'cc' => 1103, 'cv' => 208, 'type' => 'Naked'],
                ['name' => 'Panigale V2',   'cc' => 955,  'cv' => 155, 'type' => 'Sport'],
                ['name' => 'Panigale V4',   'cc' => 1103, 'cv' => 215, 'type' => 'Sport'],
                ['name' => 'Multistrada V2', 'cc' => 937, 'cv' => 113, 'type' => 'Trail'],
                ['name' => 'Multistrada V4', 'cc' => 1158, 'cv' => 170, 'type' => 'Trail'],
                ['name' => 'Scrambler Icon', 'cc' => 803, 'cv' => 73,  'type' => 'Classic'],
                ['name' => 'Hypermotard 950', 'cc' => 937, 'cv' => 114, 'type' => 'Naked'],
                ['name' => 'Diavel V4',     'cc' => 1158, 'cv' => 168, 'type' => 'Custom'],
            ]],

            // ─── APRILIA ──────────────────────────────────────────
            ['brand' => 'Aprilia', 'country' => 'Itàlia', 'models' => [
                ['name' => 'RS 125',        'cc' => 125,  'cv' => 15,  'type' => 'Sport'],
                ['name' => 'RS 660',        'cc' => 659,  'cv' => 100, 'type' => 'Sport'],
                ['name' => 'RSV4',          'cc' => 1099, 'cv' => 217, 'type' => 'Sport'],
                ['name' => 'Tuono 660',     'cc' => 659,  'cv' => 95,  'type' => 'Naked'],
                ['name' => 'Tuono V4',      'cc' => 1077, 'cv' => 175, 'type' => 'Naked'],
                ['name' => 'Tuareg 660',    'cc' => 659,  'cv' => 80,  'type' => 'Trail'],
                ['name' => 'Shiver 900',    'cc' => 896,  'cv' => 95,  'type' => 'Naked'],
                ['name' => 'SR GT 125',     'cc' => 125,  'cv' => 15,  'type' => 'Scooter'],
                ['name' => 'SR GT 200',     'cc' => 174,  'cv' => 18,  'type' => 'Scooter'],
            ]],

            // ─── TRIUMPH ──────────────────────────────────────────
            ['brand' => 'Triumph', 'country' => 'UK', 'models' => [
                ['name' => 'Street Triple R', 'cc' => 765, 'cv' => 118, 'type' => 'Naked'],
                ['name' => 'Street Triple RS', 'cc' => 765, 'cv' => 130, 'type' => 'Naked'],
                ['name' => 'Speed Triple 1200', 'cc' => 1160, 'cv' => 180, 'type' => 'Naked'],
                ['name' => 'Tiger 660',     'cc' => 660,  'cv' => 81,  'type' => 'Trail'],
                ['name' => 'Tiger 900',     'cc' => 888,  'cv' => 95,  'type' => 'Trail'],
                ['name' => 'Tiger 1200',    'cc' => 1160, 'cv' => 150, 'type' => 'Trail'],
                ['name' => 'Bonneville T100', 'cc' => 900, 'cv' => 65, 'type' => 'Classic'],
                ['name' => 'Bonneville T120', 'cc' => 1200, 'cv' => 80, 'type' => 'Classic'],
                ['name' => 'Thruxton RS',   'cc' => 1200, 'cv' => 104, 'type' => 'Classic'],
                ['name' => 'Trident 660',   'cc' => 660,  'cv' => 81,  'type' => 'Naked'],
            ]],

            // ─── ROYAL ENFIELD ────────────────────────────────────
            ['brand' => 'Royal Enfield', 'country' => 'Índia', 'models' => [
                ['name' => 'Meteor 350',    'cc' => 349,  'cv' => 20,  'type' => 'Classic'],
                ['name' => 'Classic 350',   'cc' => 349,  'cv' => 20,  'type' => 'Classic'],
                ['name' => 'Bullet 350',    'cc' => 349,  'cv' => 20,  'type' => 'Classic'],
                ['name' => 'Hunter 350',    'cc' => 349,  'cv' => 20,  'type' => 'Naked'],
                ['name' => 'Scram 411',     'cc' => 411,  'cv' => 24,  'type' => 'Trail'],
                ['name' => 'Himalayan',     'cc' => 411,  'cv' => 24,  'type' => 'Trail'],
                ['name' => 'Continental GT 650', 'cc' => 648, 'cv' => 47, 'type' => 'Classic'],
                ['name' => 'Interceptor 650', 'cc' => 648, 'cv' => 47, 'type' => 'Classic'],
            ]],

            // ─── HARLEY-DAVIDSON ──────────────────────────────────
            ['brand' => 'Harley-Davidson', 'country' => 'USA', 'models' => [
                ['name' => 'Sportster S',   'cc' => 1252, 'cv' => 121, 'type' => 'Custom'],
                ['name' => 'Iron 883',      'cc' => 883,  'cv' => null, 'type' => 'Custom'],
                ['name' => 'Fat Boy',       'cc' => 1868, 'cv' => null, 'type' => 'Custom'],
                ['name' => 'Street Glide',  'cc' => 1868, 'cv' => null, 'type' => 'Custom'],
                ['name' => 'Road King',     'cc' => 1868, 'cv' => null, 'type' => 'Custom'],
                ['name' => 'Pan America 1250', 'cc' => 1252, 'cv' => 150, 'type' => 'Trail'],
                ['name' => 'Nightster',     'cc' => 975,  'cv' => 90,  'type' => 'Custom'],
                ['name' => 'Low Rider S',   'cc' => 1868, 'cv' => null, 'type' => 'Custom'],
            ]],

            // ─── VESPA / PIAGGIO ──────────────────────────────────
            ['brand' => 'Vespa', 'country' => 'Itàlia', 'models' => [
                ['name' => 'GTS 300',       'cc' => 278,  'cv' => 24,  'type' => 'Scooter'],
                ['name' => 'GTS Super 300', 'cc' => 278,  'cv' => 24,  'type' => 'Scooter'],
                ['name' => 'Primavera 125', 'cc' => 125,  'cv' => 12,  'type' => 'Scooter'],
                ['name' => 'Sprint 125',    'cc' => 125,  'cv' => 12,  'type' => 'Scooter'],
            ]],

            // ─── KYMCO ────────────────────────────────────────────
            ['brand' => 'Kymco', 'country' => 'Taiwan', 'models' => [
                ['name' => 'AK 550',        'cc' => 550,  'cv' => 53,  'type' => 'Scooter'],
                ['name' => 'X-Town 300i',   'cc' => 300,  'cv' => 27,  'type' => 'Scooter'],
                ['name' => 'Xciting 400',   'cc' => 400,  'cv' => 35,  'type' => 'Scooter'],
                ['name' => 'Downtown 125',  'cc' => 125,  'cv' => 12,  'type' => 'Scooter'],
            ]],

            // ─── SYM ──────────────────────────────────────────────
            ['brand' => 'SYM', 'country' => 'Taiwan', 'models' => [
                ['name' => 'Maxsym TL 500', 'cc' => 499,  'cv' => 45,  'type' => 'Scooter'],
                ['name' => 'Maxsym 400',    'cc' => 399,  'cv' => 34,  'type' => 'Scooter'],
                ['name' => 'Joyride 300',   'cc' => 278,  'cv' => 26,  'type' => 'Scooter'],
            ]],

            // ─── BENELLI ──────────────────────────────────────────
            ['brand' => 'Benelli', 'country' => 'Itàlia', 'models' => [
                ['name' => 'TRK 502',       'cc' => 499,  'cv' => 47,  'type' => 'Trail'],
                ['name' => 'TRK 502 X',     'cc' => 499,  'cv' => 47,  'type' => 'Trail'],
                ['name' => 'Leoncino 500',  'cc' => 499,  'cv' => 47,  'type' => 'Naked'],
                ['name' => '752 S',         'cc' => 754,  'cv' => 77,  'type' => 'Naked'],
            ]],

            // ─── MOTO GUZZI ───────────────────────────────────────
            ['brand' => 'Moto Guzzi', 'country' => 'Itàlia', 'models' => [
                ['name' => 'V7',            'cc' => 853,  'cv' => 65,  'type' => 'Classic'],
                ['name' => 'V9 Bobber',     'cc' => 853,  'cv' => 55,  'type' => 'Custom'],
                ['name' => 'V85 TT',        'cc' => 853,  'cv' => 80,  'type' => 'Trail'],
                ['name' => 'V100 Mandello', 'cc' => 1042, 'cv' => 115, 'type' => 'Naked'],
            ]],

            // ─── HONDA (125cc populars a Espanya) ─ ja inclosos ───

            // ─── GENERIC / WK BIKES / ZONTES ──────────────────────
            ['brand' => 'Zontes', 'country' => 'Xina', 'models' => [
                ['name' => '125 U',         'cc' => 125,  'cv' => 15,  'type' => 'Naked'],
                ['name' => '310 T',         'cc' => 312,  'cv' => 35,  'type' => 'Trail'],
                ['name' => '310 R',         'cc' => 312,  'cv' => 35,  'type' => 'Naked'],
            ]],

            // ─── CFMOTO ───────────────────────────────────────────
            ['brand' => 'CFMoto', 'country' => 'Xina', 'models' => [
                ['name' => '300NK',         'cc' => 292,  'cv' => 29,  'type' => 'Naked'],
                ['name' => '700CL-X',       'cc' => 693,  'cv' => 70,  'type' => 'Naked'],
                ['name' => '800MT',         'cc' => 799,  'cv' => 94,  'type' => 'Trail'],
                ['name' => '450MT',         'cc' => 449,  'cv' => 45,  'type' => 'Trail'],
            ]],
        ];

        foreach ($brands as $brandData) {
            $brand = DB::table('motorcycle_brands')->insertGetId([
                'name'       => $brandData['brand'],
                'country'    => $brandData['country'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($brandData['models'] as $model) {
                DB::table('motorcycle_models')->insert([
                    'brand_id'   => $brand,
                    'name'       => $model['name'],
                    'cc'         => $model['cc'] ?? null,
                    'power_cv'   => $model['cv'] ?? null,
                    'type'       => $model['type'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
