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
                'email' => 'rcgbandoy@nemsu.edu.ph',
                'name' => 'Radz Chirro G. Bandoy',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'jlbarte@nemsu.edu.ph',
                'name' => 'Jericho L. Barte',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'joronquillo@nemsu.edu.ph',
                'name' => 'Jenny O. Ronquillo',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'rnburlasa@nemsu.edu.ph',
                'name' => 'Romel N. Burlasa',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'gdbutron@nemsu.edu.ph',
                'name' => 'Glyzle D. Butron',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'jamcadao@nemsu.edu.ph',
                'name' => 'Junard Adrian M. Cadao',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'racananes@nemsu.edu.ph',
                'name' => 'Ronnel Cañanes',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'jjdaguiso@nemsu.edu.ph',
                'name' => 'Jaybee J. Daguiso',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'fpdeleguerjr@nemsu.edu.ph',
                'name' => 'Fernando Deleguer Jr',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'mrjcdivino@nemsu.edu.ph',
                'name' => 'Ma. Rhea Jane C. Divino',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'mdetchon@nemsu.edu.ph',
                'name' => 'Meljoy D. Etchon',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'erafestejo@nemsu.edu.ph',
                'name' => 'Earl Rey A. Festejo',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'ljsgallardo@nemsu.edu.ph',
                'name' => 'Louie Jay Gallardo',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'naogardigo@nemsu.edu.ph',
                'name' => 'Nick Ale Gardigo',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'spmaunes@nemsu.edu.ph',
                'name' => 'Sandara P. Maunes',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'rquijada@nemsu.edu.ph',
                'name' => 'Rommel Quijada',
                'status' => 'active',
                'account_type' => 'student'
            ],
            [
                'email' => 'kjcrapal@nemsu.edu.ph',
                'name' => 'Khezar Jhon C. Rapal',
                'status' => 'active',
                'account_type' => 'student'
            ],

        ]);
    }
}
