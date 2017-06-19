<script>
    $(document).ready(function() {
      // Get Joblist
      function getJobList(){
        $('#joblist-panel table tbody')
            .empty()
            .append('<tr><td colspan="6" style="text-align:center;">Retrieving data</td></tr>');
        $.getJSON( subdir + "/snp2gene/getJobList", function( data ) {
            var items = '<tr><td colspan="6" style="text-align: center;">No Jobs Found</td></tr>';
            if(data.length){
                items = '';
                $.each( data, function( key, val ) {
                    var g2fbutton = 'Not available';
                    if(val.status == 'OK'){
                      val.status = '<a href="'+subdir+'/snp2gene/'+val.jobID+'">Go to results</a>';
                      g2fbutton = '<form action="'+subdir+'/gene2func/geneSubmit" method="post">'
                        +'<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                        +'<input type="hidden" name="jobID" value="'+val.jobID+'"/>'
                        +'<span class="form-inline">'
                          +'<input type="submit" class="btn btn-xs" id="geneQuerySubmit" name="geneQuerySubmit" value="GENE2FUNC">'
                        +'</span>'
                      +'</form>';
                    }else if(val.status == 'ERROR:005'){
                      val.status = '<a href="'+subdir+'/snp2gene/'+val.jobID+'">ERROR:005</a>';
                    }
                    items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title
                      +"</td><td>"+val.created_at+"</td><td>"+val.status+"</td><td>"+g2fbutton
                      +'</td><td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
                      +val.jobID+'"/></td></tr>';
                });
            }

            // Put list in table
            $('#joblist-panel table tbody')
                .empty()
                .append(items);
        });
      }

      getJobList();

      $('#refreshTable').on('click', function(){
        getJobList();
      });

      $('#deleteJob').on('click', function(){
        swal({
          title: "Are you sure?",
          text: "Do you really want to remove selected jobs?",
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true,
        }, function(isConfirm){
          if (isConfirm){
            $('.deleteJobCheck').each(function(){
              if($(this).is(":checked")){
                $.ajax({
                  url: subdir+"/snp2gene/deleteJob",
                  type: "POST",
                  data: {
                    jobID: $(this).val()
                  },
                  error: function(){
                    alert("error at deleteJob");
                  },
                  complete: function(){
                    getJobList();
                  }
                });
              }
            });
          }
        });
      });
    });
</script>

<div id="joblist-panel" class="sidePanel container" style="min-height:100vh;">
	<h3>My Jobs</h3>
	<div class="panel panel-default">
	    <div class="panel-heading">
	        <div class="panel-title">List of Jobs <tab><a id="refreshTable"><i class="fa fa-refresh"></i></a></div>
	    </div>
	    <div class="panel-body">
			<button class="btn btn-sm" id="deleteJob" name="deleteJob" style="float:right; margin-right:20px;">Delete selected jobs</button>
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
	    </div>
	</div>
</div>
