var sigSNPtable_selected=null;
var leadSNPtable_selected=null;
var lociTable_selected=null;
var annotPlotSelected;
var prefix = "gwas";
$(document).ready(function(){
	// side bar and hash id
	var hashid = window.location.hash;
	if(hashid=="" && gwasID.length==0){
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
	if(gwasID.length>0){
		loadResults();
	}

	function loadResults(){
		var posMap;
		var eqtlMap;
		var ciMap;
		var orcol;
		var becol;
		var secol;
		$.ajax({
			url: subdir+'/browse/getParams',
			type: 'POST',
			data:{
				gwasID: gwasID
			},
			error: function(){
				alert("JobQuery getParams error");
			},
			success: function(data){
				// $('#test').html(data)
				var tmp = data.split(":");
				filedir = tmp[0];
				posMap = parseInt(tmp[1]);
				eqtlMap = parseInt(tmp[2]);
				ciMap = parseInt(tmp[3]);
				orcol = tmp[4];
				becol = tmp[5];
				secol = tmp[6];
			},
			complete: function(){
				GWplot(gwasID);
				QQplot(gwasID);
				MAGMAresults(gwasID);
				ciMapCircosPlot(gwasID, ciMap);
				showResultTables(filedir, gwasID, posMap, eqtlMap, ciMap, orcol, becol, secol);
				expHeatMap(gwasID);
				tsEnrich(gwasID);
				tsGeneralEnrich(gwasID);
				GeneSet(gwasID);
				GeneTable(gwasID);
				$('#resultsSide').show();
			}
		});
	}

	// download file selection
	$('#allfiles').on('click', function(){
		$('#paramfile').prop('checked', true);
		$('#leadfile').prop('checked', true);
		$('#locifile').prop('checked', true);
		$('#indSNPfile').prop('checked', true);
		$('#snpsfile').prop('checked', true);
		$('#annovfile').prop('checked', true);
		$('#annotfile').prop('checked', true);
		$('#genefile').prop('checked', true);
		$('#eqtlfile').prop('checked', true);
		$('#cifile').prop('checked', true);
		$('#gwascatfile').prop('checked', true);
		// $('#exacfile').prop('checked', true);
		$('#magmafile').prop('checked', true);
		$('#download').attr('disabled',false);
	});
	$('#clearfiles').on('click', function(){
		$('#paramfile').prop('checked', false);
		$('#leadfile').prop('checked', false);
		$('#locifile').prop('checked', false);
		$('#indSNPfile').prop('checked', false);
		$('#snpsfile').prop('checked', false);
		$('#annovfile').prop('checked', false);
		$('#annotfile').prop('checked', false);
		$('#genefile').prop('checked', false);
		$('#eqtlfile').prop('checked', false);
		$('#cifile').prop('checked', false);
		// $('#exacfile').prop('checked', false);
		$('#gwascatfile').prop('checked', false);
		$('#magmafile').prop('checked', false);
		$('#download').attr('disabled',true);
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

function GWplot(gwasID){
	var chromSize = [249250621, 243199373, 198022430, 191154276, 180915260, 171115067,
		159138663, 146364022, 141213431, 135534747, 135006516, 133851895, 115169878, 107349540,
		102531392, 90354753, 81195210, 78077248, 63025520, 59128983, 48129895, 51304566, 155270560];
	var chromStart = [];
	chromStart.push(0);
	for(var i=1; i<chromSize.length; i++){
		chromStart.push(chromStart[i-1]+chromSize[i-1]);
	}

	var margin = {top:30, right: 30, bottom:50, left:50},
		width = 800,
		height = 300;

	var svg = d3.select("#manhattan").append("svg")
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append("g")
		.attr("transform", "translate("+margin.left+","+margin.top+")");

	var svg2 = d3.select("#geneManhattan").append("svg")
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append("g")
		.attr("transform", "translate("+margin.left+","+margin.top+")");

	d3.json("manhattan/"+prefix+"/"+gwasID+"/manhattan.txt", function(data){
		data.forEach(function(d){
			d[0] = +d[0]; //chr
			d[1] = +d[1]; // bp
			d[2] = +d[2]; // p
		});

		var chr = d3.set(data.map(function(d){return d[0];})).values();

		var max_chr = chr.length;
		var x = d3.scale.linear().range([0, width]);
		x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		var y = d3.scale.linear().range([height, 0]);
		// y.domain([0, d3.max(data, function(d){return -Math.log10(d.p);})+1]);
		y.domain([0, d3.max(data, function(d){return -Math.log10(d[2]);})+1]);

		var yAxis = d3.svg.axis().scale(y).orient("left");

		svg.selectAll("dot.manhattan").data(data).enter()
			.append("circle")
			.attr("r", 2)
			.attr("cx", function(d){return x(d[1]+chromStart[d[0]-1])})
			.attr("cy", function(d){return y(-Math.log10(d[2]))})
			.attr("fill", function(d){if(d[0]%2==0){return "steelblue"}else{return "blue"}});

		svg.append("line")
			.attr("x1", 0).attr("x2", width)
			.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
			.style("stroke", "red")
			.style("stroke-dasharray", ("3,3"));
		svg.append("g").attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
		svg.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');

		//Chr label
		for(var i=0; i<chr.length; i++){
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+x((chromStart[i]*2+chromSize[i])/2)+","+(height+20)+")")
				.text(chr[i])
				.style("font-size", "10px");
		}
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+width/2+","+(height+35)+")")
			.text("Chromosome");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-35)+","+(height/2)+")rotate(-90)")
			.text("-log10 P-value");
		svg.selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style("font-family", "sans-serif");
	});

	d3.json("manhattan/"+prefix+"/"+gwasID+"/magma.genes.out", function(data){
		data.forEach(function(d){
			d[0] = +d[0]; //chr
			d[1] = +d[1]; //start
			d[2] = +d[2]; //stop
			d[3] = +d[3]; //p
		});

		var nSigGenes=0;
		var sortedP = [];
		sortedP.push(0);
		data.forEach(function(d){
			if(d[3]<=0.05/data.length){nSigGenes++;}
			sortedP.push(d[3]);
		});
		$('#topGenes').val(nSigGenes);

		$('#geneManhattanDesc').html("Input SNPs were mapped to "+data.length+" protein coding genes (distance 0). "
			+"Genome wide significance (red dashed line in the plot) was defined at P = 0.05/"+data.length+" = "+(Number((0.05/data.length).toPrecision(3)).toExponential())+".");

		sortedP = sortedP.sort(function(a,b){return a-b;});
		var chr = d3.set(data.map(function(d){return d[0];})).values();
		var max_chr = chr.length;
		var x = d3.scale.linear().range([0, width]);
		x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		var y = d3.scale.linear().range([height, 0]);
		// y.domain([0, d3.max(data, function(d){return -Math.log10(d.P);})+1]);
		y.domain([0, d3.max(data, function(d){return -Math.log10(d[3]);})+1]);
		var yAxis = d3.svg.axis().scale(y).orient("left");

		svg2.selectAll("dot.geneManhattan").data(data).enter()
			.append("circle")
			.attr("r", 2)
			.attr("cx", function(d){return x((d[1]+d[2])/2+chromStart[d[0]-1])})
			.attr("cy", function(d){return y(-Math.log10(d[3]))})
			.attr("fill", function(d){if(d[0]%2==0){return "steelblue"}else{return "blue"}});

		svg2.selectAll('text.gene').data(data.filter(function(d){if(d[3]<=0.05/data.length){return d;}})).enter()
			.append("text")
			.attr("class", "gene")
			.attr("x", function(d){return x((d[1]+d[2])/2+chromStart[d[0]-1])})
			.attr("y", function(d){return y(-Math.log10(d[3]))-2})
			.text(function(d){return d[4]})
			.style("font-size", "10px");

		svg2.append("line")
			.attr("x1", 0).attr("x2", width)
			.attr("y1", y(-Math.log10(0.05/data.length))).attr("y2", y(-Math.log10(0.05/data.length)))
			.style("stroke", "red")
			.style("stroke-dasharray", ("3,3"));
		svg2.append("g").attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
		svg2.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');

		//Chr label
		for(var i=0; i<chr.length; i++){
			svg2.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+x((chromStart[i]*2+chromSize[i])/2)+","+(height+20)+")")
				.text(chr[i])
				.style("font-size", "10px");
		}
		svg2.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+width/2+","+(height+35)+")")
			.text("Chromosome");
		svg2.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-35)+","+(height/2)+")rotate(-90)")
			.text("-log10 P-value");
		svg2.selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg2.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg2.selectAll('text').style("font-family", "sans-serif");

		$('#topGenes').on("input", function(){
			svg2.selectAll(".gene").remove();
			var n = $('#topGenes').val();
			svg2.selectAll('text.gene').data(data.filter(function(d){if(d[3]<=sortedP[n]){return d;}})).enter()
				.append("text")
				.attr("class", "gene")
				.attr("x", function(d){return x((d[1]+d[2])/2+chromStart[d[0]-1])})
				.attr("y", function(d){return y(-Math.log10(d[3]))-2})
				.text(function(d){return d[4]})
				.style("font-size", "10px")
				.style("font-family", "sans-serif");
		})
	});
}

