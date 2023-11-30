<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Utils\HttpStatusCode;

class CategoryController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var Category $_category
     * @access private
     */
    private $_category;

    /**
     * Create a new controller instance.
     * @param  Category $category
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->_category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $categories = $this->_category->whereNull('type')->get();
            if ($categories->isNotEmpty()) {
                return $this->success($categories, "Categories retrieved");
            }
            return $this->error("Categories not found", HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e);
            return $this->error("Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
