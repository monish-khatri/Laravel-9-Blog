<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();

        $json = File::get("country-state-cities/cities.json");
        $countries = json_decode($json);

        foreach ($countries as $key => $value) {
            City::create([
                "city_id" => $value->id,
                "name" => $value->name,
                "state_id" => $value->state_id,
            ]);
        }
    }
}