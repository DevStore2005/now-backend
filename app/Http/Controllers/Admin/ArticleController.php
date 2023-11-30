<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\View\View;
use App\Models\SubService;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;

class ArticleController extends Controller
{

    /**
     * Private variable to store the model
     *
     * @var Article $_article
     * @var SubService $_sub_service
     * @access private
     */
    private $_article, $_sub_service;

    /**
     * Create a new controller instance.
     * @param  Article $article
     * @param  SubService $_sub_service
     * @return void
     */
    public function __construct(Article $article, SubService $sub_service)
    {
        $this->_article = $article;
        $this->_sub_service = $sub_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index(): View
    {
        $articles = $this->_article->paginate();
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function create(): View
    {
        $sub_services = $this->_sub_service->active()->get();
        return view('admin.articles.create', compact('sub_services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ArticleStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArticleStoreRequest $request): JsonResponse
    {
        try {
            $article = $this->_article->createArticle($request->validated());
            if ($article)
                return response()->json([
                    'success' => true,
                    'message' => 'Article created successfully',
                    'data' => $article,
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Article could not be created',
                ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error("message", ['context' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json([
                'success' => false,
                'message' => 'Article could not be created',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response|null
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response|View
     */
    public function edit(Article $article): View
    {
        $sub_services = $this->_sub_service->active()->get();
        return view('admin.articles.edit', compact('article', 'sub_services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ArticleUpdateRequest  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ArticleUpdateRequest $request, Article $article): JsonResponse
    {
        try {
            $article = $article->update($request->validated());
            if ($article)
                return response()->json([
                    'success' => true,
                    'message' => 'Article updated successfully',
                    'data' => $article,
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Article could not be updated',
                ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error("message", ['context' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json([
                'success' => false,
                'message' => 'Article could not be updated',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return RedirectResponse
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        return redirect()->back()->with('success_message', 'Article deleted successfully');
    }
}
