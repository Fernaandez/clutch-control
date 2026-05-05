<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Route;
use App\Models\Event;
use App\Models\SaleListing;
use App\Models\Motorcycle;
use App\Models\MaintenanceTask;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'routes' => Route::count(),
            'events' => Event::count(),
            'sales' => SaleListing::count(),
            'motorcycles' => Motorcycle::count(),
            'maintenance' => MaintenanceTask::count(),
            'reports_pending' => Report::where('status', 'pending')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }
}
