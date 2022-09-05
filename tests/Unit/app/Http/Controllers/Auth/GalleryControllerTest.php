<?php

namespace Tests\Unit\app\Http\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;
use function route;

class GalleryControllerTest extends TestCase
{
    /**
     * @var string
     */
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->route = route('home');
    }

    /**
     * Verifies that when not authenticated, the user is redirected back to the login
     * page.
     *
     * @return void
     */
    public function testGalleryController_whenNotLoggedIn_shouldRedirectToLoginPage()
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('auth.login'));
    }
}
