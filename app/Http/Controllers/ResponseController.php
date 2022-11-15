<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\UserController;


class ResponseController extends Controller
{
    /**
     * Create Response
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Http\Response  $response
     * @param App\Models\User  $user
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Response $response,User $user)
    {
        // Attaching Headers To Responses
        // return response('Hello World', 200)
        //           ->header('Content-Type', 'text/plain');

        // Attaching Cookies To Responses
        $minutes = '1';
        // The queue method accepts the arguments needed to create a cookie instance.
        // These cookies will be attached to the outgoing response before it is sent to the browser:
        Cookie::queue('monish-cookie', 'monish', $minutes);

        // Generating Cookie Instances
        $cookie = cookie('monish-cookie-3', 'monish', $minutes);

        // Expiring Cookies Early
        Cookie::expire('monish-cookie-3');

        // Redirecting To Named Routes
        // return redirect()->route('blogs.index');
        // return redirect()->route('blogs.show', ['blog' => 29]);

        // Redirecting To Controller Actions
        // return redirect()->action([UserController::class, 'show'],['id' => 1]);

        // Redirecting To External Domains
        // return redirect()->away('https://www.google.com');

        // Redirecting With Flashed Session Data
        // return redirect()->route('blogs.index')->with('status', 'Hello there!');

        // View Responses
        // return response()
        //     ->view('hello', ['name'=>'monish'], 200)
        //     ->header('Content-Type', 'text/plain');

        // JSON Responses
        // return response()->json([
        //     'name' => 'Messi',
        //     'number' => '10',
        //     'number' => 'PSG',
        // ]);

        // File Downloads
        // $pathToFile = '/home/monish.khatri/Downloads/RandomUser/3.jpg';
        // $name = 'custom_name.html';
        // return response()->download($pathToFile,$name);

        // File Responses
        // return response()->file($pathToFile);

        return response()->caps($user)->cookie(
            'monish-cookie-2', 'monish', $minutes
        )->cookie($cookie)->withoutCookie('monish-cookie-2');
    }
}
