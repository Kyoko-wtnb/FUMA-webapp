var leadSNPtable_selected=null;
var intervalTable_selected=null;
var SNPtable_selected=null;
$(document).ready(function(){
  var job = IPGAPvar.jobtype;
  var email = IPGAPvar.email;
  var filedir = IPGAPvar.filedir;
  var jobID = IPGAPvar.jobID;

  $('#annotPlotSubmit').attr("disabled", true);

  if(job==="newjob"){
    $("#results").hide();
    // get parameters
    var leadfile = IPGAPvar.leadSNPsfileup;
    var regionfile = IPGAPvar.regionsfileup;
    var gwasformat = IPGAPvar.gwasformat;
    var addleadSNPs = IPGAPvar.addleadSNPs;
    var leadP = IPGAPvar.leadP;
    var r2 = IPGAPvar.r2;
    var gwasP = IPGAPvar.gwasP;
    var pop = IPGAPvar.pop;
    var KGSNPs = IPGAPvar.KGSNPs;
    var maf = IPGAPvar.maf;
    var mergeDist = IPGAPvar.mergeDist;

    var exMHC = IPGAPvar.exMHC;
    var extMHC = IPGAPvar.extMHC;

    var genetype = IPGAPvar.genetype;

    var posMap = IPGAPvar.posMap;
    var posMapWindow = IPGAPvar.posMapWindow;
    var posMapWindowSize = IPGAPvar.posMapWindowSize;
    var posMapAnnot = IPGAPvar.posMapAnnot;
    var posMapCADDth = IPGAPvar.posMapCADDth;
    var posMapRDBth = IPGAPvar.posMapRDBth;
    var posMapChr15 = IPGAPvar.posMapChr15;
    var posMapChr15Max = IPGAPvar.posMapChr15Max;
    var posMapChr15Meth = IPGAPvar.posMapChr15Meth;

    var eqtlMap = IPGAPvar.eqtlMap;
    var eqtlMaptss = IPGAPvar.eqtlMaptss;
    var eqtlMapSigeqtl = IPGAPvar.sigeqtl;
    var eqtlMapeqtlP = IPGAPvar.eqtlP;
    var eqtlMapCADDth = IPGAPvar.eqtlMapCADDth;
    var eqtlMapRDBth = IPGAPvar.eqtlMapRDBth;
    var eqtlMapChr15 = IPGAPvar.eqtlMapChr15;
    var eqtlMapChr15Max = IPGAPvar.eqtlMapChr15Max;
    var eqtlMapChr15Meth = IPGAPvar.eqtlMapChr15Meth;

    // $('#test').html("<h3>New Job</h3><p>test</p><p>email: "+email+"<br/>genetype: "+genetype+"</p>");
    // $('#results').show();
    // document.getElementById('test').innerHTML="<p>posMapChr15: "+posMapChr15+"</p>";

    // $('#results').show();
    // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
    $.ajax({
      url: 'CandidateSelection',
      type: 'POST',
      data: {
          filedir: filedir,
          gwasformat: gwasformat,
          leadfile: leadfile,
          addleadSNPs: addleadSNPs,
          regionfile: regionfile,
          leadP: leadP,
          r2: r2,
          gwasP: gwasP,
          pop: pop,
          KGSNPs: KGSNPs,
          maf: maf,
          mergeDist: mergeDist,
          exMHC: exMHC,
          extMHC: extMHC,
          genetype: genetype,
          posMap: posMap,
          posMapWindow: posMapWindow,
          posMapWindowSize: posMapWindowSize,
          posMapAnnot: posMapAnnot,
          posMapCADDth: posMapCADDth,
          posMapRDBth: posMapRDBth,
          posMapChr15: posMapChr15,
          posMapChr15Max: posMapChr15Max,
          posMapChr15Meth: posMapChr15Meth,
          eqtlMap: eqtlMap,
          eqtlMaptss: eqtlMaptss,
          eqtlMapSigeqtl: eqtlMapSigeqtl,
          eqtlMapeqtlP: eqtlMapeqtlP,
          eqtlMapCADDth: eqtlMapCADDth,
          eqtlMapRDBth: eqtlMapRDBth,
          eqtlMapChr15: eqtlMapChr15,
          eqtlMapChr15Max: eqtlMapChr15Max,
          eqtlMapChr15Meth: eqtlMapChr15Meth
      },
      processing: true,
      beforeSend: function(){
        $('#logSNPfiltering').append('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
      },
      success: function(data){
        $('#logSNPfiltering').html('<div class="alert alert-success"><h4> Step 1. Candidate SNPs filtering is done</h4></div>');
        // $('#test').html(data);
      },
      error: function(){
        alert("Error occored (SNPfilt)");
      },
      complete: function(){
        $('#logs').hide();
        $('#results').show();
        showResultTables(filedir, jobID, posMap, eqtlMap);
        // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
      }
    });
  }else{
    $('#logs').hide();
    $('#results').show();
    var posMap = IPGAPvar.posMap;
    var eqtlMap = IPGAPvar.eqtlMap;
    // $('#test').html("<p>posMap: "+posMap+" eqtlMap: "+eqtlMap+"</p>");
    showResultTables(filedir, jobID, posMap, eqtlMap);
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
    $('#exacfile').prop('checked', true);
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
    $('#exacfile').prop('checked', false);
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

function showResultTables(filedir, jobID, posMap, eqtlMap){
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
      "serverSide": false,
      select: true,
      "ajax" : {
        url: "DTfile",
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      error: function(){
        alert("leadSNPs table error");
      },
      "columns":[
        {"data": "No", name: "No"},
        {"data": "interval", name:"Interval"},
        {"data": "uniqID", name: "uniqID"},
        {"data": "rsID", name: "rsID"},
        {"data": "chr", name: "chr"},
        {"data": "pos", name: "pos"},
        {"data": "p", name: "P-value"},
        {"data": "nSNPs", name: "nSNPs"},
        {"data": "nGWASSNPs", name: "nGWASSNPs"}
      ],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
  });

  file = "intervals.txt";
  var intervalTable = $('#intervalTable').DataTable({
      "processing": true,
      "serverSide": false,
      select: true,
      "ajax" : {
        url: "DTfile",
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      error: function(){
        alert("interval table error");
      },
      "columns":[
        {"data": "No", name: "Interval"},
        {"data": "toprsID", name: "toprsID"},
        {"data": "chr", name: "chr"},
        {"data": "pos", name: "pos"},
        {"data": "p", name: "P-value"},
        {"data": "nLeadSNPs", name:"nLeadSNPs"},
        {"data": "start", name:"start"},
        {"data": "end", name:"end"},
        {"data": "nSNPs", name: "nSNPs"},
        {"data": "nGWASSNPs", name: "nGWASSNPs"}
      ],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
  });

  file = "snps.txt";
  var SNPtable = $('#SNPtable').DataTable({
    processing: true,
    serverSide: false,
    select: true,
    ajax:{
      url: 'DTfile',
      type: "POST",
      data: {
        filedir: filedir,
        infile: file,
      }
    },
    error: function(){
      alert("SNP table error");
    },
    columns:[
      {"data": "uniqID", name:"uniqID"},
      {"data": "rsID", name:"rsID"},
      {"data": "chr", name:"chr"},
      {"data": "pos", name:"bp"},
      {"data": null, name:"MAF",
        "render": function(data, type, row){return (Math.round(row.MAF*100)/100)}},
      {"data": "gwasP", name:"P-value"},
      {"data": "Interval", name:"Interval"},
      {"data": null, name:"r2",
        "render":function(data, type, row){return (Math.round(row.r2*100)/100)}},
      {"data": "leadSNP", name:"leadSNP"},
      {"data": "nearestGene", name:"Nearest gene"},
      {"data": "dist", name:"dist"},
      {"data": "func", name:"position"},
      {"data": "CADD", name:"CADD"},
      {"data": "RDB", name:"RDB"},
      {"data": "minChrState", name:"minChrState(127)"},
      {"data": "commonChrState", name:"commonChrState(127)"},
    ],
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
    select: true,
    ajax:{
      url: 'DTfile',
      type: "POST",
      data: {
        filedir: filedir,
        infile: file,
      }
    },
    columns:[
      {"data": "uniqID", name:"uniqID"},
      {"data": "chr", name:"chr"},
      {"data": "pos", name:"bp"},
      {"data": "gene", name:"Gene"},
      {"data": "symbol", name:"Symbol"},
      {"data": "dist", name:"Distance"},
      {"data": "annot", name:"Function"},
      {"data": "exonic_func", name:"Exonic function"},
      {"data": "exon", name:"Exon"}
    ],
    "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "iDisplayLength": 10,
    dom: 'lBfrtip',
    buttons: ['csv']
  });

  file = "genes.txt";
  var thead = "<thead><tr><td>Gene</td><td>Symbol</td><td>entrezID</td><td>Interval</td><td>chr</td><td>start</td><td>end</td>";
  thead += "<td>strand</td><td>status</td><td>type</td><td>HUGO</td>";
  if(posMap==1){
    thead += "<td>posMapSNPs</td><td>posMapMaxCADD</td>";
  }
  if(eqtlMap==1){
    thead += "<td>eqtlMapSNPs</td><td>eqtlMapminP</td><td>eqtlMapminQ</td><td>eqtlMapts</td>";
  }
  thead += "<td>minGwasP</td><td>leadSNPs</td></tr></thead>";

  $('#geneTable').append(thead);
  var geneTable;
  if(posMap==1 && eqtlMap==1){
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: true,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      columns:[
        {"data": "ensg", name:"Gene"},
        {"data": "symbol", name:"Symbol"},
        {"data": "entrezID", name:"entrezID"},
        {"data": "interval", name:"Interval"},
        {"data": "chr", name:"chr"},
        {"data": "start", name:"start"},
        {"data": "end", name:"end"},
        {"data": "strand", name:"strand"},
        {"data": "status", name:"status"},
        {"data": "type", name:"type"},
        {"data": "HUGO", name:"HUGO"},
        {"data": "posMapSNPs", name:"posMapSNPs"},
        {"data": "posMapMaxCADD", name:"posMapMaxCADD"},
        {"data": "eqtlMapSNPs", name:"eqtlMapSNPs"},
        {"data": "eqtlMapminP", name:"eqtlMapminP"},
        {"data": "eqtlMapminQ", name:"eqtlMapminQ"},
        {"data": "eqtlMapts", name:"eqtlMapts"},
        {"data": "minGwasP", name:"minGwasP"},
        {"data": "leadSNPs", name:"leadSNPs"}
      ],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }else if(posMap==1){
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: true,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      columns:[
        {"data": "ensg", name:"Gene"},
        {"data": "symbol", name:"Symbol"},
        {"data": "entrezID", name:"entrezID"},
        {"data": "interval", name:"Interval"},
        {"data": "chr", name:"chr"},
        {"data": "start", name:"start"},
        {"data": "end", name:"end"},
        {"data": "strand", name:"strand"},
        {"data": "status", name:"status"},
        {"data": "type", name:"type"},
        {"data": "HUGO", name:"HUGO"},
        {"data": "posMapSNPs", name:"posMapSNPs"},
        {"data": "posMapMaxCADD", name:"posMapMaxCADD"},
        {"data": "minGwasP", name:"minGwasP"},
        {"data": "leadSNPs", name:"leadSNPs"}
      ],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }else{
    geneTable = $('#geneTable').DataTable({
      processing: true,
      serverSide: false,
      select: true,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      columns:[
        {"data": "ensg", name:"Gene"},
        {"data": "symbol", name:"Symbol"},
        {"data": "entrezID", name:"entrezID"},
        {"data": "interval", name:"Interval"},
        {"data": "chr", name:"chr"},
        {"data": "start", name:"start"},
        {"data": "end", name:"end"},
        {"data": "strand", name:"strand"},
        {"data": "status", name:"status"},
        {"data": "type", name:"type"},
        {"data": "HUGO", name:"HUGO"},
        {"data": "eqtlMapSNPs", name:"eqtlMapSNPs"},
        {"data": "eqtlMapminP", name:"eqtlMapminP"},
        {"data": "eqtlMapminQ", name:"eqtlMapminQ"},
        {"data": "eqtlMapts", name:"eqtlMapts"},
        {"data": "minGwasP", name:"minGwasP"},
        {"data": "leadSNPs", name:"leadSNPs"}
      ],
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
      select: true,
      ajax:{
        url: 'DTfile',
        type: "POST",
        data: {
          filedir: filedir,
          infile: file,
        }
      },
      columns:[
        {"data": "uniqID", name:"uniqID"},
        {"data": "chr", name:"chr"},
        {"data": "pos", name:"bp"},
        {"data": "gene", name:"Gene"},
        {"data": "symbol", name:"Symbol"},
        {"data": "db", name:"DB"},
        {"data": "tissue", name:"tissue"},
        {"data": "p", name:"P-value"},
        {"data": "FDR", name:"FDR"},
        {"data": "tz", name:"t/z"}
      ],
      "order": [[1, 'asc'], [2, 'asc']],
      "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "iDisplayLength": 10,
      dom: 'lBfrtip',
      buttons: ['csv']
    });
  }

  file = "ExAC.txt";
  var eqtlTable = $('#exacTable').DataTable({
    processing: true,
    serverSide: false,
    select: true,
    ajax:{
      url: 'DTfile',
      type: "POST",
      data: {
        filedir: filedir,
        infile: file,
      }
    },
    columns:[
      {"data": "Interval", name:"Interval"},
      {"data": "uniqID", name:"uniqID"},
      {"data": "chr", name:"chr"},
      {"data": "pos", name:"bp"},
      {"data": "ref", name:"ref"},
      {"data": "alt", name:"alt"},
      {"data": "annot", name:"Annotation"},
      {"data": "gene", name:"Gene"},
      {"data": "MAF", name:"MAF"},
      {"data": "MAF_FIN", name:"MAF(FIN)"},
      {"data": "MAF_NFE", name:"MAF(NFE)"},
      {"data": "MAF_AMR", name:"MAF(AMR)"},
      {"data": "MAF_AFR", name:"MAF(AFR)"},
      {"data": "MAF_EAS", name:"MAF(EAS)"},
      {"data": "MAF_SAS", name:"MAF(SAS)"},
      {"data": "MAF_OTH", name:"MAF(OTH)"},
    ],
    "order": [[2, 'asc'], [3, 'asc']],
    "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "iDisplayLength": 10,
    dom: 'lBfrtip',
    buttons: ['csv']
  });


  $('#leadSNPtable tbody').on('click', 'tr', function(){
    $('#plotClear').show();
    var rowI = leadTable.row(this).index();
    leadSNPtable_selected=rowI;
    if($('#annotPlotSelect_leadSNP').is(':checked')===true){
      $('#annotPlotSubmit').attr('disabled',false);
      document.getElementById('annotPlotSelect_leadSNP').value=leadSNPtable_selected;
    }
    var rowData = leadTable.row(rowI).data();
    var chr = rowData['chr'];
    // create plot space
    var colorScale = d3.scale.linear().domain([0.2,0.6,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
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
        .html(function(d){return "BP:"+d.pos+"<br/>P: "+d.gwasP;});
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

    var out = "<h5>Selected lead SNP</h5><table class='table table-striped'><tr><td>lead SNP</td><td>"+rowData["rsID"]
              +"</td></tr><tr><td>Chrom</td><td>"+rowData["chr"]+"</td></tr><tr><td>BP</td><td>"
              +rowData["pos"]+"</td></tr><tr><td>P-value</td><td>"+rowData["p"]+"</td></tr><tr><td>SNPs within LD</td><td>"
              +rowData["nSNPs"]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData["nGWASSNPs"]+"</td></tr>";
    $('#selectedLeadSNP').html(out);
  });

  $('#intervalTable tbody').on('click', 'tr', function(){
    $('#plotClear').show();
    var rowI = intervalTable.row(this).index();
    intervalTable_selected=rowI;
    if(document.getElementById('annotPlotSelect_interval').checked===true){
      document.getElementById('annotPlotSubmit').disabled=false;
      document.getElementById('annotPlotSelect_interval').value=intervalTable_selected;
    }
    var rowData = intervalTable.row(rowI).data();
    var chr = rowData['chr'];
    // create plot space
    var colorScale = d3.scale.linear().domain([0.2,0.6,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
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
        .html(function(d){return "BP:"+d.pos+"<br/>P: "+d.gwasP;});
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
        .style('fill', function(d){return colorScale(d.r2);})
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
          .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return colorScale(d.r2);}});
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
    var out = "<h5>Selected Interval</h5><table class='table table-striped'><tr><td>top lead SNP</td><td>"+rowData["toprsID"]
              +"</td></tr><tr><td>Chrom</td><td>"+rowData["chr"]+"</td></tr><tr><td>BP</td><td>"
              +rowData["pos"]+"</td></tr><tr><td>P-value</td><td>"+rowData["p"]+"</td></tr><tr><td>lead SNPs</td><td>"+rowData["nLeadSNPs"]
              +"</td></tr><tr><td>SNPs within LD</td><td>"
              +rowData["nSNPs"]+"</td></tr><tr><td>GWAS SNPs within LD</td><td>"+rowData["nGWASSNPs"]+"</td></tr>";

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
      .append("text").attr("transform", "rotate(-90)")
      .attr("dy", ".71em")
      .style("text-anchor", "end");
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
    var margin = {top:30, right: 30, bottom:50, left:150},
        width = 600,
        height = 20*y_element.length;
    var y = d3.scale.ordinal().domain(y_element).rangeRoundBands([0, height], 0.1);
    var yAxis = d3.svg.axis().scale(y).orient("left");
    var svg = d3.select('#intervalPlot').append('svg')
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
        .attr("transform", function (d) {return "rotate(-65)";})
        .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .call(yAxis)
        .append("text").attr("transform", "rotate(-90)")
        .attr("dy", ".71em")
        .style("text-anchor", "end");
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2)+","+0+")")
        .text("Intervals");
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+0+")")
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
        .attr("transform", function (d) {return "rotate(-65)";})
        .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .attr("transform", "translate("+currentWidth+",0)")
        .call(yAxis).selectAll("text").remove();
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+0+")")
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
        .attr("transform", function (d) {return "rotate(-65)";})
        .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .attr("transform", "translate("+currentWidth+",0)")
        .call(yAxis).selectAll("text").remove();
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+0+")")
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
        .attr("transform", function (d) {return "rotate(-65)";})
        .attr("dx","-.65em").attr("dy", "-.2em");
    svg.append('g').attr("class", "y axis")
        .attr("transform", "translate("+currentWidth+",0)")
        .call(yAxis).selectAll("text").remove();
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+0+")")
        .style("text-anchor", "middle")
        .text("#in genes");
    svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(currentWidth+eachWidth/2)+","+(height+margin.bottom-5)+")")
        .style("text-anchor", "middle")
        .text("#in genes");
  });
}

