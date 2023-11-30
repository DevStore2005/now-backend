<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\UserSeeder::class);
        $this->call(\ServiceSeeder::class);
        $this->call(\SubServiceSeeder::class);
        $this->call(\QuestionSeeder::class);
        $this->call(\OptionSeeder::class);
        $this->call(\CountrySeeder::class);
    }
}
