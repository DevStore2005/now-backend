<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title> @yield('title') - Farenow </title>
<meta name="description" content="Farenow Admin">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link type="image/x-icon" href="{{ asset('/admin/assets/img/icon.svg') }}" rel="icon">
<link rel="stylesheet" href="{{asset('admin/assets/plugins/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('admin/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('admin/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}"/>
<link rel='stylesheet' type='text/css' href="{{asset('admin/assets/plugins/clockpicker-wearout/css/jquery-clockpicker.min.css')}}"/>
<link rel="stylesheet" href="{{asset('admin/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/assets/plugins/chart/css/export.css')}}"/>
<link rel="stylesheet" href="{{asset('admin/assets/plugins/extras/css/extras.min.css')}}">
<link rel="stylesheet"  type='text/css' href="{{asset('admin/assets/plugins/intl-tel-input/css/intlTelInput.min.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/layout/css/admin-style.css')}}">
<link rel="stylesheet" href="{{asset('admin/assets/plugins/chart/css/jquery-jvectormap.css')}}">
<link rel="stylesheet" href="{{asset('admin/assets/plugins/data-tables/css/dataTables.bootstrap.min.css')}}"/>
<link rel="stylesheet" href="{{asset('restaurant/assets/plugins/fancybox/jquery.fancybox.min.css')}}"/>
<link rel="stylesheet" href="{{asset('admin/assets/layout/css/dashboards.min.css')}}">
<style type="text/tailwindcss">
    @layer utilities {
      .collapse {
        visibility: visible !important;
      }
    }
  </style>
@stack('plugin-stylesheet')
<script src="{{asset('admin/assets/plugins/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('admin/assets/plugins/data-tables/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('admin/assets/plugins/iscroll/js/scrolloverflow.min.js')}}"></script>
<script src="{{asset('restaurant/assets/plugins/fancybox/jquery.fancybox.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<script src="https://cdn.socket.io/4.1.2/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/pbkdf2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha1.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
@stack('plugin-scripts')
@stack('custom-scripts')

<style type="text/css">
   .dz-preview .dz-image img{
     width: 100% !important;
     height: 100% !important;
     object-fit: cover;
   }
   .intl-tel-input{
      width: 100%;
      display: block !important;
   }
</style>