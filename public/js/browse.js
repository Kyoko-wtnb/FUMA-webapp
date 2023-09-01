var sigSNPtable_selected=null;
var leadSNPtable_selected=null;
var lociTable_selected=null;
var annotPlotSelected;
var prefix = "jobs";
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
	$('#resultsSideG2F').hide();
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
		var g2f=0;
		$.ajax({
			url: subdir+'/'+page+'/checkG2F',
			type: 'POST',
			data:{
				id: id,
			},
			error: function(){
				alert('checkG2F error');
			},
			success: function(data){
				if(data.length>0){g2f = data;}
			},
			complete: function(){
				loadResults();
								
				if(g2f){
					prefix = 'gene2func';
					summaryTable(g2f);
					parametersTable(g2f);
					expHeatMap(g2f);
					tsEnrich(g2f);
					GeneSet(g2f);
					GeneTable(g2f);
					$('#gene_exp_data').on('change', function(){
						expHeatPlot(id, $('#gene_exp_data').val())
					})
					$('#resultsSideG2F').show();
				}
			}
		})
	}

	function loadResults() {
		var posMap;
		var eqtlMap;
		var ciMap;
		var orcol;
		var becol;
		var secol;
		var magma;
		$.ajax({
			url: '/browse' + '/getParams',
			type: 'POST',
			data: {
				jobID: id
			},
			error: function () {
				alert("JobQuery getParams error");
				return;
			},
			success: function (data) {
				var tmp = data.split(":");
				posMap = parseInt(tmp[0]);
				eqtlMap = parseInt(tmp[1]);
				ciMap = parseInt(tmp[2])
				orcol = tmp[3];
				becol = tmp[4];
				secol = tmp[5];
				magma = tmp[6];

				fetchData();
			}
		});

		function fetchData() {
			$.ajax({
				url: '/browse' + '/getFilesContents',
				type: 'POST',
				data: {
					jobID: id,
					fileNames: ['manhattan.txt', 'magma.genes.out', 'QQSNPs.txt']
				},
				error: function () {
					alert("JobQuery get file contents error");
					return;
				},
				success: function (data) {
					let selectedData = {
						"manhattan.txt": data['manhattan.txt'],
						"magma.genes.out": data['magma.genes.out'],
					};
					GWplot(selectedData);
					$('#GWplotSide').show();


					selectedData = {
						"QQSNPs.txt": data['QQSNPs.txt'],
						"magma.genes.out": data['magma.genes.out'],
					};
					QQplot(selectedData);
				}
			});

			if (magma == 1) {
				$.ajax({
					url: '/browse' + '/getFilesContents',
					type: 'POST',
					data: {
						jobID: id,
						fileNames: ['magma.sets.top']
					},
					error: function () {
						alert("JobQuery get magma file contents error");
					},
					success: function (data) {
						selectedData = {
							"magma.sets.top": data['magma.sets.top'],
						};
						MAGMA_GStable(selectedData);

					}
				});

				$.ajax({
					url: '/browse' + '/MAGMA_expPlot',
					type: 'POST',
					data: {
						jobID: id,
					},
					error: function () {
						alert("JobQuery MAGMA_expPlot error");
					},
					success: function (data) {
						MAGMA_expPlot(data);
					}
				});
			} else {
				$('#magmaPlot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
					+ ' MAGMA was not perform.</span><br/></div>');
			}
			if (ciMap == 1) {
				$.ajax({
					url: '/browse' + '/circos_chr',
					type: 'POST',
					data: {
						id: id
					},
					success: function (data) {
						ciMapCircosPlot(data);
					}
				});
			}

			paramTable(subdir, 'browse', 'jobs', id);
			sumTable(subdir, 'browse', 'jobs', id);

			$.ajax({
                url: subdir + '/' + page + '/getFilesContents',
                type: 'POST',
                data: {
                    jobID: id,
                    fileNames: ['annov.stats.txt', 'interval_sum.txt']
                },
                error: function () {
                    alert("JobQuery get file contents error");
                    return;
                },
                success: function (data) {
                    PlotSNPAnnot(data['annov.stats.txt']);
                    PlotLocuSum(data['interval_sum.txt']);
                }
            });

			showResultTables('jobs', id, posMap, eqtlMap, ciMap, orcol, becol, secol);
			$('#GWplotSide').show();
			$('#resultsSide').show();
		}
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

	$.getJSON(subdir + "/browse/getGwasList", function( data ) {
		var items = '<tr><td colspan="6" style="text-align: center;">No Available GWAS Found</td></tr>';
		if(data.length){
			items = '';
			$.each( data, function( key, val ) {
				if (val.old_id === "") {
					var id = val.jobID
				}else{
					var id = val.old_id
				}
				val.title = '<a href="'+subdir+'/browse/'+id+'">'+val.title+'</a>';
				// if(val.sumstats_link != "NA"){
				if(val.sumstats_link.startsWith("http") | val.sumstats_link.startsWith("ftp")){
					val.sumstats_link = '<a href="'+val.sumstats_link+'" target="_blank">'+val.sumstats_link+'</a>'
				}
				items = items + "<tr><td>"+id+"</td><td>"+val.title+"</td><td>"+val.author+"</td><td>"
					+val.publication_email+"</td><td>"+val.phenotype+"</td><td>"+val.publication+"</td>"
					+'<td style="word-wrap:break-word;word-break:break-all;">'
					+val.sumstats_link+"</td><td>"+val.sumstats_ref+"</td><td>"+val.notes+"</td><td>"
					+val.published_at+"</td></tr>";
			});
		}

		// Put list in table
		$('#GwasList table tbody')
			.empty()
			.append(items);
		$('#GwasList table').DataTable({"stripeClasses": [], select: false, order: [[0, 'desc']],});
	});
}
