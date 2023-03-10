<div id="publish" class="sidePanel container" style="padding-top:50px;">
	<h2>Publish results</h2>
	<div style="margin-left: 40px;">
		<h3>How to publish FUMA results</h3>
		<div style="margin-left: 40px;">
			<h4><strong>1. Prepare jobs in SNP2GENE and GENE2FUNC</strong></h4>
			You can publish any of existing SNP2GENE job in your account but only
			the ones without any error.
			MAGMA results are optional but it's highly recommended to include them too.
			You can also publish GENE2FUNC results together with SNP2GENE results if
			the GENE2FUNC job is performed for mapped genes from the corresponding SNP2GENE job.

			<h4><strong>2. Publish results</strong></h4>
			You can publish your results from your job list on SNP2GENE page.
			There is a "publish" button for each SNP2GENE job.<br/>
			<br/>
			<img src="{!! URL::asset('/image/publish_btn.png') !!}" style="width:80%"/><br/>
			<br/>
			When you click the "publish" button, a popup will open where you can specify some features of the job.
			Please fill the features in the table below as much as possible before submit your job.
			<br/><br/>
			<table class="table table-bordered table-ondensed" style="width:90%;">
				<thead>
					<th style="width:25%;">Features</th><th>Description</th>
				</thead>
				<tbody>
					<tr>
						<td>Selected SNP2GENE jobID</td>
						<td>
							Auto filled when you click the "publish" button.
							This value is not changeable.
						</td>
					</tr>
					<tr>
						<td>Corresponding GENE2FUNC jobID</td>
						<td>
							Auto filled when there is a recognized GENE2FUNC job.
							FUMA recognizes a matched GENE2FUNC job only when the GENE2FUNC job has been
							performed by using "GENE2FUNC" button (internal submission).
							If you manually submit GENE2FUNC for the corresponding SNP2GENE job, you can manually specify here.
						</td>
					</tr>
					<tr>
						<td>Title</td>
						<td>
							Title of the published job should be self-descriptive,
							although it is auto filled by the title of the selected SNP2GENE job.
							If the title is not clear enough,
							the developer might contact you to provide a sufficient information.
						</td>
					</tr>
					<tr>
						<td>Author</td>
						<td>
							Auto filled with the user name but please provide your full name.
						</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>
							Auto filled with your registered email address.
							Please provide an email address that is reachable to you.
							Any future modification/deletion of the published job will be only processed
							when it is requested by the matched email.
						</td>
					</tr>
					<tr>
						<td>Phenotype</td>
						<td>
							Please provide phenotype of the GWAS if applicable.
						</td>
					</tr>
					<tr>
						<td>Publication</td>
						<td>
							This is the publication where the selected SNP2GENE job is described (not the reference to the summary statistics).
							This can be any format as long as users are able to find the publication.
							Please provide PubMed ID if possible (e.g. PMID: 29184056).
							If you don't have publication yet, please let the developer know once the publication becomes available.
							You can also provide preprint DOI.
						</td>
					</tr>
					<tr>
						<td>Link to summary statistics</td>
						<td>
							If the summary statistics used in this job is publicly available, please provide the original link.
						</td>
					</tr>
					<tr>
						<td>Reference of summary statistics</td>
						<td>
							This should be the original publication of the summary statistics.
							This can be same as the publication above when a new GWAS result is presented in the publication.
						</td>
					</tr>
					<tr>
						<td>Notes</td>
						<td>
							You can provide any additional information here (max 300 characters).
							For example, when there are multiple summary statistics available from the same study,
							you should specify which result this is referring to.
						</td>
					</tr>
				</tbody>
			</table>

			<h4><strong>3. Check your published results</strong></h4>
			Published job will be listed in the "Browse Public Results" page.
			Please have a look at your published job to check if there is any problem.
			<br/>
			<img src="{!! URL::asset('/image/publish_list.png') !!}" style="width:80%"/><br/>
			<br/>
		</div>

		<h3>Modify/delete published result</h3>
		<div style="margin-left: 40px;">
			Modification and deletion of published jobs can be done by similar way as publishing the job.
			From the SNP2GENE job list, the published jobs now have "edit" button instead of "publish" button.
			By clicking this, you can update the features listed above or delete the published job.
			Note that deleting the published job does not delete the job from your account.
			At the same time, when you delete the original job from your account, it does not delete the corresponding published job.
			If you want to modify/delete published job whose original job is deleted from your account,
			please contact to the FUMA developer, (Kyoko Watanabe: k.watanabe@vu.nl).
			Please also provide id of the published job together.<br/>
			<span class="info"><i class="fa fa-info"></i> Modification/deletion is only possible when the user is logged in
				with the same email address as the entry.
			</span>
		</div>

		<h3>Users' responsibility</h3>
		<div style="margin-left: 40px;">
			We do not take any responsibility for your published results.
			Any question specific to a published result from other users
			is required to answer by the user who published the result
			not the FUMA developer.
		</div>

		<h3>What other users can do?</h3>
		<div style="margin-left: 40px;">
			Any FUMA users are able to browse your published results and download any text files and images.
			They are also able to create regional plot with annotations.<br/>
			<span class="info"><i class="fa fa-info"></i> The "Browse Public Result" page does not require users to login.</span>
		</div>
	</div>
</div>
