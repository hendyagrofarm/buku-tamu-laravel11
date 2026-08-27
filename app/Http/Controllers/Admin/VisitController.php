<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VisitExcelExporter;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function index(Request $request): View
    {
        $location = $this->selectedLocation($request);
        $visits = $this->applyFilters(Visit::with(['visitor', 'employee.division', 'location']), $request, $location)
            ->latest('check_in_at')->paginate(20)->withQueryString();
        $locations = auth()->user()->isAdmin() ? Location::where('is_active', true)->orderBy('name')->get() : collect();
        return view('admin.visits.index', compact('visits', 'locations', 'location'));
    }

    public function export(Request $request)
    {
        $location = $this->selectedLocation($request);
        $visits = $this->applyFilters(Visit::with(['visitor', 'employee.division', 'location']), $request, $location)->latest('check_in_at')->get();
        $summary = [
            'total' => $visits->count(), 'surveyed' => $visits->whereNotNull('satisfaction_rating')->count(),
            'pending' => $visits->whereNull('satisfaction_rating')->count(),
            'average' => round((float) $visits->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'), 1),
            'location' => $location?->name ?? 'Semua Lokasi',
        ];
        [$path, $filename] = (new VisitExcelExporter())->download($visits, $summary);
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function show(Visit $visit): View
    {
        $location = $this->selectedLocation(request());
        if ($location && (int) $visit->location_id !== (int) $location->id) abort(404);
        $visit->load(['visitor', 'employee.division', 'creator', 'location']);
        return view('admin.visits.show', compact('visit'));
    }

    private function selectedLocation(Request $request): ?Location
    {
        $user = auth()->user();
        if (!$user->isAdmin()) return $user->location;
        return $request->filled('location_id') ? Location::find($request->integer('location_id')) : null;
    }

    private function applyFilters($query, Request $request, ?Location $location)
    {
        if ($location) $query->where('location_id', $location->id);
        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->string('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('visit_number', 'like', "%{$search}%")
                    ->orWhereHas('visitor', fn ($visitor) => $visitor->where('name', 'like', "%{$search}%")->orWhere('company', 'like', "%{$search}%"));
            });
        });
        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->status));
        $query->when($request->filled('date'), fn ($query) => $query->whereDate('check_in_at', $request->date));
        return $query;
    }
}
