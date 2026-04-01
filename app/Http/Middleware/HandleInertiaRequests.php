<?php

namespace App\Http\Middleware;

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
            'storageUrl' => asset('storage'),
            'has_pending_maintenance' => $user 
                ? $user->motorcycles()->with('maintenanceTasks')->get()->contains('has_pending_maintenance', true) 
                : false,
            'unread_chats_count' => $user
                ? \App\Models\Message::whereIn('conversation_id', 
                    \App\Models\Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))->pluck('id')
                  )->where('sender_id', '!=', $user->id)->whereNull('read_at')->count()
                : 0,
        ];
    }
}
