<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

// Basic Routing
Route::get('/greeting', function () {
    return 'Hello World';
});

// The Default Route Files
Route::get('/user', [UserController::class, 'index']);

// Available Router Methods
/*
Route::get($uri, $callback);
Route::post($uri, $callback);
Route::put($uri, $callback);
Route::patch($uri, $callback);
Route::delete($uri, $callback);
Route::options($uri, $callback);
*/


/* Route::match(['get', 'post'], '/user', function () {
    //
});
Route::any('/user', function () {
    //
}); */

// Dependency Injection
Route::get('/users', function (Request $request) {
    dd($request);
});

// Redirect Routes

// It will show 302 code by default. but we can change the status code by passing third parameter
Route::redirect('/redirect', '/user',301);
Route::permanentRedirect('/permanent-redirect', '/user');


// View Routes
// Route::view('/view', 'view');

Route::view('/view', 'view', ['name' => 'Messi']);

// Route Parameters
    // Required Parameters
    /* Route::get('/params/{id}', function ($id) {
        return 'ID '.$id;
    }); */

    // Parameters & Dependency Injection
    Route::get('/params/{id}', function (Request $request, $id) {
        return 'ID: '.$id;
    })->where('id', '[0-9]+'); // Regular Expression Constraints

    // Optional Parameters
    Route::get('/params/{name?}', function ($name = 'No Name') {
        return 'NAME: '.$name;
    })->where('name', '[A-Za-z]+');

    /* For convenience, some commonly used regular expression patterns have helper 
    methods that allow you to quickly add pattern constraints to your routes:*/
    // whereNumber('id')
    // whereAlpha('name');
    // whereAlphaNumeric('name')
    // whereUuid('id')
    // whereIn('category', ['movie', 'song', 'painting'])
