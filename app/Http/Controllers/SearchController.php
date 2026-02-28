<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Event;

class SearchController extends Controller
{
    public function searchToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $token = trim($request->token);

        if ($route = Route::where('share_token', $token)->first()) {
            return redirect()->route('routes.preview', $token);
        }

        if ($event = Event::where('share_token', $token)->first()) {
            return redirect()->route('events.preview', $token);
        }

        // Return back with error using Inertia or session flash (if used)
        return back()->withErrors(['token' => 'Codi no vàlid. No s\'ha trobat per aquest token.']);
    }
}
