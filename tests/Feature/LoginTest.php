<?php

namespace Tests\Feature;

use Laravel\Socialite\Two\GithubProvider;
use Mockery as m;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    /** @test */
    function login_requests_are_send_to_github()
    {
        $message = 'Redirecting to GitHub...';

        $provider = m::mock(GithubProvider::class);

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        $provider->shouldReceive('redirect')->andReturn($message);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSee($message);
    }
}
