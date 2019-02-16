function loadResults(id){
	// Check files
	$.ajax({
		url: subdir+'/'+page+'/checkFileList',
		type: 'POST',
		data: {
			id: id
		},
		error: function(){alert("getFileList error")},
		success: function(data){
			data = JSON.parse(data);
			if(data[0]==0){$('#step1_file').prop('checked', false); $('#step1_file').prop('disabled', true);}
			if(data[1]==0){$('#step1_2_file').prop('checked', false); $('#step1_2_file').prop('disabled', true);}
			if(data[2]==0){$('#step2_file').prop('checked', false); $('#step2_file').prop('disabled', true);}
			if(data[3]==0){$('#step3_file').prop('checked', false); $('#step3_file').prop('disabled', true);}
		}
	});

	// Get list of datasets
	$.ajax({
		url: subdir+'/'+page+'/getDataList',
		type: 'POST',
		data: {
			id: id
		},
		error: function(){alert("getDataList error")},
		success: function(data){
			data = JSON.parse(data);
			i = 0;
			data.forEach(function(d){
				if(i==0){
					$('#dataset_select').append('<option value="'+d+'" selected>'+d+'</option>');
					i += 1;
				}else{
					$('#dataset_select').append('<option value="'+d+'">'+d+'</option>');
				}
			})
		}, complete: function(){
			updatePerDatasetPlot();
		}
	});

	// Get plot data for step 1-3
	$.ajax({
		url: subdir+'/'+page+'/getStepPlotData',
		type: 'POST',
		data: {
			id: id
		},
		error: function(){alert("getStepPlotData error")},
		success: function(data){
			data = JSON.parse(data);
			PlotStep1(data.step1);
			PlotStep2(data.step2);
			PlotStep3(data.step3, data.step2);
		}
	});

}

function updatePerDatasetPlot(){
	ds = $('#dataset_select').val();
	$('#perDatasetPlot').html('<center><i class="fa fa-spinner fa-spin fa-5x"></i></center>')
	$.ajax({
		url: subdir+'/'+page+'/getPerDatasetData',
		type: 'POST',
		data: {
			id: id,
			ds: ds
		},
		error: function(){alert("getPlotData error")},
		success: function(data){
			data = JSON.parse(data);
			PlotPerDataset(data);
		}
	});
}

