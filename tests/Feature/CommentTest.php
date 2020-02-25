<?php

namespace Tests\Feature;

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function create_comment_success()
    {
        $post = factory(Post::class)->create();
        $this->assertCount(1, Post::all());

        $response = $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'Bien le bonour',
                'post_id' => $post->id,
            ]
        );
        $response->assertRedirect(route('posts.index'));
        $this->assertCount(1, Comment::all());
        $this->assertCount(1, Post::first()->comments);
    }

    /** @test */
    public function create_comment_fail_empty()
    {
        $post = factory(Post::class)->create();

        $response = $this->be(User::first())->followingRedirects()
            ->from(route('posts.show', $post))
            ->post(route('comments.store'),
                [
                    'content' => '',
                    'post_id' => $post->id,
                ]
            )->assertSee(__('validation.required', [
                'attribute' => __('validation.attributes.content')
            ]));
    }

    /** @test */
    public function create_comment_fail_not_auth()
    {
        $post = factory(Post::class)->create();

        $response = $this->post(route('comments.store'), [
            'content' => 'Salut a toi',
            'post_id' => $post->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function edit_comment_success()
    {
        $post = factory(Post::class)->create();
        $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'azerty',
                'post_id' => $post->id,
            ]
        );
        $this->assertCount(1, Comment::all());
        $oldCommnent = Comment::first();

        $response = $this->be(User::first())->patch(route('comments.update', $oldCommnent), [
            'content' => 'Je suis modifier',
        ]);
        $this->assertNotEquals('Je suis modifier', $oldCommnent);
        $this->assertEquals('Je suis modifier', Comment::first()->content);
    }

    /** @test */
    public function edit_comment_bad_user()
    {
        $post = factory(Post::class)->create();
        $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'azerty',
                'post_id' => $post->id,
            ]
        );
        $this->assertCount(1, Comment::all());
        $oldCommnent = Comment::first();

        $this->loginWithFakeUser();
        $response = $this->patch(route('comments.update', $oldCommnent), [
            'content' => 'Je suis modifier',
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function delete_comment_success()
    {
        $post = factory(Post::class)->create();
        $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'azerty',
                'post_id' => $post->id,
            ]
        );
        $this->assertCount(1, Comment::all());

        $oldCommnent = Comment::first();

        $response = $this->delete(route('comments.destroy', $oldCommnent));

        $this->assertCount(0, Comment::all());
    }

    /** @test */
    public function delete_comment_bad_user()
    {
        $post = factory(Post::class)->create();
        $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'azerty',
                'post_id' => $post->id,
            ]
        );
        $this->assertCount(1, Comment::all());

        $oldCommnent = Comment::first();
        $this->loginWithFakeUser();
        $response = $this->delete(route('comments.destroy', $oldCommnent));
        $response->assertStatus(403);
        $this->assertCount(1, Comment::all());
    }
    /** @test */
    public function comment_delete_with_deleted_posts()
    {
        $post = factory(Post::class)->create();
        $this->be(User::first())->post(route('comments.store'),
            [
                'content' => 'azerty',
                'post_id' => $post->id,
            ]
        );
        $this->assertCount(1, Post::all());
        $this->assertCount(1, Comment::all());

        $response = $this->delete(route('admin.posts.destroy', $post ));
        $response->assertRedirect(route('admin.posts.index'));
        $this->assertCount(0, Post::all());
        $this->assertCount(0, Comment::all());

    }
}
