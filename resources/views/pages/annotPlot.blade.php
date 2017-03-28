<!-- <html> -->
@extends('layouts.simple')
@section('head')
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>

<script type="text/javascript">
  var jobID;
  var loggedin = "{{ Auth::check() }}";
$(document).ready(function(){
  $('.ImgDownSubmit').hide();

  var filedir = IPGAPvar.filedir;
  jobID = IPGAPvar.jobID;
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

  var margin = {top:50, right:250, left:60, bottom:100},
      // height = (GWASplot*1+Chr15*1)*210+(CADDplot*1+RDBplot*1)*160+60+eqtl*(eqtlNgenes*55),
      width = 600;
  var side = (xMax_init*1-xMin_init*1)*0.05;
  if(side==0){side=500;}
  // var currentHeight=0;
  // var currentHeight2 = (200+10)*GWASplot+50+10+(150+10)*CADDplot+(150+10)*RDBplot;
  var xAxisLabel = "gene";

  var height;
  // height of each plot
  var genesHeight;
  var gwasHeight=200;
  var caddHeight=150;
  var rdbHeight=150;
  var chrHeight;
  var eqtlHeight;
  // min Y for each plot
  var gwasTop = 0;
  var genesTop = (gwasHeight+gwasTop+10)*GWASplot;
  var caddTop;
  var rdbTop;
  var chrTop;
  var eqtlTop;

  var x = d3.scale.linear().range([0, width]);
  var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
  x.domain([(xMin_init*1-side), (xMax_init*1+side)]);

  var svg;
  var zoom;

  // define colors
  var colorScale = d3.scale.linear().domain([0.0,0.5,1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
  var Chr15colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];
  var Chr15eid = ["E017", "E002", "E008", "E001", "E015", "E014", "E016", "E003", "E024", "E020", "E019", "E018", "E021",
                  "E022", "E007", "E009", "E010", "E013", "E012", "E011", "E004", "E005", "E006", "E062", "E034", "E045",
                  "E033", "E044", "E043", "E039", "E041", "E042", "E040", "E037", "E048", "E038", "E047", "E029", "E031",
                  "E035", "E051", "E050", "E036", "E032", "E046", "E030", "E026", "E049", "E025", "E023", "E052", "E055",
                  "E056", "E059", "E061", "E057", "E058", "E028", "E027", "E054", "E053", "E112", "E093", "E071", "E074",
                  "E068", "E069", "E072", "E067", "E073", "E070", "E082", "E081", "E063", "E100", "E108", "E107", "E089",
                  "E090", "E083", "E104", "E095", "E105", "E065", "E078", "E076", "E103", "E111", "E092", "E085", "E084",
                  "E109", "E106", "E075", "E101", "E102", "E110", "E077", "E079", "E094", "E099", "E086", "E088", "E097",
                  "E087", "E080", "E091", "E066", "E098", "E096", "E113", "E114", "E115", "E116", "E117", "E118", "E119",
                  "E120", "E121", "E122", "E123", "E124", "E125", "E126", "E127", "E128", "E129"];
  var Chr15group = ["IMR90", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "iPSC", "iPSC", "iPSC", "iPSC",
                   "iPSC", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv",
                   "ES-deriv", "ES-deriv", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
                   "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
                   "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
                   "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell",
                   "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "Mesench", "Mesench", "Mesench", "Mesench", "Myosat",
                   "Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial",
                   "Epithelial", "Neurosph", "Neurosph", "Thymus", "Thymus", "Brain", "Brain", "Brain", "Brain", "Brain",
                   "Brain", "Brain", "Brain", "Brain", "Brain", "Adipose", "Muscle", "Muscle", "Muscle", "Muscle", "Muscle",
                   "Heart", "Heart", "Heart", "Heart", "Heart", "Sm. Muscle", "Sm. Muscle", "Sm. Muscle", "Sm. Muscle",
                   "Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive",
                   "Digestive", "Digestive", "Digestive", "Digestive", "Other", "Other", "Other", "Other", "Other", "Other",
                   "Other", "Other", "Other", "Other", "Other", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012",
                   "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012",
                   "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012"];
  var Chr15GroupCols = ["#E41A1C", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965",
                        "#69608A", "#69608A", "#69608A", "#69608A", "#69608A", "#4178AE", "#4178AE", "#4178AE", "#4178AE",
                        "#4178AE", "#4178AE", "#4178AE", "#4178AE", "#4178AE", "#55A354", "#55A354", "#55A354", "#55A354",
                        "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354",
                        "#55A354", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69",
                        "#678C69", "#B65C73", "#B65C73", "#B65C73", "#B65C73", "#E67326", "#FF9D0C", "#FF9D0C", "#FF9D0C",
                        "#FF9D0C", "#FF9D0C", "#FF9D0C", "#FF9D0C", "#FF9D0C", "#FFD924", "#FFD924", "#DAB92E", "#DAB92E",
                        "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B",
                        "#C5912B", "#AF5B39", "#C2655D", "#C2655D", "#C2655D", "#C2655D", "#C2655D", "#D56F80", "#D56F80",
                        "#D56F80", "#D56F80", "#D56F80", "#F182BC", "#F182BC", "#F182BC", "#F182BC", "#C58DAA", "#C58DAA",
                        "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA",
                        "#C58DAA", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999",
                        "#999999", "#999999", "#999999", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000",
                        "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000",
                        "#000000"]
  //  var Chr15EidTs = {};
  //  for(i=0; i<Chr15eid.length; i++){
  //    Chr15EidTs[Chr15eid[i]] = Chr15ts[i];
  //  }


  var eqtlcols = ['rgb(0,10,255)', 'rgb(0,38,255)', 'rgb(0,67,255)', 'rgb(0,96,255)', 'rgb(0,125,255)', 'rgb(0,154,255)', 'rgb(0,183,255)',
              'rgb(0,212,255)', 'rgb(0,241,255)', 'rgb(0,255,10)', 'rgb(0,255,38)', 'rgb(0,255,67)', 'rgb(0,255,96)', 'rgb(0,255,125)',
              'rgb(0,255,154)', 'rgb(0,255,183)', 'rgb(0,255,212)', 'rgb(0,255,241)', 'rgb(19,0,255)', 'rgb(19,255,0)', 'rgb(48,0,255)',
              'rgb(48,255,0)', 'rgb(77,0,255)', 'rgb(77,255,0)', 'rgb(106,0,255)', 'rgb(106,255,0)', 'rgb(135,0,255)', 'rgb(135,255,0)',
              'rgb(164,0,255)', 'rgb(164,255,0)', 'rgb(192,0,255)', 'rgb(192,255,0)', 'rgb(221,0,255)', 'rgb(221,255,0)', 'rgb(250,0,255)',
              'rgb(250,255,0)', 'rgb(255,0,0)', 'rgb(255,0,29)', 'rgb(255,0,58)', 'rgb(255,0,87)', 'rgb(255,0,115)', 'rgb(255,0,144)',
              'rgb(255,0,173)', 'rgb(255,0,202)', 'rgb(255,0,231)', 'rgb(255,29,0)', 'rgb(255,58,0)', 'rgb(255,87,0)', 'rgb(255,115,0)',
              'rgb(255,144,0)', 'rgb(255,173,0)', 'rgb(255,202,0)', 'rgb(255,231,0)'
            ];
  var eqtlts = ["Adipose_Subcutaneous", "Adipose_Visceral_Omentum", "Adrenal_Gland", "Bladder",
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
  // for(i=0; i<eqtlcols.length; i++){
  //   eQTLcolors[eqtlts[i]] = eqtlcols[i];
  // }
  var propGenes;
  queue().defer(d3.json, "d3text/"+jobID+"/annotPlot.txt")
        .defer(d3.json, "d3text/"+jobID+"/genesplot.txt")
        .defer(d3.json, "d3text/"+jobID+"/exons.txt")
        .defer(d3.json, "d3text/"+jobID+"/temp.txt")
        .defer(d3.json, "getPrioGenes/"+jobID)
        .awaitAll(function(error, data){
          var data1 = data[0];
          var data2 = data[1];
          var data3 = data[2];
          var data4 = data[3];
          prioGenes = data[4];

          //gene plot
          data2.forEach(function(d){
            d.start_position = +d.start_position;
            d.end_position = +d.end_position;
            // d.strand = +d.strand;
            d.y = 1;
          });
          // console.log(data2[0]);
          data2 = geneOver(data2, x, width);

          // height define
          genesHeight = 20*(d3.max(data2, function(d){return d.y;})+1);
          caddTop = (genesTop+genesHeight+10);
          rdbTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot;
          chrTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot;
          var cells = Chr15cells.split(":");
          if(cells.length>30 || cells[0]=="all"){chrHeight=300;}
          else{chrHeight = 10*cells.length;}
          eqtlTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot+(chrHeight+10)*Chr15;
          eqtlHeight = eqtl*(eqtlNgenes*55);
          height = genesHeight+gwasHeight*GWASplot+caddHeight*CADDplot+rdbHeight*RDBplot+chrHeight*Chr15+eqtlHeight*eqtlplot+10*(GWASplot*1+CADDplot*1+RDBplot*1+Chr15*1+eqtlplot*1);

          // Prepare svg
          svg = d3.select('#annotPlot').append('svg')
                    .attr("width", width+margin.left+margin.right)
                    .attr("height", height+margin.top+margin.bottom)
                    .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");

          // zoom
          zoom = d3.behavior.zoom().x(x).scaleExtent([0,1000]).on("zoom", zoomed);
          svg.call(zoom);
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

          // genes plot
          var y = d3.scale.linear().range([genesTop+genesHeight, genesTop]);
          // y.domain([-2.5,2.5]);
          y.domain([d3.max(data2, function(d){return d.y;})+1, 0]);

          svg.append("rect").attr("x", width+20).attr("y", genesTop+10)
            .attr("width", 20).attr("height", 5).attr("fill", "red");
          svg.append("text").attr("x", width+45).attr("y", genesTop+15)
            .text("Mapped genes").style("font-size", "10px");
          svg.append("rect").attr("x", width+20).attr("y", genesTop+25)
            .attr("width", 20).attr("height", 5).attr("fill", "blue");
          svg.append("text").attr("x", width+45).attr("y", genesTop+30)
            .text("Non-mapped protein coding genes").style("font-size", "10px");
          svg.append("rect").attr("x", width+20).attr("y", genesTop+40)
            .attr("width", 20).attr("height", 5).attr("fill", "#383838");
          svg.append("text").attr("x", width+45).attr("y", genesTop+45)
            .text("Non-mapped non-coding genes").style("font-size", "10px");

          svg.selectAll('rect.gene').data(data2).enter().append("g")
            .insert('rect').attr("class", "cell").attr("class", "genesrect")
            .attr("x", function(d){
              if(x(d.start_position)<0 || x(d.end_position)<0){return 0;}
              else{return x(d.start_position);}
            })
            // .attr("y", function(d){return y(d.strand)})
            .attr("y", function(d){return y(d.y)})
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
              else if(prioGenes.indexOf(d.external_gene_name)>=0){return "red";}
              else if(d.gene_biotype=="protein_coding"){return "blue";}
              else{return "#383838"}
            });
          svg.selectAll("text.genes").data(data2).enter()
            .append("text").attr("class", "geneName").attr("text-anchor", "middle")
            .attr("x", function(d){
              if(x(d.start_position)<0 && x(d.end_position)>width){return width/2;}
              else if(x(d.start_position)<0){return x(d.end_position)/2;}
              else if(x(d.end_position)>width){return x(d.start_position)+(width-x(d.start_position))/2;}
              else{return x(((d.end_position-d.start_position)/2)+d.start_position);}
            })
            .attr("y", function(d){return y(d.y);})
            .attr("dy", "-.7em")
            .text(function(d){
              if(d.strand==1){
                return d.external_gene_name+"\u2192";
              }else{
                return "\u2190"+d.external_gene_name;
              }
            })
            .style("font-size", "9px")
            .style("font-family", "sans-serif")
            .style("fill", function(d){
              if(x(d.end_position)<0 || x(d.start_position)>width){return "transparent";}
              else{return "black";}
            });
          if(CADDplot==1 || RDBplot==1 || Chr15==1 || (eqtl==1 && eqtlplot==1)){
            svg.append("g").attr("class", "x axis genes")
              .attr("transform", "translate(0,"+(genesTop+genesHeight)+")")
              .call(xAxis).selectAll("text").remove();
          }else{
            xAxisLabel = "genes";
            svg.append("g").attr("class", "x axis genes")
              .attr("transform", "translate(0,"+(genesTop+genesHeight)+")")
              .call(xAxis)
              .selectAll('text').style('font-size', '11px');
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+width/2+","+(height+30)+")")
              .text("Chromosome "+chr);
          }
          // svg.append("g").attr("class", "y axis").call(yAxis)
          //   .selectAll("text").remove();

          //exon plot
          // var test = data2.filter(function(d2){if(d2.ensembl_gene_id=="ENSG00000140718"){return d2.y;}})[0].y;
          // console.log(test)
          data3.forEach(function(d){
            d.exon_chrom_start = +d.exon_chrom_start;
            d.exon_chrom_end = +d.exon_chrom_end;
            d.strand = +d.strand;
            d.y = data2.filter(function(d2){if(d2.ensembl_gene_id==d.ensembl_gene_id){return d2;}})[0].y;
          });
          svg.selectAll('rect.exon').data(data3).enter().append("g")
            .insert('rect').attr("class", "cell").attr("class", "exons")
            .attr("x", function(d){
              if(x(d.exon_chrom_start)<0 || x(d.exon_chrom_end)<0){return 0;}
              else{return x(d.exon_chrom_start);}
            })
            // .attr("y", function(d){return y(d.strand)-4.5})
            .attr("y", function(d){return y(d.y)-4.5})
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
              else if(prioGenes.indexOf(d.external_gene_name)>=0){return "red";}
              else if(d.gene_biotype=="protein_coding"){return "blue";}
              else{return "#383838"}
            });

            if(Chr15==0 && eqtl==1 && eqtlplot==0){
              svg.append("text").attr("text-anchor", "middle")
                  .attr("transform", "translate("+(width/2)+","+(height+margin.bottom-30)+")")
                  .text("No eQTL of selected tissues exists in this region.");
            }

          data1.forEach(function(d){
            d.pos = +d.pos;
            d.gwasP = +d.gwasP;
            d.r2 = +d.r2;
            d.ld = +d.ld;
            d.CADD = +d.CADD;
            d.posMapFilt = +d.posMapFilt;
            d.eqtlMapFilt = +d.eqtlMapFilt;
          });
          if(GWASplot==1){
            data4.forEach(function(d){
              d.pos = +d.pos;
              d.gwasP = +d.gwasP;
            });
            // var y = d3.scale.linear().range([currentHeight+200, currentHeight]);
            var y = d3.scale.linear().range([gwasTop+gwasHeight, gwasTop]);
            var yAxis = d3.svg.axis().scale(y).orient("left");
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
              .attr("x", width+20)
              .attr("y", function(d){return 10+(10-d*10)*10})
              .attr("width", 20)
              .attr("height", 10)
              .style("fill", function(d){return colorScale(d)});
            legendGwas.append("text")
              .attr("text-anchor", "start")
              .attr("x", width+42)
              .attr("y", function(d){return 20+(10-d*10)*10})
              .text(function(d){return Math.round(d*100)/100})
              .style("font-size", "10px");
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(width+30)+",5)")
              .text("r2").style("font-size", "10px");

            svg.append("circle")
              .attr("cx", width+20).attr("cy", 130).attr("r", 4.5)
              .style("fill", "#4d0099").style("stroke", "black").style("strole-width", "2");
            svg.append("text").attr("text-anchor", "bottom")
              .attr("x", width+30).attr("y", 133)
              .text("Top lead SNP").style("font-size", "10px");
            svg.append("circle")
              .attr("cx", width+20).attr("cy", 145).attr("r", 4)
              .style("fill", "#9933ff").style("stroke", "black").style("strole-width", "2");
            svg.append("text").attr("text-anchor", "top")
              .attr("x", width+30).attr("y", 148)
              .text("Lead SNPs").style("font-size", "10px");
            svg.append("circle")
              .attr("cx", width+20).attr("cy", 160).attr("r", 3.5)
              .style("fill", "red").style("stroke", "black").style("strole-width", "2");
            svg.append("text").attr("text-anchor", "top")
              .attr("x", width+30).attr("y", 163)
              .text("Independent significant SNPs").style("font-size", "10px");

			var maxY = Math.max(d3.max(data1, function(d){return -Math.log10(d.gwasP)}), d3.max(data4, function(d){return -Math.log10(d.gwasP)}))
            y.domain([0, maxY+1]);
            svg.selectAll("dot").data(data4).enter()
              .append("circle")
              .attr("class", "GWASnonLD")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(-Math.log10(d.gwasP));})
              .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "grey";}});

            svg.selectAll("dot").data(data1.filter(function(d){if(!isNaN(d.gwasP) && d.ld==1){return d;}})).enter()
              .append("circle")
              .attr("class", "GWASdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(-Math.log10(d.gwasP));})
              .style("fill", function(d){return colorScale(d.r2);})
              .on("click", function(d){
                table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  if(cells[0]=="all"){cells=Chr15eid;}
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
            svg.selectAll('rect.KGSNPs').data(data1.filter(function(d){if(isNaN(d.gwasP)){return d;}})).enter()
              .append("rect")
              .attr("class", "KGSNPs")
              .attr("x", function(d){return x(d.pos)})
              .attr("y", -20)
              .attr("width", "3")
              .attr("height", "10")
              .style("fill", function(d){if(d.ld==0){return "grey"}else{return colorScale(d.r2)}})
              .on("click", function(d){
                table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  if(cells[0]=="all"){cells=Chr15eid;}
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
            svg.selectAll("dot.leadSNPs").data(data1.filter(function(d){if(d.ld>=2){return d;}})).enter()
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
              .style("stroke", "black")
              .on("click", function(d){
                table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  if(cells[0]=="all"){cells=Chr15eid;}
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
              .attr("transform", "translate(0,"+(gwasTop+gwasHeight)+")")
              .call(xAxis).selectAll("text").remove();
            svg.append("g").attr("class", "y axis").call(yAxis)
              .selectAll('text').style('font-size', '11px');
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-10-margin.left/2)+","+(gwasTop+gwasHeight/2)+")rotate(-90)")
              .text("-log10 P-value");
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-margin.left/2)+", -15)")
              .style("font-size", "8px")
              .text("1000G SNPs");
            // currentHeight = currentHeight+210;
          }
          // currentHeight = currentHeight+60;
          if(CADDplot==1){
            // var y = d3.scale.linear().range([currentHeight+150, currentHeight]);
            var y = d3.scale.linear().range([caddTop+caddHeight, caddTop]);
            var yAxis = d3.svg.axis().scale(y).orient("left");

            y.domain([0, d3.max(data1, function(d){return d.CADD})+1]);
            svg.append("circle").attr("cx", width+20).attr("cy", caddTop+50)
              .attr("r", 3.5).attr("fill", "blue");
            svg.append("text").attr("x", width+30).attr("y", caddTop+53)
              .text("exonic SNPs").style("font-size", "10px");
            svg.append("circle").attr("cx", width+20).attr("cy", caddTop+70)
              .attr("r", 3.5).attr("fill", "skyblue");
            svg.append("text").attr("x", width+30).attr("y", caddTop+73)
              .text("other SNPs").style("font-size", "10px");

            svg.selectAll("dot").data(data1.filter(function(d){if(d.ld!=0){return d;}})).enter()
              .append("circle")
              .attr("class", "CADDdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(d.CADD);})
              // .style("fill", function(d){if(d.ld==0){return "grey";}else if(d.func=="exonic" || d.func=="splicing"){return "blue"}else{return "skyblue";}})
              .style("fill", function(d){
                if(d.posMapFilt==0 && d.eqtlMapFilt==0){
                  return "grey";
                }else{
                  if(d.func=="exonic"){return "blue";}
                  else{return "skyblue";}
                }
              })
              .on("click", function(d){
                table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  if(cells[0]=="all"){cells=Chr15eid;}
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
            // svg.selectAll("dot").data(data1.filter(function(d){if(d.ld!=0 && d.func=="exonic"){return d;}})).enter()
            //   .append("circle")
            //   .attr("class", "CADDdot")
            //   .attr("r", 3.5)
            //   .attr("cx", function(d){return x(d.pos);})
            //   .attr("cy", function(d){return y(d.CADD);})
            //   // .style("fill", function(d){if(d.ld==0){return "grey";}else if(d.func=="exonic" || d.func=="splicing"){return "blue"}else{return "skyblue";}})
            //   .style("fill", "blue")
            //   .on("click", function(d){
            //     table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
            //             +'<tr><td>Selected SNP</td><td>'+d.rsID
            //             +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
            //             +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
            //             +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
            //             +'</td></tr><tr><td>Annotation</td><td>'+d.func
            //             +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
            //             +'</td></tr><tr><td>CADD</td><td>'+d.CADD
            //             +'</td></tr><tr><td>RDB</td><td>'+d.RDB
            //             +'</td></tr>';
            //     if(Chr15==1){
            //       cells = Chr15cells.split(":");
            //       if(cells[0]=="all"){cells=Chr15eid;}
            //       for(var i=0; i<cells.length; i++){
            //         table += '<tr><td>'+cells[i]+'</td><td>'+d[cells[i]]+'</td></tr>';
            //       }
            //     }
            //     if(eqtl==1 & eqtlplot==1){
            //       table += '<tr><td>eQTLs</td><td>'+d.eqtl+'</td></tr>';
            //     }
            //     table += '</table>'
            //     $('#annotTable').html(table);
            //   });
            svg.append("text").attr("text-anchor", "middle")
              .attr("transform", "translate("+(-10-margin.left/2)+","+(caddTop+caddHeight/2)+")rotate(-90)")
              .text("CADD score");
            if(RDBplot==1 || Chr15==1 || (eqtl==1 && eqtlplot==1)){
              svg.append("g").attr("class", "x axis CADD")
                .attr("transform", "translate(0,"+(caddTop+caddHeight)+")")
                .call(xAxis).selectAll("text").remove();
            }else{
              xAxisLabel = "CADD";
              svg.append("g").attr("class", "x axis CADD")
                .attr("transform", "translate(0,"+(caddTop+caddHeight)+")")
                .call(xAxis)
                  .selectAll('text').style('font-size', '11px');
              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+width/2+","+(height+30)+")")
                .text("Chromosome "+chr);
            }
            svg.append("g").attr("class", "y axis").call(yAxis)
              .selectAll('text').style('font-size', '11px');
            // currentHeight = currentHeight+160;
          }
          if(RDBplot==1){
            var y_element = ["1a", "1b", "1c", "1d", "1e", "1f", "2a", "2b" ,"2c", "3a", "3b", "4", "5", "6", "7"];
            // var y = d3.scale.ordinal().domain(y_element).rangePoints([currentHeight, currentHeight+150]);
            var y = d3.scale.ordinal().domain(y_element).rangePoints([rdbTop, rdbTop+rdbHeight]);
            var yAxis = d3.svg.axis().scale(y).tickFormat(function(d){return d;}).orient("left");
            svg.selectAll("dot").data(data1.filter(function(d){if(d.RDB!="NA" && d.RDB!="" && d.ld!=0){return d;}})).enter()
              .append("circle")
              .attr("class", "RDBdot")
              .attr("r", 3.5)
              .attr("cx", function(d){return x(d.pos);})
              .attr("cy", function(d){return y(d.RDB);})
              // .style("fill", function(d){if(d.ld==0){return "grey"}else{return "MediumAquaMarine"}})
              .style("fill", function(d){
                if(d.posMapFilt==0 && d.eqtlMapFilt==0){return "grey";}
                else{return "MediumAquaMarine";}
              })
              .on("click", function(d){
                table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
                        +'<tr><td>Selected SNP</td><td>'+d.rsID
                        +'</td></tr><tr><td>bp</td><td>'+d.pos+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d.r2
                        +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d.IndSigSNP
                        +'</td></tr><tr><td>GWAS P-value</td><td>'+d.gwasP
                        +'</td></tr><tr><td>Annotation</td><td>'+d.func
                        +'</td></tr><tr><td>Nearest Gene</td><td>'+d.nearestGene
                        +'</td></tr><tr><td>CADD</td><td>'+d.CADD
                        +'</td></tr><tr><td>RDB</td><td>'+d.RDB
                        +'</td></tr>';
                if(Chr15==1){
                  cells = Chr15cells.split(":");
                  if(cells[0]=="all"){cells=Chr15eid;}
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
              .attr("transform", "translate("+(-10-margin.left/2)+","+(rdbTop+rdbHeight/2)+")rotate(-90)")
              .text("RegulomeDB score");
            if(Chr15==1 || (eqtl==1 && eqtlplot==1)){
              svg.append("g").attr("class", "x axis RDB")
                .attr("transform", "translate(0,"+(rdbTop+rdbHeight)+")")
                .call(xAxis).selectAll("text").remove();
            }else{
              xAxisLabel = "RDB";
              svg.append("g").attr("class", "x axis RDB")
                .attr("transform", "translate(0,"+(rdbTop+rdbHeight)+")")
                .call(xAxis)
                  .selectAll('text').style('font-size', '11px');
              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+width/2+","+(height+30)+")")
                .text("Chromosome "+chr);
            }
            svg.append("g").attr("class", "y axis").call(yAxis)
              .selectAll('text').style('font-size', '11px');
            // currentHeight = currentHeight+160;
            RDBlegend();
          }

          //chr15 and eqtl plot
          if(Chr15==1 && eqtl==1 && eqtlplot==1){
            xAxisLabel = "eqtl";
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

              var cells = d3.set(data1.map(function(d){return d.cell;})).values();
              EIDlegend(cells);
              var chr15gcol = [];
              var y_element = [];
              for(var i=0; i<Chr15eid.length; i++){
                if(cells.indexOf(Chr15eid[i])>=0){
                  y_element.push(Chr15eid[i]);
                  chr15gcol.push(Chr15GroupCols[i]);
                }
              }
              var tileHeight = 10;
              if(y_element.length>20){
                tileHeight = chrHeight/y_element.length;
              }
              // var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([currentHeight2, currentHeight2+y_element.length*tileHeight]);
              var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([chrTop, chrTop+chrHeight]);
              var yAxisChr15 = d3.svg.axis().scale(yChr15).tickFormat(function(d){return d;}).orient("left");

              var states = ["TssA", "TssAFlnk", "TxFlnk", "Tx", "Tx/Wk", "EnhG", "Enh", "ZNF/Rpts", "Het", "TssBiv", "BivFlnk", "EnhBiv", "ReprPC", "ReprPCWk", "Quies"];
              var legData = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
              var legendChr15 = svg.selectAll(".legendChr15")
                .data(legData)
                .enter()
                .append("g").attr("class", "legend");
              if(y_element.length>10){
                var legHead = chrTop+y_element.length*tileHeight/2-8*7.5;
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
                var legHead = chrTop+y_element.length*tileHeight/2-8*4;
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
                .attr("transform", "translate("+(-margin.left/2-15)+","+(chrTop+(y_element.length*tileHeight)/2)+")rotate(-90)")
                .text("Chromatin state");

              svg.append("g").attr("class", "x axis Chr15")
                .attr("transform", "translate(0,"+(chrTop+y_element.length*tileHeight)+")")
                .call(xAxis).selectAll("text").remove();
              if(y_element.length>30){
                svg.append("g").attr("class", "y axis").call(yAxisChr15).selectAll("text").remove();
              }else{
                svg.append("g").attr("class", "y axis").call(yAxisChr15)
                .selectAll("text").attr("transform", "translate(-5,0)").style("font-size", "10px");
              }
              for(var i=0; i<y_element.length; i++){
                svg.append("rect").attr("x", -10).attr("y", yChr15(y_element[i]))
                  .attr("width", 10).attr("height",tileHeight)
                  .attr("fill", chr15gcol[i]);
              }
              // currentHeight2 = currentHeight2+y_element.length*tileHeight+10;

              data2.forEach(function(d){
                d.pos = +d.pos;
                d.p = +d.p;
                d.ld = +d.ld;
                d.eqtlMapFilt = +d.eqtlMapFilt;
              });
              var genes = d3.set(data2.map(function(d){return d.symbol;})).values();
              var tissue = d3.set(data2.map(function(d){return d.tissue;})).values();
              // neqtl = genes.length;

              // eqtl color and DB
              var db = {};
              for(i=0; i<tissue.length; i++){
                eQTLcolors[tissue[i]] = eqtlcols[Math.round(i*eqtlcols.length/tissue.length)];
                var temp;
                data2.forEach(function(d){if(d.tissue==tissue[i]){temp=d.db}});
                db[tissue[i]] = temp;
              }

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
                .attr("cy", function(d){return eqtlTop+15+d*10})
                .style("fill", function(d){return eQTLcolors[tissue[d]]});
              legendEqtl.append("text")
                .attr("text-anchor", "start")
                .attr("x", width+15)
                .attr("y", function(d){return eqtlTop+18+d*10;})
                .text(function(d){return db[tissue[d]]+" "+tissue[d]})
                .style("font-size", "10px");
              for(i=0; i<genes.length; i++){
                // var y = d3.scale.linear().range([currentHeight2+50, currentHeight2]);
                var y = d3.scale.linear().range([eqtlTop+55*i+50, eqtlTop+55*i]);
                var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
                y.domain([0, d3.max(data2, function(d){return -Math.log10(d.p)})+0.5]);
                svg.selectAll("dot").data(data2.filter(function(d){if(d.symbol===genes[i] && d.ld!=0){return d}})).enter()
                  .append("circle").attr("class", "eqtldot")
                  .attr("r", 3.5)
                  .attr("cx", function(d){return x(d.pos);})
                  .attr("cy", function(d){return y(-Math.log10(d.p));})
                  .style("fill", function(d){
                    if(d.eqtlMapFilt==0){
                      return "grey";
                    }else{
                      return eQTLcolors[d.tissue]
                    }
                  })
                  .on("click", function(d){

                  });
                svg.append("text").attr("text-anchor", "middle")
                  .attr("transform", "translate("+(-margin.left/2)+","+(eqtlTop+i*55+25)+")rotate(-90)")
                  .text(genes[i])
                  .style("font-size", "10px");
                if(i==genes.length-1){
                  svg.append("g").attr("class", "x axis eqtlend")
                    .attr("transform", "translate(0,"+(eqtlTop+55*i+50)+")")
                    .call(xAxis)
                    .selectAll('text').style('font-size', '11px');
                  svg.append("text").attr("text-anchor", "middle")
                    .attr("transform", "translate("+width/2+","+(height+30)+")")
                    .text("Chromosome "+chr);
                }else{
                  // eqtlclass = "eqtl"+i;
                  svg.append("rect")
                    .attr("x", 0).attr("y", y(0))
                    .attr("width", width).attr("height", 0.3)
                    .style("fill", "grey");
                }
                svg.append("g").attr("class", "y axis").call(yAxis)
                  .selectAll('text').attr("transform", "translate(-5,0)").style('font-size', '11px');
                // currentHeight2 = currentHeight2+55;
              }

              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+(-margin.left/2-15)+","+(eqtlTop+eqtlHeight/2)+")rotate(-90)")
                .text("eQTL -log10 P-value")
                .style("font-size", "10px");
              svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
              svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
              svg.selectAll('text').style('font-family', 'sans-serif');
            });

          }else if(Chr15==1 && eqtlplot==0){
            xAxisLabel="chr15";
            queue().defer(d3.json, "d3text/"+jobID+"/Chr15.txt")
            .awaitAll(function(error, data){
              var data1 = data[0];
              data1.forEach(function(d){
                d.start = +d.start;
                d.end = +d.end;
                d.state = +d.state;
              });
              // var colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];
              // var y_element = d3.set(data1.map(function(d){return d.cell;})).values();
              var cells = d3.set(data1.map(function(d){return d.cell;})).values();
              EIDlegend(cells);
              var chr15gcol = [];
              var y_element = [];
              for(var i=0; i<Chr15eid.length; i++){
                if(cells.indexOf(Chr15eid[i])>=0){
                  y_element.push(Chr15eid[i]);
                  chr15gcol.push(Chr15GroupCols[i]);
                }
              }
              var tileHeight = 10;
              if(y_element.length>20){
                tileHeight = chrHeight/y_element.length;
              }
              // var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([currentHeight2, currentHeight2+y_element.length*tileHeight]);
              var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([chrTop, chrTop+chrHeight]);
              var yAxisChr15 = d3.svg.axis().scale(yChr15).tickFormat(function(d){return d;}).orient("left");

              var states = ["TssA", "TssAFlnk", "TxFlnk", "Tx", "Tx/Wk", "EnhG", "Enh", "ZNF/Rpts", "Het", "TssBiv", "BivFlnk", "EnhBiv", "ReprPC", "ReprPCWk", "Quies"];
              var legData = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
              var legendChr15 = svg.selectAll(".legendChr15")
                .data(legData)
                .enter()
                .append("g").attr("class", "legend");
              if(y_element.length>10){
                var legHead = chrTop+y_element.length*tileHeight/2-8*7.5;
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
                var legHead = chrTop+y_element.length*tileHeight/2-8*4;
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
                .attr("transform", "translate("+(-margin.left/2-15)+","+(chrTop+(y_element.length*tileHeight)/2)+")rotate(-90)")
                .text("Chromatin state");

              svg.append("g").attr("class", "x axis Chr15")
                .attr("transform", "translate(0,"+(chrTop+y_element.length*tileHeight)+")")
                .call(xAxis)
                .selectAll('text').style('font-size', '11px');
              // svg.append("g").attr("class", "y axis").call(yAxisChr15);
              if(y_element.length>30){
                svg.append("g").attr("class", "y axis").call(yAxisChr15)
                  .selectAll("text").remove();
              }else{
                svg.append("g").attr("class", "y axis").call(yAxisChr15)
                  .selectAll("text").attr("transform", "translate(-5,0)").style("font-size", "10px");
              }
              for(var i=0; i<y_element.length; i++){
                svg.append("rect").attr("x", -10).attr("y", yChr15(y_element[i]))
                  .attr("width", 10).attr("height",tileHeight)
                  .attr("fill", chr15gcol[i]);
              }
              // currentHeight2 = currentHeight2+y_element.length*tileHeight+10;
              svg.append("text").attr("text-anchor", "middle")
                .attr("transform", "translate("+width/2+","+(height+30)+")")
                .text("Chromosome "+chr);
              if(eqtl==1 && eqtlplot==0){
                svg.append("text").attr("text-anchor", "middle")
                    .attr("transform", "translate("+(width/2)+","+(height+margin.bottom-30)+")")
                    .text("No eQTL of selected tissues exists in this region.");
              }
              svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
              svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
              svg.selectAll('text').style('font-family', 'sans-serif');            });
          }else if(eqtl==1 && eqtlplot==1){
            xAxisLabel="eqtl";
              queue().defer(d3.json, "d3text/"+jobID+"/eqtlplot.txt")
              .awaitAll(function(error, data){
                var data = data[0];
                data.forEach(function(d){
                  d.pos = +d.pos;
                  d.p = +d.p;
                  d.ld = +d.ld;
                  d.eqtlMapFilt = +d.eqtlMapFilt;
                });
                var genes = d3.set(data.map(function(d){return d.symbol;})).values();
                var tissue = d3.set(data.map(function(d){return d.tissue;})).values();
                // neqtl = genes.length;

                // eqtl color and db
                var db = {};
                for(i=0; i<tissue.length; i++){
                  eQTLcolors[tissue[i]] = eqtlcols[Math.round(i*eqtlcols.length/tissue.length)];
                  var temp;
                  data.forEach(function(d){if(d.tissue==tissue[i]){temp=d.db}});
                  db[tissue[i]] = temp;
                }

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
                  .attr("cy", function(d){return eqtlTop+15+d*10})
                  .style("fill", function(d){return eQTLcolors[tissue[d]]});
                legendEqtl.append("text")
                  .attr("text-anchor", "start")
                  .attr("x", width+15)
                  .attr("y", function(d){return eqtlTop+18+d*10;})
                  .text(function(d){return db[tissue[d]]+" "+tissue[d]})
                  .style("font-size", "10px");

                for(i=0; i<genes.length; i++){
                  // var y = d3.scale.linear().range([currentHeight2+50, currentHeight2]);
                  var y = d3.scale.linear().range([eqtlTop+55*i+50, eqtlTop+55*i]);
                  var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
                  y.domain([0, d3.max(data, function(d){return -Math.log10(d.p)})+0.5]);
                  svg.selectAll("dot").data(data.filter(function(d){if(d.symbol===genes[i] && d.ld!=0){return d}})).enter()
                    .append("circle").attr("class", "eqtldot")
                    .attr("r", 3.5)
                    .attr("cx", function(d){return x(d.pos);})
                    .attr("cy", function(d){return y(-Math.log10(d.p));})
                    .style("fill", function(d){
                      if(d.eqtlMapFilt==0){
                        return "grey";
                      }else{
                        return eQTLcolors[d.tissue]
                      }
                    });
                  svg.append("text").attr("text-anchor", "middle")
                    .attr("transform", "translate("+(-margin.left/2)+","+(eqtlTop+i*55+25)+")rotate(-90)")
                    .text(genes[i])
                    .style("font-size", "10px");
                  if(i==genes.length-1){
                    svg.append("g").attr("class", "x axis eqtlend")
                      .attr("transform", "translate(0,"+(eqtlTop+55*i+50)+")")
                      .call(xAxis)
                      .selectAll('text').style('font-size', '11px');
                    svg.append("text").attr("text-anchor", "middle")
                      .attr("transform", "translate("+width/2+","+(height+30)+")")
                      .text("Chromosome "+chr);
                  }else{
                    // eqtlclass = "eqtl"+i;
                    svg.append("rect")
                      .attr("x", 0).attr("y", y(0))
                      .attr("width", width).attr("height", 0.3)
                      .style("fill", "grey");

                  }
                  svg.append("g").attr("class", "y axis").call(yAxis)
                    .selectAll('text').style('font-size', '11px');
                  // currentHeight2 = currentHeight2+55;
                }
                svg.append("text").attr("text-anchor", "middle")
                  .attr("transform", "translate("+(-margin.left/2-15)+","+(eqtlTop+eqtlHeight/2)+")rotate(-90)")
                  .text("eQTL -log10 P-value")
                  .style("font-size", "10px");
                svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
                svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
                svg.selectAll('text').style('font-family', 'sans-serif');
              });
          }
          svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
          svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
          svg.selectAll('text').style('font-family', 'sans-serif');

      });


  function zoomed(){
    if(xAxisLabel=="genes"){
      svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.genes").call(xAxis);
    }else if(xAxisLabel=="CADD"){
      svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.CADD").call(xAxis);
    }else if(xAxisLabel=="RDB"){
      svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.RDB").call(xAxis);
    }else if(xAxisLabel=="chr15"){
      svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.RDB").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.Chr15").call(xAxis);
    }else if(xAxisLabel=="eqtl"){
      svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.RDB").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.Chr15").call(xAxis).selectAll("text").remove();
      svg.select(".x.axis.eqtlend").call(xAxis);
    }

    svg.selectAll(".GWASdot").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return "grey";}else{return colorScale(d.r2)}});
    svg.selectAll(".GWASnonLD").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "grey";}});
    svg.selectAll(".KGSNPs").attr("x", function(d){return x(d.pos);})
      .style("fill", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else if(d.ld==0){return "grey"}else{return colorScale(d.r2)}});
    svg.selectAll(".leadSNPs").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){
        if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}
        else if(d.ld==2){return colorScale(d.r2);}
        else if(d.ld==3){return "#9933ff"}
        else if(d.ld==4){return "#4d0099"}
      })
      .style("stroke", function(d){if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}else{return "black"}});
    svg.selectAll(".CADDdot").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){
        if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}
        else if(d.ld==0){return "grey"}
        else if(d.posMapFilt==0 && d.eqtlMapFilt==0){return "grey"}
        else if(d.func=="exonic"){return "blue"}
        else{return "skyblue"}});
    svg.selectAll(".RDBdot").attr("cx", function(d){return x(d.pos);})
      .style("fill", function(d){
        if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}
        else if(d.ld==0){return "grey"}
        else if(d.posMapFilt==0 && d.eqtlMapFilt==0){return "grey"}
        else{return "MediumAquaMarine"}});
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
      .style("fill", function(d){if(x(d.end_position)<0 || x(d.start_position)>width){return "transparent";}
        else if(prioGenes.indexOf(d.external_gene_name)>=0){return "red";}
        else if(d.gene_biotype=="protein_coding"){return "blue";}
        else{return "#383838"}
      });
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
      .style("fill", function(d){if(x(d.exon_chrom_end)<0 || x(d.exon_chrom_start)>width){return "transparent";}
        else if(prioGenes.indexOf(d.external_gene_name)>=0){return "red";}
        else if(d.gene_biotype=="protein_coding"){return "blue";}
        else{return "#383838"}
      })
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
      .style("fill", function(d){
        if(x(d.pos)<0 || x(d.pos)>width){return "transparent";}
        else if(d.ld==0){return "grey"}
        else if(d.eqtlMapFilt==0){return "grey"}
        else{return eQTLcolors[d.tissue]}
      });
  }

  // Plot Clear button
  d3.select('#plotclear').on('click', reset);
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

