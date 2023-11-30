<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentsResource;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;

class CommentController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var COmment $_comment
     * @access private
     */
    private $_comment;

    /**
     * Create a new controller instance.
     * @param  Comment $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->_comment = $comment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|CommentsResource
     */
    public function index(Blog $blog)
    {
        try {
            $comment = $blog->comments()
                ->with([
                    'user:id,first_name,last_name,image',
                    'replies'
                ])
                ->latest()
                ->paginate(10);
            if ($comment->isNotEmpty()) {
                return new CommentsResource($comment);
            } else {
                return $this->error("No comments found", HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CommentStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function store(CommentStoreRequest $request)
    {
        try {
            $comment = $this->_comment->create($request->validated() + ['user_id' => $request->user()->id]);
            if ($comment) {
                return $this->success($comment, "Comment created successfully");
            }
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CommentUpdateRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        try {
            if ($comment->update($request->validated())) {
                return $this->success($comment, "Comment updated successfully");
            }
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy(Comment $comment)
    {
        try {
            if ($comment->delete()) {
                return $this->success(null, "Comment deleted successfully");
            }
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
