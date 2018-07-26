<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function it_visit_page_of_register()
    {
        $this->get('/register')
            ->assertSee('Register');
    }

    /** @test */
    public function user_can_registered_in_the_site_web()
    {
        $response = $this->post('/register', [
            'name'                   => 'Jonathan',
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/home');
    }
}
