<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function home(): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return $this->locationPicker();

        $today = now()->toDateString();
        $base = Visit::where('location_id', $location->id);
        $stats = [
            'today' => (clone $base)->whereDate('check_in_at', $today)->count(),
            'pending_survey' => (clone $base)->whereDate('check_in_at', $today)->whereNull('satisfaction_rating')->count(),
            'surveyed' => (clone $base)->whereDate('check_in_at', $today)->whereNotNull('satisfaction_rating')->count(),
        ];
        $recentVisits = Visit::with(['visitor', 'location'])
            ->where('location_id', $location->id)->whereDate('check_in_at', $today)->latest('check_in_at')->limit(5)->get();
        return view('kiosk.home', compact('stats', 'recentVisits', 'location'));
    }

    public function locationPicker(): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('kiosk.location-picker', compact('locations'));
    }

    public function selectLocation(Location $location): RedirectResponse
    {
        abort_unless($location->is_active, 404);
        session(['kiosk_location_id' => $location->id]);
        return redirect()->route('kiosk.home');
    }

    public function create(): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        return view('kiosk.register', compact('location'));
    }

    public function store(Request $request): RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'], 'phone' => ['required', 'string', 'max:30'],
            'company' => ['required', 'string', 'max:150'], 'employee_name' => ['required', 'string', 'max:150'],
            'purpose' => ['required', 'string', 'max:500'], 'number_of_people' => ['required', 'integer', 'min:1', 'max:20'],
            'privacy' => ['accepted'],
        ], ['privacy.accepted' => 'Silakan menyetujui penggunaan data untuk keperluan kunjungan.']);

        $visit = DB::transaction(function () use ($validated, $location) {
            $visitor = Visitor::updateOrCreate(['phone' => $validated['phone']], ['name' => $validated['name'], 'company' => $validated['company']]);
            do { $visitNumber = 'BT-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)); }
            while (Visit::where('visit_number', $visitNumber)->exists());
            return Visit::create([
                'visit_number' => $visitNumber, 'visitor_id' => $visitor->id, 'location_id' => $location->id,
                'employee_id' => null, 'employee_name' => $validated['employee_name'], 'purpose' => $validated['purpose'], 'number_of_people' => $validated['number_of_people'],
                'check_in_at' => now(), 'status' => 'active',
            ]);
        });
        return redirect()->route('kiosk.survey.show', $visit);
    }

    public function status(): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        $visits = Visit::with(['visitor', 'location'])->where('location_id', $location->id)->whereDate('check_in_at', today())->latest('check_in_at')->get();
        return view('kiosk.status', compact('visits', 'location'));
    }

    public function surveyIndex(): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        $visits = Visit::with(['visitor', 'location'])->where('location_id', $location->id)->whereDate('check_in_at', today())->whereNull('satisfaction_rating')->latest('check_in_at')->get();
        return view('kiosk.survey-index', compact('visits', 'location'));
    }

    public function survey(Visit $visit): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        abort_unless((int) $visit->location_id === (int) $location->id, 404);
        if ($visit->satisfaction_rating !== null) return redirect()->route('kiosk.visit.success', $visit);
        $visit->load(['visitor', 'location']);
        return view('kiosk.survey', compact('visit'));
    }

    public function submitSurvey(Request $request, Visit $visit): RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        abort_unless((int) $visit->location_id === (int) $location->id, 404);
        if ($visit->satisfaction_rating !== null) return redirect()->route('kiosk.visit.success', $visit)->with('success', 'Survey untuk kunjungan ini sudah pernah diisi.');
        $validated = $request->validate(['rating' => ['required', 'integer', 'between:1,5']], ['rating.required' => 'Silakan pilih rating bintang terlebih dahulu.', 'rating.between' => 'Rating harus antara 1 sampai 5 bintang.']);
        $visit->update(['satisfaction_rating' => $validated['rating'], 'surveyed_at' => now(), 'status' => 'completed']);
        return redirect()->route('kiosk.visit.success', $visit);
    }

    public function success(Visit $visit): View|RedirectResponse
    {
        $location = $this->location();
        if (!$location) return redirect()->route('kiosk.location.picker');
        abort_unless((int) $visit->location_id === (int) $location->id, 404);
        $visit->load(['visitor', 'location']);
        return view('kiosk.success', compact('visit'));
    }

    private function location(): ?Location
    {
        $id = session('kiosk_location_id');
        return $id ? Location::where('id', $id)->where('is_active', true)->first() : null;
    }

}
