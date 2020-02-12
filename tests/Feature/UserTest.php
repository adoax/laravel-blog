<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use ResetsPasswords;
    
    /**
     * @test
     */
    public function login_auth_and_redirect()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'name' => $user->name
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     */
    public function register_created_and_auth_user_redirect()
    {
        $response = $this->post('register', [
            'name' => 'Anthony',
            'email' => 'a@a.com',
            'password' => 'azerty123',
            'password_confirmation' => 'azerty123'
        ]);

        $response->assertRedirect(route('home'));

        $user = User::where('email', 'a@a.com')->where('name', 'Anthony')->first();
        $this->assertNotNull($user);
    }

    /**
     * @test
     */
    public function password_show_reset_request_page()
    {
        $user = factory(User::class)->create();

        $this->get(route('password.request'))
            ->assertSuccessful()
            ->assertSee('Reset Password')
            ->assertSee('E-Mail Address')
            ->assertSee('Send Password Reset Link');
    }

    /**
     * @test
     */
    public function submit_password_reset_request_invalid_email()
    {
        $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => Str::random()
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.email', [
                'attribute' => 'adresse email',
            ]));


    }

    /**
     * @test
     */
    public function submit_password_reset_request_invalid_email_not_found()
    {
        $this
            ->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'a@a.com',
            ])
            ->assertSuccessful()
            ->assertSee(e(__('passwords.user')));
    }


    /**
     * @test
     */
    public function submit_password_reset_request()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.sent'));
        Notification::assertSentTo([$user], ResetPassword::class);
    }

    /**
     * @test
     */
    public function show_password_reset_page()
    {
        $user = factory(User::class)->create();

        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
        ]))
            ->assertSuccessful()
            ->assertSee('Reset Password')
            ->assertSee('E-Mail Address')
            ->assertSee('Password')
            ->assertSee('Confirm Password');
    }


    /**
     * @test
     */
    public function submit_password_reset_invalid_email()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $token = Password::broker()->createToken($user);

        $newPassword = 'azerty123';

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => Str::random(),
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.email', [
                'attribute' => 'adresse email',
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($newPassword, $user->password));

        $this->assertTrue(Hash::check('password', $user->password));

    }

    /**
     * @test
     */
    public function submit_password_reset_email_not_found()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $token = Password::broker()->createToken($user);

        $newPassword = 'azerty123';

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update', [
                'token' => $token,
                'email' => 'bad@email.com',
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ]))
            ->assertSuccessful()
            ->assertSee(e(__('passwords.user')));

        $user->refresh();

        $this->assertFalse(Hash::check($newPassword, $user->password));
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /**
     * @test
     */
    public function submit_password_reset_password_mis_match()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);
        $token = Password::broker()->createToken($user);

        $newPassword = 'azerty123';

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update', [
                'token' => $token,
                'email' => $user->email,
                'password' => $newPassword,
                'password_confirmation' => 'badpassword'
            ]))
            ->assertSuccessful()
            ->assertSee(__('validation.confirmed', [
                'attribute' => 'mot de passe'
            ]));
        $user->refresh();

        $this->assertFalse(Hash::check($newPassword, $user->password));
        $this->assertTrue(Hash::check('password', $user->password));

    }

    /**
     * @test
     */
    public function submit_password_reset_password_short()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $token = Password::broker()->createToken($user);
        $newPassword = "aze";

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update', [
                'token' => $token,
                'email' => $user->email,
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ]))
            ->assertSuccessful()
            ->assertSee(__('validation.min.string', [
                'attribute' => 'mot de passe',
                'min' => 8
            ]));

        $user->refresh();
        $this->assertFalse(Hash::check($newPassword, $user->password));
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /**
     * @test
     */
    public function submit_password_reset_bad_token()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $token = Password::broker()->createToken($user);
        $newPassword = 'azerty123';

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update', [
                'token' => 'badtoken',
                'email' => $user->email,
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ]))
            ->assertSuccessful()
            ->assertSee(e(__('passwords.token')));
        $user->refresh();
        $this->assertFalse(Hash::check($newPassword, $user->password));
        $this->assertTrue(Hash::check('password', $user->password));

    }

    /**
     * @test
     */
    public function submit_password_reset_success()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $token = Password::broker()->createToken($user);

        $newPassword = "azerty123";

        $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token
            ]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.reset'));

        $user->refresh();
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

}