function QQplot(gwasID){
	var margin = {top:30, right: 30, bottom:50, left:50},
		width = 300,
		height = 300;
	// create svg and canvas objects
	var qqSNP = d3.select("#QQplot").append("svg")
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append("g")
		.attr("transform", "translate("+margin.left+","+margin.top+")");

	var qqGene = d3.select("#geneQQplot").append("svg")
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append("g").attr("transform", "translate("+margin.left+","+margin.top+")");

	d3.json('QQplot/'+prefix+'/'+gwasID+'/SNP', function(data){
		data.forEach(function(d){
			d.obs = +d.obs;
			d.exp = +d.exp;
		});

		var x = d3.scale.linear().range([0, width]);
		var y = d3.scale.linear().range([height, 0]);
		var xMax = d3.max(data, function(d){return d.exp;});
		var yMax = d3.max(data, function(d){return d.obs;});
		x.domain([0, (xMax+xMax*0.01)]);
		y.domain([0, (yMax+yMax*0.01)]);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		var xAxis = d3.svg.axis().scale(x).orient("bottom");

		var maxP = Math.min(xMax, yMax);

		qqSNP.selectAll("dot.QQ").data(data).enter()
			.append("circle")
			.attr("r", 2)
			.attr("cx", function(d){return x(d.exp)})
			.attr("cy", function(d){return y(d.obs)})
			.attr("fill", "grey");
		qqSNP.append("g").attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")").call(xAxis)
			.selectAll('text').style('font-size', '11px');
		qqSNP.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');
		qqSNP.append("line")
			.attr("x1", 0).attr("x2", x(maxP))
			.attr("y1", y(0)).attr("y2", y(maxP))
			.style("stroke", "red")
			.style("stroke-dasharray", ("3,3"));
		qqSNP.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
			.text("Observed -log10 P-value");
		qqSNP.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(width/2)+","+(height+35)+")")
			.text("Expected -log10 P-value");
		qqSNP.selectAll('path').style('fill', 'none').style('stroke', 'grey');
		qqSNP.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		qqSNP.selectAll('text').style("font-family", "sans-serif");
	});

	d3.json('QQplot/'+prefix+'/'+gwasID+'/Gene', function(data){
		data.forEach(function(d){
			d.obs = +d.obs;
			d.exp = +d.exp;
			d.n = +d.n;
		});

		var x = d3.scale.linear().range([0, width]);
		var y = d3.scale.linear().range([height, 0]);
		var xMax = d3.max(data, function(d){return d.exp;});
		var yMax = d3.max(data, function(d){return d.obs;});
		x.domain([0, (xMax+xMax*0.01)]);
		y.domain([0, (yMax+yMax*0.01)]);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		var xAxis = d3.svg.axis().scale(x).orient("bottom");

		var maxP = Math.min(xMax, yMax);

		qqGene.selectAll("dot.geneQQ").data(data).enter()
			.append("circle")
			.attr("r", 2)
			.attr("cx", function(d){return x(d.exp)})
			.attr("cy", function(d){return y(d.obs)})
			.attr("fill", "grey");
		qqGene.append("g").attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")").call(xAxis)
			.selectAll('text').style('font-size', '11px');
		qqGene.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');
		qqGene.append("line")
			.attr("x1", 0).attr("x2", x(maxP))
			.attr("y1", y(0)).attr("y2", y(maxP))
			.style("stroke", "red")
			.style("stroke-dasharray", ("3,3"));

		qqGene.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
			.text("Observed -log10 P-value");
		qqGene.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(width/2)+","+(height+35)+")")
			.text("Expected -log10 P-value");
		qqGene.selectAll('path').style('fill', 'none').style('stroke', 'grey');
		qqGene.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		qqGene.selectAll("text").style("font-family", "sans-serif");
	});
}

function MAGMAresults(gwasID){
	var file = "magma.sets.top";
	$('#MAGMAtable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax" : {
			url: subdir+"/browse/DTfile",
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "FULL_NAME:NGENES:BETA:BETA_STD:SE:P:Pbon"
			}
		},
		error: function(){
		alert("leadSNPs table error");
		},
		"order": [[6, 'asc']],
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	d3.json(subdir+'/browse/MAGMAtsplot/general/'+prefix+'/'+gwasID, function(data){
		if(data==null || data==undefined || data.lenght==0){
			$('#magmaPlot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
			+' MAGMA tissue expression analyses is only available for FUMA v1.1.0 or later.</span><br/>'
			+'If your job has been submitted to older version, please contact Kyoko Watanabe (k.watanabe@vu.nl) or resubmit the job to obtain the MAGMA tisue expression results.</div>');
		}else{
			var margin = {top:30, right: 30, bottom:100, left:80},
				width = 600,
				height = 250;
			var svg = d3.select("#magma_exp_general").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");
			data.data.forEach(function(d){
				d[1] = +d[1]; // P-value
			});
			var x = d3.scale.ordinal().rangeBands([0,width]);
			var xAxis = d3.svg.axis().scale(x).orient("bottom");
			x.domain(data.data.map(function(d){return d[0];}));
			var y = d3.scale.linear().range([height, 0]);
			var yAxis = d3.svg.axis().scale(y).orient("left");
			y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[1]);})]);

			var cellsize = width/data.data.length;
			var Pbon = 0.05/data.data.length;

			var bar = svg.selectAll("rect.expgeneral").data(data.data).enter()
				.append("rect")
				.attr("x", function(d){return data.order.p[d[0]]*cellsize;})
				.attr("y", function(d){return y(-Math.log10(d[1]));})
				.attr("width", cellsize-1)
				.attr("height", function(d){return height - y(-Math.log10(d[1]));})
				.style("fill", function(d){
					if(d[1] < Pbon){return "#c00";}
					else{return "#5668f4";}
				})
				.style("stroke", "grey");
			var xLabels = svg.append("g").selectAll(".xLabel")
				.data(data.data).enter().append("text")
				.text(function(d){return d[0];})
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.attr("transform", function(d){
					return "translate("+(data.order.p[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
				});

			svg.append("line")
				.attr("x1", 0).attr("x2", width)
				.attr("y1", y(-Math.log10(Pbon))).attr("y2", y(-Math.log10(Pbon)))
				.style("stroke", "black")
				.style("stroke-dasharray", ("3,3"));

			svg.append('g').attr("class", "y axis")
				.call(yAxis)
				.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
			svg.append('g').attr("class", "x axis")
				.attr("transform", "translate(0,"+(height)+")")
				.call(xAxis).selectAll('text').remove();
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+(-margin.left/2-30)+","+height/2+")rotate(-90)")
				.text("-log 10 P-value");
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style('font-family', 'sans-serif');

			function sortOptions(type){
				if(type=="alph"){
					bar.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[0]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.alph[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
						});
				}else if(type=="p"){
					bar.transition().duration(1000)
						.attr("x", function(d){return data.order.p[d[0]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.p[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
						});
				}
			}
			d3.select('#magmaTsGorder').on("change", function(){
				var type = $('#magmaTsGorder').val();
				sortOptions(type);
			});
		}
	});

	d3.json(subdir+'/browse/MAGMAtsplot/specific/'+prefix+'/'+gwasID, function(data){
		if(data==null || data==undefined || data.lenght==0){
		}else{
			var margin = {top:30, right: 30, bottom:230, left:80},
				width = 800,
				height = 250;
			var svg = d3.select("#magma_exp").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");
			data.data.forEach(function(d){
				d[1] = +d[1]; // P-value
			});
			var x = d3.scale.ordinal().rangeBands([0,width]);
			var xAxis = d3.svg.axis().scale(x).orient("bottom");
			x.domain(data.data.map(function(d){return d[0];}));
			var y = d3.scale.linear().range([height, 0]);
			var yAxis = d3.svg.axis().scale(y).orient("left");
			y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[1]);})]);

			var cellsize = width/data.data.length;
			var Pbon = 0.05/data.data.length;

			var bar = svg.selectAll("rect.expgeneral").data(data.data).enter()
				.append("rect")
				.attr("x", function(d){return data.order.p[d[0]]*cellsize;})
				.attr("y", function(d){return y(-Math.log10(d[1]));})
				.attr("width", cellsize-1)
				.attr("height", function(d){return height - y(-Math.log10(d[1]));})
				.style("fill", function(d){
			if(d[1] < Pbon){return "#c00";}
				else{return "#5668f4";}
			})
				.style("stroke", "grey");
				var xLabels = svg.append("g").selectAll(".xLabel")
				.data(data.data).enter().append("text")
				.text(function(d){return d[0];})
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.attr("transform", function(d){
					return "translate("+(data.order.p[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
				});

			svg.append("line")
				.attr("x1", 0).attr("x2", width)
				.attr("y1", y(-Math.log10(Pbon))).attr("y2", y(-Math.log10(Pbon)))
				.style("stroke", "black")
				.style("stroke-dasharray", ("3,3"));

			svg.append('g').attr("class", "y axis")
				.call(yAxis)
				.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
			svg.append('g').attr("class", "x axis")
				.attr("transform", "translate(0,"+(height)+")")
				.call(xAxis).selectAll('text').remove();
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+(-margin.left/2-30)+","+height/2+")rotate(-90)")
				.text("-log 10 P-value");
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style('font-family', 'sans-serif');

			function sortOptions(type){
				if(type=="alph"){
					bar.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[0]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.alph[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
						});
				}else if(type=="p"){
					bar.transition().duration(1000)
					.attr("x", function(d){return data.order.p[d[0]]*cellsize;});
					xLabels.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(data.order.p[d[0]]*cellsize+((cellsize-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
				}
			}
			d3.select('#magmaTsorder').on("change", function(){
				var type = $('#magmaTsorder').val();
				sortOptions(type);
			});
		}
	});
}

