<?php

namespace App\Http\Controllers\RestaurantGrocery\Grocery;

use Auth;
use App\Models\Product;
use App\Utils\MyAppEnv;
use App\Utils\ProductType;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * @var Product $_product
     * @var Common $_common
     * @var String $_environment
     * @var Category $_category
     */
    private $_product, $_common, $_environment, $_category;


    /**
     * Create a new controller instance.
     * @param Product $product
     * @param Common $common
     * @param App $app
     * @return Category $category
     * @return void
     */
    public function __construct(Product $product, Common $common, App $app, Category $category)
    {
        $this->_product = $product;
        $this->_common = $common;
        $this->_environment = $app::environment();
        $this->_category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = $this->_product->where('status', 'Active')->where('grocer_id', auth()->user()->business_profile->id)->get();
            return view('restaurant_grocery.dashboard.productlist', compact('products'));
        } catch (\Exception $e) {
            Log::error(['name' => 'UserController -> index', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        switch ($request->method()) {
            case 'POST':
                try {
                    $product = $this->_product->create([
                        'name' => $request->name,
                        'price' => $request->price,
                        'description' => $request->description,
                        'grocer_id' => $request->user()->business_profile->id,
                        'quantity' => $request->quantity,
                        'type' => ProductType::GROCERY,
                        'category_id' => $request->category_id || null,
                        'country_id' => isset($request['default_country']) ? $request['default_country']['id'] : null,
                    ]);
                    if ($request->hasfile('image')) {
                        $product->image = $this->_common->store_media($request->file('image'), 'products/', $request->user()->role . time() . rand() . $request->user()->id);
                        $product->save();
                    }
                    return redirect()->route('grocer.product.index')->with('success_message', 'Product added successfully');
                } catch (\Exception $e) {
                    Log::error(['name' => 'UserController -> index', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
                    return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
                }
            case 'GET':

                return view('restaurant_grocery.dashboard.uploadproducts')->with('categories', $this->_category->whereType(ProductType::GROCERY)->get());;
            default:
                // invalid request
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        switch ($request->method()) {
            case 'POST':
                try {
                    $product = $this->_product->where('status', 'Active')->find($id);
                    $product->name = $request->name;
                    $product->price = $request->price;
                    $product->description = $request->description;
                    $product->quantity = $request->quantity;

                    if ($request->hasfile('image')) {
                        if ($product->image != null) {
                            $this->_common->delete_media($product->image);
                        }
                        $product->image = $this->_common->store_media($request->file('image'), 'foods/', $request->user()->role . time() . rand() . $request->user()->id);
                    }
                    $product->country_id = isset($request['default_country']) ? $request['default_country']['id'] : $product->country_id;
                    $product->save();
                    return redirect()->back()->with('success_message', 'Updated Sucessfully');
                } catch (\Throwable $th) {
                    //throw $th;
                }


            case 'GET':
                $product = Product::where('status', 'Active')->find($id);

                return view('restaurant_grocery.dashboard.editproduct', compact('product'));
            default:
                // invalid request
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('status', 'Active')->find($id);
        $product->status = 'Inactive';
        $product->save();
        return back()->with('message', 'Deleted Sucessfully');
    }
}
