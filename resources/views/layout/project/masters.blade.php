
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>         
   @include('layout.project.head')
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="fixed-top">
<!-- BEGIN HEADER -->
	 @include('layout.project.header')
<!-- END HEADER -->
<!-- BEGIN CONTAINER -->
	<div id="container" class="row-fluid">
<!-- BEGIN SIDEBAR -->
	
<!-- END SIDEBAR -->

    @yield('content')
	
	</div>
	
	@include('layout.project.footer') 
    @include('layout.project.foot')

    @yield('footer_js')
    
</body>
<!-- END BODY -->
</html>
