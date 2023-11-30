<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Utils\MyAppEnv;
use App\Utils\ProductType;

class CartController extends Controller
{

    /**
     *  @var Cart $_cart 
     *  @var Product $_cart 
     *  @var App $_cart 
     */
    private $_cart, $_product, $_environment;

    /**
     * Create a new controller instance.
     * @param  Cart $cart
     * @param  Product $product
     * @param  App $app
     * @return void
     */
    public function __construct(Cart $cart, Product $product, App $app)
    {
        $this->_cart = $cart;
        $this->_product = $product;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $cart = $this->_cart->where('object_id', $request->user()->id)->with('food', 'product')->latest()->get();
            if ($cart->isEmpty() == true) {
                return response()->json([
                    'error' => true,
                    'message' => 'No cart item found'
                ], HttpStatusCode::NOT_FOUND);
            }
            $total = $this->_cart->where('object_id', $request->user()->id)->sum('price');

            return response()->json(['error' => false, 'cart' => $cart, 'total_price' => $total]);
        } catch (\Exception $e) {
            Log::error('CartController -> getCart', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * add product to cart
     * 
     * @return JsonResponse
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'quantity' => 'integer'
        ]);

        if (isset($request->product_id)) {
            $type = 'product_id';
        }

        if (isset($request->food_id)) {
            $type = 'food_id';
        }

        if (!isset($type)) {
            return response()->json([
                'error' => true,
                'message' => 'food_id or product_id is required'
            ], HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
        try {
            $product =  $this->_product->whereType($type == 'product_id' ? ProductType::GROCERY : ProductType::FOOD)->find($type == 'product_id' ? $request->product_id : $request->food_id);

            if ($product == null) {
                return response()->json([
                    'error' => true,
                    'message' => $type == 'product_id' ? 'Product not found' : 'Food not found'
                ], HttpStatusCode::NOT_FOUND);
            }

            $cart = $this->_cart->where('object_id', $request->user()->id)->where($type, $type == 'product_id' ? $request->product_id : $request->food_id)->first();

            if ($cart == null) {
                $cart = $this->_cart->create([
                    'object_id' => $request->user()->id,
                    $type =>  $type == 'product_id' ? $request->product_id : $request->food_id,
                    'price' => $product->price,
                    'quantity' => isset($request->quantity) ? $request->quantity : 1
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => $type == 'product_id' ? "Product already in cart" : 'Food already in cart'
                ], HttpStatusCode::CONFLICT);
            }

            $total = $this->_cart->where('object_id', $request->user()->id)->sum('price');

            return response()->json([
                'error' => false,
                'message' => 'Added to Cart',
                'data' => $cart->load($type == 'product_id' ? 'product' : 'food'),
                'total_price' => $total
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('CartController -> addToCart', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * quantity update
     * @param Cart $cart
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function updateQuantity(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required'
        ]);
        try {

            if ($cart->object_id != $request->user()->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Cart not found'
                ], HttpStatusCode::NOT_FOUND);
            }

            $cart->load(['product', 'food']);
            $price = null;
            if ($cart->product !== null) {
                $price = $cart->product->price;
            }
            if ($cart->food !== null) {
                $price = $cart->food->price;
            }

            $cart->quantity = $request->quantity;
            $cart->price = $request->quantity * $price;
            $cart->save();

            $total = $this->_cart->where('object_id', $request->user()->id)->sum('price');

            return response()->json([
                'error' => false,
                'data' => $cart->load(['product', 'food']),
                'total_price' => $total,
                'message' => 'Quantity updated'
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('CartController -> updateQuantity', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * remove product from cart
     * 
     * @return JsonResponse
     */
    public function removeFromCart(Request $request, Cart $cart)
    {
        try {
            if ($cart->object_id != $request->user()->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Cart itme not found'
                ], HttpStatusCode::NOT_FOUND);
            }
            $cart->delete();

            $total = $this->_cart->where('object_id', $request->user()->id)->sum('price');

            return response()->json([
                'error' => false,
                'data' => $cart,
                'total_price' => $total,
                'message' => 'Removed from Cart'
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('CartController -> removeFromCart', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
