<?php

namespace App\Providers;

use App\Services\Flickr\Http\Middlewares\JsonRequest;
use App\Services\Flickr\Http\Middlewares\OAuth1;
use App\Services\Flickr\Repositories\Support\Contracts\GalleryRepository;
use App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use function array_values;
use function env;
use function func_get_arg;

/**
 * @package App\Providers
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class FlickrServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(OAuth1::class, function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            return new OAuth1(env('FLICKR_APP_API_KEY'), $user->flickr_token);
        });

        $this->app->bind(JsonRequest::class, function () {
            return new JsonRequest();
        });

        $this->app->bind(PhotoRepository::class, function () {
            return new \App\Services\Flickr\Repositories\PhotoRepository(
                ...array_values(func_get_arg(1))
            );
        });

        $this->app->bind(GalleryRepository::class, function () {
            return new \App\Services\Flickr\Repositories\GalleryRepository();
        });

        Http::macro(
            'flickr',
            function () {
                return Http::withOptions(['auth' => 'oauth'])
                    ->withMiddleware(app(OAuth1::class))
                    ->withMiddleware(app(JsonRequest::class))
                    ->baseUrl(env('FLICKR_REST_API_URL'));
            }
        );
    }
}
