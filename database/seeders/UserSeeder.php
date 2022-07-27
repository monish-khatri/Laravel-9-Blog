<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create([
            "name" => 'Monish Khatri',
            "email" => 'monish.k@biztechcs.com',
            "password" => bcrypt('monish10'),
            "language" => 'en',
        ]);

    }
}