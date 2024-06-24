<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'address' => '28 Nguyen Tri Phuong, TP Hue',
            'phone' => '0374162222',
            'password' => Hash::make('123456')
        ]);

        \App\Models\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456')
        ]);

        $this->call([
            SliderSeeder::class,
        ]);
    }
}
