<!DOCTYPE html>
<html>
<head>
	<title>Admin - Login</title>
	@include('admin.includes.head')
	@include('admin.includes.alert')
</head>
<body>
<section class="login">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-lg-12">
                @yield('content')
            </div>
		</div>
	</div>
</section>

@include('admin.includes.foot')
</body>
</html>