function PlotPerDataset(data){
	var bars = [];
	var xLabels = [];
	var cellwidths = []
	var margin = {top:30, right: 30, bottom:100, left:80},
		height = 150;
	var order_i = 5;
	if($('#celltype_order_panel1').val()=="alph"){order_i = 4;}

	if(data.length==0){
		$('#perDatasetPlot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
		+' No data found for the selected dataset.</span><br/></div>');
	}else{
		$('#perDatasetPlot').html("")
		var max_label = 0;
		data.forEach(function(d){
			d[1] = +d[1]; //P
			d[2] = +d[2]; //adjP per dataset
			d[3] = +d[3]; //adjP across dataset
			d[4] = +d[4]; //alph order
			d[5] = +d[5]; //P order
			if(d[0].length>max_label){max_label = d[0].length;}
		})
		margin.bottom = Math.max(max_label*6.5, 100);
		var cellwidth = 1000/data.length;
		if(cellwidth>15){cellwidth=15;}
		else if(cellwidth<4){cellwidth=4;}
		xLabelSize = "11px";
		if(cellwidth<11){
			xLabelSize = parseInt(cellwidth)+"px";
			margin.bottom = Math.max(max_label*cellwidth*0.5, 100);
		}
		cellwidths.push(cellwidth);
		var width = cellwidth*data.length;
		var svg = d3.select("#perDatasetPlot").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");

		var x = d3.scale.ordinal().rangeBands([0,width]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain(data.map(function(d){return d[0];}));
		var y = d3.scale.linear().range([height, 0]);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		y.domain([0, d3.max(data, function(d){return -Math.log10(d[1]);})]);

		var bar = svg.selectAll("rect.expgeneral").data(data).enter()
			.append("rect")
			.attr("x", function(d){return d[order_i]*cellwidth})
			.attr("y", function(d){return y(-Math.log10(d[1]));})
			.attr("width", cellwidth-1)
			.attr("height", function(d){return height - y(-Math.log10(d[1]));})
			.style("fill", function(d){
				var col = $('#test_correct_panel1').val();
				if(col=="pd"){
					if(d[2] < 0.05){return "#c00";}
					else{return "#5668f4";}
				}else if(col=="ad"){
					if(d[3] < 0.05){return "#c00";}
					else{return "#5668f4";}
				}else{
					if(d[3] < 0.05){return "#c00";}
					else if(d[2] < 0.05){return "#ffc300"}
					else{return "#5668f4";}
				}

			})
			.style("stroke", "grey")
			.attr("stroke-width", 0.2);
		var xLabel = svg.append("g").selectAll(".xLabel")
			.data(data).enter().append("text")
			.text(function(d){return d[0];})
			.style("text-anchor", "end")
			.style("font-size", xLabelSize)
			.attr("transform", function(d){
				return "translate("+(d[order_i]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
			});
		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+(height)+")")
			.call(xAxis).selectAll('text').remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-margin.left/3-15)+","+height/2+")rotate(-90)")
			.text("-log 10 P-value");
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');

		// Cell type sort
		function sortOptions1(type){
			if(type=="alph"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[4]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[4]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}else if(type=="p"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[5]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[5]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}
		}
		d3.select('#celltype_order_panel1').on("change", function(){
			sortOptions1($('#celltype_order_panel1').val());
		});

		// color optionxform
		function colorOptions1(type){
			if(type=="pd"){
				bar.transition().duration(1000)
				.style("fill", function(d){
					if(d[2] < 0.05){return "#c00";}
					else{return "#5668f4";}
				});
			}else if(type=="ad"){
				bar.transition().duration(1000)
					.style("fill", function(d){
						if(d[3] < 0.05){return "#c00";}
						else{return "#5668f4";}
					});
			}else if(type=="both"){
				bar.transition().duration(1000)
					.style("fill", function(d){
						if(d[3] < 0.05){return "#c00";}
						else if(d[2] < 0.05){return "#ffc300"}
						else{return "#5668f4";}
					});
			}
		}
		d3.select('#test_correct_panel1').on("change", function(){
			colorOptions1($('#test_correct_panel1').val());
		});
	}
}

