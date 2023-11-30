<!doctype html>
<html class="no-js h-100" lang="en">
   <head>
      @include('admin.includes.head')
   </head>
   <body class="h-100">
    <div class="container-fluid">
      <div class="row">
         <!-- Main Sidebar -->
         <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
            <div class="main-navbar">
               <nav class="navbar align-items-stretch navbar-light flex-md-nowrap  p-0">
                  <a class="navbar-brand w-100 mr-0" href="">
                     <div class="d-table" style="margin-left: 24px;">
                        <img id="main-logo" class="d-inline-block align-top mr-1" src="{{ asset('/admin/assets/img/icon.svg') }}" alt="Logo" style="max-width: 100%;height: 40px;">
                        <span class="d-none d-md-inline ml-1" style="line-height: 3"> FareNow </span>
                     </div>
                  </a>
                  <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                  <i class="material-icons"></i>
                  </a>
               </nav>
               <div class="nav-wrapper">
               {{-- <form action="#" class="main-sidebar__search w-100 border-right">
                  <div class="input-group input-group-seamless p-1">
                     <div class="input-group-prepend">
                        <div class="input-group-text">
                           <i class="fas fa-search"></i>
                        </div>
                     </div>
                     <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search">
                  </div>
               </form> --}}
                  @include('admin.includes.side_nav')
               </div>
         </aside>
         <!-- End Main Sidebar -->
         <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
            @include('admin.includes.header')
            @include('admin.includes.alert')
           <div class="main-content-container container-fluid px-4">
             @yield('content')
           </div>
           <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
            <span class="copyright mr-auto my-auto mr-2">Copyrights 2021 All Rights Reserved.</span>
           </footer>
         </main>
       </div>
    </div>
    @include('admin.includes.foot')
   </body>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

<script type="module">
import Echo from 'https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.4/echo.js';
   if(typeof io != undefined) {
      const url = "{!! @config('app.url')  !!}";
      window.Echo = new Echo({
         broadcaster: 'socket.io',
         host: url,
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

      window.Echo.connector.socket.on('connect', function () {
      console.log("connect");
      });

      window.Echo.connector.socket.on('disconnect', function () {
      console.log("disconnect");
      });

      window.Echo.connector.socket.on('reconnect', function () {
      console.log("reconnect");
      });
   }
   Window.dataTable = $('table').DataTable({
      destroy: true,
      paging: false,
      sorting: false,
   });

   $('.toggle-sidebar-btn').on('click', function() {
      $('.main-sidebar').toggleClass('open');
   });

</script>
</html>
