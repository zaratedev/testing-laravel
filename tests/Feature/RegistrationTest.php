<?php

namespace Tests\Feature;

use App\Mail\ConfirmedYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
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
    public function cannot_view_registration_form_when_authenticated()
    {
        $user = create('App\User');
        $this->signIn($user);
        $response = $this->get('/register');
        $response->assertRedirect('/home');
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

        $this->assertCredentials([
            'name'                   => 'Jonathan',
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);
    }

    /** @test */
    public function the_name_is_required()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => null,
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
                    ->assertSessionHasErrors([
                        'name' => 'The name field is required.',
                    ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_name_not_is_string()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => 123443,
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                'name' => 'The name must be a string.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_name_has_more_of_255_characters()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => "Lorem ipsum dolor sit amet consectetur adipiscing elit sapien, aenean suspendisse mattis volutpat sollicitudin condimentum hendrerit praesent, montes nec tempor habitant blandit id class. Sem mollis semper fames risus torquent maecenas, in bibendum litora justo pellentesque porta, vel montes molestie nascetur ligula.",
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                'name' => 'The name may not be greater than 255 characters.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_last_name_is_required()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => null,
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                'last_name' => 'The last name field is required.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_last_name_not_is_string()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  =>  'Jonathan',
            'last_name'              => 23486432986,
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                'last_name' => 'The last name must be a string.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_last_name_has_more_of_255_characters()
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => "Jonathan",
            'last_name'              => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sapien, aenean suspendisse mattis volutpat sollicitudin condimentum hendrerit praesent, montes nec tempor habitant blandit id class. Sem mollis semper fames risus torquent maecenas, in bibendum litora justo pellentesque porta, vel montes molestie nascetur ligula.',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                'last_name' => 'The last name may not be greater than 255 characters.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_email_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => null,
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors(["email" => "The email field is required."]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_email_not_is_string()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 0000000000000000,
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "email" => "The email must be a string."
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_email_has_format_email()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'email@@mail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "email" => "The email must be a valid email address."
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'zaratedev@gmail.com'
        ]);
    }

    /** @test */
    public function the_email_is_unique()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => $user->email,
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "email" => "The email has already been taken."
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);
    }

    /** @test */
    public function the_password_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => null,
            'password_confirmation'  => null,
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "password" => "The password field is required."
            ]);

        $this->assertDatabaseMissing('users', [
           "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function the_password_is_string()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => 123456,
            'password_confirmation'  => null,
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "password" => "The password must be a string."
            ]);

        $this->assertDatabaseMissing('users', [
            "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function the_password_has_how_minimum_six_characters()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => "123",
            'password_confirmation'  => "123",
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "password" => "The password must be at least 6 characters."
            ]);

        $this->assertDatabaseMissing('users', [
            "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function the_password_is_confirmed()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => "123456",
            'password_confirmation'  => "1234567890",
            'job'                    => 'developer',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "password" => "The password confirmation does not match."
            ]);

        $this->assertDatabaseMissing('users', [
            "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function the_job_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => "123456",
            'password_confirmation'  => "1234567890",
            'job'                    => null,
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "job" => "The job field is required."
            ]);

        $this->assertDatabaseMissing('users', [
            "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function the_job_is_string()
    {
        $this->withExceptionHandling();

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => "123456",
            'password_confirmation'  => "1234567890",
            'job'                    => 134567890,
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors([
                "job" => "The job must be a string."
            ]);

        $this->assertDatabaseMissing('users', [
            "email" => "zaratedev@gmail.com"
        ]);
    }

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        event(new Registered(create('App\User')));

        Mail::assertSent(ConfirmedYourEmail::class);
    }

    /** @test */
    public function user_can_fully_confirm_their_email_address()
    {
        $this->from('/register')->post('/register', [
            'name'                  => 'Jonathan',
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '123456',
            'password_confirmation'  => '123456',
            'job'                    => 'developer',
        ]);

        $user = User::whereName('Jonathan')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);


        // Let the user confirmed their account
        $this->get('/register/confirm?token='. $user->confirmation_token);
        $this->assertTrue($user->fresh()->confirmed);
    }

}
