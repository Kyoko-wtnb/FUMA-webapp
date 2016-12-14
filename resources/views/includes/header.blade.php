<!-- Header -->
<!-- <div id="logo"> -->
  <!-- <h1>Interactive post-GWAS pipeline
    <span style="color: #1E90FF; font-size: 150%">IPGAP</span>
  </h1>
  <p style="font-size:18px; color: #818588;">Interactive tools to identify causal SNPs and genes from GWAS summary statistics.</p> -->
  <!-- <h1>the Annotation and Prioritization Platform
    <span style="color: #1E90FF; font-size: 150%">ANNOTATOR</span>
  </h1>
  <p style="font-size:18px; color: #818588;">Interactive tools to annotate, prioritize and visualiz potential causal SNPs and genes from GWAS summary statistics.</p> -->

<!-- </div> -->

<!-- Tab bar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header" style="padding-left: 30px;">
      <!-- <a class="navbar-brand" href="{{ Config::get('app.subdir') }}/"><span style="color: #1E90FF; font-size: 130%;">IPGAP</span></a> -->
      <a class="navbar-brand" href="{{ Config::get('app.subdir') }}/" style="padding-top: 5px;"><img src="{!! URL::asset('image/logo.png') !!}" height="45px;" width="200px"></a>
    </div>

    <div class="collapse navbar-collapse" id="headNav" style="padding-right: 50px;">
      <ul class="nav navbar-nav navbar-right">
        <!-- local_start -->
        <li class="{{ Request::is('/') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/">Home</a></li>
        <li class="{{ Request::is('tutorial') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/tutorial">Tutorial</a></li>
        <!-- <li class="{{ Request::is('GWASresult*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/GWASresult">GWASresult</a></li> -->
        <li class="{{ Request::is('snp2gene*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a></li>
        <li class="{{ Request::is('gene2func*') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a></li>
        <li class="{{ Request::is('links') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/links">Links</a></li>
        <!-- <li class="{{ Request::is('contact') ? 'active' : ''}}"><a href="{{ Config::get('app.subdir') }}/contact">Contact</a></li> -->
        <!-- local_end -->
      </ul>
    </div>
  </div>
</nav>
