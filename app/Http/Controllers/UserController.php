<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function updateLanguage(Request $request,$language)
    {
        $id = Auth::id();
        $users = User::findOrFail($id);
        $users->language = $language;
        $users->save();
        return redirect()->back();
    }
}
