<?php

use App\Http\Controllers\PhpInfoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SessionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;


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
    return Redirect::route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/response/{user}', [ResponseController::class, 'index'])->name('response');

    Route::resource('blogs', BlogController::class)->missing(function (Request $request) {
        return Redirect::route('blogs.index');
    })->parameters(['blogs' => 'blog']);
});
// Basic Routing
Route::get('/greeting', function () {
    return 'Hello World';
});

// The Default Route Files
Route::get('/user', [UserController::class, 'index'])->middleware('ensureToken');
Route::get('/user-role/{id}', [UserController::class, 'show'])->middleware('ensureRole:super-admin,admin');
Route::get('/user/{id}/profile', [UserController::class, 'profile'])->withoutMiddleware('ensureToken');
Route::get('/user/profile',function(){
    return view('welcome');
})->name('profile');

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
// Route::get('/user/{id}/profile',[UserController::class, 'profile'])->name('profile');

// Route Groups
    // Middleware: give middleware to multiple routes at once
    Route::middleware(['first', 'second'])->group(function () {
        Route::get('/user', function () {
            // Uses first & second middleware...
        });
        // Route::get('/user/profile', function () {
        //     // Uses first & second middleware...
        // });
    });

    // Controller: give controller to multiple routes at once
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}', 'show')->name('show');
        // Route::get('/user/{id}/profile', 'profile')->name('profile');
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
    return view('errors.404');
});

// Form Method Spoofing
/*
    HTML forms do not support PUT, PATCH, or DELETE actions.
    when defining PUT, PATCH, or DELETE routes that are called
    from an HTML form, you will need to add a hidden _method field to the form
    1) <input type="hidden" name="_method" value="PUT">
    2) @method('PUT')  // Blade directive
*/

// CSRF Protection
Route::get('/token', function (Request $request) {
    // $token1 = $request->session()->token();
    // $token2 = csrf_token();
    // dd($token1,$token2);
});

/*
Anytime you define a "POST", "PUT", "PATCH", or "DELETE" HTML form in your application,
you should include a hidden CSRF _token field in the form
<form method="POST" action="/profile">
    @csrf
    <!-- Equivalent to... -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
</form>
*/

// Single Action Controllers
Route::get('/server-info', PhpInfoController::class)->withoutMiddleware('auth');

// Route group for BlogsController
// Route::controller(BlogController::class)->group(function () {
//     Route::get('/blogs', 'index')->name('blog.index'); //Display all blogs
//     Route::get('/blogs/show/{id}', 'show')->name('blog.view'); //Display blog by blog id
//     Route::any('/blogs/add', 'add')->name('blog.add'); //Create blog
//     Route::any('/blogs/edit/{id}', 'edit')->name('blog.edit'); //Edit blog by blog id
//     Route::any('/blogs/delete/{id}', 'delete')->name('blog.delete'); //Delete blog by blog id
// });

Route::get('/views', function () {
    // Passing Data To Views
    // return view('test_views.first_view', ['name' => 'Messi']);
    return view('test_views.first_view')->with('occupation', 'Footballer');

    // Views may also be returned using the View facade:
    // return View::make('test_views.first_view', ['name' => 'Messi']);

    // Creating The First Available View
    // return View::first(['test_views.admin', 'test_views.first_view'], ['name' => 'Messi-10']);

    // Determining If A View Exists
    // if (View::exists('test_views.admin')) {
    //     return View::make('test_views.first_view', ['name' => 'Monish']);
    // } else {
    //     return View::make('test_views.first_view', ['name' => 'Messi']);
    // }
});

Route::get('/url-generation',function(){
    // Generating URLs
    $blog = App\Models\Blog::find(30);
    // echo '<br>'. url("/blogs/{$blog->id}");

    // Accessing The Current URL
    // Get the current URL without the query string...
    // echo '<br>'. url()->current();

    // Get the current URL including the query string...
    // echo '<br>'. url()->full();

    // Get the full URL for the previous request...
    // echo '<br>'. url()->previous();

    // URLs For Named Routes
    // echo '<br>'. route('blogs.show', ['blog' => $blog]);

    // Signed URLs
    // return URL::signedRoute('blogs.show', ['blog' => $blog]);

    // temporarySignedRoute
    return URL::temporarySignedRoute('blogs.show', now()->addMinutes(30), ['blog' => $blog]);

    // Validating Signed Route Requests
    // /if (! $request->hasValidSignature()) {
    //     abort(401);
    // }
    // if (! $request->hasValidSignatureWhileIgnoring(['page', 'order'])) {
        // abort(401);
    // }
});

Route::get('/session',function (Request $request) {
    // Retrieve a piece of data from the session...
    // $value = session('key');

    // Specifying a default value...
    // $value = session('key', 'default');

    // Store a piece of data in the session...
    // session(['name' => 'Monish The Great']);

    $sessionObject = new SessionController();
    return $sessionObject->index($request);
});
require __DIR__.'/auth.php';
