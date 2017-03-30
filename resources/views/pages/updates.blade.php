@extends('layouts.master')
@section('head')
<script type="text/javascript">
  var loggedin = "{{ Auth::check() }}";
</script>
@stop

@section('content')
<div class="container" style="padding-top: 50px;">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th style="width: 15%;">Date</th>
        <th style="width: 15%;">Version</th>
        <th style="width: 70%;">Description</th>
      </tr>
    </thead>
    <tbody>
		<tr>
	      <td>24 Mar 2017</td>
	      <td>v1.1.1</td>
	      <td>SNPs filtering with functional annotation for gene mapping is now reflected in the regional plot with annotations.
			  Details are described at the bottom of the page of regional plot with annotations.
		  </td>
	    </tr>
		<!-- <tr>
			<td>17 Mar 2017</td>
			<td>v1.1.0</td>
			<td>In GENE2FUNC, in addition to enrichment test of differentialy expressed genes, enrichment of genes expressed in each tissue is also tested.
			  Details are in the <a href="{{ Config::get('app.subdir') }}/tutorial#g2fOutputs">GENE2FUNC Outputs</a> section of the tutorial.
			</td>
		</tr> -->
		<tr>
			<td>17 Mar 2017</td>
			<td>v1.1.0</td>
			<td>In SNP2GENE, MAGMA tissue expression analyses was added to "Genome wide plot".
			  Details are in the <a href="{{ Config::get('app.subdir') }}/tutorial#outputs">SNP2GENE Outputs</a> section of the tutorial.
			</td>
		</tr>
		<tr>
			<td>21 Feb 2017</td>
			<td>v1.0.0</td>
			<td>The first version was freezed.</td>
		</tr>
    </tbody>
  </table>
</div>
@stop
