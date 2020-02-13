<?php

/** @var Factory $factory */

use App\Post;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {

    $title = $faker->unique()->title;
    $content = $faker->sentence;

    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'content' => $content,
        'excerpt' => Str::words($content, 25),
        'image' => $faker->imageUrl(),
    ];
});
