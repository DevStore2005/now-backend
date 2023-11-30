<?php

namespace App\Http\Controllers\RestaurantGrocery;

use Auth;
use Session;
use App\Models\Food;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

use App\Utils\UserType;
use App\Models\Category;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Utils\BusinessProfileType;
use App\Http\Middleware\Restaurant;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

  /**
   *  @var User $_user
   *  @var BusinessProfile $_businessProfile
   *  @var Product $_product 
   *  @var Common $_common 
   *  @var String $_environment
   *  @var Category $_category
   *  @var Order $_order
   */
  private $_product, $_common, $_environment, $_category, $_user, $_businessProfile, $_order;


  /**
   * Create a new controller instance.
   * @param User $user
   * @param BusinessProfile $businessProfile
   * @param  Product $product
   * @param  Common $common
   * @param  App $app
   * @return Category $category
   * @return Order $order
   * @return void
   */
  public function __construct(Product $product, Common $common, App $app, Category $category, User $user, BusinessProfile $businessProfile, Order $order)
  {
    $this->_product = $product;
    $this->_common = $common;
    $this->_environment = $app::environment();
    $this->_category = $category;
    $this->_user = $user;
    $this->_businessProfile = $businessProfile;
    $this->_order = $order;
  }

  public function dashboard(Request $request)
  {
    $user = $request->user();
    $userRole = $user->role;
    $id = isset($user->business_profile) ? $user->business_profile->id : null;
    $type = $userRole == UserType::RESTAURANT_OWNER ? 'restaurant_id' : 'grocer_id';

    $total_product = $id !== null ? $this->_product->whereStatus('Active')->where($type, $id)->count() : 0;
    $orders = $this->_order->where($type, $id)->with(['food', 'product'])->get();
    return view('restaurant_grocery.dashboard.dashboard', compact('total_product', 'orders'));
  }


  public function profileSetting(Request $request)
  {
    switch ($request->method()) {
      case 'POST':

        $user = $this->_user->find($request->user()->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->save();
        $type = null;
        if ($request->user()->role == UserType::RESTAURANT_OWNER) {
          $type = BusinessProfileType::RESTAURANT;
        }

        if ($request->user()->role == UserType::GROCERY_OWNER) {
          $type = BusinessProfileType::GROCERY;
        }

        $profile = $this->_businessProfile->where('user_id', $request->user()->id)->where('type', $type)->first();
        if (!$profile) {
          $data = [
            'name' => $request->name,
            'user_id' => $request->user()->id,
            'website' => $request->website,
            'address' => $request->address,
            'city' => $request->city,
            'type' => $type,
            'country' => $request->country,
            'state' => $request->state,
            'business_phone' => $request->business_phone,
            'restaurant_type' => $request->restaurant_type,
            'about' => $request->about,
          ];
          $this->_businessProfile->insert($data);
        } else {
          $profile->name = $request->name;
          $profile->website = $request->website;
          $profile->address = $request->address;
          $profile->state = $request->state;
          $profile->country = $request->country;
          $profile->country = $request->country;
          $profile->city = $request->city;
          $profile->business_phone = $request->business_phone;
          $profile->restaurant_type = $request->restaurant_type;
          $profile->about = $request->about;
        }
        if ($request->hasfile('profile_image')) {
          $dirName = $request->user()->role == UserType::RESTAURANT_OWNER ? 'restaurant' : 'grocer';
          if ($profile->profile_image) {
            $this->_common->delete_media($profile->profile_image);
          };
          $profile->profile_image  = $this->_common->store_media($request->file('profile_image'), $dirName . '/profile');
        }
        if ($request->hasfile('cover_image')) {
          $dirName = $request->user()->role == UserType::RESTAURANT_OWNER ? 'restaurant' : 'grocer';
          if ($profile->cover_image) {
            $this->_common->delete_media($profile->cover_image);
          };
          $profile->cover_image  = $this->_common->store_media($request->file('cover_image'), $dirName . '/profile');
        }
        if($profile){
          $profile->save();
        }

        return back()->with('success_message', 'Updated Sucessfully');

      case 'GET':
        $user = User::whereId($request->user()->id)->with('business_profile')->first();

        return view('restaurant_grocery.dashboard.profile-settings', compact('user'));
      default:
        // invalid request
        break;
    }
  }


  public function changePassword(Request $request)
  {
    switch ($request->method()) {
      case 'POST':

      case 'GET':

        return view('restaurant_grocery.dashboard.changepassword');
      default:
        // invalid request
        break;
    }
  }
}
