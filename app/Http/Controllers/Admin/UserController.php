<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->adminOnly();
        $users = User::with('location')->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->adminOnly();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->adminOnly();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'locations'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,receptionist'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ]);
        if ($data['role'] !== 'admin' && empty($data['location_id'])) {
            return back()->withErrors(['location_id' => 'Petugas harus memiliki lokasi.'])->withInput();
        }
        if ($data['role'] === 'admin') $data['location_id'] = null;
        if (empty($data['password'])) unset($data['password']); else $data['password'] = Hash::make($data['password']);
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Petugas berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->adminOnly();
        if ($user->id === auth()->id()) return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        $user->delete();
        return back()->with('success', 'Petugas berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'in:admin,receptionist'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ]);
        if ($data['role'] !== 'admin' && empty($data['location_id'])) {
            return back()->withErrors(['location_id' => 'Petugas harus memiliki lokasi.'])->withInput()->throwResponse();
        }
        if ($data['role'] === 'admin') $data['location_id'] = null;
        return $data;
    }

    private function adminOnly(): void { abort_unless(auth()->user()?->isAdmin(), 403); }
}
