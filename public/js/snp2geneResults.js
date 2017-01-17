var leadSNPtable_selected=null;
var intervalTable_selected=null;
// var SNPtable_selected=null;
var annotPlotSelected;
$(document).ready(function(){
  // hide submit buttons for imgDown
  $('.ImgDownSubmit').hide();

  var hashid = window.location.hash;
  if(hashid==""){
    $('a[href="#newJob"]').trigger('click');
  }else{
    $('a[href="'+hashid+'"]').trigger('click');
  }

  $(".CanvDown").on('click', function(){
    var id = $(this).attr("id");
    id = id.replace("CanvasDown", "");
    var url = $('#'+id+'PNG img').attr("src");
    var a = document.createElement('a');
    a.href = url;
    a.download = id+".png";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    // Canvas2Image.saveAsPNG(canvas);
  });

  $('#annotPlotPanel').hide();

  if(status.length==0){
  }else if(status=="fileFormatGWAS"){
    $('a[href="#newJob"]').trigger('click');
    $('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
      +'<b>Provided file (GWAS summary statistics) format was not valid. Text files (with any extention), zip file or gzip files are acceptable.</b>'
      +'</div>');
  }else if(status=="fileFormatLead"){
    $('a[href="#newJob"]').trigger('click');
    $('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
    +'<b>Provided file (Pre-defined lead SNPs) format was not valid. Only plain text files (with any extention) is acceptable.</b>'
      +'</div>');
  }else if(status=="fileFormatRegions"){
    $('a[href="#newJob"]').trigger('click');
    $('#fileFormatError').html('<div class="alert alert-danger" style="width: auto;">'
    +'<b>Provided file (Pre-defined genomic regions) format was not valid. Only plain text files (with any extention) is acceptable.</b>'
      +'</div>');
  }else{
    $('#annotPlotSubmit').attr("disabled", true);
    $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or interval to plot. If you haven\'t selected any row, please click one of the row of lead SNP or interval table.</div>');
    if($('#annotPlot_Chrom15').is(":checked")==false){
      $('#annotPlotChr15Opt').hide();
    }

    var jobStatus;
    $.get({
      url: subdir + '/snp2gene/checkJobStatus/'+jobid,
      error: function(){
        alert("ERROR: checkJobStatus")
      },
      success: function(data){
        jobStatus = data;
      },
      complete: function(){
        if(jobStatus=="OK"){
          loadResults();
          $('a[href="#genomePlots"]').trigger('click');
        }
      }
    });


    function loadResults(){
      var filedir;
      var posMap;
      var eqtlMap;
      var orcol;
      var secol;
      $.ajax({
          url: subdir+'/snp2gene/getParams',
          type: 'POST',
          data:{
            jobID: jobid
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
            orcol = tmp[3];
            secol = tmp[4];
          },
          complete: function(){
            // jobInfo(jobid);
            GWplot(jobid);
            QQplot(jobid);
            showResultTables(filedir, jobid, posMap, eqtlMap, orcol, secol);
            $('#results').show();
            $('#resultsSide').show();
          }
      });
    }

  }

  // download file selection
  $('#allfiles').on('click', function(){
    $('#paramfile').prop('checked', true);
    $('#leadfile').prop('checked', true);
    $('#intervalfile').prop('checked', true);
    $('#snpsfile').prop('checked', true);
    $('#annovfile').prop('checked', true);
    $('#annotfile').prop('checked', true);
    $('#genefile').prop('checked', true);
    $('#eqtlfile').prop('checked', true);
    $('#gwascatfile').prop('checked', true);
    // $('#exacfile').prop('checked', true);
    $('#magmafile').prop('checked', true);
    $('#download').attr('disabled',false);
  });
  $('#clearfiles').on('click', function(){
    $('#paramfile').prop('checked', false);
    $('#leadfile').prop('checked', false);
    $('#intervalfile').prop('checked', false);
    $('#snpsfile').prop('checked', false);
    $('#annovfile').prop('checked', false);
    $('#annotfile').prop('checked', false);
    $('#genefile').prop('checked', false);
    $('#eqtlfile').prop('checked', false);
    // $('#exacfile').prop('checked', false);
    $('#gwascatfile').prop('checked', false);
    $('#magmafile').prop('checked', false);
    $('#download').attr('disabled',true);
  });

  // annotPlot Chr15 tissue selection clear click
  $('#annotPlotChr15TsClear').on('click', function(){
    var tmp = document.getElementById('annotPlotChr15Ts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
  });
  $('#annotPlotChr15GtsClear').on('click', function(){
    var tmp = document.getElementById('annotPlotChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
  });

});

function GWplot(jobID){
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
  // d3.select("#manhattanPane").style("height", height+margin.top+margin.bottom);
  // d3.select("#geneManhattanPane").style("height", height+margin.top+margin.bottom);
  var svg = d3.select("#manhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvas1 = d3.select('#manhattanMain')
  //           	.attr("width", width)
  //           	.attr("height", height)
  //           	.node().getContext('2d');
  var svg2 = d3.select("#geneManhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvas2 = d3.select('#geneManhattanMain')
  //           	.attr("width", width)
  //           	.attr("height", height)
  //           	.node().getContext('2d');
  d3.json("manhattan/jobs/"+jobID+"/manhattan.txt", function(data){
  // d3.tsv("/../IPGAP/sotrage/jobs/"+jobID+"/manhattan.txt", function(error, data){

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

    // data.forEach(function(d){
    // 		// if(d.p<=0.005 || d.bp%200==0){
    // 			canvas1.beginPath();
    // 			// canvas1.arc( x(d.bp+chromStart[d.chr-1]), y(-Math.log10(d.p)), 2, 0, 2*Math.PI);
    //       canvas1.arc( x(d[1]+chromStart[d[0]-1]), y(-Math.log10(d[2])), 2, 0, 2*Math.PI);
    // 			// if(d.chr%2==0){canvas1.fillStyle="steelblue";}
    //       if(d[0]%2==0){canvas1.fillStyle="steelblue";}
    // 			else{canvas1.fillStyle="blue";}
    // 			canvas1.fill();
    // 		// }
    // 	});

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

  d3.json("manhattan/jobs/"+jobID+"/magma.genes.out", function(data){
    data.forEach(function(d){
      // d.CHR = +d.CHR;
  		// d.START = +d.START;
  		// d.STOP = +d.STOP;
  		// d.P = +d.P;
      // d.y = 0;
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
      +"Genome wide significance (red dashed line in the plot) was defined at P = 0.05/"+data.length+" = "+((0.05/data.length).toExponential())+".");

    sortedP = sortedP.sort(function(a,b){return a-b;});
    // var chr = d3.set(data.map(function(d){return d.CHR;})).values();
    var chr = d3.set(data.map(function(d){return d[0];})).values();
    var max_chr = chr.length;
    var x = d3.scale.linear().range([0, width]);
    x.domain([0, (chromStart[max_chr-1]+chromSize[max_chr-1])]);
    var xAxis = d3.svg.axis().scale(x).orient("bottom");
    var y = d3.scale.linear().range([height, 0]);
    // y.domain([0, d3.max(data, function(d){return -Math.log10(d.P);})+1]);
    y.domain([0, d3.max(data, function(d){return -Math.log10(d[3]);})+1]);
    var yAxis = d3.svg.axis().scale(y).orient("left");

    // data.forEach(function(d){
    // 		canvas2.beginPath();
    // 		// canvas2.arc( x((d.START+d.STOP)/2+chromStart[d.CHR-1]), y(-Math.log10(d.P)), 2, 0, 2*Math.PI);
    //     canvas2.arc( x((d[1]+d[2])/2+chromStart[d[0]-1]), y(-Math.log10(d[3])), 2, 0, 2*Math.PI);
    // 		// if(d.CHR%2==0){canvas2.fillStyle="steelblue";}
    //     if(d[0]%2==0){canvas2.fillStyle="steelblue";}
    // 		else{canvas2.fillStyle="blue";}
    // 		canvas2.fill();
    // 	});

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

function QQplot(jobID){
  var margin = {top:30, right: 30, bottom:50, left:50},
      width = 300,
      height = 300;
  // d3.select("#QQplotPane").style("height", height+margin.top+margin.bottom);
  // create svg and canvas objects
  var qqSNP = d3.select("#QQplot").append("svg")
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g")
              .attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvasSNP = d3.select('#QQplotMain')
  //               	.attr("width", width+margin.right)
  //               	.attr("height", height+margin.bottom)
  //               	.node().getContext('2d');

  var qqGene = d3.select("#geneQQplot").append("svg")
                .attr("width", width+margin.left+margin.right)
                .attr("height", height+margin.top+margin.bottom)
                .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
  // var canvasGene = d3.select('#geneQQplotMain')
  //                 	.attr("width", width+margin.right)
  //                 	.attr("height", height+margin.bottom)
  //                 	.node().getContext('2d');
  d3.json('QQplot/jobs/'+jobID+'/SNP', function(data){
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

    // data.forEach(function(d){
  	// 	// if(d.obs>1.5 | d.n%100==0){
  	// 		canvasSNP.beginPath();
  	// 		canvasSNP.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  	// 		canvasSNP.fillStyle="grey";
  	// 		canvasSNP.fill();
  	// 	// }
  	// });

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
    // qqSNP.append("text").attr("text-anchor", "middle")
    //   .attr("transform", "translate("+width/2+","+(-15)+")")
    //   .text("GWAS summary statistics")
    //   .style("font-size", "20px");
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

  d3.json('QQplot/jobs/'+jobID+'/Gene', function(data){
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

    // data.forEach(function(d){
  	// 	canvasGene.beginPath();
  	// 	canvasGene.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  	// 	canvasGene.fillStyle="grey";
  	// 	canvasGene.fill();
  	// });

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
    // qqGene.append("text").attr("text-anchor", "middle")
    //   .attr("transform", "translate("+width/2+","+(-15)+")")
    //   .text("Gene-based statistics")
    //   .style("font-size", "20");
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

function showResultTables(filedir, jobID, posMap, eqtlMap, orcol, secol){
  $('#plotClear').hide();
  $('#download').attr('disabled', false);
  if(eqtlMap==0){
    $('#eqtlTableTab').hide();
    $('#check_eqtl_annotPlot').hide();
    $('#annotPlot_eqtl').prop('checked', false);
    $('#eqtlfiledown').hide();
    $('#eqtlfile').prop('checked',false);
  }

  $.ajax({
    url: "paramTable",
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
    url: "sumTable",
    type: "POST",
    data: {
      filedir: filedir,
    },
    success: function(data){
      $('#sumTable').append(data);
    },
    complete: function(){
      PlotSNPAnnot(jobID);
      PlotIntervalSum(jobID);
    }
  });

  var file = "leadSNPs.txt";
  var leadTable = $('#leadSNPtable').DataTable({
      "processing": true,
      serverSide: false,
      select: true,
      "ajax" : {
        url: "DTfile",
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "No:interval:uniqID:rsID:chr:pos:p:nSNPs:nGWASSNPs"
        }
      },
      error: function(){
        alert("leadSNPs table error");
      },
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
  });

  file = "intervals.txt";
  var intervalTable = $('#intervalTable').DataTable({
      "processing": true,
      serverSide: false,
      select: true,
      "ajax" : {
        url: "DTfile",
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "Interval:uniqID:rsID:chr:pos:p:nLeadSNPs:start:end:nSNPs:nGWASSNPs"
        }
      },
      error: function(){
        alert("interval table error");
      },
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
  });

  var table = "<thead>"
      +"<tr>"
        +"<th>uniqID</th><th>rsID</th><th>chr</th><th>bp</th><th>MAF</th><th>P-value</th>";
  if(orcol!="NA"){
    table += "<th>OR</th>";
  }
  if(secol!="NA"){
    table += "<th>SE</th>";
  }
  table +="<th>Interval</th><th>r2</th><th>leadSNP</th><th>Nearest gene</th><th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>"
      +"</tr>"
    +"</thead>";
  file = "snps.txt";
  $('#SNPtable').html(table);
  var cols = "uniqID:rsID:chr:pos:MAF:gwasP";
  if(orcol!="NA"){
    cols += ":or";
  }
  if(secol!="NA"){
    cols += ":se";
  }
  cols += ":Interval:r2:leadSNP:nearestGene:dist:func:CADD:RDB:minChrState:commonChrState";
  var SNPtable = $('#SNPtable').DataTable({
    processing: true,
    serverSide: false,
    select: false,
    ajax:{
      url: 'DTfile',
      type: "POST",
      data: {
        filedir: filedir,
        infile: file,
        header: cols
      }
    },
    error: function(){
      alert("SNP table error");
    },
    "order": [[2, 'asc'], [3, 'asc']],
    "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "iDisplayLength": 10,
    dom: 'lBfrtip',
    buttons: ['csv']
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
        filedir: filedir,
        infile: file,
        header: "uniqID:chr:pos:gene:symbol:dist:annot:exonic_func:exon"
      }
    },
    "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "iDisplayLength": 10,
    dom: 'lBfrtip',
    buttons: ['csv']
  });

  file = "genes.txt";
  var thead = "<thead><tr><th>Gene</th><th>Symbol</th><th>entrezID</th><th>Interval</th><th>chr</th><th>start</th><th>end</th>";
  thead += "<th>strand</th><th>status</th><th>type</th><th>HUGO</th>";
  if(posMap==1){
    thead += "<th>posMapSNPs</th><th>posMapMaxCADD</th>";
  }
  if(eqtlMap==1){
    thead += "<th>eqtlMapSNPs</th><th>eqtlMapminP</th><th>eqtlMapminQ</th><th>eqtlMapts</th><th>eqtlDirection</th>";
  }
  thead += "<th>minGwasP</th><th>leadSNPs</th></tr></thead>";

  $('#geneTable').append(thead);
  var geneTable;
  if(posMap==1 && eqtlMap==1){
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: false,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "ensg:symbol:entrezID:interval:chr:start:end:strand:status:type:HUGO:posMapCHPs:posMapMaxCADD:eqtlMapSNPs:eqtlMapminP:eqltMapminQ:eqtlMapts:eqtlDirection:minGwasP:leadSNPs"
        }
      },
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }else if(posMap==1){
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: false,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "ensg:symbol:entrezID:interval:chr:start:end:strand:status:type:HUGO:posMapCHPs:posMapMaxCADD:minGwasP:leadSNPs"
        }
      },
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }else{
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: false,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "ensg:symbol:entrezID:interval:chr:start:end:strand:status:type:HUGO:eqtlMapSNPs:eqtlMapminP:eqltMapminQ:eqtlMapts:eqtlDirection:minGwasP:leadSNPs"
        }
      },
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }

  if(eqtlMap==1){
    file = "eqtl.txt";
    var eqtlTable = $('#eqtlTable').DataTable({
      processing: true,
      serverSide: false,
      select: false,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
          header: "uniqID:chr:pos:db:tissue:gene:symbol:p:FDR:tz"
        }
      },
      "order": [[1, 'asc'], [2, 'asc']],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
      // deferLoading: 25
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
          filedir: filedir,
          infile: file,
          header: "Interval:leadSNP:chr:bp:snp:PMID:Trait:FirstAuth:Date:P"
        }
      },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10,
        dom: 'lBfrtip',
        buttons: ['csv']
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
  //       filedir: filedir,
  //       infile: file,
  //     }
  //   },
  //   columns:[
  //     {"data": "Interval", name:"Interval"},
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


  $('#leadSNPtable tbody').on('click', 'tr', function(){
    $('#plotClear').show();
    $('#annotPlotPanel').show();
    $('#annotPlotSelect').val('leadSNP');
    var rowI = leadTable.row(this).index();
    leadSNPtable_selected=rowI;
    $('#annotPlotRow').val(rowI);
    Chr15Select();

    var rowData = leadTable.row(rowI).data();
    var chr = rowData[4];
    // create plot space
    var colorScale = d3.scale.linear().domain([0.0,0.5,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
    var margin = {top:30, right: 30, bottom:50, left:50},
        width = 700-margin.right - margin.left,
        height = 300-margin.top - margin.bottom;
    // set range
    var x = d3.scale.linear().range([0, width]);
    var y = d3.scale.linear().range([height, 0]);
    d3.select('#locusPlot').select("svg").remove();
    $('#selectedLeadSNP').html("");
    var svg = d3.select("#locusPlot").append("svg")
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
    //Get data
    d3.json("locusPlot/"+rowI+'/"leadSNP"/'+jobID, function(data){
      data.forEach(function(d){
        d.pos = +d.pos;
        d.logP = +d.logP;
        d.r2 = +d.r2;
        d.ld = +d.ld;
        // d.gwasP = +d.gwasP;
      });
      var side=(d3.max(data, function(d){return d.pos})-d3.min(data, function(d){return d.pos}))*0.05;
      x.domain([d3.min(data, function(d){return d.pos})-side, d3.max(data, function(d){return d.pos})+side]);
      y.domain([0, d3.max(data, function(d){return d.logP})]);
      var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
      var yAxis = d3.svg.axis().scale(y).orient("left");
      // tip
      var tip = d3.tip().attr("class", "d3-tip")
        .offset([-10,0])
        .html(function(d){
          var out = "rsID: "+d.rsID+"<br/>BP: "+d.pos+"<br/>P: "+d.gwasP+"<br/>MAF: "+d.MAF;
          if(orcol!="NA"){out += "<br/>OR: "+d.or;}
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
      svg.selectAll("dot").data(data.filter(function(d){if(d.gwasP!=="NA"){return d;}})).enter()
        .append("circle")
        .attr("class", "dot")
        .attr("r", 3.5).attr("cx", function(d){return x(d.pos);})
        .attr("cy", function(d){return y(d.logP);})
        .style('fill', function(d){if(d.ld==0){return "grey";}else{return colorScale(d.r2);}})
        .on("mouseover", tip.show)
        .on("mouseout", tip.hide);
      // add rect for 1KG SNPs
      svg.selectAll("rect.KGSNPs").data(data.filter(function(d){if(d.gwasP==="NA"){return d;}})).enter()
        .append("rect")
        .attr("class", "KGSNPs")
        .attr("x", function(d){return x(d.pos)})
        .attr("y", -20)
        .attr("height", "10")
        .attr("width", "3")
        .style('fill', function(d){if(d.ld==0){return "grey";}else{return colorScale(d.r2);}})
        .on("mouseover", tip.show)
        .on("mouseout", tip.hide);
      // add square to lead SNPs
      // svg.selectAll("rect.leadSNPs").data(data.filter(function(d){if(d.ld==2){return d;}})).enter()
      //   .append("rect")
      //   .attr("class", "leadSNPs")
      //   .attr("x", function(d){return x(d.pos)-4})
      //   .attr("y", function(d){return y(d.logP)-4;})
      //   .attr("width", "8")
      //   .attr("height", "8")
      //   .attr("transform", function(d){return "rotate(-45,"+x(d.pos)+","+y(d.logP)+")";})
      //   .style("fill", "purple").style("stroke", "black")
      //   .on("mouseover", tip.show)
      //   .on("mouseout", tip.hide);
      svg.selectAll("dot.leadSNPs").data(data.filter(function(d){if(d.ld==2){return d;}})).enter()
        .append("circle")
        .attr("class", "leadSNPs")
        .attr("cx", function(d){return x(d.pos)})
        .attr("cy", function(d){return y(d.logP);})
        .attr("r", 4.5)
        .style("fill", "purple").style("stroke", "black")
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
        .attr("transform", "translate("+(width/2)+","+(height+margin.bottom-15)+")")
        .text("Chromosome "+chr);
      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2)+", -15)")
        .style("font-size", "8px")
        .text("1000G SNPs");

      function zoomed() {
        svg.select(".x.axis").call(xAxis);
        // svg.select(".y.axis").call(yAxis);
        // svg.selectAll(".dot").attr("transform", transform);
        // svg.selectAll(".dot").filter(function(d){if(x(d.pos)>=0 && x(d.pos)<=width){return d;}}).attr("cx", function(d){if(x(d.pos)>=0 && x(d.pos)<width){return x(d.pos);}})
        //   .attr("cy", function(d){if(x(d.pos)>=0 && x(d.pos)<width){return y(d.logP);}});
        svg.selectAll(".dot").attr("cx", function(d){return x(d.pos);})
          .attr("cy", function(d){return y(d.logP);})
          .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return"grey";}else{return colorScale(d.r2);}});
        svg.selectAll(".KGSNPs")
          .attr("x", function(d){return x(d.pos)})
          .attr("y", -20)
          .style('fill', function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return "grey";}else{return colorScale(d.r2);}});
        svg.selectAll(".leadSNPs")
          .attr("cx", function(d){return x(d.pos);})
          .attr("cy", function(d){return y(d.logP);})
          .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "purple";}})
          .style("stroke", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "black";}});
        }

      d3.select('#plotClear').on('click', reset);
      function reset(){
        d3.transition().duration(750).tween("zoom", function(){
          var ix = d3.interpolate(x.domain(), [d3.min(data, function(d){return d.pos})-side, d3.max(data, function(d){return d.pos})+side]);
          return function(t){
            zoom.x(x.domain(ix(t)));
            zoomed();
          }
        });
      }
    });

    var out = "<h5>Selected lead SNP</h5><table class='table table-striped'><tr><td>lead SNP</td><td>"+rowData[3]
              +"</td></tr><tr><td>Chrom</td><td>"+rowData[4]+"</td></tr><tr><td>BP</td><td>"
              +rowData[5]+"</td></tr><tr><td>P-value</td><td>"+rowData[6]+"</td></tr><tr><td>SNPs within LD</td><td>"
              +rowData[7]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData[8]+"</td></tr>";
    $('#selectedLeadSNP').html(out);
  });

  $('#intervalTable tbody').on('click', 'tr', function(){
    $('#plotClear').show();
    $('#annotPlotPanel').show();
    $('#annotPlotSelect').val('interval');
    var rowI = intervalTable.row(this).index();
    intervalTable_selected=rowI;
    $('#annotPlotRow').val(rowI);
    Chr15Select();

    var rowData = intervalTable.row(rowI).data();
    var chr = rowData[3];
    // create plot space
    var colorScale = d3.scale.linear().domain([0.0,0.5,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
    var margin = {top:30, right: 30, bottom:50, left:50},
        width = 700-margin.right - margin.left,
        height = 300-margin.top - margin.bottom;
    // set range
    var x = d3.scale.linear().range([0, width]);
    var y = d3.scale.linear().range([height, 0]);
    // var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
    // var yAxis = d3.svg.axis().scale(y).orient("left");
    d3.select('#locusPlot').select("svg").remove();
    $('#selectedLeadSNP').html("");
    var tooltip = d3.select('#locusPlot').append("div").attr("class", "tooltip").style("opacity",0);
    var svg = d3.select("#locusPlot").append("svg")
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
    //Get data
    d3.json("locusPlot/"+rowI+'/"intervals"/'+jobID, function(data){
      data.forEach(function(d){
        d.pos = +d.pos;
        d.logP = +d.logP;
        d.r2 = +d.r2;
        d.ld = +d.ld;
        // d.gwasP = +d.gwasP;
      });
      var side=(d3.max(data, function(d){return d.pos})-d3.min(data, function(d){return d.pos}))*0.05;
      x.domain([d3.min(data, function(d){return d.pos})-side, d3.max(data, function(d){return d.pos})+side]);
      y.domain([0, d3.max(data, function(d){return d.logP})]);
      var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
      var yAxis = d3.svg.axis().scale(y).orient("left");
      // tip
      var tip = d3.tip().attr("class", "d3-tip")
        .offset([-10,0])
        .html(function(d){
          var out = "rsID: "+d.rsID+"<br/>BP: "+d.pos+"<br/>P: "+d.gwasP+"<br/>MAF: "+d.MAF;
          if(orcol!="NA"){out += "<br/>OR: "+d.or;}
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
      // plot
      // dot for GWAS tagged SNPs
      svg.selectAll("dot").data(data.filter(function(d){if(d.gwasP!=="NA"){return d;}})).enter().append("circle")
        .attr('class', 'dot')
        .attr("r", 3.5).attr("cx", function(d){return x(d.pos);})
        .attr("cy", function(d){return y(d.logP);})
        .style('fill', function(d){if(d.ld==0){return "grey";}else{return colorScale(d.r2);}})
        .on("mouseover", tip.show)
        .on("mouseout", tip.hide);
      // rect for 1KG SNPs
      svg.selectAll("rect.KGSNPs").data(data.filter(function(d){if(d.gwasP==="NA"){return d;}})).enter()
        .append("rect")
        .attr("class", "KGSNPs")
        .attr("x", function(d){return x(d.pos)})
        .attr("y", -20)
        .attr("height", "10")
        .attr("width", "3")
        .style('fill', function(d){return colorScale(d.r2);})
        .on("mouseover", tip.show)
        .on("mouseout", tip.hide);
      // add square to lead SNPs
      svg.selectAll("dot.leadSNPs").data(data.filter(function(d){if(d.ld==2){return d;}})).enter()
        .append("circle")
        .attr("class", "leadSNPs")
        .attr("cx", function(d){return x(d.pos)})
        .attr("cy", function(d){return y(d.logP);})
        .attr("r", 4.5)
        .style("fill", "purple").style("stroke", "black")
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
        .attr("transform", "translate("+(width/2)+","+(height+margin.bottom-15)+")")
        .text("Chromosome "+chr);
      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2)+", -15)")
        .style("font-size", "8px")
        .text("1000G SNPs");
      function zoomed() {
        svg.select(".x.axis").call(xAxis);
        svg.selectAll(".dot").attr("cx", function(d){return x(d.pos);})
          .attr("cy", function(d){return y(d.logP);})
          .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return"grey";}else{return colorScale(d.r2);}});
        svg.selectAll(".KGSNPs")
          .attr("x", function(d){return x(d.pos)})
          .attr("y", -20)
          .style('fill', function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return "grey";}else{return colorScale(d.r2);}});
        svg.selectAll(".leadSNPs")
          .attr("cx", function(d){return x(d.pos);})
          .attr("cy", function(d){return y(d.logP);})
          .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "purple";}})
          .style("stroke", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "black";}});
      }
      d3.select('#plotClear').on('click', reset);
      function reset(){
        d3.transition().duration(750).tween("zoom", function(){
          var ix = d3.interpolate(x.domain(), [d3.min(data, function(d){return d.pos})-side, d3.max(data, function(d){return d.pos})+side]);
          return function(t){
            zoom.x(x.domain(ix(t)));
            zoomed();
          }
        });
      }
    });
    var rowData = intervalTable.row(rowI).data();
    var out = "<h5>Selected Loci</h5><table class='table table-striped'><tr><td>top lead SNP</td><td>"+rowData[2]
              +"</td></tr><tr><td>Chrom</td><td>"+rowData[3]+"</td></tr><tr><td>BP</td><td>"
              +rowData[4]+"</td></tr><tr><td>P-value</td><td>"+rowData[5]+"</td></tr><tr><td>#lead SNPs</td><td>"+rowData[6]
              +"</td></tr><tr><td>SNPs within LD</td><td>"
              +rowData[9]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData[10]+"</td></tr>";

    $('#selectedLeadSNP').html(out);
  });
}

