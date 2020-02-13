<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Permet de Authentifier un faux utilisateur
     */
    public function loginWithFakeUser(): void
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password')
        ]);

        $this->be($user);
    }
}
