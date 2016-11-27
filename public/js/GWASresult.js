var selectTable;
var selectedTable;
var maxSelect = 5;
$(document).ready(function(){
  // $('#Plots').hide();
  selectTable = $('#selectTable').DataTable();
  selectedTable = $('#selectedTable').DataTable();
  SelectOptions("Domain", null, null, null, null);

  $('#selectTable tbody').on('click', 'tr', function(){
    var rowData = selectTable.row(this).data();
    // console.log(rowData);
    var IDs = selectedTable.column(2).data();
    if(IDs.length>=maxSelect){
      alert("Sorry, you can only select up to "+maxSelect+" studies. Please delete one of selected studies to add another study.");
    }else{
      if(IDs.indexOf(rowData['ID'])<0){
        selectedTable.row.add([
          '<button>Delete</button>',
          '<input type="checkbox" class="plot">',
          rowData['ID'],
          rowData['PMID'],
          rowData['Year'],
          rowData['Domain'],
          rowData['ChapterLevel'],
          rowData['SubchapterLevel'],
          rowData['Trait'],
          rowData['Ncase'],
          rowData['Ncontrol'],
          rowData['N'],
          rowData['Population'],
          rowData['SNPh2'],
          rowData['website']
        ]).draw(false);
        showPlots(rowData['ID']);
      }
    }
    // console.log(IDs);
  });

  $('#selectedTable tbody').on('click', 'button', function(){
    selectedTable.row($(this).parents('tr')).remove().draw();
    var rowData = selectedTable.row(this).data();
    var ID = rowData['ID'];
    var margin = {top:30, right: 30, bottom:50, left:50},
        width = 800,
        height = 300;
    var curHeight = $('#ManhattanPanel').height();
    $('#manhattan'+ID+'Panel').remove();
    d3.select('#ManhattanPanel').style("height", curHeight-(height+margin.top+margin.bottom));
  });

});

function Selection(type){
  var domain = $('#Domain').val();
  var chapter = $('#Chapter').val();
  var subchapter = $('#Subchapter').val();
  var trait = $('#Trait').val();
  // $('#test').html(type+":"+domain+":"+chapter+":"+subchapter);
  SelectOptions(type, domain, chapter, subchapter, trait);

}

function SelectOptions(type, domain, chapter, subchapter, trait){
  $.ajax({
    url: "GWASresult/SelectOption",
    type: "POST",
    data: {
      type: type,
      domain: domain,
      chapter: chapter,
      subchapter: subchapter
    },
    processing: true,
    success: function(data){
      if(type=="Domain"){
        data = JSON.parse(data);
        $.each(data, function(key, val){
          var out = '<option value=null>-- Please select '+key+' of interest --</option>';
          $.each(val, function(k, v){
            out += '<option value="'+k+'">'+k+' ('+v+')</option>';
          });
          $('#'+key).html(out).selectpicker('refresh');
        });
      }else if(type=="Chapter"){
        data = JSON.parse(data);
        $.each(data, function(key, val){
          var out = '<option value=null>-- Please select '+key+' of interest --</option>';
          $.each(val, function(k, v){
            out += '<option value="'+k+'">'+k+' ('+v+')</option>';
          });
          $('#'+key).html(out).selectpicker('refresh');
        });
      }else if(type=="Subchapter"){
        data = JSON.parse(data);
        $.each(data, function(key, val){
          var out = '<option value=null>-- Please select '+key+' of interest --</option>';
          $.each(val, function(k, v){
            out += '<option value="'+k+'">'+k+' ('+v+')</option>';
          });
          $('#'+key).html(out).selectpicker('refresh');
        });
      }
    }
  });

  // console.log(typeof chapter)
  // console.log(chapter)

  $('#selectTable').DataTable().destroy();
  selectTable = $('#selectTable').DataTable({
    processing: false,
    serverSide: false,
    select: true,
    "ajax" : {
      url: "GWASresult/selectTable",
      type: "POST",
      data: {
        type: type,
        domain: domain,
        chapter: chapter,
        subchapter: subchapter,
        trait: trait
      }
    },
    error: function(){

    },
    "columns":[
      {"data": "ID", name: "ID"},
      {"data": "PMID", name:"PMID"},
      {"data": "Year", name: "Year"},
      {"data": "Domain", name: "Domain"},
      {"data": "ChapterLevel", name: "Chapter level"},
      {"data": "SubchapterLevel", name: "Subchapter level"},
      {"data": "Trait", name: "Trait"},
      {"data": "Ncase", name: "Case"},
      {"data": "Ncontrol", name: "Control"},
      {"data": "N", name: "N"},
      {"data": "Population", name: "Population"},
      {"data": "SNPh2", name: "SNP h2"},
      {"data": "website", name: "Web site"}
    ],

    // "columnDefs": [{
    //   "targets": -1,
    //   "data": null,
    //   "defaultContent": "<button>Delete</button>"
    // }],
    "lengthMenue": [[5, 10, 25, -1], [5, 10, 25, "All"]],
    "iDisplayLength": 5
  });
}

