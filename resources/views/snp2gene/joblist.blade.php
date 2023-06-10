<div id="joblist-panel" class="sidePanel container" style="min-height:80vh;">
	<h3>My Jobs</h3>
	<div class="panel panel-default">
	    <div class="panel-heading">
	        <div class="panel-title">List of Jobs <tab><a id="refreshTable"><i class="fa fa-refresh"></i></a></div>
	    </div>
	    <div class="panel-body">
			<button class="btn btn-default btn-sm" id="deleteJob" name="deleteJob" style="float:right; margin-right:20px;">Delete selected jobs</button>
			<table class="table">
				<thead>
					<tr>
						<th>Job ID</th>
						<th>Job name</th>
						<th>Submit date</th>
						<th>Started at</th>
						<th>Completed at</th>
						<th>Status
							<a class="infoPop" data-toggle="popover" data-html="true" data-content="<b>NEW: </b>The job has been submitted.<br/>
								<b>QUEUED</b>: The job has been dispatched to queue.<br/><b>RUNNING</b>: The job is running.<br/>
								<b>Go to results</b>: The job has been completed. This is linked to result page.<br/>
								<b>ERROR</b>: An error occurred during the process. Please refer email for detail message.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</th>
						<th>Jump to GENE2FUNC</th>
						<th>Publish</th>
						<th>Select</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="7" style="text-align:center;">Retrieving data</td>
					</tr>
				</tbody>
			</table>
			<form action="{{ Config::get('app.subdir') }}/gene2func/geneSubmit" method="post" target="_blank">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" id="g2fSubmitJobID" name="jobID" value=""/>
				<input type="submit" class="btn btn-default" id="g2fSubmitBtn" name="g2fSubmitBtn">
			</form>
	    </div>
	</div>
</div>

<!-- Modal for publish results -->
<div class="modal fade" id="modalPublish" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header"><h4><strong id="modalTitle">Publish your results</strong></h4></div>
			<div class="modal-body">
				<table class="table table-bordered">
					<thead>
						<th style="width:35%;">Feature</th>
						<th>Value</th>
					</thead>
					<tbody>
						<tr>
							<td>Selected SNP2GENE jobID<sup style="color:red;">*</sup></td>
							<td><span id="publish_s2g_jobID_text"></span>
								<input type="hidden" id="publish_s2g_jobID" name="publish_s2g_jobID"/>
							</td>
						</tr>
						<tr>
							<td>
								Corresponding GENE2FUNC jobID
								<a class="infoPop" data-toggle="popover" data-content="
								If you have performed GENE2FUNC for the selected SNP2GENE job, please specify GENE2FUNC jobID.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_g2f_jobID" name="publish_g2f_jobID" class="form-control" type="number">
							</td>
						</tr>
						<tr>
							<td>Title<sup style="color:red;">*</sup>
								<a class="infoPop" data-toggle="popover" data-content="
								Please provide self-descriptive title for the job.
								If the title is not clear enough, the developer might contact you to provide a sufficient information.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_title" name="publish_title" class="form-control" type="text" onkeyup="checkPublishInput();" onpaste="checkPublishInput();" oninput="checkPublishInput();">
							</td>
						</tr>
						<tr>
							<td>Author<sup style="color:red;">*</sup><br>
								<span class="info"><i class="fa fa-info"></i> Please provide your full name.</span>
							</td>
							<td>
								<input id="publish_author" name="publish_author" class="form-control" type="text" onkeyup="checkPublishInput();" onpaste="checkPublishInput();" oninput="checkPublishInput();">
							</td>
						</tr>
						<tr>
							<td>Email<sup style="color:red;">*</sup>
								<a class="infoPop" data-toggle="popover" data-content="
								Please provide an email address that is reachable to you.
								Any future modification/deletion of the published job will be only processed
								when it is requested by the matched email.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_email" name="publish_email" class="form-control" type="text" onkeyup="checkPublishInput();" onpaste="checkPublishInput();" oninput="checkPublishInput();">
							</td>
						</tr>
						<tr>
							<td>Phenotype</td>
							<td>
								<input id="publish_phenotype" name="publish_phenotype" class="form-control" type="text">
							</td>
						</tr>
						<tr>
							<td>Publication
								<a class="infoPop" data-toggle="popover" data-content="
								This is the publication where the selected SNP2GENE job is described (not the reference to the summary statistics).
								This can be any format as long as users are able to find the publication.
								Please provide PubMed ID if possible (e.g. PMID: 29184056).
								If you don't have publication yet, please let the developer know once the publication becomes available.
								You can also provide preprint DOI.
								">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_publication" name="publish_publication" class="form-control" type="text">
							</td>
						</tr>
						<tr>
							<td>Link to summary statistics
								<a class="infoPop" data-toggle="popover" data-content="
								If the summary statistics used in this job is publicly available, please provide the original link.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_sumstats_link" name="publish_sumstats_link" class="form-control" type="text">
							</td>
						</tr>
						<tr>
							<td>Reference of summary statistics
								<a class="infoPop" data-toggle="popover" data-content="
								This should be the original publication of the summary statistics.
								This can be same as the publication above when a new GWAS result is presented in the publication.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
							</td>
							<td>
								<input id="publish_sumstats_ref" name="publish_sumstats_ref" class="form-control" type="text">
							</td>
						</tr>
						<tr>
							<td>
								Notes
								<a class="infoPop" data-toggle="popover" data-content="
								Please add any additional information here.
								For example, when there are multiple summary statistics available from the same study,
								you should specify which result this is referring to.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a>
								<br/>
								<span class="info"><i class="fa fa-info"></i> Max 300 characters.</span>
							</td>
							<td>
								<textarea id="publish_notes" name="publish_notes" rows="5" cols="30" maxlength="300"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
				<span style="color:red;"><sup>*</sup>Required input</span><br/><br/>
				<button class="btn btn-default btn-sm" id="publishSubmit">Submit</button>
				<button class="btn btn-default btn-sm" id="publishUpdate">Update</button>
				<button class="btn btn-default btn-sm" id="publishDelete">Delete</button>
				<button class="btn btn-default btn-sm" id="publishCancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
