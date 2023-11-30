<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        User::insert([
            [
                'first_name' => 'Farenow',
                'last_name' => 'Admin',
                'email' => 'support@farenow.com',
                'email_verified_at' => Carbon::now(),
                'phone' => '123456789',
                'password' => Hash::make('Farenow123**'),
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
                'phone_verification' => '1',
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => Carbon::now(config('app.timezone')),
            ],
            [
                'first_name' => 'provider',
                'last_name' => 'provider',
                'email' => 'provider@site.com',
                'email_verified_at' => Carbon::now(config('app.timezone')),
                'phone' => '123456789',
                'password' => Hash::make('provider123'),
                'role' => 'PROVIDER',
                'status' => 'ACTIVE',
                'phone_verification' => '1',
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => Carbon::now(config('app.timezone')),
            ],
            [
                'first_name' => 'user',
                'last_name' => 'user',
                'email' => 'user@site.com',
                'phone' => '123456789',
                'password' => Hash::make('user123'),
                'email_verified_at' => Carbon::now(config('app.timezone')),
                'role' => 'USER',
                'status' => 'ACTIVE',
                'phone_verification' => '1',
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => Carbon::now(config('app.timezone')),
            ],
        ]);
    }
}
