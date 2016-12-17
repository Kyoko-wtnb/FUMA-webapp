<div id="joblist-panel" class="sidePanel container" style="padding-top:50px; height:100%;">
    <h3>My Jobs</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">List of Jobs</div>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Job name</th>
                        <th>Submitdate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Retrieving data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Get Joblist
        $.getJSON( subdir + "/snp2gene/getJobList", function( data ) {
            var items = '<tr><td colspan="4">No Jobs Found</td></tr>';
            if(data){
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
    });
</script>
