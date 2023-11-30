<?php

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Question::truncate();

        Question::insert([
            [
                'sub_service_id' => 1,
                'question' => 'How many bedrooms do you have?',
                'is_multiple' => false,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'sub_service_id' => 2,
                'question' => 'What type of plumbing work do you need?',
                'is_multiple' => false,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ],
            [
                'sub_service_id' => 3,
                'question' => 'What type of moving do you need?',
                'is_multiple' => false,
                'created_at' => \Carbon\Carbon::now(config('app.timezone')),
                'updated_at' => null,
            ]
        ]);
    }
}
