<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteReviewController extends Controller
{
    public function store(Request $request, Route $route)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($route->user_id === Auth::id()) {
            return back()->with('error', 'No pots valorar la teva pròpia ruta.');
        }

        RouteReview::updateOrCreate(
            ['route_id' => $route->id, 'user_id' => Auth::id()],
            ['rating' => $validated['rating'], 'comment' => $validated['comment']]
        );

        return back()->with('success', 'Valoració guardada correctament!');
    }
}
