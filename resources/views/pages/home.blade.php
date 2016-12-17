@extends('layouts.master')
@section('head')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

@stop
@section('content')
<div class="container" style="padding-top:50px;">
  <div style="text-align: center;">
    <h2>Welcome to FUMA (Functional Mapping and Annocation of GWAS)</h2>
  </div>
  <p>FUMA is a web application which annotates, prioritizes and visualizes GWAS results.
    Publicly available data resources and tools used in this applications are accessible from <a href="/IPGAP/links">links</a> tab.</p>
    All you need to prepare is to get GWAS summary statistics of phenotype of interest and submit at <a href="/IPGAP/snp2gene">SNP2GENE</a>.
    You can also query a list of genes directory from <a href="/IPGAP/gene2func">GENE2FUNC</a>.
  </p>

  <p>Please log in to seubmit GWAS summary statistics at <a href="/IPGAP/snp2gene">SNP2GENE</a>.
    If you have't registered yet, you can do from <a href="/register">here</a>.<br/>
    <a href="/IPGAP/gene2func">GENE2FUNC</a> can be performed solely without registration.
  </p>
  <p>
    Since, FUMA provides a variety of parameters, please follow the <a href="/IPGAP/tutorial">Tutorial</a>.
    For detail methods, please refer the publication.
    If you have any question or problem using this application, please let us know!! (Kyoko Watanabe: k.watanabe@vu.nl)
  </p>

  <h3>News and Updates</h3>
  <div id="NewsFeed" class="container" style="overflow:auto; max-height:500px;">
    <div class="panel panel-info">
      <div class="panel-heading" data-toggle="collapse" data-target="#news2">
        <h4><a>2016-11-30 (Wed): Update of the tutorial page.</a></h4>
      </div>
      <div class="panel-body collapse in" id="news2">
        Tutorial has been updated.
      </div>
    </div>

  </div>
</div>
</br>
@stop
