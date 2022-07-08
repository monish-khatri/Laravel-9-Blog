<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return array
     */
    public function index(Request $request)
    {
        dd($request);
    }

    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return array
     */
    public function show(Request $request)
    {
        return redirect()->route('profile',['id'=>$request->id]);
    }
    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
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
