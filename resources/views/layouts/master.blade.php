<!DOCTYPE html>
<html lang="en">
<head>
  @include('includes.head')
  @yield('head')
</head>

<body>
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
