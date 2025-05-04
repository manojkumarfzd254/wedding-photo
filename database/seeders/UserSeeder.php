<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Wife',
            'email' => 'wife@example.com',
            'role' => 'wife',
            'password' => Hash::make('12345678'),
            'access_token' => Str::uuid(),
        ]);

        User::create([
            'name' => 'Father',
            'email' => 'father@example.com',
            'role' => 'family',
            'password' => Hash::make('12345678'),
            'access_token' => Str::uuid(),
        ]);
    }
}
