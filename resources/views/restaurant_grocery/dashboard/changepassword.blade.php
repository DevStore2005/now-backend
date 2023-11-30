 @include('restaurant_grocery.includes.header')

 <div class="breadcrumb-bar">
     <div class="container-fluid">
         <div class="row align-items-center">
             <div class="col-md-12 col-12">
                 <nav aria-label="breadcrumb" class="page-breadcrumb">
                     <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="{{ restaurant / index }}">Home</a></li>
                         <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                     </ol>
                 </nav>
                 <h2 class="breadcrumb-title">Change Password</h2>
             </div>
         </div>
     </div>
 </div>



 <div class="content">
     <div class="container">
         <div class="row">
             @include('restaurant_grocery.includes.sidebar')
             <div class="col-md-7 col-lg-8 col-xl-9">
                 <div class="card">
                     <div class="card-body">
                         <div class="row">
                             <div class="col-md-12 col-lg-6">

                                 <form>
                                     <div class="form-group">
                                         <label>Old Password</label>
                                         <input type="password" class="form-control">
                                     </div>
                                     <div class="form-group">
                                         <label>New Password</label>
                                         <input type="password" class="form-control">
                                     </div>
                                     <div class="form-group">
                                         <label>Confirm Password</label>
                                         <input type="password" class="form-control">
                                     </div>
                                     <div class="submit-section">
                                         <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                                     </div>
                                 </form>

                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>


 @include('restaurant_grocery.includes.footer')