function ciMapCircosPlot(gwasID, ciMap){
	if(ciMap==1){
		var chr = [];
		$.ajax({
			url: subdir+"/browse/circos_chr",
			type: 'POST',
	        data: {
	          id: gwasID,
			  prefix: prefix
	        },
			success: function(data){
				chr = data.split(":");
				for(var i=0; i<chr.length; i++){
					chr[i] = parseInt(chr[i]);
				}
				chr.sort(function(a,b){return a-b;});
			},
			complete: function(){
				var images = "";
				var j = 0;
				for(var i=0; i<chr.length; i++){
					j++;
					if(i==0){
						images += '<div class="row"><div class="col-md-4 col-xs-4 col-sm-4">'
								+'Chromosome '+chr[i]+'<br/>'
								+'<a target="_blank" href="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"><img width="80%" src="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"></img></a><br/><br/>'
								+'</div>';
					}else if(i==chr.length-1){
						images += '<div class="col-md-4 col-xs-4 col-sm-4">'
								+'Chromosome '+chr[i]+'<br/>'
								+'<a target="_blank" href="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"><img width="80%" src="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"></img></a><br/><br/>'
								+'</div></div>';
					}else if(j==3){
						images += '<div class="col-md-4 col-xs-4 col-sm-4">'
								+'Chromosome '+chr[i]+'<br/>'
								+'<a target="_blank" href="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"><img width="80%" src="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"></img></a><br/><br/>'
								+'</div></div>';
						j=0;
					}else{
						images += '<div class="col-md-4 col-xs-4 col-sm-4">'
								+'Chromosome '+chr[i]+'<br/>'
								+'<a target="_blank" href="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"><img width="80%" src="'+subdir+'/browse/circos_image/'+prefix+'/'+gwasID+'/circos_chr'+chr[i]+'.png'+'"></img></a><br/><br/>'
								+'</div>';
					}
				}
				$('#ciMapCircosPlot').html(images);
			}
		});
	}
}

