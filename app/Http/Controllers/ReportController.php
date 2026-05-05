<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use App\Models\Report;
use App\Models\Route;
use App\Models\SaleListing;
use App\Models\User;
use App\Notifications\NewReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    private const REPORTABLE_TYPES = [
        'user' => User::class,
        'message' => Message::class,
        'route' => Route::class,
        'event' => Event::class,
        'sale' => SaleListing::class,
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string', Rule::in(array_keys(self::REPORTABLE_TYPES))],
            'reportable_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', Rule::in(['spam', 'harassment', 'scam', 'inappropriate', 'dangerous', 'other'])],
            'details' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        $reportableClass = self::REPORTABLE_TYPES[$validated['reportable_type']];
        $reportable = $reportableClass::findOrFail($validated['reportable_id']);

        $this->authorizeReportableAccess($request, $reportable);

        $report = Report::create([
            'reportable_type' => $reportableClass,
            'reportable_id' => $reportable->id,
            'reporter_id' => Auth::id(),
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            try {
                Notification::send($admins, new NewReportNotification($report));
            } catch (\Throwable $e) {
                Log::error('Report notification failed: ' . $e->getMessage(), ['report_id' => $report->id]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Denuncia enviada.']);
        }

        return back()->with('success', 'Denuncia enviada.');
    }

    private function authorizeReportableAccess(Request $request, mixed $reportable): void
    {
        if ($reportable instanceof Message) {
            if (!Auth::check() || !$reportable->conversation->participants()->where('user_id', Auth::id())->exists()) {
                abort(403);
            }
        }

        if ($reportable instanceof Route && !$reportable->is_public && !$this->isSharePreview($request, $reportable)) {
            if (!Auth::check() || $reportable->user_id !== Auth::id()) {
                abort(403);
            }
        }

        if ($reportable instanceof Event && !$reportable->is_public && !$this->isSharePreview($request, $reportable)) {
            if (!Auth::check() || $reportable->user_id !== Auth::id() && !$reportable->participants()->where('user_id', Auth::id())->exists()) {
                abort(403);
            }
        }
    }

    private function isSharePreview(Request $request, Route|Event $reportable): bool
    {
        $referer = (string) $request->headers->get('referer', '');

        if (!$reportable->share_token || $referer === '') {
            return false;
        }

        return str_contains($referer, "/r/{$reportable->share_token}")
            || str_contains($referer, "/e/{$reportable->share_token}");
    }
}
