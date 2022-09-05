<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use function redirect;
use function route;

/**
 * Receives a call when a user approves our application's access to the user's
 * information.
 *
 * The following query string parameters will be received:
 * 1. `oauth_token` - This is a part of the request token API response.
 * 2. `oauth_verifier` - This one is for the security checks. This acts like a
 * "private key" to our "public key" which is the `oath_token` and its secret.
 *
 * @package App\Http\Controllers\Auth
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class CallbackController extends Controller
{
    /**
     * @todo Maybe we don't have to save the user in the database? (see GenericUser)
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function __invoke(Request $request)
    {
        $flickrUser = Socialite::driver('flickr')->user();
        $user = User::findByFlickrId($flickrUser->getId())->first() ?? new User;

        $user->fill([
            'flickr_id' => $flickrUser->getId(),
            'name' => $flickrUser->getName(),
            'nickname' => $flickrUser->getNickname(),
            'email' => $flickrUser->getEmail(),
            'flickr_token' => $flickrUser->token,
            'flickr_refresh_token' => $flickrUser->tokenSecret,
        ]);
        $user->save();

        Auth::login($user);

        return redirect(route('home'));
    }
}
