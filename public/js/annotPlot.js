var plotData;
var genes;
var chrom;
var xMin;
var xMax;
var xMin_init;
var xMax_init;
var eqtlgenes;

$(document).ready(function(){
	$('.ImgDownSubmit').hide();

	$.ajax({
	  url: 'annotPlot/getData',
	  type: 'POST',
	  data:{
		  id: id,
		  prefix: prefix,
		  type: type,
		  rowI: rowI,
		  GWASplot: GWASplot,
		  CADDplot: CADDplot,
		  RDBplot: RDBplot,
		  eqtlplot: eqtlplot,
		  ciplot: ciplot,
		  Chr15: Chr15,
		  Chr15cells: Chr15cells
	  },
	  beforeSend: function(){
		  $("#load").append('<span style="color:grey;"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i><br/>Loading ...</span><br/>');
	  },
	  success: function(data){
		  plotData = JSON.parse(data.replace(/NaN/g, "-1"));
		  chrom = plotData["chrom"];
		  xMin = plotData["xMin"];
		  xMax = plotData["xMax"];
		  xMin_init = plotData["xMin_init"];
		  xMax_init = plotData["xMax_init"];
		  eqtlgenes = plotData["eqtlgenes"];
	  },
	  complete: function(){
		  $.ajax({
			  url: 'annotPlot/getGenes',
			  type: 'POST',
			  data:{
				  id: id,
				  prefix: prefix,
				  chrom: chrom,
				  eqtlplot: eqtlplot,
				  ciplot: ciplot,
				  xMin: xMin,
				  xMax: xMax,
				  eqtlgenes: eqtlgenes
			  },
			  success: function(data){
				  genes = JSON.parse(data);
				  $('#load').html("");
			  },
			  complete: function(){
				Plot();
			  }
		  })
	  }
	});

	function Plot(){
		/*---------------------------------------------
		| Set parameters
		---------------------------------------------*/
		var svg;
	    var zoom;
		var margin = {top:50, right:280, left:60, bottom:100},
	        width = 600;
		// 5% of the genomic region is added to both side
	    var side = (xMax_init*1-xMin_init*1)*0.05;
	    if(side==0){side=500;}

		// set x axis
		var x = d3.scale.linear().range([0, width]);
	    var xAxis = d3.svg.axis().scale(x).orient("bottom").ticks(5);
	    x.domain([(xMin_init*1-side), (xMax_init*1+side)]);

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
	                          "#000000"];

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
		var eid = [];

		// Variable stores whch plot panel is the bottom
		var xAxisLabel = "gene";

		// height variables
	    var height; // Total height of the plot
	    var genesHeight; // Depends on the overlap of genes
	    var gwasHeight=200;
	    var caddHeight=150;
	    var rdbHeight=150;
	    var chrHeight; // Depends on the number of selected epigenomes
	    var eqtlHeight; // Depends on the number the number of genes which have eQTLs
		var ciHeight; // Depends on the number of data sets and interactions
		var ciregHeight; // Depends on t he number of selected epigenomes

		// minimum Y variables
	    var gwasTop = 0;
	    var genesTop = (gwasHeight+gwasTop+10)*GWASplot;
	    var caddTop;
	    var rdbTop;
	    var chrTop;
	    var eqtlTop;
		var ciTop;
		var ciregTop;

		var ci_cellsize = 3;

		// gene data
		genes.genes.forEach(function(d){
	      d[2] = +d[2]; //start position
	      d[3] = +d[3]; //end position
	      d[6] = 1; //y
	    });
		genes.genes = geneOver(genes.genes, x, width); //avoid overlap of genes

		//  snps data
		plotData.snps.forEach(function(d){
			d[2] = +d[2]; //pos
			d[4] = +d[4]; //gwasP
			if(d[4]==-1){d[4] = NaN}
			d[5] = +d[5]; //ld
			d[6] = +d[6]; //r2
			d[9] = +d[9]; //CADD
			d[13] = +d[13]; //MapFilt
	    });

		// define height
	    genesHeight = 20*(d3.max(genes.genes, function(d){return d[6];})+1);
	    caddTop = (genesTop+genesHeight+10);
	    rdbTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot;
	    chrTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot;
	    var cells = Chr15cells.split(":");
	    if(cells.length>30 || cells[0]=="all"){chrHeight=300;}
	    else{chrHeight = 10*cells.length;}
		var eqtlNgenes = parseInt(plotData["eqtlNgenes"])
		if(plotData["eqtl"].length==0){
			eqtlNgenes = 0;
		}
		eqtlTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot+(chrHeight+10)*Chr15;
	    eqtlHeight = eqtlplot*(eqtlNgenes*55);

		ciHeight = 0;
		plotData.ciheight.forEach(function(d){
			if(d*ci_cellsize+10<30){
				ciHeight += 30;
			}else{
				ciHeight += d*ci_cellsize+10;
			}
			ciHeight += 5;
		});
		ciTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot+(chrHeight+10)*Chr15+eqtlHeight;
		ciregHeight = plotData["cieid"].length * 10;
		if(ciregHeight > 250){ciregHeight=250}
		ciregTop = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot+(chrHeight+10)*Chr15+eqtlHeight+ciHeight;
		height = (gwasHeight+10)*GWASplot+genesHeight+10+(caddHeight+10)*CADDplot+(rdbHeight+10)*RDBplot+(chrHeight+10)*Chr15+eqtlHeight+ciHeight+ciregHeight;
		if(plotData["eqtl"].length>0){
			ciTop += 10;
			ciregTop += 10;
			height += 10;
		}
		if(plotData["ci"].length>0 && plotData["cireg"].length>0){
			ciregTop += 10;
			height += 10;
		}

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
		                .style("fill-opacity", "0");

		// transparent rect for mouse over
		svg.append("rect")
			.attr("width", width).attr("height", height)
			.style("fill-opacity", "0")
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

		/*---------------------------------------------
		| Plot genes
		---------------------------------------------*/
		var y = d3.scale.linear().range([genesTop+genesHeight, genesTop]);
		y.domain([d3.max(genes.genes, function(d){return d[6];})+1, 0]);

		// genes legend
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

		// genes
		svg.selectAll('rect.gene').data(genes.genes).enter().append("g")
		    .insert('rect').attr("class", "cell").attr("class", "genesrect")
		    .attr("x", function(d){
		      if(x(d[2])<0 || x(d[3])<0){return 0;}
		      else{return x(d[2]);}
		    })
		    // .attr("y", function(d){return y(d.strand)})
		    .attr("y", function(d){return y(d[6])})
		    .attr("width", function(d){
		      if(x(d[3])<0 || x(d[2])>width){return 0;}
		      else if(x(d[2])<0 && x(d[3])>width){return width;}
		      else if(x(d[2])<0){return x(d[3]);}
		      else if(x(d[3])>width){return width-x(d[2]);}
		      else{return x(d[3])-x(d[2])}
		    })
		    .attr("height", 1)
		    .attr("fill", function(d){
		      if(x(d[3])<0 || x(d[2])>width){return "none";}
		      else if(genes["mappedGenes"].indexOf(d[1])>=0){return "red";}
		      else if(d[5]=="protein_coding"){return "blue";}
		      else{return "#383838"}
		    });

		// gene names
		svg.selectAll("text.genes").data(genes.genes).enter()
			.append("text").attr("class", "geneName").attr("text-anchor", "middle")
			.attr("x", function(d){
			  if(x(d[2])<0 && x(d[3])>width){return width/2;}
			  else if(x(d[2])<0){return x(d[3])/2;}
			  else if(x(d[3])>width){return x(d[2])+(width-x(d[2]))/2;}
			  else{return x(((d[3]-d[2])/2)+d[2]);}
			})
			.attr("y", function(d){return y(d[6]);})
			.attr("dy", "-.7em")
			.text(function(d){
			  if(d[4]==1){
			    return d[1]+"\u2192";
			  }else{
			    return "\u2190"+d[1];
			  }
			})
			.style("font-size", "9px")
			.style("font-family", "sans-serif")
			.style("fill", function(d){
			  if(x(d[3])<0 || x(d[2])>width){return "none";}
			  else{return "black";}
			});
		if(CADDplot==1 || RDBplot==1 || Chr15==1 || plotData["eqtl"].length>0 || plotData["ci"].length > 0){
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
			  .text("Chromosome "+chrom);
		}

		//exon plot
	    genes.exons.forEach(function(d){
	      d[6] = +d[6]; // exon start
	      d[7] = +d[7]; //exon end
	      d[4] = +d[4]; //strand
	      d[8] = genes.genes.filter(function(d2){if(d2[0]==d[0]){return d2;}})[0][6];
	    });
	    svg.selectAll('rect.exon').data(genes.exons).enter().append("g")
	      .insert('rect').attr("class", "cell").attr("class", "exons")
	      .attr("x", function(d){
	        if(x(d[6])<0 || x(d[7])<0){return 0;}
	        else{return x(d[6]);}
	      })
	      // .attr("y", function(d){return y(d.strand)-4.5})
	      .attr("y", function(d){return y(d[8])-4.5})
	      .attr("width", function(d){
	        if(x(d[7])<0 || x(d[6])>width){return 0;}
	        else if(x(d[6])<0 && x(d[7])>width){return width;}
	        else if(x(d[6])<0){return x(d[7]);}
	        else if(x(d[7])>width){return width-x(d[6]);}
	        else{return x(d[7])-x(d[6]);}
	      })
	      .attr("height", 9)
	      .attr("fill", function(d){
	        if(x(d[6])>width || x(d[7])<0){return "none";}
	        else if(genes["mappedGenes"].indexOf(d[1])>=0){return "red";}
	        else if(d[5]=="protein_coding"){return "blue";}
	        else{return "#383838"}
	      });

		/*---------------------------------------------
  		| Plot GWAS P-value
  		---------------------------------------------*/
		if(GWASplot==1){
			plotData.osnps.forEach(function(d){
				d[1] = +d[1]; //pos
				d[2] = +d[2]; //gwasP
			});
			var y = d3.scale.linear().range([gwasTop+gwasHeight, gwasTop]);
			var yAxis = d3.svg.axis().scale(y).orient("left");

			// legend
			var legData = [];
				for(i=10; i>0; i--){
				legData.push(i*0.1);
			}
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

			// plot SNPs which are not in LD (filled in grey)
			var maxY = Math.max(d3.max(plotData.snps, function(d){return -Math.log10(d[4])}), d3.max(plotData.osnps, function(d){return -Math.log10(d[2])}))
			y.domain([0, maxY+1]);
			svg.selectAll("dot").data(plotData.osnps).enter()
				.append("circle")
				.attr("class", "GWASnonLD")
				.attr("r", 3.5)
				.attr("cx", function(d){return x(d[1]);})
				.attr("cy", function(d){return y(-Math.log10(d[2]));})
				.style("fill", function(d){if(x(d[1])<0 || x(d[1])>width){return "none";}else{return "grey";}});

			// plot SNPs which exist in the input GWAS file
			svg.selectAll("dot").data(plotData.snps.filter(function(d){if(!isNaN(d[4]) && d[5]==1){return d;}})).enter()
				.append("circle")
				.attr("class", "GWASdot")
				.attr("r", 3.5)
				.attr("cx", function(d){return x(d[2]);})
				.attr("cy", function(d){return y(-Math.log10(d[4]));})
				.style("fill", function(d){return colorScale(d[6]);})
				.on("click", function(d){
				  table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
				          +'<tr><td>Selected SNP</td><td>'+d[3]
				          +'</td></tr><tr><td>bp</td><td>'+d[2]+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d[6]
				          +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d[7]
				          +'</td></tr><tr><td>GWAS P-value</td><td>'+d[4]
				          +'</td></tr><tr><td>Annotation</td><td>'+d[12]
				          +'</td></tr><tr><td>Nearest Gene</td><td>'+d[11]
				          +'</td></tr><tr><td>CADD</td><td>'+d[9];
				  if(d[10]=="NA"){
					  table += '</td></tr><tr><td>RDB</td><td>'+d[10]+'</td>';
				  }else{
					  table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr'+chrom+'/'+(d[2]-1)+'">'+d[10]
					  +' (external link)*</a></td></tr>';
				  }
				  if(Chr15==1){
				    cells = Chr15cells.split(":");
				    if(cells[0]=="all"){cells=Chr15eid;}
				    for(var i=0; i<cells.length; i++){
				      table += '<tr><td>'+cells[i]+'</td><td>'+d[14+i]+'</td></tr>';
				    }
				  }
				  if(eqtlplot==1 & plotData["eqtl"].length>0){
				    table += '<tr><td>eQTLs</td><td>'+d[plotData.snps[0].length-1]+'</td></tr>';
				  }
				  table += '</table>'
				  $('#annotTable').html(table);
				});

			// plot SNPs which do not exist in input GWAS (rect)
			svg.selectAll('rect.KGSNPs').data(plotData.snps.filter(function(d){if(isNaN(d[4])){return d;}})).enter()
				.append("rect")
				.attr("class", "KGSNPs")
				.attr("x", function(d){return x(d[2])})
				.attr("y", -20)
				.attr("width", "3")
				.attr("height", "10")
				.style("fill", function(d){if(d[5]==0){return "grey"}else{return colorScale(d[6])}})
				.on("click", function(d){
				  table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
				          +'<tr><td>Selected SNP</td><td>'+d[3]
				          +'</td></tr><tr><td>bp</td><td>'+d[2]+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d[6]
				          +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d[7]
				          +'</td></tr><tr><td>GWAS P-value</td><td>'+d[4]
				          +'</td></tr><tr><td>Annotation</td><td>'+d[12]
				          +'</td></tr><tr><td>Nearest Gene</td><td>'+d[11]
				          +'</td></tr><tr><td>CADD</td><td>'+d[9];
				  if(d[10]=="NA"){
					  table += '</td></tr><tr><td>RDB</td><td>'+d[10]+'</td>';
				  }else{
					  table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr'+chrom+'/'+(d[2]-1)+'">'+d[10]
					  +' (external link)*</a></td></tr>';
				  }
				  if(Chr15==1){
				    cells = Chr15cells.split(":");
				    if(cells[0]=="all"){cells=Chr15eid;}
				    for(var i=0; i<cells.length; i++){
				      table += '<tr><td>'+cells[i]+'</td><td>'+d[14+i]+'</td></tr>';
				    }
				  }
				  if(eqtlplot==1 & plotData["eqtl"].length>0){
				    table += '<tr><td>eQTLs</td><td>'+d[plotData.snps[0].length-1]+'</td></tr>';
				  }
				  table += '</table>'
				  $('#annotTable').html(table);
				});

			// lead SNPs
			svg.selectAll("dot.leadSNPs").data(plotData.snps.filter(function(d){if(d[5]>=2){return d;}})).enter()
				.append("circle")
				.attr("class", "leadSNPs")
				.attr("cx", function(d){return x(d[2])})
				.attr("cy", function(d){return y(-Math.log10(d[4]));})
				.attr("r", function(d){
				  if(d[5]==2){return 3.5;}
				  else if(d[5]==3){return 4;}
				  else if(d[5]==4){return 4.5;}
				})
				.style("fill", function(d){
				    if(d[5]==2){return colorScale(d[6]);}
				    else if(d[5]==3){return "#9933ff"}
				    else if(d[5]==4){return "#4d0099"}
				})
				.style("stroke", "black")
				.on("click", function(d){
				  table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
				          +'<tr><td>Selected SNP</td><td>'+d[3]
				          +'</td></tr><tr><td>bp</td><td>'+d[2]+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d[6]
				          +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d[7]
				          +'</td></tr><tr><td>GWAS P-value</td><td>'+d[4]
				          +'</td></tr><tr><td>Annotation</td><td>'+d[12]
				          +'</td></tr><tr><td>Nearest Gene</td><td>'+d[11]
				          +'</td></tr><tr><td>CADD</td><td>'+d[9];
				  if(d[10]=="NA"){
					  table += '</td></tr><tr><td>RDB</td><td>'+d[10]+'</td>';
				  }else{
					  table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr'+chrom+'/'+(d[2]-1)+'">'+d[10]
					  +' (external link)*</a></td></tr>';
				  }
				  if(Chr15==1){
				    cells = Chr15cells.split(":");
				    if(cells[0]=="all"){cells=Chr15eid;}
				    for(var i=0; i<cells.length; i++){
				      table += '<tr><td>'+cells[i]+'</td><td>'+d[14+i]+'</td></tr>';
				    }
				  }
				  if(eqtlplot==1 & plotData["eqtl"].length>0){
				    table += '<tr><td>eQTLs</td><td>'+d[plotData.snps[0].length-1]+'</td></tr>';
				  }
				  table += '</table>'
				  $('#annotTable').html(table);
				});

			// labels
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
				.text("ref SNPs");
		}

		/*---------------------------------------------
  		| Plot CADD
  		---------------------------------------------*/
		if(CADDplot==1){
			var y = d3.scale.linear().range([caddTop+caddHeight, caddTop]);
			var yAxis = d3.svg.axis().scale(y).orient("left");

			// legend
			y.domain([0, d3.max(plotData.snps, function(d){return d[9]})+1]);
			svg.append("circle").attr("cx", width+20).attr("cy", caddTop+50)
				.attr("r", 3.5).attr("fill", "blue");
			svg.append("text").attr("x", width+30).attr("y", caddTop+53)
				.text("exonic SNPs").style("font-size", "10px");
			svg.append("circle").attr("cx", width+20).attr("cy", caddTop+70)
				.attr("r", 3.5).attr("fill", "skyblue");
			svg.append("text").attr("x", width+30).attr("y", caddTop+73)
				.text("other SNPs").style("font-size", "10px");

			// plot SNPs
			svg.selectAll("dot").data(plotData.snps.filter(function(d){if(d[5]!=0){return d;}})).enter()
				.append("circle")
				.attr("class", "CADDdot")
				.attr("r", 3.5)
				.attr("cx", function(d){return x(d[2]);})
				.attr("cy", function(d){return y(d[9]);})
				// .style("fill", function(d){if(d.ld==0){return "grey";}else if(d.func=="exonic" || d.func=="splicing"){return "blue"}else{return "skyblue";}})
				.style("fill", function(d){
				  if(d[13]==0){
				    return "grey";
				  }else{
				    if(d[12]=="exonic"){return "blue";}
				    else{return "skyblue";}
				  }
				})
				.on("click", function(d){
				  table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
				          +'<tr><td>Selected SNP</td><td>'+d[3]
				          +'</td></tr><tr><td>bp</td><td>'+d[2]+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d[6]
				          +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d[7]
				          +'</td></tr><tr><td>GWAS P-value</td><td>'+d[4]
				          +'</td></tr><tr><td>Annotation</td><td>'+d[12]
				          +'</td></tr><tr><td>Nearest Gene</td><td>'+d[11]
				          +'</td></tr><tr><td>CADD</td><td>'+d[9];
				  if(d[10]=="NA"){
					  table += '</td></tr><tr><td>RDB</td><td>'+d[10]+'</td>';
				  }else{
					  table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr'+chrom+'/'+(d[2]-1)+'">'+d[10]
					  +' (external link)*</a></td></tr>';
				  }
				  if(Chr15==1){
				    cells = Chr15cells.split(":");
				    if(cells[0]=="all"){cells=Chr15eid;}
				    for(var i=0; i<cells.length; i++){
				      table += '<tr><td>'+cells[i]+'</td><td>'+d[14+i]+'</td></tr>';
				    }
				  }
				  if(eqtlplot==1 & plotData["eqtl"].length>0){
				    table += '<tr><td>eQTLs</td><td>'+d[plotData.snps[0].length-1]+'</td></tr>';
				  }
				  table += '</table>'
				  $('#annotTable').html(table);
				});

			// labels
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+(-10-margin.left/2)+","+(caddTop+caddHeight/2)+")rotate(-90)")
				.text("CADD score");
			if(RDBplot==1 || Chr15==1 || plotData["eqtl"].length>0 || plotData["ci"].length > 0){
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
				  .text("Chromosome "+chrom);
			}
			svg.append("g").attr("class", "y axis").call(yAxis)
				.selectAll('text').style('font-size', '11px');
		}

		/*---------------------------------------------
  		| Plot RegulomeDB
  		---------------------------------------------*/
		if(RDBplot==1){
			var y_element = ["1a", "1b", "1c", "1d", "1e", "1f", "2a", "2b" ,"2c", "3a", "3b", "4", "5", "6", "7"];
			var y = d3.scale.ordinal().domain(y_element).rangePoints([rdbTop, rdbTop+rdbHeight]);
			var yAxis = d3.svg.axis().scale(y).tickFormat(function(d){return d;}).orient("left");

			// plot SNPs
			svg.selectAll("dot").data(plotData.snps.filter(function(d){if(d[10]!="NA" && d[10]!="" && d[5]!=0){return d;}})).enter()
				.append("circle")
				.attr("class", "RDBdot")
				.attr("r", 3.5)
				.attr("cx", function(d){return x(d[2]);})
				.attr("cy", function(d){return y(d[10]);})
				// .style("fill", function(d){if(d.ld==0){return "grey"}else{return "MediumAquaMarine"}})
				.style("fill", function(d){
				  if(d[13]==0){return "grey";}
				  else{return "MediumAquaMarine";}
				})
				.on("click", function(d){
				  table = '<table class="table table-sm" style="font-size: 10px;" cellpadding="1">'
				          +'<tr><td>Selected SNP</td><td>'+d[3]
				          +'</td></tr><tr><td>bp</td><td>'+d[2]+'</td></tr><tr><td>r<sup>2</sup></td><td>'+d[6]
				          +'</td></tr><tr><td>Ind. Sig. SNPs</td><td>'+d[7]
				          +'</td></tr><tr><td>GWAS P-value</td><td>'+d[4]
				          +'</td></tr><tr><td>Annotation</td><td>'+d[12]
				          +'</td></tr><tr><td>Nearest Gene</td><td>'+d[11]
				          +'</td></tr><tr><td>CADD</td><td>'+d[9];
				  if(d[10]=="NA"){
					  table += '</td></tr><tr><td>RDB</td><td>'+d[10]+'</td>';
				  }else{
					  table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr'+chrom+'/'+(d[2]-1)+'">'+d[10]
					  +' (external link)*</a></td></tr>';
				  }
				  if(Chr15==1){
				    cells = Chr15cells.split(":");
				    if(cells[0]=="all"){cells=Chr15eid;}
				    for(var i=0; i<cells.length; i++){
				      table += '<tr><td>'+cells[i]+'</td><td>'+d[14+i]+'</td></tr>';
				    }
				  }
				  if(eqtlplot==1 & plotData["eqtl"].length>0){
				    table += '<tr><td>eQTLs</td><td>'+d[plotData.snps[0].length-1]+'</td></tr>';
				  }
				  table += '</table>'
				  $('#annotTable').html(table);
				});

			// labels
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+(-10-margin.left/2)+","+(rdbTop+rdbHeight/2)+")rotate(-90)")
				.text("RegulomeDB score");
			if(Chr15==1 || plotData["eqtl"].length>0 || plotData["ci"].length > 0){
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
				  .text("Chromosome "+chrom);
			}
			svg.append("g").attr("class", "y axis").call(yAxis)
				.selectAll('text').style('font-size', '11px');
			RDBlegend();
		}

		/*---------------------------------------------
  		| Plot 15 core Chromatin state
  		---------------------------------------------*/
		if(Chr15==1){
			plotData.Chr15.forEach(function(d){
				d[1] = +d[1]; //start
				d[2] = +d[2]; //end
				d[3] = +d[3]; //state
			});
			// var colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];

			var cells = d3.set(plotData.Chr15.map(function(d){return d[0];})).values();
			// EIDlegend(cells);
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
			var yChr15 = d3.scale.ordinal().domain(y_element).rangeBands([chrTop, chrTop+chrHeight]);
			var yAxisChr15 = d3.svg.axis().scale(yChr15).tickFormat(function(d){return d;}).orient("left");

			// legend
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

			// plot rect
			svg.selectAll("rect.chr").data(plotData.Chr15).enter().append("g")
				.insert('rect').attr('class', 'cell').attr("class", "Chr15rect")
				.attr("x", function(d){
		          if(x(d[1])<0 || x(d[2])<0){return 0;}
		          else{return x(d[1]);}
		        })
		        .attr("width", function(d){
		          if(x(d[2])<0 || x(d[1])>width){return 0;}
		          else if(x(d[1])<0 && x(d[2])>width){return width;}
		          else if(x(d[1])<0){return x(d[2]);}
		          else if(x(d[2])>width){return width-x(d[1]);}
		          else{return x(d[2])-x(d[1]);}
		        })
				.attr("height", tileHeight)
				.attr('y', function(d){return yChr15(d[0])})
				.attr("fill", function(d){
					if(x(d[2])<0 || x(d[1])>width){return "none";}
  	          		else{return Chr15colors[d[3]-1];}
				})
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

			// labels
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate("+(-margin.left/2-15)+","+(chrTop+(y_element.length*tileHeight)/2)+")rotate(-90)")
				.text("Chromatin state");
			if(plotData["eqtl"].length>0 || plotData["ci"].length > 0){
				svg.append("g").attr("class", "x axis Chr15")
					.attr("transform", "translate(0,"+(chrTop+y_element.length*tileHeight)+")")
					.call(xAxis).selectAll("text").remove();
			}else{
				xAxisLabel = "chr15";
				svg.append("g").attr("class", "x axis Chr15")
					.attr("transform", "translate(0,"+(chrTop+y_element.length*tileHeight)+")")
					.call(xAxis)
				    .selectAll('text').style('font-size', '11px');
				svg.append("text").attr("text-anchor", "middle")
				  .attr("transform", "translate("+width/2+","+(height+30)+")")
				  .text("Chromosome "+chrom);
			}
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
			EIDlegend(y_element);
		}

		/*---------------------------------------------
  		| Plot eQTLs
  		---------------------------------------------*/
		if(eqtlplot == 1){
			if(plotData["eqtl"].length==0){
				svg.append("text").attr("text-anchor", "middle")
	                .attr("transform", "translate("+(width/2)+","+(height+margin.bottom-30)+")")
	                .text("No eQTL of selected tissues exists in this region.")
					.style('font-family', 'sans-serif');
			}else{
				xAxisLabel = "eqtl";
				plotData.eqtl.forEach(function(d){
					d[11] = +d[11]; //pos
					d[5] = +d[5]; //p
					d[7] = +d[7]; //FDR
					d[13] = +d[13]; //eqtlMapFilt
				});
				var eqtlgenes = d3.set(plotData.eqtl.map(function(d){return d[12];})).values();
				var tissue = d3.set(plotData.eqtl.map(function(d){return d[2];})).values();

				// eqtl color and DB
				var db = {};
				for(i=0; i<tissue.length; i++){
					eQTLcolors[tissue[i]] = eqtlcols[Math.round(i*eqtlcols.length/tissue.length)];
					var temp;
					plotData.eqtl.forEach(function(d){if(d[2]==tissue[i]){temp=d[1]}});
					db[tissue[i]] = temp;
				}

				// legend
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
					.attr("cy", function(d){return eqtlTop+10+d*10})
					.style("fill", function(d){return eQTLcolors[tissue[d]]});
				legendEqtl.append("text")
					.attr("text-anchor", "start")
					.attr("x", width+15)
					.attr("y", function(d){return eqtlTop+13+d*10;})
					.text(function(d){return db[tissue[d]]+" "+tissue[d]})
					.style("font-size", "10px");

				// plot eQTLs per gene
				for(i=0; i<eqtlgenes.length; i++){
					var y = d3.scale.linear().range([eqtlTop+55*i+50, eqtlTop+55*i]);
					var yAxis = d3.svg.axis().scale(y).orient("left").ticks(4);
					var yMax = d3.max(plotData.eqtl, function(d){return -Math.log10(d[5])})
					if(yMax==undefined){yMax=d3.max(plotData.eqtl, function(d){return -Math.log10(d[7])})}
					y.domain([0, yMax+0.5]);
					svg.selectAll("dot").data(plotData.eqtl.filter(function(d){if(d[12]===eqtlgenes[i]){return d}})).enter()
						.append("circle").attr("class", "eqtldot")
						.attr("r", 3.5)
						.attr("cx", function(d){return x(d[11]);})
						.attr("cy", function(d){if(d[5]>=0){return y(-Math.log10(d[5]));}else{return y(-Math.log10(d[7]));}})
						.style("fill", function(d){
						  if(d[13]==0){
						    return "grey";
						  }else{
						    return eQTLcolors[d[2]]
						  }
						})
						.on("click", function(d){

						});
					var gene_font_size = '9px'
					if(eqtlgenes[i].length>6){gene_font_size='7px'}
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(-margin.left/2)+","+(eqtlTop+i*55+25)+")rotate(-90)")
						.text(eqtlgenes[i])
						.style("font-size", gene_font_size);
					if(i==eqtlgenes.length-1 && plotData["ci"].length == 0){
						svg.append("g").attr("class", "x axis eqtlend")
						  .attr("transform", "translate(0,"+(eqtlTop+55*i+50)+")")
						  .call(xAxis)
						  .selectAll('text').style('font-size', '11px');
						svg.append("text").attr("text-anchor", "middle")
						  .attr("transform", "translate("+width/2+","+(height+30)+")")
						  .text("Chromosome "+chrom);
					}else if(i==eqtlgenes.length-1){
						svg.append("g").attr("class", "x axis eqtlend")
						  .attr("transform", "translate(0,"+(eqtlTop+55*i+50)+")")
						  .call(xAxis)
						  .selectAll('text').remove();
					}else{
						svg.append("rect")
						  .attr("x", 0).attr("y", y(0))
						  .attr("width", width).attr("height", 0.3)
						  .style("fill", "grey");
					}
					svg.append("g").attr("class", "y axis").call(yAxis)
						.selectAll('text')
						.style('font-size', '11px');
				}

				// labels
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate("+(-margin.left/2-15)+","+(eqtlTop+eqtlHeight/2)+")rotate(-90)")
					.text("eQTL -log10 P-value")
					.style("font-size", "10px");
			}
		}

		/*---------------------------------------------
  		| Plot chromatin interactions
  		---------------------------------------------*/
		if(ciplot == 1){
			if(plotData["citypes"].length==0){

			}else{
				xAxisLabel = "ci";
				plotData.ci.forEach(function(d){
					d[0] = +d[0]; //start1
					d[1] = +d[1]; //end1
					d[2] = +d[2]; //start2
					d[3] = +d[3]; //end2
					d[4] = +d[4]; //FDR
					d[8] = +d[8]; //y
				});
				var minFDR = d3.min(plotData.ci, function(d){if(d[4]>0){return d[4];}});
				plotData.ci.forEach(function(d){
						if(d[4]==0){
							d[4] = minFDR;
						}
				});
				var cicolor = d3.scale.linear().domain([0, d3.max(plotData.ci, function(d){return -Math.log10(d[4])})]).range(["pink", "red"]);
				var cur_height = 0;

				// plot chromatin interaction per data set
				for(var i =0; i<plotData["citypes"].length; i++){
					var types = plotData.citypes[i];
					types = types.split(":");
					var max_y = plotData.ciheight[i];
					var tmp_height = 0;
					if(max_y*ci_cellsize+10<30){
						tmp_height = 30;
					}else{
						tmp_height = max_y*ci_cellsize+10;
					}

					var y = d3.scale.linear().range([ciTop+5*i+cur_height+tmp_height, ciTop+5*i+cur_height]);
					var yAxis = d3.svg.axis().scale(y).orient("left").ticks(0);
					y.domain([max_y+1, 0]);

					svg.selectAll("rect.ci1").data(plotData.ci.filter(function(d){if(d[5]==types[0] && d[6]==types[1] && d[7]==types[2]){return d;}})).enter()
						.insert("rect").attr("class", "cirect1")
						.attr("x", function(d){
							if(x(d[0])<0){return 0}
							else if(x(d[0])>width){return width}
							else{return x(d[0])}
						})
						.attr("y", function(d){return y(d[8])})
						.attr("width", function(d){
							if(x(d[1])<0 || x(d[0])>width){return 0}
							else if(x(d[0])<0 && x(d[1])>width){return width}
							else if(x(d[1])>width){return width-x(d[0])}
							else if(x(d[0])<0){return x(d[1])}
							else{return x(d[1])-x(d[0])}
						})
						.attr("height", ci_cellsize)
						.attr("fill", function(d){
							if(x(d[0])>width || x(d[1])<0){return "none"}
							else{return cicolor(-Math.log10(d[4]))}
						})
						.attr("stroke", function(d){
							if(x(d[0])>width){return "none"}
							else{return "grey"}
						})
						.attr("stroke-width", 0.1);
					svg.selectAll("rect.ci2").data(plotData.ci.filter(function(d){if(d[5]==types[0] && d[6]==types[1] && d[7]==types[2]){return d;}})).enter()
						.insert("rect").attr("class", "cirect2")
						.attr("x", function(d){
							if(x(d[2])<0){return 0}
							else if(x(d[2])>width){return width}
							else{return x(d[2])}
						})
						.attr("y", function(d){return y(d[8])})
						.attr("width", function(d){
							if(x(d[3])<0 || x(d[2])>width){return 0}
							else if(x(d[2])<0 && x(d[3])>width){return width}
							else if(x(d[3])>width){return width-x(d[2])}
							else if(x(d[2])<0){return x(d[3])}
							else{return x(d[3])-x(d[2])}
						})
						.attr("height", ci_cellsize)
						.attr("fill", function(d){
							if(x(d[2])>width){return "none"}
							else{return cicolor(-Math.log10(d[4]))}
						})
						.attr("stroke", function(d){
							if(x(d[2])>width || x(d[3])<0){return "none"}
							else{return "grey"}
						})
						.attr("stroke-width", 0.1);

					svg.selectAll("rect.ci").data(plotData.ci.filter(function(d){if(d[5]==types[0] && d[6]==types[1] && d[7]==types[2] && Math.abs(d[2]-d[1])>1){return d;}})).enter()
						.insert("rect").attr("class", "cirect")
						.attr("x", function(d){
							if(x(d[1])<0){return 0}
							else if(x(d[1])>width){return width}
							else{return x(d[1])}
						})
						.attr("y", function(d){return y(d[8])+ci_cellsize*0.5})
						.attr("width", function(d){
							if(x(d[2])<0 || x(d[1])>width){return 0}
							else if(x(d[2])>width && x(d[1])<0){return width}
							else if(x(d[1])<0){return x(d[2])}
							else if(x(d[2])>width){return width - x(d[1])}
							else{return x(d[2])-x(d[1])}
						})
						.attr("height", 0.8)
						.attr("fill", "grey");

					svg.append("text").attr("text-anchor", "start")
						.attr("transform", "translate(10,"+(ciTop+cur_height+5*i+2)+")")
						.text(types.join(" "))
						.style("font-size", "8.5px").style("font-family", "sans-serif");
					svg.append("g").attr("class", "y axis").call(yAxis)
						.selectAll('text').attr("transform", "translate(-5,0)").style('font-size', '11px');
					if(i==plotData.citypes.length-1 && plotData["cieid"].length==0){
						svg.append("g").attr("class", "x axis ci")
							.attr("transform", "translate(0,"+(ciTop+cur_height+5*i+tmp_height)+")")
							.call(xAxis);
						svg.append("text").attr("text-anchor", "middle")
						  .attr("transform", "translate("+width/2+","+(height+35)+")")
						  .text("Chromosome "+chrom);
					}else if(i==plotData.citypes.length-1){
						svg.append("g").attr("class", "x axis ci")
							.attr("transform", "translate(0,"+(ciTop+cur_height+5*i+tmp_height)+")")
							.call(xAxis).selectAll("text").remove();
					}else{
						svg.append("rect")
						  .attr("x", 0).attr("y", y(max_y+1))
						  .attr("width", width).attr("height", 0.3)
						  .style("fill", "grey");
					}
					cur_height += tmp_height;
				}

				// plot enhancer and promoter if annoated
				if(plotData["cieid"].length>0){
					xAxisLabel="cireg";
					plotData.cireg.forEach(function(d){
						d[0] = +d[0]; //start
						d[1] = +d[1]; //end
					});
					var cieid = plotData["cieid"];
					cieid.forEach(function(d){
						if(eid.indexOf(d)<0){eid.push(d)}
					});
					var chr15gcol = [];
					for(var i=0; i<Chr15eid.length; i++){
						if(cieid.indexOf(Chr15eid[i])>=0){
							chr15gcol.push(Chr15GroupCols[i]);
						}
					}
					var tileHeight = ciregHeight/cieid.length;

					// legend
					svg.append("rect").attr("x", width+20).attr("y", ciregTop+5)
					  .attr("width", 20).attr("height", 5).attr("fill", "orange");
					svg.append("text").attr("x", width+45).attr("y", ciregTop+10)
					  .text("Enhancers").style("font-size", "10px");
					svg.append("rect").attr("x", width+20).attr("y", ciregTop+15)
  					  .attr("width", 20).attr("height", 5).attr("fill", "green");
  					svg.append("text").attr("x", width+45).attr("y", ciregTop+20)
  					  .text("Promoters").style("font-size", "10px");
					svg.append("rect").attr("x", width+20).attr("y", ciregTop+25)
  					  .attr("width", 20).attr("height", 5).attr("fill", "blue");
  					svg.append("text").attr("x", width+45).attr("y", ciregTop+30)
  					  .text("Dyadic").style("font-size", "10px");

					var yCireg = d3.scale.ordinal().domain(cieid).rangeBands([ciregTop, ciregTop+ciregHeight]);
					var yAxisCireg = d3.svg.axis().scale(yCireg).tickFormat(function(d){return d;}).orient("left");
					svg.selectAll("rect.cireg").data(plotData.cireg).enter().append("g")
						.insert('rect').attr("class", "ciregrect")
						.attr('x', function(d){
							if(x(d[0])<0){return 0;}
							else{return x(d[0]);}
						})
						.attr('y', function(d){return yCireg(d[3])})
						.attr("width", function(d){
							return x(d[1])-x(d[0]);
							if(x(d[1])<0 || x(d[0])>width){return 0}
							else if(x(d[0])<0 && x(d[1])>width){return width}
							else if(x(d[1])>width){return width-x(d[0])}
							else if(x(d[0])<0){return x(d[1])}
							else{return x(d[1])-x(d[0])}
						})
						.attr("height", tileHeight)
						.attr("fill", function(d){
							if(x(d[1])<0 || x(d[0])>width){return "none"}
							else if(d[2]=="enh"){return "orange"}
							else if(d[2]=="prom"){return "green"}
							else{return "blue"}
						});
					if(cieid.length>30){
						svg.append("g").attr("class", "y axis").call(yAxisCireg)
							.selectAll('text').remove();
					}else{
						svg.append("g").attr("class", "y axis").call(yAxisCireg)
							.selectAll('text').attr("transform", "translate(-5,0)").style('font-size', '11px');
					}
					for(var i=0; i<cieid.length; i++){
						svg.append("rect").attr("x", -10).attr("y", yCireg(cieid[i]))
							.attr("width", 10).attr("height",tileHeight)
							.attr("fill", chr15gcol[i]);
					}
					svg.append("g").attr("class", "x axis cireg")
						.attr("transform", "translate(0,"+(ciregTop+ciregHeight)+")")
						.call(xAxis);
					svg.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate("+(-margin.left/2-15)+","+(ciregTop+ciregHeight/2)+")rotate(-90)")
						.text("Regulatory elements");
					svg.append("text").attr("text-anchor", "middle")
					  .attr("transform", "translate("+width/2+","+(height+35)+")")
					  .text("Chromosome "+chrom);
				}
			}
		}

		// add style to text
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');

		// zoom function
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
		  }else if (xAxisLabel=="ci"){
			svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.RDB").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.Chr15").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.eqtlend").call(xAxis).selectAll("text").remove();
			svg.select(".x.axis.ci").call(xAxis);
		  }else if (xAxisLabel=="cireg"){
			svg.select(".x.axis.GWAS").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.genes").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.CADD").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.RDB").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.Chr15").call(xAxis).selectAll("text").remove();
	        svg.select(".x.axis.eqtlend").call(xAxis).selectAll("text").remove();
			svg.select(".x.axis.ci").call(xAxis).selectAll("text").remove();
			svg.select(".x.axis.cireg").call(xAxis);
		  }

	      svg.selectAll(".GWASdot").attr("cx", function(d){return x(d[2]);})
	        .style("fill", function(d){if(x(d[2])<0 || x(d[2])>width){return "none";}else if(d[5]==0){return "grey";}else{return colorScale(d[6])}});
	      svg.selectAll(".GWASnonLD").attr("cx", function(d){return x(d[1]);})
	        .style("fill", function(d){if(x(d[1])<0 || x(d[1])>width){return "none";}else{return "grey";}});
	      svg.selectAll(".KGSNPs").attr("x", function(d){return x(d[2]);})
	        .style("fill", function(d){if(x(d[2])<0 || x(d[2])>width){return "none";}else if(d[5]==0){return "grey"}else{return colorScale(d[6])}});
	      svg.selectAll(".leadSNPs").attr("cx", function(d){return x(d[2]);})
	        .style("fill", function(d){
	          if(x(d[2])<0 || x(d[2])>width){return "none";}
	          else if(d[5]==2){return colorScale(d[6]);}
	          else if(d[5]==3){return "#9933ff"}
	          else if(d[5]==4){return "#4d0099"}
	        })
	        .style("stroke", function(d){if(x(d[2])<0 || x(d[2])>width){return "none";}else{return "black"}});
	      svg.selectAll(".CADDdot").attr("cx", function(d){return x(d[2]);})
	        .style("fill", function(d){
	          if(x(d[2])<0 || x(d[2])>width){return "none";}
	          else if(d[5]==0){return "grey"}
	          else if(d[13]==0){return "grey"}
	          else if(d[12]=="exonic"){return "blue"}
	          else{return "skyblue"}});
	      svg.selectAll(".RDBdot").attr("cx", function(d){return x(d[2]);})
	        .style("fill", function(d){
	          if(x(d[2])<0 || x(d[2])>width){return "none";}
	          else if(d[5]==0){return "grey"}
	          else if(d[13]==0){return "grey"}
	          else{return "MediumAquaMarine"}});
	      svg.selectAll(".genesrect").attr("x", function(d){
	          if(x(d[2])<0 || x(d[3])<0){return 0;}
	          else{return x(d[2]);}
	        })
	        .attr("width", function(d){
	          if(x(d[3])<0 || x(d[2])>width){return 0;}
	          else if(x(d[3])>width && x(d[2])<0){return width;}
	          else if(x(d[3])>width){return width-x(d[2]);}
	          else if(x(d[2])<0){return x(d[3]);}
	          else{return x(d[3])-x(d[2]);}
	        })
	        .style("fill", function(d){if(x(d[3])<0 || x(d[2])>width){return "none";}
	          else if(genes["mappedGenes"].indexOf(d[1])>=0){return "red";}
	          else if(d[5]=="protein_coding"){return "blue";}
	          else{return "#383838"}
	        });
	      svg.selectAll(".geneName")
		      .attr("x", function(d){
		        if(x(d[2])<0 && x(d[3])>width){return width/2;}
		        else if(x(d[2])<0){return x(d[3])/2;}
		        else if(x(d[3])>width){return x(d[2])+(width-x(d[2]))/2;}
		        else{return x(((d[3]-d[2])/2)+d[2]);}
		      })
		      .style("fill", function(d){
		        if(x(d[3])<0 || x(d[2])>width){return "none";}
		        else{return "black";}
		      });
	      svg.selectAll(".exons").attr("x", function(d){
	          if(x(d[6])<0 || x(d[7])<0){return 0;}
	          else{return x(d[6]);}
	        })
	        .attr("width", function(d){
	          if(x(d[7])<0 || x(d[6])>width){return 0;}
	          else if(x(d[6])<0 && x(d[7])>width){return width;}
	          else if(x(d[7])>width){return width-x(d[6]);}
	          else if(x(d[6])<0){return x(d[7]);}
	          else{return x(d[7])-x(d[6]);}
	        })
	        .style("fill", function(d){if(x(d[7])<0 || x(d[6])>width){return "none";}
	          else if(genes["mappedGenes"].indexOf(d[1])>=0){return "red";}
	          else if(d[5]=="protein_coding"){return "blue";}
	          else{return "#383838"}
	        })
	      svg.selectAll(".Chr15rect")
	        .attr("x", function(d){
	          if(x(d[1])<0 || x(d[2])<0){return 0;}
	          else{return x(d[1]);}
	        })
	        .attr("width", function(d){
	          if(x(d[2])<0 || x(d[1])>width){return 0;}
	          else if(x(d[1])<0 && x(d[2])>width){return width;}
	          else if(x(d[1])<0){return x(d[2]);}
	          else if(x(d[2])>width){return width-x(d[1]);}
	          else{return x(d[2])-x(d[1]);}
	        })
	        .style("fill", function(d){
	          if(x(d[2])<0 || x(d[1])>width){return "none";}
	          else{return Chr15colors[d[3]*1-1];}
	        });
	      svg.selectAll(".eqtldot").attr("cx", function(d){return x(d[11])})
	        .style("fill", function(d){
	          if(x(d[11])<0 || x(d[11])>width){return "none";}
	          else if(d[13]==0){return "grey"}
	          else{return eQTLcolors[d[2]]}
	        });
		  svg.selectAll(".cirect1")
			  .attr("x", function(d){
				  if(x(d[0])<0){return 0}
				  else if(x(d[0])>width){return width}
				  else{return x(d[0])}
			  })
			  .attr("width", function(d){
				  if(x(d[1])<0 || x(d[0])>width){return 0}
				  else if(x(d[0])<0 && x(d[1])>width){return width}
				  else if(x(d[1])>width){return width-x(d[0])}
				  else if(x(d[0])<0){return x(d[1])}
				  else{return x(d[1])-x(d[0])}
			  })
			  .attr("fill", function(d){
				  if(x(d[0])>width || x(d[1])<0){return "none"}
				  else{return cicolor(-Math.log10(d[4]))}
			  })
			  .attr("stroke", function(d){
				  if(x(d[0])>width){return "none"}
				  else{return "grey"}
			  });
		  svg.selectAll(".cirect2")
			  .attr("x", function(d){
				  if(x(d[2])<0){return 0}
				  else if(x(d[2])>width){return width}
				  else{return x(d[2])}
			  })
			  .attr("width", function(d){
				  if(x(d[3])<0 || x(d[2])>width){return 0}
				  else if(x(d[2])<0 && x(d[3])>width){return width}
				  else if(x(d[3])>width){return width-x(d[2])}
				  else if(x(d[2])<0){return x(d[3])}
				  else{return x(d[3])-x(d[2])}
			  })
			  .attr("fill", function(d){
				  if(x(d[2])>width || x(d[3])<0){return "none"}
				  else{return cicolor(-Math.log10(d[4]))}
			  })
			  .attr("stroke", function(d){
				  if(x(d[2])>width){return "none"}
				  else{return "grey"}
			  });
		  svg.selectAll(".cirect")
			  .attr("x", function(d){
				  if(x(d[1])<0){return 0}
				  else if(x(d[1])>width){return width}
				  else{return x(d[1])}
			  })
			  .attr("width", function(d){
				  if(x(d[2])<0 || x(d[1])>width){return 0}
				  else if(x(d[2])>width && x(d[1])<0){return width}
				  else if(x(d[1])<0){return x(d[2])}
				  else if(x(d[2])>width){return width - x(d[1])}
				  else{return x(d[2])-x(d[1])}
			  });

		  svg.selectAll(".ciregrect")
			  .attr("x", function(d){
				  if(x(d[0])<0){return 0;}
				  else{return x(d[0]);}
			  })
			  .attr("width", function(d){
				  return x(d[1])-x(d[0]);
				  if(x(d[1])<0 || x(d[0])>width){return 0}
				  else if(x(d[0])<0 && x(d[1])>width){return width}
				  else if(x(d[1])>width){return width-x(d[0])}
				  else if(x(d[0])<0){return x(d[1])}
				  else{return x(d[1])-x(d[0])}
			  })
			  .attr("fill", function(d){
				  if(x(d[1])<0 || x(d[0])>width){return "none"}
				  else if(d[2]=="enh"){return "orange"}
				  else if(d[2]=="prom"){return "green"}
				  else{return "blue"}
			  });
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
  			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
  			svg.selectAll('text').style('font-family', 'sans-serif');
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
	}

});

