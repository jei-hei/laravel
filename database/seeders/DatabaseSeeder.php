<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'campus'   => 'Echague',
            'role'     => 'admin',
            'password' => 'AdminPass123!', // model mutator will hash
        ]);

        // Student user
        User::create([
            'name'       => 'Student User',
            'email'      => 'student@example.com',
            'student_id' => '12-3456',
            'lrn'        => '123456789012', // model will populate lrn_hash
            'campus'     => 'Echague',
            'role'       => 'student',
            'password'   => 'StudentPass123!', // optional; model will hash
        ]);
    }
}
