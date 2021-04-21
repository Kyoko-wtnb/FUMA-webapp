@php
	use DebugBar\StandardDebugBar;
	$debugbar = new StandardDebugBar();
	$debugbarRenderer = $debugbar->getJavascriptRenderer();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
	@php
	echo $debugbarRenderer->renderHead()
	@endphp
	@include('includes.head')
	@yield('head')
</head>

<body>
@php
	DebugBar::info("Debug messages initialized");
@endphp
	<div class="container-fluid">
		<div id="header" class="row">
			@include('includes.header')
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
