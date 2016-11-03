<!DOCTYPE html>
<html lang="en">
<head>
  @include('includes.head')
</head>

<body>
  <div class="container">
    <div id="header" class="row">
      @include('includes.header')
    </div>
    <div id="main" class="row">
      @yield('content')
    </div>
    <div id="footer" class="row">
      @include('includes.footer')
    </div>
  </div>
</body>
</html>
