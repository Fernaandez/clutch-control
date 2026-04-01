<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * SAFATA D'ENTRADA: Mostra totes les converses de l'usuari (directes + grups)
     */
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['participants', 'motorcycle', 'event'])
            ->withCount(['messages as unread_count' => function ($query) use ($userId) {
                $query->where('sender_id', '!=', $userId)->whereNull('read_at');
            }])
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            )
            ->get()
            ->map(function ($conv) use ($userId) {
                $lastMsg = $conv->messages()->orderByDesc('created_at')->first();
                $conv->last_message = $lastMsg ? $lastMsg->body : 'Encara no hi ha missatges';
                $conv->last_message_time = $lastMsg ? $lastMsg->created_at : $conv->created_at;

                if ($conv->type === 'direct') {
                    $conv->other_user = $conv->participants->firstWhere('id', '!=', $userId);
                } else {
                    $conv->other_user = null; // Groups don't have a single "other"
                }

                return $conv;
            });

        return Inertia::render('Chats/Index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * SALA DE XAT: Mostrar conversa individual amb tots els missatges
     */
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();

        // Comprovar que l'usuari pertany a la conversa
        if (!$conversation->participants()->where('user_id', $userId)->exists()) {
            abort(403);
        }

        // Marcar missatges no llegits com a llegits
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['participants', 'motorcycle', 'event', 'messages.sender']);

        $otherUser = null;
        if ($conversation->type === 'direct') {
            $otherUser = $conversation->participants->firstWhere('id', '!=', $userId);
        }

        return Inertia::render('Chats/Show', [
            'conversation' => $conversation,
            'otherUser' => $otherUser
        ]);
    }

    /**
     * INICIAR CONVERSA DIRECTA (des del Mercat de Motos)
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'other_user_id' => 'required|exists:users,id',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $userId = Auth::id();
        $otherUserId = $validated['other_user_id'];

        if ($userId == $otherUserId) {
            return back()->with('error', 'No pots parlar amb tu mateix.');
        }

        $motoId = $validated['motorcycle_id'] ?? null;

        // Buscar si ja existeix una conversa directa entre els 2 per a la mateixa moto
        $existing = Conversation::where('type', 'direct')
            ->where('motorcycle_id', $motoId)
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('participants', function ($q) use ($otherUserId) {
                $q->where('user_id', $otherUserId);
            })
            ->first();

        if ($existing) {
            return redirect()->route('chats.show', $existing->id);
        }

        // Crear nova conversa
        $conversation = Conversation::create([
            'type' => 'direct',
            'motorcycle_id' => $motoId,
        ]);

        $conversation->participants()->attach([$userId, $otherUserId]);

        return redirect()->route('chats.show', $conversation->id);
    }

    /**
     * ENVIAR MISSATGE dins d'una conversa
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();

        if (!$conversation->participants()->where('user_id', $userId)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000'
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body' => $validated['body']
        ]);

        $message->load('sender');

        try {
            if (class_exists(\App\Events\MessageSent::class)) {
                event(new \App\Events\MessageSent($message));
            }
            
            // FASE 2: Enviar també la notificació Push via Firebase a tots excepte nosaltres
            $otherParticipants = $conversation->participants()->where('user_id', '!=', $userId)->get();
            $messaging = app('firebase.messaging');
            $senderName = $message->sender->name;
            $title = $conversation->type === 'group' ? "Grup: {$conversation->name}" : "Missatge de {$senderName}";
            
            foreach ($otherParticipants as $participant) {
                if ($participant->fcm_token) {
                    $notification = \Kreait\Firebase\Messaging\Notification::create($title, $message->body);
                    $cloudMessage = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $participant->fcm_token)
                        ->withNotification($notification);
                    $messaging->send($cloudMessage);
                }
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Firebase/Pusher error: ' . $e->getMessage());
        }

        return back();
    }
}
