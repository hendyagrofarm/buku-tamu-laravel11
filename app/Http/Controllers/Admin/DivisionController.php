<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(): View
    {
        $divisions = Division::withCount('employees')->orderBy('name')->paginate(15);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create(): View
    {
        return view('admin.divisions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Division::create($this->validated($request));
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division): View
    {
        return view('admin.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division): RedirectResponse
    {
        $division->update($this->validated($request, $division->id));
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division): RedirectResponse
    {
        if ($division->employees()->exists()) {
            return back()->with('error', 'Divisi masih digunakan oleh data pegawai.');
        }
        $division->delete();
        return back()->with('success', 'Divisi berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:divisions,name'.($id ? ','.$id : '')],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
