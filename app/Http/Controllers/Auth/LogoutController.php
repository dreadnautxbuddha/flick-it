<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use function redirect;
use function route;

/**
 * Logs the user out by destroying the current session, then redirects the user back
 * to the login page.
 *
 * @package App\Http\Controllers\Auth
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogoutController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function __invoke()
    {
        Auth::logout();

        return redirect(route('auth.login'));
    }
}
