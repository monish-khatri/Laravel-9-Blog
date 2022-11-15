<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhpInfoController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('ensureToken');
        /*
            Controllers also allow us to register middleware using a closure.
            This provides a convenient way to define an inline middleware for a
            single controller without defining an entire middleware class
        */
        // $this->middleware(function ($request, $next) {
        //     return $next($request);
        // });
    }

    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return phpinfo();
    }
}
