@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.panel-heading.faq a').on('click', function(){
		if($(this).attr('class')=="active"){
			$(this).removeClass('active');
			$(this).children('i').attr('class', 'fa fa-chevron-down');
		}else{
			$(this).addClass('active');
			$(this).children('i').attr('class', 'fa fa-chevron-up');
		}
	});
})
</script>
@stop

@section('content')
<div style="padding-top: 50px; padding-right: 50px; padding-left: 50px;">
	<div class="panel panel-default">
		<div class="panel-heading faq" style="padding-top:5px;padding-bottom:5px;">
			<h4>My job returned: ERROR: 001 (Input file format not correct). What should I do? <a href="#faq1" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="faq1">
			When you get this error, this is because there has been an issue with the processing of your input. Please check the following:<br/>
            1. Does this error still occur if the column names are manually assigned on the submission page?<br/>
            2. Are the basepair positions accidentally changed to scientific notation?<br/>
            3. Are the columns consistently spaced by either a tab or whitespace?<br/>
            4. Are there any rows with missing values?<br/>
            5. Does the error still occur when I gzip my input file?<br/>
            After trying the above and the issue still persists, you can open a thread on <a target="_blank" href="https://groups.google.com/g/fuma-gwas-users/">Google Users Group</a><br/>
		</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading faq" style="padding-top:5px;padding-bottom:5px;">
			<h4>My job returned: ERROR: 001 (Input file format was not correct/ValueError: cannot convert float NaN to integer). What do I do? <a href="#faq2" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="faq2">
			When you get this error, this is because one of the columns in your input file that is supposed to be a an integer is a missing value or a NA value.<br/>
            This would most likely be the CHR, POS, BETA or P column.<br/>
            Either removing those rows or correcting those missing values should solve this issue.<br/>
		</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading faq" style="padding-top:5px;padding-bottom:5px;">
			<h4>My job returned: ERROR: 001 (No variants remain after filtering). What should I do?<a href="#faq3" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="faq3">
			FUMA creates risk loci for your GWAS summary statistics in step one. In order to do this, we need LD information between variants from our reference panel.
            FUMA will therefore drop all variants that are not found in the reference panel. If no variants with p-values below the selected significance threshold remain (5e-8 by default), FUMA will throw this error.<br/>
            You can check this by seeing if the significant variants in your input summary statistics are part of the reference panel you selected. More info on the FUMA reference panels can be found here: <a target="_blank" href="https://fuma.ctglab.nl/tutorial#refpanel">Refpanel Information</a>.<br/>
		</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading faq" style="padding-top:5px;padding-bottom:5px;">
			<h4>My job status shows “Job Failed”. What could cause this and how do I fix this?<a href="#faq4" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="faq4">
			Currently, jobs that are running for more than 6 hours are killed automatically. Most of the time this happens because the job is stuck in MAGMA.<br/>
            If you had checked MAGMA (after v1.5.1 update), then you can uncheck MAGMA and re-submit. Note that you will not get results that are dependent on MAGMA when you uncheck MAGMA.<br/>
		</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading faq" style="padding-top:5px;padding-bottom:5px;">
			<h4>How many jobs can I submit at once?<a href="#faq5" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="faq5">
			We maintain a dedicated server for running FUMA jobs. As this is a free service we provide for the advancement of science, this also means that there is a limited amount of computational resources to go around.<br/>
            In order to prevent single users to occupy the entire server, there is a job limit of 10 jobs per user. Currently, there is no API for FUMA.<br/>
		</div>
    </div>
</div>