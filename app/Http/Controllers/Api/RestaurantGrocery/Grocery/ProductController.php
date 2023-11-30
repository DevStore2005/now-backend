<?php

namespace App\Http\Controllers\Api\RestaurantGrocery\Grocery;

use App\Models\Product;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Models\Category;
use App\Utils\ProductType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;

class ProductController extends Controller
{
    /**
     * @var Request $_request
     * @var Product $_product
     * @var Category $_category
     * @var String $_environment
     */
    private $_request, $_product, $_category, $_environment;

    /**
     * Create a new controller instance.
     * @param  Request $request
     * @param  Product $product
     * @param  Category $category
     * @param  App $app
     * @return void
     */
    public function __construct(Request $request, Product $product, Category $category, App $app)
    {
        $this->_request = $request;
        $this->_product = $product;
        $this->_category = $category;
        $this->_environment = $app::environment();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($grocer_id)
    {
        try {
            $products = $this->_product->where('grocer_id', $grocer_id)
                ->when($this->_request->categories, function ($query, $categories) {
                    $categories = explode(',', $categories);
                    return $query->WhereIn('category_id', $categories);
                })
                ->where('type', ProductType::GROCERY)
                ->whereStatus('Active')
                ->with(['grocery_store', 'category'])
                ->orderBy('total_order', 'DESC')
                ->paginate(AppConst::PAGE_SIZE)
                ->withPath('');
            if ($products->isEmpty() == true) {
                return response()->json([
                    'error' => true,
                    'message' => 'No products found'
                ], HttpStatusCode::NOT_FOUND);
            }
            $categories = $this->_category->where('type', ProductType::GROCERY)->get();
            $products->categories = $categories;
            return new ProductsResource($products);
        } catch (\Exception $e) {
            Log::error(['ProductController -> products', $e->getMessage(), $e->getTraceAsString()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * 
     */
    public function show(Product $product)
    {
        try {
            if ($product->type == ProductType::GROCERY) {
                return new ProductResource($product);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'No product found'
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error(['ProductController -> products', $e->getMessage(), $e->getTraceAsString()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
