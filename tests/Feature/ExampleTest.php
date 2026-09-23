<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_guests_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_logout_route_is_not_blocked_by_an_expired_csrf_token(): void
    {
        $middleware = app('router')->getRoutes()->getByName('logout')->gatherMiddleware();

        $this->assertNotContains(ValidateCsrfToken::class, $middleware);
    }
}
