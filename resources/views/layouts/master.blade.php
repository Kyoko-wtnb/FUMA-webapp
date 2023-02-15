<!DOCTYPE html>
<html lang="en">
<head>
	@include('includes.head')
	@yield('head')
</head>

<body>
	<div id="header" class="row">
		@include('includes.header')
	</div>
	<div class="container-fluid text-center" style="padding-top: 50px">
		@if(Session::has('alert-success'))
		<div class="center-block">      
			<div class="alert alert-success alert-dismissable" style="display:inline-block;">
				<span type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></span>
				<em> {!! session('alert-success') !!}</em>
			</div>
		</div>
		@endif 
		@if ( Session::has('alert-warning') )
		<div class="container-fluid text-center">
			<div class="center-block">      
				<div class="alert alert-warning alert-dismissable" style="display:inline-block;">
					<span type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></span>
					<em> {!! Session::get('alert-warning') !!}</em>
				</div>
			</div>
		</div>
		@endif 
		@if(Session::has('alert-danger'))
		<div class="center-block">      
			<div class="alert alert-danger alert-dismissable" style="display:inline-block;">
				<span type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></span>
				<em> {!! session('alert-danger') !!}</em>
			</div>
		</div>
		@endif 
	</div>
	<div class="container-fluid">
		<div id="main" class="row" style="padding-bottom: 50px;">
			@yield('content')
		</div>
	</div> 	
	<div id="foot" class="row">
		@include('includes.footer')
	</div>
	
</body>
</html>
