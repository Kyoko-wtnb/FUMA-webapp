function summaryTable(id){
	$.ajax({
		url: subdir+'/'+page+'/g2f_sumTable',
		type: "POST",
		data: {
			id: id,
			prefix: prefix
		},
		error: function(){
			alert("summary table error");
		},
		success: function(data){
			data = JSON.parse(data);
			var table = '<table class="table table-condensed table-bordered" style="width:auto;text-align:right;"><tbody>'
			data.forEach(function(d){
				if(d[0]!="created_at"){d[1] = d[1].replace(/:/g, ', ');}
				table += '<tr><td>'+d[0]+'</td><td>'+d[1]+'</td></tr>'
			})
			table += '</tbody></table>'
			$('#g2f_summaryTable').html(table);
		}
	});
}

function paramTable(id){
	$.ajax({
		url: subdir+"/"+page+"/g2f_paramTable",
		type: "POST",
		data: {
			id: id,
			prefix: prefix
		},
		error: function(){
			alert("param table error");
		},
		success: function(data){
			data = JSON.parse(data);
			var table = '<table class="table table-condensed table-bordered" style="width: 90%; text-align: right;"><tbody>'
			data.forEach(function(d){
				if(d[0]!="created_at"){d[1] = d[1].replace(/:/g, ', ');}
				table += '<tr><td>'+d[0]+'</td><td>'+d[1]+'</td></tr>'
			})
			table += '</tbody></table>'
			$('#g2f_paramTable').html(table);
		}
	});
}

function expHeatMap(id){
	$.ajax({
		url: subdir+'/'+page+'/expDataOption',
		type: "POST",
		data: {
			id: id,
			prefix: prefix
		},
		error: function(){
			alert("expdata error");
		},
		success: function(data){
			data = data.split(":");
			console.log("HeatMap data: " + data);
			data.forEach(function(d){
				var tmp = d.split("/");
				tmp = tmp[tmp.length-1];
				$('#gene_exp_data').append('<option value="'+tmp+'">'+exp_data_title[tmp]+'</option>');
			})
		},
		complete: function(){
			expHeatPlot(id, $("#gene_exp_data").val());
		}
	})
}

