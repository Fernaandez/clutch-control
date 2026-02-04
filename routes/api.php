<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// En una App amb Inertia, normalment aquest fitxer gairebé no es toca.
// Deixem només la ruta d'usuari per defecte per si de cas.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- AQUI NO HI HA D'HAVER RES DE MOTOS ---
// Totes les rutes de motos ara viuen a web.php