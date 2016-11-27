@extends('layouts.master')
<script type="text/javascript">
  <!--hide
  var password;
  var pass1 = "CTGLab01";
  password = prompt("Please enter password to go IPGAP website");
  if(password==pass1){
    alert("Correct password, click 'OK' to enter!!");
  }else{
    window.location="http://www.google.com";
  }
  //-->
</script>
@section('content')
  <div style="text-align: center;">
    <h2>Welcome to IPGAP web applciation!!</h2>
  </div>
  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <p>IPGAP is a pilot which systematically process GWAS summary statistics and extract relevant biological information from multiple databases.
        All you need to prepare is to get GWAS summary statistics of phenotype of interest.
        If you are here at first time, please follow the tutorial carefully since IPGAP provides a variety of parameters.</p>
      <p>Databases used in this pilot can be accessed from link tab.</p>
      <br/>
    </div>
    <div class="col-md-1"></div>
  </div>
  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <h3>News and Updates</h3>
      
    </div>
    <div class="col-md-1"></div>
  </div>


  <br/>
@stop