function GWAStable(dbName){
  $('#gwasTable').DataTable().destroy();
  $('#gwasTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: "GWASresult/gwasDBtable",
      type: 'POST',
      data:{dbName: dbName},
      error: function(){
        alert("GWAStable error");
      }
    },
    columns:[
      {"data": "ID", name: "ID"},
      {"data": "PMID", name:"PMID"},
      {"data": "FirstAuth", name: "FirstAuth"},
      {"data": "Year", name: "Year"},
      {"data": "Domain", name: "Domain"},
      {"data": "ChapterLevel", name: "Chapter level"},
      {"data": "SubchapterLevel", name: "Subchapter level"},
      {"data": "Trait", name: "Trait"},
      {"data": "Nsample", name: "N"},
      {"data": "SNPh2", name: "SNP h2"},
      {"data": "DataInfo", name: "DataInfo"}
    ],
    "lengthMenue": [[5, 10, 25, -1], [5, 10, 25, "All"]],
    "iDisplayLength": 5
    // dom: 'lBfrtip',
    // buttons: ['csv']
  });
}

function showPlots(ID){
  var curHeight = $('#ManhattanPanel').height();
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

  d3.select('#ManhattanPanel').style("height", curHeight+height+margin.top+margin.bottom);
  $('#ManhattanPanel').append('<div id="manhattan'+ID+'Panel" style="position: relative; height:'+(height+margin.top+margin.bottom)+';"></div>');
  $('#manhattan'+ID+"Panel").append('<div id="manhattan'+ID+'" class="canvasarea"></div>')
  var svg = d3.select("#manhattan"+ID).append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvas1 = d3.select('#manhattan'+ID).append("div").attr("class", "canvasarea")
               .style("left", margin.left)
              .style("top", margin.top)
              .append("canvas")
              .attr("class", "canvasarea")
              .attr("width", width)
              .attr("height", height)
              .node().getContext('2d');

  d3.json("GWASresult/manhattan/gwasDB/"+ID+"/manhattan.txt", function(data){
    data.forEach(function(d){
  		// d.chr = +d.chr;
  		// d.bp = +d.bp;
  		// d.p = +d.p;
      // d.y = 0;
      d[0] = +d[0]; //chr
      d[1] = +d[1]; // bp
      d[2] = +d[2]; // p
  	});
    // var chr = d3.set(data.map(function(d){return d.chr;})).values();
    var chr = d3.set(data.map(function(d){return d[0];})).values();

    var max_chr = chr.length;
    var x = d3.scale.linear().range([0, width]);
    x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
    var xAxis = d3.svg.axis().scale(x).orient("bottom");
    var y = d3.scale.linear().range([height, 0]);
    // y.domain([0, d3.max(data, function(d){return -Math.log10(d.p);})+1]);
    y.domain([0, d3.max(data, function(d){return -Math.log10(d[2]);})+1]);

    var yAxis = d3.svg.axis().scale(y).orient("left");

    data.forEach(function(d){
    		// if(d.p<=0.005 || d.bp%200==0){
    			canvas1.beginPath();
    			// canvas1.arc( x(d.bp+chromStart[d.chr-1]), y(-Math.log10(d.p)), 2, 0, 2*Math.PI);
          canvas1.arc( x(d[1]+chromStart[d[0]-1]), y(-Math.log10(d[2])), 2, 0, 2*Math.PI);
    			// if(d.chr%2==0){canvas1.fillStyle="steelblue";}
          if(d[0]%2==0){canvas1.fillStyle="steelblue";}
    			else{canvas1.fillStyle="blue";}
    			canvas1.fill();
    		// }
    	});

    svg.append("line")
  	 .attr("x1", 0).attr("x2", width)
    	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
  	svg.append("g").attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
    svg.append("g").attr("class", "y axis").call(yAxis);

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
  });
}

