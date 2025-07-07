<?php

namespace Database\Seeders;

use App\Models\academy;
use App\Models\User;
use Illuminate\Container\Attributes\Database;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::query()->delete();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@blendbarometer.nl',
            'password' => Hash::make(env('USER_PASSWORD')),
        ]);
    }
}
