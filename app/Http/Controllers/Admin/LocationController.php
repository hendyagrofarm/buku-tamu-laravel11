<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index(): View
    {
        $this->adminOnly();
        $locations = Location::withCount(['users', 'employees', 'visits'])->orderBy('name')->paginate(20);
        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View { $this->adminOnly(); return view('admin.locations.create'); }

    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly();
        Location::create($this->validated($request));
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View { $this->adminOnly(); return view('admin.locations.edit', compact('location')); }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $this->adminOnly();
        $location->update($this->validated($request, $location->id));
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->adminOnly();
        if ($location->users()->exists() || $location->employees()->exists() || $location->visits()->exists()) {
            return back()->with('error', 'Lokasi sudah digunakan oleh data petugas, pegawai, atau kunjungan sehingga tidak dapat dihapus. Nonaktifkan saja.');
        }
        $location->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:locations,slug'.($id ? ','.$id : '')],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        return $data;
    }

    private function adminOnly(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
