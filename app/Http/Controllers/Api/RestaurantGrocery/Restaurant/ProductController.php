<?php

namespace App\Http\Controllers\Api\RestaurantGrocery\Restaurant;

use Auth;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Utils\UserType;
use App\Models\Category;
use App\Utils\ProductType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\RestaurantsResource;
use App\Http\Resources\GroceryStoresResource;

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
   * get list of products
   * 
   * @return JsonResponse
   */
  public function index($restaurant_id)
  {
    try {
      $products = $this->_product->where('restaurant_id', $restaurant_id)
        ->when($this->_request->restaurant_type, function ($query, $type) {
          return $query->whereHas('restaurant', function ($query) use ($type) {
            return $query->Where('restaurant_type', $type);
          });
        })
        ->when($this->_request->categories, function ($query, $categories) {
          $categories = explode(',', $categories);
          return $query->WhereIn('category_id', $categories);
        })
        ->where('type', ProductType::FOOD)
        ->with('restaurant', 'category')
        ->whereStatus('Active')
        ->orderBy('total_order', 'DESC')
        ->paginate(AppConst::PAGE_SIZE)
        ->withPath('');
      if ($products->isEmpty() == true) {
        return response()->json([
          'error' => true,
          'message' => 'No food found'
        ], HttpStatusCode::NOT_FOUND);
      }
      $categories = $this->_category->where('type', ProductType::FOOD)->get();
      $products->categories = $categories;
      return new ProductsResource($products);
    } catch (\Exception $e) {
      Log::error(['ProductController -> products', $e->getMessage()]);
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
      if ($product->type == ProductType::FOOD) {
        return new ProductResource($product);
      } else {
        return response()->json([
          'error' => true,
          'message' => 'No food found'
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
}
