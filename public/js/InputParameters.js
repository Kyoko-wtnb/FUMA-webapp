$(document).ready(function(){
  $('#SubmitNewJob').attr('disabled',true);
  $('#go2job').attr('disabled',true);
  $('#NewJobFiles').hide();
  $('#NewJobMHC').hide();
  $('#NewJobParams').hide();
  $('#addleadSNPsOpt').hide();
  $('#eqtlMapOptions').hide();
  $('#posMapAnnot').hide();
  $('#posMapChr15Opt').hide();
  $('#eqtlPOpt').hide();
  $('#eqtlMapChr15Opt').hide();
  $('#posMapAnnotClear').hide();
  $('#NewJobGene').hide();
  $('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");

  // clear annotation multi selection
  $('#posMapAnnotClear').on('click', function(){
    var tmp = document.getElementById('posMapAnnot');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    if($('#annotCheck').is(':checked')===true){
      $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one annotation to map.</div>");
    }
  });

  // clear eqtl tissue
  $('#eqtlMapTsClear').on('click', function(){
    var tmp = document.getElementById('eqtlMapTs');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var gts = [];
    var tmp = document.getElementById('eqtlMapGts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        gts.push(tmp.options[i].value);
      }
    }
    if($('#eqtlMap').is(':checked')===true && gts.length===0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
    }else{
      $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected general tissue types.</div>");
    }
  });

  // clear eqtl general tissue
  $('#eqtlMapGtsClear').on('click', function(){
    var tmp = document.getElementById('eqtlMapGts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var ts = [];
    var tmp = document.getElementById('eqtlMapTs');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        ts.push(tmp.options[i].value);
      }
    }
    if($('#eqtlMap').is(':checked')===true && ts.length===0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
    }else{
      $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected individual tissue types.</div>");
    }
  });

  // clear Chr15 tissue types
  $('#posMapChr15TsClear').on('click', function(){
    var tmp = document.getElementById('posMapChr15Ts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var gts = [];
    var tmp = document.getElementById('posMapChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        gts.push(tmp.options[i].value);
      }
    }
    if(gts.length===0){
      $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
    }else{
      $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
    }
  });
  $('#posMapChr15GtsClear').on('click', function(){
    var tmp = document.getElementById('posMapChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var ts = [];
    var tmp = document.getElementById('posMapChr15Ts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        ts.push(tmp.options[i].value);
      }
    }
    if(ts.length===0){
      $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
    }else{
      $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
    }
  });
  $('#eqtlMapChr15TsClear').on('click', function(){
    var tmp = document.getElementById('eqtlMapChr15Ts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var gts = [];
    var tmp = document.getElementById('eqtlMapChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        gts.push(tmp.options[i].value);
      }
    }
    if(gts.length===0){
      $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
    }else{
      $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
    }
  });
  $('#eqtlMapChr15GtsClear').on('click', function(){
    var tmp = document.getElementById('eqtlMapChr15Gts');
    for(var i=0; i<tmp.options.length; i++){
      tmp.options[i].selected=false;
    }
    var ts = [];
    var tmp = document.getElementById('eqtlMapChr15Ts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        ts.push(tmp.options[i].value);
      }
    }
    if(ts.length===0){
      $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
    }else{
      $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
    }
  });


});

