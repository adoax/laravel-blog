<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * L'utilisateur doit être Authentifier pour accéder la page 'posts'
     * @test
     */
    public function index_not_auth_has_redirect(): void
    {
        $response = $this->get('/posts');

        $response->assertRedirect('/login');
        $response->assertSee('Redirecting');
    }

    /**
     * L'utilisateur est Authentifier est accede à la page
     * @test
     */
    public function index_is_auth(): void
    {
        $this->loginWithFakeUser();

        $response = $this->get('/posts');
        $response->assertSuccessful();
        $response->assertStatus(200);

    }

    /**
     * Permet de Savoir si les 'Posts' sont afficher dans la page d'accueil
     *
     * @test
     */
    public function posts_created(): void
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $post = Post::first();
        $response->assertRedirect(route('posts.show', $post->id));
        $this->assertCount(1, Post::all());
    }

    /**
     * @test
     */
    public function posts_errors_title()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => '',
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);
        $response->assertSessionHasErrors('title');
    }

    /**
     * @test
     */
    public function posts_errors_slug()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => '',
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);
        $response->assertSessionHasErrors('slug');
    }

    /**
     * @test
     */
    public function posts_errors_content()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '',
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);
        $response->assertSessionHasErrors('content');
    }

    /**
     * @test
     */
    public function posts_errors_excerpt()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => '',
            'image' => $this->faker->imageUrl(450, 350)
        ]);
        $response->assertSessionHasErrors('excerpt');
    }

    /**
     * @test
     */
    public function posts_errors_image()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => ''
        ]);
        $response->assertSessionHasErrors('image');
    }


    /** @test */
    public function posts_edit(): void
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $oldPost = Post::first();

        $response = $this->put('posts/' . $oldPost->id, [
            'title' => 'New title',
            'slug' => 'new-title',
            'content' => $oldPost->content,
            'excerpt' => $oldPost->excerpt,
            'image' => $oldPost->image
        ]);

        $response->assertRedirect(route('posts.show', $oldPost->id));

        $this->assertEquals('New title', Post::first()->title);
        $this->assertEquals('new-title', Post::first()->slug);


    }

    /** @test */
    public function posts_delete()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph;

        $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $oldPost = Post::first();

        $response = $this->delete(route('posts.destroy', $oldPost->id));
        $response->assertRedirect(route('posts.index'));
        $this->assertCount(0, Post::all());

    }
}
