<?php

namespace App\Http\Controllers\Api\RestaurantGrocery;

use App\Utils\MyAppEnv;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;

class CategoryController extends Controller
{
    /**
     * @var Category $_category
     * @var String $_environment
     */
    private $_category, $_environment;

    /**
     * Create a new controller instance.
     * @param Category $category
     * @param App $app
     * @return void
     */
    public function __construct(Category $category, App $app)
    {
        $this->_category = $category;
        $this->_environment = $app::environment();
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|CategoriesResource
     */
    public function index()
    {
        try {
            $category = $this->_category->get();
            if ($category->isEmpty() == true) {
                return response()->json([
                    'error' => true,
                    'message' => 'No category found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new CategoriesResource($category);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response|null
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Category $category)
    {
        //
    }
}
