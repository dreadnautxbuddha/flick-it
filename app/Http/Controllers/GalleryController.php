<?php

namespace App\Http\Controllers;

/**
 * @package App\Http\Controllers
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class GalleryController extends Controller
{
    /**
     * Displays the users' galleries and those corresponding galleries' photos
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('gallery');
    }
}