function showResultTables(filedir, gwasID, posMap, eqtlMap, ciMap, orcol, becol, secol){
	$('#plotClear').hide();
	$('#download').attr('disabled', false);
	if(eqtlMap==0){
		$('#eqtlTableTab').hide();
		$('#check_eqtl_annotPlot').hide();
		$('#annotPlot_eqtl').prop('checked', false);
		$('#eqtlfiledown').hide();
		$('#eqtlfile').prop('checked',false);
	}

	if(ciMap==0){
		$('#ciTableTab').hide();
		$('#check_ci_annotPlot').hide();
		$('#annotPlot_ci').prop('checked', false);
		$('#cifiledown').hide();
		$('#cifile').prop('checked',false);
	}

	$.ajax({
		url: subdir+"/browse/paramTable",
		type: "POST",
		data: {
			filedir: filedir,
		},
		error: function(){
			alert("param table error");
		},
		success: function(data){
			$('#paramTable').html(data);
		}
	});

	$.ajax({
		url: subdir+"/browse/sumTable",
		type: "POST",
		data: {
			filedir: filedir,
		},
		success: function(data){
			$('#sumTable').append(data);
		},
		complete: function(){
			PlotSNPAnnot(gwasID);
			PlotLocuSum(gwasID);
		}
	});

	var file = "GenomicRiskLoci.txt";
	var lociTable = $('#lociTable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax" : {
			url: "DTfile",
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "GenomicLocus:uniqID:rsID:chr:pos:p:start:end:nSNPs:nGWASSNPs:nIndSigSNPs:IndSigSNPs:nLeadSNPs:LeadSNPs"
			}
		},
		error: function(){
		alert("GenomicRiskLoci table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "leadSNPs.txt";
	var leadTable = $('#leadSNPtable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax" : {
			url: "DTfile",
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nIndSigSNPs:IndSigSNPs"
			}
		},
		error: function(){
			alert("sigSNPs table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10,
	});

	file = "IndSigSNPs.txt";
	var IndSigTable = $('#sigSNPtable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax" : {
			url: "DTfile",
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nSNPs:nGWASSNPs"
			}
		},
		error: function(){
		alert("sigSNPs table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10,
	});

	var table = "<thead>"
		+"<tr>"
		+"<th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>ref</th><th>alt</th><th>MAF</th><th>gwasP</th>";
	var cols = "uniqID:rsID:chr:pos:ref:alt:MAF:gwasP";
	if(orcol!="NA"){
		table += "<th>OR</th>";
		cols += ":or";
	}
	if(becol!="NA"){
		table += "<th>Beta</th>";
		cols += ":beta";
	}
	if(secol!="NA"){
		table += "<th>SE</th>";
		cols += ":se";
	}
	table +="<th>Genomic Locus</th><th>r2</th><th>IndSigSNP</th><th>Nearest gene</th><th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>"
		+"</tr>"
		+"</thead>";
	cols += ":GenomicLocus:r2:IndSigSNP:nearestGene:dist:func:CADD:RDB:minChrState:commonChrState";
	file = "snps.txt";
	$('#SNPtable').html(table)
	var SNPtable = $('#SNPtable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax:{
			url: 'DTfile',
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: cols
			}
		},
		error: function(){
			alert("SNP table error");
		},
		// "order": [[2, 'asc'], [3, 'asc']],
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "annov.txt";
	var annovTable = $('#annovTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax:{
			url: 'DTfile',
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "uniqID:chr:pos:gene:symbol:dist:annot:exonic_func:exon"
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "genes.txt";
	var table = "<thead><tr><th>Gene</th><th>Symbol</th><th>HUGO</th><th>entrezID</th><th>chr</th><th>start</th><th>end</th>";
	table += "<th>strand</th><th>status</th><th>type</th><th>pLI</th><th>ncRVIS</th>";
	var col = "ensg:symbol:HUGO:entrezID:chr:start:end:strand:status:type:pLI:ncRVIS";
	if(posMap==1){
		table += "<th>posMapSNPs</th><th>posMapMaxCADD</th>";
		col += ":posMapSNPs:posMapMaxCADD";
	}
	if(eqtlMap==1){
		table += "<th>eqtlMapSNPs</th><th>eqtlMapminP</th><th>eqtlMapminQ</th><th>eqtlMapts</th><th>eqtlDirection</th>";
		col += ":eqtlMapSNPs:eqtlMapminP:eqtlMapminQ:eqtlMapts:eqtlDirection";
	}
	table += "<th>minGwasP</th><th>Genomic Locus</th><th>IndSigSNPs</th></tr></thead>";
	col += ":minGwasP:GenomicLocus:IndSigSNPs"
	$('#geneTable').append(table);
	var geneTable;
	geneTable = $('#geneTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax:{
			url: 'DTfile',
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: col
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	if(eqtlMap==1){
		file = "eqtl.txt";
		var eqtlTable = $('#eqtlTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax:{
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					id: gwasID,
					prefix: prefix,
					infile: file,
					header: "uniqID:chr:pos:db:tissue:gene:symbol:p:FDR:tz"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});
	}

	if(ciMap==1){
		file = "ci.txt";
		var ciTable = $('#ciTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax:{
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					id: gwasID,
					prefix: prefix,
					infile: file,
					header: "GenomicLocus:region1:region2:FDR:type:DB:tissue/cell:inter/intra:SNPs:genes"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});

		file = "ciSNPs.txt";
		var ciSNPsTable = $('#ciSNPsTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax:{
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					id: gwasID,
					prefix: prefix,
					infile: file,
					header: "uniqID:rsID:chr:pos:reg_region:type:tissue/cell"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});

		file = "ciProm.txt";
		var ciGenesTable = $('#ciGenesTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax:{
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					id: gwasID,
					prefix: prefix,
					infile: file,
					header: "region2:reg_region:type:tissue/cell:genes"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});
	}

	file = "gwascatalog.txt";
	var gwascatTable = $('#gwascatTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax:{
			url: 'DTfile',
			type: "POST",
			data: {
				id: gwasID,
				prefix: prefix,
				infile: file,
				header: "GenomicLocus:leadSNP:chr:bp:snp:PMID:Trait:FirstAuth:Date:P"
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
  // file = "ExAC.txt";
  // var eqtlTable = $('#exacTable').DataTable({
  //   processing: true,
  //   serverSide: false,
  //   select: false,
  //   ajax:{
  //     url: 'DTfile',
  //     type: "POST",
  //     data: {
  //       id: gwasID,
  // 	   prefix: prefix,
  //       infile: file,
  //     }
  //   },
  //   columns:[
  //     {"data": "GenomicLocus", name:"GenomicLocus"},
  //     {"data": "uniqID", name:"uniqID"},
  //     {"data": "chr", name:"chr"},
  //     {"data": "pos", name:"bp"},
  //     {"data": "ref", name:"ref"},
  //     {"data": "alt", name:"alt"},
  //     {"data": "annot", name:"Annotation"},
  //     {"data": "gene", name:"Gene"},
  //     {"data": "MAF", name:"MAF"},
  //     {"data": "MAF_FIN", name:"MAF(FIN)"},
  //     {"data": "MAF_NFE", name:"MAF(NFE)"},
  //     {"data": "MAF_AMR", name:"MAF(AMR)"},
  //     {"data": "MAF_AFR", name:"MAF(AFR)"},
  //     {"data": "MAF_EAS", name:"MAF(EAS)"},
  //     {"data": "MAF_SAS", name:"MAF(SAS)"},
  //     {"data": "MAF_OTH", name:"MAF(OTH)"},
  //   ],
  //   "order": [[2, 'asc'], [3, 'asc']],
  //   "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  //   "iDisplayLength": 10,
  //   dom: 'lBfrtip',
  //   buttons: ['csv']
  // });


	$('#sigSNPtable tbody').on('click', 'tr', function(){
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('IndSigSNP');
		var rowI = IndSigTable.row(this).index();
		sigSNPtable_selected=rowI;
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = IndSigTable.row(rowI).data();
		var chr = rowData[4];

		$.ajax({
			url: subdir+'/browse/locusPlot',
			type: "POST",
			data:{
				type: "IndSigSNP",
				id: gwasID,
				prefix: prefix,
				rowI: rowI
			},
			success: function(data){
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "IndSigSNP", chr);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected Ind. Sig. SNP</h5><table class='table table-striped'><tr><td>Ind. Sig. SNP</td><td>"+rowData[3]
			+"</td></tr><tr><td>Chrom</td><td>"+rowData[4]+"</td></tr><tr><td>BP</td><td>"
			+rowData[5]+"</td></tr><tr><td>P-value</td><td>"+rowData[6]+"</td></tr><tr><td>SNPs within LD</td><td>"
			+rowData[7]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData[8]+"</td></tr>";
		$('#selectedLeadSNP').html(out);
	});

	$('#leadSNPtable tbody').on('click', 'tr', function(){
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('leadSNP');
		var rowI = leadTable.row(this).index();
		sigSNPtable_selected=rowI;
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = leadTable.row(rowI).data();
		var chr = rowData[4];

		$.ajax({
			url: subdir+'/browse/locusPlot',
			type: "POST",
			data:{
				type: "leadSNP",
				id: gwasID,
				prefix: prefix,
				rowI: rowI
			},
			success: function(data){
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "leadSNP", chr);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected lead SNP</h5><table class='table table-striped'><tr><td>Lead SNP</td><td>"+rowData[3]
			+"</td></tr><tr><td>Chrom</td><td>"+rowData[4]+"</td></tr><tr><td>BP</td><td>"
			+rowData[5]+"</td></tr><tr><td>P-value</td><td>"+rowData[6]+"</td></tr>"
			+"<tr><td>#Ind. Sig. SNPs</td><td>"+rowData[7]+"</td></tr>";
		$('#selectedLeadSNP').html(out);
	});

	$('#lociTable tbody').on('click', 'tr', function(){
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('GenomicLocus');
		var rowI = lociTable.row(this).index();
		lociTable_selected=rowI;
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = lociTable.row(rowI).data();
		var chr = rowData[3];

		$.ajax({
			url: subdir+'/browse/locusPlot',
			type: "POST",
			data:{
				type: "loci",
				id: gwasID,
				prefix: prefix,
				rowI: rowI
			},
			success: function(data){
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "loci", chr);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected Locus</h5><table class='table table-striped'><tr><td>top lead SNP</td><td>"+rowData[2]
			+"</td></tr><tr><td>Chrom</td><td>"+rowData[3]+"</td></tr><tr><td>BP</td><td>"
			+rowData[4]+"</td></tr><tr><td>P-value</td><td>"+rowData[5]+"</td></tr>"
			+"<tr><td>#Ind. Sig. SNPs</td><td>"+rowData[10]+"</td></tr><tr><td>#lead SNPs</td><td>"+rowData[12]
			+"</td></tr><tr><td>SNPs within LD</td><td>"
			+rowData[8]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData[9]+"</td></tr>";

		$('#selectedLeadSNP').html(out);
	});

	function locusPlot(data, type, chr){
		// create plot space
		var colorScale = d3.scale.linear().domain([0.0,0.5,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
		var margin = {top:50, right: 50, bottom:60, left:50},
			width = 700-margin.right - margin.left,
			height = 300-margin.top - margin.bottom;
		// set range
		var x = d3.scale.linear().range([0, width]);
		var y = d3.scale.linear().range([height, 0]);

		var svg = d3.select("#locusPlot").append("svg")
			.attr("width", width+margin.left+margin.right)
			.attr("height", height+margin.top+margin.bottom)
			.append("g").attr("transform", "translate("+margin.left+","+margin.top+")");

		var legData = [];
		for(i=10; i>0; i--){
			legData.push(i*0.1);
		}
		// document.getElementById('test').innerHTML += "0: "+legData[0]+"<br/>9:"+legData[9]+"<br>";
		var legendGwas = svg.selectAll(".legendGWAS")
			.data(legData)
			.enter()
			.append("g").attr("class", "legend")
		legendGwas.append("rect")
			.attr("x", width+10)
			.attr("y", function(d){return 10+(10-d*10)*10})
			.attr("width", 20)
			.attr("height", 10)
			.style("fill", function(d){return colorScale(d)});
		legendGwas.append("text")
			.attr("text-anchor", "start")
			.attr("x", width+32)
			.attr("y", function(d){return 20+(10-d*10)*10})
			.text(function(d){return Math.round(d*100)/100})
			.style("font-size", "10px");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(width+20)+",5)")
			.text("r2").style("font-size", "10px");

		svg.append("circle")
			.attr("cx", 145).attr("cy", height+45).attr("r", 4.5)
			.style("fill", "#4d0099").style("stroke", "black").style("strole-width", "2");
		svg.append("text").attr("text-anchor", "top")
			.attr("x", 150).attr("y", height+50)
			.text("Top lead SNP");
		svg.append("circle")
			.attr("cx", 250).attr("cy", height+45).attr("r", 4)
			.style("fill", "#9933ff").style("stroke", "black").style("strole-width", "2");
		svg.append("text").attr("text-anchor", "top")
			.attr("x", 255).attr("y", height+50)
			.text("Lead SNPs");
		svg.append("circle")
			.attr("cx", 340).attr("cy", height+45).attr("r", 3.5)
			.style("fill", "red").style("stroke", "black").style("strole-width", "2");
		svg.append("text").attr("text-anchor", "top")
			.attr("x", 345).attr("y", height+50)
			.text("Independent significant SNPs");

		data.snps.forEach(function(d){
			d.pos = +d.pos;
			d.gwasP = +d.gwasP;
			d.r2 = +d.r2;
			d.ld = +d.ld;
		});

		data.allsnps.forEach(function(d){
			d[0] = +d[0]; //pos
			d[1] = +d[1]; //P
		});

		var side=(d3.max(data.allsnps, function(d){return d[0]})-d3.min(data.allsnps, function(d){return d[0]}))*0.05;
		x.domain([d3.min(data.allsnps, function(d){return d[0]})-side, d3.max(data.allsnps, function(d){return d[0]})+side]);
		y.domain([0, Math.max(d3.max(data.snps, function(d){return -Math.log10(d.gwasP)}), d3.max(data.allsnps, function(d){return -Math.log10(d[1])}))]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		// tip
		var tip = d3.tip().attr("class", "d3-tip")
			.offset([-10,0])
			.html(function(d){
				var out = "rsID: "+d.rsID+"<br/>BP: "+d.pos+"<br/>P: "+d.gwasP+"<br/>MAF: "+d.MAF
					+"<br/>r2: "+d.r2+"<br/>Ind. Sig. SNP: "+d.IndSigSNP;
				if(orcol!="NA"){out += "<br/>OR: "+d.or;}
				if(becol!="NA"){out += "<br/>Beta: "+d.beta;}
				if(secol!="NA"){out += "<br/>SE: "+d.se;}
				return out;
			});
		svg.call(tip);
		// zoom
		var zoom = d3.behavior.zoom().x(x).scaleExtent([1,10]).on("zoom", zoomed);
		svg.call(zoom);
		// add rect
		svg.append("rect").attr("width", width).attr("height", height)
			.style("fill", "transparent")
			.style("shape-rendering", "crispEdges");

		// dot plot for gwas tagged SNPs
		// SNPs not in LD
		svg.selectAll("dot").data(data.allsnps).enter()
			.append("circle")
			.attr("class", "nonLD")
			.attr("r", 3).attr("cx", function(d){return x(d[0]);})
			.attr("cy", function(d){return y(-Math.log10(d[1]));})
			.style('fill', "grey");
		// SNPs in LD
		svg.selectAll("dot").data(data.snps.filter(function(d){if(d.gwasP!=-1 && d.ld==1){return d;}})).enter()
			.append("circle")
			.attr("class", "dot")
			.attr("r", 3).attr("cx", function(d){return x(d.pos);})
			.attr("cy", function(d){return y(-Math.log10(d.gwasP));})
			.style('fill', function(d){return colorScale(d.r2);})
			.on("mouseover", tip.show)
			.on("mouseout", tip.hide);
		// add rect for 1KG SNPs
		svg.selectAll("rect.KGSNPs").data(data.snps.filter(function(d){if(d.gwasP==-1){return d;}})).enter()
			.append("rect")
			.attr("class", "KGSNPs")
			.attr("x", function(d){return x(d.pos)})
			.attr("y", -20)
			.attr("height", "10")
			.attr("width", "3")
			.style('fill', function(d){if(d.ld==0){return "grey";}else{return colorScale(d.r2);}})
			.on("mouseover", tip.show)
			.on("mouseout", tip.hide);

		svg.selectAll("dot.leadSNPs").data(data.snps.filter(function(d){if(d.ld>1){return d;}})).enter()
			.append("circle")
			.attr("class", "leadSNPs")
			.attr("cx", function(d){return x(d.pos)})
			.attr("cy", function(d){return y(-Math.log10(d.gwasP));})
			.attr("r", function(d){
				if(d.ld==2){return 3.5;}
				else if(d.ld==3){return 4;}
				else if(d.ld==4){return 4.5;}
			})
			.style("fill", function(d){
				if(d.ld==2){return colorScale(d.r2);}
				else if(d.ld==3){return "#9933ff"}
				else if(d.ld==4){return "#4d0099"}
			})
			.style("stroke", "black").style("stroke-width", "2")
			.on("mouseover", tip.show)
			.on("mouseout", tip.hide);
		// axis labels
		svg.append("g").attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")").call(xAxis);
		svg.append("g").attr("class", "y axis").call(yAxis);
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-margin.left/2-5)+","+(height/2)+")rotate(-90)")
			.text("-log10 P-value");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(width/2)+","+(height+32)+")")
			.text("Chromosome "+chr);
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-margin.left/2)+", -15)")
			.style("font-size", "8px")
			.text("1000G SNPs");

		function zoomed() {
			svg.select(".x.axis").call(xAxis);
			svg.selectAll(".nonLD").attr("cx", function(d){return x(d[0]);})
				.attr("cy", function(d){return y(-Math.log10(d[1]));})
				.style("fill", function(d){if(x(d[0])<0 || x(d[0])>width){return "transparent";}else{return"grey";}});
			svg.selectAll(".dot").attr("cx", function(d){return x(d.pos);})
				.attr("cy", function(d){return y(-Math.log10(d.gwasP));})
				.style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return"grey";}else{return colorScale(d.r2);}});
			svg.selectAll(".KGSNPs")
				.attr("x", function(d){return x(d.pos)})
				.attr("y", -20)
				.style('fill', function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return "grey";}else{return colorScale(d.r2);}});
			svg.selectAll(".leadSNPs")
				.attr("cx", function(d){return x(d.pos);})
				.attr("cy", function(d){return y(-Math.log10(d.gwasP));})
				.style("fill", function(d){
					if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}
					else if(d.ld==2){return colorScale(d.r2);}
					else if(d.ld==3){return "#9933ff"}
					else if(d.ld==4){return "#4d0099"}
				})
				.style("stroke", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "black";}});
		}

		d3.select('#plotClear').on('click', reset);
		function reset(){
			d3.transition().duration(750).tween("zoom", function(){
				var ix = d3.interpolate(x.domain(), [d3.min(data.allsnps, function(d){return d[0]})-side, d3.max(data.allsnps, function(d){return d[0]})+side]);
				return function(t){
					zoom.x(x.domain(ix(t)));
					zoomed();
				}
			});
		}
	}
}

