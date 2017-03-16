var geneTable;
$(document).ready(function(){
  // hide submit buttons for imgDown
  $('.ImgDownSubmit').hide();

  // hash activate
  var hashid = window.location.hash;
  if(hashid=="" && status=="getJob"){
    $('a[href="#expPanel"]').trigger('click');
  }else if(hashid==""){
    $('a[href="#newquery"]').trigger('click');
  }else{
    $('a[href="'+hashid+'"]').trigger('click');
  }

  // gene type clear
  $('#bkgeneSelectClear').on('click', function(){
    $("#genetype option").each(function(){
      $(this).prop('selected', false);
    });
    checkInput();
  });

  updateList();

  $('#deleteJob').on('click', function(){
    swal({
      title: "Are you sure?",
      text: "Do you really want to remove selected jobs?",
      type: "warning",
      showCancelButton: true,
      closeOnConfirm: true,
    }, function(isConfirm){
      if (isConfirm){
        $('.deleteJobCheck').each(function(){
          if($(this).is(":checked")){
            $.ajax({
              url: subdir+"/gene2func/deleteJob",
              type: "POST",
              data: {
                jobID: $(this).val()
              },
              error: function(){
                alert("error at deleteJob");
              },
              complete: function(){
                updateList();
              }
            });
          }
        });
      }
    });
  });

  if(status=="new"){
    checkInput();
    $('#resultSide').hide()
    // $('#results').hide();
  }else if(status=="getJob"){
    var id = jobID;
    checkInput();
    expHeatMap(id);
    tsEnrich(id);
    tsGeneralEnrich(id);
    ExpTsGeneralEnrich(id);
    ExpTsEnrich(id);
    GeneSet(id);
    GeneTable(id);
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
        // $('#resultSide').show()
        // $('#results').show();
        // $('#overlay').remove();
      },
      complete: function(){
        window.location.href=subdir+'/gene2func/'+id;
      }
    });
  }

});

// Plot donwload
function ImgDown(id, type){
  $('#'+id+'Data').val($('#'+id).html());
  $('#'+id+'Type').val(type);
  $('#'+id+'JobID').val(jobID);
  $('#'+id+'FileName').val(id);
  $('#'+id+'Dir').val("gene2func");
  $('#'+id+'Submit').trigger('click');
}

function GSImgDown(id, type){
  $('#GSData').val($('#'+id).html());
  $('#GSType').val(type);
  $('#GSJobID').val(jobID);
  $('#GSFileName').val(id);
  $('#GSDir').val("gene2func");
  $('#GSSubmit').trigger('click');
}

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

function updateList(){
  $.getJSON( subdir + "/gene2func/getG2FJobList", function( data ) {
      var items = '<tr><td colspan="7" style="text-align: center;">No Jobs Found</td></tr>';
      if(data.length){
          items = '';
          $.each( data, function( key, val ) {
              var status = '<a href="'+subdir+'/gene2func/'+val.jobID+'">load results</a>';
              items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title+"</td><td>"
                +val.snp2gene+"</td><td>"+val.snp2geneTitle+"</td><td>"
                +val.created_at+"</td><td>"+status+"</td>"
                +'<td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
                +val.jobID+'"/></td></tr>';
          });
      }

      // Put list in table
      $('#queryhistory table tbody')
          .empty()
          .append(items);
  });
}

function AjaxLoad(){
  var over = '<div id="overlay"><div id="loading">'
          +'<h4>Running gene test</h4>'
          +'<p>Please wait for a moment (20-30 sec)</br>'
          +'<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>'
          +'</div></div>';
  $(over).appendTo('body');
}

function GeneSetPlot(category){
  $('#'+category+'Plot').show();
  $('#'+category+'Table').hide();
}

function GeneSetTable(category){
  $('#'+category+'Plot').hide();
  $('#'+category+'Table').show();
}

Array.prototype.unique = function(a){
    return function(){ return this.filter(a) }
}(function(a,b,c){ return c.indexOf(a,b+1) < 0 });

