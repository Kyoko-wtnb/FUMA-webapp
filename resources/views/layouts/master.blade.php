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
	<div class="container-fluid">
		<div id="header" class="row">
			@include('includes.header')

			@if(Session::has('flash_message'))
            <div class="container">      
                <div class="alert alert-success"><em> {!! session('flash_message') !!}</em>
                </div>
            </div>
			@endif 

			<div class="row">
				<div class="col-md-8 col-md-offset-2">              
					@include ('errors.list') {{-- Including error file --}}
				</div>
			</div>
		</div>
		<div id="main" class="row" style="padding-top:50px; padding-bottom: 50px;">
			@yield('content')
		</div>
	</div> 	
	<div id="foot" class="row">
		@include('includes.footer')
	</div>
	
</body>
</html>
