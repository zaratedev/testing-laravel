<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function it_visit_page_of_login()
    {
        $this->get('/login')
            ->assertSee('Login');
    }

    /** @test */
    public function authenticated_to_a_user()
    {
        $user = create('App\User', [
            "email" => "user@mail.com"
        ]);

        $this->get('/login')->assertSee('Login');

        $credentials = [
            "email" => "user@mail.com",
            "password" => "secret"
        ];

        $response = $this->post('/login', $credentials);

        $response->assertRedirect('/home');

        $this->assertCredentials($credentials);
    }

    /** @test */
    public function not_authenticate_to_a_user_with_credentials_invalid()
    {
        $user = create('App\User', [
            "email" => "user@mail.com"
        ]);

        $credentials = [
            "email" => "users@mail.com",
            "password" => "secret"
        ];

        $this->assertInvalidCredentials($credentials);
    }

    /** @test */
    public function the_email_is_required_for_authenticate()
    {
        $user = create('App\User');

        $credentials = [
            "email" => null,
            "password" => "secret"
        ];

        $response = $this->from('/login')->post('/login', $credentials);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'The email field is required.',
            ]);
    }

    /** @test */
    public function the_password_is_required_for_authenticate()
    {
        $user = create('App\User', ['email' => 'zaratedev@gmail.com']);

        $credentials = [
            "email" => "zaratedev@gmail.com",
            "password" => null
        ];

        $response = $this->from('/login')->post('/login', $credentials);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'password' => 'The password field is required.',
            ]);
    }
}
