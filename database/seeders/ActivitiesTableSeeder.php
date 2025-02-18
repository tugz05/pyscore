<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'classlist_id' => 'h7v-wxyu-8n3',
                'section_id' => 1,
                'title' => 'Introduction to Programming',
                'instruction' => 'Read Chapter 1 and answer the quiz.',
                'points' => 100,
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'due_time' => '23:59:00',
                'accesible_date' => Carbon::now()->toDateString(),
                'accesible_time' => '08:00:00',
            ],
            [
                'classlist_id' => 'h7v-wxyu-8n3',
                'section_id' => 1,
                'title' => 'Python Basics',
                'instruction' => 'Write a simple Python program that prints "Hello, World!".',
                'points' => 50,
                'due_date' => Carbon::now()->addDays(10)->toDateString(),
                'due_time' => '23:59:00',
                'accesible_date' => Carbon::now()->toDateString(),
                'accesible_time' => '08:00:00',
            ],
            [
                'classlist_id' => 'h7v-wxyu-8n3',
                'section_id' => 1,
                'title' => 'Conditional Statements',
                'instruction' => 'Explain the use of if-else statements with examples.',
                'points' => 75,
                'due_date' => Carbon::now()->addDays(14)->toDateString(),
                'due_time' => '23:59:00',
                'accesible_date' => Carbon::now()->toDateString(),
                'accesible_time' => '08:00:00',
            ],
            [
                'classlist_id' => 'h7v-wxyu-8n3',
                'section_id' => 1,
                'title' => 'Loops and Iterations',
                'instruction' => 'Write a program that demonstrates for and while loops.',
                'points' => 80,
                'due_date' => Carbon::now()->addDays(21)->toDateString(),
                'due_time' => '23:59:00',
                'accesible_date' => Carbon::now()->toDateString(),
                'accesible_time' => '08:00:00',
            ],
            [
                'classlist_id' => 'h7v-wxyu-8n3',
                'section_id' => 1,
                'title' => 'Functions in Python',
                'instruction' => 'Create a function that takes two numbers and returns their sum.',
                'points' => 100,
                'due_date' => Carbon::now()->addDays(30)->toDateString(),
                'due_time' => '23:59:00',
                'accesible_date' => Carbon::now()->toDateString(),
                'accesible_time' => '08:00:00',
            ],
        ];

        DB::table('activities')->insert($activities);
    }
}