function geneOver(genes, x, width){
  var tg = genes;
  // genes.forEach(function(d){
  //   if(x(d.end_position)>0 && x(d.start_position)<width){tg.push(d);}
  // })

  for(var i=0; i<tg.length; i++){
    var temp = tg.filter(function(d2){
      if((d2.end_position>=tg[i].start_position && d2.end_position<=tg[i].end_position)
        // || (d2.start_position>=d.start_position && d2.start_position<=d.end_position)
        || (d2.start_position<=tg[i].start_position && d2.end_position>=tg[i].end_position)
      ){return d2;}
      else if(x((d2.end_position+d2.start_position+d2.external_gene_name.length*9)/2)>=x((tg[i].end_position+tg[i].start_position)/2)-((tg[i].external_gene_name.length*9)/2)
          && x((d2.end_position+d2.start_position+d2.external_gene_name.length*9)/2)<=x((tg[i].end_position+tg[i].start_position)/2)+((tg[i].external_gene_name.length*9)/2)
        ){return d2}
    })
    if(temp.length>1){
      var ymin = d3.min(temp, function(d){return d.y});
      if(ymin>1){
        tg[i].y = 1;
      }else{
        tg[i].y = d3.max(temp, function(d){return d.y})+1;
      }
    }else{
      tg[i].y = 1;
    }
  }

  return tg;
}

