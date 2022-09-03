<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

/**
 * Redirects the user to the Flickr OAuth 1.0 redirection page where the user will be
 * asked whether our application will be allowed access to his/her information.
 *
 * The URL looks like this:
 * {@link https://www.flickr.com/services/oauth/authorize?oauth_token=72157720855836011-98b38e2b52753f0e}
 * The value of `oauth_token` is a response from an API request made by Socialite to
 * {@link https://www.flickr.com/services/oauth/request_token} and is given the
 * following information:
 * 1. api key
 * 2. api secret
 * 3. callback url
 *
 * Find out more about the authentication flow {@see https://www.flickr.com/services/api/auth.oauth.html#request_token here.}
 *
 * @package App\Http\Controllers\Auth
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class RedirectController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke()
    {
        return Socialite::driver('flickr')->redirect();
    }
}
