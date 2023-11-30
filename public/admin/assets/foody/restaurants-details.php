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
               <a   class="dis-column" href="./my-trips.php">
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
      <div class="site-wrapper animsition content-box z-1 p-0">
      <section class="inner-page-hero bg-image">
         <div class="profile">
            <div class="container">
               <div class="row">
                  <!-- <div class="col-xs-12 col-sm-12  col-md-4 col-lg-4 profile-img">
                     <div class="image-wrap">
                        <figure><img src="../img/foody/profile-image.jpg" alt="Profile Image"></figure>
                     </div>
                     </div> -->
                  <div class="col-xs-12 col-sm-12 col-md-12 col-xl-5 profile-desc">
                     <div class="pull-left right-text white-txt">
                        <div class="image-wrap">
                           <figure><img src="../img/foody/profile-image.jpg" alt="Profile Image"></figure>
                        </div>
                        <h4 class="txt-white">Maenaam Thai Restaurant</h4>
                        <p>Burgers, American, Sandwiches, Fast Food, BBQ</p>
                        <ul class="nav nav-inline">
                           <li class="nav-item"> <a class="nav-link active" ><i class="fa fa-check"></i> Min $ 10,00</a> </li>
                           <li class="nav-item"> <a class="nav-link" ><i class="fa fa-motorcycle"></i> 30 min</a> </li>
                           <li class="nav-item ratings">
                              <a class="nav-link" > <span>
                              <i class="fa fa-star"></i>
                              <i class="fa fa-star"></i>
                              <i class="fa fa-star"></i>
                              <i class="fa fa-star"></i>
                              <i class="fa fa-star-o"></i>
                              </span> </a>
                           </li>
                           <li class="nav-item">
                              <a class="btn btn-small btn-green2">Open</a>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
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
         <!-- Address Modal -->
         <!-- Cart Modal -->
         <div class="modal" id="cartModal">
            <div class="modal-dialog dis-center">
               <div class="modal-content min-50vw">
                  <!-- Cart Header -->
                  <div class="modal-header">
                     <h4 class="modal-title">Cart</h4>
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <!-- Cart body -->
                  <div class="modal-body">
                     <!-- Empty Cart -->
                     <div class="widget widget-cart empty-cart d-none">
                        <div class="widget-heading bg-white b-0 p-0 dis-column">
                           <div class="clearfix"></div>
                           <img class="w-50 mt-3" src="../img/foody/empty-cart.svg">
                        </div>
                     </div>
                     <!-- Empty Cart -->
                     <div class="row m-0">
                        <div class="widget-body col-sm-12 col-md-12 col-xl-6 height30vh">
                           <ul class="list-inline w-100 order-row dis-row">
                              <li class="list-inline-item">
                                 <h5 class="price ">Cheese Pizza</h5>
                                 <p class="txt-color1">$ 19.99</p>
                              </li>
                              <li class="list-inline-item quantity ">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                              </li>
                              <li class="list-inline-item mr-3">
                                 <h5 class="price ">$ 19.99</h5>
                              </li>
                              <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li>
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
                              <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li>
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
                              <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li>
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
                              <li class="list-inline-item "><a ><i class="fa fa-times-circle font-16 primary-color c-pointer"></i></a> </li>
                           </ul>
                        </div>
                        <div class="cart-totals-fields col-sm-12 col-md-12 col-xl-6">
                           <table class="table">
                              <tbody>
                                 <tr>
                                    <td>Cart Subtotal</td>
                                    <td>$29.00</td>
                                 </tr>
                                 <tr>
                                    <td>Shipping &amp; Handling</td>
                                    <td>$2.00</td>
                                 </tr>
                              </tbody>
                           </table>
                           <div class="widget-body red">
                              <div class="price-wrap text-center">
                                 <i class="material-icons address-category">attach_money</i>
                                 <p class="txt-white">GRAND TOTAL</p>
                                 <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                                 <p class="txt-white">Free Shipping</p>
                                 <!-- <a id="checkout" href="./checkout.php" class="btn btn-primary bg-white primary-color">Checkout <i class="fa fa-arrow-right" aria-hidden="true"></i></a> -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Cart footer -->
                  <div class="modal-footer">
                     <a  class="btn btn-primary btn-block" href="./checkout.php">Checkout <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                  </div>
               </div>
            </div>
         </div>
         <!-- Cart Modal -->
      </section>
      <div class="page-wrapper">
         <section class="restaurants-page">
            <div class="result-show bg-white">
               <div class="container">
                  <div class="row">
                     <!-- <div class="col-sm-12 col-lg-4">
                        <span class="fa fa-location-arrow" style=" position: absolute; left: 5%; top: 25%;color: #495057;font-size: 18px;"></span>
                        <input class="form-control" type="text" name="order-location" placeholder="Search for area, street name.." required>
                        <span><i class="material-icons" style=" position: absolute; right: 5%; top: 25%;color: #495057;font-size: 18px;cursor: pointer;">my_location</i> </span>
                        </div> -->
                     <div class="col-sm-6 col-md-3 col-xl-3  dis-center p-0">
                        <div class="location-section m-0"  data-toggle="modal" data-target="#addressModal">
                           <span class="location-title">
                           <span class="landmark">San Fransico</span>
                           </span><span class="city"> Los Angles, USA</span>
                           <span class="fa fa-angle-down arrow-icon"></span>
                        </div>
                        <!-- <div class="location-section ">
                           <span class="location-title">
                              <span class="landmark">Velachery</span>
                           </span><span class="city"> Choose Location</span>
                           <span class="fa fa-angle-down arrow-icon"></span>
                           </div> -->
                     </div>
                     <div class="col-sm-6 col-md-3 col-xl-3 ">
                        <!-- <span class="fa fa-search" style=" position: absolute; left: 8.5%; top: 32%;color: #495057;font-size: 12px;"></span> -->
                        <input class="form-control" type="text" name="search" placeholder="Search for Items.." required>
                     </div>
                     <div class="col-sm-6 col-md-3 col-xl-3  dis-center">
                        <!-- <span class="fa fa-filter" style=" position: absolute; left: 8.5%; top: 32%;color: #495057;font-size: 12px;"></span> -->
                        <select class="form-control" name="filter" id="">
                           <option value="en" selected="">FIlter</option>
                           <option value="ar">Filter</option>
                        </select>
                     </div>
                     <div class="col-sm-6 col-md-3 col-xl-3 p-0 dis-flex-end cart" >
                        <!-- <h6>Cart</h6> -->
                        <i class="material-icons " data-toggle="modal" data-target="#cartModal" >shopping_cart</i>
                        <span class="cart-count">3</span>
                     </div>
                  </div>
               </div>
               <div class="row bg-white p-3 m-0">
                  <div class="col-md-12 col-sm-12 col-xl-2">
                     <div class="sidebar left">
                        <div class="widget style2 quick_filters">
                           <h4 class="widget-title2 sudo-bg-red" itemprop="headline">Recommended</h4>
                           <div class="widget-data">
                              <ul>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-2"><label for="filt1-2">Main Course</label></span></li>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-3"><label for="filt1-3">Beverages</label></span></li>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-4"><label for="filt1-4">Deserts</label></span></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12 col-xl-10">
                     <div class="">
                        <div class="row">
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left restaurant-list">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food1.jpg" alt="Food logo">
                                       <span class=" rating">
                                       
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food1.jpg" alt="Food logo">
                                       <span class=" rating">
                                     
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food3.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food1.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food2.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food3.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food1.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-6 col-xl-4 text-xs-center text-sm-left">
                              <div class=" bg-gray restaurant-entry food-item-wrap">
                                 <div class="entry-logo figure-wrap">
                                    <a class="img-fluid" >
                                       <img src="../img/foody/food2.jpg" alt="Food logo">
                                       <span class=" rating">
                                      
                                       </span>
                                 </div>
                                 <!-- end:Logo -->
                                 <div class="entry-dscr">
                                 <h6><a >Veg Extravaganza</a></h6>
                                 <ul class="list-inline">
                                 <li class="list-inline-item"><h5 class="price pull-left">$ 19.99</h5> </li>
                                 <li class="list-inline-item quantity pull-right d-none">
                                 <span class="add" value="-" id="moins" onclick="minus()">-</span>
                                 <input class="form-control text-center" type="" size="25" value="1" id="count">
                                 <span class="minus" value="+"  id="plus" onclick="plus()">+</span>
                                 </li class="addCart">
                                 <li class="list-inline-item pull-right add-item"> <a data-toggle="modal" data-target="#myModal" href="profile.php" class="btn btn-secondary">Add</a> </li>
                                 </ul>
                                 </div>
                                 <!-- end:Entry description -->
                              </div>
                           </div>
                        </div>
                        <!--end:row -->
                     </div>
                     <!-- end:Restaurant entry -->
                  </div>
                  <!-- end:Bar -->
                  <!-- <div class="col-xs-12 col-md-12 col-lg-3 col-xl-3">
                     <div class="sidebar-wrap">
                        <div class="widget widget-cart empty-cart">
                           <div class="widget-heading bg-white b-0 p-0">
                              <h4 class="widget-title2 sudo-bg-red">
                              Cart Empty
                              </h3>
                              <div class="clearfix"></div>
                              <img class="w-100 mt-3" src="../img/foody/empty-cart.svg">
                           </div>
                        </div>
                        <div class="widget widget-cart added-cart d-none bg-white">
                           <div class="widget-heading p-0">
                              <h4 class="widget-title2 sudo-bg-red ">
                              Your Cart
                              </h3>
                              <p>2 items</p>
                              <div class="clearfix"></div>
                           </div>
                           <div class="order-row bg-white">
                              <div class="widget-body">
                                 <div class="title-row">Cheese Pizza<a ><i class="fa fa-trash pull-right"></i></a></div>
                                 <div class="form-group row no-gutter flex-nowrap">
                                    <div class="col-xs-8 w-100">
                                       <select class="form-control b-r-0" id="exampleSelect1">
                                          <option>Size SM</option>
                                          <option>Size LG</option>
                                          <option>Size XL</option>
                                       </select>
                                    </div>
                                    <div class="col-xs-4">
                                       <input class="form-control" type="number" value="2" id="example-number-input"> 
                                    </div>
                                 </div>
                              </div>
                              <div class="widget-body">
                                    <div class="title-row">Burger<a ><i class="fa fa-trash pull-right"></i></a></div>
                                    <div class="form-group row no-gutter flex-nowrap">
                                       <div class="col-xs-8 w-100">
                                          <select class="form-control b-r-0">
                                             <option>Size SM</option>
                                             <option>Size LG</option>
                                             <option>Size XL</option>
                                          </select>
                                       </div>
                                       <div class="col-xs-4">
                                          <input class="form-control" value="4" id="quant-input"> 
                                       </div>
                                    </div>
                                 </div>
                           </div>
                          
                           <div class="widget-body red">
                              <div class="price-wrap text-center">
                                 <p class="txt-white">TOTAL</p>
                                 <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                                 <p class="txt-white">Free Shipping</p>
                                 <a id="checkout" href="./checkout.php" class="btn btn-primary bg-white primary-color">Checkout <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                     </div> -->
                  <!-- end:Right Sidebar -->
               </div>
         </section>
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
         
          // Input Checked
         //  $(document).ready(function(){
         //  $('input:checkbox').click(function() {
         //    $('input:checkbox').not(this).prop('checked', false);
         //  });
         //  });
         
         // JS count plus/minus
         var count = 1;
         var countEl = document.getElementById("count");
         function plus(){
            count++;
            countEl.value = count;
         }
         function minus(){
            if (count > 1) {
            count--;
            countEl.value = count;
            }  
         }
         
         
         
          $('#add_cart').click(function() {
            $('.added-cart').removeClass('d-none');
            $('.empty-cart').addClass('d-none');
          });
         
          $('.add-item').click(function() {
            $('.quantity').removeClass('d-none');
            $('.add-item').addClass('d-none');
          });
         
          // $('#checkout').click(function() {
          //   $('.payment-sec').removeClass('d-none');
          //   $('.site-wrapper').addClass('d-none');
          // });
          
      </script>
   </body>
</html>