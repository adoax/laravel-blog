<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Post;
use App\Services\ImageService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
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
        return view('admin.posts.index', ['posts' => Post::all()]);
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
     */
    public function edit(Post $post)
    {
        $categories = Category::all();


        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->imageUploadService->handleUploadImage($request->file('image'), $post);
        $this->imageUploadService->handleDeleteImage($post);

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

        $this->imageUploadService->handleDeleteImage($post);
        $post->delete();

        return redirect()->route('admin.posts.index');
    }
}
