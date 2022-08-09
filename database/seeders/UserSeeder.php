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
            "name" => 'Administrator',
            "email" => 'admin@admin.com',
            "email_verified_at" => now(),
            "role" => 'admin',
            "password" => bcrypt('admin'),
            "language" => 'en',
        ]);

    }
}
