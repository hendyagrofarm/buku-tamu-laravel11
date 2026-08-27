<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $location = $this->selectedLocation($request);
        $employees = Employee::with(['division', 'location'])->when($location, fn ($q) => $q->where('location_id', $location->id))
            ->when($request->filled('search'), function ($query) use ($request) { $search = $request->string('search'); $query->where(fn ($q) => $q->where('name','like',"%{$search}%")->orWhere('position','like',"%{$search}%")->orWhere('email','like',"%{$search}%")); })
            ->orderBy('name')->paginate(15)->withQueryString();
        $locations = auth()->user()->isAdmin() ? Location::where('is_active', true)->orderBy('name')->get() : collect();
        return view('admin.employees.index', compact('employees', 'locations', 'location'));
    }

    public function create(): View
    {
        $divisions = Division::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('admin.employees.create', compact('divisions', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validated($request));
        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee): View
    {
        $this->guardEmployee($employee);
        $divisions = Division::orderBy('name')->get(); $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'divisions', 'locations'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->guardEmployee($employee); $employee->update($this->validated($request, $employee->id));
        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->guardEmployee($employee);
        if ($employee->visits()->exists()) { $employee->update(['is_active' => false]); return back()->with('success', 'Pegawai memiliki riwayat kunjungan sehingga dinonaktifkan.'); }
        $employee->delete(); return back()->with('success', 'Pegawai berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $user = auth()->user();
        $data = $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'], 'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:100'], 'position' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150', 'unique:employees,email'.($id ? ','.$id : '')],
            'phone' => ['nullable', 'string', 'max:30'], 'is_active' => ['required', 'boolean'],
        ]);
        if (!$user->isAdmin()) $data['location_id'] = $user->location_id;
        if (!$data['location_id']) return back()->withErrors(['location_id' => 'Lokasi wajib dipilih.'])->withInput()->throwResponse();
        return $data;
    }

    private function selectedLocation(Request $request): ?Location { $u=auth()->user(); return $u->isAdmin() ? ($request->filled('location_id') ? Location::find($request->integer('location_id')) : null) : $u->location; }
    private function guardEmployee(Employee $employee): void { $u=auth()->user(); abort_unless($u->isAdmin() || (int)$employee->location_id === (int)$u->location_id, 404); }
}
