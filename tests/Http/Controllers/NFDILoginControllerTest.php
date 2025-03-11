<?php

namespace Biigle\Tests\Modules\AuthNFDI\Http\Controllers;

use Biigle\Modules\AuthNFDI\NfdiLoginId;
use Biigle\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Session;
use TestCase;

class NFDILoginControllerTest extends TestCase
{
    public function testRedirect()
    {
        $this->get('auth/nfdi/redirect')
            ->assertRedirectContains('https://login.aai.lifescience-ri.eu');
    }

    public function testCallbackNewUser()
    {
        config(['biigle.user_registration' => true]);
        $user = new SocialiteUser;
        $user->setToken('mytoken');
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->get('auth/nfdi/callback')
            ->assertSessionHas('nfdilogin-token', 'mytoken')
            ->assertRedirectToRoute('nfdi-register-form');
    }

    public function testCallbackNewUserRegistrationDisabled()
    {
        config(['biigle.user_registration' => false]);
        $user = new SocialiteUser;
        $user->setToken('mytoken');
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->get('auth/nfdi/callback')
            ->assertInvalid(['nfdi-id'])
            ->assertRedirectToRoute('login');
    }

    public function testCallbackExistingUser()
    {
        $id = NfdiLoginId::factory()->create();
        $user = new SocialiteUser;
        $user->map(['id' => $id->id]);
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->get('auth/nfdi/callback')->assertRedirectToRoute('home');
        $this->assertAuthenticatedAs($id->user);
    }

    public function testCallbackConnectWithUser()
    {

        $user = new SocialiteUser;
        $user->map(['id' => 'myspecialid']);
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $user = User::factory()->create();
        $this->be($user);
        $this->get('auth/nfdi/callback')->assertRedirectToRoute('settings-authentication');
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(NfdiLoginId::where('user_id', $user->id)->where('id', 'myspecialid')->exists());
    }

    public function testCallbackConnectConflictingIDExists()
    {
        $id = NfdiLoginId::factory()->create();
        $user = new SocialiteUser;
        $user->map(['id' => $id->id]);
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $user = User::factory()->create();
        $this->be($user);
        $this->get('auth/nfdi/callback')
            ->assertInvalid(['nfdi-id'])
            ->assertRedirectToRoute('settings-authentication');
        $this->assertAuthenticatedAs($user);
    }

    public function testCallbackConnectAlreadyConnected()
    {
        $id = NfdiLoginId::factory()->create();
        $user = new SocialiteUser;
        $user->map(['id' => $id->id]);
        Socialite::shouldReceive('driver->user')->andReturn($user);

        $this->be($id->user);
        $this->get('auth/nfdi/callback')->assertRedirectToRoute('settings-authentication');
        $this->assertAuthenticatedAs($id->user);
    }

    public function testInvalidStateExceptionDuringLogin()
    {
        config(['biigle.user_registration' => true]);
        Socialite::shouldReceive('driver->user')->andThrow(InvalidStateException::class);

        $this->get('auth/nfdi/callback')
            ->assertInvalid(['nfdi-id'])
            ->assertRedirectToRoute('login');
    }

    public function testInvalidStateExceptionDuringConnect()
    {
        config(['biigle.user_registration' => true]);
        Socialite::shouldReceive('driver->user')->andThrow(InvalidStateException::class);

        $user = User::factory()->create();
        $this->be($user);
        $this->get('auth/nfdi/callback')
            ->assertInvalid(['nfdi-id'])
            ->assertRedirectToRoute('settings-authentication');
    }
}
