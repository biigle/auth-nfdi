<?php

namespace Biigle\Tests\Modules\AuthNFDI\Http\Controllers;

use Biigle\Modules\AuthNFDI\NfdiLoginId;
use Biigle\Role;
use Biigle\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Session;
use TestCase;
use View;

class RegisterControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config(['biigle.user_registration' => true]);
    }

    public function testShowRegistrationForm()
    {
        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->get('auth/nfdi/register')
            ->assertSuccessful();
    }

    public function testShowRegistrationFormWithoutToken()
    {
        $this->get('auth/nfdi/register')->assertRedirectToRoute('register');
    }

    public function testShowRegistrationFormAuthenticated()
    {
        $user = User::factory()->create();
        $this->be($user);
        $this->get('auth/nfdi/register')->assertRedirectToRoute('home');
    }

    public function testShowRegistrationFormDisabledRegistration()
    {
        config(['biigle.user_registration' => false]);
        $this->get('auth/nfdi/register')->assertStatus(404);
    }

    public function testRegister()
    {
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertSessionMissing('nfdilogin-token')
            ->assertRedirectToRoute('home');

        $user = User::where('email', 'joe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Joe', $user->firstname);
        $this->assertSame('User', $user->lastname);
        $this->assertSame('something', $user->affiliation);
        $this->assertSame(Role::editorId(), $user->role_id);

        $this->assertTrue(NfdiLoginId::where('user_id', $user->id)->where('id', 'mynfdiid')->exists());
    }

    public function testRegisterMissingAffiliation()
    {
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
            ])
            ->assertSessionHas('nfdilogin-token')
            ->assertInvalid('affiliation');
    }

    public function testRegisterEmailTaken()
    {
        User::factory()->create(['email' => 'joe@example.com']);
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertInvalid('email');
    }

    public function testRegisterIdTaken()
    {
        NfdiLoginId::factory()->create(['id' => 'mynfdiid']);
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertSessionMissing('nfdilogin-token')
            ->assertInvalid('nfdi-id');
    }

    public function testRegisterWithoutToken()
    {
        $this->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertRedirectToRoute('register');
    }

    public function testRegisterInvalidToken()
    {
        Socialite::shouldReceive('driver->userFromToken')->andThrow(Exception::class);
        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertSessionMissing('nfdilogin-token')
            ->assertInvalid('nfdi-id');
    }

    public function testRegisterPrivacy()
    {
        View::shouldReceive('exists')->with('privacy')->andReturn(true);
        View::shouldReceive('exists')->with('terms')->andReturn(false);
        View::shouldReceive('share')->passthru();
        View::shouldReceive('make')->andReturn('');
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertSessionHas('nfdilogin-token')
            ->assertInvalid('privacy');

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
                'privacy' => '1',
            ])
            ->assertRedirectToRoute('home');
    }

    public function testRegisterTerms()
    {
        View::shouldReceive('exists')->with('privacy')->andReturn(false);
        View::shouldReceive('exists')->with('terms')->andReturn(true);
        View::shouldReceive('share')->passthru();
        View::shouldReceive('make')->andReturn('');
        $user = new SocialiteUser;
        $user->map([
            'id' => 'mynfdiid',
            'given_name' => 'Joe',
            'family_name' => 'User',
            'email' => 'joe@example.com',
        ]);
        Socialite::shouldReceive('driver->userFromToken')
            ->with('mytoken')
            ->andReturn($user);

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
            ])
            ->assertSessionHas('nfdilogin-token')
            ->assertInvalid('terms');

        $this->withSession(['nfdilogin-token' => 'mytoken'])
            ->post('auth/nfdi/register', [
                '_token'    => Session::token(),
                'affiliation' => 'something',
                'terms' => '1',
            ])
            ->assertRedirectToRoute('home');
    }

    public function testRegisterDisabledRegistration()
    {
        config(['biigle.user_registration' => false]);
        $this->post('auth/nfdi/register')->assertStatus(404);
    }

    public function testRegisterAuthenticated()
    {
        $user = User::factory()->create();
        $this->be($user);
        $this->post('auth/nfdi/register')->assertRedirectToRoute('home');
    }
}
