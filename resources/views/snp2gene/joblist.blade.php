<div id="joblist-panel" class="sidePanel container" style="min-height:100vh;">
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
						<th>Status
							<a class="infoPop" data-toggle="popover" data-html="true" data-content="<b>NEW: </b>The job has been submitted.<br/>
							<b>QUEUED</b>: The job has beed dispatched to queue.<br/><b>RUNNING</b>: The job is running.<br/>
							<b>Go to results</b>: The job has been completed. This is linked to result page.<br/>
							<b>ERROR</b>: An error occured durting the process. Please refer email for detail message.">
							<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</th>
						<th>Jump to GENE2FUNC</th>
						<th>Select</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="6" style="text-align:center;">Retrieving data</td>
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
