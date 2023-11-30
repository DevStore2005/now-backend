<style type="text/css">
  .m-b-0{
      margin: 30px !important;
  } 
  .notification {
    padding: 20px 20px;
    border-radius: 4px;
    margin-bottom: 40px;
    position: fixed;
    z-index: 99999;
    top: 89px;
    width: 283px;
    right: 12px;
    box-shadow: 0px 0px 0px 5px rgba(255, 255, 255, 0.6);
  }
  .notification p {
    color: #fff;
    text-align: left;
    padding-bottom: 0;
  }
  .notification p a {
    color: rgba(255, 255, 255, 0.71);
    font-weight: 600;
    padding: 0 5px;
  }
  .notification.success {
    background: #28a745!important;
  }
  .notification.danger {
    background: #dc3545!important;
  }
  .notification.waitforreview {
    background: #FBC54F;
  }
  .notification.reject {
    background: #EA4D37;
  }
  .notification-close {
    position: absolute;
    top: 27%;
    right: 10px;
    height: 20px;
    margin-top: -10px;
    color: rgba(255, 255, 255, 0.71);
  }
  .notification-close:hover {
    color: #fff;
  }
</style>

@if($errors->any())
  @foreach($errors->all() as $error)
    <div class="notification danger fl-wrap">
      <p>{{ $error }}</p>
      <a class="notification-close" href="#"><i class="fa fa-times"></i></a>
    </div>
  @endforeach
@endif

@if(Session::has('success_message'))
  <div class="notification success fl-wrap">
    <p>{{ Session::get('success_message') }}</p>
    <a class="notification-close" href="#"><i class="fa fa-times"></i></a>
  </div>
@endif

@if(Session::has('error_message'))
  <div class="notification danger fl-wrap">
    <p>{{ Session::get('error_message') }}</p>
    <a class="notification-close" href="#"><i class="fa fa-times"></i></a>
  </div>
@endif

<script type="text/javascript">
  setTimeout(function() {
    $(".notification").slideUp(500);
  }, 5000);

  $('.notification-close').on('click', function(){
    $(".notification").slideUp(500);
  });
</script>