function RDBlegend(){
  var margin = {top: 20, right: 20, bottom: 20, left: 20},
    width = 600,
    height = 350;
  var svg = d3.select('#RDBlegend').append('svg')
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");
  d3.json("legendText/RDB.txt", function(data){
    svg.append("text")
      .attr("x", 0)
      .attr("y", 0)
      .text("RegulomeDB Categorical Scores")
      .style("font-size", "14px");
    var curHeight = 20;
    svg.append("rect")
      .attr("x", 0)
      .attr("y", 6)
      .attr("height",1)
      .attr("width", 550);
    svg.append("rect")
      .attr("x", 0)
      .attr("y", curHeight+5)
      .attr("height",1)
      .attr("width", 550);

    svg.append("text")
      .attr("x", 5)
      .attr("y", curHeight)
      .text("Category")
      .style("font-size", "13px");
    svg.append("text")
      .attr("x", (500+60)/2)
      .attr("y", curHeight)
      .text("Description")
      .style("font-size", "13px");
    data.forEach(function(d){
      if(d.Category==""){
        curHeight += 5;
      }
      svg.append("text")
        .attr("x", 5)
        .attr("y", curHeight+15)
        .text(d.Category)
        .style("font-size", "13px");
      svg.append("text")
        .attr("x", 60)
        .attr("y", curHeight+15)
        .text(d.Description)
        .style("font-size", "13px");
      curHeight +=15;

    });
    svg.append("rect")
      .attr("x", 0)
      .attr("y", curHeight+5)
      .attr("height",1)
      .attr("width", 550);
    svg.selectAll('text').style("font-family", "sans-serif");
  });
}

