<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'date_start', 'date_end']);

        $events = Event::with('organizer')->withCount('participants')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('location', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['date_start'] ?? null, function ($query, $dateStart) {
                $query->where('start_time', '>=', $dateStart . ' 00:00:00');
            })
            ->when($filters['date_end'] ?? null, function ($query, $dateEnd) {
                $query->where('start_time', '<=', $dateEnd . ' 23:59:59');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'filters' => $filters
        ]);
    }

    public function edit(Event $event)
    {
        $event->load(['organizer', 'routes', 'participants']);
        return Inertia::render('Admin/Events/Edit', [
            'eventRecord' => $event
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'max_participants' => 'nullable|integer|min:1',
            'share_token' => 'nullable|string',
            'photo' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }
}
