<?php

namespace App\Http\Controllers\Api\User;

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
     *  @var Request $_request 
     *  @var Category $_category 
     *  @var string $_environment
     */
    private $_request, $_category, $_environment;


    /**
     * Create a new controller instance.
     * @param  Request $request
     * @param  Category $category
     * @return App $app
     */
    public function __construct(Request $request, Category $category, App $app)
    {
        $this->_request = $request;
        $this->_category = $category;
        $this->_environment = $app::environment();
    }

    /**
     * User list
     * @return JsonResponse|CategoriesResource
     */
    public function index()
    {
        try {
            $data = $this->_category->where('type', $this->_request->type)->get();
            if ($data->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
            return new CategoriesResource($data);
        } catch (\Exception $e) {
            $error = ["info" => 'CategoryController -> index', "mesage" => $e->getMessage(), "line" =>  $e->getLine(), "fileName" => $e->getFile()];
            Log::error('CategoryController -> index', $error);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $error : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
