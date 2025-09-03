<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@restaurant.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Crear usuario cajero
        User::create([
            'name' => 'Cajero Principal',
            'email' => 'cajero@restaurant.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Crear usuario mesero
        User::create([
            'name' => 'Mesero 1',
            'email' => 'mesero@restaurant.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}