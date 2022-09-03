<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * @package App\Http\Controllers\Auth
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LoginController extends Controller
{
    /**
     * Shows the user the login page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('authentication.login');
    }
}
