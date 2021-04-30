<!DOCTYPE html>
@php
	use DebugBar\StandardDebugBar;
	$debugbar = new StandardDebugBar();
	$debugbarRenderer = $debugbar->getJavascriptRenderer();
@endphp
<html lang="en">
<head>
	@include('includes.head')
	@yield('head')
	@php
	echo $debugbarRenderer->renderHead()
	@endphp
</head>

<body>
@php
	DebugBar::info("Debug messages initialized");
@endphp

	<!--div id="header" class="row"-->
		@include('includes.header')
	<!--/div-->
	<div class="container-fluid text-center">
		@if(Session::has('alert-success'))
		<div class="center-block">      
			<div class="alert alert-success alert-dismissable" style="display:inline-block;">
				<button type="button" class="Close" data-dismiss="alert" aria-label="Close">x</button>
				<em> {!! session('alert-success') !!}</em>
			</div>
		</div>
		@endif 
		@if(Session::has('alert-danger'))
		<div class="center-block">      
			<div class="alert alert-danger alert-dismissable" style="display:inline-block;">
				<button type="button" class="Close" data-dismiss="alert" aria-label="Close">x</button>
				<em> {!! session('alert-danger') !!}</em>
			</div>
		</div>
		@endif 
	</div>
	<div class="container-fluid">
		<div id="main" class="row">
			@yield('content')
		</div>
	</div> 	
	<div id="foot" class="row">
		@include('includes.footer')
	</div>
	
</body>
</html>
