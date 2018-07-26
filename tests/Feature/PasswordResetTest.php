<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_visit_page_password_email()
    {
        $this->get('/password/reset/efd886f6a2117dbaebd504e0d3ceac3f3c58a4458933cb88cb5ffe4e2217474e')
            ->assertStatus(200)
            ->assertSee("Reset Password");
    }

    /** @test */
    public function a_user_can_reset_his_password()
    {
        $user = create('App\User', ["email" => "zaratedev@gmail.com"]);

        $this->from('/password/reset')->post("/password/email", [
            "email" => "zaratedev@gmail.com"
        ]);



        $response = $this->post('/password/reset', [
            "token" => "",
            "email" => "zaratedev@gmail.com",
            "password" => "123456",
            "password" => "123456"
        ]);
    }
}
