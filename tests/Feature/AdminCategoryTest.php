<?php

namespace Tests\Feature;

use App\Category;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function index_not_auth_has_redirect()
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function index_authorise()
    {
        $this->loginWithFakeUser();

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function created_category()
    {
        $this->loginWithFakeUser();

        $response = $this->post(route('admin.categories.store'), [
            'name' => $this->faker->sentence()
        ]);

        $category = Category::first();
        $this->assertEquals(Str::slug($category->name), $category->slug);
        $response->assertRedirect(route('admin.categories.show', $category->id));
        $this->assertCount(1, Category::all());
    }

    /** @test */
    public function created_category_error_name_empty()
    {
        $this->loginWithFakeUser();

        $response = $this->post(route('admin.categories.store'), [
            'name' => ''
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function created_category_error_name_exist()
    {
        $this->loginWithFakeUser();
        $this->post(route('admin.categories.store'), [
            'name' => 'Aventure'
        ]);

        $response = $this->followingRedirects()
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Aventure'
            ])
            ->assertSee(__('validation.unique', [
                'attribute' => __('validation.attributes.name')
            ]));

    }

    /** @test */
    public function updated_category_success()
    {
        $this->loginWithFakeUser();
        $this->post(route('admin.categories.store'), [
            'name' => 'Aventure'
        ]);
        $categories = Category::first();

        $reponse = $this->patch(route('admin.categories.update', $categories->id), [
            'name' => 'Informatique'
        ]);

        $this->assertNotEquals($categories->name, Category::first()->name);
        $this->assertEquals('Informatique', Category::first()->name);
        $this->assertCount(1, Category::all());
    }

    /** @test */
    public function delete_category_success()
    {
        $this->loginWithFakeUser();
        $this->post(route('admin.categories.store'), [
            'name' => 'Aventure'
        ]);
        $categories = Category::all();
        $this->assertCount(1, Category::all());

        $this->delete(route('admin.categories.destroy', Category::first()));

        $this->assertCount(0, Category::all());
    }

    /** @test */
    public function delete_category_link_posts_fail()
    {
        $file = UploadedFile::fake()->image('image.jpg');

        $this->loginWithFakeUser();

        $this->post(route('admin.categories.store'), [
            'name' => 'Aventure'
        ]);
        $category = Category::first();

        $this->assertCount(1, Category::all());

        $this->post(route('admin.posts.store'), [
            'title' => 'coucuo je sus un test',
            'content' => 'Je suis le contenue de l\'article',
            'excerpt' => 'Je suis une court presentation du cotnenue',
            'categories' => $category,
            'image' => $file
        ]);

        $this->assertCount(1, Post::all());
        $this->assertCount(1, Post::first()->categories);

        $this->deleteImageTest(Post::first());

        $this->delete(route('admin.categories.destroy', Category::first()));

        $this->assertCount(0, Category::all());

    }
}
