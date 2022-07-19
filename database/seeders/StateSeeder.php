<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use File;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::truncate();

        $json = File::get("country-state-cities/states.json");
        $countries = json_decode($json);

        foreach ($countries as $key => $value) {
            State::create([
                "id" => $value->id,
                "name" => $value->name,
                "country_id" => $value->country_id,
            ]);
        }
    }
}