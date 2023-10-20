var prefix = "celltype";
$(document).ready(function(){
	// hide submit buttons for imgDown
	$('.ImgDownSubmit').hide();
	$('#cellSubmit').attr("disabled", true);
	$('#resultSide').hide();

	// hash activate
	var hashid = window.location.hash;
	if(hashid=="" && status.length==0){
		$('a[href="#newJob"]').trigger('click');
	}else if(hashid==""){
		$('a[href="#result"]').trigger('click');
	}else{
		$('a[href="'+hashid+'"]').trigger('click');
	}

	// download file selection
	$('.allfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			if(!$(this).is(':disabled')){
				$(this).prop("checked", true);
			}
		});
		DownloadFiles();
	});
	$('.clearfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", false);
		});
		DownloadFiles();
	});

	getJobList();
	$('#refreshTable').on('click', function(){
		getJobList();
	});

	// Get SNP2GENE job IDs
	$.ajax({
		url: subdir+"/celltype/getS2GIDs",
		type: "POST",
		error: function(){
			alert("error for getS2GIDs");
		},
		success: function(data){
			$('#s2gID').html('<option value=0 selected>None</option>');
			data.forEach(function(d){
				$('#s2gID').append('<option value='+d.jobID+'>'+d.jobID+' ('+d.title+')</option>');
			})
		},
		complete: function(){
			CheckInput();
		}
	})

	// Delete jobs
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
							url: subdir+'/'+page+'/deleteJob',
							type: "POST",
							data: {
								jobID: $(this).val()
							},
							error: function(){
								alert("error at deleteJob");
							},
							success: function (resdata) {
								// chech if resdata is null
								if (resdata != "") {
									alert(resdata);
								}
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

	if(status.length==0){
	}else{
		var jobStatus;
		$.get({
			url: subdir+'/'+page+'/checkJobStatus/'+id,
			error: function(){
				alert("ERROR: checkJobStatus")
			},
			success: function(data){
				jobStatus = data;
			},
			complete: function(){
				if(jobStatus=="OK"){
					$('#resultSide').show();
					loadResults(id);
				}
			}
		});
	}
});

function CheckInput(){
	var check = true;
	var s2gID = $('#s2gID').val();
	var fileName = $('#genes_raw').val();
	var ds = $("#cellDataSets :selected").length;

	// If all datasets are selected, not allow step 2 and step 3
	var all = $("#cellDataSets :not(:selected)").length;
	if(all==0){
		$('#step2').prop('checked', false).prop('disabled', true);
		$('#step3').prop('checked', false).prop('disabled', true);
	}else{
		$('#step2').prop('disabled', false);
		$('#step3').prop('disabled', false);
	}

	if(s2gID==0 && fileName.length==0){
		check = false;
		$('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please either select SNP2GENE jobID or upload a file.</div>')
	}else{
		if(ds==0){
			check = false;
			$('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please select at least one single-cell expression data set.</div>')
		}else if(s2gID>0){
			var filecheck = false;
			$.ajax({
				url: subdir+"/celltype/checkMagmaFile",
				type: 'POST',
				data: { id: s2gID },
				error: function(){alert("error from checkMagmaFile")},
				success: function(data){
					if(data==1){filecheck=true}
				},
				complete: function(){
					if(!filecheck){
						check = false;
						$('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">The seleted SNP2GENE job does not have valid MAGMA output.</div>')
					}else if(fileName.length>0){
						$('#CheckInput').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">Both SNP2GENE job ID and upload file are provided. Selected SNP2GENE job will be used.</div>')
					}else{
						$('#CheckInput').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The MAGMA gene analysis results will be obtained from the selected SNP2GENE job.</div>')
					}
				}
			});
		}else{
			if(fileName.endsWith(".genes.raw")){
				$('#CheckInput').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The selected file will be uploaded.</div>')
			}else{
				check = false;
				$('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">The seleted file does not have extension "genes.raw".</div>')
			}
		}
	}

	if(check){$('#cellSubmit').attr("disabled", false);}
	else{$('#cellSubmit').attr("disabled", true);}
}

function getJobList(){
	$('#joblist table tbody')
		.empty()
		.append('<tr><td colspan="7" style="text-align:center;">Retrieving data</td></tr>');
	$.getJSON( subdir+'/'+page+'/getJobList', function( data ){
		var items = '<tr><td colspan="7" style="text-align: center;">No Jobs Found</td></tr>';
		if(data.length){
			items = '';
			$.each( data, function( key, val ) {

				if (val.parent != null && val.parent.removed_at != null) {
					val.parent = null;
				}
				
				if(val.status == 'OK'){
					val.status = '<a href="'+subdir+'/'+page+'/'+val.jobID+'">Go to results</a>';
				}
				items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title
					+"</td><td>"+(val.parent != null ? val.parent.jobID : '-')+"</td><td>"+(val.parent != null ? val.parent.title : '-')
					+"</td><td>"+val.created_at+"</td><td>"+val.status
					+'</td><td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
					+val.jobID+'"/></td></tr>';
			});
		}

		// Put list in table
		$('#joblist table tbody')
			.empty()
			.append(items);
	});
}
