<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogsResource;

class BlogController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var Blog $_blog
     * @access private
     */
    private $_blog;

    /**
     * Create a new controller instance.
     * @param Blog $blog
     * @return void
     */
    public function __construct(Blog $blog)
    {
        $this->_blog = $blog;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|BlogsResource
     */
    public function index(Request $request)
    {
        try {
            $blog = $this->_blog->with([
                'lastest_comment' => fn($query) => $query->with([
                    'user:id,first_name,last_name,image',
                ])->latest()->take(1)
            ])
                ->when($request->categoryId, function ($qry, $categoryId) {
                    return $qry->where('category_id', $categoryId);
                })->when($request->search, function ($qry, $search) {
                    return $qry->where('title', 'like', "%{$search}%");
                })->latest()->when($request->hasAny('trend', 'popular'), function ($qry) use ($request) {
                    return $qry->orderBy('views', 'desc')->take($request->has('trend') ? 5 : 3)->get();
                }, function ($qry) {
                    return $qry->paginate(20);
                });
            if ($blog->isNotEmpty()) {
                return new BlogsResource($blog);
            } else {
                return $this->error("No blogs found", HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Blog $blog
     * @return BlogResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Blog $blog)
    {
        try {
            $blog->increment('views');
            if ($request->has('increment')) {
                return $this->success(['views' => $blog->views], 'Blog viewed successfully');
            }
            return new BlogResource($blog);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Blog $blog
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update(Request $request, $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Blog $blog
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy($blog)
    {
        //
    }
}
