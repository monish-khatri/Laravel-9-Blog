<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    const BR = '<br>';
    /**
     * Create Response
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $request->session()->get('name');
        // dd($request->session()->get('name','Messi'));

        // Retrieving All Session Data
        // dd($request->session()->all());

        // Determining If An Item Exists In The Session
        // if ($request->session()->has('name')) {
        //     echo self::BR . ($request->session()->get('name'));
        // } else {
        //     echo self::BR . ('name not found in session');
        // }
        // if ($request->session()->missing('names')) {
        //     echo self::BR . ('Missing names');
        // }

        // Storing Data
        // Via a request instance...
        // $request->session()->put('logged', true);

        // Via the global "session" helper...
        // session(['logged' => true]);

        // Pushing To Array Session Values
        // $request->session()->push('user.teams', 'developers');

        // Retrieving & Deleting An Item
        // dd($request->session()->pull('name', 'default'),$request->session()->all());

        // Incrementing & Decrementing Session Values
        // $request->session()->increment('count',$incrementBy = 2);
        // $request->session()->decrement('count',$decrementBy = 2);
        
        // Flash Data
        // $request->session()->flash('status', 'Task was successful!');
        // $request->session()->reflash();
        // $request->session()->keep(['username', 'email']);
        // $request->session()->now('status', 'Task was successful!');
        
        // Deleting Data
        // Forget a single key...
        // $request->session()->forget('name');

        // Forget multiple keys...
        // $request->session()->forget(['name', 'key']);

        // Delete all session
        // $request->session()->flush();

        // Regenerating The Session ID
        $request->session()->regenerate();
        // $request->session()->invalidate();


        dd($request->session()->all());
    }
}
