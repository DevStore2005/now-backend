<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        $sql = file_get_contents(database_path('world.sql'));
        DB::unprepared($sql);
    }
}
