<div id="joblist-panel" class="sidePanel container" style="min-height:100vh;">
    <h3>My Jobs</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">List of Jobs <tab><a id="refreshTable"><i class="fa fa-refresh"></i></a></div>
        </div>
        <div class="panel-body">
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
                            <b>ERROR</b>: An error occured durting the process. Please refer bottom of the page for details.">
                            <i class="fa fa-question-circle-o fa-lg"></i>
                          </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center;">Retrieving data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- <div>
      <h4>Error types</h4>
      ERROR:001 <br/>
      ERROR:002 <br/>
      ERROR:003 <br/>
      ERROR:004 <br/>
      ERROR:005 <br/>
      ERROR:006 <br/>
      ERROR:007 <br/>
      ERROR:008 <br/>
      ERROR:009 <br/>
      ERROR:010 <br/>
    </div> -->
</div>

<script>
    $(document).ready(function() {

        // Get Joblist
        function getJobList(){
          $('#joblist-panel table tbody')
              .empty()
              .append('<tr><td colspan="4" style="text-align:center;">Retrieving data</td></tr>');

          $.getJSON( subdir + "/snp2gene/getJobList", function( data ) {
              var items = '<tr><td colspan="4">No Jobs Found</td></tr>';
              if(data.length){
                  items = '';
                  $.each( data, function( key, val ) {
                      if(val.status == 'OK'){
                          val.status = '<a href="'+subdir+'/snp2gene/'+val.jobID+'">Go to results</a>';
                      }
                      items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title+"</td><td>"+val.created_at+"</td><td>"+val.status+"</td></tr>";
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
    });
</script>
