<html>
<head><h3>FUMA an error occured</h3></head>
<body>

<p>
  There was an error occured during the process of your job (job title: {{ $jobtitle }}).<br/>
  ERROR: {{ $status }}

  <?php
  if($status==1){
      echo ' (Not enough columns are provided in GWAS summary statistics file)<br/>
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==2){
      echo ' (Error from MAGMA)<br/>
      This error might be because of the rsID and/or p-value columns are wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==3 | $status==4){
      echo ' (Error during SNPs filtering for manhattan plot)<br/>
      This error might be because of the p-value column is wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==5){
      echo ' (Error from lead SNPs and candidate SNPs identification)<br/>
      This error occures when no candidate SNPs were identified.
      It might be becaseu there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS tagged SNPs.
      In that case, you can relax threshold or provide predefined lead SNPs.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#snp2gene">Tutorial<a/> for detilas.<br/>';
  }else if($status==6){
      echo ' (Error from lead SNPs and candidate SNPs identification)<br/>
      This error might be because of either invalid input parameters or columns which are wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==7){
      echo ' (Error during SNPs annotation extraction)<br/>
      This error might be because of either invalid input parameters or columns which are wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==8 || $status==9){
      echo ' (Error during extracting external data sources)<br/>
      This error might be because of either invalid input parameters or columns which are wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }else if($status==10){
      echo ' (Error during gene mapping)<br/>
      This error might be because of either invalid input parameters or columns which are wrongly labeled.
      Please make sure your input file have sufficient column names.
      Please refer <a href="http://ctg.labs.vu.nl/fuma/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
  }
  ?>
</p>
<p>
  Please do not hesitate to contact us for any questions/suggestions.<br/><br/>
  Kyoko Watanabe<br/>
  VU University Amsterdam<br/>
  Dept. Complex Trait Genetics<br/>
  De Boelelaan 1085 WN-B628 1018HV Amsterdam The Netherlands<br/>
  k.watanabe@vu.nl<br/>
</p>
</body>
</html>
