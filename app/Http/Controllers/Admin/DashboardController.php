<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $location = $this->selectedLocation($request);
        $base = Visit::query();
        if ($location) $base->where('location_id', $location->id);
        $stats = [
            'today' => (clone $base)->whereDate('check_in_at', today())->count(),
            'pending_survey' => (clone $base)->whereDate('check_in_at', today())->whereNull('satisfaction_rating')->count(),
            'surveyed_today' => (clone $base)->whereDate('surveyed_at', today())->count(),
            'average_rating' => round((float) (clone $base)->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'), 1),
        ];
        $recentQuery = Visit::with(['visitor', 'employee.division', 'location'])->latest('check_in_at')->limit(8);
        if ($location) $recentQuery->where('location_id', $location->id);
        $recentVisits = $recentQuery->get();
        $locations = auth()->user()->isAdmin() ? Location::where('is_active', true)->orderBy('name')->get() : collect();
        return view('admin.dashboard', compact('stats', 'recentVisits', 'location', 'locations'));
    }

    private function selectedLocation(Request $request): ?Location
    {
        $user = auth()->user();
        if (!$user->isAdmin()) return $user->location;
        return $request->filled('location_id') ? Location::find($request->integer('location_id')) : null;
    }
}
