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

        $response = $this->post('/login', [
            "email" => "user@mail.com",
            "password" => "secret"
        ]);

        $response->assertRedirect('/home');
    }
}
