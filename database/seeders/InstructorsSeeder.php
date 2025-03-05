<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstructorsSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'email' => 'vftugajr@nemsu.edu.ph',
                'status' => 'active',
                'account_type' => 'instructor'
            ],
            [
                'email' => 'dmatlih@nemsu.edu.ph',
                'status' => 'active',
                'account_type' => 'instructor'
            ],
        ]);
    }
}
