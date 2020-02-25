<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('posts.index', ['posts' => Post::all()]);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Factory|View
     */
    public function show(Post $post)
    {
        $comments = Comment::where('post_id', $post->id)->orderBy('id', 'desc')->get();

        return view('posts.show', compact('post', 'comments'));
    }
}
