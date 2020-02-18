<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $file
        ]);

        $post = Post::all();
        $response->assertRedirect(route('posts.show', $post->first()->id));
        $this->assertCount(1, $post);
        $this->assertInstanceOf(Carbon::class, $post->first()->created_at);
        $this->assertInstanceOf(Carbon::class, $post->first()->updated_at);
        Storage::disk('public')->assertExists('images/' . $file->hashName());
        Storage::disk('public')->assertMissing('failling.jpg');
    }

    /**
     * @test
     */
    public function posts_errors_title()
    {
        $this->loginWithFakeUser();
        
        $file = UploadedFile::fake()->image('image.jpg');
        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => '',
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file
        ]);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function posts_generate_edit_slug()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $slug = $this->faker->sentence;

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->faker->paragraph(),
            'image' => $file
        ]);

        $response->assertRedirect();
        $post = Post::first();

        $this->assertEquals(Str::slug($slug), $post->slug);
        Storage::disk('public')->assertExists('images/' . $file->hashName());
        Storage::disk('public')->assertMissing('failling.jpg');

    }

    /** @test */
    public function posts_generate_edit_excerpt()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();

        $excerpt = $this->faker->sentence;

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'excerpt' => $excerpt,
            'image' => $file
        ]);

        $response->assertRedirect();
        $post = Post::first();

        $this->assertEquals($excerpt, $post->excerpt);
        Storage::disk('public')->assertExists('images/' . $file->hashName());
        Storage::disk('public')->assertMissing('failling.jpg');

    }

    /**
     * @test
     */
    public function posts_errors_content()
    {
        $this->loginWithFakeUser();

        $file = UploadedFile::fake()->image('image.jpg');
        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '',
            'excerpt' => Str::words($content, 50),
            'image' => $file
        ]);
        $response->assertSessionHasErrors('content');
    }


    /**
     * @test
     */
    public function posts_errors_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


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
        Storage::disk('public')->assertMissing('missing.jpg');
    }


    /** @test */
    public function posts_edit(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file
        ]);

        $oldPost = Post::first();
        $newFile = UploadedFile::fake()->image('newImage.jpg');
        $response = $this->put('posts/' . $oldPost->id, [
            'title' => 'New title',
            'slug' => $oldPost->slug,
            'content' => $oldPost->content,
            'excerpt' => $oldPost->excerpt,
            'image' => $newFile
        ]);

        $response->assertRedirect(route('posts.show', $oldPost->id));

        $this->assertEquals('New title', Post::first()->title);
        Storage::disk('public')->assertExists('images/' . $newFile->hashName());
        Storage::disk('public')->assertExists('images/' . Post::first()->image);
        Storage::disk('public')->assertMissing('failling.jpg');


    }

    /** @test */
    public function posts_delete()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph;

        $this->post('/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file
        ]);

        $oldPost = Post::first();

        $response = $this->delete(route('posts.destroy', $oldPost->id));
        $response->assertRedirect(route('posts.index'));
        $this->assertCount(0, Post::all());


    }

    /** @test */
    public function post_author_automatically_added()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');


        $this->loginWithFakeUser();
        $this->withoutExceptionHandling();

        $response = $this->post('/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $file
        ]);

        $post = Post::first();
        $user = User::first();

        $this->assertCount(1, User::all());
        $this->assertEquals($user->id, $post->user_id);


    }
}
