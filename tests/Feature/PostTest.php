<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Carbon\Carbon;
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

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $post = Post::all();
        $response->assertRedirect(route('posts.show', $post->first()->id));
        $this->assertCount(1, $post);
        $this->assertInstanceOf(Carbon::class, $post->first()->created_at);
        $this->assertInstanceOf(Carbon::class, $post->first()->updated_at);
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

    /** @test */
    public function posts_generate_edit_slug()
    {
        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $slug = $this->faker->sentence;

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $response->assertRedirect();
        $post  = Post::first();

        $this->assertEquals(Str::slug($slug), $post->slug);

    }

    /** @test */
    public function posts_generate_edit_excerpt()
    {
        $this->loginWithFakeUser();

        $excerpt = $this->faker->sentence;

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'excerpt' => $excerpt,
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $response->assertRedirect();
        $post  = Post::first();

        $this->assertEquals($excerpt, $post->excerpt);

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
            'slug' => $oldPost->slug,
            'content' => $oldPost->content,
            'excerpt' => $oldPost->excerpt,
            'image' => $oldPost->image
        ]);

        $response->assertRedirect(route('posts.show', $oldPost->id));

        $this->assertEquals('New title', Post::first()->title);


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

    /** @test */
    public function post_author_automatically_added()
    {
        $this->loginWithFakeUser();
        $this->withoutExceptionHandling();

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(450, 350)
        ]);

        $post = Post::first();
        $user = User::first();

        $this->assertCount(1, User::all());
        $this->assertEquals($user->id, $post->user_id);

    }
}
