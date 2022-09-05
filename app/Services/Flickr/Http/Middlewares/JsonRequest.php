<?php

namespace App\Services\Flickr\Http\Middlewares;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * A GuzzleHttp middleware that adds query strings to the request so that Flickr may
 * understand that we're requesting for a JSON response.
 *
 * @package App\Services\Flickr\Http\Middlewares
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class JsonRequest
{
    /**
     * @var array
     */
    protected $options = [
        'format' => 'json',
        'nojsoncallback' => 1,
    ];

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            foreach ($this->options as $default => $value) {
                $uriWithQueryStrings = Uri::withQueryValue(
                    $request->getUri(),
                    $default,
                    $value
                );

                $request = $request->withUri($uriWithQueryStrings);
            }

            return $handler($request, $options);
        };
    }
}
