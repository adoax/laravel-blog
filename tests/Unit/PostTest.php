<?php

namespace Tests\Unit;

use App\Category;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function an_user_id_is_recorded()
    {
        Post::create([
            'title' => 'Good title',
            'content' => 'Le emilleir contenue que tu est jamais lue.. ba ',
            'image' => 'urldimage',
            'user_id' => 1
        ]);

        $this->assertCount(1, Post::all());
    }

    /** @test */
    public function success_created_with_factory()
    {

        $post = factory(Post::class)->create();

        $this->assertCount(1, Post::all());
    }

    /** @test */
    public function posts_has_categories()
    {
        $this->withoutExceptionHandling();
        $category = factory(Category::class, 10)->create();
        $posts = factory(Post::class)->create();
        $posts->categories()->attach($category);

        $posts->categories($category);

        $this->assertCount(10, Category::all());
        $this->assertCount(1, Post::all());
        $this->assertCount(10, Post::first()->categories);


    }

}
