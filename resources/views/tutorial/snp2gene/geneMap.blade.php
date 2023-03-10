<h3 id="geneMap">Redo gene mapping for existing jobs</h3>
From FUMA v1.3.0, gene mapping can be re-performed for existing job with a different parameter setting.
This allows users to tune gene mapping parameters without performing entire process again,
by duplicating the selected job, which reduce a large amount of time.
<div style="padding-left: 40px;">
	<h4><strong>1. Select a jobID to duplicate</strong></h4>
	At the top of the page, users can select a jobID of existing job on the account.
	Note that only jobs which are succeeded are selectable.
	This is only available for users who already have SNP2GENE jobs.
	<h4><strong>2. Modify parameters</strong></h4>
	Once a jobID is selected, the previous parameters are automatically loaded.
	Modify parameters before submitting, otherwise the results will be same as the selected job.
	For chromatin interaction mapping, user custom files need to be re-uploaded.
	<br/>
	Users are allowed to provide new title and suffix "_copied_(jobID)" will be automatically
	added to the title.
	<br/>
	<span class="info"><i class="fa fa-info"></i>
		Users are only allowed to modify gene mapping parameters.
		Other parameters such as P-value or r2 threshold for defining independent significant SNPs
		cannot be changed.
	</span>
	<h4><strong>3. Submit</strong></h4>
	User can submit the job by clicking the button at the bottom of the page.
	After submission, the process is same as submitting a new SNP2GENE job,
	you will get an email once the process is done and results are accessible from your
	job list table.
</div>
