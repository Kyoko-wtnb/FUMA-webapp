$(document).ready(function(){
  $("#newJob").show();
  $("#jobinfoSide").hide();
  $("#resultsSide").hide();
  $('#SubmitNewJob').attr('disabled',true);
  $('#go2job').attr('disabled',true);
  $('.posMapOptions').hide();
  $('#posMapOptFilt').hide();
  $('.eqtlMapOptions').hide();
  $('#eqtlMapOptFilt').hide();
  CheckAll();
  // $('#NewJobFiles').hide();
  // $('#NewJobMHC').hide();
  // $('#NewJobParams').hide();
  // $('#addleadSNPsOpt').hide();
  // $('#eqtlMapOptions').hide();
  // $('#posMapAnnot').hide();
  // $('#posMapChr15Opt').hide();
  // $('#eqtlPOpt').hide();
  // $('#eqtlMapChr15Opt').hide();
  // $('#posMapAnnotClear').hide();
  // $('#NewJobGene').hide();
  $('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");

  $('.multiSelect a').on('click',function(){
    var selection = $(this).siblings("select").attr("id");
    console.log(selection);
    $("#"+selection+" option").each(function(){
      $(this).prop('selected', false);
    });
    CheckAll();
    // $("#"+selection).selectpicker('refresh');
  });

  // console.log($('#NewJobFiles')[0].rows[1].cells[0]);
  // clear annotation multi selection
  // $('#posMapAnnotClear').on('click', function(){
  //   var tmp = document.getElementById('posMapAnnot');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   if($('#annotCheck').is(':checked')===true){
  //     $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one annotation to map.</div>");
  //   }
  // });
  //
  // // clear eqtl tissue
  // $('#eqtlMapTsClear').on('click', function(){
  //   var tmp = document.getElementById('eqtlMapTs');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var gts = [];
  //   var tmp = document.getElementById('eqtlMapGts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       gts.push(tmp.options[i].value);
  //     }
  //   }
  //   if($('#eqtlMap').is(':checked')===true && gts.length===0){
  //     $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
  //   }else{
  //     $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected general tissue types.</div>");
  //   }
  // });
  //
  // // clear eqtl general tissue
  // $('#eqtlMapGtsClear').on('click', function(){
  //   var tmp = document.getElementById('eqtlMapGts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var ts = [];
  //   var tmp = document.getElementById('eqtlMapTs');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       ts.push(tmp.options[i].value);
  //     }
  //   }
  //   if($('#eqtlMap').is(':checked')===true && ts.length===0){
  //     $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
  //   }else{
  //     $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected individual tissue types.</div>");
  //   }
  // });
  //
  // // clear Chr15 tissue types
  // $('#posMapChr15TsClear').on('click', function(){
  //   var tmp = document.getElementById('posMapChr15Ts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var gts = [];
  //   var tmp = document.getElementById('posMapChr15Gts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       gts.push(tmp.options[i].value);
  //     }
  //   }
  //   if(gts.length===0){
  //     $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
  //   }else{
  //     $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
  //   }
  // });
  // $('#posMapChr15GtsClear').on('click', function(){
  //   var tmp = document.getElementById('posMapChr15Gts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var ts = [];
  //   var tmp = document.getElementById('posMapChr15Ts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       ts.push(tmp.options[i].value);
  //     }
  //   }
  //   if(ts.length===0){
  //     $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
  //   }else{
  //     $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
  //   }
  // });
  // $('#eqtlMapChr15TsClear').on('click', function(){
  //   var tmp = document.getElementById('eqtlMapChr15Ts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var gts = [];
  //   var tmp = document.getElementById('eqtlMapChr15Gts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       gts.push(tmp.options[i].value);
  //     }
  //   }
  //   if(gts.length===0){
  //     $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
  //   }else{
  //     $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
  //   }
  // });
  // $('#eqtlMapChr15GtsClear').on('click', function(){
  //   var tmp = document.getElementById('eqtlMapChr15Gts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     tmp.options[i].selected=false;
  //   }
  //   var ts = [];
  //   var tmp = document.getElementById('eqtlMapChr15Ts');
  //   for(var i=0; i<tmp.options.length; i++){
  //     if(tmp.options[i].selected===true){
  //       ts.push(tmp.options[i].value);
  //     }
  //   }
  //   if(ts.length===0){
  //     $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
  //   }else{
  //     $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
  //   }
  // });


});


function CheckAll(){
  var submit = true;
  var table;

  table = $('#NewJobFiles')[0];
  if($('#GWASsummary').val().length==0){
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
                    +'<i class="fa fa-ban"></i> Mandatory input</div></td>');
    // $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
    //                 +'<i class="fa fa-ban"></i> Please chose GWAS summary stats file first.</div></td>');
    submit=false;
  }else{
    // var file = document.getElementById('GWASsummary');
    // console.log("File type:", file.type);
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. Please check your input file format.</div></td>');
    // $(table.rows[1].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
    //   +'<i class="fa fa-exclamation-triangle"></i> OK. Please make sure correct format is selected.</div></td>');
    submit=true;
  }

  if($('#leadSNPs').val().length==0){
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional. <br/>This is only valid when predefined lead SNPs are provided.</div></td>');
  }else{
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    if($('#addleadSNPs').is(":checked")==true){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    }
  }

  if($('#regions').val().length==0){
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }else{
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }

  table=$('#NewJobParams')[0];
  if($('#N').val().length==0){
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Mandatory input</div></td>');
    submit=false;
  }else{
    if(isNaN($('#N').val())){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else if($('#N').val()<0){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK</div></td>');
    }
  }

  if($('#leadP').val().length==0){
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
  }else{
    if(isNaN($('#leadP').val())){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else if($('#leadP').val()>=0 && $('#leadP').val()<=1){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }
  }

  if($('#r2').val().length==0){
    $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
  }else{
    if(isNaN($('#r2').val())){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else if($('#r2').val()>=0 && $('#r2').val()<=1){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }
  }

  if($('#gwasP').val().length==0){
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
  }else{
    if(isNaN($('#gwasP').val())){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else if($('#gwasP').val()>=0 && $('#gwasP').val()<=1){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }
  }

  // Population is always OK [4]
  // KGSNPs is always OK [5]

  if($('#maf').val().length==0){
    $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
  }else{
    if(isNaN($('#maf').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else if($('#maf').val()>=0 && $('#maf').val()<=1){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }
  }

  if($('#mergeDist').val().length==0){
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
  }else{
    if(isNaN($('#mergeDist').val())){
      $(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
    }else{
      $(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
  }

  table = $('#NewJobPosMap')[0];
  var ms=0;
  $('#posMapAnnot option').each(function(){
    if($(this).is(":selected")){ms++;}
  });
  if($('#posMap').is(":checked")==true){
    $('.posMapOptions').show();
    $('#posMapOptFilt').show();
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    if($('#windowCheck').is(':checked')==true){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
      if($('#posMapWindow').val().length==0){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Mandatory since you checked distance based mapping.</div></td>');
        submit=false;
      }else{
        if(isNaN($('#posMapWindow').val())){
          $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
            +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
          submit=false;
        }else{
          $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
            +'<i class="fa fa-check"></i> OK.</div></td>');
        }
      }
    }else{
      if(ms==0){
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Please check distance based mapping or select annotations for positional mapping.</div></td>');
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
        submit=false;
      }else{
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-exclamation-circle"></i> Optional. This option is only valid when distance based mapping is checked.</div></td>');
      }
    }
  }else{
    $('.posMapOptions').hide();
    $('#posMapOptFilt').hide();
    if($('#eqtlMap').is(":checked")==true){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select either positional or eQTL mapping.</div></td>');
      submit-false;
    }
  }

  if(ms>0){
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. only selected annotations will be used for positional mapping.</div></td>');
  }else{
    if($('#windowCheck').is(':checked')==true){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional</div></td>');
    }else{
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select at least one annotation or check distance based mapping.</div></td>');
      submit=false;
    }
  }

  table = $('#posMapOptFiltTable')[0];
  if($('#posMapCADDcheck').is(":checked")==true){
    $(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    if($('#posMapCADDth').val().length==0){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
      submit=false;
    }else{
      if(isNaN($('#posMapCADDth').val())){
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
      }else{
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }
    }
  }else{
    $(table.rows[0].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  if($('#posMapRDBcheck').is(":checked")){
    $(table.rows[2].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }else{
    $(table.rows[2].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  if($('#posMapChr15check').is(":checked")){
    $(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    var ts = 0;
    $('#posMapChr15Ts option').each(function(){
      if($(this).is(":selected")){ts++;}
    });
    $('#posMapChr15Gts option').each(function(){
      if($(this).is(":selected")){ts++;}
    });
    if(ts==0){
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select at least one tissue/cell type.</div></td>');
      submit=false;
    }else{
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
    if(isNaN($('#posMapChr15Max').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
      submit=false;
    }else{
      if($('#posMapChr15Max').val()>=1 && $('#posMapChr15Max').val()<=15){
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
        submit=false;
      }
    }
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }else{
    $(table.rows[4].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  table = $('#NewJobEqtlMap')[0];
  if($('#eqtlMap').is(":checked")==true){
    $('.eqtlMapOptions').show();
    $('#eqtlMapOptFilt').show();
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    var ts = 0;
    $('#eqtlMapTs option').each(function(){
      if($(this).is(":checked")==true){ts++;}
    });
    $('#eqtlMapGts option').each(function(){
      if($(this).is(":checked")==true){ts++;}
    });
    if(ts>0){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select at least one tissue type.</div></td>');
      submit=false;
    }
    if($('#sigeqtlCheck').is(":checked")==true){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. Only significant snp-gene pairs will be used.</div></td>');
    }else{
      if($('#eqtlP').val().length==0){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Please either check only significant eQTLs or type P-value threshold.</div></td>');
        submit=false;
      }else if(isNaN($('#eqtlP').val())){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
      }else if($('#eqtlP').val()>=0 && $('#eqtlP').val()<=1){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
      }
    }
  }else{
    $('.eqtlMapOptions').hide();
    $('#eqtlMapOptFilt').hide();
    if($('#posMap').is(":checked")==true){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select either positional or eQTL mapping.</div></td>');
      submit-false;
    }
  }

  table = $('#eqtlMapOptFiltTable')[0];
  if($('#eqtlMapCADDcheck').is(":checked")==true){
    $(table.rows[0].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    if($('#eqtlMapCADDth').val().length==0){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Mandatory input.</div></td>');
      submit=false;
    }else{
      if(isNaN($('#eqtlMapCADDth').val())){
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
      }else{
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }
    }
  }else{
    $(table.rows[0].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  if($('#eqtlMapRDBcheck').is(":checked")){
    $(table.rows[2].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }else{
    $(table.rows[2].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  if($('#eqtlMapChr15check').is(":checked")){
    $(table.rows[4].cells[3]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    var ts = 0;
    $('#eqtlMapChr15Ts option').each(function(){
      if($(this).is(":selected")){ts++;}
    });
    $('#eqtlMapChr15Gts option').each(function(){
      if($(this).is(":selected")){ts++;}
    });
    if(ts==0){
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select at least one tissue/cell type.</div></td>');
      submit=false;
    }else{
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
    if(isNaN($('#eqtlMapChr15Max').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
      submit=false;
    }else{
      if($('#eqtlMapChr15Max').val()>=1 && $('#eqtlMapChr15Max').val()<=15){
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
        submit=false;
      }
    }
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }else{
    $(table.rows[4].cells[3]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[5].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[6].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  table = $('#NewJobMHC')[0];
  if($('#MHCregion').is(':checked')==true){
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. Normal MHC region will be excluded.</div></td>');
    if($('#extMHCregion').val().length==0){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    }else{
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. Entered region will be excluded.</div></td>');

    }
  }else{
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  table = $('#NewJobSubmit')[0];
  if($('#NewJobEmail').val().length==0){
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-triangle"></i> We recommend to submit your email address. <br/>Otherwise you have to bookmark link and job won\'t be saved.</div></td>');
  }else{
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. We will send you an email once job is done and results will be saved.</div></td>')
  }
  if($('#NewJobTitle').val().length==0){
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-triangle"></i> This is not mandatory but if you want to save a job, job title will be useful. <br/></div></td>');
  }else{
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }
  if(submit){$('#SubmitNewJob').attr("disabled", false)}
  else{$('#SubmitNewJob').attr("disabled", true)}
}


function JobQueryCheck(){
  var Email = $('#JobQueryEmail').val();
  var jobtitle = $('#JobQueryTitle').val();
  if(Email.length>0 && jobtitle.length>0){
    $.ajax({
      url: "/IPGAP/snp2gene/jobcheck",
      type: 'POST',
      data: {
        'Email': Email,
        'jobtitle': jobtitle
      },
      success: function(data){
        if(data==="2"){
          $("#go2job").attr('disabled', false);
          $("#JobQueryChecked").html("<br/><div class='alert alert-success'>OK. Press 'Go to Job' to query the job.</div>");
        }else{
          $("#go2job").attr('disabled', false);
          $("#JobQueryChecked").html("<br/><div class='alert alert-danger'>Sorry, the job does not exists. Please check email and job title again.</div>");
        }
      }
    });
  }else{
    $("#JobQueryChecked").html("<br/><div class='alert alert-info'>Please enter both E-mail and job title.</div>");
    $("#go2job").attr('disabled', true);
  }

}

// function NewJobCheck(){
//   var Email = $('#NewJobEmail').val();
//   var jobtitle = $('#NewJobTitle').val();
//   if(Email.length>0 && jobtitle.length>0){
//     $.ajax({
//       url: '/IPGAP/snp2gene/jobcheck',
//       type: 'POST',
//       data: {
//         'Email': Email,
//         'jobtitle': jobtitle
//       },
//       success: function(data){
//         if(data==="1"){
//           $("#NewJobChecked").html("<br/><div class='alert alert-success'>OK, we will create new job!!</div>");
//           $('#NewJobFiles').show();
//         }else if(data==="2"){
//           $("#NewJobChecked").html("<br/><div class='alert alert-warning'>OK, this job already exists but we will overwrite.</div>");
//           $('#NewJobFiles').show();
//         }else{
//           $("#NewJobChecked").html("<br/><div class='alert alert-danger'>e-mail is not correct. Please enter valid e-mail address.</div>");
//           $('#NewJobFiles').hide();
//           $('#NewJobMHC').hide();
//           $('#NewJobParams').hide();
//           $('#NewJobGene').hide();
//         }
//       }
//     });
//   }else{
//     $("#NewJobChecked").html("<br/><div class='alert alert-info'>Please enter both E-mail and job title.</div>");
//     $('#NewJobFiles').hide();
//     $('#NewJobMHC').hide();
//     $('#NewJobParams').hide();
//     $('#NewJobGene').hide();
//   }
// }
//
// function buttonEnable(){
//   if($('#GWASsummary').val().length>0){
//     $("#SubmitNewJob").attr('disabled', false);
//     $('#NewJobMHC').show();
//     $('#NewJobParams').show();
//     $('#NewJobGene').show();
//     $('#fileCheck').html("<br/><div class='alert alert-success'>OK. GWAS summary statistics is selected.<br/>You can also add predefined lead SNPs and/or genetic regions.</div>");
//   }else{
//     $("#SubmitNewJob").attr('disabled', true);
//     $('#NewJobMHC').hide();
//     $('#NewJobParams').hide();
//     $('#NewJobGene').hide();
//     $('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");
//   }
//   if($('#leadSNPs').val().length>0){
//     $('#addleadSNPsOpt').show();
//   }else{
//     $('#addleadSNPsOpt').hide();
//   }
// }
//
// function posMapOpt(i){
//   if($('#posMap').is(':checked')===true){
//     $('#posMapOptions').show();
//     if(i==="windowMap"){
//       $('#annotCheck').attr('checked', false);
//       $('#posMapAnnot').hide();
//       $('#posMapAnnotClear').hide();
//     }else if(i==="annotMap"){
//       $('#windowCheck').attr('checked', false);
//       if($('#annotCheck').is(':checked')===true){$('#posMapAnnot').show();$('#posMapAnnotClear').show();}
//       else{$('#posMapAnnot').hide();$('#posMapAnnotClear').hide();}
//     }
//     if($('#posMapChr15check').is(':checked')===true){
//       $('#posMapChr15Opt').show();
//       var ts = [];
//       var tmp = document.getElementById('posMapChr15Ts');
//       for(var i=0; i<tmp.options.length; i++){
//         if(tmp.options[i].selected===true){
//           ts.push(tmp.options[i].value);
//         }
//       }
//       var gts = [];
//       var tmp = document.getElementById('posMapChr15Gts');
//       for(var i=0; i<tmp.options.length; i++){
//         if(tmp.options[i].selected===true){
//           gts.push(tmp.options[i].value);
//         }
//       }
//       if(ts.length===0 && gts.length===0){
//         $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
//       }else if(ts.length>0 && gts.length>0){
//         $('#posMapCheckChr15').html("<br/><div class='alert alert-warning'>OK. Both individual and general tisue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</div>");
//       }else if(ts.length>0){
//         $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
//       }else if(gts.length>0){
//         $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
//       }
//     }else{
//       $('#posMapChr15Opt').hide();
//       $('#posMapCheckChr15').html("");
//     }
//     // Check input parameters
//     if($('#windowCheck').is(':checked')===false && $('#annotCheck').is(':checked')===false){
//       $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please choose either window or annotation to perform positional mapping.</div>");
//     }else if($('#windowCheck').is(':checked')===true){
//       $('#posMapCheck').html("<br/><div class='alert alert-success'>Positional mapping will be performed with "+$("#posMapWindow").val()+"kb window.</div>");
//     }else{
//       var tmp = document.getElementById('posMapAnnot');
//       var annot=[];
//       for(var i=0; i<tmp.options.length; i++){
//         if(tmp.options[i].selected===true){
//           annot.push(tmp.options[i].value);
//         }
//       }
//       if(annot.length>0){
//         $('#posMapCheck').html("<br/><div class='alert alert-success'>OK. Positional mapping will be performed with selected annotations.</div>");
//       }else{
//         $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one annotation to map.</div>");
//       }
//     }
//   }else{
//     $('#posMapOptions').hide();
//     $('#posMapCheck').html("");
//   }
// }

// function eqtlMapOpt(){
//   if($('#eqtlMap').is(':checked')===true){
//     $('#eqtlMapOptions').show();
//     var ts = [];
//     var tmp = document.getElementById('eqtlMapTs');
//     for(var i=0; i<tmp.options.length; i++){
//       if(tmp.options[i].selected===true){
//         ts.push(tmp.options[i].value);
//       }
//     }
//     var gts = [];
//     var tmp = document.getElementById('eqtlMapGts');
//     for(var i=0; i<tmp.options.length; i++){
//       if(tmp.options[i].selected===true){
//         gts.push(tmp.options[i].value);
//       }
//     }
//     if(ts.length===0 && gts.length===0){
//       $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
//     }else if(ts.length>0 && gts.length>0){
//       $('#eqtlMapCheck').html("<br/><div class='alert alert-warning'>Both individual tissues and geberal tissues are selected<br/>All selected tissues will be used unless clear them.</div>");
//     }else if(ts.length>0){
//       $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected individual tissue types.</div>");
//     }else if(gts.length>0){
//       $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected general tissue types.</div>");
//     }
//     if($('#sigeqtlCheck').is(':checked')===true){
//       $('#eqtlPOpt').hide();
//     }else{
//       $('#eqtlPOpt').show();
//     }
//     if($('#eqtlMapChr15check').is(':checked')===true){
//       $('#eqtlMapChr15Opt').show();
//       var ts = [];
//       var tmp = document.getElementById('eqtlMapChr15Ts');
//       for(var i=0; i<tmp.options.length; i++){
//         if(tmp.options[i].selected===true){
//           ts.push(tmp.options[i].value);
//         }
//       }
//       var gts = [];
//       var tmp = document.getElementById('eqtlMapChr15Gts');
//       for(var i=0; i<tmp.options.length; i++){
//         if(tmp.options[i].selected===true){
//           gts.push(tmp.options[i].value);
//         }
//       }
//       if(ts.length===0 && gts.length===0){
//         $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
//       }else if(ts.length>0 && gts.length>0){
//         $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-warning'>OK. Both individual and general tisue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</div>");
//       }else if(ts.length>0){
//         $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
//       }else if(gts.length>0){
//         $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
//       }
//     }else{
//       $('#eqtlMapChr15Opt').hide();
//       $('#eqtlMapCheckChr15').html("");
//     }
//   }else{
//     $('#eqtlMapOptions').hide();
//     $('#eqtlMapCheck').html("");
//   }
// }
