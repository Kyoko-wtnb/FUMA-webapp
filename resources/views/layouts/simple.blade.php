<!DOCTYPE html>
<html lang="en">
	<head>
		@include('partials._head')
	</head>
	<body>
		<div id="script_alert_block" class="container-fluid text-center"></div>
		<div class="container-fluid">
			<div id="main" class="row">
				@yield('content')
			</div>
		</div>
		<div id="foot" class="row">
			<footer>
				@include('partials._footer')
			</footer>
		</div>

		@include('partials._javascript')
		@yield('scripts')
	</body>
</html>
