<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::truncate();

        $json = File::get("country-state-cities/countries.json");
        $countries = json_decode($json);

        foreach ($countries as $key => $value) {
            Country::create([
                "id" => $value->id,
                "name" => $value->name,
                "iso3" => $value->iso3,
                "iso2" => $value->iso2,
                "phone_code" => $value->phone_code,
            ]);
        }
    }
}