function PlotStep1(data){
	var bars = [];
	var xLabels = [];
	var cellwidths = []
	var margin = {top:30, right: 250, bottom:100, left:80},
		height = 150;
	var order_i = 5;
	if($('#celltype_order_panel2').val()=="p"){order_i = 4;}

	if(data.length==0){
		$('#step1Plot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
		+' Either there was no significant cell type or no data was available.</span><br/></div>');
	}else{
		$('#step1Plot').html("")
		var max_label = 0;
		var max_ds = 0;
		data.forEach(function(d){
			d[2] = +d[2]; //P
			d[3] = +d[3]; //step3 retained
			d[4] = +d[4]; //p-value order
			d[5] = +d[5]; //p-value pre dataset order
			if(d[1].length>max_label){max_label = d[1].length;}
			if(d[0].length>max_ds){max_ds = d[0].length;}
		})
		margin.bottom = Math.max(max_label*6.5, 100);
		margin.right = Math.max(max_ds*6.5, 250);
		var ds = d3.set(data.map(function(d){return d[0];})).values();
		var cellwidth = 1000/data.length;
		if(cellwidth>15){cellwidth=15;}
		else if(cellwidth<4){cellwidth=4;}
		xLabelSize = "11px";
		if(cellwidth<11){
			xLabelSize = parseInt(cellwidth)+"px";
			margin.bottom = Math.max(max_label*cellwidth*0.5, 100);
		}
		cellwidths.push(cellwidth);
		var width = cellwidth*data.length;
		var svg = d3.select("#step1Plot").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");

		var x = d3.scale.ordinal().rangeBands([0,width]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain(data.map(function(d){return d[1];}));
		var y = d3.scale.linear().range([height, 0]);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		y.domain([0, d3.max(data, function(d){return -Math.log10(d[2]);})]);

		// legend
		var cur_height = 5;
		ds.forEach(function(d){
			svg.append('rect')
				.attr("x", width+20).attr("y", cur_height)
				.attr("height", 10).attr("width", 10)
				.style("fill", d3.hsl(ds.indexOf(d)*360/ds.length,1,.5))
				.style("stroke", "grey")
				.attr("stroke-width", 0.2);
			svg.append("text")
				.attr("x", width+32)
				.attr("y", cur_height+9)
				.text(d)
				.attr('text-anchor', 'start')
				.style("font-size", "11px");
			cur_height += 15;
		})

		var bar = svg.selectAll("rect.expgeneral").data(data).enter()
			.append("rect")
			.attr("x", function(d){return d[order_i]*cellwidth})
			.attr("y", function(d){return y(-Math.log10(d[2]));})
			.attr("width", cellwidth-1)
			.attr("height", function(d){return height - y(-Math.log10(d[2]));})
			.style("fill", function(d){
				return(d3.hsl(ds.indexOf(d[0])*360/ds.length,1,.5))
			})
			.style("stroke", "grey")
			.attr("stroke-width", 0.2);
		var xLabel = svg.append("g").selectAll(".xLabel")
			.data(data).enter().append("text")
			.text(function(d){return d[1];})
			.style("text-anchor", "end")
			.style("font-size", xLabelSize)
			.attr("transform", function(d){
				return "translate("+(d[order_i]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
			});
		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+(height)+")")
			.call(xAxis).selectAll('text').remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-margin.left/3-15)+","+height/2+")rotate(-90)")
			.text("-log 10 P-value");
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');

		// Cell type sort
		function sortOptions2(type){
			if(type=="p"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[4]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[4]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}else if(type=="dp"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[5]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[5]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}
		}
		d3.select('#celltype_order_panel2').on("change", function(){
			sortOptions2($('#celltype_order_panel2').val());
		});
	}
}

