var prefix = "jobs";
$(document).ready(function(){
	getGeneMapIDs();
	geneMapCheckAll();

	$('.geneMapMultiSelect a').on('click',function(){
		var selection = $(this).siblings("select").attr("id");
		$("#"+selection+" option").each(function(){
			$(this).prop('selected', false);
		});
		geneMapCheckAll();
	});
})

function getGeneMapIDs(){
	$.ajax({
		url: subdir+"/snp2gene/getGeneMapIDs",
		type: "POST",
		error: function(){
			alert("error for getGeneMapIDs")
		},
		success: function(data){
			$('#geneMapID').html('<option value=0 selected>None</option>')
			data.forEach(function(d){
				$('#geneMapID').append('<option value='+d.jobID+'>'+d.jobID+' ('+d.title+')</option>');
			})
		}
	})
}

function loadGeneMap(){
	var geneMapID = $('#geneMapID').val();
	if(geneMapID > 0){
		$.ajax({
			url: subdir+"/snp2gene/loadParams",
			type: "POST",
			data: {
				"id": geneMapID
			},
			error: function(){
				alert("error for loadParams");
			},
			success: function(data){
				data = JSON.parse(data);
				geneMapSetParams(data);
			}
		})
	}
}

function geneMapSetParams(data){
	//posMap
	if(data.posMap=="1"){
		$('#geneMap_posMap').prop("checked", true);
	}else{
		$('#geneMap_posMap').prop("checked", false);
	}
	if(data.posMapWindowSize != "NA"){
		$('#geneMap_posMapAnnot').val('');
		geneMapCheckAll();
		$('#geneMap_posMapWindow').val(data.posMapWindowSize);
	}else{
		$('#geneMap_posMapWindow').val('');
		var annot = data.posMapAnnot.split(":");
		$("#geneMap_posMapAnnot option").each(function(){
			if(annot.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
	}
	if(data.posMapCADDth>0){
		$('#geneMap_posMapCADDcheck').prop("checked", true);
		$('#geneMap_posMapCADDth').val(data.posMapCADDth);
	}else{
		$('#geneMap_posMapCADDcheck').prop("checked", false);
	}
	if(data.posMapRDBth!="NA"){
		$('#geneMap_posMapRDBcheck').prop("checked", true);
		$('#geneMap_posMapRDBth').val(data.posMapRDBth);
	}else{
		$('#geneMap_posMapRDBcheck').prop("checked", false);
	}
	if(data.posMapChr15!="NA"){
		$('#geneMap_posMapChe15check').porp("checked", true);
		var cell = data.posMapChr15.split(":");
		$('#geneMap_posMapChr15Ts option').each(function(){
			if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#geneMap_posMapChr15Max').val(data.posMapChr15Max);
		$('#geneMap_posMapChr15Meth').val(data.posMapChr15Meth);
	}
	if(data.posMapAnnoDs!=undefined){
		$('#geneMap_posMapAnnoDs option').each(function(){
			if(data.posMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#geneMap_posMapAnnoMeth').val(data.posMapAnnoMeth);
	}


	//eqtl map
	if(data.eqtlMap == "1"){$('#geneMap_eqtlMap').prop("checked", true)}
	else{$('#geneMap_eqtlMap').prop("checked", false)}
	if(data.eqtlMaptss != "NA"){
		var ts = data.eqtlMaptss.split(":");
		$('#geneMap_eqtlMapTs option').each(function(){
			if(ts.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		geneMapCheckAll();
		if(data.eqtlMapSig=="1"){$('#sigeqtlCheck').prop("checked", true);}
		else{$('#sigeqtlCheck').prop("checked", false);$('#eqtlP').val(data.eqtlMapP);}
	}
	if(data.eqtlMapCADDth>0){
		$('#geneMap_eqtlMapCADDcheck').prop("checked", true);
		$('#geneMap_eqtlMapCADDth').val(data.eqtlMapCADDth);
	}else{
		$('#geneMap_eqtlMapCADDcheck').prop("checked", false);
	}
	if(data.eqtlMapRDBth!="NA"){
		$('#geneMap_eqtlMapRDBcheck').prop("checked", true);
		$('#geneMap_eqtlMapRDBth').val(data.eqtlMapRDBth);
	}else{
		$('#geneMap_eqtlMapRDBcheck').prop("checked", false);
	}
	if(data.eqtlMapChr15!="NA"){
		$('#geneMap_eqtlMapChr15check').prop("checked", true);
		var cell = data.eqtlMapChr15.split(":");
		$('#geneMap_eqtlMapChr15Ts option').each(function(){
			if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#geneMap_eqtlMapChr15Max').val(data.eqtlMapChr15Max);
		$('#geneMap_eqtlMapChr15Meth').val(data.eqtlMapChr15Meth);
	}
	if(data.eqtlMapAnnoDs!=undefined){
		$('#geneMap_eqtlMapAnnoDs option').each(function(){
			if(data.eqtlMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#geneMap_eqtlMapAnnoMeth').val(data.eqtlMapAnnoMeth);
	}

	// ci map
	if(data.ciMap!=null){
		if(data.ciMap=="1"){
			$('#geneMap_ciMap').prop('checked', true);
			$('#geneMap_ciMapFDR').val(data.ciMapFDR);
			$('#geneMap_ciMapPromWindow').val(data.ciMapPromWindow);
		}else{
			$('#chMap').prop('checked', false);
		}
		if(data.ciMapBuiltin!="NA"){
			var ts = data.ciMapBuiltin.split(":");
			$('#geneMap_ciMapBuiltin option').each(function(){
				if(ts.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
			geneMapCheckAll();
		}
		if(data.ciMapRoadmap!="NA"){
			var cell = data.ciMapRoadmap.split(":");
			$('#geneMap_ciMapRoadmap option').each(function(){
				if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
			geneMapCheckAll();
		}
		if(data.ciMapEnhFilt=="1"){$('#geneMap_ciMapEnhFilt').prop('checked', true)}
		if(data.ciMapPromFilt=="1"){$('#geneMap_ciMapPromFilt').prop('checked', true)}

		if(data.ciMapCADDth>0){
			$('#geneMap_ciMapCADDcheck').prop("checked", true);
			$('#geneMap_ciMapCADDth').val(data.ciMapCADDth);
		}else{
			$('#geneMap_ciMapCADDcheck').prop("checked", false);
		}
		if(data.ciMapRDBth!="NA"){
			$('#geneMap_ciMapRDBcheck').prop("checked", true);
			$('#geneMap_ciMapRDBth').val(data.ciMapRDBth);
		}else{
			$('#geneMap_ciMapRDBcheck').prop("checked", false);
		}
		if(data.ciMapChr15!="NA"){
			$('#geneMap_ciMapChe15check').porp("checked", true);
			var cell = data.ciMapChr15.split(":");
			$('#geneMap_ciMapChr15Ts option').each(function(){
				if(cell.indexOf($(this).val())>=0){$(this).prop('selected', true);}
				else{$(this).prop('selected', false);}
			});
			$('#geneMap_ciMapChr15Max').val(data.ciMapChr15Max);
			$('#geneMap_ciMapChr15Meth').val(data.ciMapChr15Meth);
		}
	}else{
		$('#geneMap_ciMap').prop('checked', false);
	}
	if(data.ciMapAnnoDs!=undefined){
		$('#geneMap_ciMapAnnoDs option').each(function(){
			if(data.ciMapAnnoDs.indexOf($(this).val())>=0){$(this).prop('selected', true);}
			else{$(this).prop('selected', false);}
		});
		$('#geneMap_ciMapAnnoMeth').val(data.ciMapAnnoMeth);
	}

	geneMapCheckAll();
}

function geneMapCheckAll(){
	var submit = true;
	var table;
	var tablecheck = true;

	//posMap table
	tablecheck=true;
	table = $('#geneMapPosMap')[0];
	var ms=0;
	$('#geneMap_posMapAnnot option').each(function(){
		if($(this).is(":selected")){ms++;}
	});
	if($('#geneMap_posMap').is(":checked")==true){
		$('.posMapOptions').show();
		$('#geneMap_posMapOptFilt').show();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#geneMap_posMapWindow').val().length>0){
			$('#geneMap_posMapAnnot').attr("disabled", true);
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-check"></i> OK. SNPs are mapped to genes up to '+$('#geneMap_posMapWindow').val()+' kb</div></td>');
		}else{
			$('#geneMap_posMapAnnot').attr("disabled", false);
			if(ms==0){
				$('#geneMap_posMapWindow').attr("disabled", false);
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> Please either specify maximum distance or select functional consequences of SNPs to map to genes.</div></td>');
			}else{
				$('#geneMap_posMapWindow').attr("disabled", true);
				$(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK. SNPs with selected functional consequences on genes will be mapped.</div></td>');
			}
		}
	}else{
		$('.posMapOptions').hide();
		$('#geneMap_posMapOptFilt').hide();
		if($('#geneMap_eqtlMap').is(":checked")==true || $('#geneMap_ciMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	table = $('#geneMap_posMapOptFiltTable')[0];
	if($('#geneMap_posMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#geneMap_posMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#geneMap_posMapCADDth').val())){
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

	if($('#geneMap_posMapRDBcheck').is(":checked")){
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

	if($('#geneMap_posMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#geneMap_posMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#geneMap_posMapChr15Gts option').each(function(){
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
		if(isNaN($('#geneMap_posMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#geneMap_posMapChr15Max').val()>=1 && $('#geneMap_posMapChr15Max').val()<=15){
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
		$('#geneMapPosMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#geneMapPosMapPanel').parent().attr("class", "panel panel-default");
	}

	//eqtlMap table
	tablecheck=true;
	table = $('#geneMapEqtlMap')[0];
	if($('#geneMap_eqtlMap').is(":checked")==true){
		$('.eqtlMapOptions').show();
		$('#geneMap_eqtlMapOptFilt').show();
		$(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#geneMap_eqtlMapTs option').each(function(){
			if($(this).is(":checked")==true){ts++;}
		});
		$('#geneMap_eqtlMapGts option').each(function(){
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
		$('#geneMap_eqtlMapOptFilt').hide();
		if($('#geneMap_posMap').is(":checked")==true || $('#geneMap_ciMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			submit=false;
			tablecheck=false;
		}
	}

	table = $('#geneMap_eqtlMapOptFiltTable')[0];
	if($('#geneMap_eqtlMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
		+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#geneMap_eqtlMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
			}else{
			if(isNaN($('#geneMap_eqtlMapCADDth').val())){
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

	if($('#geneMap_eqtlMapRDBcheck').is(":checked")){
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

	if($('#geneMap_eqtlMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#geneMap_eqtlMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#geneMap_eqtlMapChr15Gts option').each(function(){
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
		if(isNaN($('#geneMap_eqtlMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#geneMap_eqtlMapChr15Max').val()>=1 && $('#geneMap_eqtlMapChr15Max').val()<=15){
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
		$('#geneMapEqtlMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#geneMapEqtlMapPanel').parent().attr("class", "panel panel-default");
	}

	//ciMap table
	tablecheck=true;
	table = $('#geneMapCiMap')[0];
	if($('#geneMap_ciMap').is(":checked")==true){
		$('.ciMapOptions').show();
		$('#geneMap_ciMapOptFilt').show();
		var cidata = 0;
		$('#geneMap_ciMapBuiltin option').each(function(){
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
		if($('#geneMap_ciMapFDR').val().length==0){
			$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#geneMap_ciMapFDR').val())){
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input</div></td>');
				submit=false;
				tablecheck=false;
			}else if($('#geneMap_ciMapFDR').val()>=0 && $('#geneMap_ciMapFDR').val()<=1){
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-check"></i> OK.</div></td>');
			}else{
				$(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
					+'<i class="fa fa-ban"></i> Invalid input</div></td>');
				submit=false;
				tablecheck=false;
			}
		}
		if($('#geneMap_ciMapPromWindow').val().length==0){
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input.</div></td>');
		}else{
			$(table.rows[4].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-"check></i> OK.</div></td>');
		}
		cidata = 0;
		$('#geneMap_ciMapRoadmap option').each(function(){
			if($(this).is(":checked")==true){cidata++;}
		});
		if(cidata==0){
			$('#geneMap_ciMapEnhFilt').prop("disabled", true);
			$('#geneMap_ciMapPromFilt').prop("disabled", true);
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-triangle"></i> Select at least one epigenome to eable tis option.</div></td>');
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-triangle"></i> Select at least one epigenome to eable tis option.</div></td>');
		}else{
			$('#geneMap_ciMapEnhFilt').prop("disabled", false);
			$('#geneMap_ciMapPromFilt').prop("disabled", false);
			$(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-"check></i> OK.</div></td>');
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
			$(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}
	}else{
		$('.ciMapOptions').hide();
		$('#geneMap_ciMapOptFilt').hide();
		if($('#geneMap_posMap').is(':checked')==true || $('#geneMap_eqtlMap').is(':checked')==true){
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
		}else{
			$(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Please select at least one of the positional, eQTL or chromatin interaction mapping.</div></td>');
			tablecheck=false;
			submit=false;
		}
	}

	table = $('#geneMap_ciMapOptFiltTable')[0];
	if($('#geneMap_ciMapCADDcheck').is(":checked")==true){
		$(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		if($('#geneMap_ciMapCADDth').val().length==0){
			$(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if(isNaN($('#geneMap_ciMapCADDth').val())){
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

	if($('#geneMap_ciMapRDBcheck').is(":checked")){
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

	if($('#geneMap_ciMapChr15check').is(":checked")){
		$(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
			+'<i class="fa fa-check"></i> OK.</div></td>');
		var ts = 0;
		$('#geneMap_ciMapChr15Ts option').each(function(){
			if($(this).is(":selected")){ts++;}
		});
		$('#geneMap_ciMapChr15Gts option').each(function(){
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
		if(isNaN($('#geneMap_ciMapChr15Max').val())){
			$(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
				+'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
			submit=false;
			tablecheck=false;
		}else{
			if($('#geneMap_ciMapChr15Max').val()>=1 && $('#geneMap_ciMapChr15Max').val()<=15){
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
		$('#geneMapCiMapPanel').parent().attr("class", "panel panel-danger");
	}else{
		$('#geneMapCiMapPanel').parent().attr("class", "panel panel-default");
	}

	//check if job is selected
	if($('#geneMapID').val()<=0){submit=false;}

	if(submit){$('#SubmitGeneMap').attr("disabled", false)}
	else{$('#SubmitGeneMap').attr("disabled", true)}
}
