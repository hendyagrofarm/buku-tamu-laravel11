<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function home(): View
    {
        $today = now()->toDateString();

        $stats = [
            'today' => Visit::whereDate('check_in_at', $today)->count(),
            'active' => Visit::whereDate('check_in_at', $today)->where('status', 'active')->count(),
            'completed' => Visit::whereDate('check_in_at', $today)->where('status', 'completed')->count(),
        ];

        $recentVisits = Visit::with(['visitor', 'employee.division'])
            ->whereDate('check_in_at', $today)
            ->latest('check_in_at')
            ->limit(4)
            ->get();

        return view('kiosk.home', compact('stats', 'recentVisits'));
    }

    public function create(): View
    {
        $employees = Employee::with('division')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('kiosk.register', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'company' => ['required', 'string', 'max:150'],
            'employee_id' => ['required', 'exists:employees,id'],
            'purpose' => ['required', 'string', 'max:500'],
            'number_of_people' => ['required', 'integer', 'min:1', 'max:20'],
            'vehicle_number' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
            'privacy' => ['accepted'],
        ], [
            'privacy.accepted' => 'Silakan menyetujui penggunaan data untuk keperluan kunjungan.',
        ]);

        $visit = DB::transaction(function () use ($validated) {
            $visitor = Visitor::updateOrCreate(
                ['phone' => $validated['phone']],
                [
                    'name' => $validated['name'],
                    'email' => $validated['email'] ?? null,
                    'company' => $validated['company'],
                    'vehicle_number' => $validated['vehicle_number'] ?? null,
                ]
            );

            do {
                $visitNumber = 'BT-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
            } while (Visit::where('visit_number', $visitNumber)->exists());

            return Visit::create([
                'visit_number' => $visitNumber,
                'visitor_id' => $visitor->id,
                'employee_id' => $validated['employee_id'],
                'purpose' => $validated['purpose'],
                'number_of_people' => $validated['number_of_people'],
                'check_in_at' => now(),
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('kiosk.visit.success', $visit);
    }

    public function success(Visit $visit): View
    {
        $visit->load(['visitor', 'employee.division']);

        return view('kiosk.success', compact('visit'));
    }

    public function checkout(): View
    {
        return view('kiosk.checkout', ['visits' => collect(), 'keyword' => null]);
    }

    public function searchCheckout(Request $request): View
    {
        $keyword = trim((string) $request->validate([
            'keyword' => ['required', 'string', 'max:100'],
        ])['keyword']);

        $visits = Visit::with(['visitor', 'employee.division'])
            ->where('status', 'active')
            ->where(function ($query) use ($keyword) {
                $query->where('visit_number', 'like', "%{$keyword}%")
                    ->orWhereHas('visitor', function ($visitorQuery) use ($keyword) {
                        $visitorQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    });
            })
            ->latest('check_in_at')
            ->limit(10)
            ->get();

        return view('kiosk.checkout', compact('visits', 'keyword'));
    }

    public function completeCheckout(Visit $visit): RedirectResponse
    {
        if ($visit->status !== 'active') {
            return redirect()->route('kiosk.checkout')->with('error', 'Kunjungan tersebut sudah selesai.');
        }

        $visit->update([
            'status' => 'completed',
            'check_out_at' => now(),
        ]);

        return redirect()->route('kiosk.home')->with('success', 'Check-out berhasil. Terima kasih atas kunjungan Anda.');
    }
}
