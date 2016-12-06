var geneTable;
$(document).ready(function(){
  // $('#test').html("Status: "+status+"<br/>");
  $('#newquery').show();
  if(status=="new"){
    checkInput();
    $('#resultSide').hide()
    // $('#results').hide();
  }else if(status=="query"){
    $('#geneSubmit').attr("disabled", true);
    var id = IPGAPvar.id;
    var filedir = IPGAPvar.filedir;
    var gtype = IPGAPvar.gtype;
    var gval = IPGAPvar.gval;
    var bkgtype = IPGAPvar.bkgtype;
    var bkgval = IPGAPvar.bkgval;
    // var Xchr = IPGAPvar.Xchr;
    var MHC = IPGAPvar.MHC;
    var adjPmeth = IPGAPvar.adjPmeth;
    var adjPcut = IPGAPvar.adjPcut;
    var minOverlap = IPGAPvar.minOverlap;
    // $('#test').html("adjPmeth: "+adjPmeth);

    if(gtype=="text"){
      $('#genes').val(gval.replace(/:/g, '\n'));
    }

    if(bkgtype == "select"){
      // var s = bkgval.split(':');
      var tmp = document.getElementById('genetype');
      for(var i=0; i<tmp.options.length; i++){
        if(bkgval.indexOf(tmp.options[i].value)>=0){
          tmp.options[i].selected=true;
        }
      }
    }else if(bkgtype == "text"){
      $('#bkgenes').val() = bkgval.replace(/:/g, '\n');
    }

    // if(Xchr==1){
    //   $('#Xchr').attr('checked', true);
    // }

    if(MHC==1){
      $('#MHC').attr('checked', true);
    }

    d3.select('#expHeat').select('svg').remove();
    d3.select('#tsEnrichBar').select('svg').remove();
    $.ajax({
      url: "geneQuery",
      type: "POST",
      data: {
        filedir: filedir,
        gtype: gtype,
        gval: gval,
        bkgtype: bkgtype,
        bkgval: bkgval,
        // Xchr: Xchr,
        MHC: MHC,
        adjPmeth: adjPmeth,
        adjPcut: adjPcut,
        minOverlap: minOverlap
      },
      beforeSend: function(){
        // $('#results').hide();
        $('#resultSide').hide()
        // $('#loadingGeneQuery').show();
        // $('#loadingGeneQuery').html('<h4>Running gene query</h4><img src="'+public_path+'" width="50" height="50"/><br/><br/>');
        AjaxLoad();
      },
      success: function(){
        // $('#loadingGeneQuery').html("");
        // $('#loadingGeneQuery').hide();
        $('#resultSide').show()
        // $('#results').show();
        $('#overlay').remove();
      },
      complete: function(){
        checkInput();
        expHeatMap(id);
        tsEnrich(id);
        tsGeneralEnrich(id);
        GeneSet(id);
        GeneTable(id);
      }
    });

    // $('#DEGdown').on('click', function(){
    //   fileDown('DEG.txt', id);
    // });
    $('#DEGgdown').on('click', function(){
      fileDown('DEGgeneral.txt', id);
    });
    $('#GSdown').on('click', function(){
      filedown('GS.txt', id);
    });
  }

  // $('#geneSubmit').on('click', function(){
  //   var g = document.getElementById('genes').value;
  //   var gfile = $('#genesfile').val();
  //   var bkg_select = 0;
  //   var tmp = document.getElementById('genetype');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       bkg_select = 1;
  //       break;
  //     }
  //   }
  //   var bkg = document.getElementById('bkgenes').value;
  //   var bkgfile = $('#bkgenesfile').val();
  //
  //   var gtype;
  //   var bkgtype;
  //   if(g.length>0){
  //     gtype=1; //textbox
  //   }else{
  //     gtype=2; //file
  //   }
  //
  //   if(bkg_select==1){
  //     bkgtype = 0; //select
  //   }else if(bkg.length>0){
  //     bkgtype = 1; //text box
  //   }else{
  //     bkgtype = 2; //file
  //   }
  //
  //   var genes = g.replace(/\n/g, ":");
  //   g = g.split("\n");
  //
  //   var bkgenes=[];
  //   var tmp = document.getElementById('genetype');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       bkgenes.push(tmp.options[i].value);
  //     }
  //   }
  //   bkgenes = bkgenes.join(":");
  //
  //   d3.select('#expHeat').select('svg').remove();
  //   d3.select('#tsEnrichBar').select('svg').remove();
  //   $.ajax({
  //     url: "gene2func/geneQuery",
  //     type: "POST",
  //     data: {
  //       genes : genes,
  //       bkgenes : bkgenes
  //     },
  //     beforeSend: function(){
  //       $('#results').hide();
  //       $('#loadingGeneQuery').show();
  //       $('#loadingGeneQuery').html('<h4>Running gene query</h4><img src="'+public_path+'" width="50" height="50"/><br/><br/>');
  //     },
  //     success: function(){
  //       $('#loadingGeneQuery').html("");
  //       $('#loadingGeneQuery').hide();
  //       $('#results').show();
  //     },
  //     complete: function(){
  //       expHeatMap();
  //       tsEnrich();
  //       tsGeneralEnrich();
  //       GeneSet();
  //     }
  //   });
  //
  // });
});