function EIDlegend(cells){
  var margin = {top: 20, right: 20, bottom: 20, left: 20},
    width = 800,
    height = 30+15*cells.length;
  var svg = d3.select('#EIDlegend').append('svg')
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

  d3.json("legendText/EID.txt", function(data){
    svg.append("text")
      .attr("x", 0)
      .attr("y", 0)
      .text("Epigenome ID")
      .style("font-size", "14px");
    var curHeight = 20;
    svg.append("rect")
      .attr("x", 0)
      .attr("y", 6)
      .attr("height",1)
      .attr("width", 750);
    svg.append("rect")
      .attr("x", 0)
      .attr("y", curHeight+3)
      .attr("height",1)
      .attr("width", 750);

    svg.append("text")
      .attr("x", 5)
      .attr("y", curHeight)
      .text("EID")
      .style("font-size", "13px");
    svg.append("text")
      .attr("x", 50)
      .attr("y", curHeight)
      .text("Color")
      .style("font-size", "13px");
    svg.append("text")
      .attr("x", 120)
      .attr("y", curHeight)
      .text("Group")
      .style("font-size", "13px");
    svg.append("text")
      .attr("x", 210)
      .attr("y", curHeight)
      .text("Anatomy")
      .style("font-size", "13px");
    svg.append("text")
      .attr("x", 370)
      .attr("y", curHeight)
      .text("Standerdized epigenome name")
      .style("font-size", "13px");

    data.forEach(function(d){
      if(cells.indexOf(d.EID)>=0){
        svg.append("text")
          .attr("x", 5)
          .attr("y", curHeight+15)
          .text(d.EID)
          .style("font-size", "13px");
        svg.append("rect")
          .attr("x", 50)
          .attr("y", curHeight+4)
          .attr("width", 60)
          .attr("height", 15)
          .attr("fill", d.Color);
        svg.append("text")
          .attr("x", 50)
          .attr("y", curHeight+15)
          .text(d.Color)
          .style("fill", function(){if(d.Color=="#000000"){return "white"}else{return "black"}})
          .style("font-size", "13px");
        svg.append("text")
          .attr("x", 120)
          .attr("y", curHeight+15)
          .text(d.Group)
          .style("font-size", "13px");
        svg.append("text")
          .attr("x", 210)
          .attr("y", curHeight+15)
          .text(d.Anatomy)
          .style("font-size", "13px");
        svg.append("text")
          .attr("x", 370)
          .attr("y", curHeight+15)
          .text(d.Name)
          .style("font-size", "13px");
        curHeight +=15;

      }
    });
    svg.append("rect")
      .attr("x", 0)
      .attr("y", curHeight+5)
      .attr("height",1)
      .attr("width", 750);
    svg.selectAll('text').style("font-family", "sans-serif");
  });
}