function PlotStep2(data){
	var bars = [];
	var xLabels = [];
	var cellwidths = []
	var margin = {top:30, right: 250, bottom:100, left:80},
		height = 150;
	var order_i = 5;
	if($('#celltype_order_panel3').val()=="p"){order_i = 4;}

	if(data.length==0){
		$('#step2Plot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
		+' Either there was no significant cell type or step 2 was not performed.</span><br/></div>');
	}else{
		$('#step2Plot').html("")
		var max_label = 0;
		var max_ds = 0;
		data.forEach(function(d){
			d[2] = +d[2]; //P
			d[3] = +d[3]; //step3 retained
			d[4] = +d[4]; //p-value order
			d[5] = +d[5]; //p-value pre dataset order
			if(d[1].length>max_label){max_label = d[1].length;}
			if(d[0].length>max_ds){max_ds = d[0].length;}
		})
		margin.bottom = Math.max(max_label*6.5, 100);
		margin.right = Math.max(max_ds*6.5, 250);
		var ds = d3.set(data.map(function(d){return d[0];})).values();
		var cellwidth = 1000/data.length;
		if(cellwidth>15){cellwidth=15;}
		else if(cellwidth<4){cellwidth=4;}
		xLabelSize = "11px";
		if(cellwidth<11){
			xLabelSize = parseInt(cellwidth)+"px";
			margin.bottom = Math.max(max_label*cellwidth*0.5, 100);
		}
		cellwidths.push(cellwidth);
		var width = cellwidth*data.length;
		var svg = d3.select("#step2Plot").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");

		var x = d3.scale.ordinal().rangeBands([0,width]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain(data.map(function(d){return d[1];}));
		var y = d3.scale.linear().range([height, 0]);
		var yAxis = d3.svg.axis().scale(y).orient("left");
		y.domain([0, d3.max(data, function(d){return -Math.log10(d[2]);})]);

		// legend
		var cur_height = 5;
		ds.forEach(function(d){
			svg.append('rect')
				.attr("x", width+20).attr("y", cur_height)
				.attr("height", 10).attr("width", 10)
				.style("fill", d3.hsl(ds.indexOf(d)*360/ds.length,1,.5))
				.style("stroke", "grey")
				.attr("stroke-width", 0.2);
			svg.append("text")
				.attr("x", width+32)
				.attr("y", cur_height+9)
				.text(d)
				.attr('text-anchor', 'start')
				.style("font-size", "11px");
			cur_height += 15;
		})

		var bar = svg.selectAll("rect.expgeneral").data(data).enter()
			.append("rect")
			.attr("x", function(d){return d[order_i]*cellwidth})
			.attr("y", function(d){return y(-Math.log10(d[2]));})
			.attr("width", cellwidth-1)
			.attr("height", function(d){return height - y(-Math.log10(d[2]));})
			.style("fill", function(d){
				return(d3.hsl(ds.indexOf(d[0])*360/ds.length,1,.5))
			})
			.style("stroke", "grey")
			.attr("stroke-width", 0.2);
		var xLabel = svg.append("g").selectAll(".xLabel")
			.data(data).enter().append("text")
			.text(function(d){return d[1];})
			.style("text-anchor", "end")
			.style("font-size", xLabelSize)
			.attr("transform", function(d){
				return "translate("+(d[order_i]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
			});
		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+(height)+")")
			.call(xAxis).selectAll('text').remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-margin.left/3-15)+","+height/2+")rotate(-90)")
			.text("-log 10 P-value");
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');

		// Cell type sort
		function sortOptions3(type){
			if(type=="p"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[4]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[4]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}else if(type=="dp"){
				bar.transition().duration(1000)
					.attr("x", function(d){return d[5]*cellwidth;});
				xLabel.transition().duration(1000)
					.attr("transform", function(d){
						return "translate("+(d[5]*cellwidth+((cellwidth-1)/2)+3)+","+(height+8)+")rotate(-70)";
					});
			}
		}
		d3.select('#celltype_order_panel3').on("change", function(){
			sortOptions3($('#celltype_order_panel3').val());
		});
	}
}

