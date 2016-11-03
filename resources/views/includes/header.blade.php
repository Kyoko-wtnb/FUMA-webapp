<!-- Header -->
<div id="logo">
  <h1>Interactive post-GWAS pipeline
    <span style="color: #1E90FF; font-size: 150%">IPGAP</span>
  </h1>
  <p style="font-size:18px; color: #818588;">Interactive tools to identify causal SNPs and genes from GWAS summary statistics.</p>
</div>

<!-- Tab bar -->
<nav class="navbar navbar-default">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNuvbar">
      <sapn class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav navbar-right">
      <li class="{{ Request::is('/') ? 'active' : ''}}"><a href="/">Home</a></li>
      <li class="{{ Request::is('tutorial') ? 'active' : ''}}"><a href="/tutorial">Tutorial</a></li>
      <li class="{{ Request::is('snp2gene*') ? 'active' : ''}}"><a href="/snp2gene">SNP2GENE</a></li>
      <li class="{{ Request::is('gene2func') ? 'active' : ''}}"><a href="/gene2func">GENE2FUNC</a></li>
      <li class="{{ Request::is('links') ? 'active' : ''}}"><a href="/links">Links</a></li>
      <li class="{{ Request::is('contact') ? 'active' : ''}}"><a href="/contact">Contact</a></li>
    </ul>
  </div>
</nav>
