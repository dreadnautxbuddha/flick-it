<?php

namespace Tests\Support\Concerns;

use Mockery;
use function array_merge;

trait MocksSocialite
{
    /**
     * Returns a mocked \Laravel\Socialite\One\User object with a few default values
     * for the shouldReceive() method
     *
     * @param array $defaultShouldReceives
     * @param string|null $token
     * @param string|null $tokenSecret
     *
     * @return \Laravel\Socialite\One\User|\Laravel\Socialite\One\User&\Mockery\LegacyMockInterface|\Laravel\Socialite\One\User&\Mockery\MockInterface|\Mockery\LegacyMockInterface|\Mockery\MockInterface|Laravel\Socialite\One\User|Laravel\Socialite\One\User&\Mockery\LegacyMockInterface|Laravel\Socialite\One\User&\Mockery\MockInterface
     */
    protected function getMockedOneUser(
        array $defaultShouldReceives = [],
        ?string $token = null,
        ?string $tokenSecret = null
    ) {
        $mock = Mockery::mock('Laravel\Socialite\One\User');
        $mock->token = $token ?? 'token';
        $mock->tokenSecret = $tokenSecret ?? 'token-secret';
        $mock->shouldReceive(
            array_merge(
                [
                    'getId' => 'id',
                    'getName' => 'name',
                    'getNickname' => 'nickname',
                    'getEmail' => 'email',
                ],
                $defaultShouldReceives
            )
        );

        return $mock;
    }
}
