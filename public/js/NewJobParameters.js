var gwasFileSize = 0;
var ciFileSize = 0;
$(document).ready(function(){
	$("#newJob").show();
	$("#GWplotSide").hide();
	$("#Error5Side").hide();
	$("#resultsSide").hide();
	$('#SubmitNewJob').attr('disabled',true);
	$('#go2job').attr('disabled',true);
	$('.posMapOptions').hide();
	$('#posMapOptFilt').hide();
	$('.eqtlMapOptions').hide();
	$('#eqtlMapOptFilt').hide();
	$('.ciMapOptions').hide();
	$('#ciMapOptFilt').hide();

	getjobIDs();
	CheckAll();
	$('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");

	// $('.multiSelect.clear').on('click',function(){
	// 	var selection = $(this).siblings("select").attr("id");
	// 	$("#"+selection+" option").each(function(){
	// 		$(this).prop('selected', false);
	// 	});
	// 	CheckAll();
	// });

	$('.multiSelect a').on('click',function(){
		var selection = $(this).siblings("select").attr("id");
		if($(this).hasClass('all')){
			$("#"+selection+" option").each(function(){
				$(this).prop('selected', true);
			});
		}else if($(this).hasClass('clear')){
			$("#"+selection+" option").each(function(){
				$(this).prop('selected', false);
			});
		}
		CheckAll();
	});

	$("#GWASsummary").bind('change', function(){
		if($(this).val().length>0){
			gwasFileSize = this.files[0].size;
		}
		CheckAll();
	});

	// input parameters data toggle
	$('.panel-heading.input a').on('click', function(){
		if($(this).attr('class')=="active"){
			$(this).removeClass('active');
			$(this).children('i').attr('class', 'fa fa-chevron-down');
		}else{
			$(this).addClass('active');
			$(this).children('i').attr('class', 'fa fa-chevron-up');
		}
	});

	$('#ciFileAdd').on('click',function(){
		var n = 0;
		$('.ciFileID').each(function(){
			if(parseInt($(this).val()) > n){
				n = parseInt($(this).val());
			}
		})
		n += 1;
		$('#ciFiles').append('<span class="form-inline ciFile"><br/>File '+n+': data type <input type="text" class="form-control" placeholder="e.g. HiC or ChIA-PET" name="ciMapType'+n+'" id="ciMapType'+n
			+'"><tab><button type="button" class="btn btn-default btn-xs ciFileDel" onclick="ciFileDel(this)">delete</button><tab><input type="file" class="form-control-file ciMapFile" name="ciMapFile'+n+'" id="ciMapFile'+n
			+'" onchange="ciFileCheck()"><input type="hidden" class="ciFileID" id="ciFileID'+n+'" name="ciFileID'+n+'" value="'+n+'"></span>');
	})
});

function getjobIDs(){
	$.ajax({
		url: subdir+"/snp2gene/getjobIDs",
		type: "POST",
		error: function(){
			alert("error for getjobIDs");
		},
		success: function(data){
			$('#paramsID').html('<option value=0 selected>None</option>');
			data.forEach(function(d){
				$('#paramsID').append('<option value='+d.jobID+'>'+d.jobID+' ('+d.title+')</option>');
			})
		}
	})
}

function loadParams(){
	var paramsID = $('#paramsID').val();
	if(paramsID > 0){
		$.ajax({
			url: subdir+"/snp2gene/loadParams",
			type: "POST",
			data: {
				"id": paramsID
			},
			error: function(){
				alert("error for loadParams");
			},
			success: function(data){
				data = JSON.parse(data);
				setParams(data);
			}
		})
	}
}

function setParams(data){
	// columns
	if(data.chrcol!="NA"){$('#chrcol').val(data.chrcol)}
	else{$('#chrcol').val('')}
	if(data.poscol!="NA"){$('#poscol').val(data.poscol)}
	else{$('#poscol').val('')}
	if(data.rsIDcol!="NA"){$('#rsIDcol').val(data.rsIDcol)}
	else{$('#rsIDcol').val('')}
	if(data.pcol!="NA"){$('#pcol').val(data.pcol)}
	else{$('#pcol').val('')}
	if(data.eacol!="NA"){$('#eacol').val(data.eacol)}
	else{$('#eacol').val('')}
	if(data.neacol!="NA"){$('#neacol').val(data.neacol)}
	else{$('#neacol').val('')}
	if(data.orcol!="NA"){$('#orcol').val(data.orcol)}
	else{$('#orcol').val('')}
	if(data.becol!="NA"){$('#becol').val(data.becol)}
	else{$('#becol').val('')}
	if(data.secol!="NA"){$('#secol').val(data.secol)}
	else{$('#secol').val('')}

	if(data.addleadSNPs=="1"){$('#addleadSNPs').prop('checked', true);}
	else{$('#addleadSNPs').prop('checked', false);}

	// params
	if(data.N == "NA"){
		$('#N').val('');
		CheckAll();
		$('#Ncol').val(data.Ncol);
	}else{
		$('#Ncol').val('');
		CheckAll();
		$('#N').val(data.N);
	}
	$('#leadP').val(data.leadP);
	$('#gwasP').val(data.gwasP);
	$('#r2').val(data.r2);
	if(data.r2_2 != undefined){$('#r2_2').val(data.r2_2);}
	$('#refpanel').val(data.refpanel+"/"+data.pop);
	if($("#refpanel").val()==undefined){
		$('#refpanel').val("1KG/Phase3/EUR");
	}

	if(data.refSNPs=="1"){$('#refSNPs').val("Yes")}
	else($('#refSNPs').val("No"))
	$('#maf').val(data.MAF);
	$('#mergeDist').val(data.mergeDist);

	//posMap
	if(data.posMap=="1"){
		$('#posMap').prop("checked", true);
	}else{
		$('#posMap').prop("checked", false);
	}
	if(data.posMapWindowSize != "NA"){
		$('#posMapAnnot').val('');
		CheckAll();
		$('#posMapWindow').val(data.posMapWindowSize);
	}else{
		$('#posMapWindow').val('');
		var annot = data.posMapAnnot.split(":");
		$("#posMapAnnot option").each(function(){
			if(annot.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
	}
	if(data.posMapCADDth>0){
		$('#posMapCADDcheck').prop("checked", true);
		$('#posMapCADDth').val(data.posMapCADDth);
	}else{
		$('#posMapCADDcheck').prop("checked", false);
	}
	if(data.posMapRDBth!="NA"){
		$('#posMapRDBcheck').prop("checked", true);
		$('#posMapRDBth').val(data.posMapRDBth);
	}else{
		$('#posMapRDBcheck').prop("checked", false);
	}
	if(data.posMapChr15!="NA"){
		$('#posMapChe15check').porp("checked", true);
		var cell = data.posMapChr15.split(":");
		if(cell.indexOf("all")>=0){
			$('#posMapChr15Ts option').each(function(){
				$(this).prop('selected', true);
			});
		}else{
			$('#posMapChr15Ts option').each(function(){
				if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
		}
		$('#posMapChr15Max').val(data.posMapChr15Max);
		$('#posMapChr15Meth').val(data.posMapChr15Meth);
	}
	if(data.posMapAnnoDs!=undefined){
		$('#posMapAnnoDs option').each(function(){
			if(data.posMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#posMapAnnoMeth').val(data.posMapAnnoMeth);
	}

	//eqtl map
	if(data.eqtlMap == "1"){$('#eqtlMap').prop("checked", true)}
	else{$('#eqtlMap').prop("checked", false)}
	if(data.eqtlMaptss != "NA"){
		var ts = data.eqtlMaptss.split(":");
		if(ts.indexOf("all")>=0){
			$('#eqtlMapTs option').each(function(){
				$(this).prop('selected', true);
			});
		}else{
			$('#eqtlMapTs option').each(function(){
				if(ts.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
		}
		CheckAll();
		if(data.eqtlMapSig=="1"){$('#sigeqtlCheck').prop("checked", true);}
		else{$('#sigeqtlCheck').prop("checked", false);$('#eqtlP').val(data.eqtlMapP);}
	}
	if(data.eqtlMapCADDth>0){
		$('#eqtlMapCADDcheck').prop("checked", true);
		$('#eqtlMapCADDth').val(data.eqtlMapCADDth);
	}else{
		$('#eqtlMapCADDcheck').prop("checked", false);
	}
	if(data.eqtlMapRDBth!="NA"){
		$('#eqtlMapRDBcheck').prop("checked", true);
		$('#eqtlMapRDBth').val(data.eqtlMapRDBth);
	}else{
		$('#eqtlMapRDBcheck').prop("checked", false);
	}
	if(data.eqtlMapChr15!="NA"){
		$('#eqtlMapChr15check').prop("checked", true);
		var cell = data.eqtlMapChr15.split(":");
		if(cell.indexOf("all")>=0){
			$('#eqtlMapChr15Ts option').each(function(){
				$(this).prop('selected', true);
			});
		}else{
			$('#eqtlMapChr15Ts option').each(function(){
				if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
		}
		$('#eqtlMapChr15Max').val(data.eqtlMapChr15Max);
		$('#eqtlMapChr15Meth').val(data.eqtlMapChr15Meth);
	}
	if(data.eqtlMapAnnoDs!=undefined){
		$('#eqtlMapAnnoDs option').each(function(){
			if(data.eqtlMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#eqtlMapAnnoMeth').val(data.eqtlMapAnnoMeth);
	}
	if(data.ciMap!=null){
		if(data.ciMap=="1"){
			$('#ciMap').prop('checked', true);
			$('#ciMapFDR').val(data.ciMapFDR);
			$('#ciMapPromWindow').val(data.ciMapPromWindow);
		}else{
			$('#chMap').prop('checked', false);
		}
		if(data.ciMapBuiltin!="NA"){
			var ts = data.ciMapBuiltin.split(":");
			if(ts.indexOf("all")>=0){
				$('#ciMapBuiltin option').each(function(){
					$(this).prop('selected', true);
				});
			}else{
				$('#ciMapBuiltin option').each(function(){
					if(ts.indexOf($(this).val())>=0){$(this).prop('selected', true);}
					else{$(this).prop('selected', false);}
				});
			}
			CheckAll();
		}
		if(data.ciMapRoadmap!="NA"){
			var cell = data.ciMapRoadmap.split(":");
			if(cell.indexOf("all")>=0){
				$('#ciMapRoadmap option').each(function(){
					$(this).prop('selected', true);
				});
			}else{
				$('#ciMapRoadmap option').each(function(){
					if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
					else{$(this).prop('selected', false);}
				});
			}
			CheckAll();
		}
		if(data.ciMapEnhFilt=="1"){$('#ciMapEnhFilt').prop('checked', true)}
		if(data.ciMapPromFilt=="1"){$('#ciMapPromFilt').prop('checked', true)}

		if(data.ciMapCADDth>0){
			$('#ciMapCADDcheck').prop("checked", true);
			$('#ciMapCADDth').val(data.ciMapCADDth);
		}else{
			$('#ciMapCADDcheck').prop("checked", false);
		}
		if(data.ciMapRDBth!="NA"){
			$('#ciMapRDBcheck').prop("checked", true);
			$('#ciMapRDBth').val(data.ciMapRDBth);
		}else{
			$('#ciMapRDBcheck').prop("checked", false);
		}
		if(data.ciMapChr15!="NA"){
			$('#ciMapChe15check').porp("checked", true);
			var cell = data.ciMapChr15.split(":");
			$('#ciMapChr15Ts option').each(function(){
				if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
			$('#ciMapChr15Max').val(data.ciMapChr15Max);
			$('#ciMapChr15Meth').val(data.ciMapChr15Meth);
		}
		if(data.ciMapAnnoDs!=undefined){
			$('#ciMapAnnoDs option').each(function(){
				if(data.ciMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
			$('#ciMapAnnoMeth').val(data.ciMapAnnoMeth);
		}
	}else{
		$('#ciMap').prop('checked', false);
	}

	// gene type
	$('#ensembl option').each(function(){
		if($(this).val()==data.ensembl){$(this).prop('selected', true)}
		else{$(this).prop('selected', false)}
	})
	$('#genetype option').each(function(){
		if(data.genetype.indexOf($(this).val())>=0){$(this).prop('selected', true);}
		else{$(this).prop('selected', false);}
	});
	// MHC region
	if(data.exMHC=="1"){$('#MHCregion').prop('checked', true);}
	else{$('#MHCregion').prop('checked', false);}
	$('#MHCopt option').each(function(){
		if($(this).val()==data.MHCopt){$(this).prop('selected', true);}
	});
	if(data.extMHC!="NA"){$('#extMHCregion').val(data.extMHC)}

	// MAGMA
	if(data["magma"]!=undefined){
		if(data.magma=="1"){
			$('#magma').prop('checked', true)
			if(data.magma_window!=undefined){
				$('#magma_window').val(data.magma_window)
			}
			var ds = data.magma_exp.split(":");
			$('#magma_exp option').each(function(){
				if(ds.indexOf($(this).val())>=0){$(this).prop('selected', true)}
				else{$(this).prop('selected', false)}
			})
		}else{
			$('#magma').prop('checked', false)
		}
	}

	CheckAll();
}

function ciFileCheck(){
	var maxSize = 0;
	var nFiles = 0;
	$('.ciMapFile').each(function(){
		if($(this).val().length>0){
			nFiles += 1;
			if(this.files[0].size > maxSize){
				maxSize = this.files[0].size;
			}
		}
	})
	ciFileSize = maxSize;
	$('#ciFileN').val(nFiles);
	CheckAll();
}

function ciFileDel(del){
	$(del).parent().parent().remove();
	ciFileCheck();
}

function CheckAll(){
	var submit = true;
	var table;
	var tablecheck = true;

	//NewJobFile table
	table = $('#NewJobFiles')[0];
	if($('#GWASsummary').val().length==0){
		if($('#egGWAS').is(':checked')){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. An example file will be used.</div></td>');
			$('#N').val(21389);
			$('#chrcol').attr("disabled", true);
			$('#poscol').attr("disabled", true);
			$('#rsIDcol').attr("disabled", true);
			$('#pcol').attr("disabled", true);
			$('#eacol').attr("disabled", true);
			$('#neacol').attr("disabled", true);
			$('#orcol').attr("disabled", true);
			$('#becol').attr("disabled", true);
			$('#secol').attr("disabled", true);
			submit=true;
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory<br/>The maximum file size is 600Mb. Please gzip if your file is bigger than 600Mb.</div></td>');
			$('#chrcol').attr("disabled", true);
			$('#poscol').attr("disabled", true);
			$('#rsIDcol').attr("disabled", true);
			$('#pcol').attr("disabled", true);
			$('#eacol').attr("disabled", true);
			$('#neacol').attr("disabled", true);
			$('#orcol').attr("disabled", true);
			$('#becol').attr("disabled", true);
			$('#secol').attr("disabled", true);
			submit=false;
			tablecheck=false;
		}
	}else{
		$('#chrcol').attr("disabled", false);
		$('#poscol').attr("disabled", false);
		$('#rsIDcol').attr("disabled", false);
		$('#pcol').attr("disabled", false);
		$('#eacol').attr("disabled", false);
		$('#neacol').attr("disabled", false);
		$('#orcol').attr("disabled", false);
		$('#becol').attr("disabled", false);
		$('#secol').attr("disabled", false);
		if(gwasFileSize>=600000000){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> The file size if above 600Mb. Please gzip your file.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. Please check your input file format.</div></td>');
			submit=true;
		}
	}

	if($('#leadSNPs').val().length==0){
		$(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional. <br/>This is only valid when predefined lead SNPs are provided.</div></td>');
		$('#addleadSNPs').attr("disabled", true);
	}else{
		$('#addleadSNPs').attr("disabled", false);
		$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#addleadSNPs').is(":checked")==true){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else{
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}
	}

	if($('#regions').val().length==0){
		$(table.rows[4].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}else{
		$(table.rows[4].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}

	if(tablecheck==false){
		$('#NewJobFilesPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#NewJobFilesPanel').parent().attr("class", "panel panel-default");
	}

	//NewJobParams table
	tablecheck=true;
	table=$('#NewJobParams')[0];
	if($('#N').val().length==0 && $('#Ncol').val().length==0){
		$('#N').attr("disabled", false);
		$('#Ncol').attr("disabled", false);
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Mandatory input. <br/>Please provide either total sample size of GWAS study or column name of N in input file.</div></td>');
		submit=false;
		tablecheck=false;
	}else if($('#N').val().length>0){
		$('#Ncol').attr("disabled", true);
		if($('#N').val()<50){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Smple size must be greater than 50.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. The total sample size will be applied to all SNPs.</div></td>');
		}
	}else{
		$('#N').attr("disabled", true);
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK. The defined column will be used for sample size per SNP.</div></td>');
	}

	if($('#leadP').val().length==0){
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#leadP').val())){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else if($('#leadP').val()>=0 && $('#leadP').val()<=1e-5){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else if($('#leadP').val()>1e-5 && $('#leadP').val()<=1){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> The maximum lead SNP P-value is 1e-5.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	if($('#gwasP').val().length==0){
		$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#gwasP').val())){
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else if($('#gwasP').val()>=0 && $('#gwasP').val()<=1){
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else{
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	if($('#r2').val().length==0){
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#r2').val())){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else if($('#r2').val()>=0.05 && $('#r2').val()<=1){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else if($('#r2').val()<0.05){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> The minimum r2 is 0.05.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	if($('#r2_2').val().length==0){
		$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#r2_2').val())){
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else if($('#r2_2').val()>=0.05 && $('#r2_2').val()<=1){
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else if($('#r2_2').val()<0.05){
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> The minimum r2 is 0.05.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

  // Population is always OK [4]
  // refSNPs is always OK [5]

	if($('#maf').val().length==0){
		$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#maf').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else if($('#maf').val()>=0 && $('#maf').val()<=1){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else{
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	if($('#mergeDist').val().length==0){
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-ban"></i> Invalid input</div></td>');
		submit=false;
		tablecheck=false;
	}else{
		if(isNaN($('#mergeDist').val())){
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}
	}

	if(tablecheck==false){
		$('#NewJobParamsPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#NewJobParamsPanel').parent().attr("class", "panel panel-default");
	}

	//posMap table
	tablecheck=true;
	table = $('#NewJobPosMap')[0];
	var ms=0;
	$('#posMapAnnot option').each(function(){
		if($(this).is(":selected")){ms++;}
	});
	if($('#posMap').is(":checked")==true){
		$('.posMapOptions').show();
		$('#posMapOptFilt').show();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#posMapWindow').val().length>0){
			$('#posMapAnnot').attr("disabled", true);
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. SNPs are mapped to genes up to '+$('#posMapWindow').val()+' kb</div></td>');
		}else{
			$('#posMapAnnot').attr("disabled", false);
			if(ms==0){
				$('#posMapWindow').attr("disabled", false);
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> Please either specify maximum distance or select functional consequences of SNPs to map to genes.</div></td>');
			}else{
				$('#posMapWindow').attr("disabled", true);
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK. SNPs with selected functional consequences on genes will be mapped.</div></td>');
			}
		}
	}else{
		$('.posMapOptions').hide();
		$('#posMapOptFilt').hide();
		if($('#eqtlMap').is(":checked")==true || $('#ciMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	table = $('#posMapOptFiltTable')[0];
	if($('#posMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#posMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#posMapCADDth').val())){
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
				submit=false;
				tablecheck=false;
			}else{
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}
		}
	}else{
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#posMapRDBcheck').is(":checked")){
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#posMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#posMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#posMapChr15Gts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		if(ts==0){
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one tissue/cell type.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}
		if(isNaN($('#posMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#posMapChr15Max').val()>=1 && $('#posMapChr15Max').val()<=15){
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if(tablecheck==false){
		$('#NewJobPosMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#NewJobPosMapPanel').parent().attr("class", "panel panel-default");
	}

	//eqtlMap table
	tablecheck=true;
	table = $('#NewJobEqtlMap')[0];
	if($('#eqtlMap').is(":checked")==true){
		$('.eqtlMapOptions').show();
		$('#eqtlMapOptFilt').show();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#eqtlMapTs option').each(function(){
			if($(this).is(":checked")==true){ts++;}
		});
		$('#eqtlMapGts option').each(function(){
			if($(this).is(":checked")==true){ts++;}
		});
		if(ts>0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}else{
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one tissue type.</div></td>');
			submit=false;
			tablecheck=false;
		}
		if($('#sigeqtlCheck').is(":checked")==true){
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. Only significant snp-gene pairs will be used.</div></td>');
		}else{
			if($('#eqtlP').val().length==0){
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Please either check only significant eQTLs or type P-value threshold.</div></td>');
				submit=false;
				tablecheck=false;
			}else if(isNaN($('#eqtlP').val())){
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
				submit=false;
				tablecheck=false;
			}else if($('#eqtlP').val()>=0 && $('#eqtlP').val()<=1){
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
	}else{
		$('.eqtlMapOptions').hide();
		$('#eqtlMapOptFilt').hide();
		if($('#posMap').is(":checked")==true || $('#ciMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	table = $('#eqtlMapOptFiltTable')[0];
	if($('#eqtlMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#eqtlMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
			}else{
			if(isNaN($('#eqtlMapCADDth').val())){
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
				submit=false;
				tablecheck=false;
			}else{
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}
		}
	}else{
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#eqtlMapRDBcheck').is(":checked")){
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#eqtlMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#eqtlMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#eqtlMapChr15Gts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		if(ts==0){
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one tissue/cell type.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}
		if(isNaN($('#eqtlMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#eqtlMapChr15Max').val()>=1 && $('#eqtlMapChr15Max').val()<=15){
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if(tablecheck==false){
		$('#NewJobEqtlMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#NewJobEqtlMapPanel').parent().attr("class", "panel panel-default");
	}

	//ciMap table
	tablecheck=true;
	table = $('#NewJobCiMap')[0];
	if($('#ciMap').is(":checked")==true){
		$('.ciMapOptions').show();
		$('#ciMapOptFilt').show();
		var cidata = 0;
		$('#ciMapBuiltin option').each(function(){
			if($(this).is(":checked")==true){cidata++;}
		});
		if(cidata>0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
			if($('#ciFiles .ciFile').length>0){
				if(ciFileSize>600000000){
					$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
						+'<i class="fa fa-ban"></i>  The size of the last selected file id above 600Mb. Please gzip or filter the file to upload.</div></td>');
				}else{
					var filecheck = false;
					$('.ciMapFile').each(function(){
						if($(this).val().length>0){filecheck=true; return false;}
					})
					if(filecheck){
						$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
							+'<i class="fa fa-check"></i> OK.</div></td>');
					}else{
						$(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
							+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
					}
				}
			}else{
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
			}
		}else if($('#ciFiles .ciFile').length>0){
			if(ciFileSize>600000000){
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Please select at least one build in data or upload interaction matrix.</div></td>');
				$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> The size of the last selected file id above 600Mb. Please gzip or filter the file to upload.</div></td>');
				submit=false;
				tablecheck=false;
			}else{
				var filecheck = false;
				$('.ciMapFile').each(function(){
					if($(this).val().length>0){filecheck=true; return false;}
				})
				if(filecheck){
					$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
						+'<i class="fa fa-check"></i> OK.</div></td>');
					$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
						+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
				}else{
					$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
						+'<i class="fa fa-ban"></i> Please select at least one build in data or upload interaction matrix.</div></td>');
					$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
						+'<i class="fa fa-ban"></i> Please select at least one build in data or upload interaction matrix.</div></td>');
					submit=false;
					tablecheck=false;
				}
			}
		}else{
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one build in data or upload interaction matrix.</div></td>');
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one build in data or upload interaction matrix.</div></td>');
			submit=false;
			tablecheck=false;
		}
		if($('#ciMapFDR').val().length==0){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#ciMapFDR').val())){
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input</div></td>');
				submit=false;
				tablecheck=false;
			}else if($('#ciMapFDR').val()>=0 && $('#ciMapFDR').val()<=1){
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
		if($('#ciMapPromWindow').val().length==0){
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
		}else{
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-"check></i> OK.</div></td>');
		}
		cidata = 0;
		$('#ciMapRoadmap option').each(function(){
			if($(this).is(":checked")==true){cidata++;}
		});
		if(cidata==0){
			$('#ciMapEnhFilt').prop("disabled", true);
			$('#ciMapPromFilt').prop("disabled", true);
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-triangle"></i> Select at least one epigenome to eable this option.</div></td>');
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-triangle"></i> Select at least one epigenome to eable this option.</div></td>');
		}else{
			$('#ciMapEnhFilt').prop("disabled", false);
			$('#ciMapPromFilt').prop("disabled", false);
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-"check></i> OK.</div></td>');
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}
	}else{
		$('.ciMapOptions').hide();
		$('#ciMapOptFilt').hide();
		if($('#posMap').is(':checked')==true || $('#eqtlMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			tablecheck=false;
			submit=false;
		}
	}

	table = $('#ciMapOptFiltTable')[0];
	if($('#ciMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#ciMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#ciMapCADDth').val())){
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
				submit=false;
				tablecheck=false;
			}else{
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}
		}
	}else{
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#ciMapRDBcheck').is(":checked")){
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[2].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if($('#ciMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#ciMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#ciMapChr15Gts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		if(ts==0){
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one tissue/cell type.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}
		if(isNaN($('#ciMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#ciMapChr15Max').val()>=1 && $('#ciMapChr15Max').val()<=15){
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
	}else{
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if(tablecheck==false){
		$('#NewJobCiMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#NewJobCiMapPanel').parent().attr("class", "panel panel-default");
	}


	// GeneTypes
	table = $("#NewJobGene")["0"];
	var genetype_count = $("#genetype :selected").length;
	if(genetype_count==0) {
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+'<i class="fa fa-check"></i> Select at least one gene type.</div></td>');
		submit = false;
	} else {
		$(table.rows[1].cells[2]).html('<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+ '<i class="fa fa-check"></i> OK.</div>');
	}

	//MHC table
	tablecheck=true;
	table = $('#NewJobMHC')[0];
	if($('#MHCregion').is(':checked')){
		$('#MHCopt').show();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK. Normal MHC region will be excluded '+$('#MHCopt option:selected').text()+'.</div></td>');
		if($('#extMHCregion').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. Entered region will be excluded.</div></td>');
		}
	}else{
		$('#MHCopt').hide();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	//MAGMA
	tablecheck=true;
	table = $('#NewJobMAGMA')[0];
	if($('#magma').is(':checked')){
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK. MAGMA will be performed.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var mw = $('#magma_window').val().split(",")
		$.each(mw, function(i, x){
			if(parseFloat(x)>50){
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> Maximum window size is limited to 50kb.</div></td>');
				submit=false;
			}
		})
		var ds = 0;
		$('#magma_exp option').each(function(){
			if($(this).is(":selected")){ds++;}
		});
		if(ds==0){
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one data set.</div></td>');
			submit=false;
		}else{
			$(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK.</div></td>');
		}
	}else{
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional. MAGMA results will not be available.</div></td>');
		$(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		$(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
	}

	if(submit){$('#SubmitNewJob').attr("disabled", false)}
	else{$('#SubmitNewJob').attr("disabled", true)}
}
