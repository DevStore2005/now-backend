<?php

use Carbon\Carbon;
use App\Models\SubService;
use Illuminate\Database\Seeder;

class SubServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubService::truncate();

        SubService::insert([
            [
                'name' => 'Home Cleaning',
                'service_id' => 1,
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1074&q=80',
            ],
            [
                'name' => 'Plumbing',
                'service_id' => 2,
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',
            ],
            [
                'name' => 'House Moving',
                'service_id' => 3,
                'created_at' => Carbon::now(config('app.timezone')),
                'updated_at' => null,
                'image' => 'https://images.unsplash.com/photo-1600518464441-9154a4dea21b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1975&q=80',
            ]
        ]);
    }
}
