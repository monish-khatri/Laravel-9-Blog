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
    }); // For `id` :Regular Expression Defined in RouteServiceProvider

    // Optional Parameters
    Route::get('/params/{name?}', function ($name = 'No Name') {
        return 'NAME: '.$name;
    })->where('name', '[A-Za-z]+'); // Regular Expression Constraints

    /* For convenience, some commonly used regular expression patterns have helper
    methods that allow you to quickly add pattern constraints to your routes:*/
    // whereNumber('id')
    // whereAlpha('name');
    // whereAlphaNumeric('name')
    // whereUuid('id')
    // whereIn('category', ['movie', 'song', 'painting'])

    // Encoded Forward Slashes
    Route::get('/search/{search}', function ($search) {
        return $search;
    })->where('search', '.*');


// Named Routes
Route::get('/user/{id}',[UserController::class, 'show'])->name('show');
Route::get('/user/{id}/profile',[UserController::class, 'profile'])->name('profile');

// Route Groups
    // Middleware: give middleware to multiple routes at once
    Route::middleware(['first', 'second'])->group(function () {
        Route::get('/user', function () {
            // Uses first & second middleware...
        });
        Route::get('/user/profile', function () {
            // Uses first & second middleware...
        });
    });

    // Controller: give controller to multiple routes at once
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}', 'show')->name('show');
        Route::get('/user/{id}/profile', 'profile')->name('profile');
    });

    // Subdomain Routing
    Route::domain('laravel.{account}.com')->group(function () {
        Route::get('monish/{id}', function ($account, $id) {
            dd($account,$id);
        });
    });

    // Route Prefixes
    Route::prefix('admin')->group(function () {
        Route::get('/users', function () {
            // Matches The "/admin/users" URL
        });
    });

    // Route Name Prefixes
    Route::name('admin.')->group(function () {
        Route::get('/users', function () {
            // Route assigned name "admin.users"...
        })->name('users');
    });

// Implicit Enum Binding
Route::get('/categories/{category}', function (App\Enums\Category $category) {
    return $category->value;
});

// Fallback Routes
Route::fallback(function () {
    dd("dehk kr url dalna");
});

// Form Method Spoofing
/*
    HTML forms do not support PUT, PATCH, or DELETE actions.
    when defining PUT, PATCH, or DELETE routes that are called
    from an HTML form, you will need to add a hidden _method field to the form
    1) <input type="hidden" name="_method" value="PUT">
    2) @method('PUT')  // Blade directive
*/

