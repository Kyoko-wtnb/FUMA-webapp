var sigSNPtable_selected=null;
var leadSNPtable_selected=null;
var lociTable_selected=null;
// var SNPtable_selected=null;
var annotPlotSelected;
var prefix = "jobs";
$(document).ready(function(){
	// hide submit buttons for imgDown
	$('.ImgDownSubmit').hide();
	$('#annotPlotPanel').hide();
	$('#g2fSubmitBtn').hide();

	var hashid = window.location.hash;
	if(hashid=="" && status.length==0){
		$('a[href="#newJob"]').trigger('click');
	}else if(hashid==""){
		$('a[href="#genomePlots"]').trigger('click');
	}else{
		$('a[href="'+hashid+'"]').trigger('click');
	}

	$('.RegionalPlotOn').on('click', function(){
		$('#regionalPlot').show();
	});
	$('.RegionalPlotOff').on('click', function(){
		$('#regionalPlot').hide();
	});

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
							url: subdir+'/'+page+'/deleteJob',
							type: "POST",
							data: {
								jobID: $(this).val()
							},
							error: function(){
								alert("error at deleteJob");
							},
							complete: function(){
								getJobList();
								getjobIDs();
								getGeneMapIDs();
							}
						});
					}
				});
			}
		});
	});

	$('.level1').on('click', function(){
		var cur = $(this);
		var selected = $(this).is(":selected");

		while(cur.next().hasClass('level2')){
			cur = cur.next();
			cur.prop('selected', selected);
		}
	});

	$('.level2').on('click', function(){
		var cur = $(this);
		var selected = $(this).is(":selected");

		var total = true;
		while(cur.next().hasClass('level2')){
			cur = cur.next();
			total = (total && cur.is(':selected'));
		}
		cur = $(this);
		while(cur.prev().hasClass('level2')){
			cur = cur.prev();
			total = (total && cur.is(':selected'));
		}
		cur.prev().prop('selected', total);
	});

	if(status.length==0){
	}else if(status=="fileFormatGWAS"){
		$('a[href="#newJob"]').trigger('click');
		$('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
		+'<b>Provided file (GWAS summary statistics) format was not valid. Text files (with any extention), zip file or gzip files are acceptable.</b>'
		+'</div>');
	}else if(status=="fileFormatLead"){
		$('a[href="#newJob"]').trigger('click');
		$('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
		+'<b>Provided file (Pre-defined lead SNPs) format was not valid. Only plain text files (with any extention) is acceptable.</b>'
		+'</div>');
	}else if(status=="fileFormatRegions"){
		$('a[href="#newJob"]').trigger('click');
		$('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
		+'<b>Provided file (Pre-defined genomic regions) format was not valid. Only plain text files (with any extention) is acceptable.</b>'
		+'</div>');
	}else{
		$('#annotPlotSubmit').attr("disabled", true);
		$('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or genomic risk loci to plot. If you haven\'t selected any row, please click one of the row of lead SNP or genomic risk loci table.</div>');
		if($('#annotPlot_Chrom15').is(":checked")==false){
			$('#annotPlotChr15Opt').hide();
		}

		var jobStatus;
		$.get({
			url: subdir + '/'+page+'/checkJobStatus/'+id,
			error: function(){
				alert("ERROR: checkJobStatus")
			},
			success: function(data){
				jobStatus = data;
			},
			complete: function(){
				if(jobStatus=="OK"){
					loadResults();
				}else if(jobStatus=="ERROR:005"){
					error5();
				}
			}
		});

		function loadResults(){
			var posMap;
			var eqtlMap;
			var ciMap;
			var orcol;
			var becol;
			var secol;
			var magma;
			$.ajax({
				url: subdir+'/'+page+'/getParams',
				type: 'POST',
				data:{
					jobID: id
				},
				error: function(){
					alert("JobQuery getParams error");
				},
				success: function(data){
					// $('#test').html(data)
					var tmp = data.split(":");
					posMap = parseInt(tmp[0]);
					eqtlMap = parseInt(tmp[1]);
					ciMap = parseInt(tmp[2])
					orcol = tmp[3];
					becol = tmp[4];
					secol = tmp[5];
					magma = tmp[6];
				},
				complete: function(){
					// jobInfo(id);
					GWplot(id);
					QQplot(id);
					MAGMAresults(id, magma);
					ciMapCircosPlot(id, ciMap);
					showResultTables(prefix, id, posMap, eqtlMap, ciMap, orcol, becol, secol);
					$('#GWplotSide').show();
					$('#resultsSide').show();
				}
			});
		}

		function error5(){
			GWplot(id);
			QQplot(id);
			MAGMAresults(id);
			$.ajax({
				url: subdir+'/'+page+'/Error5',
				type: 'POST',
				data: {
					jobID: id
				},
				error: function(){
					alert("Error5 read file error");
				},
				success: function(data){
					var temp = JSON.parse(data);
					var out = "<thead><tr>";
					$.each(temp[0], function(key, d){
						out += "<th>"+d+"</th>";
					});
					out += "</tr></thead><tbody>";
					for(var i=1; i<temp.length; i++){
						out += "<tr>"
						$.each(temp[i], function(key, d){
							out += "<td>"+d+"</td>";
						});
						out += "</tr>";
					}
					out += "</tbody>";
					$('#topSNPs').html(out);
				}
			});
			$('#results').show();
			$('#GWplotSide').show();
			$('#Error5Side').show();
		}
	}

	// download file selection
	$('.allfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", true);
		});
		DownloadFiles();
	});
	$('.clearfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", false);
		});
		DownloadFiles();
	});

	// annotPlot Chr15 tissue selection clear click
	$('#annotPlotChr15TsClear').on('click', function(){
		var tmp = document.getElementById('annotPlotChr15Ts');
		for(var i=0; i<tmp.options.length; i++){
			tmp.options[i].selected=false;
		}
	});
	$('#annotPlotChr15GtsClear').on('click', function(){
		var tmp = document.getElementById('annotPlotChr15Gts');
		for(var i=0; i<tmp.options.length; i++){
			tmp.options[i].selected=false;
		}
	});

	$('#publishCancel').on('click', function(){
		$('#modalPublish').modal('hide');
	});

	$('#publishSubmit').on('click', function(){
		$.ajax({
			url: subdir+'/'+page+'/publish',
			type: 'POST',
			data: {
				jobID: $('#publish_s2g_jobID').val(),
				g2f_jobID: $('#publish_g2f_jobID').val(),
				title: $('#publish_title').val(),
				author: $('#publish_author').val(),
				email: $('#publish_email').val(),
				publication: $('#publish_publication').val(),
				sumstats_link: $('#publish_sumstats_link').val(),
				sumstats_ref: $('#publish_sumstats_ref').val(),
				notes: $('#publish_notes').val()
			},
			beforeSend: function(){
				var options = {
					theme: "sk-circle",
					message: 'Publishing the result, please wait for a second.'
				}
				HoldOn.open(options);
				$('#modalPublish').modal('hide');
			},
			error: function(){
				alert('JQuery publish error');
			},
			success: function(){
				HoldOn.close()
				swal({
					title: "The selected job has been published ",
					type: "success",
					showCancelButton: false,
					closeOnConfirm: true,
				});
			}
		});
	});
});

