<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'full_name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'super_admin',
        // ]);
        User::factory()->create([
            'full_name' => 'Test Admin',
            'email' => 'test@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin_rs',
        ]);
        User::factory()->create([
            'full_name' => 'Test Patient',
            'email' => 'test@patient.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
        ]);
        User::factory()->create([
            'full_name' => 'Test Doctor',
            'email' => 'test@doctor.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
        ]);
        User::factory()->create([
            'full_name' => 'Test Staff',
            'email' => 'test@staff.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);
    }
}
