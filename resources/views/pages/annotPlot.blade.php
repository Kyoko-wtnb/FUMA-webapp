<!-- <html> -->
@extends('layouts.simple')

<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="https://d3js.org/queue.v1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  var filedir = IPGAPvar.filedir;
  var jobID = IPGAPvar.jobID;
  var type = IPGAPvar.type;
  var rowI = IPGAPvar.rowI;
  var chr = IPGAPvar.chr;
  var GWASplot = IPGAPvar.GWASplot;
  var CADDplot = IPGAPvar.CADDplot;
  var RDBplot = IPGAPvar.RDBplot;
  var Chr15 = IPGAPvar.Chr15;
  var Chr15cells = IPGAPvar.Chr15cells;
  var eqtl = IPGAPvar.eqtl;
  var eqtlplot = IPGAPvar.eqtlplot;
  var eqtlNgenes = IPGAPvar.eqtlNgenes;
  var xMin = IPGAPvar.xMin;
  var xMax = IPGAPvar.xMax;
  var xMin_init = IPGAPvar.xMin_init;
  var xMax_init = IPGAPvar.xMax_init;
  // $('#test').html("<p>xMax: "+xMax+" xMin: "+xMin+"</p>");
  var margin = {top:50, right:250, left:50, bottom:100},
      height = (GWASplot*1+Chr15*1)*210+(CADDplot*1+RDBplot*1)*160+60+eqtl*(eqtlNgenes*55),
      width = 600;
  var side = (xMax_init*1-xMin_init*1)*0.05;
  var currentHeight=0;
  var x = d3.scale.linear().range([0, width]);
  var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
  x.domain([(xMin_init*1-side), (xMax_init*1+side)]);
  var svg = d3.select('#annotPlot').append('svg')
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");

  // zoom
  var zoom = d3.behavior.zoom().x(x).scaleExtent([0,1000]).on("zoom", zoomed);
  svg.call(zoom);

  // define colors
  var colorScale = d3.scale.linear().domain([0.2,0.6,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
  var Chr15colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];
  var cols = ['rgb(0,10,255)', 'rgb(0,38,255)', 'rgb(0,67,255)', 'rgb(0,96,255)', 'rgb(0,125,255)', 'rgb(0,154,255)', 'rgb(0,183,255)',
              'rgb(0,212,255)', 'rgb(0,241,255)', 'rgb(0,255,10)', 'rgb(0,255,38)', 'rgb(0,255,67)', 'rgb(0,255,96)', 'rgb(0,255,125)',
              'rgb(0,255,154)', 'rgb(0,255,183)', 'rgb(0,255,212)', 'rgb(0,255,241)', 'rgb(19,0,255)', 'rgb(19,255,0)', 'rgb(48,0,255)',
              'rgb(48,255,0)', 'rgb(77,0,255)', 'rgb(77,255,0)', 'rgb(106,0,255)', 'rgb(106,255,0)', 'rgb(135,0,255)', 'rgb(135,255,0)',
              'rgb(164,0,255)', 'rgb(164,255,0)', 'rgb(192,0,255)', 'rgb(192,255,0)', 'rgb(221,0,255)', 'rgb(221,255,0)', 'rgb(250,0,255)',
              'rgb(250,255,0)', 'rgb(255,0,0)', 'rgb(255,0,29)', 'rgb(255,0,58)', 'rgb(255,0,87)', 'rgb(255,0,115)', 'rgb(255,0,144)',
              'rgb(255,0,173)', 'rgb(255,0,202)', 'rgb(255,0,231)', 'rgb(255,29,0)', 'rgb(255,58,0)', 'rgb(255,87,0)', 'rgb(255,115,0)',
              'rgb(255,144,0)', 'rgb(255,173,0)', 'rgb(255,202,0)', 'rgb(255,231,0)', 'rgb(68, 36, 17)', 'rgb(47, 48, 51)'];
  var ts = ["Adipose_Subcutaneous", "Adipose_Visceral_Omentum", "Adrenal_Gland", "Bladder",
            "Cells_EBV-transformed_lymphocytes", "Whole_Blood", "Artery_Aorta", "Artery_Coronary",
            "Artery_Tibial", "Brain_Amygdala", "Brain_Anterior_cingulate_cortex_BA24", "Brain_Caudate_basal_ganglia",
            "Brain_Cerebellar_Hemisphere", "Brain_Cerebellum", "Brain_Cortex", "Brain_Frontal_Cortex_BA9",
            "Brain_Hippocampus", "Brain_Hypothalamus", "Brain_Nucleus_accumbens_basal_ganglia", "Brain_Putamen_basal_ganglia",
            "Brain_Spinal_cord_cervical_c-1", "Brain_Substantia_nigra", "Breast_Mammary_Tissue", "Cervix_Ectocervix",
            "Cervix_Endocervix", "Colon_Sigmoid", "Colon_Transverse", "Esophagus_Gastroesophageal_Junction",
            "Esophagus_Mucosa", "Esophagus_Muscularis", "Fallopian_Tube", "Heart_Atrial_Appendage",
            "Heart_Left_Ventricle", "Kidney_Cortex", "Liver", "Lung", "Muscle_Skeletal", "Nerve_Tibial",
            "Ovary", "Pancreas", "Pituitary", "Prostate"," Minor_Salivary_Gland", "Cells_Transformed_fibroblasts",
            "Skin_Not_Sun_Exposed_Suprapubic", "Skin_Sun_Exposed_Lower_leg", "Small_Intestine_Terminal_Ileum", "Spleen",
            "Stomach", "Testis", "Thyroid", "Uterus", "Vagina", "BloodeQTL", "BIOS_eQTL_geneLevel"];
  var eQTLcolors = {};
  for(i=0; i<cols.length; i++){
    eQTLcolors[ts[i]] = cols[i];
  }

  // vertical line
  var vertical = svg.append("rect")
                    .attr("class", "vertical")
                    .attr("z-index", "500")
                    .attr("width", 5)
                    .attr("height", height+25)
                    .attr("x", 0)
                    .attr("y", -25)
                    .style("fill", "transparent");

  svg.append("rect")
    .attr("width", width).attr("height", height)
    .style("fill", "transparent")
    .style("shape-rendering", "crispEdges")
    .on("mousemove", function(){
      var mousex = d3.mouse(this)[0];
      vertical.attr("x", mousex);
    })
    .on("mouseover", function(){
      var mousex = d3.mouse(this)[0];
      vertical.attr("x", mousex).style("stroke", "grey");
    })
    .on("mouseout", function(){
      vertical.style("stroke", "transparent")
    });

  // Plots
  // if((Chr15==0 & eqtl==0) | (Chr15==0 & eqtl==1 & eqtlplot==0)){
  //   queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/exons.txt")
  //         .awaitAll(function(error, data){
  //           var data_annot = data[0];
  //           var data_genes = data[1];
  //           var data_exons = data[2];
  //           data_annot.forEach(function(d){
  //             d.pos = +d.pos;
  //             d.logP = +d.logP;
  //             d.r2 = +d.r2;
  //             d.ld = +d.ld;
  //             d.CADD = +d.CADD;
  //           });
  //           currentHeight = PlotAnnot(data_annot, currentHeight, GWASplot, CADDplot, RDBplot);
  //           PlotGenes(data_genes, data_exon, currentHeight);
  //           if(eqtl==1){
  //
  //           }
  //         });
  // }else if((Chr15==1 & eqtl==0) | (Chr15==1 & eqtl==1 & eqtlplot==0)){
  //   queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/exons.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/Chr15.txt")
  //         .awaitAll(function(error, data){
  //           var data_annot = data[0];
  //           var data_genes = data[1];
  //           var data_exons = data[2];
  //           var data_chr15 = data[3];
  //         });
  // }else if(Chr15==0 & eqtl==1 & eqtlplot==1){
  //   queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/exons.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/eqtlplot.txt")
  //         .awaitAll(function(error, data){
  //           var data_annot = data[0];
  //           var data_genes = data[1];
  //           var data_exons = data[2];
  //           var data_eqtl = data[3];
  //         });
  // }else if(Chr15==1 & eqtl==1 & eqtlplot==1){
  //   queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/exons.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/Chr15.txt")
  //         .defer(d3.json, "d3text/"+jobID+"/eqtlplot.txt")
  //         .awaitAll(function(error, data){
  //           var data_annot = data[0];
  //           var data_genes = data[1];
  //           var data_exons = data[2];
  //           var data_chr15 = data[3];
  //           var data_eqtl = data[4];
  //         });
  // }
  var data1;
  var xaxistext="genes";
  queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
        .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
        .defer(d3.json, "d3text/"+jobID+"/exons.txt")
        //.defer(d3.tsv, filedir+"Chr15.txt")
        .awaitAll(function(error, data){
          data1 = data[0];
          var data2 = data[1];
          var data3 = data[2];
          //var data4 = data[3];
          data1.forEach(function(d){
            d.pos = +d.pos;
            d.logP = +d.logP;
            d.r2 = +d.r2;
            d.ld = +d.ld;
            d.CADD = +d.CADD;
          });
          if(GWASplot==1){
            var y = d3.scale.linear().range([currentHeight+200, currentHeight]);
            var yAxis = d3.svg.axis().scale(y).orient("left");
            var legData = [];
            for(i=10; i>1; i--){
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

            y.domain([0, d3.max(data1, function(d){return d.logP})+1]);
            svg.selectAll("dot").data(data1.filter(function(d){if(d.gwasP!=="NA"){return d;}})).enter()
              .append("circle")
              .attr("class", "GWASdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(d.logP);})
              .style("fill", function(d){return colorScale(d.r2)})
              .on("click", function(d){
                table = '<table class="table" style="font-size: 10;">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>lead SNPs</td><td>'+d.leadSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  for(var i=0; i<cells.length; i++){
                    table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
                  }
                }
                if(eqtl==1 & eqtlplot==1){
                  table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
                }
                table += '</table>'
                $('#annotTable').html(table);
              });
            svg.selectAll('rect.KGSNPs').data(data1.filter(function(d){if(d.gwasP==="NA"){return d;}})).enter()
              .append("rect")
              .attr("class", "KGSNPs")
              .attr("x", function(d){return x(d.pos)})
              .attr("y", -20)
              .attr("width", "3")
              .attr("height", "10")
              .style("fill", function(d){return colorScale(d.r2)})
              .on("click", function(d){
                table = '<table class="table" style="font-size: 10;">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>lead SNPs</td><td>'+d.leadSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  for(var i=0; i<cells.length; i++){
                    table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
                  }
                }
                if(eqtl==1 & eqtlplot==1){
                  table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
                }
                table += '</table>'
                $('#annotTable').html(table);
              });
            // lead SNPs
            svg.selectAll("dot.leadSNPs").data(data1.filter(function(d){if(d.ld==2){return d;}})).enter()
              .append("circle")
              .attr("class", "leadSNPs")
              .attr("cx", function(d){return x(d.pos)})
              .attr("cy", function(d){return y(d.logP);})
              .attr("r", 4.5)
              .style("fill", "purple").style("stroke", "black")
              .on("click", function(d){
                table = '<table class="table" style="font-size: 10;">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>lead SNPs</td><td>'+d.leadSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  for(var i=0; i<cells.length; i++){
                    table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
                  }
                }
                if(eqtl==1 & eqtlplot==1){
                  table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
                }
                table += '</table>'
                $('#annotTable').html(table);
              });

            // function mousemove(){
            //   var x0 = x.invert(d3.mouse(this)[0]),
            //       i = bisectD
            // }
            svg.append("g").attr("class", "x axis GWAS")
              .attr("transform", "translate(0,"+(currentHeight+200)+")")
              .call(xAxis).selectAll("text").remove();
            svg.append("g").attr("class", "y axis").call(yAxis);
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-margin.left/2)+","+(currentHeight+100)+")rotate(-90)")
              .text("-log10 P-value");
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-margin.left/2)+", -15)")
              .style("font-size", "8px")
              .text("1000G SNPs");
            currentHeight = currentHeight+210;
          }
          currentHeight = currentHeight+60;
          if(CADDplot==1){
            var y = d3.scale.linear().range([currentHeight+150, currentHeight]);
            var yAxis = d3.svg.axis().scale(y).orient("left");
            y.domain([0, d3.max(data1, function(d){return d.CADD})+1]);
            svg.selectAll("dot").data(data1).enter()
              .append("circle")
              .attr("class", "CADDdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(d.CADD);})
              .style("fill", "skyblue")
              .on("click", function(d){
                table = '<table class="table" style="font-size: 10;">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>lead SNPs</td><td>'+d.leadSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  for(var i=0; i<cells.length; i++){
                    table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
                  }
                }
                if(eqtl==1 & eqtlplot==1){
                  table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
                }
                table += '</table>'
                $('#annotTable').html(table);
              });
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-margin.left/2)+","+(currentHeight+75)+")rotate(-90)")
              .text("CADD score");
            if(RDBplot==1 || Chr15==1 || (eqtl==1 && eqtlplot==1)){
              svg.append("g").attr("class", "x axis CADD")
                .attr("transform", "translate(0,"+(currentHeight+150)+")")
                .call(xAxis).selectAll("text").remove();
            }else{
              svg.append("g").attr("class", "x axis CADD")
                .attr("transform", "translate(0,"+(currentHeight+150)+")")
                .call(xAxis);
              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+width/2+","+(currentHeight+160+30)+")")
                .text("Chromosome "+chr);
            }
            svg.append("g").attr("class", "y axis").call(yAxis);
            currentHeight = currentHeight+160;
          }
          if(RDBplot==1){
            var y_element = ["1a", "1b", "1c", "1d", "1e", "1f", "2a", "2b" ,"2c", "3a", "3b", "4", "5", "6", "7"];
            var y = d3.scale.ordinal().domain(y_element).rangePoints([currentHeight, currentHeight+150]);
            var yAxis = d3.svg.axis().scale(y).tickFormat(function(d){return d;}).orient("left");
            svg.selectAll("dot").data(data1.filter(function(d){if(d.RDB!=="NA"){return d;}})).enter()
              .append("circle")
              .attr("class", "RDBdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(d.RDB);})
              .style("fill", "MediumAquaMarine")
              .on("click", function(d){
                table = '<table class="table" style="font-size: 10;">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>lead SNPs</td><td>'+d.leadSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  for(var i=0; i<cells.length; i++){
                    table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
                  }
                }
                if(eqtl==1 & eqtlplot==1){
                  table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
                }
                table += '</table>'
                $('#annotTable').html(table);
              });            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-margin.left/2)+","+(currentHeight+75)+")rotate(-90)")
              .text("RegulomeDB score");
            if(Chr15==1 || (eqtl==1 && eqtlplot==1)){
              svg.append("g").attr("class", "x axis RDB")
                .attr("transform", "translate(0,"+(currentHeight+150)+")")
                .call(xAxis).selectAll("text").remove();
            }else{
              svg.append("g").attr("class", "x axis RDB")
                .attr("transform", "translate(0,"+(currentHeight+150)+")")
                .call(xAxis);
              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+width/2+","+(currentHeight+160+30)+")")
                .text("Chromosome "+chr);
            }
            svg.append("g").attr("class", "y axis").call(yAxis);
            currentHeight = currentHeight+160;
          }
          if(GWASplot==1){var ch = 210+60;}
          else{var ch = 60;}
          var y = d3.scale.linear().range([ch-10, ch-60]);
          //gene plot
          data2.forEach(function(d){
            d.start_position = +d.start_position;
            d.end_position = +d.end_position;
            d.strand = +d.strand;
          });
          y.domain([-2.5,2.5]);

          svg.selectAll('rect.gene').data(data2).enter().append("g")
            .insert('rect').attr("class", "cell").attr("class", "genesrect")
            .attr("x", function(d){
              if(x(d.start_position)<0 || x(d.end_position)<0){return 0;}
              else{return x(d.start_position);}
            })
            .attr("y", function(d){return y(d.strand)})
            .attr("width", function(d){
              if(x(d.end_position)<0 || x(d.start_position)>width){return 0;}
              else if(x(d.start_position)<0 && x(d.end_position)>width){return width;}
              else if(x(d.start_position)<0){return x(d.end_position);}
              else if(x(d.end_position)>width){return width-x(d.start_position);}
              else{return x(d.end_position)-x(d.start_position)}
            })
            .attr("height", 1)
            .attr("fill", function(d){
              if(x(d.end)<0 || x(d.start)>width){return "transparent";}
              else{return "blue";}
            });
          svg.selectAll("text.genes").data(data2).enter()
            .append("text").attr("class", "geneName").attr("text-anchor", "middle")
            .attr("x", function(d){
              if(x(d.start_position)<0 && x(d.end_position)>width){return width/2;}
              else if(x(d.start_position)<0){return x(d.end_position)/2;}
              else if(x(d.end_position)>width){return x(d.start_position)+(width-x(d.start_position))/2;}
              else{return x(((d.end_position-d.start_position)/2)+d.start_position);}
            })
            .attr("y", function(d){return y(d.strand);})
            .attr("dy", "-.7em")
            .text(function(d){return d.external_gene_name;})
            .style("font-size", "9px")
            .style("font-family", "sans-serif")
            .style("fill", function(d){
              if(x(d.end_position)<0 || x(d.start_position)>width){return "transparent";}
              else{return "black";}
            });
          if(CADDplot==1 || RDBplot==1 || Chr15==1 || (eqtl==1 && eqtlplot==1)){
            svg.append("g").attr("class", "x axis genes")
              .attr("transform", "translate(0,"+(ch-10)+")")
              .call(xAxis).selectAll("text").remove();
          }else{
            svg.append("g").attr("class", "x axis genes")
              .attr("transform", "translate(0,"+(ch-10)+")")
              .call(xAxis);
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+width/2+","+(currentHeight+30)+")")
              .text("Chromosome "+chr);
          }
          // svg.append("g").attr("class", "y axis").call(yAxis)
          //   .selectAll("text").remove();

          //exon plot
          data3.forEach(function(d){
            d.exon_chrom_start = +d.exon_chrom_start;
            d.exon_chrom_end = +d.exon_chrom_end;
            d.strand = +d.strand;
          });
          svg.selectAll('rect.exon').data(data3).enter().append("g")
            .insert('rect').attr("class", "cell").attr("class", "exons")
            .attr("x", function(d){
              if(x(d.exon_chrom_start)<0 || x(d.exon_chrom_end)<0){return 0;}
              else{return x(d.exon_chrom_start);}
            })
            .attr("y", function(d){return y(d.strand)-4.5})
            .attr("width", function(d){
              if(x(d.exon_chrom_end)<0 || x(d.exon_chrom_start)>width){return 0;}
              else if(x(d.exon_chrom_start)<0 && x(d.exon_chrom_end)>width){return width;}
              else if(x(d.exon_chrom_start)<0){return x(d.exon_chrom_end);}
              else if(x(d.exon_chrom_end)>width){return width-x(d.exon_chrom_start);}
              else{return x(d.exon_chrom_end)-x(d.exon_chrom_start);}
            })
            .attr("height", 9)
            .attr("fill", function(d){
              if(x(d.exon_chrom_start)>width || x(d.exon_chrom_end)<0){return "transparent";}
              else{return "blue";}
            });

            if(Chr15==0 && eqtl==1 && eqtlplot==0){
              svg.append("text").attr("text-anchor", "middle")
                  .attr("transform", "translate("+(width/2)+","+(currentHeight+margin.bottom-30)+")")
                  .text("No eQTL of selected tissues exists in this region.");
            }
      });

  if(Chr15==1 && eqtl==1 && eqtlplot==1){
    queue().defer(d3.json, "d3text/"+jobID+"/Chr15.txt")
    .defer(d3.json, "d3text/"+jobID+"/eqtlplot.txt")
    .awaitAll(function(error, data){
      var data1 = data[0];
      var data2 = data[1];
      data1.forEach(function(d){
        d.start = +d.start;
        d.end = +d.end;
        d.state = +d.state;
      });
      // var colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];

      var y_element = d3.set(data1.map(function(d){return d.cell;})).values();
      var tileHeight = 10;
      if(y_element.length>20){
        tileHeight = 200/y_element.length;
      }
      var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([currentHeight, currentHeight+y_element.length*tileHeight]);
      var yAxisChr15 = d3.svg.axis().scale(yChr15).tickFormat(function(d){return d;}).orient("left");

      var states = ["TssA", "TssAFlnk", "TxFlnk", "Tx", "Tx/Wk", "EnhG", "Enh", "ZNF/Rpts", "Het", "TssBiv", "BivFlnk", "EnhBiv", "ReprPC", "ReprPCWk", "Quies"];
      var legData = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
      var legendChr15 = svg.selectAll(".legendChr15")
        .data(legData)
        .enter()
        .append("g").attr("class", "legend");
      if(y_element.length>10){
        var legHead = currentHeight+y_element.length*tileHeight/2-8*7.5;
        legendChr15.append("rect")
          .attr("x", width+10)
          .attr("y", function(d){return legHead+d*8;})
          .attr("width", 20)
          .attr("height", 8)
          .style("fill", function(d){return Chr15colors[d]})
          .style("stroke", "grey")
          .style("stroke-width", "0.5");
        legendChr15.append("text")
          .attr("text-anchor", "start")
          .attr("x", width+10+22)
          .attr("y", function(d){return legHead+d*8+8;})
          .text(function(d){return states[d]})
          .style("font-size", "9px");
      }else{
        var legHead = currentHeight+y_element.length*tileHeight/2-8*4;
        legendChr15.append("rect")
          .attr("x", function(d){if(d<7){return width+10;}else{return width+70;}})
          .attr("y", function(d){if(d<7){return legHead+d*8;}else{return legHead+(d-7)*8}})
          .attr("width", 20)
          .attr("height", 8)
          .style("fill", function(d){return Chr15colors[d]})
          .style("stroke", "grey")
          .style("stroke-width", "0.5");
        legendChr15.append("text")
          .attr("text-anchor", "start")
          .attr("x", function(d){if(d<7){return width+10+22;}else{return width+70+22;}})
          .attr("y", function(d){if(d<7){return legHead+d*8+8;}else{return legHead+(d-7)*8+8}})
          .text(function(d){return states[d]})
          .style("font-size", "9px");
      }

      svg.selectAll("rect.chr").data(data1).enter().append("g")
        .insert('rect').attr('class', 'cell').attr("class", "Chr15rect")
        .attr("width", function(d){return x(d.end)-x(d.start);})
        .attr("height", tileHeight)
        .attr('x', function(d){return x(d.start);})
        .attr('y', function(d){return yChr15(d.cell)})
        .attr("fill", function(d){return Chr15colors[d.state*1-1];})
        .on("mousemove", function(){
          var mousex = d3.mouse(this)[0];
          vertical.attr("x", mousex);
        })
        .on("mouseover", function(){
          var mousex = d3.mouse(this)[0];
          vertical.attr("x", mousex).style("stroke", "grey");
        })
        .on("mouseout", function(){
          vertical.style("stroke", "transparent")
        });
      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2-15)+","+(currentHeight+(y_element.length*tileHeight+10)/2)+")rotate(-90)")
        .text("Chromatin state");

      svg.append("g").attr("class", "x axis Chr15")
        .attr("transform", "translate(0,"+(currentHeight+y_element.length*tileHeight)+")")
        .call(xAxis).selectAll("text").remove();
      svg.append("g").attr("class", "y axis").call(yAxisChr15);
      currentHeight = currentHeight+y_element.length*tileHeight+10;

      data2.forEach(function(d){
        d.pos = +d.pos;
        d.logP = +d.logP;
      });
      var genes = d3.set(data2.map(function(d){return d.symbol;})).values();
      var tissue = d3.set(data2.map(function(d){return d.tissue;})).values();
      // neqtl = genes.length;

      var legData = [];
      for(i=0; i<tissue.length; i++){
        legData.push(i);
      }
      var legendEqtl = svg.selectAll(".legendEqtl")
        .data(legData)
        .enter()
        .append("g").attr("class", "legend")
      legendEqtl.append("circle")
        .attr("r", 3.5)
        .attr("cx", width+10)
        .attr("cy", function(d){return currentHeight+15+d*10})
        .style("fill", function(d){return eQTLcolors[tissue[d]]});
      legendEqtl.append("text")
        .attr("text-anchor", "start")
        .attr("x", width+15)
        .attr("y", function(d){return currentHeight+18+d*10;})
        .text(function(d){return tissue[d]})
        .style("font-size", "10px");
      for(i=0; i<genes.length; i++){
        var y = d3.scale.linear().range([currentHeight+50, currentHeight]);
        var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
        y.domain([0, d3.max(data2, function(d){return d.logP})+0.5]);
        svg.selectAll("dot").data(data2.filter(function(d){if(d.symbol===genes[i]){return d}})).enter()
          .append("circle").attr("class", "eqtldot")
          .attr("r", 3.5)
          .attr("cx", function(d){return x(d.pos);})
          .attr("cy", function(d){return y(d.logP);})
          .style("fill", function(d){return eQTLcolors[d.tissue]})
          .on("click", function(d){
            
          });
        svg.append("text").attr("text-anchor", "middle")
          .attr("transform", "translate("+(-margin.left/2)+","+(currentHeight+25)+")rotate(-90)")
          .text(genes[i])
          .style("font-size", "10px");
        if(i==genes.length-1){
          svg.append("g").attr("class", "x axis eqtlend")
            .attr("transform", "translate(0,"+(currentHeight+50)+")")
            .call(xAxis);
          svg.append("text").attr("text-anchor", "middle")
            .attr("transform", "translate("+width/2+","+(currentHeight+55+30)+")")
            .text("Chromosome "+chr);
        }else{
          // eqtlclass = "eqtl"+i;
          svg.append("rect")
            .attr("x", 0).attr("y", y(0))
            .attr("width", width).attr("height", 0.5)
            .style("fill", "transparent")
            .style("stroke", "grey");
          // svg.append("g").attr("class", "x axis eqtl"+i)
          //   // .attr("class", eqtlclass)
          //   .attr("transform", "translate(0,"+(currentHeight+50)+")")
          //   .call(xAxis).selectAll("text").remove();
        }
        svg.append("g").attr("class", "y axis").call(yAxis);
        currentHeight = currentHeight+55;
      }

      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2-15)+","+(currentHeight-30*genes.length)+")rotate(-90)")
        .text("eQTL -log10 P-value")
        .style("font-size", "10px");
    });

  }else if(Chr15==1 && eqtlplot==0){
    queue().defer(d3.json, "d3text/"+jobID+"/Chr15.txt")
    .awaitAll(function(error, data){
      var data1 = data[0];
      data1.forEach(function(d){
        d.start = +d.start;
        d.end = +d.end;
        d.state = +d.state;
      });
      // var colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];
      var y_element = d3.set(data1.map(function(d){return d.cell;})).values();
      var tileHeight = 10;
      if(y_element.length>20){
        tileHeight = 200/y_element.length;
      }
      var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([currentHeight, currentHeight+y_element.length*tileHeight]);
      var yAxisChr15 = d3.svg.axis().scale(yChr15).tickFormat(function(d){return d;}).orient("left");

      var states = ["TssA", "TssAFlnk", "TxFlnk", "Tx", "Tx/Wk", "EnhG", "Enh", "ZNF/Rpts", "Het", "TssBiv", "BivFlnk", "EnhBiv", "ReprPC", "ReprPCWk", "Quies"];
      var legData = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
      var legendChr15 = svg.selectAll(".legendChr15")
        .data(legData)
        .enter()
        .append("g").attr("class", "legend");
      if(y_element.length>10){
        var legHead = currentHeight+y_element.length*tileHeight/2-8*7.5;
        legendChr15.append("rect")
          .attr("x", width+10)
          .attr("y", function(d){return legHead+d*8;})
          .attr("width", 20)
          .attr("height", 8)
          .style("fill", function(d){return Chr15colors[d]})
          .style("stroke", "grey")
          .style("stroke-width", "0.5");
        legendChr15.append("text")
          .attr("text-anchor", "start")
          .attr("x", width+10+22)
          .attr("y", function(d){return legHead+d*8+8;})
          .text(function(d){return states[d]})
          .style("font-size", "9px");
      }else{
        var legHead = currentHeight+y_element.length*tileHeight/2-8*4;
        legendChr15.append("rect")
          .attr("x", function(d){if(d<7){return width+10;}else{return width+70;}})
          .attr("y", function(d){if(d<7){return legHead+d*8;}else{return legHead+(d-7)*8}})
          .attr("width", 20)
          .attr("height", 8)
          .style("fill", function(d){return Chr15colors[d]})
          .style("stroke", "grey")
          .style("stroke-width", "0.5");
        legendChr15.append("text")
          .attr("text-anchor", "start")
          .attr("x", function(d){if(d<7){return width+10+22;}else{return width+70+22;}})
          .attr("y", function(d){if(d<7){return legHead+d*8+8;}else{return legHead+(d-7)*8+8}})
          .text(function(d){return states[d]})
          .style("font-size", "9px");
      }

      svg.selectAll("rect.chr").data(data1).enter().append("g")
        .insert('rect').attr('class', 'cell').attr("class", "Chr15rect")
        .attr("width", function(d){return x(d.end)-x(d.start);})
        .attr("height", tileHeight)
        .attr('x', function(d){return x(d.start);})
        .attr('y', function(d){return yChr15(d.cell)})
        .attr("fill", function(d){return Chr15colors[d.state*1-1];})
        .on("mousemove", function(){
          var mousex = d3.mouse(this)[0];
          vertical.attr("x", mousex);
        })
        .on("mouseover", function(){
          var mousex = d3.mouse(this)[0];
          vertical.attr("x", mousex).style("stroke", "grey");
        })
        .on("mouseout", function(){
          vertical.style("stroke", "transparent")
        });
      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+(-margin.left/2-15)+","+(currentHeight+(y_element.length*tileHeight+10)/2)+")rotate(-90)")
        .text("Chromatin state");

      svg.append("g").attr("class", "x axis Chr15")
        .attr("transform", "translate(0,"+(currentHeight+y_element.length*tileHeight)+")")
        .call(xAxis);
      svg.append("g").attr("class", "y axis").call(yAxisChr15);
      currentHeight = currentHeight+y_element.length*tileHeight+10;
      svg.append("text").attr("text-anchor", "middle")
        .attr("transform", "translate("+width/2+","+(currentHeight+25)+")")
        .text("Chromosome "+chr);
      if(eqtl==1 && eqtlplot==0){
        svg.append("text").attr("text-anchor", "middle")
            .attr("transform", "translate("+(width/2)+","+(currentHeight+margin.bottom-30)+")")
            .text("No eQTL of selected tissues exists in this region.");
      }
    });
  }else if(eqtl==1 && eqtlplot==1){
      queue().defer(d3.json, "d3text/"+jobID+"/eqtlplot.txt")
      .awaitAll(function(error, data){
        var data = data[0];
        data.forEach(function(d){
          d.pos = +d.pos;
          d.logP = +d.logP;
        });
        var genes = d3.set(data.map(function(d){return d.symbol;})).values();
        var tissue = d3.set(data.map(function(d){return d.tissue;})).values();
        // neqtl = genes.length;

        var legData = [];
        for(i=0; i<tissue.length; i++){
          legData.push(i);
        }
        var legendEqtl = svg.selectAll(".legendEqtl")
          .data(legData)
          .enter()
          .append("g").attr("class", "legend")
        legendEqtl.append("circle")
          .attr("r", 3.5)
          .attr("cx", width+10)
          .attr("cy", function(d){return currentHeight+15+d*10})
          .style("fill", function(d){return eQTLcolors[tissue[d]]});
        legendEqtl.append("text")
          .attr("text-anchor", "start")
          .attr("x", width+15)
          .attr("y", function(d){return currentHeight+18+d*10;})
          .text(function(d){return tissue[d]})
          .style("font-size", "10px");

        for(i=0; i<genes.length; i++){
          var y = d3.scale.linear().range([currentHeight+50, currentHeight]);
          var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
          y.domain([0, d3.max(data, function(d){return d.logP})+0.5]);
          svg.selectAll("dot").data(data.filter(function(d){if(d.symbol===genes[i]){return d}})).enter()
            .append("circle").attr("class", "eqtldot")
            .attr("r", 3.5)
            .attr("cx", function(d){return x(d.pos);})
            .attr("cy", function(d){return y(d.logP);})
            .style("fill", function(d){return eQTLcolors[d.tissue]});
          svg.append("text").attr("text-anchor", "middle")
            .attr("transform", "translate("+(-margin.left/2)+","+(currentHeight+25)+")rotate(-90)")
            .text(genes[i])
            .style("font-size", "10px");
          if(i==genes.length-1){
            svg.append("g").attr("class", "x axis eqtlend")
              .attr("transform", "translate(0,"+(currentHeight+50)+")")
              .call(xAxis);
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+width/2+","+(currentHeight+55+30)+")")
              .text("Chromosome "+chr);
          }else{
            // eqtlclass = "eqtl"+i;
            svg.append("rect")
              .attr("x", 0).attr("y", y(0))
              .attr("width", width).attr("height", 1)
              .style("fill", "transparent")
              .style("stroke", "grey");

            // svg.append("g").attr("class", "x axis eqtl"+i)
            //   // .attr("class", eqtlclass)
            //   .attr("transform", "translate(0,"+(currentHeight+50)+")")
            //   .call(xAxis).selectAll("text").remove();
          }
          svg.append("g").attr("class", "y axis").call(yAxis);
          currentHeight = currentHeight+55;
        }
        svg.append("text").attr("text-anchor", "middle")
          .attr("transform", "translate("+(-margin.left/2-15)+","+(currentHeight-30*genes.length)+")rotate(-90)")
          .text("eQTL -log10 P-value")
          .style("font-size", "10px");
      });
  }

  // reset();

  function zoomed(){
    svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
    svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
    svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
    svg.select(".x.axis.RDB").call(xAxis).selectAll("text").remove();
    svg.select(".x.axis.Chr15").call(xAxis).selectAll("text").remove();
    // for(var i; i<neqtl-1; i++){
    //   svg.select("eqtl"+i).call(xAxis).selectAll("text").remove();
    // }
    svg.select(".x.axis.eqtlend").call(xAxis);
    svg.selectAll(".GWASdot").attr("cx", function(d){return x(d.pos);})
        .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return colorScale(d.r2)}});
    svg.selectAll(".KGSNPs").attr("x", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return colorScale(d.r2)}});
    svg.selectAll(".leadSNPs").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "purple"}})
      .style("stroke", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "black"}});
    svg.selectAll(".CADDdot").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "skyblue"}});
    svg.selectAll(".RDBdot").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "MediumAquaMarine"}});
    svg.selectAll(".genesrect").attr("x", function(d){
        if(x(d.start_position)<0 || x(d.end_position)<0){return 0;}
        else{return x(d.start_position);}
      })
      .attr("width", function(d){
        if(x(d.end_position)<0 || x(d.start_position)>width){return 0;}
        else if(x(d.end_position)>width && x(d.start_position)<0){return width;}
        else if(x(d.end_position)>width){return width-x(d.start_position);}
        else if(x(d.start_position)<0){return x(d.end_position);}
        else{return x(d.end_position)-x(d.start_position);}
      })
      .style("fill", function(d){if(x(d.end_position)<0 || x(d.start_position)>width){return "transparent";}else{return "blue"}});
    svg.selectAll(".geneName")
    .attr("x", function(d){
      if(x(d.start_position)<0 && x(d.end_position)>width){return width/2;}
      else if(x(d.start_position)<0){return x(d.end_position)/2;}
      else if(x(d.end_position)>width){return x(d.start_position)+(width-x(d.start_position))/2;}
      else{return x(((d.end_position-d.start_position)/2)+d.start_position);}
    })
    .style("fill", function(d){
      if(x(d.end_position)<0 || x(d.start_position)>width){return "transparent";}
      else{return "black";}
    });
    svg.selectAll(".exons").attr("x", function(d){
        if(x(d.exon_chrom_start)<0 || x(d.exon_chrom_end)<0){return 0;}
        else{return x(d.exon_chrom_start);}
      })
      .attr("width", function(d){
        if(x(d.exon_chrom_end)<0 || x(d.exon_chrom_start)>width){return 0;}
        else if(x(d.exon_chrom_start)<0 && x(d.exon_chrom_end)>width){return width;}
        else if(x(d.exon_chrom_end)>width){return width-x(d.exon_chrom_start);}
        else if(x(d.exon_chrom_start)<0){return x(d.exon_chrom_end);}
        else{return x(d.exon_chrom_end)-x(d.exon_chrom_start);}
      })
      .style("fill", function(d){if(x(d.exon_chrom_end)<0 || x(d.exon_chrom_start)>width){return "transparent";}else{return "blue";}})
    svg.selectAll(".Chr15rect")
      .attr("x", function(d){
        if(x(d.start)<0 || x(d.end)<0){return 0;}
        else{return x(d.start);}
      })
      .attr("width", function(d){
        if(x(d.end)<0 || x(d.start)>width){return 0;}
        else if(x(d.start)<0 && x(d.end)>width){return width;}
        else if(x(d.start)<0){return x(d.end);}
        else if(x(d.end)>width){return width-x(d.start);}
        else{return x(d.end)-x(d.start);}
      })
      .style("fill", function(d){
        if(x(d.end)<0 || x(d.start)>width){return "transparent";}
        else{return Chr15colors[d.state*1-1];}
      });
    svg.selectAll(".eqtldot").attr("cx", function(d){return x(d.pos)})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return eQTLcolors[d.tissue]}});
  }

  // Plot Clear button
  d3.select('#plotClear').on('click', reset);
  function reset(){
    d3.transition().duration(750).tween("zoom", function(){
      // var ix = d3.interpolate(x.domain(), [d3.min(data1, function(d){return d.pos})-side, d3.max(data1, function(d){return d.pos})+side]);
      var ix = d3.interpolate(x.domain(), [xMin_init*1-side, xMax_init*1+side]);
      return function(t){
        zoom.x(x.domain(ix(t)));
        zoomed();
      }
    });
  }
});
</script>

@section('content')

<div id="test"></div>
<!-- <h3>Annotplot head</h3> -->
<br/><br/>
<div class="row">
  <div class="col-md-8">
    <div id='title' style="text-align: center;"><h3>Regional plot</h3></div>
    <a id="plotclear" style="position: absolute;right: 30px;">Clear</a>
    <div id="annotPlot"></div>
  </div>
  <div class="col-md-4" style="text-align: center;">
    <h3>SNP annotations</h3>
    <div id="annotTable">
      click any SNP on the plot</br>
    </div>
  </div>
</div>

<br/><br/>
<!-- <h3>Annotplot end</h3> -->
@stop
<!-- </html> -->
