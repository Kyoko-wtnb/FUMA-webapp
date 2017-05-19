<div id="GwasList" class="sidePanel container" style="min-height:100vh;">
    <h3>GWAS list</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">List of available GWAS <tab></div>
        </div>
        <div class="panel-body">
	      	<table class="table">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Title</th>
						<th>PMID</th>
						<th>Year</th>
	                    <th>Created date</th>
	                    <th>Last update</th>
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

<script>
    $(document).ready(function() {
      // Get Joblist
      function getGwasList(){
        $('#GwasList table tbody')
            .empty()
            .append('<tr><td colspan="6" style="text-align:center;">Retrieving data</td></tr>');

        $.getJSON( subdir + "/browse/getGwasList", function( data ) {
            var items = '<tr><td colspan="6" style="text-align: center;">No Available GWAS Found</td></tr>';
            if(data.length){
                items = '';
                $.each( data, function( key, val ) {
                    val.title = '<a href="'+subdir+'/browse/'+val.gwasID+'">Go to results</a>';
                    items = items + "<tr><td>"+val.gwasID+"</td><td>"+val.title+"</td><td>"+val.PMID+"</td><td>"+val.year+"</td><td>"
                      +"</td><td>"+val.created_at+"</td><td>"+val.updated_at+"</td></tr>";
                });
            }

            // Put list in table
            $('#GwasList table tbody')
                .empty()
                .append(items);
        });
      }

      getGwasList();

    });
</script>
