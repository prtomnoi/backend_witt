<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'SuperAdmin',
                'email' => 'admin@twofoursixplus.com',
                'password' => Hash::make('Admin246+'),
                'role_id' => 1,
                'tel' => '099999999',
                'status' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
