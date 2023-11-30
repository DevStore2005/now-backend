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
      <!-- <link href="../taxi-booking/css/taxi-booking.css" rel="stylesheet"> -->
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
         <a href="../taxi-booking/restaurants-details.php">Profile</a>
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
               <a   class="dis-column" href="../taxi-booking/restaurants-details.php">
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
      <div class="site-wrapper animsition">
         <div class="page-wrapper z-1 content-box">
            <!-- start: Inner page hero -->
            <div class="result-show">
               <div class="container">
                  <div class="row">
                     <div class="col-sm-12 col-md-6 col-xl-4">
                        <span class="fa fa-location-arrow" style=" position: absolute; left: 5%; top: 25%;color: #495057;font-size: 18px;"></span>
                        <input class="form-control" type="text" name="order-location" placeholder="Search for area, street name.." required>
                        <span><i class="material-icons" style=" position: absolute; right: 5%; top: 25%;color: #495057;font-size: 18px;cursor: pointer;">my_location</i> </span>
                     </div>
                     <div class="col-sm-12 col-md-6 col-xl-4 dis-center">
                        <div class="location-section m-0">
                           <span class="location-title">
                           <span class="landmark">Velachery</span>
                           </span><span class="city"> Chennai, Tamil Nadu, India</span>
                           <span class="fa fa-angle-down arrow-icon"></span>
                        </div>
                        <!-- <div class="location-section ">
                           <span class="location-title">
                              <span class="landmark">Velachery</span>
                           </span><span class="city"> Choose Location</span>
                           <span class="fa fa-angle-down arrow-icon"></span>
                           </div> -->
                     </div>
                     </p>
                     <div class="col-sm-9 dis-ver-center c-pointer">
                        <!-- <p><span class="primary-color"><strong>124</strong></span> Results so far  -->
                     </div>
                  </div>
               </div>
            </div>
            <!-- //results show -->
            <section class="restaurants-page pt-5 pb-5">
               <div class="row bg-white p-3">
                  <div class="col-md-12 col-sm-12 col-xl-2">
                     <div class="sidebar left">
                        <div class="widget style2 Search_filters">
                           <h4 class="widget-title2 sudo-bg-red" itemprop="headline">Search Filters</h4>
                           <div class="widget-data">
                              <ul>
                                 <li><a href="#" title="" itemprop="url">Pizza</a> <span>6</span></li>
                                 <li><a href="#" title="" itemprop="url">Ice Cream</a> <span>6</span></li>
                                 <li><a href="#" title="" itemprop="url">Rolls</a> <span>6</span></li>
                              </ul>
                           </div>
                        </div>
                        <div class="widget style2 quick_filters">
                           <h4 class="widget-title2 sudo-bg-red" itemprop="headline">Quick Filters</h4>
                           <div class="widget-data">
                              <ul>
                                 <!-- <li><span class="radio-box"><input type="radio" name="filter" id="filt1-1"><label for="filt1-1">Promotions</label></span></li> -->
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-2"><label for="filt1-2">Non Veg</label></span></li>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-3"><label for="filt1-3">Pure veg</label></span></li>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-4"><label for="filt1-4">Free Delivery</label></span></li>
                                 <li><span class="radio-box"><input type="radio" name="filter" id="filt1-5"><label for="filt1-5">Online Payments</label></span></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-9 col-sm-12 col-lg-12">
                     <div class="row">
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left restaurant-list">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food1.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#">
                                    <img src="../img/foody/food2.jpg" alt="Food logo">
                                    <div class="blog-hover"></div>
                                    <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span>
                                 </a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food3.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food1.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food2.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food3.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food1.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-6 col-xl-4 text-xs-center text-sm-left">
                           <div class=" bg-gray restaurant-entry food-item-wrap">
                              <div class="entry-logo figure-wrap">
                                 <a class="img-fluid" href="#"><img src="../img/foody/food2.jpg" alt="Food logo">
                                 <span class="post-rate"><i class="fa fa-star-o"></i> 4.25</span></a>
                              </div>
                              <!-- end:Logo -->
                              <div class="entry-dscr">
                                 <div><a href="#">Thai Restaurant</a></div>
                                 <span>Burgers, American, Sandwiches, Fast Food, BBQ,urgers, American, Sandwiches</span>
                                 <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-check"></i> Min $ 10,00</li>
                                    <li class="list-inline-item"><i class="fa fa-motorcycle"></i> 30 min</li>
                                 </ul>
                                 <a href="restaurants-details.php" class="btn btn-primary">View Menu</a> 
                              </div>
                              <!-- end:Entry description -->
                           </div>
                        </div>
                     </div>
                     <!--end:row -->
                     <!-- end:Restaurant entry -->
                  </div>
               </div>
            </section>
         </div>
      </div>
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
          $(document).ready(function(){
          $('input:checkbox').click(function() {
            $('input:checkbox').not(this).prop('checked', false);
          });
          });
      </script>
   </body>
</html>