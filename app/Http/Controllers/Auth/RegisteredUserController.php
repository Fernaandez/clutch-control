<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            // AFEGIM UNIQUE AQUÍ. Buscarà a la taula 'users' la columna 'phone_number'
            'phone' => 'nullable|string|max:20|unique:users,phone_number', 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Personalitzem l'error perquè s'entengui bé
            'phone.unique' => 'Aquest número de telèfon ja està registrat a una altra compte.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('motorcycles.index');
    }
}