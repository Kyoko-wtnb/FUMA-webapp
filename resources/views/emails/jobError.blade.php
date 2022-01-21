<html>
<head><h3>FUMA an error occurred</h3></head>
<body>

<p>
	This is unfortunate! An error occurred during the process of your job (job ID: {{ $jobID }}, job title: {{ $jobtitle }}).<br/>
	ERROR: {{ $status }}

	<?php
	if($status==-1){
		echo ' File upload failed.<br/>
		This error is because your file upload failed. Please try again. Do not leave the page while uploading the file (after clicking the submit button).<br/>
		Only click the "Submit Job" once.
		The job has been deleted but the last job you have submitted could still be under process.<br/>
		Please check the list of jobs from <a href="https://fuma.ctglab.nl/snp2gene">here</a> (login required).';
	}else if($status==1){
		echo ' (Input file format was not correct / <span style="color:blue;"><strong>'.$msg.'</strong></span>)<br/>
		Please make sure your input file has sufficient column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==2){
		echo ' (Error from MAGMA / <span style="color:blue;"><strong>'.$msg.'</strong></span>)<br/>
		This error can occur if the rsID and/or p-value columns are mistakenly labelled wrong.
		Please make sure your input file has the correct column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==3 || $status==4){
		echo ' (Error during SNPs filtering for Manhattan plot)<br/>
		This error can occur if the p-value column is mistakenly labelled wrong.
		Please make sure your input file have sufficient column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==5){
		echo ' (Error from lead SNPs and candidate SNPs identification / <span style="color:blue;"><strong>No significant SNPs were identified</strong></span>)<br/>
		This error can occur when no candidate SNPs were identified. Note that indels are included in the FUMA from v1.3.0 but both alleles need to match exactly with selected reference panel.
		MHC region is also excluded by default.
		1. If there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS tagged SNPs,
		you can try to use a less stringent P-value threshold or provide predefined lead SNPs.
		2. If there are significant SNPs with very low minor allele frequency, try decreasing MAF threshold (default 0.01).
		Manhattan plots and significant top 10 SNPs in your input file are available from <a href="https://fuma.ctglab.nl/snp2gene/'.$jobID.'">SNP2GENE<a/>.<br/>';
	}else if($status==6){
		echo ' (Error from lead SNPs and candidate SNPs identification)<br/>
		This error can occur because  1. invalid input parameters or 2. columns are mistakenly labelled wrong.
		Please make sure your input file has the correct column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==7){
		echo ' (Error during SNPs annotation extraction)<br/>
		This error can occur because  1. invalid input parameters or 2. columns are mistakenly labelled wrong.
		Please make sure your input file has the correct column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==8 || $status==9){
		echo ' (Error during extracting external data sources)<br/>
		This error can occur because  1. invalid input parameters or 2. columns are mistakenly labelled wrong.
		Please make sure your input file has the correct column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==10){
		echo ' (Error from chromatin interaction mapping / <span style="color:blue;"><strong>'.$msg.'</strong></span>)<br/>
		This error might be because one of the uploaded chromatin interaction files did not follow the correct format.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#ciMap">Tutorial<a/> for details.<br/>';
	}else if($status==11){
		echo ' (Error during gene mapping)<br/>
		This error can occur because  1. invalid input parameters or 2. columns are mistakenly labelled wrong.
		Please make sure your input file has the correct column names.
		Please refer <a href="https://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for details.<br/>';
	}else if($status==12){
		echo ' (Error from circos / <span style="color:blue;"><strong>'.$msg.'</strong></span>)<br/>
		This error is most likely due to server side error. Please contact the developer for details.<br/>';
	}else if($status==100){
		echo ' (Unknown error in job'.$msg.'</strong></span>)<br/>
		This may be a result of job submission failure, job abort or perhaps a server side error. If this persists please contact the developer for details.<br/>';
	}
	?>
</p>
<p>
	You can post questions, suggestions and bug reports on Google Forum:
	<a href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a><br/><br/>
	FUMA development team<br/>
	VU University Amsterdam<br/>
	Dept. Complex Trait Genetics<br/>
</p>
</body>
</html>
