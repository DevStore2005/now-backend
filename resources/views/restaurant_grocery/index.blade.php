 <!doctype html>
<html class="no-js h-100" lang="en">
   <head>
      @include('restaurant_grocery.includes.head')
   </head>
   <body class="h-100">
         @include('restaurant_grocery.includes.alert')
         @include('restaurant_grocery.includes.header')
         @yield('content')
         @include('restaurant_grocery.includes.footer')
         @include('restaurant_grocery.includes.foot')
   </body>
</html>
