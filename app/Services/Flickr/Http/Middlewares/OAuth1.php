<?php

namespace App\Services\Flickr\Http\Middlewares;

/**
 * An OAuth 1.0 middleware that we'll use when authenticating our requests to Flickr
 *
 * @package App\Services\Flickr\Http\Middlewares
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class OAuth1 extends \GuzzleHttp\Subscriber\Oauth\Oauth1
{
    /**
     * @param string $consumerKey
     * @param string $accessToken
     */
    public function __construct(string $consumerKey, string $accessToken)
    {
        parent::__construct([
            'consumer_key' => $consumerKey,
            'token' => $accessToken,
        ]);
    }
}
