<?php

/** @var Factory $factory */

use App\Post;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {


    return [
        'title' => $faker->unique()->sentence,
        'content' => $faker->paragraph,
        'image' => $faker->imageUrl(),
        'user_id' => \factory(User::class),
    ];
});
