var geneTable;
var prefix = "gene2func";
var exp_data_title = {
	'gtex_v7_ts_avg_log2TPM': 'GTEx v7 53 tissue types',
	'gtex_v7_ts_general_avg_log2TPM': 'GTEx v7 30 general tissue types',
	'gtex_v6_ts_avg_log2RPKM': 'GTEx v6 53 tissue types',
	'gtex_v6_ts_general_avg_log2RPKM': 'GTEx v6 30 general tissue types',
	'bs_age_avg_log2RPKM': "BrainSpan 29 different ages of brain samples",
	"bs_dev_avg_log2RPKM": "BrainSpan 11 general developmental stages of brain samples"
}
$(document).ready(function(){
	// hide submit buttons for imgDown
	$('.ImgDownSubmit').hide();

	// hash activate
	var hashid = window.location.hash;
	if(hashid=="" && status=="getJob"){
		$('a[href="#g2f_summaryPanel"]').trigger('click');
	}else if(hashid==""){
		$('a[href="#newquery"]').trigger('click');
	}else{
		$('a[href="'+hashid+'"]').trigger('click');
	}

	// gene type clear
	$('#bkgeneSelectClear').on('click', function(){
		$("#genetype option").each(function(){
			$(this).prop('selected', false);
		});
		checkInput();
	});

	// download file selection
	$('.allfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", true);
		});
	});
	$('.clearfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", false);
		});
	});

	updateList();

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
							url: subdir+"/gene2func/deleteJob",
							type: "POST",
							data: {
								jobID: $(this).val()
							},
							error: function(){
								alert("error at deleteJob");
							},
							complete: function(){
								updateList();
							}
						});
					}
				});
			}
		});
	});

	if(status.length==0 || status=="new"){
		checkInput();
		$('#resultSide').hide();
	}else if(status=="getJob"){
		// var id = jobID;

		checkInput();
		summaryTable(id);
		paramTable(id);
		expHeatMap(id);
		tsEnrich(id);
		GeneSet(id);
		GeneTable(id);
		$('#gene_exp_data').on('change', function(){
			expHeatPlot(id, $('#gene_exp_data').val())
		})
	}else if(status=="query"){
		$('#geneSubmit').attr("disabled", true);
		id = fumaJS.id;
		var filedir = fumaJS.filedir;
		var gtype = fumaJS.gtype;
		var gval = fumaJS.gval;
		var bkgtype = fumaJS.bkgtype;
		var bkgval = fumaJS.bkgval;
		var ensembl = fumaJS.ensembl;
		var gene_exp = fumaJS.gene_exp;
		var MHC = fumaJS.MHC;
		var adjPmeth = fumaJS.adjPmeth;
		var adjPcut = fumaJS.adjPcut;
		var minOverlap = fumaJS.minOverlap;

		if(gtype=="text"){
			$('#genes').val(gval.replace(/:/g, '\n'));
		}

		if(bkgtype == "select"){
			var tmp = document.getElementById('genetype');
			for(var i=0; i<tmp.options.length; i++){
				if(bkgval.indexOf(tmp.options[i].value)>=0){
					tmp.options[i].selected=true;
				}
			}
		}else if(bkgtype == "text"){
			$('#bkgenes').val(bkgval.replace(/:/g, '\n'));
		}

		$('#ensembl option').each(function(){
			if($(this).val()==ensembl){$(this).prop("selected", true)}
			else{$(this).prop("selected", false)}
		})

		gene_exp = gene_exp.split(":");
		$('#gene_exp option').each(function(){
			if(gene_exp.indexOf($(this).val())>=0){$(this).prop("selected", true)}
			else{$(this).prop("selected", false)}
		})

		if(MHC==1){
			$('#MHC').attr('checked', true);
		}

		d3.select('#expHeat').select('svg').remove();
		d3.select('#tsEnrichBar').select('svg').remove();
		$.ajax({
			url: "geneQuery",
			type: "POST",
			data: {
				filedir: filedir
			},
			beforeSend: function(){
				var options = {
					theme: "sk-circle",
					message: 'Running GENE2FUNC process. Please wait for a moment..'
				}
				HoldOn.open(options)
				$('#resultSide').hide()
			},
			success: function(){
				HoldOn.close()
			},
			complete: function(){
				window.location.href=subdir+'/gene2func/'+id;
			}
		});
	}
});

// Plot donwload
function ImgDown(name, type){
	$('#'+name+'Data').val($('#'+name).html());
	$('#'+name+'Type').val(type);
	$('#'+name+'JobID').val(id);
	$('#'+name+'FileName').val(name);
	$('#'+name+'Dir').val(prefix);
	$('#'+name+'Submit').trigger('click');
}

function checkInput(){
	var g = document.getElementById('genes').value;
	var gfile = $('#genesfile').val().length;
	if(g.length==0 && gfile==0){
		$('#GeneCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please either copy-paste or upload a liet of genes to test.</div>');
		$('#geneSubmit').attr("disabled", true);
	}else if(g.length>0 && gfile>0){
		$('#GeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used. To use uploaded file, please clear the text box.</div>');
	}else if(g.length > 0){
		$('#GeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used.</div>');
	}else if(gfile > 0){
		$('#GeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The uploaded file will be used.</div>');
	}

	var bkg_select = 0;
	var tmp = document.getElementById('genetype');
	for(var i=0; i<tmp.options.length; i++){
		if(tmp.options[i].selected===true){
			bkg_select = 1;
			break;
		}
	}
	var bkg = document.getElementById('bkgenes').value;
	var bkgfile = $('#bkgenesfile').val().length;

	if(bkg_select==0 && bkg.length==0 && bkgfile==0){
		$('#bkGeneCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide backgrond genes.</div>');
		$('#geneSubmit').attr("disabled", true);
	}else if(bkg_select==1 && (bkg.length>0 || bkgfile>0)){
		$('#bkGeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. You have provided multiple options. Selected gene types are used as background gene. To use other options, please clear the selection.</div>');
	}else if(bkg_select==1){
		$('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Selected gene type will be used as background.</div>');
	}else if(bkg.length>0 && bkgfile>0){
		$('#bkGeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. You have provided multiple options. Genes in the text box will be used as background. To use other options, please clear the text box.</div>');
	}else if(bkg.length>0){
		$('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used.</div>');
	}else if(bkgfile>0){
		$('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The uploaded file will be used.</div>');
	}

	if(g.length+gfile > 0 && (bkg_select==1 || bkg.length+bkgfile>0)){
		$('#geneSubmit').attr("disabled", false);
	}
};

function updateList(){
	$.getJSON( subdir + "/gene2func/getG2FJobList", function( data ){
		var items = '<tr><td colspan="7" style="text-align: center;">No Jobs Found</td></tr>';
		if(data.length){
			items = '';
			$.each( data, function( key, val ) {
				var status = '<a href="'+subdir+'/gene2func/'+val.jobID+'">load results</a>';
				items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title+"</td><td>"
					+val.snp2gene+"</td><td>"+val.snp2geneTitle+"</td><td>"
					+val.created_at+"</td><td>"+status+"</td>"
					+'<td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
					+val.jobID+'"/></td></tr>';
			});
		}
		// Put list in table
		$('#queryhistory table tbody')
			.empty()
			.append(items);
	});
}

function DownloadFiles(){
	var check = false;
	$('#downFileCheck input').each(function(){
		if($(this).is(":checked")==true){check=true;}
	})
	if(check){$('#download').prop('disabled', false)}
	else{$('#download').prop('disabled', true)}
}
