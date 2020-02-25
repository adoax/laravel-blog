<?php

namespace Tests\Feature;

use App\Category;
use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * L'utilisateur doit être Authentifier pour accéder la page 'posts'
     * @test
     */
    public function index_not_auth_has_redirect(): void
    {
        $response = $this->get('/admin/posts');

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

        $response = $this->get('/admin/posts');
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
        $file = UploadedFile::fake()->image('image.jpg');
        $categorie = factory(Category::class, 4)->create();

        $this->withoutExceptionHandling();
        $this->loginWithFakeUser();

        $response = $this->post('admin/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $file,
            'categories' => $categorie
        ]);
        $post = Post::all();

        $response->assertRedirect(route('admin.posts.show', $post->first()->id));
        $this->assertCount(1, $post);
        $this->assertCount(4, $post->first()->categories);
        $this->assertInstanceOf(Carbon::class, $post->first()->created_at);
        $this->assertInstanceOf(Carbon::class, $post->first()->updated_at);

        $this->assertFileExists(storage_path('app/public/images/' . $file->hashName()));
        $this->assertFileExists(storage_path('app/public/images/thumbs/' . $file->hashName()));

        $this->deleteImageTest(Post::first());
    }

    /** @test */
    public function post_author_automatically_added()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');

        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $response = $this->post('admin/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'image' => $file,
            'categories' => $categorie
        ]);

        $post = Post::first();
        $user = User::first();

        $this->assertCount(1, User::all());
        $this->assertEquals($user->id, $post->user_id);

        $this->deleteImageTest($post);


    }

    /**
     * @test
     */
    public function posts_errors_title()
    {
        $this->loginWithFakeUser();

        $file = UploadedFile::fake()->image('image.jpg');
        $categories =  factory(Category::class)->create();
        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $response = $this->post('admin/posts', [
            'title' => '',
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file,
            'categories' => $categories
        ]);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function posts_created_with_slug()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');
        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $slug = $this->faker->sentence;

        $response = $this->post('admin/posts', [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->faker->paragraph(),
            'image' => $file,
            'categories' => $categorie
        ]);

        $response->assertRedirect();
        $post = Post::first();

        $this->assertEquals(Str::slug($slug), $post->slug);
        $this->deleteImageTest($post);

    }

    /** @test */
    public function posts_created_with_excerpt()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');

        $categorie = factory(Category::class)->create();
        $this->loginWithFakeUser();

        $excerpt = $this->faker->sentence;

        $response = $this->post('admin/posts', [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraph(),
            'excerpt' => $excerpt,
            'image' => $file,
            'categories' => $categorie
        ]);

        $response->assertRedirect();
        $post = Post::first();

        $this->assertEquals($excerpt, $post->excerpt);
        $this->deleteImageTest($post);

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

        $response = $this->post('admin/posts', [
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

        $response = $this->post('admin/posts', [
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
        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $this->post('admin/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file,
            'categories' => $categorie
        ]);

        $oldPost = Post::first();
        $newFile = UploadedFile::fake()->image('newImage.jpg');
        $response = $this->put('admin/posts/' . $oldPost->id, [
            'title' => 'New title',
            'slug' => $oldPost->slug,
            'content' => $oldPost->content,
            'excerpt' => $oldPost->excerpt,
            'image' => $newFile,
            'categories' => $categorie
        ]);

        $response->assertRedirect(route('admin.posts.show', $oldPost->id));

        $this->assertEquals('New title', Post::first()->title);
        $this->assertFileExists(storage_path('app/public/images/' . $newFile->hashName()));
        $this->assertFileExists(storage_path('app/public/images/' . Post::first()->image));

        $this->deleteImageTest(Post::first());

    }

    public function posts_edit_bad_user(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.jpg');
        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph();

        $this->post('admin/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file,
            'categories' => $categorie
        ]);

        $oldPost = Post::first();

        $this->loginWithFakeUser();
        $newFile = UploadedFile::fake()->image('newImage.jpg');
        $response = $this->put('admin/posts/' . $oldPost->id, [
            'title' => 'New title',
            'slug' => $oldPost->slug,
            'content' => $oldPost->content,
            'excerpt' => $oldPost->excerpt,
            'image' => $newFile,
            'categories' => $categorie
        ]);

        $response->assertSee('Vous avez pas le droit ! ');

        $this->assertFileExists(storage_path('app/public/images/' . $newFile->hashName()));
        $this->assertFileExists(storage_path('app/public/images/' . Post::first()->image));

        $this->deleteImageTest(Post::first());

    }

    /** @test */
    public function posts_delete()
    {
        $file = UploadedFile::fake()->image('image.jpg');
        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph;

        $this->post('admin/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file,
            'categories' => $categorie
        ]);

        $oldPost = Post::first();

        $this->assertFileExists(storage_path('app/public/images/' . $file->hashName()));
        $this->assertFileExists(storage_path('app/public/images/thumbs/' . $file->hashName()));

        $response = $this->delete(route('admin.posts.destroy', $oldPost->id));
        $this->assertFileNotExists(storage_path('app/public/images/' . $file->hashName()));
        $this->assertFileNotExists(storage_path('app/public/images/thumbs/' . $file->hashName()));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertCount(0, Post::all());





    }

    /** @test */
    public function posts_delete_bad_user()
    {
        $file = UploadedFile::fake()->image('image.jpg');
        $categorie = factory(Category::class)->create();

        $this->loginWithFakeUser();

        $title = $this->faker->unique()->sentence;
        $content = $this->faker->paragraph;

        $this->post('admin/posts', [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::words($content, 50),
            'image' => $file,
            'categories' => $categorie
        ]);

        $oldPost = Post::first();

        $this->assertFileExists(storage_path('app/public/images/' . $file->hashName()));
        $this->assertFileExists(storage_path('app/public/images/thumbs/' . $file->hashName()));

        $this->loginWithFakeUser();
        $response = $this->delete(route('admin.posts.destroy', $oldPost->id));
        $response->assertStatus(403);

        $this->assertFileExists(storage_path('app/public/images/' . $file->hashName()));
        $this->assertFileExists(storage_path('app/public/images/thumbs/' . $file->hashName()));

        $this->deleteImageTest($oldPost);

    }




}
