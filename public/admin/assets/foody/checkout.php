<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset='utf-8'>
      <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
      <meta content='website' property='og:type'>
      <title>Foody</title>
      <!-- Bootstrap core CSS -->
      <link href="../css/bootstrap.min.css" rel="stylesheet">
      <link href="../css/style.css" rel="stylesheet">
      <link href="./foody.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
         rel="stylesheet">
      <link rel='stylesheet' type='text/css' href='../css/font-awesome.min.css'/>
   <body>
      <div id="mySidenav" class="sidenav">
         <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
         <a href="./restaurants.php">Dashboard</a>
         <li class="nav-item dropdown">
            <a class="nav-link" id="navbarDropdown1" data-target="#" href="./my-orders.php" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">History <i class="fa fa-caret-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
               <li><a class="dropdown-item" href="../taxi-booking/my-trips.php">Taxi</a></li>
               <li><a class="dropdown-item" href="./my-orders.php">Food</a></li>
               <li><a class="dropdown-item" href="../services/my-services.php">Services</a></li>
            </ul>
         </li>
         <a href="../taxi-booking/profile.php">Profile</a>
         <a href="../taxi-booking/payment.php">My Wallet</a>
      </div>
      <nav class="menu" >
         <div id="sidebarCollapse" class="active">
            <span></span>
            <span></span>
            <span></span>
         </div>
         <div id="header">
            <div class="menu-item menu-one menu-active ">
               <a  class="dis-column" href="./restaurants.php">
                  <div class="dis-center"><i class="material-icons">dashboard</i></div>
                  <span>Dashboard</span>
               </a>
            </div>
            <div class="menu-item menu-two">
               <a   class="dis-column" href="./my-orders.php">
                  <div class="dis-center"><i class="material-icons">history</i></div>
                  <span>History</span>
               </a>
            </div>
            <div class="menu-item menu-three ">
               <a   class="dis-column" href="../taxi-booking/profile.php">
                  <div class="dis-center"><i class="material-icons">account_box</i></div>
                  <span>Profile</span>
               </a>
            </div>
            <div class="menu-item menu-four">
               <a  class="dis-column" href="../taxi-booking/payment.php">
                  <div class="dis-center"><i class="material-icons">account_balance_wallet</i></div>
                  <span>Wallet</span>
               </a>
            </div>
            <div class="menu-item menu-four">
               <a  class="dis-column" href="../services.html">
                  <div class="dis-center"><i class="material-icons">build</i></div>
                  <span>Book Services</span>
               </a>
            </div>
         </div>
      </nav>
      <?php include ('../header.php'); ?>
      <!-- Refer Modal -->
      <div class="modal" id="referModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Refer Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Refer A Friend</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Refer body -->
               <div class="modal-body">
                  <div class="col-md-12 col-xl-12 col-xs-12 p-0 mb-4">
                     <div class=" top small-box green mb-2">
                        <div class="left">
                           <h2>Your Referral Code</h2>
                           <h4><i class="material-icons">card_giftcard</i></h4>
                           <h1>284295</h1>
                        </div>
                        <div class="sub-box dis-column">
                           <span class="font-12 txt-white">Referral Count: <strong>0</strong></span>
                           <span class="font-12 float-right txt-white">Referral Amount: <strong>0</strong></span>
                        </div>
                     </div>
                  </div>
                  <div class="dis-column col-lg-12 col-md-12 col-sm-12 p-0 bor-bottom mb-4 pb-4">
                     <h5 class="text-center">Refer Your Friends & Earn upto 20%</h5>
                     <p class="text-center">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p>
                     <input class="form-control" type="text" name="pickup-location" placeholder="Enter Email" required>
                     <a  class="btn btn-primary">Invite</a>
                  </div>
                  <h5 class="text-center mb-2">Refer Your Friends via Social Media</h5>
                  <div class="social-login">
                     <a href="#"><span class=" fb-bg "><i class="fa fa-facebook-square "></i></span></a>
                     <a href="#"><span class=" fb-bg"><i class="fa fa-twitter-square "></i></span></a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Refer Modal -->
      <!-- Schedule Order Modal -->
      <div class="modal" id="scheduleModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Schedule Order Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Schedule a Ride</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Schedule Order body -->
               <div class="modal-body">
                  <input class="form-control date-picker" type="date" name="date" onkeypress="return false">
                  <input class="form-control mt-3  time-picker" type="text" name="time" onkeypress="return false">
               </div>
               <!-- Schedule Order footer -->
               <div class="modal-footer">
                  <a  class="btn btn-primary btn-block" data-toggle="modal" data-target="#scheduleModal">Schedule later <i class="fa fa-clock-o" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
      <!-- Schedule RiOrderde Modal -->
      <!-- Emergency Contact Modal -->
      <div class="modal" id="contactModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Emergency Contact Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Contact</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Emergency Contact body -->
               <div class="modal-body">
                  <select class="form-control" name="cancel" id="contact">
                     <option value="en" selected="">Emergency Contact</option>
                     <option value="ar">Lorem Ipsum</option>
                     <option value="en">Lorem Ipsum</option>
                     <option value="ar">Lorem Ipsum</option>
                  </select>
                  <p class="mt-3">Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
               </div>
               <!-- Emergency Contact footer -->
               <div class="modal-footer">
                  <a  class="btn btn-primary btn-block" data-toggle="modal" data-target="#contactModal">Submit <i class="fa fa-check" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
      <!-- Emergency Contact Modal -->
      <!-- Address Modal -->
      <div class="modal" id="addressModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Address Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Choose Location</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Address body -->
               <div class="modal-body">
                  <div class="col-sm-12 col-lg-12">
                     <span class="fa fa-location-arrow" style=" position: absolute; left: 5%; top: 25%;color: #495057;font-size: 18px;"></span>
                     <input class="form-control" type="text" name="order-location" placeholder="Search for area, street name.." required>
                     <span><i class="material-icons" style=" position: absolute; right: 5%; top: 25%;color: #495057;font-size: 18px;cursor: pointer;">my_location</i> </span>
                  </div>
                  <div class="address-block ">
                     <h5 >SAVED ADDRESSES</h5>
                     <div class="address-set red">
                        <i class="material-icons address-category">home</i>
                        <div class="">
                           <h5 class="">Home</h5>
                           <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                        </div>
                     </div>
                     <div class="address-set red">
                        <i class="material-icons address-category">work</i>
                        <div class="">
                           <h5 class="">Work</h5>
                           <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                        </div>
                     </div>
                     <div class="address-set red">
                        <i class="material-icons address-category">location_on</i>
                        <div class="">
                           <h5 class="">Other</h5>
                           <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                        </div>
                     </div>
                     <!-- <a class="btn btn-secondary">View More</a> -->
                  </div>
               </div>
               <!-- Address footer -->
               <!-- <div class="modal-footer">
                  <a  class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModal">Schedule later</a>
                  </div> -->
            </div>
         </div>
      </div>
      <!-- Addon Modal -->
      <div class="modal" id="myModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Addon Header -->
               <div class="modal-header dis-end b-b">
                  <h4 class="modal-title">Veg Extravaganza</h4>
                  <span>$19.99</span>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Addon body -->
               <div class="modal-body">
                  <div class="col-sm-12 p-0">
                     <div class="widget bg-white col-sm-12 p-0">
                        <h4>Add-On Fries <small>(optional)</small></h4>
                        <div class="dis-end">
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-1"><label for="addon-1" class="mr-2">Medium Fries </label> $3</span>
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-2"><label for="addon-2" class="mr-2">Large Fries </label> $5</span>
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-3"><label for="addon-3" class="mr-2">Regular Fries </label> $2</span>
                        </div>
                     </div>
                     <div class="widget bg-white col-sm-12 p-0 mt-3">
                        <h4>Extras <small>(optional)</small></h4>
                        <div class="dis-end">
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-4"><label for="addon-4" class="mr-2">Medium Fries </label> $3</span>
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-5"><label for="addon-5" class="mr-2">Large Fries </label> $5</span>
                           <span class="radio-box mt-2"><input type="radio" name="filter" id="addon-6"><label for="addon-6" class="mr-2">Regular Fries </label> $2</span>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Addon footer -->
               <div class="modal-footer b-0">
                  <a id="add_cart" data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-block" ><span class=""></span>Add  <i class="fa fa-plus" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
      <!-- Addon Modal -->
      <!-- Address Modal -->
      <div class="content-box">
         <div class="widget clearfix bg-white">
            <!-- /widget heading -->
            <!-- <div class="widget-heading">
               <h3 class="widget-title text-dark">
                  ORDER #109563986
               </h3>
               <p class="m-0">2 items, $168 | 6:00PM</p>
               <div class="clearfix"></div>
               </div> -->
            <div class="widget-body">
               <form method="post" action="#">
                  <div class="row">
                     <div class="col-sm-12 col-md-12 col-xl-8 margin-b-30 checkout-section">
                        <div class="menu-widget m-b-30 select-address" >
                           <div class="widget-heading c-pointer"  data-toggle="collapse" data-target="#popular1" aria-expanded="true">
                              <i class="material-icons text-dark">location_on</i>
                              <h3 class="widget-title text-dark">
                                 Select Address
                              </h3>
                              <div class="clearfix"></div>
                           </div>
                           <div class="collapse in show white" id="popular1">
                              <div class="food-item white b-0">
                                 <div class="row m-0">
                                    <div class="rest-descr ">
                                       <div class="address-block b-0">
                                          <!-- <h5 >SAVED ADDRESSES</h5> -->
                                          <div class="flex-container">
                                             <div class="address-set col-sm-12 col-lg-5 col-xl-4 red">
                                                <i class="material-icons address-category">home</i>
                                                <div class="">
                                                   <h5 class="Ku2oK">Home</h5>
                                                   <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                                                   <a class="btn btn-primary bg-white primary-color btn-block">Delivery Here <i class="fa fa-check" aria-hidden="true"></i></a>
                                                </div>
                                             </div>
                                             <div class="address-set col-sm-12 col-lg-5 col-xl-4 red">
                                                <i class="material-icons address-category">work</i>
                                                <div class="">
                                                   <h5 class="Ku2oK">Work</h5>
                                                   <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                                                   <a class="btn btn-primary bg-white primary-color btn-block">Delivery Here <i class="fa fa-check" aria-hidden="true"></i></a>
                                                </div>
                                             </div>
                                             <div class="address-set col-sm-12 col-lg-5 col-xl-4 red">
                                                <i class="material-icons address-category">location_on</i>
                                                <div class="">
                                                   <h5 class="Ku2oK">Other</h5>
                                                   <p >Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                                                   <a class="btn btn-primary bg-white primary-color btn-block">Delivery Here <i class="fa fa-check" aria-hidden="true"></i></a>
                                                </div>
                                             </div>
                                             <div class="address-set col-sm-12 col-lg-5 col-xl-4 dis-ver-center red min-23vh">
                                                <i class="material-icons address-category">location_on</i>
                                                <a class="btn btn-primary bg-white primary-color btn-block" data-toggle="modal" data-target="#addressModal">Add New Address <i class="fa fa-plus" aria-hidden="true"></i></a>
                                             </div>
                                             <!-- <a class="btn btn-secondary">View More</a> -->
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="menu-widget m-b-30 d-none delivery-section col-sm-12 col-md-12 col-xl-12 bg-none">
                           <div id="popular2">
                              <div class="food-item white b-0">
                                 <div class="row">
                                    <div class="rest-descr ">
                                       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2970.652981076582!2d-87.63116368463953!3d41.87881207334865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880e2cbcb88d3b45%3A0x37ef3145a8a1c23d!2sUnited+States+Attorney&#39;s+Office!5e0!3m2!1sen!2sin!4v1549101057336" width="0" height="0" frameborder="0" style="border:0" allowfullscreen></iframe>
                                       <div class="form-register w-100">
                                          <div class="dis-center ">
                                             <div class="col-lg-6 col-sm-12 dis-row pickup-details red">
                                                <div class="dis-column col-lg-6 col-md-6 col-sm-12 p-0">
                                                   <img class="user-img" src="../img/taxi/user.jpg">
                                                   <h5 class="txt-white">Frank Provider</h5>
                                                   <div class="rating-outer">
                                                      <span class="txt-white" style="cursor: default;">
                                                         4.6
                                                         <div class="rating-symbol" style="display: inline-block; position: relative;">
                                                            <div class="fa fa-star-o" style="visibility: hidden;"></div>
                                                            <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                                         </div>
                                                      </span>
                                                      <input type="hidden" class="rating" value="1" disabled="disabled">
                                                   </div>
                                                </div>
                                                <div class="dis-column col-lg-6 col-md-6 col-sm-12 p-0">
                                                   <span data-toggle="modal" data-target="#contactModal" class="pull-right c-pointer txt-white dis-row"> <i class="material-icons mr-1 txt-white">phone</i> Emergency Contact</span>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="steps clearfix">
                                             <ul role="tablist">
                                                <li role="tab" aria-disabled="false" class="first" aria-selected="true">
                                                   <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                                      <span class="current-info audible"> </span>
                                                      <div class="title">
                                                         <span class="step-icon"><img src="../img/svg/order-received.svg"></span>
                                                         <span class="step-text">Order Received</span>
                                                      </div>
                                                   </a>
                                                </li>
                                                <li role="tab" aria-disabled="false" class="second" aria-selected="false">
                                                   <a id="form-total-t-1" href="#form-total-h-1" aria-controls="form-total-p-1">
                                                      <div class="title">
                                                         <span class="step-icon"><img src="../img/svg/meal.svg"></span>
                                                         <span class="step-text">Cooking</span>
                                                      </div>
                                                   </a>
                                                </li>
                                                <li role="tab" aria-disabled="false" class="third" aria-selected="false">
                                                   <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                                      <div class="title">
                                                         <span class="step-icon"><img src="../img/svg/scooter.svg"></span>
                                                         <span class="step-text">On The Way</span>
                                                      </div>
                                                   </a>
                                                </li>
                                             </ul>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="menu-widget m-b-30 d-none">
                           <div class="widget-heading c-pointer"  data-toggle="collapse" data-target="#popular3" aria-expanded="true">
                              <h3 class="widget-title text-dark">
                                 Order Picked Up 
                              </h3>
                              <div class="clearfix"></div>
                           </div>
                           <div class="collapse in white" id="popular3">
                              <div class="food-item white b-0">
                                 <div class="row">
                                    <div class="rest-descr ">
                                       <p> Your order has been accepted by the restaurent</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-12 col-xl-4 bor-dashed pt-2 pb-2 cart-summary">
                        <h4 class="modal-title">Cart Summary</h4>
                        <div class="cart-totals margin-b-20 height30vh">
                           <!-- <div class="cart-totals-title">
                              <h4>Cart Summary</h4> </div> -->
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1 m-0">$ 19.99</p>
                                 <a class="text-color c-pointer" data-toggle="modal" data-target="#myModal">Customize <i class="fa fa-cogs" aria-hidden="true"></i></a>
                              </li>
                              <li class="list-inline-item quantity ">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 19.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1 m-0">$ 19.99</p>
                                 <a class="text-color c-pointer" data-toggle="modal" data-target="#myModal">Customize <i class="fa fa-cogs" aria-hidden="true"></i></a>
                              </li>
                              <li class="list-inline-item quantity ">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 19.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1 m-0">$ 19.99</p>
                                 <a class="text-color c-pointer" data-toggle="modal" data-target="#myModal">Customize <i class="fa fa-cogs" aria-hidden="true"></i></a>
                              </li>
                              <li class="list-inline-item quantity ">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 19.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                        </div>
                        <div class="cart-totals-fields">
                           <table class="table">
                              <tbody>
                                 <tr>
                                    <td class="pl-0">Cart Subtotal</td>
                                    <td>$29.00</td>
                                 </tr>
                                 <tr>
                                    <td class="pl-0">Shipping &amp; Handling</td>
                                    <td>$2.00</td>
                                 </tr>
                                 <tr>
                                    <td class="txt-green pl-0">Promocode Discount</td>
                                    <td class="txt-green">$2.00</td>
                                 </tr>
                                 <tr class="promocode" data-toggle="modal" data-target="#couponModal">
                                    <td class="dis-ver-center">
                                       <img src="../img/svg/coupon.svg">
                                       <h5 class="c-pointer" >Apply Promocode</h5>
                                    </td>
                                    <td></td>
                                 </tr>
                                 <tr>
                                    <td class="pl-0 b-0">
                                       <select class="form-control w-140" name="payment_mode" id="payment_mode" >
                                          <option value="CASH" selected>Cash</option>
                                          <option value="CARD">Card</option>
                                       </select>
                                    </td>
                                 </tr>
                                 <!-- <tr>
                                    <td class="text-color"><strong>Total</strong></td>
                                    <td class="text-color"><strong class="total-price">$31.00</strong></td>
                                    </tr> -->
                              </tbody>
                           </table>
                           <div class="widget-body red">
                              <div class="price-wrap text-center">
                                 <i class="material-icons address-category">attach_money</i>
                                 <p class="txt-white">GRAND TOTAL</p>
                                 <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                                 <!-- <p class="txt-white">Free Shipping</p> -->
                                 <a data-toggle="modal" data-target="#scheduleModal" class="btn btn-primary bg-white primary-color">Schedule Order <i class="fa fa-clock-o" aria-hidden="true"></i></a>
                                 <a id="placeOrder" class="btn btn-primary bg-white primary-color">Place Order <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-12 col-xl-4 bor-dashed pt-2 pb-2 cart-details d-none">
                        <h4 class="modal-title">Order Details</h4>
                        <div class="cart-totals margin-b-20 height20vh">
                           <!-- <div class="cart-totals-title">
                              <h4>Cart Summary</h4> </div> -->
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1">$ 19.99 x 2</p>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 28.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1">$ 19.99 x 2</p>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 28.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1">$ 19.99 x 2</p>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 28.99</h5>
                              </li>
                              <!-- <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li> -->
                           </ul>
                        </div>
                        <div class="cart-totals-fields">
                           <table class="table">
                              <tbody>
                                 <tr>
                                    <td class="pl-0">Cart Subtotal</td>
                                    <td>$29.00</td>
                                 </tr>
                                 <tr>
                                    <td class="pl-0">Shipping &amp; Handling</td>
                                    <td>$2.00</td>
                                 </tr>
                                 <tr>
                                    <td class="txt-green pl-0">Promocode Discount</td>
                                    <td class="txt-green">$2.00</td>
                                 </tr>
                                 <!-- <tr>
                                    <td class="text-color"><strong>Total</strong></td>
                                    <td class="text-color"><strong class="total-price">$31.00</strong></td>
                                    </tr> -->
                              </tbody>
                           </table>
                           <div class="widget-body red">
                              <div class="price-wrap text-center">
                                 <i class="material-icons address-category">attach_money</i>
                                 <p class="txt-white">GRAND TOTAL</p>
                                 <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <!-- Coupon Modal -->
      <div class="modal" id="couponModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Coupon Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Coupon Code</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Coupon body -->
               <div class="modal-body">
                  <div class="dis-row col-lg-12 col-md-12 col-sm-12 p-0 ">
                     <div class="col-sm-8">
                        <input class="form-control" type="text" name="enter coupon" placeholder="Enter Coupon Code">
                     </div>
                     <div class="col-sm-4">
                        <a  class="btn btn-primary " data-toggle="modal" data-target="#couponModal">Apply</a>
                     </div>
                  </div>
                  <ul class="height50vh">
                     <li class="coupon-box">
                        <img src="../img/svg/coupon-outline.svg">
                        <span class="txt-red coupon-text" >Get5</span>
                        <!-- <a class="btn btn-primary pull-right">Apply</a> -->
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        <small>Valid Till: 05/10/2019</small>
                     </li>
                     <li class="coupon-box">
                        <img src="../img/svg/coupon-outline.svg">
                        <span class="txt-red coupon-text" >Get5</span>
                        <!-- <a class="btn btn-primary pull-right">Apply</a> -->
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        <small>Valid Till: 05/10/2019</small>
                     </li>
                     <li class="coupon-box">
                        <img src="../img/svg/coupon-outline.svg">
                        <span class="txt-red coupon-text" >Get5</span>
                        <!-- <a class="btn btn-primary pull-right">Apply</a> -->
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        <small>Valid Till: 05/10/2019</small>
                     </li>
                     <li class="coupon-box">
                        <img src="../img/svg/coupon-outline.svg">
                        <span class="txt-red coupon-text" >Get5</span>
                        <!-- <a class="btn btn-primary pull-right">Apply</a> -->
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        <small>Valid Till: 05/10/2019</small>
                     </li>
                     <li class="coupon-box">
                        <img src="../img/svg/coupon-outline.svg">
                        <span class="txt-red coupon-text" >Get5</span>
                        <!-- <a class="btn btn-primary pull-right">Apply</a> -->
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        <small>Valid Till: 05/10/2019</small>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <!-- Coupon Modal -->
      <!--Footer Content Start-->
      <section class="footer">
         <div class="container">
            <div class="row m-0">
               <div class="col-md-12 col-sm-12 dis-center">
                  <div class="copyright_text">
                     <p>&copy; Copyrights 2019 All Rights Reserved.</p>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- Rating Modal -->
      <div class="modal" id="rating">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Rating Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Rating</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Rating body -->
               <div class="modal-body">
                  <div class="dis-column col-lg-12 col-md-12 col-sm-12 p-0 ">
                     <div class="dis-column col-lg-6 col-md-6 col-sm-12 red">
                        <img class="user-img" src="../img/taxi/user.jpg">
                        <h5 class="txt-white">Frank Provider</h5>
                        <div class="rating-outer">
                           <span class="txt-white" style="cursor: default;">
                              4.6
                              <div class="rating-symbol" style="display: inline-block; position: relative;">
                                 <div class="fa fa-star-o" style="visibility: hidden;"></div>
                                 <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                              </div>
                           </span>
                           <input type="hidden" class="rating" value="1" disabled="disabled">
                        </div>
                     </div>
                     <div class="trip-user w-100 dis-column mt-3 pickup-details">
                        <h5>Rate Your Driver</h5>
                        <div class="rating-outer">
                           <fieldset class="rating">
                              <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                              <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                              <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                              <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                              <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
                              <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                              <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                              <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                              <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                              <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                           </fieldset>
                        </div>
                        <p></p>
                     </div>
                     <div class="comments-section field-box mt-3">
                        <textarea class="form-control" rows="4" cols="50" placeholder="Leave Your Comments..."></textarea>
                     </div>
                  </div>
               </div>
               <!-- Rating footer -->
               <div class="modal-footer">
                  <a  class="btn btn-primary btn-block " href="./restaurants.php">Submit <i class="fa fa-check-square" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
      <!-- Rating Modal -->
      <!--Footer Content End-->
      <!--/end:Site wrapper -->
      <!-- Bootstrap core JavaScript
         ================================================== -->
      <script src="../js/jquery.min.js"></script>
      <script src="../js/popper.min.js"></script>
      <script src="../js/bootstrap.min.js"></script>
      <script>
         function openNav() {
          document.getElementById("mySidenav").style.width = "100%";
          }
          
          function closeNav() {
          document.getElementById("mySidenav").style.width = "0";
          }
         
          $('#placeOrder').click(function() {
            $('.delivery-section').removeClass('d-none');
            $('.select-address').addClass('d-none');
            $('.cart-summary').addClass('d-none');
            $('.cart-details').removeClass('d-none');
            setTimeout(function(){ $(".steps .first").addClass("current"); }, 3000);
            setTimeout(function(){ $(".steps .second").addClass("current"); }, 6000);
            setTimeout(function(){ $(".steps .third").addClass("current"); }, 9000);
            setTimeout(function(){ $("#rating").show(); }, 12000);
            // $('#myModal').fadeIn(200);
          });
      </script>
   </body>
</html>