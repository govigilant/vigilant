<?php

namespace Vigilant\Users\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Vigilant\Users\Actions\Fortify\CreateNewUser;
use Vigilant\Users\Models\User;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_if(! in_array($provider, ['google']), 404);

        return Socialite::driver($provider)->redirect(); // @phpstan-ignore-line
    }

    public function callback(string $provider, CreateNewUser $creator): RedirectResponse
    {
        abort_if(! in_array($provider, ['google']), 404);

        $socialiteUser = Socialite::driver($provider)->user(); // @phpstan-ignore-line

        $user = User::query()
            ->where('email', '=', $socialiteUser->getEmail())
            ->first();

        if ($user === null) {
            $user = $creator->create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'password' => str()->random(32),
            ], ['terms', 'password']);
        }

        Auth::login($user);

        return response()->redirectToRoute('sites');
    }
}
