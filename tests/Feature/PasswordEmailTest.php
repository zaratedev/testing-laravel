<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PasswordEmailTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_visit_page_password_email()
    {
        $this->get('/password/reset')
            ->assertStatus(200)
            ->assertSee("Reset Password");
    }

    /** @test */
    public function a_user_can_reset_password_with_email_address()
    {
        $this->withExceptionHandling();

        $user = create('App\User', ["email" => "zaratedev@gmail.com"]);

        $response = $this->from('/password/reset')->post("/password/email", [
            "email" => "zaratedev@gmail.com"
        ]);

        $response->assertRedirect('/password/reset')
            ->assertSessionHas([
            "status" => "We have e-mailed your password reset link!"
        ]);
    }

    /** @test */
    public function the_email_not_found_for_password_reset()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $response = $this->from('/password/reset')->post("/password/email", [
            "email" => "zaratedev@gmail.com"
        ]);

        $response->assertRedirect('/password/reset')
            ->assertSessionHasErrors([
                "email" => "We can't find a user with that e-mail address."
            ]);
    }

    /** @test */
    public function the_email_is_required_for_password_reset()
    {
        $this->withExceptionHandling();

        $response = $this->from('/password/reset')->post("/password/email", [
            "email" => null
        ]);

        $response->assertRedirect('/password/reset')
            ->assertSessionHasErrors([
                "email" => "The email field is required."
            ]);
    }

    /** @test */
    public function the_email_is_not_valid_for_password_reset()
    {
        $this->withExceptionHandling();

        $response = $this->from('/password/reset')->post("/password/email", [
            "email" => "user@@email.com"
        ]);

        $response->assertRedirect('/password/reset')
            ->assertSessionHasErrors([
                "email" => "The email must be a valid email address."
            ]);
    }
}