function PlotSNPAnnot(gwasID){
	var file = "snpsannot.txt";
	// filedir = filedir.replace("../", "");
	var margin = {top:20, right: 30, bottom:90, left:50},
		width = 500-margin.right - margin.left,
		height = 250-margin.top - margin.bottom;
	var x_element = ["intergenic", "downstream", "upstream", "UTR3", "UTR5", "intronic", "exonic", "ncRNA_intronic", "ncRNA_exonic", "NA"];
	var x = d3.scale.ordinal().domain(x_element).rangeRoundBands([0,width], 0.1);
	var y = d3.scale.linear().range([height, 0]);
	var xAxis = d3.svg.axis().scale(x).orient("bottom");
	var yAxis = d3.svg.axis().scale(y).orient("left");
	var svg = d3.select('#snpAnnotPlot').append('svg')
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append('g').attr("transform", "translate("+margin.left+","+margin.top+")");
	var tip = d3.tip()
		.attr('class', 'd3-tip')
		.offset([-5, 0])
		.html(function(d) {
			return d.count;
		})
	svg.call(tip);
	d3.json("d3text/"+prefix+"/"+gwasID+"/"+file, function(data){
		data.forEach(function(d){
			d.count =+ d.count;
		});
		y.domain([0, d3.max(data, function(d){return d.count})]);
		svg.selectAll('.bar').data(data).enter().append('rect').attr("class", "bar")
			.attr("x", function(d){return x(d.annot);})
			.attr("width", x.rangeBand())
			.attr("y", function(d){return y(d.count);})
			.attr("height", function(d){return height-y(d.count);})
			.attr("fill", "steelblue")
			.on("mouseover", tip.show)
			.on("mouseout", tip.hide);
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")")
			.call(xAxis).selectAll('text')
			.attr("transform", function (d) {return "rotate(-65)";})
			.attr("dy", "-.45em")
			.attr("dx", "-.65em")
			.style("text-anchor", "end");
		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.append("text")
			.attr("transform", "rotate(-90)")
			.attr("dy", ".71em")
			.style("text-anchor", "end");
		svg.selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');
		svg.selectAll('.axis').selectAll('text').style('font-size', '11px');
	});
}

