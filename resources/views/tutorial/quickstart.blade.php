<div id="quick-start" class="sidePanel container" style="padding-top:50px;">
	<h2>Quick Start</h2>
	<div style="margin-left: 40px;">
		<h3 id="generalInfo">General Information</h3>
		Each page contains information where needed and brief descriptions of inputs and results to help you understand them without going through entire tutorial.<br/>
		<p>
			<div style="padding-left: 40px">
				<span class="info"><i class="fa fa-info"></i> This is information of inputs or results.</span><br/><br/>
				<a class="infoPop" data-toggle="popover" data-content="This popover will show brief description. Click anywhere outside of this popover to close.">
					<i class="fa fa-question-circle-o fa-lg"></i>
				</a> :click the question mark to display a brief description.<br/><br/>
				<span class="alert alert-info" style="padding: 5px;">
					This is for optional inputs/parameters.
				</span><br/><br/>
				<span class="alert alert-success" style="padding: 5px;">
					This is the message if everything is fine.
				</span><br/><br/>
				<span class="alert alert-danger" style="padding: 5px;">
					This is the message if the input/parameter is mandatory and not given or invalid input is given.
				</span><br/><br/>
				<span class="alert alert-warning" style="padding: 5px;">
					This is the warning message for the input/parameter. Please check your input settings.
				</span><br/><br/>
			</div>
		</p>

		<h3 id="getCandidate">Prioritize genes based on your own GWAS summary statistics</h3>
		<p>For risk loci identified by FUMA in your summary statistics, you can obtain functional annotation of SNPs and map them to genes.
			By changing parameter settings, you can control which annotations or filters need to be used to prioritize genes.
		</p>
		<p>Because you will upload your own GWAS summary statistics, we require you to register.
			All uploaded files are handled securely and can only be seen by you.
			Results can be queried at later times, but can also be deleted.
			If you delete a previously run job, your uploaded file will be deleted from the FUMA server.
		</p>
		<div style="margin-left: 40px">
			<p><h4><strong>1. Registration/Login</strong></h4>
				If you haven't registered yet, please do so from <a href="{{ url('/register') }}">Register</a>.<br/>
				Before you submit your GWAS summary statistics, please log in to your account.
				You can login from either <a href="{{ url('/login') }}">login</a> page or <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a> page directly.<br/><br/>
				<img src="{!! URL::asset('/image/homereg.png') !!}" style="width:80%"/><br/>
			</p><br/>

			<p><h4><strong>2. Submit new job at <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a></strong></h4>
				A new job stats with a GWAS summary statistics file. A variety of file formats are supported.
				Please refer the section of <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#prepare-input-files">Input files</a> for details.
				If your input file is an output from PLINK, SNPTEST or METAL, you can directly submit the file without specifying column names.<br/>
				The input GWAS summary statistics file could be a subset of SNPs (e.g. only SNPs which are interesting in your study), but in this case, MAGMA results are not relevant anymore.<br/>
				Optionally, if you would like to pre-specify lead SNPs, you can upload a file with 3 columns; rsID, chromosome and position.
				FUMA will then use these SNPs to select LD-related SNPs for annotation and mapping, instead of using lead SNPs identified by FUMA (it requires to disable an option for "identify additional lead SNPs").<br/>
				In addition, if you are interested in specific genomic regions, you can also provide them by uploading a file with 3 columns; chromosome, start and end position.
				FUMA will then use these genomic regions to select LD-related SNPs for annotation and mapping, instead of determining the regions itself.<br/>
				<br/>
				<img src="{!! URL::asset('/image/newjobfile.png') !!}" style="width:80%"/><br/>
			</p><br/>

			<p><h4><strong>3. Set parameters</strong></h4>
				On the same page as where you specify the input files, there are a variety of optional parameters that control the prioritization of genes.
				Please check your parameters carefully.
				The default settings are to perform identification of independent genome-wide significant SNPs at r<sup>2</sup> 0.6 and lead SNPs at r<sup>2</sup> 0.1, to maps SNPs to genes up to 10kb apart.<br/>
				To filter SNPs by specific functional annotations and to use eQTL mapping, please change parameters (please refer the parameter section of this tutorial from <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#parameters">here</a>).<br/>
				If all inputs are valid, 'Submit Job' button will be activated. Once you submit a job, this will be listed in My Jobs.<br/>
				Please do not navigate away from the page while your file is uploading (this may take up to couple of minutes depending on the file size and your internet speed).<br/>
				<br/>
				<img src="{!! URL::asset('/image/submitjob.png') !!}" style="width:70%"/><br/>
			</p><br/>

			<p><h4><strong>4. Check your results</strong></h4>
				After you submit files and parameter settings, a JOB has the status NEW which will be updated to QUEUES to RUNNING.
				Depending on the number of significant genomic regions, this may take between a couple of minutes and an hour.
				Once a JOB has finished running, you will receive an email.
				Unless an error occurred during the process, the email includes the link to the result page (this again requires login).
				You can also access to the results page from My Jobs page. <br/>
				The result page displays 4 additional side bars.<br/>
				<strong>Genome-wide plots</strong>: Manhattan plots and Q-Q plots for GWAS summary statistics and gene-based test by MAGMA, results of MAGMA gene-set analysis and tissue expression analysis.<br/>
				<strong>Summary of results</strong>: Summary of results such as the number of lead and LD-related SNPs, and mapped genes for overall and per identified genomic risk locus.<br/>
				<strong>Results</strong>: Tables of lead SNPs, genomic risk loci, candidate SNPs with annotations, eQTLs (only when eQTL mapping is performed), mapped genes and GWAS-catalog reported SNPs matched with candidate SNPs.
				You can also create interactive regional plots with functional annotations from this tab.<br/>
				<strong>Downloads</strong>: Download all results as text files.<br/>
				Details of all FUMA outputs are provided in the <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#outputs">SNP2GENE Outputs</a> section of this tutorial.<br/><br/>
				<img src="{!! URL::asset('/image/result.png') !!}" style="width:70%"/><br/><br/>
			</p>
		</div>
		<br/>

		<h3 id="geneQuery">Gene functions: Tissue specific gene expression and shared biological functions of a list of genes</h3>
		<p><strong>GENE2FUNC</strong> can take the list of prioritized genes from <strong>SNP2GENE</strong> or alternatively you can provide another list of pre-specified genes.
			Note that the genes prioritized in SNP2GENE are based on the functional and/or eQTL mapping, but not on MAGMA based gene output.
		</p>
		<p>For every input genes, <a href="{{ Config::get('app.subdir') }}/gene2func"><strong>GENE2FUNC</strong></a> provides information on expression in different tissue types,
			tissue specificity and enrichment of publicly available gene sets.<br/>
		</p>
		<div style="margin-left: 40px">
			<p><h4><strong>1. Submit a list of genes</strong></h4>
				Both a list of genes of interest and background genes (for hypergeometric test) are mandatory input.<br/>
				You can use mapped genes from SNP2GENE by clicking the "Submit" button in the result page (Results tab).<br/><br/>
				<img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="width:70%"/><br/>
			</p><br/>

			<p><h4><strong>2. Results</strong></h4>
				Once genes are submitted, four extra side bars are shown.<br/>
				<strong>Gene Expression</strong>: An interactive heatmap of gene expression of user selected data sets.<br/>
				<strong>Tissue Specificity</strong>: Bar plots for enrichment test of differentially expressed genes in a certain label compared to all other samples for a use selected data sets.
				See <a href="{{ Config::get('app.subdir') }}/tutorial#g2fOutputs">GENE2FUNC Outputs</a> section for details.<br/>
				<strong>Gene Sets</strong>: Plots and tables of enriched gene sets.<br/>
				<strong>Gene Table</strong>: Table of input genes with links to external databases; OMIM, Drugbank and GeneCards.<br/>
				Further details are provided in the <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#g2fOutputs">GENE2FUNC Outputs</a> section of  this tutorial.<br/><br/>
			</p>
		</div>
	</div>
</div>
