<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use function env;
use function route;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

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
