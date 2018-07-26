<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');

        $response = $this->get("/profiles/$user->id");

        $response->assertSee($user->name)
            ->assertStatus(200);
    }

    /** @test */
    public function it_authenticated_user_can_view_the_page_for_edit_his_profile()
    {
        $this->signIn();

        $response = $this->get("/profiles/" . auth()->id() . "/edit");

        $response->assertSee("Edit Profile")
            ->assertStatus(200);
    }

    /** @test */
    public function it_authenticated_user_can_edit_his_profile()
    {
        $user = create('App\User');
        $this->signIn($user);

        $response = $this->post("/profiles/update/". auth()->id(), [
            '_token' => csrf_token(),
            'name'                   => 'Jonathan',
            'last_name'              => 'zarate hernandez',
            'email'                  => 'zaratedev@gmail.com',
            'password'               => '',
            'password_confirmation'  => '',
            'job'                    => 'developer',
        ]);

        $response->assertRedirect("/profiles/". $user->id);

        $this->assertEquals($user->fresh()->name, "Jonathan");
    }
}