function annotSelect(i){
  if(i==="leadSNP"){
    if(document.getElementById('annotPlotSelect_leadSNP').checked===true){
      document.getElementById('annotPlotSelect_interval').checked=false;
      // document.getElementById('annotPlotSelect_SNP').checked=false;
      if(leadSNPtable_selected===null){
        document.getElementById('annotPlotSubmit').disabled=true;
        alert("Lead SNP is not selected.\nPlease select one from the table.")
      }else{
        document.getElementById('annotPlotSubmit').disabled=false;
        // document.getElementById('annotPlotSelect_leadSNP').value=leadSNPtable_selected;
      }
    }else{document.getElementById('annotPlotSubmit').disabled=true;}
  }else if(i==="interval"){
    if(document.getElementById('annotPlotSelect_interval').checked===true){
      document.getElementById('annotPlotSelect_leadSNP').checked=false;
      // document.getElementById('annotPlotSelect_SNP').checked=false;
      if(intervalTable_selected===null){
        document.getElementById('annotPlotSubmit').disabled=true;
        alert("Interval is not selected.\nPlease select one from the table.")
      }else{
        document.getElementById('annotPlotSubmit').disabled=false;
        document.getElementById('annotPlotSelect_interval').value=intervalTable_selected;
      }
    }else{document.getElementById('annotPlotSubmit').disabled=true;}
  }
  // }else if(i==="SNP"){
  //   if(document.getElementById('annotPlotSelect_SNP').checked===true){
  //     document.getElementById('annotPlotSelect_leadSNP').checked=false;
  //     document.getElementById('annotPlotSelect_interval').checked=false;
  //     if(SNPtable_selected===null){
  //       document.getElementById('annotPlotSubmit').disabled=true;
  //       alert("SNP is not selected.\nPlease select one from the table.")
  //     }else{
  //       document.getElementById('annotPlotSubmit').disabled=false;
  //       document.getElementById('annotPlotSelect_SNP').value=SNPtable_selected;
  //     }
  //   }else{document.getElementById('annotPlotSubmit').disabled=true;}
  // }
  if(document.getElementById('annotPlot_Chrom15').checked==true){
    $('#annotPlotChr15Opt').show();
  }else{
    $('#annotPlotChr15Opt').hide();
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
  var exacfile = document.getElementById('exacfile').checked;
  if(paramfile || leadfile || intervalfile || snpsfile || annovfile || annotfile || genefile || eqtlfile || exacfile){
    document.getElementById('download').disabled=false;
  }
}
