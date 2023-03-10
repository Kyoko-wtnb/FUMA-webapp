<div id="joblist" class="sidePanel container" style="min-height:80vh; padding-top:50px;">
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
						<th>SNP2GENE job ID</th>
						<th>SNP2GENE title</th>
						<th>Submit date</th>
						<th>Status
							<a class="infoPop" data-toggle="popover" data-html="true" data-content="<b>NEW: </b>The job has been submitted.<br/>
								<b>QUEUED</b>: The job has been dispatched to queue.<br/><b>RUNNING</b>: The job is running.<br/>
								<b>Go to results</b>: The job has been completed. This is linked to result page.<br/>
								<b>ERROR</b>: An error occurred during the process. Please refer email for detail message.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</th>
						<th>Select</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="7" style="text-align:center;">Retrieving data</td>
					</tr>
				</tbody>
			</table>
	    </div>
	</div>
</div>
