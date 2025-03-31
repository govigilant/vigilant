<?php

namespace Vigilant\Users\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_if(! in_array($provider, ['google']), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, CreatesNewUsers $creator): RedirectResponse
    {
        abort_if(! in_array($provider, ['google']), 404);

        $socialiteUser = Socialite::driver($provider)->user();

        $user = $creator->create([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'password' => str()->random(32),
        ], false);

        Auth::login($user);

        return response()->redirectToRoute('sites');
    }
}
