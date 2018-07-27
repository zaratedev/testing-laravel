<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

        $response = $this->post('/password/reset', [
            "token" => Password::broker()->createToken($user),
            "email" => "zaratedev@gmail.com",
            "password" => "123456",
            "password_confirmation" => "123456"
        ]);

        $response->assertRedirect('/home');
    }

    /** @test */
    public function a_user_cannot_view_password_reset_form_when_authenticated()
    {
        $user = create('App\User');

        $this->signIn($user);

        $response = $this->get('/password/reset/' . Password::broker()->createToken($user));

        $response->assertRedirect('/home');
    }

    /** @test */
    public function a_user_cannot_reset_password_with_invalid_token()
    {
        $user = create('App\User',[
            'password' => bcrypt('123456'),
        ]);

        $response = $this->from('/password/reset/token-invalid')->post('/password/reset', [
            'token' => 'token-invalid',
            'email' => $user->email,
            'password' => 'qwerty',
            'password_confirmation' => 'qwerty',
        ]);

        $response->assertRedirect('/password/reset/token-invalid');
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('123456', $user->fresh()->password));
        $this->assertGuest();
    }

    /** @test */
    public function a_user_cannot_reset_password_without_confirmation_password()
    {
        $user = create('App\User',[
            'password' => bcrypt('123456'),
        ]);

        $response = $this->from('/password/reset/' . $token = Password::broker()->createToken($user))->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/password/reset/' . $token);
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('123456', $user->fresh()->password));
        $this->assertGuest();
    }

    /** @test */
    public function a_user_cannot_reset_password_without_email()
    {
        $user = create('App\User',[
            'password' => bcrypt('123456'),
        ]);

        $response = $this->from('/password/reset/' . $token = Password::broker()->createToken($user))->post('/password/reset', [
            'token' => $token,
            'email' => '',
            'password' => 'qwerty',
            'password_confirmation' => 'qwerty',
        ]);

        $response->assertRedirect('/password/reset/' . $token);
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('123456', $user->fresh()->password));
        $this->assertGuest();
    }
}
