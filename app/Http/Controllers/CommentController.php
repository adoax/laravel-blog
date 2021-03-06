<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only('store', 'update', 'delete');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CommentRequest $request
     * @return RedirectResponse
     */
    public function store(CommentRequest $request)
    {
        Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'content' => $request['content'],
        ]);

        return redirect()->route('posts.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CommentRequest $request
     * @param Comment $comment
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $comment->update($request->all());

        return redirect()->route('posts.show', $request->post_id)->with('status', 'Votre message a bien été modifer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back();
    }
}
