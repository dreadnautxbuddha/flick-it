<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tests\Support\Concerns\MocksSocialite;
use Tests\TestCase;
use function route;

class CallbackControllerTest extends TestCase
{
    use RefreshDatabase;
    use MocksSocialite;

    /**
     * @var string
     */
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->route = route('auth.callback');
    }

    /**
     * Verifies that when already authenticated, the user is redirected back to the
     * homepage, and its attributes is not changed.
     *
     * @return void
     */
    public function testCallbackController_whenAlreadyAuthenticated_shouldRedirectToHomepage()
    {
        $user = User::factory()->create();
        $userMock = $this->getMockedOneUser(['getId' => $user->flickr_id]);
        Socialite::shouldReceive('driver->user')->andReturn($userMock);

        $this->be($user);
        $response = $this->get(
            "{$this->route}?oauth_token=72157720855916832-399c4f475368235e&oauth_verifier=2354699563309718"
        );
        $freshUser = User::find($user->id)->fresh();

        $response->assertRedirect(route('home'));
        $this->assertEquals($user->flickr_id, $freshUser->flickr_id);
        $this->assertEquals($user->name, $freshUser->name);
        $this->assertEquals($user->nickname, $freshUser->nickname);
        $this->assertEquals($user->email, $freshUser->email);
        $this->assertEquals($user->flickr_token, $freshUser->flickr_token);
        $this->assertEquals(
            $user->flickr_refresh_token,
            $freshUser->flickr_refresh_token
        );
    }

    /**
     * Verifies that an authenticated user should be set when the user is not yet
     * logged in
     *
     * @return void
     */
    public function testCallbackController_shouldSetAuthenticatedUser()
    {
        Socialite::shouldReceive('driver->user')->andReturn(
            $this->getMockedOneUser(['getId' => 'a-new-id'])
        );

        $this->get(
            "{$this->route}?oauth_token=72157720855916832-399c4f475368235e&oauth_verifier=2354699563309718"
        );

        $this->assertNotNull(Auth::user());
    }

    /**
     * Verifies that a new user will be created if the flickr user id that we got do
     * not match any users.
     *
     * @return void
     */
    public function testCallbackController_whenUserDoesNotExist_shouldCreate()
    {
        $user = $this->getMockedOneUser(
            [
                'getId' => 'flickr id',
                'getName' => 'flickr name',
                'getNickname' => 'flickr nickname',
                'getEmail' => 'flickr email',
            ], 'flickr-token',
            'flickr-token-secret'
        );
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->get(
            "{$this->route}?oauth_token=72157720855916832-399c4f475368235e&oauth_verifier=2354699563309718"
        );

        $user = Auth::user();
        $this->assertEquals('flickr id', $user->flickr_id);
        $this->assertEquals('flickr name', $user->name);
        $this->assertEquals('flickr nickname', $user->nickname);
        $this->assertEquals('flickr email', $user->email);
        $this->assertEquals('flickr-token', $user->flickr_token);
        $this->assertEquals('flickr-token-secret', $user->flickr_refresh_token);
    }

    /**
     * Verifies that an existing user will be updated if the flickr user id matches
     * one of our users in the database.
     *
     * @return void
     */
    public function testCallbackController_whenUserExists_shouldUpdate()
    {
        $existingUser = User::factory()->create([
            'flickr_id' => 'old-flickr-id',
            'name' => 'old-flickr-name',
            'nickname' => 'old-flickr-nickname',
            'email' => 'old-flickr-email',
            'flickr_token' => 'old-flickr-token',
            'flickr_refresh_token' => 'old-flickr-refresh_token',
        ]);
        $user = $this->getMockedOneUser(
            [
                'getId' => $existingUser->flickr_id,
                'getName' => 'new-flickr name',
                'getNickname' => 'new-flickr nickname',
                'getEmail' => 'new-flickr email',
            ],
            'new-flickr-token',
            'new-flickr-token-secret'
        );
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->get(
            "{$this->route}?oauth_token=72157720855916832-399c4f475368235e&oauth_verifier=2354699563309718"
        );

        $existingUser = Auth::user();
        $this->assertEquals('old-flickr-id', $existingUser->flickr_id);
        $this->assertEquals('new-flickr name', $existingUser->name);
        $this->assertEquals('new-flickr nickname', $existingUser->nickname);
        $this->assertEquals('new-flickr email', $existingUser->email);
        $this->assertEquals('new-flickr-token', $existingUser->flickr_token);
        $this->assertEquals(
            'new-flickr-token-secret',
            $existingUser->flickr_refresh_token
        );
    }

    /**
     * Verifies that the user will be redirected to the homepage after authentication
     *
     * @return void
     */
    public function testCallbackController_shouldRedirectToHomepage()
    {
        Socialite::shouldReceive('driver->user')->andReturn(
            $this->getMockedOneUser()
        );

        $response = $this->get(
            "{$this->route}?oauth_token=72157720855916832-399c4f475368235e&oauth_verifier=2354699563309718"
        );

        $response->assertRedirect(route('home'));
    }
}