function expHeatPlot(id, dataset){
	d3.select('#expHeat').select("svg").remove();
	var itemSizeRow = 15, cellSize=itemSizeRow-1, itemSizeCol=10;
	var val = $('#expval').val("log2");
	var tssort = $('#tsSort').val("alph");
	var gsort = $('#geneSort').val("alph");

	d3.json(subdir+'/'+page+'/expPlot/'+prefix+'/'+id+'/'+dataset, function(data){
		if(data==null || data==undefined || data.length==0){
			$('#expHeat').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> '
				+'None of your input genes exists in the selected expression data.</span></br>'
				+'This might also be because of the mismatch of input gene ID or symbol.<br/></div>');
			$('#expHeat').parent().children('.ImgDown').each(function(){$(this).prop("disabled", true)});
		}else{
			// data = JSON.parse(data);

			data.data.forEach(function(d){
				d[2] = +d[2];
				d[3] = +d[3];
			})

			var margin = {top: 10, right: 60, bottom: 220, left: 100},
				width = itemSizeRow*data.label.length,
				height = itemSizeCol*data.gene.length;

			var svg = d3.select('#expHeat').append('svg')
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g").attr("transform", "translate("+margin.left+","+margin.top+")");

			var expMax = d3.max(data.data,function(d){return d[2]});
			var expMin = d3.min(data.data, function(d){return d[2];});
			var colorScale = d3.scale.linear().domain([0, expMax/2, expMax]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);

			// legened
			var t = [];
			for(var i =0; i<23; i++){t.push(i);}
			var legendRect = svg.selectAll(".legend").data(t).enter().append("g")
				.append("rect")
				.attr("class", 'legendRect')
				.attr("x", width+10)
				.attr("y", function(d){return (t.length-1-d)*5+5})
				.attr("width", 20)
				.attr("height", 5)
				.attr("fill", function(d){return colorScale(d*expMax/(t.length-1))});
			var legendText = svg.selectAll("text.legend").data([0,11,22]).enter().append("g")
				.append("text")
				.attr("text-anchor", "start")
				.attr("class", "legenedText")
				.attr("x", width+32)
				.attr("y", function(d){return (t.length-1-d)*5+11+5})
				.text(function(d){return Math.round(100*d*expMax/(t.length-1))/100})
				.style("font-size", "12px");

			// y axis label
			var rowLabels = svg.append("g").selectAll(".rowLabel")
				.data(data.order_gene).enter().append("text")
				.text(function(d){return data.gene[d[0]];})
				.attr("x", -3)
				.attr("y", function(d){return (d[0]+1)*itemSizeCol;})
				.style("font-size", "10px")
				.style("text-anchor", "end");
			// x axis label
			var colLabels = svg.append("g").selectAll(".colLabel")
				.data(data.order_label).enter().append("text")
				.text(function(d){return data.label[d[0]];})
				.style("text-anchor", "end")
				.style("font-size", "10px")
				.attr("transform", function(d){
					return "translate("+((d[0]+1)*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
				});

			var heatMap = svg.append("g").attr("class", "cell heatmapcell")
				.selectAll("rect.cell").data(data.data).enter()
				.append("rect")
				.attr("width", cellSize).attr("height", itemSizeCol-0.5)
				.attr('y', function(d){return data.order_gene[data.gene.indexOf(d[0])][0]*itemSizeCol})
				.attr('x', function(d){return data.order_label[data.label.indexOf(d[1])][0]*itemSizeRow})
				.attr('fill', function(d){return colorScale(d[2])});

			svg.selectAll('text').style('font-family', 'sans-serif');
			// Change ordeing of cells
			function sortOptions(val, gsort, tssort){
				var expMax;
				var expMin;
				var col;
				var gi = 0;
				var gcol = 2;
				var li = 0;
				if(val=="log2"){
					expMax = d3.max(data.data,function(d){return d[2]});
					expMin = d3.min(data.data, function(d){return d[2]});
					col = d3.scale.linear().domain([0, (expMax+expMin)/2, expMax]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
					legendRect.attr("fill", function(d){return col(d*expMax/(t.length-1))});
					legendText.text(function(d){return Math.round(100*d*expMax/(t.length-1))/100})
					if(gsort=="clst"){gi = 1;}
					if(tssort=="clst"){li = 1;}
				}else{
					expMax = d3.max(data.data,function(d){return d[3]});
					expMin = d3.min(data.data, function(d){return d[3];});
					var m = Math.max(expMax, Math.abs(expMin));
					col = d3.scale.linear().domain([-m, 0, m]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
					legendRect.attr("fill", function(d){return col(d*2*m/(t.length-1)-m)});
					legendText.text(function(d){return Math.round(d*2*m/(t.length-1)-m)});
					gcol = 3;
					if(gsort=="clst"){gi = 2;}
					if(tssort=="clst"){li = 2;}
				}

				heatMap.transition().duration(1000)
					.attr("fill", function(d){return col(d[gcol])})
					.attr("y", function(d){return data.order_gene[data.gene.indexOf(d[0])][gi]*itemSizeCol})
					.attr("x", function(d){return data.order_label[data.label.indexOf(d[1])][li]*itemSizeRow});
				rowLabels.transition().duration(1000)
					.attr("y", function(d){return (d[gi]+1)*itemSizeCol;});
				colLabels.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+((d[li]+1)*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
					});
			};

			d3.select('#expval').on("change", function(){
				var val = this.value;
				var gsort = $('#geneSort').val();
				var tssort = $('#tsSort').val();
				sortOptions(val, gsort, tssort);
			});

			d3.select('#geneSort').on("change", function(){
				var val = $('#expval').val();
				var gsort = this.value;
				var tssort = $('#tsSort').val();
				sortOptions(val, gsort, tssort);
			});

			d3.select('#tsSort').on("change", function(){
				var val = $('#expval').val();
				var gsort = $('#geneSort').val();
				var tssort = this.value;
				sortOptions(val, gsort, tssort);
			});
		}
	})
}

function tsEnrich(id){
	var data_title = {
		'gtex_v8_ts': 'GTEx v8 54 tissue types',
		'gtex_v8_ts_general': 'GTEx v8 30 general tissue types',
		'gtex_v7_ts': 'GTEx v7 53 tissue types',
		'gtex_v7_ts_general': 'GTEx v7 30 general tissue types',
		'gtex_v6_ts': 'GTEx v6 53 tissue types',
		'gtex_v6_ts_general': 'GTEx v6 30 general tissue types',
		'bs_age': "BrainSpan 29 different ages of brain samples",
		"bs_dev": "BrainSpan 11 general developmental stages of brain samples"
	}
	d3.json(subdir+'/'+page+'/DEGPlot/'+prefix+"/"+id, function(data){
		if(data==null || data==undefined || data.lenght==0){
			$('#magmaPlot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
			+' MAGMA was not able to perform.</span><br/></div>');
		}else{
			data.forEach(function(d){
				d[3] = +d[3]; //P-value
				d[4] = +d[4]; //alph order
				d[5] = +d[5]; //P up order
				d[6] = +d[6]; //P down order
				d[7] = +d[7]; //P twoside order
			})
			var bars = [];
			var xLabels = [];
			var dataset = d3.set(data.map(function(d){return d[0]})).values();
			var cellsize = 15;
			var margin = {top:30, right: 30, bottom:100, left:80},
				height = 450+20;
			var span = 150;
			dataset.forEach(function(ds){
				$('#DEGPlot').append('<div id="'+ds+'Panel"><h4>'+data_title[ds]+'</h4></div>')

				// img download buttons
				$('#'+ds+'Panel').append('<div id="'+ds+'Plot">Download the plot as '
					+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'DEGImgDown("'+ds+'","png");'+"'"+'>PNG</button> '
					+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'DEGImgDown("'+ds+'","jpeg");'+"'"+'>JPG</button> '
					+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'DEGImgDown("'+ds+'","svg");'+"'"+'>SVG</button> '
					+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'DEGImgDown("'+ds+'","pdf");'+"'"+'>PDF</button></div>'
				);

				// plot
				$('#'+ds+'Panel').append('<div id="'+ds+'"></div>')
				var tdata = [];
				var maxLabel = 100;
				data.forEach(function(d){
					if(d[0]==ds){
						tdata.push(d)
						if(d[2].length*6.5>maxLabel){maxLabel=d[2].length*6.5}
					}
				});
				margin.bottom = maxLabel;
				var width = cellsize*tdata.length/3;
				var svg = d3.select("#"+ds).append("svg")
						.attr("width", width+margin.left+margin.right)
						.attr("height", height+margin.top+margin.bottom)
						.append("g")
						.attr("transform", "translate("+margin.left+","+margin.top+")");
				var x = d3.scale.ordinal().rangeBands([0,width]);
				var xAxis = d3.svg.axis().scale(x).orient("bottom");
				var label = d3.set(tdata.map(function(d){return d[2]})).values();
				x.domain(label);
				var currentHeight = 0;

				//up-regulated
				var yup = d3.scale.linear().range([currentHeight+span, currentHeight]);
				var yAxisup = d3.svg.axis().scale(yup).orient("left").ticks(4);
				yup.domain([0, d3.max(tdata, function(d){return -Math.log10(d[3])})]);
				var xLabel = svg.append("g").selectAll(".xLabel")
					.data(tdata.filter(function(d){if(d[1]=="DEG.up"){return d;}})).enter().append("text")
					.text(function(d){return d[2];})
					.style("text-anchor", "end")
					.style("font-size", "11px")
					.style("font-family", "sans-serif")
					.attr("transform", function(d){
						return "translate("+((d[7]+1)*cellsize)+","+(height+10)+")rotate(-70)";
					});
				xLabels.push(xLabel);

				var barup = svg.selectAll('rect.up')
					.data(tdata.filter(function(d){if(d[1]=="DEG.up"){return d;}})).enter()
					.append("rect").attr("class", "bar")
					.attr("x", function(d){return d[7]*cellsize;})
					.attr("width", cellsize-1)
					.attr("y", function(d){return yup(-Math.log10(d[3]))})
					.attr("height", function(d){return yup(0)-yup(-Math.log10(d[3]));})
					.style("fill", function(d){
						if(d[3]>0.05/label.length){return "#5668f4";}
						else{return "#c00";}
					})
					.style("stroke", "grey")
					.style("stroke-width", 0.3);
				bars.push(barup);
				svg.append('g').attr("class", "y axis")
					.call(yAxisup)
					.selectAll('text').style('font-size', '11px');
				svg.append('g').attr("class", "x axis")
					.attr("transform", "translate(0,"+(currentHeight+span)+")")
					.call(xAxis).selectAll('text').remove();
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
					.text("Up-regulated DEG");
				currentHeight += span+10;

				//down regulated
				var ydown = d3.scale.linear().range([currentHeight+span, currentHeight]);
				var yAxisdown = d3.svg.axis().scale(ydown).orient("left").ticks(4);
				ydown.domain([0, d3.max(tdata, function(d){return -Math.log10(d[3])})]);

				var bardown = svg.selectAll('rect.down')
					.data(tdata.filter(function(d){if(d[1]=="DEG.down"){return d;}})).enter()
					.append("rect").attr("class", "bar")
					.attr("x", function(d){return d[7]*cellsize;})
					.attr("width", cellsize-1)
					.attr("y", function(d){return ydown(-Math.log10(d[3]))})
					.attr("height", function(d){return ydown(0)-ydown(-Math.log10(d[3]));})
					.style("fill", function(d){
						if(d[3]>0.05/label.length){return "#5668f4";}
						else{return "#c00";}
					})
					.style("stroke", "grey")
					.style("stroke-width", 0.3);
				bars.push(bardown);
				svg.append('g').attr("class", "y axis")
					.call(yAxisdown)
					.selectAll('test').style('font-size', '11px');
				svg.append('g').attr("class", "x axis")
					.attr("transform", "translate(0, "+(currentHeight+span)+")")
					.call(xAxis).selectAll('text').remove();
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
					.text("Down-regulated DEG");
				currentHeight += span+10;

				//twoside
				var y = d3.scale.linear().range([currentHeight+span, currentHeight]);
				var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
				y.domain([0, d3.max(tdata, function(d){return -Math.log10(d[3])})]);

				var bartwo = svg.selectAll('rect.two')
					.data(tdata.filter(function(d){if(d[1]=="DEG.twoside"){return d;}})).enter()
					.append("rect").attr("class", "bar")
					.attr("x", function(d){return d[7]*cellsize;})
					.attr("width", cellsize-1)
					.attr("y", function(d){return y(-Math.log10(d[3]))})
					.attr("height", function(d){return y(0)-y(-Math.log10(d[3]));})
					.style("fill", function(d){
						if(d[3]>0.05/label.length){return "#5668f4";}
						else{return "#c00";}
					})
					.style("stroke", "grey")
					.style("stroke-width", 0.3);
				bars.push(bartwo);
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
					.text("DEG (both side)");

				svg.append('g').attr("class", "x axis")
					.attr("transform", "translate(0,"+height+")")
					.call(xAxis).selectAll('text').remove();

				svg.append('g').attr("class", "y axis")
					.call(yAxis)
					.selectAll('test').style('font-size', '11px');
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate("+(-margin.left/2+5)+","+height/2+")rotate(-90)")
					.text("-log 10 P-value");
				svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
				svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
				svg.selectAll('text').style('font-family', 'sans-serif');
			});

			function sortOptions(type){
				var idx;
				if(type=="alph"){
					idx = 4;
				}else if(type=="up"){
					idx = 5;
				}else if(type=="down"){
					idx = 6;
				}else if(type=="two"){
					idx = 7;
				}
				for(var i=0; i<bars.length; i++){
					bars[i].transition().duration(1000)
						.attr("x", function(d){return d[idx]*cellsize;})
				}
				for(var i=0; i<xLabels.length; i++){
					xLabels[i].transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+((d[idx]+1)*cellsize)+","+(height+10)+")rotate(-70)";
						});
				}
			}
			d3.select('#DEGorder').on("change", function(){
				sortOptions($('#DEGorder').val());
			});
		}
	})
}

