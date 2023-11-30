<?php

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Option::truncate();

        Option::insert([
            [
                'question_id' => 1,
                'option' => '1',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'question_id' => 1,
                'option' => '2',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'question_id' => 2,
                'option' => 'Leakage issue',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'question_id' => 2,
                'option' => 'Clogged drain',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'question_id' => 3,
                'option' => 'Local',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'question_id' => 3,
                'option' => 'Long distance',
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ]
        ]);
    }
}