function PlotLocuSum(gwasID){
	var file="interval_sum.txt";
	d3.json("d3text/"+prefix+"/"+gwasID+"/"+file, function(data){
		data.forEach(function(d){
			d.nSNPs = +d.nSNPs;
			d.size = +(d.size/1000);
			d.nGenes = +d.nGenes;
			d.nWithinGene = +d.nWithinGene;
		});
		var y_element = data.map(function(d){return d.label;});
		var margin = {top:60, right: 30, bottom:70, left:180},
			width = 600,
			height = 15*y_element.length;
		var y = d3.scale.ordinal().domain(y_element).rangeBands([0, height], 0.1);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		var svg = d3.select('#lociPlot').append('svg')
			.attr("class", 'plotSVG')
			.attr("width", width+margin.left+margin.right)
			.attr("height", height+margin.top+margin.bottom)
			.append('g').attr("transform", "translate("+margin.left+","+margin.top+")");
		var tip_size = d3.tip()
			.attr('class', 'd3-tip')
			.offset([0, 0])
			.html(function(d) {return d.size+" kb";});
		svg.call(tip_size);
		var tip_nSNPs = d3.tip()
			.attr('class', 'd3-tip')
			.offset([0, 0])
			.html(function(d) {return d.nSNPs;});
		svg.call(tip_nSNPs);
		var tip_nGenes = d3.tip()
			.attr('class', 'd3-tip')
			.offset([0, 0])
			.html(function(d) {return d.nGenes;});
		svg.call(tip_nGenes);
		var tip_nWithinGene = d3.tip()
			.attr('class', 'd3-tip')
			.offset([0, 0])
			.html(function(d) {return d.nWithinGene;});
		svg.call(tip_nWithinGene);
		var currentWidth = 0;
		var eachWidth = 140;
		// plot nSNPs
		var x = d3.scale.linear().range([currentWidth, currentWidth+eachWidth]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain([0, d3.max(data, function(d){return d.size})]);
		svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
			.attr("x", x(0))
			.attr("width", function(d){return x(d.size)})
			.attr("y", function(d){return y(d.label)})
			.attr("height", y.rangeBand())
			.attr("fill", "lightgreen")
			.on("mouseover", tip_size.show)
			.on("mouseout", tip_size.hide);
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")")
			.call(xAxis).selectAll("text")
			.style("text-anchor", "end")
			.attr("transform", function (d) {return "translate(-12,3)rotate(-65)";});

		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.append("text").attr("transform", "rotate(-90)")
			.attr("dy", ".71em")
			.style("text-anchor", "end");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(-50,-5)")
			.text("Genomic loci");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
			.style("text-anchor", "middle")
			.text("Size (kb)");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-20)+")")
			.style("text-anchor", "middle")
			.text("Size (kb)");
		currentWidth += eachWidth + 10;
		// plot size
		x = d3.scale.linear().range([currentWidth, currentWidth+eachWidth]);
		xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain([0, d3.max(data, function(d){return d.nSNPs;})]);
		svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
			.attr("x", x(0))
			.attr("width", function(d){return x(d.nSNPs)-currentWidth})
			.attr("y", function(d){return y(d.label)})
			.attr("height", y.rangeBand())
			.attr("fill", "skyblue")
			.on("mouseover", tip_nSNPs.show)
			.on("mouseout", tip_nSNPs.hide);
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")")
			.call(xAxis).selectAll("text")
			.style("text-anchor", "end")
			.attr("transform", function (d) {return "translate(-12,3)rotate(-65)";});
		svg.append('g').attr("class", "y axis")
			.attr("transform", "translate("+currentWidth+",0)")
			.call(yAxis).selectAll("text").remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
			.style("text-anchor", "middle")
			.text("#SNPs");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-20)+")")
			.style("text-anchor", "middle")
			.text("#SNPs");
		currentWidth += eachWidth + 10;

		// plot nGene
		x = d3.scale.linear().range([currentWidth, currentWidth+eachWidth]);
		xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain([0, d3.max(data, function(d){return d.nGenes;})]);
		svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
			.attr("x", x(0))
			.attr("width", function(d){return x(d.nGenes)-currentWidth})
			.attr("y", function(d){return y(d.label)})
			.attr("height", y.rangeBand())
			.attr("fill", "orange")
			.on("mouseover", tip_nGenes.show)
			.on("mouseout", tip_nGenes.hide);
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")")
			.call(xAxis).selectAll("text")
			.style("text-anchor", "end")
			.attr("transform", function (d) {return "translate(-12,3)rotate(-65)";});
		// .attr("dx","-.65em").attr("dy", "-.2em");
		svg.append('g').attr("class", "y axis")
			.attr("transform", "translate("+currentWidth+",0)")
			.call(yAxis).selectAll("text").remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
			.style("text-anchor", "middle")
			.text("#mapped genes");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-20)+")")
			.style("text-anchor", "middle")
			.text("#mapped genes");
		currentWidth += eachWidth + 10;

		// plot nWithinGene
		x = d3.scale.linear().range([currentWidth, currentWidth+eachWidth]);
		xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain([0, d3.max(data, function(d){return d.nWithinGene;})]);
		svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
			.attr("x", x(0))
			.attr("width", function(d){return x(d.nWithinGene)-currentWidth})
			.attr("y", function(d){return y(d.label)})
			.attr("height", y.rangeBand())
			.attr("fill", "pink")
			.on("mouseover", tip_nWithinGene.show)
			.on("mouseout", tip_nWithinGene.hide);
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+height+")")
			.call(xAxis).selectAll("text")
			.style("text-anchor", "end")
			.attr("transform", function (d) {return "translate(-12,3)rotate(-65)";});
		svg.append('g').attr("class", "y axis")
			.attr("transform", "translate("+currentWidth+",0)")
			.call(yAxis).selectAll("text").remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-20)+")")
			.style("text-anchor", "middle")
			.text("#genes physically");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
			.style("text-anchor", "middle")
			.text("located in loci");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-20)+")")
			.style("text-anchor", "middle")
			.text("#genes physically");
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
			.style("text-anchor", "middle")
			.text("located in loci");
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');
		svg.selectAll('.axis').selectAll('text').style('font-size', '11px');
	});
}

function Chr15Select(){
	if($('#annotPlot_Chrom15').is(":checked")==true){
		$('#annotPlotChr15Opt').show();
		var ts = [];
		var tmp = document.getElementById('annotPlotChr15Ts');
		for(var i=0; i<tmp.options.length; i++){
			if(tmp.options[i].selected===true){
				ts.push(tmp.options[i].value);
			}
		}
		if(ts.length===0){
			$('#CheckAnnotPlotOpt').html('<span class="alert alert-danger">You have selected to plot 15-core chromatin state. Please select at least one tissue/cell type.</span>');
			$('#annotPlotSubmit').attr("disabled", true);
		}else if(ts.length>0){
			$('#CheckAnnotPlotOpt').html("<span class='alert alert-success'>OK. Selected tissue/cell types will appear in the plot.</span>");
			$('#annotPlotSubmit').attr("disabled", false);
		}
	}else{
		$('#annotPlotChr15Opt').hide();
		$('#annotPlotSubmit').attr("disabled", false);
		$('#CheckAnnotPlotOpt').html('<span class="alert alert-success">OK. Good to go. Click "Plot" to create regional plot with selected annotations.</span>');
	}
}

function expHeatMap(id){
	d3.select('#expHeat').select("svg").remove();
	var itemSizeRow = 15, cellSize=itemSizeRow-1, itemSizeCol=10;
	queue().defer(d3.json, "d3text/"+prefix+"/"+id+"/g2f:exp.txt")
		.defer(d3.json, "d3text/"+prefix+"/"+id+"/g2f:exp.row.txt")
		.defer(d3.json, "d3text/"+prefix+"/"+id+"/g2f:exp.col.txt")
		.awaitAll(function(error, data){
			if(data==null || data==undefined){
				$('#expHeat').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> None of your input genes exists in expression data.</span></br>'
				+'Only genes which have average RPKM per tissue > 1 in at least one tissue type are availalbe in the expression data.<br/>'
				+'This might also be because of the mismatch of input gene ID or symbol.<br/></div>');
				$('#expHeat').parent().children('.ImgDown').each(function(){$(this).prop("disabled", true)});
			}else{
				var exp = data[0];
				var rows = data[1];
				var cols = data[2];

				var galph = [];
				var gclstlog2 = [];
				var gclstnorm = [];
				rows.forEach(function(d){
					galph.push(d.alph);
					gclstlog2.push(d.clstLog2);
					gclstnorm.push(d.clstNorm);
				});

				var tsalph = [];
				var tsclstlog2 = [];
				var tsclstnorm = [];
				cols.forEach(function(d){
					tsalph.push(d.alph);
					tsclstlog2.push(d.clstLog2);
					tsclstnorm.push(d.clstNorm);
				});

				exp.forEach(function(d){
					d.log2 = +d.log2;
					d.norm = +d.norm;
				});

				var genes = d3.set(rows.map(function(d){return d.gene})).values();
				var tss = d3.set(cols.map(function(d){return d.ts})).values();
				var margin = {top: 10, right: 60, bottom: 220, left: 100},
					width = 800,
					height = (itemSizeCol*genes.length);

				var svg = d3.select('#expHeat').append('svg')
					.attr("width", width+margin.left+margin.right)
					.attr("height", height+margin.top+margin.bottom)
					.append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
				var log2Max = d3.max(exp,function(d){return d.log2});
				var log2Min = d3.min(exp, function(d){return d.log2;});
				var colorScale = d3.scale.linear().domain([0, log2Max/2, log2Max]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);

				// legened
				var t = [];
				for(var i =0; i<23; i++){t.push(i);}
				var legendRect = svg.selectAll(".legend").data(t).enter().append("g")
					.append("rect")
					.attr("class", 'legendRect')
					.attr("x", width+10)
					.attr("y", function(d){return (t.length-1-d)*10+50})
					.attr("width", 20)
					.attr("height", 10)
					.attr("fill", function(d){return colorScale(d*log2Max/(t.length-1))});
				var legendText = svg.selectAll("text.legend").data([0,11,22]).enter().append("g")
					.append("text")
					.attr("text-anchor", "start")
					.attr("class", "legenedText")
					.attr("x", width+32)
					.attr("y", function(d){return (t.length-1-d)*10+11+50})
					.text(function(d){return Math.round(100*d*log2Max/(t.length-1))/100})
					.style("font-size", "12px");

				// y axis label
				var rowLabels = svg.append("g").selectAll(".rowLabel")
					.data(rows).enter().append("text")
					.text(function(d){return d.gene;})
					.attr("x", -3)
					.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;})
					.style("font-size", "10px")
					.style("text-anchor", "end");

				// x axis label
				var colLabels = svg.append("g").selectAll(".colLabel")
					.data(cols).enter().append("text")
					.text(function(d){return d.ts;})
					.style("text-anchor", "end")
					.style("font-size", "10px")
					.attr("transform", function(d){
						return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
					});
				// colLabels.selectAll(".colLabel").attr("transform", function(d){return "rotate(-65)"});

				var heatMap = svg.append("g").attr("class", "cell heatmapcell")
					.selectAll("rect.cell").data(exp).enter()
					.append("rect")
					.attr("width", cellSize).attr("height", itemSizeCol-0.5)
					.attr('y', function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
					.attr('x', function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow})
					.attr('fill', function(d){return colorScale(d.log2)});

				svg.append('text').attr("text-anchor", "middle")
					.attr("transform", "translate("+(-margin.left/2-10)+","+height/2+")rotate(-90)")
					.text("genes");
				svg.append('text').attr("text-anchor", "middle")
					.attr("transform", "translate("+width/2+","+(height+margin.bottom-10)+")")
					.text("Tissue types");

				svg.selectAll('text').style('font-family', 'sans-serif');
				// Change ordeing of cells
				function sortOptions(type, val, gsort, tssort){
					if(type=="color"){
						if(val=="log2RPKM"){
							var log2Max = d3.max(exp,function(d){return d.log2});
							var log2Min = d3.min(exp, function(d){return d.log2;});
							var col = d3.scale.linear().domain([0, (log2Max+log2Min)/2, log2Max]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
							legendRect.attr("fill", function(d){return col(d*log2Max/(t.length-1))});
							legendText.text(function(d){return Math.round(100*d*log2Max/(t.length-1))/100})
							if(gsort=="clst" && tssort=="clst"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.log2)})
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="clst" && tssort=="alph"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.log2)})
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="alph" && tssort=="clst"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.log2)})
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="alph" && tssort=="alph"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.log2)})
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}
						}else{
							var normMax = d3.max(exp,function(d){return d.norm});
							var normMin = d3.min(exp, function(d){return d.norm;});
							var m = Math.max(normMax, Math.abs(normMin));
							var col = d3.scale.linear().domain([-m, 0, m]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
							legendRect.attr("fill", function(d){return col(d*2*m/(t.length-1)-m)});
							legendText.text(function(d){return Math.round(d*2*m/(t.length-1)-m)});
							if(gsort=="clst" && tssort=="clst"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.norm)})
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="clst" && tssort=="alph"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.norm)})
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="alph" && tssort=="clst"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.norm)})
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else if(gsort=="alph" && tssort=="alph"){
								heatMap.transition().duration(2000)
									.attr("fill", function(d){return col(d.norm)})
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
									.attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}
						}
					}else if(type=="geneSort"){
						if(gsort=="clst"){
							if(val=="log2RPKM"){
								heatMap.transition().duration(2000)
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol;});
							}else{
								heatMap.transition().duration(2000)
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol});
								rowLabels.transition().duration(2000)
									.attr("y", function(d){return gclstnorm[genes.indexOf(d.gene)]*itemSizeCol;});
							}
						}else{
							heatMap.transition().duration(2000)
								.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol});
							rowLabels.transition().duration(2000)
								.attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
						}
					}else if(type="tsSort"){
						if(tssort=="clst"){
							if(val=="log2RPKM"){
								heatMap.transition().duration(2000)
									.attr("x", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}else{
								heatMap.transition().duration(2000)
									.attr("x", function(d){return tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
								colLabels.transition().duration(2000)
									.attr("transform", function(d){
										return "translate("+(tsclstnorm[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
									});
							}
						}else{
							heatMap.transition().duration(2000)
								.attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
							colLabels.transition().duration(2000)
								.attr("transform", function(d){
									return "translate("+(tsalph[tss.indexOf(d.ts)]*itemSizeRow-5)+","+(height+3)+")rotate(-90)";
								});
						}
					}
				};

				d3.select('#expval').on("change", function(){
					var val = this.value;
					var gsort = $('#geneSort').val();
					var tssort = $('#tsSort').val();
					sortOptions("color", val, gsort, tssort);
				});

				d3.select('#geneSort').on("change", function(){
					var val = $('#expval').val();
					var gsort = this.value;
					var tssort = $('#tsSort').val();
					sortOptions('geneSort', val, gsort, tssort);
				});

				d3.select('#tsSort').on("change", function(){
					var val = $('#expval').val();
					var gsort = $('#geneSort').val();
					var tssort = this.value;
					sortOptions('tsSort', val, gsort, tssort);
				});
			}
		});
}