function JobQueryCheck(){
  var Email = $('#JobQueryEmail').val();
  var jobtitle = $('#JobQueryTitle').val();
  if(Email.length>0 && jobtitle.length>0){
    $.ajax({
      // url: "/jobcheck",
      url: "/jobcheck", //local
      //webserver url: "/IPGAP/snp2gene/jobcheck",
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

function NewJobCheck(){
  var Email = $('#NewJobEmail').val();
  var jobtitle = $('#NewJobTitle').val();
  if(Email.length>0 && jobtitle.length>0){
    $.ajax({
      url: '/jobcheck', //local
      //webserver url: "/IPGAP/snp2gene/jobcheck",
      type: 'POST',
      data: {
        'Email': Email,
        'jobtitle': jobtitle
      },
      success: function(data){
        if(data==="1"){
          $("#NewJobChecked").html("<br/><div class='alert alert-success'>OK, we will create new job!!</div>");
          $('#NewJobFiles').show();
        }else if(data==="2"){
          $("#NewJobChecked").html("<br/><div class='alert alert-warning'>OK, this job already exists but we will overwrite.</div>");
          $('#NewJobFiles').show();
        }else{
          $("#NewJobChecked").html("<br/><div class='alert alert-danger'>e-mail is not correct. Please enter valid e-mail address.</div>");
          $('#NewJobFiles').hide();
          $('#NewJobMHC').hide();
          $('#NewJobParams').hide();
          $('#NewJobGene').hide();
        }
      }
    });
  }else{
    $("#NewJobChecked").html("<br/><div class='alert alert-info'>Please enter both E-mail and job title.</div>");
    $('#NewJobFiles').hide();
    $('#NewJobMHC').hide();
    $('#NewJobParams').hide();
    $('#NewJobGene').hide();
  }
}

function buttonEnable(){
  if($('#GWASsummary').val().length>0){
    $("#SubmitNewJob").attr('disabled', false);
    $('#NewJobMHC').show();
    $('#NewJobParams').show();
    $('#NewJobGene').show();
    $('#fileCheck').html("<br/><div class='alert alert-success'>OK. GWAS summary statistics is selected.<br/>You can also add predefined lead SNPs and/or genetic regions.</div>");
  }else{
    $("#SubmitNewJob").attr('disabled', true);
    $('#NewJobMHC').hide();
    $('#NewJobParams').hide();
    $('#NewJobGene').hide();
    $('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");
  }
  if($('#leadSNPs').val().length>0){
    $('#addleadSNPsOpt').show();
  }else{
    $('#addleadSNPsOpt').hide();
  }
}

function posMapOpt(i){
  if($('#posMap').is(':checked')===true){
    $('#posMapOptions').show();
    if(i==="windowMap"){
      $('#annotCheck').attr('checked', false);
      $('#posMapAnnot').hide();
      $('#posMapAnnotClear').hide();
    }else if(i==="annotMap"){
      $('#windowCheck').attr('checked', false);
      if($('#annotCheck').is(':checked')===true){$('#posMapAnnot').show();$('#posMapAnnotClear').show();}
      else{$('#posMapAnnot').hide();$('#posMapAnnotClear').hide();}
    }
    if($('#posMapChr15check').is(':checked')===true){
      $('#posMapChr15Opt').show();
      var ts = [];
      var tmp = document.getElementById('posMapChr15Ts');
      for(var i=0; i<tmp.options.length; i++){
        if(tmp.options[i].selected===true){
          ts.push(tmp.options[i].value);
        }
      }
      var gts = [];
      var tmp = document.getElementById('posMapChr15Gts');
      for(var i=0; i<tmp.options.length; i++){
        if(tmp.options[i].selected===true){
          gts.push(tmp.options[i].value);
        }
      }
      if(ts.length===0 && gts.length===0){
        $('#posMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
      }else if(ts.length>0 && gts.length>0){
        $('#posMapCheckChr15').html("<br/><div class='alert alert-warning'>OK. Both individual and general tisue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</div>");
      }else if(ts.length>0){
        $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
      }else if(gts.length>0){
        $('#posMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
      }
    }else{
      $('#posMapChr15Opt').hide();
      $('#posMapCheckChr15').html("");
    }
    // Check input parameters
    if($('#windowCheck').is(':checked')===false && $('#annotCheck').is(':checked')===false){
      $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please choose either window or annotation to perform positional mapping.</div>");
    }else if($('#windowCheck').is(':checked')===true){
      $('#posMapCheck').html("<br/><div class='alert alert-success'>Positional mapping will be performed with "+$("#posMapWindow").val()+"kb window.</div>");
    }else{
      var tmp = document.getElementById('posMapAnnot');
      var annot=[];
      for(var i=0; i<tmp.options.length; i++){
        if(tmp.options[i].selected===true){
          annot.push(tmp.options[i].value);
        }
      }
      if(annot.length>0){
        $('#posMapCheck').html("<br/><div class='alert alert-success'>OK. Positional mapping will be performed with selected annotations.</div>");
      }else{
        $('#posMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one annotation to map.</div>");
      }
    }
  }else{
    $('#posMapOptions').hide();
    $('#posMapCheck').html("");
  }
}

function eqtlMapOpt(){
  if($('#eqtlMap').is(':checked')===true){
    $('#eqtlMapOptions').show();
    var ts = [];
    var tmp = document.getElementById('eqtlMapTs');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        ts.push(tmp.options[i].value);
      }
    }
    var gts = [];
    var tmp = document.getElementById('eqtlMapGts');
    for(var i=0; i<tmp.options.length; i++){
      if(tmp.options[i].selected===true){
        gts.push(tmp.options[i].value);
      }
    }
    if(ts.length===0 && gts.length===0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-danger'>Please select at least one tissue for eQTL mapping.</div>");
    }else if(ts.length>0 && gts.length>0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-warning'>Both individual tissues and geberal tissues are selected<br/>All selected tissues will be used unless clear them.</div>");
    }else if(ts.length>0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected individual tissue types.</div>");
    }else if(gts.length>0){
      $('#eqtlMapCheck').html("<br/><div class='alert alert-success'>OK. eQTL mapping will be performed for selected general tissue types.</div>");
    }
    if($('#sigeqtlCheck').is(':checked')===true){
      $('#eqtlPOpt').hide();
    }else{
      $('#eqtlPOpt').show();
    }
    if($('#eqtlMapChr15check').is(':checked')===true){
      $('#eqtlMapChr15Opt').show();
      var ts = [];
      var tmp = document.getElementById('eqtlMapChr15Ts');
      for(var i=0; i<tmp.options.length; i++){
        if(tmp.options[i].selected===true){
          ts.push(tmp.options[i].value);
        }
      }
      var gts = [];
      var tmp = document.getElementById('eqtlMapChr15Gts');
      for(var i=0; i<tmp.options.length; i++){
        if(tmp.options[i].selected===true){
          gts.push(tmp.options[i].value);
        }
      }
      if(ts.length===0 && gts.length===0){
        $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-danger'>Please select at least one tissue/cell types to perform chromatin state filtering.</div>");
      }else if(ts.length>0 && gts.length>0){
        $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-warning'>OK. Both individual and general tisue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</div>");
      }else if(ts.length>0){
        $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
      }else if(gts.length>0){
        $('#eqtlMapCheckChr15').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
      }
    }else{
      $('#eqtlMapChr15Opt').hide();
      $('#eqtlMapCheckChr15').html("");
    }
  }else{
    $('#eqtlMapOptions').hide();
    $('#eqtlMapCheck').html("");
  }
}