function checkInput(){
  var g = document.getElementById('genes').value;
  var gfile = $('#genesfile').val().length;
  if(g.length==0 && gfile==0){
    $('#GeneCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please either copy-paste or upload a liet of genes to test.</div>');
    $('#geneSubmit').attr("disabled", true);
  }else if(g.length>0 && gfile>0){
    $('#GeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used. To use uploaded file, please clear the text box.</div>');
  }else if(g.length > 0){
    $('#GeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used.</div>');
  }else if(gfile > 0){
    $('#GeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The uploaded file will be used.</div>');
  }

  var bkg_select = 0;
  var tmp = document.getElementById('genetype');
  for(var i=0; i<tmp.options.length; i++){
    if(tmp.options[i].selected===true){
      bkg_select = 1;
      break;
    }
  }
  var bkg = document.getElementById('bkgenes').value;
  var bkgfile = $('#bkgenesfile').val().length;

  if(bkg_select==0 && bkg.length==0 && bkgfile==0){
    $('#bkGeneCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide backgrond genes.</div>');
    $('#geneSubmit').attr("disabled", true);
  }else if(bkg_select==1 && (bkg.length>0 || bkgfile>0)){
    $('#bkGeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. You have provided multiple options. Selected gene types are used as background gene. To use other options, please clear the selection.</div>');
  }else if(bkg_select==1){
    $('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Selected gene type will be used as background.</div>');
  }else if(bkg.length>0 && bkgfile>0){
    $('#bkGeneCheck').html('<div class="alert alert-warning" style="padding-bottom: 10; padding-top: 10;">OK. You have provided multiple options. Genes in the text box will be used as background. To use other options, please clear the text box.</div>');
  }else if(bkg.length>0){
    $('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Genes in the text box will be used.</div>');
  }else if(bkgfile>0){
    $('#bkGeneCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The uploaded file will be used.</div>');
  }

  if(g.length+gfile > 0 && (bkg_select==1 || bkg.length+bkgfile>0)){
    $('#geneSubmit').attr("disabled", false);
  }

};
function AjaxLoad(){
  var over = '<div id="overlay"><div id="loading">'
          +'<h4>Running gene test</h4>'
          +'<p>Please wait for a moment</br>'
          +'<p>Currentry this job takes 1-2 min</p>'
          +'<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>'
          +'</div></div>';
  $(over).appendTo('body');
}

function GeneSetPlot(category){
  $('#'+category).show();
  $('#'+category+'Table').hide();
}

