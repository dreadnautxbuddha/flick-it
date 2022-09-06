<?php

use App\Http\Controllers\Auth\CallbackController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\GalleriesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'auth', 'middleware' => ['guest']], function () {
    Route::get('/login', LoginController::class)->name('auth.login');
    Route::get('/redirect', RedirectController::class)->name('auth.redirect');
    Route::get('/callback', CallbackController::class)->name('auth.callback');

    Route::get('/logout', LogoutController::class)
        ->name('auth.logout')
        ->withoutMiddleware('guest');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () { return view('gallery'); })->name('home');
    Route::get('gallery', GalleriesController::class)->name('gallery');
});