function geneOver(genes, x, width){
  var tg = genes;

  for(var i=1; i<tg.length; i++){
    var temp = tg.filter(function(d2){
      if((d2[2]<=tg[i][2] && d2[3] >= tg[i][2] && d2[3]<=tg[i][3])
        || (d2[2]<=tg[i][2] && d2[3]>=tg[i][3])
      ){return d2;}
      else if(x((d2[3]+d2[2]+d2[1].length*12)/2)>=x((tg[i][3]+tg[i][2])/2)-((tg[i][1].length*12)/2)
          && x((d2[3]+d2[2]+d2[1].length*12)/2)<=x((tg[i][3]+tg[i][2])/2)+((tg[i][1].length*12)/2)
      ){return d2}
    })
    if(temp.length>1){
		var yall = [];
		for(var j=0; j<temp.length; j++){
			if(temp[j][1] != tg[i][1]){
				yall.push(temp[j][6]);
			}
		}
		tg[i][6] = getMinY(yall);
    }else{
    	tg[i][6] = 1;
    }
  }

  return tg;
}

function getMinY(y){
	y.sort(function(a,b){return a-b});
	var miny = 1;
	if(Math.min.apply(null, y) > 1){return 1;}
	if(y.length==1){return y[0]+1;}
	for(var l=1; l<y.length; l++){
		if(y[l]-y[l-1] > 1){
			miny = y[l-1]+1;
			break;
		}else{
			miny = y[l]+1;
		}
	}
	return miny;
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

function ImgDown(name, type){
  $('#'+name+'Data').val($('#'+name).html());
  $('#'+name+'Type').val(type);
  $('#'+name+'ID').val(id);
  $('#'+name+'FileName').val(name);
  $('#'+name+'Dir').val(prefix);
  $('#'+name+'Submit').trigger('click');
}