function PlotSNPAnnot(jobID){
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
            // .attr("id", "SnpAnnotPlotsvg")
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
  d3.json("d3text/"+jobID+"/"+file, function(data){
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

function PlotIntervalSum(jobID){
  var file="interval_sum.txt";
  // filedir = filedir.replace("../", "");
  d3.json("d3text/"+jobID+"/"+file, function(data){
    data.forEach(function(d){
      d.nSNPs = +d.nSNPs;
      d.size = +(d.size/1000);
      d.nGenes = +d.nGenes;
      d.nWithinGene = +d.nWithinGene;
    });
    var y_element = data.map(function(d){return d.label;});
    var margin = {top:30, right: 30, bottom:50, left:180},
        width = 600,
        height = 15*y_element.length;
    var y = d3.scale.ordinal().domain(y_element).rangeBands([0, height], 0.1);
    var yAxis = d3.svg.axis().scale(y).orient("left");
    var svg = d3.select('#intervalPlot').append('svg')
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
        // .attr("dx","-.65em").attr("dy", "-.2em");
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
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
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
        // .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .attr("transform", "translate("+currentWidth+",0)")
        .call(yAxis).selectAll("text").remove();
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
        .style("text-anchor", "middle")
        .text("#SNPs");
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
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
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
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
        // .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .attr("transform", "translate("+currentWidth+",0)")
        .call(yAxis).selectAll("text").remove();
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(-5)+")")
        .style("text-anchor", "middle")
        .text("#genes within loci");
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
        .style("text-anchor", "middle")
        .text("#genes within loci");
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
    var gts = [];
    var tmp = document.getElementById('annotPlotChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        gts.push(tmp.options[i].value);
      }
    }
    if(ts.length===0 && gts.length===0){
      $('#CheckAnnotPlotOpt').html('<span class="alert alert-danger">You have selected to plot 15-core chromatin state. Please select at least one tissue/cell type.</span>');
      $('#annotPlotSubmit').attr("disabled", true);
    }else if(ts.length>0 && gts.length>0){
      $('#CheckAnnotPlotOpt').html("<span class='alert alert-warning'>OK. Both individual and general tissue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</span>");
      $('#annotPlotSubmit').attr("disabled", false);
    }else if(ts.length>0){
      $('#CheckAnnotPlotOpt').html("<span class='alert alert-success'>OK. Selected inspanidual tissue/cell types will be used for chromatine state filtering.</span>");
      $('#annotPlotSubmit').attr("disabled", false);
    }else if(gts.length>0){
      $('#CheckAnnotPlotOpt').html("<span class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</span>");
      $('#annotPlotSubmit').attr("disabled", false);
    }
  }else{
    $('#annotPlotChr15Opt').hide();
    $('#annotPlotSubmit').attr("disabled", false);
    $('#CheckAnnotPlotOpt').html('<span class="alert alert-success">OK. Good to go. Click "Plot" to create regional plot with selected annotations.</span>');
  }
}

function DownloadFiles(){
  // var allfiles = document.getElementById('allfiles').checked;
  var paramfile = document.getElementById('paramfile').checked;
  var leadfile = document.getElementById('leadfile').checked;
  var intervalfile = document.getElementById('intervalfile').checked;
  var snpsfile = document.getElementById('snpsfile').checked;
  var annovfile = document.getElementById('annovfile').checked;
  var annotfile = document.getElementById('annotfile').checked;
  var genefile = document.getElementById('genefile').checked;
  var eqtlfile = document.getElementById('eqtlfile').checked;
  // var exacfile = document.getElementById('exacfile').checked;
  var magmafile = document.getElementById('magmafile').checked;
  if(paramfile || leadfile || intervalfile || snpsfile || annovfile || annotfile || genefile || eqtlfile || magmafile){
    document.getElementById('download').disabled=false;
  }
}

function ImgDown(id, type){
  $('#'+id+'Data').val($('#'+id).html());
  $('#'+id+'Type').val(type);
  $('#'+id+'JobID').val(jobid);
  $('#'+id+'FileName').val(id);
  $('#'+id+'Dir').val("jobs");
  $('#'+id+'Submit').trigger('click');
}
