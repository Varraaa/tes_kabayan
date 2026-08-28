<?php

namespace Database\Seeders;

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
        // admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sinar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // operator
        User::create([
            'name' => 'Operator',
            'email' => 'operator@sinar.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);

        // panggil master gudang dan barang
        $this->call([
            GudangSeeder::class,
            BarangSeeder::class,
        ]);
    }
}
