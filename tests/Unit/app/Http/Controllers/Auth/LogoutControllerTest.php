<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use function route;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->route = route('auth.logout');
    }

    /**
     * Verifies that when not authenticated, the user is redirected back to the login
     * page
     *
     * @return void
     */
    public function testLogoutController_whenNotAuthenticated_shouldRedirectToLoginPage()
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('auth.login'));
        $this->assertNull(Auth::user());
    }

    /**
     * Verifies that when logging out, the authenticated user is cleared.
     *
     * @return void
     */
    public function testLogoutController_shouldClearAuthenticatedUser()
    {
        $user = User::factory()->create();

        $this->be($user);
        $this->get($this->route);

        $this->assertNull(Auth::user());
    }

    /**
     * Verifies that when logging out, the user is redirected back to the login page.
     *
     * @return void
     */
    public function testLogoutController_shouldRedirectUserBackToLoginPage()
    {
        $user = User::factory()->create();

        $this->be($user);
        $response = $this->get($this->route);

        $response->assertRedirect(route('auth.login'));
    }
}
