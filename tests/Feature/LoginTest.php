<?php

namespace Tests\Feature;

use App\User;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery as m;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $email = 'duilio@styde.net';
    private $name = 'Duilio Palacios';

    /** @test */
    function login_requests_are_send_to_github()
    {
        $message = 'Redirecting to GitHub...';

        $this->mockGithubProvider()
            ->shouldReceive('redirect')
            ->andReturn($message);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSee($message);
    }

    /** @test */
    function new_users_authorized_by_github_are_registered_and_authenticated()
    {
        // Given...
        $this->mockGithubUser();

        // When
        $response = $this->get('/login/callback');

        // Then
        $this->assertDatabaseHas('users', [
            'email' => $this->email,
            'name' => $this->name,
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect('/');
    }

    /** @test */
    function known_users_authorized_by_github_are_authenticated()
    {
        $this->withoutExceptionHandling();

        // Given...
        factory(User::class)->create(['email' => $this->email]);

        $this->mockGithubUser();

        // When
        $response = $this->get('/login/callback');

        // Then
        $this->assertAuthenticated();

        $response->assertRedirect('/');
    }

    protected function mockGithubUser()
    {
        $githubUser = m::mock(SocialiteUser::class, [
            'getEmail' => $this->email,
            'getName' => $this->name,
        ]);

        $this->mockGithubProvider()
            ->shouldReceive('user')->andReturn($githubUser);
    }

    protected function mockGithubProvider()
    {
        $provider = m::mock(GithubProvider::class);

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        return $provider;
    }
}
