<?php

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        Service::truncate();

        Service::insert([
            [
                'name' => 'Cleaning Services',
                'status' => 1,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1603712725038-e9334ae8f39f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80',
            ],
            [
                'name' => 'Handyman Services',
                'status' => 1,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',

            ],
            [
                'name' => 'Moving Services',
                'status' => 0,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1600518464441-9154a4dea21b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1975&q=80',
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