function GeneSetTable(category){
  $('#'+category).hide();
  $('#'+category+'Table').show();
}

Array.prototype.unique = function(a){
    return function(){ return this.filter(a) }
}(function(a,b,c){ return c.indexOf(a,b+1) < 0 });

function expHeatMap(id){
  d3.select('#expHeat').select("svg").remove();
  var itemSizeRow = 15, cellSize=itemSizeRow-1, itemSizeCol=8;

  d3.json("d3text/"+id+"/exp.txt", function(response){
    var data = response.map(function(item){
      var newItem = {};
      newItem.tissue = item.tissue;
      newItem.gene = item.gene;
      newItem.exp = item.exp;
      return newItem;
    });

    var x_elements = d3.set(data.map(function(item){return item.tissue})).values(),
        y_elements = d3.set(data.map(function(item){return item.gene})).values();

    var margin = {top: 10, right: 10, bottom: 200, left: 200},
      width = 800,
      height = (itemSizeCol*y_elements.length);

    var xScale = d3.scale.ordinal().domain(x_elements).rangeBands([0,x_elements.length*itemSizeRow]);
    var xAxis = d3.svg.axis().scale(xScale).tickFormat(function(d){return d;}).orient("bottom");
    var yScale = d3.scale.ordinal().domain(y_elements).rangeBands([0,y_elements.length*itemSizeCol]);
    var yAxis = d3.svg.axis().scale(yScale).tickFormat(function(d){return d;}).orient("left");
    //var colorScale = d3.scale.linear().domain([d3.min(data,function(d){return d.exp;}), d3.max(data, function(d){return d.exp;})]).range(["blue", "red"])
    var colorScale = d3.scale.linear().domain([0, (5.7/2), 5.7]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
    var svg = d3.select('#expHeat').append('svg')
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
    var cells = svg.selectAll('rect').data(data).enter().append("g")
                .append("rect").attr('class', 'cell')
                .attr("class", 'tile')
                .attr("width", cellSize).attr("height", itemSizeCol-0.5)
                .attr('x', function(d){return xScale(d.tissue)})
                .attr('y', function(d){return yScale(d.gene)})
                .attr('fill', function(d){return colorScale(d.exp)})
                .on("click", function(d){
                  d3.select('#expBox').select('svg').remove;
                  document.getElementById('expBox').innerHTML="<h3>Expression of "+d.gene+"</h3>";
                  var g = d.gene.split(":");
                  // geneBoxPlot(g[0]);
                });
    svg.append("g").attr("class", "y axis").call(yAxis).selectAll('text').attr('font-weight', 'normal').attr('class', "smalltext");
    svg.append("g").attr("class", "x axis").attr("transform", "translate(0,"+(y_elements.length*itemSizeCol)+")")
      .call(xAxis).selectAll('text').attr('font-weight', 'normal')
      .style("text-anchor", "end").attr("transform", function (d) {return "rotate(-65)";})
      .attr("dx","-.65em").attr("dy", "-.45em");
  });
}

function tsEnrich(id){
  d3.select('#tsEnrichBar').select('svg').remove();
  var span = 150;
  var currentHeight = 0;
  var margin = {top: 20, right: 20, bottom: 200, left: 50},
      width = 900,
      height = span*3+20;

  var x = d3.scale.ordinal().rangeBands([0,width]);
  var xAxis = d3.svg.axis().scale(x).orient("bottom");
  var svg = d3.select('#tsEnrichBar').append('svg')
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

  var gradient1 = svg.append("defs").append("linearGradient")
                    .attr("id", 'gradient1')
                    .attr("x1", "0%")
                    .attr("y1", "0%")
                    .attr("x2", "100%")
                    .attr("y2", "100%")
                    .attr("spreadMethod", "pad");
  gradient1.append("stop").attr("offset", "0%")
          .attr("stop-color", "#c00")
          .attr("stop-ocupacity", 1);
  gradient1.append("stop").attr("offset", "100%")
          .attr("stop-color", "#3c2c2c")
          .attr("stop-ocupacity", 1);

  var gradient2 = svg.append("defs").append("linearGradient")
                    .attr("id", 'gradient2')
                    .attr("x1", "0%")
                    .attr("y1", "0%")
                    .attr("x2", "100%")
                    .attr("y2", "100%")
                    .attr("spreadMethod", "pad");
  gradient2.append("stop").attr("offset", "0%")
          .attr("stop-color", "#5668f4")
          .attr("stop-ocupacity", 1);
  gradient2.append("stop").attr("offset", "100%")
          .attr("stop-color", "#606060")
          .attr("stop-ocupacity", 1);

  d3.json("d3text/"+id+"/DEG.txt", function(data){
    data.forEach(function(d){
      d.logFDR = +d.logFDR;
      d.logP = +d.logP;
      d.FDR = +d.FDR;
    });
    x.domain(data.map(function(d){return d.GeneSet;}));

    //up-regulated
    var yup = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxisup = d3.svg.axis().scale(yup).orient("left");
    yup.domain([currentHeight, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.up').data(data.filter(function(d){if(d.Category=="DEG.up"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return yup(d.logP)})
      .attr("height", function(d){return currentHeight+span-yup(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append('g').attr("class", "y axis")
      .call(yAxisup);
    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0,"+(currentHeight+span)+")")
      .call(xAxis).selectAll('text').remove();
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("Up-regulated DEG");
    currentHeight += span+10;

    //down regulated
    var ydown = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxisdown = d3.svg.axis().scale(ydown).orient("left");
    ydown.domain([0, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.down').data(data.filter(function(d){if(d.Category=="DEG.down"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return ydown(d.logP)})
      .attr("height", function(d){return currentHeight+span-ydown(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append('g').attr("class", "y axis")
      .call(yAxisdown);
    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0, "+(currentHeight+span)+")")
      .call(xAxis).selectAll('text').remove();
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("Down-regulated DEG");
    currentHeight += span+10;
    //twoside
    var y = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxis = d3.svg.axis().scale(y).orient("left");
    y.domain([0, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.two').data(data.filter(function(d){if(d.Category=="DEG.twoside"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return y(d.logP)})
      .attr("height", function(d){return currentHeight+span-y(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("DEG (both side)");

    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0,"+(currentHeight+span)+")")
      .call(xAxis).selectAll('text')
      .attr("transform", function (d) {return "rotate(-65)";})
      .attr("dy", "-.45em")
      .attr("dx", "-.65em")
      .style("text-anchor", "end");

    svg.append('g').attr("class", "y axis")
      .call(yAxis);
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-margin.left/2-10)+","+height/2+")rotate(-90)")
      .text("-log 10 P-value");
  });
}

function tsGeneralEnrich(id){
  d3.select('#tsGeneralEnrichBar').select('svg').remove();
  var span = 150;
  var currentHeight = 0;
  var margin = {top: 20, right: 20, bottom: 80, left: 50},
      width = 900,
      height = span*3+20;

  var x = d3.scale.ordinal().rangeBands([0,width]);
  var xAxis = d3.svg.axis().scale(x).orient("bottom");
  var svg = d3.select('#tsGeneralEnrichBar').append('svg')
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

  var gradient1 = svg.append("defs").append("linearGradient")
                    .attr("id", 'gradient1')
                    .attr("x1", "0%")
                    .attr("y1", "0%")
                    .attr("x2", "100%")
                    .attr("y2", "100%")
                    .attr("spreadMethod", "pad");
  gradient1.append("stop").attr("offset", "0%")
          .attr("stop-color", "#c00")
          .attr("stop-ocupacity", 1);
  gradient1.append("stop").attr("offset", "100%")
          .attr("stop-color", "#3c2c2c")
          .attr("stop-ocupacity", 1);

  var gradient2 = svg.append("defs").append("linearGradient")
                    .attr("id", 'gradient2')
                    .attr("x1", "0%")
                    .attr("y1", "0%")
                    .attr("x2", "100%")
                    .attr("y2", "100%")
                    .attr("spreadMethod", "pad");
  gradient2.append("stop").attr("offset", "0%")
          .attr("stop-color", "#5668f4")
          .attr("stop-ocupacity", 1);
  gradient2.append("stop").attr("offset", "100%")
          .attr("stop-color", "#606060")
          .attr("stop-ocupacity", 1);
  d3.json("d3text/"+id+"/DEGgeneral.txt", function(data){
    data.forEach(function(d){
      d.logFDR = +d.logFDR;
      d.logP = +d.logP;
      d.FDR = +d.FDR;
    });
    x.domain(data.map(function(d){return d.GeneSet;}));

    //up-regulated
    var yup = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxisup = d3.svg.axis().scale(yup).orient("left");
    yup.domain([0, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.up').data(data.filter(function(d){if(d.Category=="DEG.up"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return yup(d.logP)})
      .attr("height", function(d){return currentHeight+span-yup(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append('g').attr("class", "y axis")
      .call(yAxisup);
    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0,"+(currentHeight+span)+")")
      .call(xAxis).selectAll('text').remove();
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("Up-regulated DEG");
    currentHeight += span+10;

    //down regulated
    var ydown = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxisdown = d3.svg.axis().scale(ydown).orient("left");
    ydown.domain([0, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.down').data(data.filter(function(d){if(d.Category=="DEG.down"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return ydown(d.logP)})
      .attr("height", function(d){return currentHeight+span-ydown(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append('g').attr("class", "y axis")
      .call(yAxisdown);
    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0, "+(currentHeight+span)+")")
      .call(xAxis).selectAll('text').remove();
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("Down-regulated DEG");
    currentHeight += span+10;
    //twoside
    var y = d3.scale.linear().range([currentHeight+span, currentHeight]);
    var yAxis = d3.svg.axis().scale(y).orient("left");
    y.domain([0, d3.max(data, function(d){return d.logP})]);

    svg.selectAll('rect.two').data(data.filter(function(d){if(d.Category=="DEG.twoside"){return d;}})).enter()
      .append("rect").attr("class", "bar")
      .attr("x", function(d){return x(d.GeneSet);})
      .attr("width", x.rangeBand())
      .attr("y", function(d){return y(d.logP)})
      .attr("height", function(d){return height-y(d.logP);})
      .style("fill", function(d){
        if(d.FDR>0.05){return "url(#gradient2)";}
        else{return "url(#gradient1)";}
      });
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width+margin.right/2)+","+(currentHeight+span/2)+")rotate(90)")
      .text("DEG (both side)");

    svg.append('g').attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")")
      .call(xAxis).selectAll('text')
      .attr("transform", function (d) {return "rotate(-65)";})
      .attr("dy", "-.45em")
      .attr("dx", "-.65em")
      .style("text-anchor", "end");

    svg.append('g').attr("class", "y axis")
      .call(yAxis);
    svg.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-margin.left/2-10)+","+height/2+")rotate(-90)")
      .text("-log 10 P-value");
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
  var category_title = {'Hallmark_gene_sets' : 'Hallmark gene set (MsigDB v5.2 h)',
                  'Positional_gene_sets' : 'Positional gene sets (MsigDB v5.2 c1)',
                  'Curetaed_gene_sets' : 'All curated gene sets (MsigDB v5.2 c2)',
                  'Chemical_and_Genetic_pertubation' : 'Chemical and Genetic pertubation (MsigDB v5.2 c2)',
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
                  'Wikipathways' : 'Wikipathways (Curated version 20161010)',
                  'GWAScatalog' : 'GWAS catalog (reported genes, ver. e85 20160927)'
                };
  d3.json("d3text/"+id+"/GS.txt", function(data){
    // data.forEach(function(d){
    //   d.logP = +d.logP;
    //   d.logFDR = +d.logFDR;
    // });

    // var category = d3.set(data.map(function(d){return d.Category;})).values();
    // $('#test').html("<p>Category 1: "+category[0]+"</p>");
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
        d.logP = +d.logP;
        d.logFDR = +d.logFDR;
        var g = d.genes.split(":");
        for(var j=0; j<g.length; j++){
          genesplot.push({"GeneSet":d.GeneSet, "gene":g[j]})
        }
        if(d.GeneSet.length>gs_max){gs_max = d.GeneSet.length;}
      });
      genes = d3.set(genesplot.map(function(d){return d.gene;})).values();

      if(tdata.length==0){
        var panel = $('<div class="panel panel-default" style="padding-top:0;"><div class="panel-heading" style="height: 35px; padding-top:0.1;"><a href="#'
          +category[i]+'Panel" data-toggle="collapse" style="color: black;"><h4>'
          +category_title[category[i]]+'<tab>(0)</h4></div><div class="panel-body collapse" id="'
          +category[i]+'Panel"><div id="'+category[i]+'" style="text-align: center;">No significant results</div><div id="'
          +category[i]+'Table"></div></div></div>');
          $('#GeneSet').append(panel);
      }else{
        // $('#test').append("<p>"+category[i]+"<br/>gs_max: "+gs_max+'<br/>genes: '+genes.length+'</p>');
        // add div
        var panel = $('<div class="panel panel-default" style="padding-top:0;"><div class="panel-heading" style="height: 35px; padding-top:0.1;"><a href="#'
          +category[i]+'Panel" data-toggle="collapse" style="color: black;"><h4>'
          +category_title[category[i]]+'<tab>('+tdata.length+')</h4></div><div class="panel-body collapse" id="'
          +category[i]+'Panel"><p><a onclick="GeneSetPlot('+"'"+category[i]+"'"+');">Plot</a> / <a onclick="GeneSetTable('+
          "'"+category[i]+"'"+');">Table</a></p><div id="'+category[i]+'"></div><div id="'
          +category[i]+'Table"></div></div></div>');
        $('#GeneSet').append(panel);
        $('#'+category[i]+'Table').hide();

        // Plots
        var gs = d3.set(tdata.map(function(d){return d.GeneSet})).values();
        var ngs = gs.length;
        var barplotwidth = 150;

        var margin = {top: 10, right: 10, bottom: 80, left: gs_max*5.5},
            width = barplotwidth+10+(genes.length*15),
            height = 15*ngs;
        // $('#test').append("<p>"+category[i]+" width: "+width+"</p>")
        var svg = d3.select('#'+category[i]).append('svg')
                  .attr("width", width+margin.left+margin.right)
                  .attr("height", height+margin.top+margin.bottom)
                  .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

        var gradient1 = svg.append("defs").append("linearGradient")
                          .attr("id", 'gradient1')
                          .attr("x1", "0%")
                          .attr("y1", "0%")
                          .attr("x2", "100%")
                          .attr("y2", "100%")
                          .attr("spreadMethod", "pad");
        gradient1.append("stop").attr("offset", "0%")
                .attr("stop-color", "#6ef986")
                .attr("stop-ocupacity", 1);
        gradient1.append("stop").attr("offset", "100%")
                .attr("stop-color", "#004d01")
                .attr("stop-ocupacity", 1);
        var gradient = svg.append("defs").append("linearGradient")
                          .attr("id", 'gradient2')
                          .attr("x1", "0%")
                          .attr("y1", "0%")
                          .attr("x2", "100%")
                          .attr("y2", "100%")
                          .attr("spreadMethod", "pad");
        gradient.append("stop").attr("offset", "0%")
                .attr("stop-color", "#fbad4c")
                .attr("stop-ocupacity", 1);
        gradient.append("stop").attr("offset", "100%")
                .attr("stop-color", "#653800")
                .attr("stop-ocupacity", 1);

        // bar plot
        var xbar = d3.scale.linear().range([0, barplotwidth]);
        var xbarAxis = d3.svg.axis().scale(xbar).orient("bottom");
        xbar.domain([0, d3.max(tdata, function(d){return d.logFDR})]);
        var y = d3.scale.ordinal().rangeBands([0,height]);
        var yAxis = d3.svg.axis().scale(y).orient("left");
        y.domain(tdata.map(function(d){return d.GeneSet;}));
        svg.selectAll('rect').data(tdata).enter()
          .append("rect").attr("class", "bar")
          .attr("x", xbar(0))
          .attr("width", function(d){return xbar(d.logFDR)})
          .attr("y", function(d){return y(d.GeneSet)})
          .attr("height", 15)
          .style("fill", "url(#gradient1)");
        svg.append('g').attr("class", "y axis")
          .call(yAxis);
        svg.append('g').attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")")
          .call(xbarAxis).selectAll('text').attr('font-weight', 'normal')
          .style("text-anchor", "end").attr("transform", function (d) {return "rotate(-65)";})
          .attr("dx","-.75em").attr("dy", "-.15em");

        // gene plot
        var xgenes = d3.scale.ordinal().rangeBands([barplotwidth+10,width]);
        xgenes.domain(genesplot.map(function(d){return d.gene}));
        var xgenesAxis = d3.svg.axis().scale(xgenes).orient("bottom");
        svg.selectAll('rect.genes').data(genesplot).enter()
          .append("rect")
          .attr("x", function(d){return xgenes(d.gene)})
          .attr("y", function(d){return y(d.GeneSet)})
          .attr("width", 15)
          .attr("height", 15)
          .style("fill", "url(#gradient2)");
        svg.append('g').attr("class", "y axis")
          .attr("transform", "translate("+(barplotwidth+10)+",0)")
          .call(yAxis).selectAll("text").remove();
        svg.append('g').attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")")
          .call(xgenesAxis).selectAll('text').attr('font-weight', 'normal')
          .style("text-anchor", "end").attr("transform", function (d) {return "rotate(-65)";})
          .attr("dx","-.75em").attr("dy", "-.15em");

        svg.append("text").attr("text-anchor", "middle")
          .attr("transform", "translate("+(barplotwidth/2)+","+(height+40)+")")
          .text("-log10 FDR").attr("font-size", "12px");
        svg.append("text").attr("text-anchor", "middle")
          .attr("transform", "translate("+(barplotwidth+10+width)/2+","+(height+70)+")")
          .text("genes").attr("font-size", "12px");

        // Table
        var table = '<table class="table table-bordered"><thead><td>GeneSet</td><td>N</td><td>n</td><td>P-value</td><td>FDR</td><td>genes</td></thead>';
        tdata.forEach(function(d){
          table += '<tr><td>'+d.GeneSet+'</td><td>'+d.N_genes+'</td><td>'+d.N_overlap
                  +'</td><td>'+Number(Number(d.p).toPrecision(3)).toExponential(2)+'</td><td>'+Number(Number(d.FDR).toPrecision(3)).toExponential(2)+'</td><td>'+d.genes.split(":").join(", ")+'</td></tr>';
        });
        table += '</table>'
        $('#'+category[i]+"Table").html(table);
      }
    }
  });
}

// function fileDown(file, id){
//   $.ajax({
//     url: 'fileDown',
//     type: 'POST',
//     data: {
//       file: file,
//       id: id
//     },
//     success: function(){
//       window.location = "fileDown";
//     }
//   });
// }

function GeneTable(id){
  geneTable = $('#GeneTable').DataTable({
    "processing": true,
    serverSide: false,
    select: true,
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
