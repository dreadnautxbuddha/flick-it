<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;
use function route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->route = route('auth.redirect');
    }

    /**
     * Verifies that when already authenticated, the user is redirected back to the
     * homepage
     *
     * @return void
     */
    public function testRedirectController_whenAlreadyAuthenticated_shouldRedirectToHomepage()
    {
        $this->be(User::factory()->create());

        $response = $this->get($this->route);

        $response->assertRedirect(route('home'));
    }

    /**
     * Verifies that when not yet authenticated, the user is redirected to a page in
     * Flickr where the user can accept or reject our app's access
     *
     * @return void
     */
    public function testRedirectController_shouldRedirectUserToFlickr()
    {
        $response = $this->get($this->route);

        $response->assertRedirect();
        $this->assertStringStartsWith(
            'https://www.flickr.com/services/oauth/authorize?oauth_token=',
            $response->getTargetUrl()
        );
    }
}