function GeneSetPlot(category){
	$('#'+category+'Plot').show();
	$('#'+category+'Table').hide();
}

function GeneSetTable(category){
	$('#'+category+'Plot').hide();
	$('#'+category+'Table').show();
}

function GeneSet(id){
	$('#GeneSet').html("");
	var category = ['Hallmark_gene_sets', 'Positional_gene_sets', 'Curated_gene_sets',
			'Chemical_and_Genetic_pertubation', 'Canonical_Pathways', 'BioCarta', 'KEGG', 'Reactome',
			'microRNA_targets', 'TF_targets', 'Computational_gene_sets',
			'Cancer_gene_neighborhoods', 'Cancer_modules', 'GO_bp', 'GO_cc', 'GO_mf',
			'Oncogenic_signatures', 'Immunologic_signatures', 'Wikipathways',
			'GWAScatalog'
		];
	var category_title = {'Hallmark_gene_sets' : 'Hallmark gene sets (MsigDB h)',
			'Positional_gene_sets' : 'Positional gene sets (MsigDB c1)',
			'Curetaed_gene_sets' : 'All curated gene sets (MsigDB c2)',
			'Chemical_and_Genetic_pertubation' : 'Chemical and Genetic pertubation gene sets (MsigDB c2)',
			'Canonical_Pathways' : 'All Canonical Pathways (MsigDB c2)',
			'BioCarta' : 'BioCarta (MsigDB c2)',
			'KEGG' : 'KEGG (MsigDB c2)',
			'Reactome' : 'Reactome (MsigDB c2)',
			'microRNA_targets' : 'microRNA targets (MsigDB c3)',
			'TF_targets' : 'TF targets (MsigDB c3)',
			'Computational_gene_sets' : 'All computational gene sets (MsigDB c4)',
			'Cancer_gene_neighborhoods' : 'Cancer gene neighborhoods (MsigDB c4)',
			'Cancer_modules' : 'Cancer gene modules (MsigDB c4)',
			'GO_bp' : 'GO biological processes (MsigDB c5)',
			'GO_cc' : 'GO cellular components (MsigDB c5)',
			'GO_mf' : 'GO molecular functions (MsigDB c5)',
			'Oncogenic_signatures' : 'Oncogenic signatures (MsigDB c6)',
			'Immunologic_signatures' : 'Immunologic signatures (MsigDB c7)',
			'Wikipathways' : 'WikiPathways',
			'GWAScatalog' : 'GWAS catalog reported genes'
		};
	d3.json(subdir+'/'+page+'/g2f_d3text/'+prefix+'/'+id+'/GS.txt', function(data){
		if(data == undefined || data == null){
			$('#GeneSet').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> The number of input genes exist in selected background genes was 0 or 1.</span></br>'
			+'The hypergeometric test is only performed if more than 2 genes are available.</div>');
			$('#GSdown').attr("disabled", true);
		}else{
			data.forEach(function(d){
				d.adjP = +d.adjP;
			});
			tmp_category = d3.set(data.map(function(d){return d.Category})).values();
			tmp_category.forEach(function(d){
				if(category.indexOf(d)<0){category.push(d)}
			})
			data = data.filter(function(d){if(d.adjP<0.05){return d}})
			for(var i=0; i<category.length; i++){
				// title
				var title = category[i];
				if(category_title[category[i]]!=undefined){
					title = category_title[category[i]];
				}
				// extract data
				var tdata=[];
				data.forEach(function(d){
					if(d.Category==category[i]){
						tdata.push(d);
					}
				});
				var genesplot = [];
				var gs_max = 0;
				tdata.forEach(function(d){
					// d.P = +d.P;
					d.adjP = +d.adjP;
					d.N_overlap = +d.N_overlap;
					d.N_genes = +d.N_genes;
					var g = d.genes.split(":");
					for(var j=0; j<g.length; j++){
						genesplot.push({"GeneSet":d.GeneSet, "gene":g[j]})
					}
					if(d.GeneSet.length>gs_max){gs_max = d.GeneSet.length;}
				});
				genes = d3.set(genesplot.map(function(d){return d.gene;})).values();

				if(tdata.length==0){
					var panel = $('<div class="panel panel-default" style="padding-top:0;"><div class="panel-heading" style="height: 35px;"><a href="#'
						+category[i]+'Panel" data-toggle="collapse" style="color: black;">'
						+title+'<tab>(0)</div><div class="panel-body collapse" id="'
						+category[i]+'Panel"><div id="'+category[i]+'" style="text-align: center;">No significant results</div><div id="'
						+category[i]+'Table"></div></div></div>');
					$('#GeneSet').append(panel);
				}else{
					// $('#test').append("<p>"+category[i]+"<br/>gs_max: "+gs_max+'<br/>genes: '+genes.length+'</p>');
					// add div
					var panel = '<div class="panel panel-default" style="padding-top:0;"><div class="panel-heading" style="height: 35px;"><a href="#'
						+category[i]+'Panel" data-toggle="collapse" style="color: black;">'
						+title+'<tab>('+tdata.length+')</div><div class="panel-body collapse" id="'
						+category[i]+'Panel"><p><a onclick="GeneSetPlot('+"'"+category[i]+"'"+');">Plot</a> / <a onclick="GeneSetTable('+
						"'"+category[i]+"'"+');">Table</a></p></div></div>';
					$('#GeneSet').append(panel);
					// $('#'+category[i]+"Panel").append('<button class="btn btn-default btn-xs ImgDown" id="'+category[i]+'Img" style="float:right; margin-right:100px;">Download PNG</button>');
					$('#'+category[i]+"Panel").append('<div id="'+category[i]+'Plot">Download the plot as '
						+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","png");'+"'"+'>PNG</button> '
						+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","jpeg");'+"'"+'>JPG</button> '
						+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","svg");'+"'"+'>SVG</button> '
						+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","pdf");'+"'"+'>PDF</button> '
						+'<div id="'+category[i]+'" style="overflow: auto; width: 100%;"></div></div>'
						+'<div id="'+category[i]+'Table"></div>');

					$('#'+category[i]+'Table').hide();

					// Plots
					var gs = d3.set(tdata.map(function(d){return d.GeneSet})).values();
					var ngs = gs.length;
					var barplotwidth = 150;

					var margin = {top: 40, right: 10, bottom: 80, left: Math.max(gs_max*7, 80)},
						width = barplotwidth*2+10+(Math.max(genes.length,6)*15),
						height = 15*ngs;
					var svg = d3.select('#'+category[i]).append('svg')
						.attr("width", width+margin.left+margin.right)
						.attr("height", height+margin.top+margin.bottom)
						.append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

					// bar plot (overlap proportion)
					var xprop = d3.scale.linear().range([0, barplotwidth]);
					var xpropAxis = d3.svg.axis().scale(xprop).orient("bottom");
					xprop.domain([d3.max(tdata,function(d){return d.N_overlap/d.N_genes})+0.1,0]);
					var y = d3.scale.ordinal().rangeBands([0,height]);
					var yAxis = d3.svg.axis().scale(y).orient("left");
					y.domain(tdata.map(function(d){return d.GeneSet;}));
					svg.selectAll('rect.prop').data(tdata).enter()
						.append("rect").attr("class", "bar")
						.attr("x", function(d){return xprop(d.N_overlap/d.N_genes)})
						.attr("width", function(d){return barplotwidth-xprop(d.N_overlap/d.N_genes)})
						.attr("y", function(d){return y(d.GeneSet)})
						.attr("height", 15)
						.style("fill", "#ff6666")
						.style("stroke", "grey")
						.style("stroke-width", 0.3);
					svg.append('g').attr("class", "x axis")
						.attr("transform", "translate(0,"+height+")")
						.call(xpropAxis)
						.selectAll(".tick")
						.each(function (d){
							if ( d == 0 ) {
								this.remove();
							}
						})
						.selectAll('text').attr('font-weight', 'normal')
						.style("text-anchor", "end")
						.attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
						.style('font-size', '11px');

					// bar plot (enrichment P-value)
					var xbar = d3.scale.linear().range([barplotwidth, barplotwidth*2]);
					var xbarAxis = d3.svg.axis().scale(xbar).orient("bottom");
					if(d3.min(tdata, function(d){return d.adjP})==0){
						if(tdata.length==1){
							xbar.domain([0, 1]);
							svg.selectAll('rect.p').data(tdata).enter()
								.append("rect").attr("class", "bar")
								.attr("x", xbar(0))
								.attr("width", function(d){return xbar(1)-barplotwidth})
								.attr("y", function(d){return y(d.GeneSet)})
								.attr("height", 15)
								.style("fill", "#4d4dff")
								.style("stroke", "grey")
								.style("stroke-width", 0.3);
							svg.append('g').attr("class", "x axis")
								.attr("transform", "translate(0,"+height+")")
								.call(xbarAxis).selectAll('text').remove();
							svg.append('text').attr('font-weight', 'normal')
								.style("text-anchor", "end")
								.attr("transform", "translate("+(xbar(1)+3)+","+(height+12)+")rotate(-65)")
								.text("Inf")
								.style('font-size', '11px');
							svg.append('text').attr('font-weight', 'normal')
								.style("text-anchor", "end")
								.attr("transform", "translate("+(xbar(0)+3)+","+(height+12)+")rotate(-65)")
								.text("0.0")
								.style('font-size', '11px');
						}else{
							var tmp_max = d3.max(tdata, function(d){if(d.adjP!=0){return -Math.log10(d.adjP)}})
							xbar.domain([0, tmp_max*1.5]);
							svg.selectAll('rect.p').data(tdata).enter()
								.append("rect").attr("class", "bar")
								.attr("x", xbar(0))
								.attr("width", function(d){
									if(d.adjP==0){
										return xbar(tmp_max*1.5)-barplotwidth
									}else{
										return xbar(-Math.log10(d.adjP))-barplotwidth
									}
								})
								.attr("y", function(d){return y(d.GeneSet)})
								.attr("height", 15)
								.style("fill", "#4d4dff")
								.style("stroke", "grey")
								.style("stroke-width", 0.3);
							svg.append('g').attr("class", "x axis")
								.attr("transform", "translate(0,"+height+")")
								.call(xbarAxis)
								.selectAll(".tick")
								.each(function (d){
									if ( d >= tmp_max ) {
										this.remove();
									}
								})
								.selectAll('text').attr('font-weight', 'normal')
								.style("text-anchor", "end").attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
								.style('font-size', '11px');
							svg.append('text').attr('font-weight', 'normal')
								.style("text-anchor", "end")
								.attr("transform", "translate("+(xbar(tmp_max*1.5)+3)+","+(height+12)+")rotate(-65)")
								.text("Inf")
								.style('font-size', '11px');
						}
					}else{
						xbar.domain([0, d3.max(tdata, function(d){return -Math.log10(d.adjP)})]);

						svg.selectAll('rect.p').data(tdata).enter()
							.append("rect").attr("class", "bar")
							.attr("x", xbar(0))
							.attr("width", function(d){return xbar(-Math.log10(d.adjP))-barplotwidth})
							.attr("y", function(d){return y(d.GeneSet)})
							.attr("height", 15)
							.style("fill", "#4d4dff")
							.style("stroke", "grey")
							.style("stroke-width", 0.3);

						svg.append('g').attr("class", "x axis")
							.attr("transform", "translate(0,"+height+")")
							.call(xbarAxis).selectAll('text').attr('font-weight', 'normal')
							.style("text-anchor", "end").attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
							.style('font-size', '11px');
					}
					svg.append('g').attr("class", "y axis")
						.call(yAxis).selectAll('text').style('font-size', '11px');

					// gene plot
					var xgenes = d3.scale.ordinal().rangeBands([barplotwidth*2+10,barplotwidth*2+10+15*genes.length]);
					xgenes.domain(genesplot.map(function(d){return d.gene}));
					var xgenesAxis = d3.svg.axis().scale(xgenes).orient("bottom");
					svg.selectAll('rect.genes').data(genesplot).enter()
						.append("rect")
						.attr("x", function(d){return xgenes(d.gene)})
						.attr("y", function(d){return y(d.GeneSet)})
						.attr("width", 15)
						.attr("height", 15)
						.style("fill", "#ffa64a")
						.style("stroke", "grey")
						.style("stroke-width", 0.3);
					svg.append('g').attr("class", "y axis")
						.attr("transform", "translate("+(barplotwidth*2+10)+",0)")
						.call(yAxis).selectAll("text").remove();
					svg.append('g').attr("class", "x axis")
						.attr("transform", "translate(0,"+height+")")
						.call(xgenesAxis).selectAll('text').attr('font-weight', 'normal')
						.style("text-anchor", "end").attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
						.style('font-size', '11px');

					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth/2)+","+(height+45)+")")
						.text("Proportion").attr("font-size", "12px");
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth/2)+","+(-margin.top/2-6)+")")
						.text("Proportion of overlapping").attr("font-size", "12px");
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth/2)+","+(-margin.top/2+6)+")")
						.text("genes in gene sets").attr("font-size", "12px");

					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth*1.5)+","+(height+45)+")")
						.text("-log10 adjusted P-value").attr("font-size", "12px");
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth*1.5)+","+(-margin.top/2)+")")
						.text("Enrichment P-value").attr("font-size", "12px");
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(barplotwidth*2+10+width)/2+","+(-margin.top/2)+")")
						.text("overlapping genes").attr("font-size", "12px");

					svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
					svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
					svg.selectAll('text').style('font-family', 'sans-serif');

					// Table
					var table = '<table class="table table-bordered"><thead><td>GeneSet</td><td>N</td><td>n</td><td>P-value</td><td>adjusted P</td><td>genes</td></thead>';
					tdata.forEach(function(d){
						if(d.link.length>0 & d.link.startsWith("http")){
							table += '<tr><td><a href="'+d.link+'" target="_blank">'+d.GeneSet+'</a></td><td>'+d.N_genes+'</td><td>'+d.N_overlap
								+'</td><td>'+Number(Number(d.p).toPrecision(3)).toExponential(2)+'</td><td>'+Number(Number(d.adjP).toPrecision(3)).toExponential(2)+'</td><td>'+d.genes.split(":").join(", ")+'</td></tr>';
						}else{
							table += '<tr><td>'+d.GeneSet+'</td><td>'+d.N_genes+'</td><td>'+d.N_overlap
								+'</td><td>'+Number(Number(d.p).toPrecision(3)).toExponential(2)+'</td><td>'+Number(Number(d.adjP).toPrecision(3)).toExponential(2)+'</td><td>'+d.genes.split(":").join(", ")+'</td></tr>';

						}
					});
					table += '</table>'
					$('#'+category[i]+"Table").html(table);
				}
			}
		}
	});
}

