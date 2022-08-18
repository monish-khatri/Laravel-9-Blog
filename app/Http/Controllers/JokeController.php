<?php

namespace App\Http\Controllers;

use Illuminate\Support\HtmlString;

class JokeController extends Controller
{
   /**
    * Get Random Jokes
    *
    * @return array
    */
   public function randomJoke()
   {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://v2.jokeapi.dev/joke/Programming,Miscellaneous?type=single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($response,true);
        return view('joke.joke', [
            'joke' => new HtmlString($result['joke']),
        ]);
   }
}