function PlotStep3(data, step2){
	var margin = {top:30, right: 250, bottom:100, left:100};
	var order_i = 5;
	if($('#celltype_order_panel4').val()=="p"){order_i = 4;}

	if(data.length==0){
		$('#step3Plot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
		+' Either there was no significant cell type or step 3 was not performed.</span><br/></div>');
	}else{
		$('#step3Plot').html("")
		var max_label = 0;
		var max_ds = 0;
		data.forEach(function(d){
			d[3] = +d[3]; // PS
		})
		step2.forEach(function(d){
			d[2] = +d[2]; //P
			d[3] = +d[3]; //step3 retained
			d[4] = +d[4]; //p-value order
			d[5] = +d[5]; //p-value pre dataset order
			if(d[1].length>max_label){max_label = d[1].length;}
			if(d[0].length>max_ds){max_ds = d[0].length;}
		})
		margin.bottom = Math.max(max_label*6.5, 100);
		margin.left = Math.max(max_label*8, 100);
		margin.right = Math.max(max_ds*6.5, 250);
		var ds = d3.set(step2.map(function(d){return d[0];})).values();
		var ct = d3.set(step2.map(function(d){return d[1];})).values();
		var cellsize = 1000/step2.length;
		if(cellsize>20){cellsize=20;}
		else if(cellsize<4){cellsize=4;}
		xLabelSize = "11px";
		if(cellsize<11){
			xLabelSize = parseInt(cellsize)+"px";
			margin.bottom = Math.max(max_label*cellsize*0.5, 100);
			margin.left = Math.max(max_label*cellsize*0.8, 100);
		}

		var height = cellsize*step2.length+80;
		var width = cellsize*step2.length;
		var svg = d3.select("#step3Plot").append("svg")
				.attr("width", width+margin.left+margin.right)
				.attr("height", height+margin.top+margin.bottom)
				.append("g")
				.attr("transform", "translate("+margin.left+","+margin.top+")");
		var colorScale = d3.scale.linear().domain([0, 0.5, 1]).range(["#000099", "#fff", "#b30000"]);
		var bar_height = 75;
		var heatmap_top = 80;

		// legend
		var cur_height = 5;
		ds.forEach(function(d){
			svg.append('rect')
				.attr("x", width+20).attr("y", cur_height)
				.attr("height", 10).attr("width", 10)
				.style("fill", d3.hsl(ds.indexOf(d)*360/ds.length,1,.5))
				.style("stroke", "grey")
				.attr("stroke-width", 0.2);
			svg.append("text")
				.attr("x", width+32)
				.attr("y", cur_height+9)
				.text(d)
				.attr('text-anchor', 'start')
				.style("font-size", "11px");
			cur_height += 15;
		})

		cur_height += 15;
		if(cur_height < heatmap_top){cur_height = heatmap_top}
		var t = [];
		for(var i=0; i<26; i++){t.push(i);}
		var legendRect = svg.selectAll(".legend").data(t).enter().append("g")
			.append("rect")
			.attr("class", 'legendRect')
			.attr("x", width+20)
			.attr("y", function(d){return (25-d)*2+cur_height})
			.attr("width", 12)
			.attr("height", 2)
			.attr("fill", function(d){return colorScale(d*0.04)});
		var legendText = svg.selectAll("text.legend").data([0,12.5,25]).enter().append("g")
			.append("text")
			.attr("text-anchor", "start")
			.attr("class", "legenedText")
			.attr("x", width+35)
			.attr("y", function(d){return (25-d)*2+5+cur_height})
			.text(function(d){return d*0.04})
			.style("font-size", "11px");

		svg.append('text')
			.attr("x", width+20).attr("y", cur_height+70)
			.text("* Colinear")
			.style("font-size", "11px");
		svg.append('text')
			.attr("x", width+20).attr("y", cur_height+83)
			.text("** PS>1")
			.style("font-size", "11px");

		// bar plot
		var x = d3.scale.ordinal().rangeBands([0,width]);
		var xAxis = d3.svg.axis().scale(x).orient("bottom");
		x.domain(step2.map(function(d){return d[1];}));
		var y = d3.scale.linear().range([bar_height, 0]);
		var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
		y.domain([0, d3.max(step2, function(d){return -Math.log10(d[2]);})]);
		var bar = svg.selectAll("rect.expgeneral").data(step2).enter()
			.append("rect")
			.attr("x", function(d){return d[order_i]*cellsize})
			.attr("y", function(d){return y(-Math.log10(d[2]));})
			.attr("width", cellsize-1)
			.attr("height", function(d){return bar_height - y(-Math.log10(d[2]));})
			.style("fill", function(d){
				return(d3.hsl(ds.indexOf(d[0])*360/ds.length,1,.5))
			})
			.style("stroke", "grey")
			.attr("stroke-width", 0.2);
		svg.append('g').attr("class", "y axis")
			.call(yAxis)
			.selectAll('text').style('font-size', '10px').style('font-family', 'sans-serif');
		svg.append('g').attr("class", "x axis")
			.attr("transform", "translate(0,"+(bar_height)+")")
			.call(xAxis).selectAll('text').remove();
		svg.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate("+(-35)+","+bar_height/2+")rotate(-90)")
			.text("-log 10 P-value")
			.style('font-size', '10px');

		// x label
		var colLabels = svg.append("g").selectAll(".colLabel")
			.data(step2).enter().append("text")
			.text(function(d){return d[1];})
			.style("text-anchor", "end")
			.style("font-size", xLabelSize)
			.attr("transform", function(d){
				return "translate("+(d[order_i]*cellsize+((cellsize-1)/2)+3)+","+(height+4)+")rotate(-70)";
			});
		// y label
		var rowLabels = svg.append("g").selectAll(".rowLabel")
			.data(step2).enter().append("text")
			.text(function(d){return d[1];})
			.attr("x", -3)
			.attr("y", function(d){return d[order_i]*cellsize+((cellsize-1)/2)+3+heatmap_top})
			.style("text-anchor", "end")
			.style("font-size", xLabelSize);
		// heatmap
		var heatmap = svg.append("g").attr("class", "cell")
			.selectAll("rect.cell").data(data).enter()
			.append("rect")
			.attr("width", cellsize).attr("height", cellsize)
			.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize})
			.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+heatmap_top})
			.style("fill", function(d){
				if(d[3]<0){return "grey"}
				else if(d[3]>1){return colorScale(1)}
				else{return colorScale(d[3])}
			});
		// colinear
		var star1 = svg.append("g").attr("class", "star1")
			.selectAll("star1").data(data.filter(function(d){if(d[3]<0){return d}})).enter()
			.append("text")
			.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize+cellsize*0.35})
			.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+cellsize*0.8+heatmap_top})
			.text("*")
			.attr('dy', function(){if(cellsize==20){return ".1em"}else{return ".2em"}});
		//PS>1
		var star2 = svg.append("g").attr("class", "star2")
			.selectAll("star2").data(data.filter(function(d){if(d[3]>1){return d}})).enter()
			.append("text")
			.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize+cellsize*0.35})
			.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+cellsize*0.8+heatmap_top})
			.text("**")
			.attr('dy', function(){if(cellsize==20){return ".1em"}else{return ".2em"}});
		// diagonal
		svg.append("g").attr("class", "diag_cell")
			.selectAll("rect.cell").data(step2).enter()
			.append("rect")
			.attr("width", cellsize).attr("height", cellsize)
			.attr("x", function(d){return d[order_i]*cellsize})
			.attr("y", function(d){return d[order_i]*cellsize+heatmap_top})
			.style("fill", "grey");

		// Cell type sort
		function sortOptions4(type){
			if(type=="p"){
				order_i = 4;
			}else if(type=="dp"){
				order_i = 5;
			}
			bar.transition().duration(1000)
				.attr("x", function(d){return d[order_i]*cellsize});
			colLabels.transition().duration(1000)
				.attr("transform", function(d){
					return "translate("+(d[order_i]*cellsize+((cellsize-1)/2)+3)+","+(height+4)+")rotate(-70)";
				});
			rowLabels.transition().duration(1000)
				.attr("y", function(d){return d[order_i]*cellsize+((cellsize-1)/2)+3+heatmap_top})
			heatmap.transition().duration(1000)
				.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize})
				.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+heatmap_top});
			star1.transition().duration(1000)
				.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize+cellsize*0.35})
				.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+cellsize*0.8+heatmap_top});
			star2.transition().duration(1000)
				.attr("x", function(d){return step2[ct.indexOf(d[2])][order_i]*cellsize+cellsize*0.35})
				.attr("y", function(d){return step2[ct.indexOf(d[1])][order_i]*cellsize+cellsize*0.8+heatmap_top});
		}
		d3.select('#celltype_order_panel4').on("change", function(){
			sortOptions4($('#celltype_order_panel4').val());
		});
	}

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

function ImgDownDS(plot, type){
	$('#celltypeData').val($('#'+plot).html());
	$('#celltypeType').val(type);
	$('#celltypeID').val(id);
	$('#celltypeFileName').val($('#dataset_select').val());
	$('#celltypeDir').val(prefix);
	$('#celltypeSubmit').trigger('click');
}
