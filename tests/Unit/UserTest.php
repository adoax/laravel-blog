<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function an_author_id_is_recorded()
    {
        User::create([
            'name' => 'Anthony',
            'password' => bcrypt('azerty'),
            'email' => "a@a.com",
        ]);


        $this->assertCount(1, User::all());
    }
}
