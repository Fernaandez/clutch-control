<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'storageUrl' => rtrim(\Illuminate\Support\Facades\Storage::disk('public')->url(''), '/'),
            // Consulta SQL (no accessors): evita 500 si hi ha dades rares a maintenance_tasks
            'has_pending_maintenance' => $user
                ? MaintenanceTask::query()
                    ->join('motorcycles', 'motorcycles.id', '=', 'maintenance_tasks.motorcycle_id')
                    ->where('motorcycles.user_id', $user->id)
                    ->whereNotNull('maintenance_tasks.frequency_km')
                    ->where('maintenance_tasks.frequency_km', '>', 0)
                    ->whereRaw(
                        'motorcycles.current_km >= COALESCE(maintenance_tasks.last_km_done, 0) + maintenance_tasks.frequency_km'
                    )
                    ->exists()
                : false,
            'unread_chats_count' => $user
                ? \App\Models\Message::whereIn('conversation_id', 
                    \App\Models\Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))->pluck('id')
                  )->where('sender_id', '!=', $user->id)->whereNull('read_at')->count()
                : 0,
            'flash' => [
                'habitual_done' => fn () => $request->session()->get('habitual_done'),
            ],
        ];
    }
}
