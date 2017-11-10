<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToGitHub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGitHubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $appUser = User::firstOrCreate([
            'email' => $githubUser->getEmail()
        ], [
            'name' => $githubUser->getName()
        ]);

        auth()->login($appUser);

        return redirect('/');
    }
}
