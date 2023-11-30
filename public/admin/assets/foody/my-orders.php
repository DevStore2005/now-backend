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
      <link href="../taxi-booking/css/taxi-booking.css" rel="stylesheet">
      <link rel='stylesheet' type='text/css' href='../css/bootstrap.datatable.min.css'/>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
         rel="stylesheet">
      <link rel='stylesheet' type='text/css' href='../css/font-awesome.min.css'/>
   <body>
      <div id="mySidenav" class="sidenav">
         <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
         <a href="./restaurants.php">Dashboard</a>
         <li class="nav-item dropdown">
            <a class="nav-link" id="navbarDropdown1" data-target="#" href="./my-orders.php" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">History   <i class="fa fa-caret-down"></i></a>
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
            <div class="menu-item menu-one ">
               <a  class="dis-column" href="./restaurants.php">
                  <div class="dis-center"><i class="material-icons">dashboard</i></div>
                  <span>Dashboard</span>
               </a>
            </div>
            <div class="menu-item menu-two menu-active ">
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
      <div class="subnav" style="display:none">
         <div class="menu-item menu-one ">
            <a  class="dis-column" href="../taxi-booking/my-trips.php">
               <div class="dis-center"><i class="material-icons">local_taxi</i></div>
               <span>Taxi</span>
            </a>
         </div>
         <div class="menu-item menu-two menu-active ">
            <a   class="dis-column" href="./my-orders.php">
               <div class="dis-center"><i class="material-icons">fastfood</i></div>
               <span>Food</span>
            </a>
         </div>
         <div class="menu-item menu-three ">
            <a   class="dis-column" href="../services/my-services.php">
               <div class="dis-center"><i class="material-icons">local_laundry_service</i></div>
               <span>Book Services</span>
            </a>
         </div>
      </div>
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
      <!-- View Modal 1 Starts -->
      <div class="modal " id="modal1">
         <div class="modal-dialog min-70vw">
            <div class="modal-content">
               <!-- Schedule Header -->
               <div class="modal-header">
                  <h4 class="modal-title m-0">Order Details</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Schedule body -->
               <div class="modal-body ">
                  <div class="col-lg-6 col-md-6 col-sm-12 float-left">
                     <div class="my-trip-left">
                        <h4 class="text-center">
                           <strong>
                           Food Service</strong>
                        </h4>
                        <div class="from-to row m-0">
                           <div class="from">
                              <h5>Order Location : San Francisco, USA</h5>
                              <h5>Order Items : Cheese Pizza, Cheese Pizza, Cheese Pizza</h5>
                              <h5>Order Date : 04-02-19</h5>
                              <h5>Order Time : 22:05 PM</h5>
                           </div>
                        </div>
                     </div>
                     <div class="mytrip-right">
                        <ul class="invoice">
                           <li>
                              <span class="fare">Cart Subtotal</span>
                              <span class="pricing">$10</span>
                           </li>
                           <li>
                              <span class="fare">Shipping &amp; Handling</span>
                              <span class="pricing">$25</span>
                           </li>
                           <li>
                              <span class="fare">Promocode Discount</span>
                              <span class="txt-green pricing">GET5</span>
                           </li>
                           <li>
                              <div class="widget-body red">
                                 <div class="price-wrap text-center">
                                    <i class="material-icons address-category">attach_money</i>
                                    <p class="txt-white">GRAND TOTAL</p>
                                    <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                                 </div>
                              </div>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-6 float-right">
                     <div class="map-static">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2970.652981076582!2d-87.63116368463953!3d41.87881207334865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880e2cbcb88d3b45%3A0x37ef3145a8a1c23d!2sUnited+States+Attorney&#39;s+Office!5e0!3m2!1sen!2sin!4v1549101057336" width="0" height="0" frameborder="0" style="border:0" allowfullscreen></iframe>
                     </div>
                     <div class="trip-user">
                        <div class="user-img" style="background-image: url(https://schedule.tranxit.co/storage/provider/profile/f1505bf83063da02b2106323e78be9a5.jpeg);">
                        </div>
                        <div class="user-right">
                           <h5>Frank Provider</h5>
                           <div class="rating-outer">
                              <span style="cursor: default;">
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: hidden;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0%;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                              </span>
                              <input type="hidden" class="rating" value="1" disabled="disabled">
                           </div>
                           <p></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- View Modal 1 Ends -->
      <!-- View Modal 2 Starts -->
      <div class="modal" id="modal2">
         <div class="modal-dialog min-70vw">
            <div class="modal-content">
               <!-- Schedule Header -->
               <div class="modal-header">
                  <h4 class="modal-title m-0">Service Details</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Schedule body -->
               <div class="modal-body ">
                  <div class="col-lg-6 col-md-6 col-sm-12 float-left">
                     <div class="my-trip-left">
                        <h4 class="text-center">
                           <strong>
                           Food Service</strong>
                        </h4>
                        <div class="from-to row m-0">
                           <div class="from">
                              <h5>Order Location : San Francisco, USA</h5>
                              <h5>Order Items : Cheese Pizza, Cheese Pizza, Cheese Pizza</h5>
                              <h5>Order Date : 04-02-19</h5>
                              <h5>Order Time : 22:05 PM</h5>
                           </div>
                        </div>
                     </div>
                     <div class="mytrip-right">
                        <ul class="invoice">
                           <li>
                              <span class="fare">Cart Subtotal</span>
                              <span class="pricing">$10</span>
                           </li>
                           <li>
                              <span class="fare">Shipping &amp; Handling</span>
                              <span class="pricing">$25</span>
                           </li>
                           <li>
                              <span class="fare">Promocode Discount</span>
                              <span class="txt-green pricing">GET5</span>
                           </li>
                           <li>
                              <div class="widget-body red">
                                 <div class="price-wrap text-center">
                                    <i class="material-icons address-category">attach_money</i>
                                    <p class="txt-white">GRAND TOTAL</p>
                                    <h3 class="value txt-white"><strong>$ 25,49</strong></h3>
                                 </div>
                              </div>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-6 float-right">
                     <div class="map-static">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2970.652981076582!2d-87.63116368463953!3d41.87881207334865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880e2cbcb88d3b45%3A0x37ef3145a8a1c23d!2sUnited+States+Attorney&#39;s+Office!5e0!3m2!1sen!2sin!4v1549101057336" width="0" height="0" frameborder="0" style="border:0" allowfullscreen></iframe>
                     </div>
                     <div class="trip-user">
                        <div class="user-img" style="background-image: url(https://schedule.tranxit.co/storage/provider/profile/f1505bf83063da02b2106323e78be9a5.jpeg);">
                        </div>
                        <div class="user-right">
                           <h5>Frank Provider</h5>
                           <div class="rating-outer">
                              <span style="cursor: default;">
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: hidden;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0%;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                                 <div class="rating-symbol" style="display: inline-block; position: relative;">
                                    <div class="fa fa-star-o" style="visibility: visible;"></div>
                                    <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div>
                                 </div>
                              </span>
                              <input type="hidden" class="rating" value="1" disabled="disabled">
                           </div>
                           <p></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- View Modal 2 Ends -->
      <!-- Dispute Modal -->
      <div class="modal" id="disputeModal">
         <div class="modal-dialog">
            <div class="modal-content">
               <!-- Dispute Header -->
               <div class="modal-header">
                  <h4 class="modal-title">Write Your Message</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Dispute body -->
               <div class="comments-section field-box mt-3 col-12">
                  <textarea class="form-control" rows="4" cols="50" placeholder="Leave Your Comments..."></textarea>
               </div>
               <!-- Dispute footer -->
               <div class="modal-footer">
                  <a  class="btn btn-primary btn-block" data-toggle="modal" data-target="#disputeModal" >Submit <i class="fa fa-check" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
      <!-- Dispute Modal -->
      <section class="ride-grid content-box-2">
         <div class="">
            <div class="clearfix ">
               <div class="dis-center col-md-12 p-0 dis-center">
                  <ul class="nav nav-tabs">
                     <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#my_trips">My Orders</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#upcoming_trips">Upcoming Orders</a>
                     </li>
                  </ul>
               </div>
               <div class="tab-content">
                  <div id="my_orders" class="tab-pane active col-sm-12 col-md-12 col-lg-12">
                     <div class="row ride-details">
                        <h4>My Orders</h4>
                        <div class="col-md-12 col-lg-12 col-sm-12 p-0 table-responsive-sm mt-4">
                           <table  id="my_orders_grid" class="table  table-striped table-bordered w-100">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Booking Id</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Total Amount</th>
                                    <th>Items</th>
                                    <th>Payment</th>
                                    <th>View</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"><i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>2.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"><i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"><i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"><i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"><i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div id="upcoming_orders" class="tab-pane col-sm-12 col-md-12 col-lg-12">
                     <div class="row ride-details">
                        <h4>Upcoming Orders</h4>
                        <div class="col-md-12 col-lg-12 col-sm-12 p-0 table-responsive-sm mt-4">
                           <table  id="upcoming_orders_grid" class="table table-striped table-bordered w-100">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Booking Id</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Total Amount</th>
                                    <th>Items</th>
                                    <th>Payment</th>
                                    <th>View</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                                 <tr >
                                    <td>1.</td>
                                    <td>TRNX204197</td>
                                    <td>04-02-2019</td>
                                    <td>Frank Provider</td>
                                    <td>$63.39</td>
                                    <td>Cheese Pizza, Cheese Pizza, Cheese Pizza</td>
                                    <td>PAID VIA CASH</td>
                                    <td><span class="view-icon" data-toggle="modal" data-target="#modal1"> <i class="fa fa-eye"></i></span>
                                       <span class="view-icon" data-toggle="modal" data-target="#disputeModal"><i class="fa fa-commenting-o"></i></span>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
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
      <script type='text/javascript' src='../js/jquery.datatable.min.js'></script>
      <script type='text/javascript' src='../js/bootstrap.datatable.min.js'></script>
      <script src="../js/popper.min.js"></script>
      <script src="../js/bootstrap.min.js"></script>
      <script>
         $(document).ready(function() {
         $('#my_orders_grid').DataTable();
         $('#upcoming_orders_grid').DataTable();
         } );
         function openNav() {
         document.getElementById("mySidenav").style.width = "100%";
         }
         
         function closeNav() {
         document.getElementById("mySidenav").style.width = "0";
         }
         
          // Input Checked
         $(document).ready(function(){
         $('input:checkbox').click(function() {
           $('input:checkbox').not(this).prop('checked', false);
         });
         });
      </script>
   </body>
</html>