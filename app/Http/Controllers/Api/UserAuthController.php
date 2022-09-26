<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;

class UserAuthController extends Controller
{
    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return array
     */
    public function register(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        $data['password'] = bcrypt($request->password);

        $user = User::create($data);
        $accessToken = $user->createToken('API Token')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);
    }

    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return array
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Email/Password is Incorrect.Please try again']);
        }

        $accessToken = auth()->user()->createToken('API Token')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}