function ImgDown(id, type){
  $('#'+id+'Data').val($('#'+id).html());
  $('#'+id+'Type').val(type);
  $('#'+id+'JobID').val(jobID);
  $('#'+id+'FileName').val(id);
  $('#'+id+'Dir').val("jobs");
  $('#'+id+'Submit').trigger('click');
}

</script>
@stop
@section('content')
<canvas id="canvas" style="display:none;"></canvas>

<div id="test"></div>
<!-- <h3>Annotplot head</h3> -->
<br/><br/>
<div class="container">
<div class="row">
  <div class="col-md-9">
    <div id='title' style="text-align: center;"><h4>Regional plot</h4></div>
    <span class="info"><i class="fa fa-info"></i>
      For SNPs colored grey in the plots of GWAS P-value, CADD, RegulomeDB score and eQTLs, please refer the legend at the bottom of the plot.
    </span><br/>
    <a id="plotclear" style="position: absolute;right: 30px;">Clear</a><br/>
    Download the plot as
    <button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","png");'>PNG</button>
    <button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","jpeg");'>JPG</button>
    <button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","svg");'>SVG</button>
    <button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","pdf");'>PDF</button>

    <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="dir" id="annotPlotDir" val=""/>
      <input type="hidden" name="id" id="annotPlotJobID" val=""/>
      <input type="hidden" name="data" id="annotPlotData" val=""/>
      <input type="hidden" name="type" id="annotPlotType" val=""/>
      <input type="hidden" name="fileName" id="annotPlotFileName" val=""/>
      <input type="submit" id="annotPlotSubmit" class="ImgDownSubmit"/>
    </form>    <div id="annotPlot"></div>
    <br/>
    <div id="RDBlegend"></div>
    <br/>
    <div id="EIDlegend"></div>
    <br/>
    <div id="SNPlegend">
      <h4>SNPs colored grey in the plots</h4>
      <strong>GWAS P-value</strong>: SNPs which are not in LD of any of significant independent lead SNPs in the selected region are colored grey.<br/>
      <strong>CADD score</strong>: Only SNPs which are in LD of any of significant independet lead SNPs are displayed in the plot.
      Of those SNPs, SNPs which did not used for mapping (SNPs that were filtered by user defined parameters) are colored grey.
      When both positional and eQTL mappings were performed, only SNPs which were not used either of them are colored grey.<br/>
      <strong>RegulomeDB score</strong>: Same as CADD score.<br/>
      <strong>eQTLs</strong>: When eQTL mapping was performed and eQTLs exist in the selected region, all eQTLs with user defined P-value threshold and tissue types are displayed.
      Of those eQTLs, eQTLs which did not used for eQTL mapping (eQTLs that were filtered by user defined parameters) are colored grey.<br/>
    </div>
  </div>
  <div class="col-md-3" style="text-align: center;">
    <h4>SNP annotations</h4>
    <div id="annotTable">
      click any SNP on the plot</br>
    </div>
  </div>
</div>
</div>

<br/><br/>
<!-- <h3>Annotplot end</h3> -->
@stop
<!-- </html> -->
