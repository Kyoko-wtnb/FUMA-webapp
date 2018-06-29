function loadResults(id){
	// File fownload
	$.ajax({
		url: subdir+'/'+page+'/getFileList',
		type: 'POST',
		data: {
			id: id
		},
		error: function(){alert("getFileList error")},
		success: function(data){
			data = JSON.parse(data);
			data.forEach(function(d){
				$('#downFileCheck').append('<span class="form-inline">'
				+'<input checked type="checkbox" value="'+d+'" name="files[]" checked onchange="DownloadFiles();">: '
				+d+'</span><br/>')
			});
			$('#downFileCheck').append('<br/>')
		}
	});
	// plot
	$.ajax({
		url: subdir+'/'+page+'/getPlotData',
		type: 'POST',
		data: {
			id: id
		},
		error: function(){alert("getPlotData error")},
		success: function(data){
			data = JSON.parse(data);
			Plot(data);
		}
	});
}

function Plot(data){
	var bars = [];
	var xLabels = [];
	var cellwidths = []
	var margin = {top:30, right: 30, bottom:100, left:80},
		height = 150;

	if(Object.keys(data.long).length>0){
		var cur_row;
		for(var i = 0; i < Object.keys(data.long).length; i++){
			$('#cellPlotPanel').append('<div class="row"><div class="col-md-12 col-sm-12 col-xs-12" id="long_row'+i+'"></div></div>')
			cur_row = $('#long_row'+i)
			cur_row.html('<div class="panel panel-default"><div class="panel-heading">'
			+'<h4 class="panel-title">'+data.long[i]["name"]+'</h4></div>'
			+'<div class="panel-body" style="overflow-x:auto;"><div id="long_button'+i+'"></div><div id="long_plot'+i+'"></div></div></div>')

			// img download buttons
			$('#long_button'+i).append('Download the plot as '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("long_plot'+i+'","'+data.short[i].name+'","png");'+"'"+'>PNG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("long_plot'+i+'","'+data.short[i].name+'","jpeg");'+"'"+'>JPG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("long_plot'+i+'","'+data.short[i].name+'","svg");'+"'"+'>SVG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("long_plot'+i+'","'+data.short[i].name+'","pdf");'+"'"+'>PDF</button>'
			);
			tdata = data.long[i]["data"]
			var max_label = 0;
			tdata.forEach(function(d){
				d[1] = +d[1];
				if(d[0].length>max_label){max_label = d[0].length;}
			})
			margin.bottom = Math.max(max_label*5.5, 100);
			var cellwidth = 1000/tdata.length;
			if(cellwidth>15){cellwidth=15;}
			else if(cellwidth<8){cellwidth=8;}
			xLabelSize = "11px";
			if(cellwidth<11){
				xLabelSize = parseInt(cellwidth)+"px";
				margin.bottom = Math.max(max_label*cellwidth*0.5, 100);
			}
			cellwidths.push(cellwidth);
			var width = cellwidth*tdata.length;
			var svg = d3.select("#long_plot"+i).append("svg")
					.attr("width", width+margin.left+margin.right)
					.attr("height", height+margin.top+margin.bottom)
					.append("g")
					.attr("transform", "translate("+margin.left+","+margin.top+")");

			var x = d3.scale.ordinal().rangeBands([0,width]);
			var xAxis = d3.svg.axis().scale(x).orient("bottom");
			x.domain(tdata.map(function(d){return d[0];}));
			var y = d3.scale.linear().range([height, 0]);
			var yAxis = d3.svg.axis().scale(y).orient("left");
			y.domain([0, d3.max(tdata, function(d){return -Math.log10(d[1]);})]);

			var Pbon = 0.05/tdata.length;

			var bar = svg.selectAll("rect.expgeneral").data(tdata).enter()
				.append("rect")
				.attr("x", function(d){return d[3]*cellwidth})
				.attr("y", function(d){return y(-Math.log10(d[1]));})
				.attr("width", cellwidth-1)
				.attr("height", function(d){return height - y(-Math.log10(d[1]));})
				.style("fill", function(d){
					if(d[1] < Pbon){return "#c00";}
					else{return "#5668f4";}
				})
				.style("stroke", "grey");
			bars.push(bar);
			var xLabel = svg.append("g").selectAll(".xLabel")
				.data(tdata).enter().append("text")
				.text(function(d){return d[0];})
				.style("text-anchor", "end")
				.style("font-size", xLabelSize)
				.attr("transform", function(d){
					return "translate("+(d[3]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
				});
			xLabels.push(xLabel);
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
				.attr("transform", "translate("+(-margin.left/2-15)+","+height/2+")rotate(-90)")
				.text("-log 10 P-value");
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style('font-family', 'sans-serif');
		}
	}
	if(Object.keys(data.short).length>0){
		var idx = 0;
		var cur_row;
		var max_label=0;
		for(var i = 0; i < Object.keys(data.short).length; i++){
			data.short[i]["data"].forEach(function(d){
				if(d[0].length>max_label){max_label = d[0].length;}
			})
		}
		margin.bottom = Math.max(max_label*5.5, 100);
		for(var i = 0; i < Object.keys(data.short).length; i++){
			if(i%2 == 0){
				idx += 1;
				$('#cellPlotPanel').append('<div class="row" id="short_row'+idx+'"></div>')
				cur_row = $('#short_row'+idx)
			}
			cur_row.append('<div class="col-md-6 col-sm-6 col-xs-6" id="short_panel'+i+'"></div>')
			var cur_panel = $('#short_panel'+i)
			cur_panel.html('<div class="panel panel-default"><div class="panel-heading">'
			+'<h4 class="panel-title">'+data.short[i]["name"]+'</h4></div>'
			+'<div class="panel-body" style="overflow-x:auto;"><div id="short_button'+i+'"></div><div id="short_plot'+i+'"></div></div></div>')

			tdata = data.short[i]["data"]
			tdata.forEach(function(d){
				d[1] = +d[1];
			})

			// img download buttons
			$('#short_button'+i).append('Download the plot as '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("short_plot'+i+'","'+data.short[i].name+'","png");'+"'"+'>PNG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("short_plot'+i+'","'+data.short[i].name+'","jpeg");'+"'"+'>JPG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("short_plot'+i+'","'+data.short[i].name+'","svg");'+"'"+'>SVG</button> '
				+'<button class="btn btn-default btn-xs ImgDown" onclick='+"'"+'ImgDown("short_plot'+i+'","'+data.short[i].name+'","pdf");'+"'"+'>PDF</button>'
			);

			var cellwidth = 15;
			cellwidths.push(cellwidth);
			var width = cellwidth*tdata.length;
			var svg = d3.select("#short_plot"+i).append("svg")
					.attr("width", width+margin.left+margin.right)
					.attr("height", height+margin.top+margin.bottom)
					.append("g")
					.attr("transform", "translate("+margin.left+","+margin.top+")");

			var x = d3.scale.ordinal().rangeBands([0,width]);
			var xAxis = d3.svg.axis().scale(x).orient("bottom");
			x.domain(tdata.map(function(d){return d[0];}));
			var y = d3.scale.linear().range([height, 0]);
			var yAxis = d3.svg.axis().scale(y).orient("left");
			y.domain([0, d3.max(tdata, function(d){return -Math.log10(d[1]);})]);

			var Pbon = 0.05/tdata.length;

			var bar = svg.selectAll("rect.expgeneral").data(tdata).enter()
				.append("rect")
				.attr("x", function(d){return d[3]*cellwidth})
				.attr("y", function(d){return y(-Math.log10(d[1]));})
				.attr("width", cellwidth-1)
				.attr("height", function(d){return height - y(-Math.log10(d[1]));})
				.style("fill", function(d){
					if(d[1] < Pbon){return "#c00";}
					else{return "#5668f4";}
				})
				.style("stroke", "grey");
			bars.push(bar);
			var xLabel = svg.append("g").selectAll(".xLabel")
				.data(tdata).enter().append("text")
				.text(function(d){return d[0];})
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.attr("transform", function(d){
					return "translate("+(d[3]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
				});
			xLabels.push(xLabel);
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
				.attr("transform", "translate("+(-margin.left/2-15)+","+height/2+")rotate(-90)")
				.text("-log 10 P-value");
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style('font-family', 'sans-serif');
		}
	}
	// Cell type sort
	function sortOptions(type){
		if(type=="alph"){
			for(var i=0; i<bars.length; i++){
				bars[i].transition().duration(1000)
					.attr("x", function(d){return d[2]*cellwidths[i];});
				xLabels[i].transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[2]*cellwidths[i]+((cellwidths[i]-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}
		}else if(type=="p"){
			for(var i=0; i<bars.length; i++){
				bars[i].transition().duration(1000)
					.attr("x", function(d){return d[3]*cellwidths[i];});
				xLabels[i].transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[3]*cellwidths[i]+((cellwidths[i]-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}
		}
	}

	d3.select('#celltype_order').on("change", function(){
		sortOptions($('#celltype_order').val());
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

function ImgDown(plot, name, type){
	$('#celltypeData').val($('#'+plot).html());
	$('#celltypeType').val(type);
	$('#celltypeID').val(id);
	$('#celltypeFileName').val(name);
	$('#celltypeDir').val(prefix);
	$('#celltypeSubmit').trigger('click');
}
