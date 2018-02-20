var sigSNPtable_selected=null;
var leadSNPtable_selected=null;
var lociTable_selected=null;
var annotPlotSelected;
var prefix = "gwas";
var geneTable;
var exp_data_title = {
	'gtex_v7_ts_avg_log2TPM': 'GTEx v7 53 tissue types',
	'gtex_v7_ts_general_avg_log2TPM': 'GTEx v7 30 general tissue types',
	'gtex_v6_ts_avg_log2RPKM': 'GTEx v6 53 tissue types',
	'gtex_v6_ts_general_avg_log2RPKM': 'GTEx v6 30 general tissue types',
	'bs_age_avg_log2RPKM': "BrainSpan 29 different ages of brain samples",
	"bs_dev_avg_log2RPKM": "BrainSpan 11 general developmental stages of brain samples"
}
$(document).ready(function(){
	// side bar and hash id
	var hashid = window.location.hash;
	if(hashid=="" && id.length==0){
		$('a[href="#GwasList"]').trigger('click');
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

	// get list of gwas
	getGwasList();

	// hide side and panels
	$('#resultsSide').hide();
	$('#annotPlotPanel').hide();

	// hide submit buttons for imgDown
	$('.ImgDownSubmit').hide();

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

	// $('#allfiles').on('click', function(){
	// 	$('#downFileCheck input').each(function(){
	// 		$(this).prop("checked", true);
	// 	});
	// });
	// $('#clearfiles').on('click', function(){
	// 	$('#downFileCheck input').each(function(){
	// 		$(this).prop("checked", false);
	// 	});
	// });

	// disable job submission
	$('#SubmitNewJob').prop('disabled', true);
	$('#geneQuerySubmit').prop('disabled', true);

	// disabel input
	$('#newJob :input').each(function(){
		$(this).prop('disabled', true);
	});

	// annot Plot select
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

	// load results
	if(id.length>0){
		loadResults();
	}

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
				gwasID: id
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
				GWplot(id);
				QQplot(id);
				MAGMAresults(id);
				ciMapCircosPlot(id, ciMap);
				showResultTables(prefix, id, posMap, eqtlMap, ciMap, orcol, becol, secol);
				summaryTable(id);
				paramTable(id);
				expHeatMap(id);
				tsEnrich(id);
				GeneSet(id);
				GeneTable(id);
				$('#gene_exp_data').on('change', function(){
					expHeatPlot(id, $('#gene_exp_data').val())
				})
				$('#resultsSide').show();
			}
		});
	}

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
});

function getGwasList(){
  $('#GwasList table tbody')
	  .empty()
	  .append('<tr><td colspan="6" style="text-align:center;">Retrieving data</td></tr>');

  $.getJSON( subdir + "/browse/getGwasList", function( data ) {
	  var items = '<tr><td colspan="6" style="text-align: center;">No Available GWAS Found</td></tr>';
	  if(data.length){
		  items = '';
		  $.each( data, function( key, val ) {
			  val.title = '<a href="'+subdir+'/browse/'+val.gwasID+'">'+val.title+'</a>';
			  items = items + "<tr><td>"+val.gwasID+"</td><td>"+val.title+"</td><td>"+val.PMID+"</td><td>"+val.year
				+"</td><td>"+val.created_at+"</td><td>"+val.updated_at+"</td></tr>";
		  });
	  }

	  // Put list in table
	  $('#GwasList table tbody')
		  .empty()
		  .append(items);
  });
}