function expHeatMap(id){
  d3.select('#expHeat').select("svg").remove();
  var itemSizeRow = 15, cellSize=itemSizeRow-1, itemSizeCol=10;
  queue().defer(d3.json, "d3text/"+id+"/exp.txt")
        .defer(d3.json, "d3text/"+id+"/exp.row.txt")
        .defer(d3.json, "d3text/"+id+"/exp.col.txt")
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
                            // .attr("dx", "-.3em");
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
                      .attr("y", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeCol;})
                      .attr("transform", function(d){
                        return "translate("+(tsclstlog2[tss.indexOf(d.ts)]*(itemSizeCol/2)-itemSizeCol/2)+","+(2*height+3)+")rotate(-90)";
                      });
                  }else if(gsort=="clst" && tssort=="alph"){
                    heatMap.transition().duration(2000)
                      .attr("fill", function(d){return col(d.log2)})
                      .attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
                      .attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
                    rowLabels.transition().duration(2000)
                      .attr("y", function(d){return gclstlog2[genes.indexOf(d.gene)]*itemSizeCol;});
                    colLabels.transition().duration(2000)
                      .attr("y", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeCol;})
                      .attr("transform", function(d){
                        return "translate("+(tsalph[tss.indexOf(d.ts)]*(itemSizeCol/2)-itemSizeCol/2)+","+(2*height+3)+")rotate(-90)";
                      });
                  }else if(gsort=="alph" && tssort=="clst"){
                    heatMap.transition().duration(2000)
                      .attr("fill", function(d){return col(d.log2)})
                      .attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
                      .attr("x", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
                    rowLabels.transition().duration(2000)
                      .attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
                    colLabels.transition().duration(2000)
                      .attr("y", function(d){return tsclstlog2[tss.indexOf(d.ts)]*itemSizeCol;})
                      .attr("transform", function(d){
                        return "translate("+(tsclstlog2[tss.indexOf(d.ts)]*(itemSizeCol/2)-itemSizeCol/2)+","+(2*height+3)+")rotate(-90)";
                      });
                  }else if(gsort=="alph" && tssort=="alph"){
                    heatMap.transition().duration(2000)
                      .attr("fill", function(d){return col(d.log2)})
                      .attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol-itemSizeCol})
                      .attr("x", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeRow-itemSizeRow});
                    rowLabels.transition().duration(2000)
                      .attr("y", function(d){return galph[genes.indexOf(d.gene)]*itemSizeCol;});
                    colLabels.transition().duration(2000)
                      .attr("y", function(d){return tsalph[tss.indexOf(d.ts)]*itemSizeCol;})
                      .attr("transform", function(d){
                        return "translate("+(tsalph[tss.indexOf(d.ts)]*(itemSizeCol/2)-itemSizeCol/2)+","+(2*height+3)+")rotate(-90)";
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

  d3.json(subdir+"/gene2func/DEGPlot/specific/"+id, function(data){
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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

  d3.json(subdir+"/gene2func/DEGPlot/general/"+id, function(data){
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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
          // if(d.FDR>0.05){return "url(#gradient2)";}
          // else{return "url(#gradient1)";}
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

function ExpTsGeneralEnrich(id){
  d3.select('#ExpTsGeneralEnrichBar').select('svg').remove();
  d3.json(subdir+'/gene2func/ExpTsPlot/general/'+jobID, function(data){
    if(data==null || data==undefined || data.lenght==0){
      $('#ExpTsGeneralEnrichBar').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
      +' Enrichment of tissue expressed gene is only available for FUMA v1.1.0 or later.</span><br/>'
      +'If your job has been submitted to older version, please contact Kyoko Watanabe (k.watanabe@vu.nl) or resubmit the job to obtain the MAGMA tisue expression results.</div>');
    }else{
      var margin = {top:30, right: 30, bottom:100, left:80},
          width = 600,
          height = 250;
      var svg = d3.select("#ExpTsGeneralEnrichBar").append("svg")
                  .attr("width", width+margin.left+margin.right)
                  .attr("height", height+margin.top+margin.bottom)
                  .append("g")
                  .attr("transform", "translate("+margin.left+","+margin.top+")");
      data.data.forEach(function(d){
        d[1] = +d[1]; //p
        d[2] = +d[2]; //adj.p
      });

      var cellsize = width/data.data.length;
      var Pbon = 0.05/data.data.length;

      var x = d3.scale.ordinal().rangeBands([0,width]);
      var xAxis = d3.svg.axis().scale(x).orient("bottom");
      x.domain(data.data.map(function(d){return d[0];}));
      var y = d3.scale.linear().range([height, 0]);
      var yAxis = d3.svg.axis().scale(y).orient("left");
      y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[1]);})]);

      var bar = svg.selectAll("rect.expgeneral").data(data.data).enter()
        .append("rect")
        .attr("x", function(d){return data.order.p[d[0]]*cellsize;})
        .attr("y", function(d){return y(-Math.log10(d[1]));})
        .attr("width", cellsize-1)
        .attr("height", function(d){return height - y(-Math.log10(d[1]));})
        .style("fill", function(d){
          if(d[2] < 0.05){return "#c00";}
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
        .attr("transform", "translate("+(-margin.left/2+5)+","+height/2+")rotate(-90)")
        .text("-log 10 P-value");
      svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
      svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
      svg.selectAll('text').style('font-family', 'sans-serif');
    }

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
    d3.select('#ExpTsGorder').on("change", function(){
      var type = $('#ExpTsGorder').val();
      sortOptions(type);
    });
  });
}

function ExpTsEnrich(id){
  d3.select('#ExpTsEnrichBar').select('svg').remove();
  d3.json(subdir+'/gene2func/ExpTsPlot/specific/'+jobID, function(data){
    if(data==null || data==undefined || data.lenght==0){
      $('#ExpTsEnrichBar').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
      +' Enrichment of tissue expressed gene is only available for FUMA v1.1.0 or later.</span><br/>'
      +'If your job has been submitted to older version, please contact Kyoko Watanabe (k.watanabe@vu.nl) or resubmit the job to obtain the MAGMA tisue expression results.</div>');
    }else{
      var margin = {top:30, right: 30, bottom:230, left:80},
          width = 800,
          height = 250;
      var svg = d3.select("#ExpTsEnrichBar").append("svg")
                  .attr("width", width+margin.left+margin.right)
                  .attr("height", height+margin.top+margin.bottom)
                  .append("g")
                  .attr("transform", "translate("+margin.left+","+margin.top+")");
      data.data.forEach(function(d){
        d[1] = +d[1]; //p
        d[2] = +d[2]; //adj.p
      });

      var cellsize = width/data.data.length;
      var Pbon = 0.05/data.data.length;

      var x = d3.scale.ordinal().rangeBands([0,width]);
      var xAxis = d3.svg.axis().scale(x).orient("bottom");
      x.domain(data.data.map(function(d){return d[0];}));
      var y = d3.scale.linear().range([height, 0]);
      var yAxis = d3.svg.axis().scale(y).orient("left");
      y.domain([0, d3.max(data.data, function(d){return -Math.log10(d[1]);})]);

      var bar = svg.selectAll("rect.expgeneral").data(data.data).enter()
        .append("rect")
        .attr("x", function(d){return data.order.p[d[0]]*cellsize;})
        .attr("y", function(d){return y(-Math.log10(d[1]));})
        .attr("width", cellsize-1)
        .attr("height", function(d){return height - y(-Math.log10(d[1]));})
        .style("fill", function(d){
          if(d[2] < 0.05){return "#c00";}
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
        .attr("transform", "translate("+(-margin.left/2+5)+","+height/2+")rotate(-90)")
        .text("-log 10 P-value");
      svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
      svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
      svg.selectAll('text').style('font-family', 'sans-serif');
    }

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
    d3.select('#ExpTsorder').on("change", function(){
      var type = $('#ExpTsorder').val();
      sortOptions(type);
    });
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
  d3.json("d3text/"+id+"/GS.txt", function(data){
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
          // $('#test').append("<p>"+category[i]+"<br/>gs_max: "+gs_max+'<br/>genes: '+genes.length+'</p>');
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

          var margin = {top: 40, right: 10, bottom: 80, left: Math.max(gs_max*6, 60)},
              width = barplotwidth*2+10+(Math.max(genes.length,6)*15),
              height = 15*ngs;
          // $('#test').append("<p>"+category[i]+" width: "+width+"</p>")
          var svg = d3.select('#'+category[i]).append('svg')
                    .attr("width", width+margin.left+margin.right)
                    .attr("height", height+margin.top+margin.bottom)
                    .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

          // var gradient1 = svg.append("defs").append("linearGradient")
          //                   .attr("id", 'gradient1')
          //                   .attr("x1", "0%")
          //                   .attr("y1", "0%")
          //                   .attr("x2", "100%")
          //                   .attr("y2", "100%")
          //                   .attr("spreadMethod", "pad");
          // gradient1.append("stop").attr("offset", "0%")
          //         .attr("stop-color", "#4d4dff")
          //         .attr("stop-ocupacity", 1);
          // gradient1.append("stop").attr("offset", "100%")
          //         .attr("stop-color", "#00003d")
          //         .attr("stop-ocupacity", 1);
          // var gradient3 = svg.append("defs").append("linearGradient")
          //                   .attr("id", 'gradient3')
          //                   .attr("x1", "0%")
          //                   .attr("y1", "0%")
          //                   .attr("x2", "100%")
          //                   .attr("y2", "100%")
          //                   .attr("spreadMethod", "pad");
          // gradient3.append("stop").attr("offset", "0%")
          //         .attr("stop-color", "#ff6666")
          //         .attr("stop-ocupacity", 1);
          // gradient3.append("stop").attr("offset", "100%")
          //         .attr("stop-color", "#4d0000")
          //         .attr("stop-ocupacity", 1);
          // var gradient = svg.append("defs").append("linearGradient")
          //                   .attr("id", 'gradient2')
          //                   .attr("x1", "0%")
          //                   .attr("y1", "0%")
          //                   .attr("x2", "100%")
          //                   .attr("y2", "100%")
          //                   .attr("spreadMethod", "pad");
          // gradient.append("stop").attr("offset", "0%")
          //         .attr("stop-color", "#ffa64d")
          //         .attr("stop-ocupacity", 1);
          // gradient.append("stop").attr("offset", "100%")
          //         .attr("stop-color", "#653800")
          //         .attr("stop-ocupacity", 1);

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
            }).selectAll('text').attr('font-weight', 'normal')
            .style("text-anchor", "end")
            .attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
            .style('font-size', '11px');
            // .attr("dx","-.75em").attr("dy", "-.15em");

          // bar plot (enrichment P-value)
          var xbar = d3.scale.linear().range([barplotwidth, barplotwidth*2]);
          var xbarAxis = d3.svg.axis().scale(xbar).orient("bottom");
          xbar.domain([0, d3.max(tdata, function(d){return -Math.log10(d.adjP)})]);
          // var y = d3.scale.ordinal().rangeBands([0,height]);
          // var yAxis = d3.svg.axis().scale(y).orient("left");
          // y.domain(tdata.map(function(d){return d.GeneSet;}));
          svg.selectAll('rect.p').data(tdata).enter()
            .append("rect").attr("class", "bar")
            .attr("x", xbar(0))
            .attr("width", function(d){return xbar(-Math.log10(d.adjP))-barplotwidth})
            .attr("y", function(d){return y(d.GeneSet)})
            .attr("height", 15)
            .style("fill", "#4d4dff")
            .style("stroke", "grey")
            .style("stroke-width", 0.3);
          svg.append('g').attr("class", "y axis")
            .call(yAxis).selectAll('text').style('font-size', '11px');
          svg.append('g').attr("class", "x axis")
            .attr("transform", "translate(0,"+height+")")
            .call(xbarAxis).selectAll('text').attr('font-weight', 'normal')
            .style("text-anchor", "end").attr("transform", function (d) {return "translate(-10,3)rotate(-65)";})
            .style('font-size', '11px');
            // .attr("dx","-.75em").attr("dy", "-.15em");

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
            // .attr("dx","-.75em").attr("dy", "-.15em");

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
          // svg.append("text").attr("text-anchor", "middle")
          //   .attr("transform", "translate("+(barplotwidth*2+10+width)/2+","+(height+70)+")")
          //   .text("genes").attr("font-size", "12px");
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