function getJobList(){
	$('#joblist-panel table tbody')
		.empty()
		.append('<tr><td colspan="6" style="text-align:center;">Retrieving data</td></tr>');
	$.getJSON( subdir + '/'+page+'/getJobList', function( data ){
		var items = '<tr><td colspan="6" style="text-align: center;">No Jobs Found</td></tr>';
		if(data.length){
			items = '';
			$.each( data, function( key, val ) {
				var g2fbutton = 'Not available';
				var publish = 'Not available';
				if(val.status == 'OK'){
					val.status = '<a href="'+subdir+'/'+page+'/'+val.jobID+'">Go to results</a>';
					g2fbutton = '<button class="btn btn-default btn-xs" value="'+val.jobID+'" onclick="g2fbtn('+val.jobID+');">GENE2FUNC</button>';
					publish = '<button class="btn btn-default btn-xs" value="'+val.jobID+'" onclick="publish('+val.jobID+');">Publish</button>';
				}else if(val.status == 'ERROR:005'){
					val.status = '<a href="'+subdir+'/'+page+'/'+val.jobID+'">ERROR:005</a>';
				}
				items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title
					+"</td><td>"+val.created_at+"</td><td>"+val.status+"</td><td>"+g2fbutton
					+'</td><td>'+publish+'</td><td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
					+val.jobID+'"/></td></tr>';
			});
		}

		// Put list in table
		$('#joblist-panel table tbody')
			.empty()
			.append(items);
	});
}

function g2fbtn(id){
	$('#g2fSubmitJobID').val(id);
	$('#g2fSubmitBtn').trigger('click');
}

function publish(id){
	$.ajax({
		url: subdir+"/"+page+"/checkPublish",
		type: "POST",
		data: {
			id: id
		},
		error: function(){
			alert("JQuery chechPublish error")
		},
		success: function(data){
			data = JSON.parse(data);
			if(data.publish==0){
				$('#publish_s2g_jobID').val(id);
				$('#publish_s2g_jobID_text').html(id);
				if(data.g2f != undefined){
					$('#publish_g2f_jobID').val(data.g2f);
				}else{
					$('#publish_g2f_jobID').val('');
				}
				$('#publish_title').val(data.title);
				$('#publish_author').val(data.author);
				$('#publish_email').val(data.email);
				checkPublishInput()
				$('#modalPublish').modal('show');
			}else{
				swal({
					title: "Already published",
					text: "The selected job is already published. Please contact the developper to modify/delete the published results.",
					type: "warning",
					showCancelButton: false,
					closeOnConfirm: true,
				});
			}
		}
	})
}

function checkPublishInput(){
	var submit = false;
	if($('#publish_title').val().length>0 && $('#publish_author').val().length>0 && $('#publish_email').val().length>0){submit = true}
	if(submit){$('#publishSubmit').prop('disabled', false)}
	else{$('#publishSubmit').prop('disabled', true)}
}
