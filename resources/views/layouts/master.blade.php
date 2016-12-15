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
    <div id="main" class="row" style="padding-top:50px; min-height:90%;">
      @yield('content')
    </div>
    <div id="footer" class="row">
      @include('includes.footer')
    </div>
  </div>
</body>
</html>