function GeneTable(id){
	geneTable = $('#GeneTable').DataTable({
		"processing": true,
		serverSide: false,
		select: false,
		"ajax" : {
			url: "geneTable",
			type: "POST",
			data: {
				id: id,
				prefix: prefix
			}
		},
		error: function(){
			alert("geneTable error");
		},
		"columns":[
			{"data": "ensg", name: "ENSG"},
			{"data": "entrezID", name: "entrezID"},
			{"data": "symbol", name: "symbol"},
			{"data": "OMIM", name: "OMIM"},
			{"data": "uniprotID", name: "UniProtID"},
			{"data": "DrugBank", name: "DrugBank"},
			{"data": "GeneCard", name: "GeneCard"}
		],
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
}

function DEGImgDown(name, type){
	$('#DEGData').val($('#'+name).html());
	$('#DEGType').val(type);
	$('#DEGJobID').val(id);
	$('#DEGFileName').val(name);
	$('#DEGDir').val(prefix);
	$('#DEGSubmit').trigger('click');
}

function GSImgDown(name, type){
	$('#GSData').val($('#'+name).html());
	$('#GSType').val(type);
	$('#GSJobID').val(id);
	$('#GSFileName').val(name);
	$('#GSDir').val(prefix);
	$('#GSSubmit').trigger('click');
}
