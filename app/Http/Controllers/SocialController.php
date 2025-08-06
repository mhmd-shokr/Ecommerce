<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
class SocialController extends Controller
{
    public function redirect($parameter){
        return Socialite::driver($parameter)->redirect();
    }
    public function callBack($parameter){
        $user=Socialite::driver($parameter)->user();
        //email exist -> login
        //if not ->create new user
        $dbUser=User::where('email',$user->getEmail())->first();
        if($dbUser){
            Auth::login($dbUser);
        return redirect()->route('home.index');
        }else{
            $newUser=User::create([
                'name'=>$user->getName(),
                'email'=>$user->getEmail(),
                'mobile'=>'',
                'password'=>Hash::make(Str::random(20)),
            ]);
            Auth::login($newUser);
            return redirect()->route('profile.complete');
        }
    }
}
