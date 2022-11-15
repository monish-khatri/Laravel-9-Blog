<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google auth interface
     *
     * @return Laravel\Socialite\Facades\Socialite
     */
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create or Update the user based on google login
     *
     * @return Illuminate\Http\Response
     */
    public function callbackFromGoogle()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Check Users Email If Already There
            $alreadyUser = User::where('email', $user->getEmail())->first();
            if(!$alreadyUser){
                $createUpdateUser = User::updateOrCreate([
                    'google_id' => $user->getId(),
                ],[
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => Hash::make($user->getName().'@'.$user->getId()),
                    'email_verified_at' => now(),
                ]);
            }else{
                $createUpdateUser = User::where('email',  $user->getEmail())->update([
                    'google_id' => $user->getId(),
                ]);
                $createUpdateUser = User::where('email', $user->getEmail())->first();
            }

            Auth::loginUsingId($createUpdateUser->id);

            return redirect(RouteServiceProvider::HOME);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
