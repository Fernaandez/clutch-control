<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'type', 'reason', 'search']);

        $reports = Report::with(['reporter', 'reportable'])
            ->when(($filters['status'] ?? 'all') !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->when(($filters['type'] ?? 'all') !== 'all', fn ($query) => $query->where('reportable_type', $this->typeToClass($filters['type'])))
            ->when(($filters['reason'] ?? 'all') !== 'all', fn ($query) => $query->where('reason', $filters['reason']))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('details', 'like', '%' . $search . '%')
                        ->orWhere('contact_email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Report $report) => $this->serializeReport($report));

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'filters' => $filters,
            'pendingCount' => Report::where('status', 'pending')->count(),
        ]);
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'reviewer', 'reportable']);

        return Inertia::render('Admin/Reports/Show', [
            'reportRecord' => $this->serializeReport($report, detailed: true),
        ]);
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewing', 'resolved', 'dismissed'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.reports.show', $report)->with('success', 'Report updated.');
    }

    private function serializeReport(Report $report, bool $detailed = false): array
    {
        $data = [
            'id' => $report->id,
            'type' => $report->type_label,
            'type_key' => $this->classToType($report->reportable_type),
            'subject' => $report->subject_label,
            'reason' => $report->reason,
            'status' => $report->status,
            'reporter' => $report->reporter ? [
                'id' => $report->reporter->id,
                'name' => $report->reporter->name,
                'email' => $report->reporter->email,
            ] : null,
            'contact_email' => $report->contact_email,
            'created_at' => $report->created_at,
        ];

        if (!$detailed) {
            return $data;
        }

        return array_merge($data, [
            'details' => $report->details,
            'admin_notes' => $report->admin_notes,
            'ip_address' => $report->ip_address,
            'user_agent' => $report->user_agent,
            'reviewed_at' => $report->reviewed_at,
            'reviewer' => $report->reviewer ? [
                'name' => $report->reviewer->name,
                'email' => $report->reviewer->email,
            ] : null,
            'reportable' => $report->reportable,
        ]);
    }

    private function typeToClass(string $type): string
    {
        return match ($type) {
            'user' => \App\Models\User::class,
            'message' => \App\Models\Message::class,
            'route' => \App\Models\Route::class,
            'event' => \App\Models\Event::class,
            'sale' => \App\Models\SaleListing::class,
            default => $type,
        };
    }

    private function classToType(?string $class): string
    {
        return match ($class) {
            \App\Models\User::class => 'user',
            \App\Models\Message::class => 'message',
            \App\Models\Route::class => 'route',
            \App\Models\Event::class => 'event',
            \App\Models\SaleListing::class => 'sale',
            default => 'unknown',
        };
    }
}