function PlotManhattan(dbName){
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
  // set size of parent div
  d3.select("#SNPmanPlot").style("width", width+margin.left+margin.right)
    .style("height", height+margin.top+margin.bottom);
  d3.select("#GenemanPlot").style("width", width+margin.left+margin.right)
    .style("height", height+margin.top+margin.bottom);

  // create svg and canvas object
  var svg = d3.select("#SNPsManhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvas1 = d3.select('#SNPsManhattan').append("div").attr("class", "canvasarea")
  // 	           .style("left", margin.left)
  //           	.style("top", margin.top)
  //           	.append("canvas")
  //           	.attr("class", "canvasarea")
  //           	.attr("width", width)
  //           	.attr("height", height)
  //           	.node().getContext('2d');
  var svg2 = d3.select("#GenesManhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvas2 = d3.select('#GenesManhattan').append("div").attr("class", "canvasarea")
  // 	           .style("left", margin.left)
  //           	.style("top", margin.top)
  //           	.append("canvas")
  //           	.attr("class", "canvasarea")
  //           	.attr("width", width)
  //           	.attr("height", height)
  //           	.node().getContext('2d');

  // arrays for chromosome coordinate
  // var chr=[];
  // var chrlen = [];
  // var chrstart = [];
  // var max_chr;
  var x = d3.scale.linear().range([0, width]);

  // plot SNPs manhattan
  d3.json("GWASresult/d3text/"+dbName+"/manhattan.txt", function(data){
    data.forEach(function(d){
  		d.chr = +d.chr;
  		d.bp = +d.bp;
  		d.p = +d.p;
      d.y = 0;
  	});
    var chr = d3.set(data.map(function(d){return d.chr;})).values();
    var max_chr = chr.length;
    var y = d3.scale.linear().range([height, 0]);
  	x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
  	y.domain([0, d3.max(data, function(d){return -Math.log10(d.p);})+1]);
  	var yAxis = d3.svg.axis().scale(y).orient("left");
  	var xAxis = d3.svg.axis().scale(x).orient("bottom");
    var time = d3.scale.linear().range([1000, 3000]).domain([0,d3.max(data, function(d){return -Math.log10(d.p);})]);


    svg.selectAll('.dot').data(data).enter()
      .append("circle")
      .attr("r", 2)
      .attr("cx", function(d){return x(d.bp+chromStart[d.chr-1]);})
      .attr("cy", y(0))
      .attr("fill", function(d){if(d.chr%2==0){return "steelblue";}else{return "blue";}})
      .transition()
        .duration(function(d){return time(-Math.log10(d.p))})
        .attr("cy", function(d){return y(-Math.log10(d.p));});
    // d3.timer(moveCircles);
    // var duration = 2000;
    // var timeScale = d3.scale.linear()
    // 	.domain([0, duration])
    // 	.range([0,1]);
    // var renderTime = 0;
    // function moveCircles(t) {
  	// 	data.forEach(function(d){
  	// 		d.y = (t/duration)*-Math.log10(d.p);
  	// 	});
  	// 	drawCircles();
  	// 	if(t >= duration){
  	// 		console.log('Render time:', renderTime);
  	// 		return true;
  	// 	}
  	// }
    //
    // function drawCircles(point) {
  	// 	var start = new Date();
  	// 	canvas1.clearRect(0, 0, width, height);
  	// 	//fill = point ? "#e4e5e5" : "steelblue";
  	// 	data.forEach(function(d) {
    //     if(d.chr%2==0){canvas1.fillStyle="steelblue";}
    //     else{canvas1.fillStyle="blue";}
  	// 		canvas1.beginPath();
  	// 		canvas1.moveTo(x(d.bp+chromStart[d.chr-1]), y(d.y));
  	// 		canvas1.arc(x(d.bp+chromStart[d.chr-1]), y(d.y), 2, 0, 2 * Math.PI);
  	// 		canvas1.fill();
  	// 	});
  	// 	var end = new Date();
  	// 	renderTime += (end-start);
  	// }

    svg.append("line")
  	 .attr("x1", 0).attr("x2", width)
    	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
  	svg.append("g").attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
    svg.append("g").attr("class", "y axis").transition().duration(3000).call(yAxis);

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
  });

  // plot gene manhattan
  d3.json("GWASresult/d3text/"+dbName+"/magma.genes.out", function(data){
    data.forEach(function(d){
      d.CHR = +d.CHR;
  		d.START = +d.START;
  		d.STOP = +d.STOP;
  		d.P = +d.P;
      d.y = 0;
  	});
    var chr = d3.set(data.map(function(d){return d.CHR;})).values();
    var max_chr = chr.length;
    var y = d3.scale.linear().range([height, 0]);
  	x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
  	y.domain([0, d3.max(data, function(d){return -Math.log10(d.P);})+1]);
  	var yAxis = d3.svg.axis().scale(y).orient("left");
  	var xAxis = d3.svg.axis().scale(x).orient("bottom");
    var time = d3.scale.linear().range([1000, 3000]).domain([0,d3.max(data, function(d){return -Math.log10(d.P);})]);

    svg2.selectAll('.dot').data(data).enter()
      .append("circle")
      .attr("r", 2)
      .attr("cx", function(d){return x((d.START+d.STOP)/2+chromStart[d.CHR-1]);})
      .attr("cy", y(0))
      .attr("fill", function(d){if(d.CHR%2==0){return "steelblue";}else{return "blue";}})
      .transition()
        .duration(function(d){return time(-Math.log10(d.P))})
        .attr("cy", function(d){return y(-Math.log10(d.P));});

      // d3.timer(moveCircles);
      // var duration = 2000;
      // var timeScale = d3.scale.linear()
      // 	.domain([0, duration])
      // 	.range([0,1]);
      // var renderTime = 0;
      // function moveCircles(t) {
    	// 	data.forEach(function(d){
    	// 		d.y = (t/duration)*-Math.log10(d.P);
    	// 	});
    	// 	drawCircles();
    	// 	if(t >= duration){
    	// 		console.log('Render time:', renderTime);
    	// 		return true;
    	// 	}
    	// }
      //
      // function drawCircles(point) {
    	// 	var start = new Date();
    	// 	canvas2.clearRect(0, 0, width, height);
    	// 	//fill = point ? "#e4e5e5" : "steelblue";
    	// 	data.forEach(function(d) {
      //     if(d.CHR%2==0){canvas2.fillStyle="steelblue";}
      //     else{canvas2.fillStyle="blue";}
    	// 		canvas2.beginPath();
    	// 		canvas2.moveTo(x((d.START+d.STOP)/2+chromStart[d.CHR-1]), y(d.y));
    	// 		canvas2.arc(x((d.START+d.STOP)/2+chromStart[d.CHR-1]), y(d.y), 2, 0, 2 * Math.PI);
    	// 		canvas2.fill();
    	// 	});
    	// 	var end = new Date();
    	// 	renderTime += (end-start);
    	// }

    svg2.append("line")
  	 .attr("x1", 0).attr("x2", width)
    	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
  	svg2.append("g").attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
    svg2.append("g").attr("class", "y axis").transition().duration(3000).call(yAxis);

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
  });


  // queue().defer(d3.json, "GWASresult/d3text/"+dbName+"/manhattan.txt")
  // 	.defer(d3.json, "GWASresult/d3text/"+dbName+"/magma.genes.out")
  // 	.awaitAll(function(error, data){
  // 	var data1 = data[0];
  // 	var data2 = data[1];
  //
  // 	data1.forEach(function(d){
  // 		d.chr = +d.chr;
  // 		d.bp = +d.bp;
  // 		d.p = +d.p;
  // 	});
  // 	chr = d3.set(data1.map(function(d){return d.chr;})).values();
  //
  // 	// for(var i=0; i<chr.length; i++){
  // 	// 	chrlen[i] = d3.max(data1, function(d){if(d.chr==chr[i]){return d.bp;}});
  // 	// 	if(i==0){chrstart[i]=0;}
  // 	// 	else{chrstart[i]=chrlen[i-1]+chrstart[i-1];}
  // 	// }
  // 	max_chr = chr.length;
  // 	var y = d3.scale.linear().range([height, 0]);
  // 	x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
  // 	y.domain([0, d3.max(data1, function(d){return -Math.log10(d.p);})+1]);
  // 	var yAxis = d3.svg.axis().scale(y).orient("left");
  // 	var xAxis = d3.svg.axis().scale(x).orient("bottom");
  //
  // 	data1.forEach(function(d){
  // 		// if(d.p<=0.005 || d.bp%200==0){
  // 			canvas1.beginPath();
  // 			canvas1.arc( x(d.bp+chromStart[d.chr-1]), y(-Math.log10(d.p)), 2, 0, 2*Math.PI);
  // 			if(d.chr%2==0){canvas1.fillStyle="steelblue";}
  // 			else{canvas1.fillStyle="blue";}
  // 			canvas1.fill();
  // 		// }
  // 	});
  //
  // 	svg.append("line")
  // 	 .attr("x1", 0).attr("x2", width)
  //   	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
  //   	.style("stroke", "red")
  //   	.style("stroke-dasharray", ("3,3"));
  // 	svg.append("g").attr("class", "x axis")
  //     .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
  //   svg.append("g").attr("class", "y axis").call(yAxis);
  //
  // 	//Chr label
  // 	for(var i=0; i<chr.length; i++){
  // 		svg.append("text").attr("text-anchor", "middle")
  // 		.attr("transform", "translate("+x((chromStart[i]*2+chromSize[i])/2)+","+(height+20)+")")
  // 		.text(chr[i])
  //     .style("font-size", "10px");
  // 	}
  // 	svg.append("text").attr("text-anchor", "middle")
  // 	 .attr("transform", "translate("+width/2+","+(height+35)+")")
  // 	  .text("Chromosome");
  //   svg.append("text").attr("text-anchor", "middle")
  //     .attr("transform", "translate("+(-35)+","+(height/2)+")rotate(-90)")
  //     .text("-log10 P-value");
  //
  // 	data2.forEach(function(d){
  // 		d.CHR = +d.CHR;
  // 		d.START = +d.START;
  // 		d.STOP = +d.STOP;
  // 		d.P = +d.P;
  // 	});
  // 	var y2 = d3.scale.linear().range([height, 0]);
  // 	x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
  // 	y2.domain([0, d3.max(data2, function(d){return -Math.log10(d.P);})+1]);
  // 	var yAxis2 = d3.svg.axis().scale(y2).orient("left");
  // 	var xAxis = d3.svg.axis().scale(x).orient("bottom");
  //
  //   data2.forEach(function(d){
  // 		canvas2.beginPath();
  // 		canvas2.arc( x((d.START+d.STOP)/2+chromStart[d.CHR-1]), y2(-Math.log10(d.P)), 2, 0, 2*Math.PI);
  // 		if(d.CHR%2==0){canvas2.fillStyle="steelblue";}
  // 		else{canvas2.fillStyle="blue";}
  // 		canvas2.fill();
  // 	});
  //
  // 	svg2.append("line")
  //   	.attr("x1", 0).attr("x2", width)
  //   	.attr("y1", y2(-Math.log10(2.5e-6))).attr("y2", y2(-Math.log10(2.5e-6)))
  //   	.style("stroke", "red")
  //   	.style("stroke-dasharray", ("3,3"));
  // 	svg2.append("g").attr("class", "x axis")
  //     .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
  //   svg2.append("g").attr("class", "y axis").call(yAxis2);
  //
  // 	//Chr label
  // 	for(var i=0; i<chr.length; i++){
  // 		svg2.append("text").attr("text-anchor", "middle")
  // 		  .attr("transform", "translate("+x((chromStart[i]*2+chromSize[i])/2)+","+(height+20)+")")
  // 		  .text(chr[i])
  //       .style("font-size", "10px");
  // 	}
  // 	svg2.append("text").attr("text-anchor", "middle")
  // 	 .attr("transform", "translate("+width/2+","+(height+35)+")")
  // 	  .text("Chromosome");
  //   svg2.append("text").attr("text-anchor", "middle")
  //     .attr("transform", "translate("+(-35)+","+(height/2)+")rotate(-90)")
  //     .text("-log10 P-value");
  // });
}

function PlotQQ(dbName){
  var margin = {top:30, right: 30, bottom:50, left:50},
      width = 300,
      height = 300;

  // parent div
  d3.select("#QQplot").style("height", height+margin.top+margin.bottom);

  // create svg and canvas objects
  var qqSNP = d3.select("#QQSNPs").append("svg")
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g")
              .attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvasSNP = d3.select('#QQSNPs').append("div")
                  .attr("class", "canvasarea")
                	.style("left", margin.left)
                	.style("top", margin.top)
                	.append("canvas")
                	.attr("class", "canvasarea")
                	.attr("width", width+margin.right)
                	.attr("height", height+margin.bottom)
                	.node().getContext('2d');

  var qqGene = d3.select("#QQGenes").append("svg")
                .attr("width", width+margin.left+margin.right)
                .attr("height", height+margin.top+margin.bottom)
                .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvasGene = d3.select('#QQGenes').append("div")
                    .attr("class", "canvasarea")
                  	.style("left", margin.left)
                  	.style("top", margin.top)
                  	.append("canvas")
                  	.attr("class", "canvasarea")
                  	.attr("width", width+margin.right)
                  	.attr("height", height+margin.bottom)
                  	.node().getContext('2d');

  // Plot
  d3.json('GWASresult/QQplot/'+dbName+'/SNP', function(data){
  	data.forEach(function(d){
  		d.obs = +d.obs;
  		d.exp = +d.exp;
  		// d.n = +d.n;
  	});

  	var x = d3.scale.linear().range([0, width]);
  	var y = d3.scale.linear().range([height, 0]);
    var xMax = d3.max(data, function(d){return d.exp;});
    var yMax = d3.max(data, function(d){return d.obs;});
  	x.domain([0, (xMax+xMax*0.01)]);
  	y.domain([0, (yMax+yMax*0.01)]);
  	var yAxis = d3.svg.axis().scale(y).orient("left");
  	var xAxis = d3.svg.axis().scale(x).orient("bottom");

  	// var maxP = Math.min(d3.max(data, function(d){return d.exp;}), d3.max(data, function(d){return d.obs;}));
    var maxP = Math.min(xMax, yMax);

    data.forEach(function(d){
  		// if(d.obs>1.5 | d.n%100==0){
  			canvasSNP.beginPath();
  			canvasSNP.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  			canvasSNP.fillStyle="grey";
  			canvasSNP.fill();
  		// }
  	});

  	qqSNP.append("g").attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")").call(xAxis);
    qqSNP.append("g").attr("class", "y axis").call(yAxis);
    qqSNP.append("line")
  	    .attr("x1", 0).attr("x2", x(maxP))
        .attr("y1", y(0)).attr("y2", y(maxP))
        .style("stroke", "red")
        .style("stroke-dasharray", ("3,3"));
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+width/2+","+(-15)+")")
      .text("GWAS summary statistics")
      .style("font-size", "20px");
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
      .text("Observed -log10 P-value");
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width/2)+","+(height+35)+")")
      .text("Expected -log10 P-value");

  });

  d3.json('GWASresult/QQplot/'+dbName+'/Gene', function(data){
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

  	// var maxP = Math.min(d3.max(data, function(d){return d.exp;}), d3.max(data, function(d){return d.obs;}));
    var maxP = Math.min(xMax, yMax);

    data.forEach(function(d){
  		canvasGene.beginPath();
  		canvasGene.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  		canvasGene.fillStyle="grey";
  		canvasGene.fill();
  	});

  	qqGene.append("g").attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")").call(xAxis);
        	qqGene.append("g").attr("class", "y axis").call(yAxis);
  	qqGene.append("line")
    	.attr("x1", 0).attr("x2", x(maxP))
    	.attr("y1", y(0)).attr("y2", y(maxP))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+width/2+","+(-15)+")")
      .text("Gene-based statistics")
      .style("font-size", "20");
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
      .text("Observed -log10 P-value");
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width/2)+","+(height+35)+")")
      .text("Expected -log10 P-value");
  });

}
