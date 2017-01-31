<html>
<head><h3>FUMA an error occured</h3></head>
<body>

<p>
  This is unfortunate! An error occured during the process of your job (job title: {{ $jobtitle }}).<br/>
  ERROR: {{ $status }}

  <?php
  if($status==-1){
    echo ' File upload failed.<br/>
    This error is because your file upload failed. Please try again. Do not leave the page while uploading the file (after clicking the submit button).<br/>
    Only click the "Submit Job" once.
    The job has been deleted but the last job you have submitted could still be under process.<br/>
    Please check the list of jobs from <ahref="http://fuma.ctglab.nl/snp2gene">here</a> (login required).';
  }else if($status==1){
    echo ' (Number of columns in the GWAS summary statistics file do not match / '.$msg.')<br/>
    Please make sure your input file has sufficient column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==2){
    echo ' (Error from MAGMA / '.$msg.')<br/>
    This error can occur if the rsID and/or p-value columns are mistakenly labeled wrong.
    Please make sure your input file has the correct column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==3 || $status==4){
    echo ' (Error during SNPs filtering for manhattan plot)<br/>
    This error can occur if the p-value column is mistakenly labeled wrong.
    Please make sure your input file have sufficient column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==5){
    echo ' (Error from lead SNPs and candidate SNPs identification / No)<br/>
    This error can occur when no candidate SNPs were identified.
    1. If there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS tagged SNPs, 
    you can try to use a less stringent P-value threshold or provide predefined lead SNPs.
    2. If there are significant SNPs with very low minor allele frequency, try decreasing MAF threshold (default 0.01).
    Manhattan plots and significant top 10 SNPs in your input file are avilable from <a href="http://fuma.ctglab.nl/snp2gene/'.$jobID.'">SNP2GENE<a/>.<br/>';
  }else if($status==6){
    echo ' (Error from lead SNPs and candidate SNPs identification)<br/>
    This error can occur because  1. invalid input parameters or 2. columns are mistakenly labeled wrong.
    Please make sure your input file has the correct column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==7){
    echo ' (Error during SNPs annotation extraction)<br/>
    This error can occur because  1. invalid input parameters or 2. columns are mistakenly labeled wrong.
    Please make sure your input file has the correct column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==8 || $status==9){
    echo ' (Error during extracting external data sources)<br/>
    This error can occur because  1. invalid input parameters or 2. columns are mistakenly labeled wrong.
    Please make sure your input file has the correct column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==10){
    echo ' (Error during gene mapping)<br/>
    This error can occur because  1. invalid input parameters or 2. columns are mistakenly labeled wrong.
    Please make sure your input file has the correct column names.
    Please refer <a href="http://fuma.ctglab.nl/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }
  ?>
</p>
<p>
  Please do not hesitate to contact me if you have any questions/suggestions regarding FUMA.<br/><br/>
  Kyoko Watanabe<br/>
  VU University Amsterdam<br/>
  Dept. Complex Trait Genetics<br/>
  De Boelelaan 1085 WN-B628 1018HV Amsterdam The Netherlands<br/>
  k.watanabe@vu.nl<br/>
</p>
</body>
</html>
