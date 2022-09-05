<?php

use App\Http\Controllers\Auth\CallbackController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RedirectController;
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

Route::group(['prefix' => 'auth', 'middleware' => ['guest']], function ($group) {
    Route::get('/login', LoginController::class)->name('auth.login');
    Route::get('/redirect', RedirectController::class)->name('auth.redirect');
    Route::get('/callback', CallbackController::class)->name('auth.callback');

    Route::get('/logout', LogoutController::class)
        ->name('auth.logout')
        ->withoutMiddleware('guest');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', 'App\Http\Controllers\GalleryController@index')->name('home');
});
