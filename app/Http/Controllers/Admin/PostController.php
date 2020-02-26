<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Post;
use App\Services\ImageService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * @var ImageService
     */
    private $imageUploadService;

    public function __construct(ImageService $imageUploadService)
    {
        $this->middleware('auth');
        $this->imageUploadService = $imageUploadService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        $posts = Post::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.posts.create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest $request
     * @return RedirectResponse
     */
    public function store(PostRequest $request)
    {
        $this->imageUploadService->handleUploadImage($request->file('image'));
        $post = Post::create($request->all());
        $post->categories()->attach($request->categories);

        return redirect()->route('admin.posts.show', $post->id)->with('status', 'Vous avez créer un article');
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Factory|View
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = Category::all();


        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest $request
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $this->imageUploadService->handleUploadImage($request->file('image'), $post);

        $post->update($request->all());
        $post->categories()->sync($request->categories);

        return redirect()->route('admin.posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $this->imageUploadService->handleDeleteImage($post);

        $post->comments()->delete();
        $post->delete();

        return redirect()->route('admin.posts.index');
    }
}
