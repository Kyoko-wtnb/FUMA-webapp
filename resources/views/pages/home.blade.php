@extends('layouts.master')
@section('head')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

@stop
@section('content')
<div class="container" style="padding-top:50px;">
  <div style="text-align: center;">
    <h2>Welcome to FUMA (Functional Mapping and Annotation of GWAS)</h2>
  </div>
  <p>FUMA is a web application which annotates, prioritizes and visualizes GWAS results.
    Publicly available data resources and tools used in this applications are accessible from <a href="{{ Config::get('app.subdir') }}/links">links</a> tab.</p>
    All you need to prepare is to get GWAS summary statistics of phenotype of interest and submit at <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a>.
    You can also query a list of genes directory from <a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a>.
  </p>

  <p>Please log in to submit GWAS summary statistics at <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a>.
    If you have't registered yet, you can do from <a href="{{ url('/register') }}">here</a>.<br/>
    <a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a> can be performed solely without registration.
  </p>
  <p>
    Since, FUMA provides a variety of parameters, please follow the <a href="{{ Config::get('app.subdir') }}/tutorial">Tutorial</a>.
    For detail methods, please refer the publication.
    If you have any question or problem using this application, please let us know!! (Kyoko Watanabe: k.watanabe@vu.nl)
  </p>

</div>
</br>
@stop
