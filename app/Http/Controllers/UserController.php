<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Index Method
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return array
     */
    public function index(Request $request)
    {
        dd($request);
    }

    /**
     * Show Method
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return array
     */
    public function show(Request $request)
    {
        return redirect()->route('profile',['id'=>$request->id]);
    }

    /**
     * Profile Method
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return array
     */
    public function profile(Request $request)
    {
        // Accessing The Current Route
        $route = Route::current(); // Illuminate\Routing\Route
        $name = Route::currentRouteName(); // string
        $action = Route::currentRouteAction();
        dd($request->id,$action);

        return redirect()->route('profile');
    }
}
