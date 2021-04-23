<!-- Tab bar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header" style="padding-left: 30px;">
			<!-- <a class="navbar-brand" href="{{ Config::get('app.subdir') }}/"><span style="color: #1E90FF; font-size: 130%;">IPGAP</span></a> -->
			<a class="navbar-brand" href="{{ Config::get('app.subdir') }}/" style="padding-top: 5px;">
			<img src="{!! URL::asset('image/fuma.png') !!}" height="50px;">
			<!-- <span style="color:#fff; font-size:30px;">FUMA</span> -->
			</a>
		</div>

		<div class="collapse navbar-collapse" id="headNav" style="padding-right: 50px;">
			<ul class="nav navbar-nav navbar-right">
				<!-- local_start -->
				<li class="{{ Request::is('/') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/">Home</a></li>
				<li class="{{ Request::is('tutorial') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/tutorial">Tutorial</a></li>
				<li class="{{ Request::is('browse*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/browse">Browse Public Results</a></li>
				<li class="{{ Request::is('snp2gene*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a></li>
				<li class="{{ Request::is('gene2func*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a></li>
				<li class="{{ Request::is('celltype*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/celltype">Cell Type</a></li>
				<li class="{{ Request::is('links') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/links">Links</a></li>
				<li class="{{ Request::is('updates') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/updates">Updates</a></li>
				<li>
					<a id="appInfo" class="infoPop" data-placement="bottom" data-toggle="popover" data-html="true"
						title="FUMA information"
						data-content='<div style="width:200px;">
						Current FUMA verions: <span id="FUMAver"></span><br/>
						Total users: <span id="FUMAuser"></span><br/>
						Total SNP2GENE jobs: <span id="FUMAs2g"></span><br/>
						Total GENE2FUNC jobs: <span id="FUMAg2f"></span><br/>
						Currently running jobs: <span id="FUMArun"></span><br/>
						Currently queued jobs: <span id="FUMAque"></span></div>'>
						<i class="fa fa-info-circle fa-lg"></i>
					</a>
				</li>
				<!-- local_end -->
				<!-- Authentication Links -->
				@if (Auth::guest())
					<li><a href="{{ url('/login') }}">Login</a></li>
					<li><a href="{{ url('/register') }}">Register</a></li>
				@else
					<!-- hidden logout form - logout requires HTTP POST since Laravel 5.3 -->
					<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
    					{{ csrf_field() }}
					</form>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							{{ Auth::user()->name }} <span class="caret"></span>
						</a>

						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ Config::get('app.subdir') }}/snp2gene#joblist-panel">SNP2GENE My Jobs</a></li>
							<li><a href="{{ Config::get('app.subdir') }}/gene2func#queryhistory">GENE2FUNC History</a></li>
							<li>
								<a href="{{ url('/logout') }}"
    								onclick="event.preventDefault();
             						document.getElementById('logout-form').submit();">
    								Logout
								</a>
							</li>
						</ul>
					</li>
				@endif
			</ul>
		</div>
	</div>
</nav>
