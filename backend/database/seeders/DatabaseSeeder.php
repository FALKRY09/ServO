<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['Admin', 'Koordinator', 'Petugas', 'Pelapor'])
            ->mapWithKeys(fn (string $name) => [$name => Role::firstOrCreate(['name' => $name])]);

        $department = Department::firstOrCreate(['name' => 'IT Support']);
        Category::firstOrCreate(['name' => 'Perangkat Keras']);
        Priority::firstOrCreate(['name' => 'Normal'], ['level' => 2]);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password123'), 'role_id' => $roles['Admin']->id, 'department_id' => $department->id, 'is_active' => true]
        );

        $demoUsers = [
            ['name' => 'Koordinator Demo', 'email' => 'koordinator@example.com', 'role' => 'Koordinator'],
            ['name' => 'Petugas Demo', 'email' => 'petugas@example.com', 'role' => 'Petugas'],
            ['name' => 'Pelapor Demo', 'email' => 'pelapor@example.com', 'role' => 'Pelapor'],
        ];

        foreach ($demoUsers as $demoUser) {
            User::updateOrCreate(
                ['email' => $demoUser['email']],
                ['name' => $demoUser['name'], 'password' => Hash::make('password123'), 'role_id' => $roles[$demoUser['role']]->id, 'department_id' => $department->id, 'is_active' => true]
            );
        }
    }
}
