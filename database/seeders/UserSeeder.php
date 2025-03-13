<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
        User::create(
            [
                'name' => 'Mr.Admin',
                'email' => 'demosuperadmin@gmail.com',
                'password' => Hash::make('!demo@dmin!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        User::create([
                'name' => 'Ms.Jane Doe',
                'email' => 'democashier@gmail.com',
                'password' => Hash::make('!demo@cashier!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
