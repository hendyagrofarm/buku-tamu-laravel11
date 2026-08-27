<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pabrik = Location::updateOrCreate(['slug' => 'pabrik'], [
            'name' => 'Pabrik', 'description' => 'Lokasi operasional pabrik', 'is_active' => true,
        ]);
        $ho = Location::updateOrCreate(['slug' => 'ho'], [
            'name' => 'HO', 'description' => 'Head Office', 'is_active' => true,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@bukutamu.local'],
            ['name' => 'Administrator', 'password' => Hash::make('password123'), 'role' => 'admin', 'location_id' => null, 'is_active' => true]
        );
        User::updateOrCreate(
            ['email' => 'petugas.pabrik@bukutamu.local'],
            ['name' => 'Petugas Pabrik', 'password' => Hash::make('password123'), 'role' => 'receptionist', 'location_id' => $pabrik->id, 'is_active' => true]
        );
        User::updateOrCreate(
            ['email' => 'petugas.ho@bukutamu.local'],
            ['name' => 'Petugas HO', 'password' => Hash::make('password123'), 'role' => 'receptionist', 'location_id' => $ho->id, 'is_active' => true]
        );

        $management = Division::firstOrCreate(['name' => 'Manajemen'], ['description' => 'Direksi dan manajemen perusahaan', 'is_active' => true]);
        $general = Division::firstOrCreate(['name' => 'General Affairs'], ['description' => 'Administrasi umum dan penerimaan tamu', 'is_active' => true]);
        $it = Division::firstOrCreate(['name' => 'Information Technology'], ['description' => 'Divisi teknologi informasi', 'is_active' => true]);

        Employee::firstOrCreate(['email' => 'direktur@example.com'], ['location_id' => $pabrik->id, 'division_id' => $management->id, 'name' => 'Budi Santoso', 'position' => 'Direktur', 'phone' => '081234567890', 'is_active' => true]);
        Employee::firstOrCreate(['email' => 'ga@example.com'], ['location_id' => $pabrik->id, 'division_id' => $general->id, 'name' => 'Siti Rahma', 'position' => 'GA Supervisor', 'phone' => '081234567891', 'is_active' => true]);
        Employee::firstOrCreate(['email' => 'it@example.com'], ['location_id' => $ho->id, 'division_id' => $it->id, 'name' => 'Andi Pratama', 'position' => 'IT Support', 'phone' => '081234567892', 'is_active' => true]);
    }
}
