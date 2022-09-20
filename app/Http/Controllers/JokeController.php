<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
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

        $response = Http::retry(3, 100)->acceptJson()->get('https://v2.jokeapi.dev/joke/Programming,Miscellaneous', [
            'type' => 'single',
        ]);
        if($response->ok()) {
            $result = json_decode($response,true);
            return view('joke.joke', [
                'joke' => new HtmlString($result['joke']),
            ]);
        }
        abort($response->status());
   }
}
