<!DOCTYPE html>
<html lang="en">
<head>
  @include('includes.head')
  @yield('head')
</head>

<body>
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
