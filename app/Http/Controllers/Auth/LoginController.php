<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * Shows the login page to the user where a button will be displayed. Once clicked,
 * the user will then be redirected to the /auth/redirect controller where the user
 * will be redirected once again but this time, to the Flickr authentication page.
 *
 * @package App\Http\Controllers\Auth
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LoginController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function __invoke()
    {
        return view('auth.login');
    }
}
