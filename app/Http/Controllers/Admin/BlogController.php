<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;

class BlogController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var Blog $_blog
     * @var Category $_category
     * @access private
     */
    private $_blog, $_category;

    /**
     * Create a new controller instance.
     * @param Blog $blog
     * @param Category $category
     * @return void
     */
    public function __construct(Blog $blog, Category $category)
    {
        $this->_blog = $blog;
        $this->_category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index()
    {
        $blogs = $this->_blog->get();
        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $categories = $this->_category->category()->get();
        return view('admin.blogs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlogStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BlogStoreRequest $request)
    {
        try {
            $blog = $this->_blog->createBlog($request->all());
            if ($blog) return $this->success($blog, 'Blog created successfully');
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\Response|null
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Blog $blog
     * @return View
     */
    public function edit(Blog $blog)
    {
        $categories = $this->_category->category()->get();
        $blog->getFirstMediaUrl();
        $contents = $blog->contents->map(fn($content) => [
            'id' => $content->id,
            'content' => $content->content,
            'image' => $content->getFirstMediaUrl('image'),
        ]);
        $blog = $blog->toArray();
        $blog['contents'] = $contents;
        $blog = json_decode(json_encode($blog), FALSE);
        return view('admin.blogs.edit', compact(['blog', 'categories']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BlogUpdateRequest $request
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BlogUpdateRequest $request, Blog $blog)
    {
        try {
            Log::info("new", [$request->validated(), $blog]);
            $blog->updateBlog($request->all(), $blog);
            return $this->success(['request' => $request->validated(), 'blog' => $blog], 'Blog updated successfully ');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Blog $blog)
    {
        try {
            $blog->delete();
            return redirect()->back()->with('success_message', 'Blog deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }
}
