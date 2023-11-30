<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Article;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticlesResources;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;

class ArticleController extends Controller
{

    /**
     *  @var Article $_article
     *  @var string $_environment
     */
    private $_article, $_environment;



    /**
     *  @param Article $article
     *  @param string $_environment
     *  @param App $app
     */
    public function __construct(Article $article, App $app)
    {
        $this->_article = $article;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|ArticlesResources
     */
    public function index()
    {
        try {
            $articles = $this->_article::with('sub_service:id,name')->paginate();
            if ($articles->isNotEmpty())
                return new ArticlesResources($articles);
            return response()->json(['error' => true,
                'message' => 'No articles found'
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error while fetching articles', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Error while fetching articles'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ArticleStoreRequest  $request
     * @return JsonResponse|null
     */
    public function store(ArticleStoreRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  Article  $article
     * @return ArticleResource
     */
    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ArticleUpdateRequest  $request
     * @param  Article  $article
     * @return JsonResponse|null
     */
    public function update(ArticleUpdateRequest $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Article  $article
     * @return JsonResponse|null
     */
    public function destroy(Article $article)
    {
        //
    }
}
