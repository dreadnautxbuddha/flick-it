<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use function env;
use function route;

class LoginControllerTest extends TestCase
{
    /**
     * @var \GuzzleHttp\Promise\PromiseInterface
     */
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->response = Http::response('oauth_callback_confirmed=true&oauth_token=72157720855855640-e4ad2b8ba40d9e85&oauth_token_secret=df3da781e8ca7be2');
    }

    /**
     * Verifies that the user will be redirected to the homepage when accessing the
     * login page while authenticated.
     *
     * @return void
     */
    public function testLoginController_whenAlreadyAuthenticated_shouldRedirectToHomepage()
    {
        $user = User::factory()->create();

        $this->be($user);
        $request = $this->get(route('auth.login'));

        $request->assertRedirect(route('home'));
    }

    /**
     * Verifies that the user will be shown a button where when clicked, the user
     * will be redirected to the /auth/redirect route.
     *
     * @return void
     */
    public function testLoginController_whenNotAuthenticated_shouldShowLoginPageWithRedirectButton()
    {
        $request = $this->get(route('auth.login'));

        $request->assertSee(
            sprintf('<a class="btn btn-primary col-md-3" href="%s" role="button">Try me!</a>', route('auth.redirect')),
            false
        );
    }
}