function tsEnrich(id){
	d3.select('#tsEnrichBar').select('svg').remove();
	var span = 150;
	var currentHeight = 0;
	var margin = {top: 20, right: 20, bottom: 230, left: 80},
		width = 900,
		height = span*3+20;

	var x = d3.scale.ordinal().rangeBands([0,width]);
	var xAxis = d3.svg.axis().scale(x).orient("bottom");
	var svg = d3.select('#tsEnrichBar').append('svg')
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

	d3.json(subdir+"/browse/DEGPlot/specific/"+id, function(data){
		if(data==null || data==undefined){
			$('#tsEnrichBar').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> The number of input genes exist in the selected background genes was 0 or 1.</span></br>'
			+'Enrichment of differentially expressed genes in different tissue types require at least 2 gene to test.<br/>'
			+'This might be because of the mismatch of input gene ID or symbol.<br/></div>');
			$('#DEGdown').prop("disabled", true);
			$('#tsEnrichBarPanel').children('.ImgDown').each(function(){$(this).prop("disabled", true)});
		}else{
			data.data.forEach(function(d){
				d[2] = +d[2]; //p
				d[3] = +d[3]; //adj.p
			});
			var ts = d3.set(data.data.map(function(d){return d[1]})).values();
			x.domain(ts);
			var cellsize = width/ts.length;
			//up-regulated
			var yup = d3.scale.linear().range([currentHeight+span, currentHeight]);
			var yAxisup = d3.svg.axis().scale(yup).orient("left").ticks(4);
			yup.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var xLabels = svg.append("g").selectAll(".xLabel")
				.data(ts).enter().append("text")
				.text(function(d){return d;})
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.style("font-family", "sans-serif")
				.attr("transform", function(d){
					return "translate("+(data.order.two[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
				});

			var barup = svg.selectAll('rect.up').data(data.data.filter(function(d){if(d[0]=="DEG.up"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return yup(-Math.log10(d[2]))})
				.attr("height", function(d){return currentHeight+span-yup(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
			svg.append('g').attr("class", "y axis")
				.call(yAxisup)
				.selectAll('test').style('font-size', '11px');
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
			ydown.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var bardown = svg.selectAll('rect.down').data(data.data.filter(function(d){if(d[0]=="DEG.down"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return ydown(-Math.log10(d[2]))})
				.attr("height", function(d){return currentHeight+span-ydown(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
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
			y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var bartwo = svg.selectAll('rect.two').data(data.data.filter(function(d){if(d[0]=="DEG.twoside"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return y(-Math.log10(d[2]))})
				.attr("height", function(d){return height-y(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
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

			function sortOptions(type){
				if(type=="alph"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.alph[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="up"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.up[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="down"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.down[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="two"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.two[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}
			}
			d3.select('#DEGorder').on("change", function(){
				var type = $('#DEGorder').val();
				sortOptions(type);
			});
		}
	});
}

function tsGeneralEnrich(id){
	d3.select('#tsGeneralEnrichBar').select('svg').remove();
	var span = 150;
	var currentHeight = 0;
	var margin = {top: 20, right: 20, bottom: 80, left: 80},
		width = 600,
		height = span*3+20;

	var x = d3.scale.ordinal().rangeBands([0,width]);
	var xAxis = d3.svg.axis().scale(x).orient("bottom");
	var svg = d3.select('#tsGeneralEnrichBar').append('svg')
		.attr("width", width+margin.left+margin.right)
		.attr("height", height+margin.top+margin.bottom)
		.append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

	d3.json(subdir+"/browse/DEGPlot/general/"+id, function(data){
		if(data==null || data==undefined){
			$('#tsGeneralEnrichBar').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> The number of input genes exist in the selected background genes was 0 or 1.</span></br>'
			+'Enrichment of differentially expressed genes in different tissue types require at least 2 gene to test.<br/>'
			+'This might be because of the mismatch of input gene ID or symbol.<br/></div>');
			$('#DEGgdown').prop("disabled", true);
		}else{
			data.data.forEach(function(d){
				d[2] = +d[2]; //p
				d[3] = +d[3]; //adj.p
			});
			var ts = d3.set(data.data.map(function(d){return d[1]})).values();
			x.domain(ts);
			var cellsize = width/ts.length;
			//up-regulated
			var yup = d3.scale.linear().range([currentHeight+span, currentHeight]);
			var yAxisup = d3.svg.axis().scale(yup).orient("left").ticks(4);
			yup.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var xLabels = svg.append("g").selectAll(".xLabel")
				.data(ts).enter().append("text")
				.text(function(d){return d;})
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.style("font-family", "sans-serif")
				.attr("transform", function(d){
					return "translate("+(data.order.two[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
				});

			var barup = svg.selectAll('rect.up').data(data.data.filter(function(d){if(d[0]=="DEG.up"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return yup(-Math.log10(d[2]))})
				.attr("height", function(d){return currentHeight+span-yup(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
			svg.append('g').attr("class", "y axis")
				.call(yAxisup)
				.selectAll('test').style('font-size', '11px');
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
			ydown.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var bardown = svg.selectAll('rect.down').data(data.data.filter(function(d){if(d[0]=="DEG.down"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return ydown(-Math.log10(d[2]))})
				.attr("height", function(d){return currentHeight+span-ydown(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
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
			y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[2])})]);

			var bartwo = svg.selectAll('rect.two').data(data.data.filter(function(d){if(d[0]=="DEG.twoside"){return d;}})).enter()
				.append("rect").attr("class", "bar")
				.attr("x", function(d){return data.order.two[d[1]]*cellsize;})
				.attr("width", cellsize-1)
				.attr("y", function(d){return y(-Math.log10(d[2]))})
				.attr("height", function(d){return height-y(-Math.log10(d[2]));})
				.style("fill", function(d){
					if(d[3]>0.05){return "#5668f4";}
					else{return "#c00";}
				})
				.style("stroke", "grey")
				.style("stroke-width", 0.3);
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

			function sortOptions(type){
				if(type=="alph"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.alph[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.alph[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="up"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.up[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.up[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="down"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.down[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.down[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}else if(type=="two"){
					barup.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					bardown.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					bartwo.transition().duration(1000)
						.attr("x", function(d){return data.order.two[d[1]]*cellsize;});
					xLabels.transition().duration(1000)
						.attr("transform", function(d){
							return "translate("+(data.order.two[d]*cellsize+((cellsize-1)/2)+3)+","+(height+10)+")rotate(-70)";
						});
				}
			}
			d3.select('#DEGGorder').on("change", function(){
				var type = $('#DEGGorder').val();
				sortOptions(type);
			});
		}
	});
}

function GeneSet(id){
	$('#GeneSet').html("");
	var category = ['Hallmark_gene_sets', 'Positional_gene_sets', 'Curetaed_gene_sets',
			'Chemical_and_Genetic_pertubation', 'Canonical_Pathways', 'BioCarta', 'KEGG', 'Reactome',
			'microRNA_targets', 'TF_targets', 'Computational_gene_sets',
			'Cancer_gene_neighborhoods', 'Cancer_modules', 'GO_bp', 'GO_cc', 'GO_mf',
			'Oncogenetic_signatures', 'Immunologic_signatures', 'Wikipathways',
			'GWAScatalog'
		];
	var category_title = {'Hallmark_gene_sets' : 'Hallmark gene sets (MsigDB v5.2 h)',
			'Positional_gene_sets' : 'Positional gene sets (MsigDB v5.2 c1)',
			'Curetaed_gene_sets' : 'All curated gene sets (MsigDB v5.2 c2)',
			'Chemical_and_Genetic_pertubation' : 'Chemical and Genetic pertubation gene sets (MsigDB v5.2 c2)',
			'Canonical_Pathways' : 'All Canonical Pathways (MsigDB v5.2 c2)',
			'BioCarta' : 'BioCarta (MsigDB v5.2 c2)',
			'KEGG' : 'KEGG (MsigDB v5.2 c2)',
			'Reactome' : 'Reactome (MsigDB v5.2 c2)',
			'microRNA_targets' : 'microRNA targets (MsigDB v5.2 c3)',
			'TF_targets' : 'TF targets (MsigDB v5.2 c3)',
			'Computational_gene_sets' : 'All computational gene sets (MsigDB v5.2 c4)',
			'Cancer_gene_neighborhoods' : 'Cancer gene neighborhoods (MsigDB v5.2 c4)',
			'Cancer_modules' : 'Cancer gene modules (MsigDB v5.2 c4)',
			'GO_bp' : 'GO biological processes (MsigDB v5.2 c5)',
			'GO_cc' : 'GO cellular components (MsigDB v5.2 c5)',
			'GO_mf' : 'GO molecular functions (MsigDB v5.2 c5)',
			'Oncogenetic_signatures' : 'Oncogenetic signatures (MsigDB v5.2 c6)',
			'Immunologic_signatures' : 'Immunologic signatures (MsigDB v5.2 c7)',
			'Wikipathways' : 'WikiPathways (Curated version 20161010)',
			'GWAScatalog' : 'GWAS catalog (reported genes, ver. e85 20160927)'
		};
	d3.json("d3text/"+prefix+"/"+id+"/g2f:GS.txt", function(data){
		if(data == undefined || data == null){
			$('#GeneSet').html('<div style="text-align:center; padding-top:100px; padding-bottom:100px;"><span style="color: red; font-size: 24px;"><i class="fa fa-ban"></i> The number of input genes exist in selected background genes was 0 or 1.</span></br>'
			+'The hypergeometric test is only performed if more than 2 genes are available.</div>');
			$('#GSdown').attr("disabled", true);
		}else{
			for(var i=0; i<category.length; i++){
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
						+category_title[category[i]]+'<tab>(0)</div><div class="panel-body collapse" id="'
						+category[i]+'Panel"><div id="'+category[i]+'" style="text-align: center;">No significant results</div><div id="'
						+category[i]+'Table"></div></div></div>');
					$('#GeneSet').append(panel);
				}else{
					// add div
					var panel = '<div class="panel panel-default" style="padding-top:0;"><div class="panel-heading" style="height: 35px;"><a href="#'
						+category[i]+'Panel" data-toggle="collapse" style="color: black;">'
						+category_title[category[i]]+'<tab>('+tdata.length+')</div><div class="panel-body collapse" id="'
						+category[i]+'Panel"><p><a onclick="GeneSetPlot('+"'"+category[i]+"'"+');">Plot</a> / <a onclick="GeneSetTable('+
						"'"+category[i]+"'"+');">Table</a></p></div></div>';
					$('#GeneSet').append(panel);
					// $('#'+category[i]+"Panel").append('<button class="btn btn-xs ImgDown" id="'+category[i]+'Img" style="float:right; margin-right:100px;">Download PNG</button>');
					$('#'+category[i]+"Panel").append('<div id="'+category[i]+'Plot">Download the plot as '
						+'<button class="btn btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","png");'+"'"+'>PNG</button> '
						+'<button class="btn btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","jpeg");'+"'"+'>JPG</button> '
						+'<button class="btn btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","svg");'+"'"+'>SVG</button> '
						+'<button class="btn btn-xs ImgDown" onclick='+"'"+'GSImgDown("'+category[i]+'","pdf");'+"'"+'>PDF</button> '
						+'<div id="'+category[i]+'" style="overflow: auto; width: 100%;"></div></div>'
						+'<div id="'+category[i]+'Table"></div>');

					$('#'+category[i]+'Table').hide();

					// Plots
					var gs = d3.set(tdata.map(function(d){return d.GeneSet})).values();
					var ngs = gs.length;
					var barplotwidth = 150;

					var margin = {top: 40, right: 10, bottom: 80, left: Math.max(gs_max*6, 80)},
						width = barplotwidth*2+10+(Math.max(genes.length,6)*15),
						height = 15*ngs;
					// $('#test').append("<p>"+category[i]+" width: "+width+"</p>")
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
						.each(function (d) {
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
								.each(function (d) {
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
					if(category[i]=="GWAScatalog"){
						tdata.forEach(function(d){
							table += '<tr><td>'+d.GeneSet+'</td><td>'+d.N_genes+'</td><td>'+d.N_overlap
								+'</td><td>'+Number(Number(d.p).toPrecision(3)).toExponential(2)+'</td><td>'+Number(Number(d.adjP).toPrecision(3)).toExponential(2)+'</td><td>'+d.genes.split(":").join(", ")+'</td></tr>';
						});
					}else{
						tdata.forEach(function(d){
							table += '<tr><td><a href="'+d.link+'" target="_blank">'+d.GeneSet+'</a></td><td>'+d.N_genes+'</td><td>'+d.N_overlap
								+'</td><td>'+Number(Number(d.p).toPrecision(3)).toExponential(2)+'</td><td>'+Number(Number(d.adjP).toPrecision(3)).toExponential(2)+'</td><td>'+d.genes.split(":").join(", ")+'</td></tr>';
						});
					}

					table += '</table>'
					$('#'+category[i]+"Table").html(table);
				}
			}
		}
	});
}

function GSImgDown(name, type){
	$('#GSData').val($('#'+name).html());
	$('#GSType').val(type);
	$('#GSID').val(gwasID);
	$('#GSFileName').val(name);
	$('#GSDir').val("gwas");
	$('#GSSubmit').trigger('click');
}

function GeneSetPlot(category){
	$('#'+category+'Plot').show();
	$('#'+category+'Table').hide();
}

function GeneSetTable(category){
	$('#'+category+'Plot').hide();
	$('#'+category+'Table').show();
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

function DownloadFiles(){
	var paramfile = document.getElementById('paramfile').checked;
	var leadfile = document.getElementById('leadfile').checked;
	var locifile = document.getElementById('locifile').checked;
	var snpsfile = document.getElementById('snpsfile').checked;
	var annovfile = document.getElementById('annovfile').checked;
	var annotfile = document.getElementById('annotfile').checked;
	var genefile = document.getElementById('genefile').checked;
	var eqtlfile = document.getElementById('eqtlfile').checked;
	var cifile = document.getElementById('cifile').checked;
	// var exacfile = document.getElementById('exacfile').checked;
	var gwascatfile = document.getElementById('gwascatfile').checked;
	var magmafile = document.getElementById('magmafile').checked;

	if(paramfile || leadfile || locifile || snpsfile || annovfile || annotfile || genefile || eqtlfile || cifile || gwascatfile || magmafile){
		document.getElementById('download').disabled=false;
	}
}

function ImgDown(name, type){
	$('#'+name+'Data').val($('#'+name).html());
	$('#'+name+'Type').val(type);
	$('#'+name+'ID').val(gwasID);
	$('#'+name+'FileName').val(name);
	$('#'+name+'Dir').val("gwas");
	$('#'+name+'Submit').trigger('click');
}

function circosDown(type){
	$('#circosPlotID').val(gwasID);
	$('#circosPlotDir').val(prefix);
	$('#circosPlotType').val(type);
	$('#circosPlotSubmit').trigger('click');
}
