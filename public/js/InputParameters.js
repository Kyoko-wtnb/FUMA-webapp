var gwasFileSize = 0;
$(document).ready(function(){
  $("#newJob").show();
  $("#GWplotSide").hide();
  $("#Error5Side").hide();
  $("#PaintorSide").hide();
  $("#resultsSide").hide();
  $('#SubmitNewJob').attr('disabled',true);
  $('#go2job').attr('disabled',true);
  $('.posMapOptions').hide();
  $('#posMapOptFilt').hide();
  $('.eqtlMapOptions').hide();
  $('#eqtlMapOptFilt').hide();
  CheckAll();
  $('#fileCheck').html("<br/><div class='alert alert-danger'>GWAS summary statistics is a mandatory input.</div>");

  $('.multiSelect a').on('click',function(){
    var selection = $(this).siblings("select").attr("id");
    $("#"+selection+" option").each(function(){
      $(this).prop('selected', false);
    });
    CheckAll();
  });

  $("#GWASsummary").bind('change', function(){
    if($(this).val().length>0){
      gwasFileSize = this.files[0].size;
    }
    CheckAll();
  });

  // input parameters data toggle
  $('.panel-heading.input a').on('click', function(){
    if($(this).attr('class')=="active"){
      $(this).removeClass('active');
      $(this).children('i').attr('class', 'fa fa-chevron-down');
    }else{
      $(this).addClass('active');
      $(this).children('i').attr('class', 'fa fa-chevron-up');
    }
  });

  $('.level1').on('click', function(){
    var cur = $(this);
    var selected = $(this).is(":selected");

    while(cur.next().hasClass('level2')){
      cur = cur.next();
      cur.prop('selected', selected);
    }
  });

  $('.level2').on('click', function(){
    var cur = $(this);
    var selected = $(this).is(":selected");

    var total = true;
    while(cur.next().hasClass('level2')){
      cur = cur.next();
      total = (total && cur.is(':selected'));
    }
    cur = $(this);
    while(cur.prev().hasClass('level2')){
      cur = cur.prev();
      total = (total && cur.is(':selected'));
    }
    cur.prev().prop('selected', total);
  });

});

function CheckAll(){
  var submit = true;
  var table;
  var tablecheck = true;

  table = $('#NewJobFiles')[0];
  if($('#GWASsummary').val().length==0){
    if($('#egGWAS').is(':checked')){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
                      +'<i class="fa fa-check"></i> OK. An example file will be used.</div></td>');
      $('#N').val(21389);
      $('#chrcol').attr("disabled", true);
      $('#poscol').attr("disabled", true);
      $('#rsIDcol').attr("disabled", true);
      $('#pcol').attr("disabled", true);
      $('#altcol').attr("disabled", true);
      $('#refcol').attr("disabled", true);
      $('#orcol').attr("disabled", true);
      $('#becol').attr("disabled", true);
      $('#secol').attr("disabled", true);
      submit=true;
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
                      +'<i class="fa fa-ban"></i> Mandatory<br/>The maximum file size is 600Mb. Please gzip if your file is bigger than 600Mb.</div></td>');
      // $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      //                 +'<i class="fa fa-ban"></i> Please chose GWAS summary stats file first.</div></td>');
      $('#N').val('');
      $('#chrcol').attr("disabled", true);
      $('#poscol').attr("disabled", true);
      $('#rsIDcol').attr("disabled", true);
      $('#pcol').attr("disabled", true);
      $('#altcol').attr("disabled", true);
      $('#refcol').attr("disabled", true);
      $('#orcol').attr("disabled", true);
      $('#becol').attr("disabled", true);
      $('#secol').attr("disabled", true);
      submit=false;
      tablecheck=false;
    }
  }else{
    // var file = document.getElementById('GWASsummary');
    // console.log("File type:", file.type);
    $('#chrcol').attr("disabled", false);
    $('#poscol').attr("disabled", false);
    $('#rsIDcol').attr("disabled", false);
    $('#pcol').attr("disabled", false);
    $('#altcol').attr("disabled", false);
    $('#refcol').attr("disabled", false);
    $('#orcol').attr("disabled", false);
    $('#becol').attr("disabled", false);
    $('#secol').attr("disabled", false);
    if(gwasFileSize>=600000000){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> The file size if above 600Mb. Please gzip your file.</div></td>');
      // $(table.rows[1].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      //   +'<i class="fa fa-exclamation-triangle"></i> OK. Please make sure correct format is selected.</div></td>');
      submit=false;
      tablecheck=false;
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. Please check your input file format.</div></td>');
      // $(table.rows[1].cells[2]).html('<td><div class="alert alert-warning" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      //   +'<i class="fa fa-exclamation-triangle"></i> OK. Please make sure correct format is selected.</div></td>');
      submit=true;
    }
  }

  if($('#leadSNPs').val().length==0){
    $(table.rows[2].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional. <br/>This is only valid when predefined lead SNPs are provided.</div></td>');
    $('#addleadSNPs').attr("disabled", true);
  }else{
    $('#addleadSNPs').attr("disabled", false);
    $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
    if($('#addleadSNPs').is(":checked")==true){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    }
  }

  if($('#regions').val().length==0){
    $(table.rows[4].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }else{
    $(table.rows[4].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK.</div></td>');
  }

  if(tablecheck==false){
    $('#NewJobFilesPanel').parent().attr("class", "panel panel-danger");
  }else{
    $('#NewJobFilesPanel').parent().attr("class", "panel panel-default");
  }

  tablecheck=true;
  table=$('#NewJobParams')[0];
  if($('#N').val().length==0 && $('#Ncol').val().length==0){
    $('#N').attr("disabled", false);
    $('#Ncol').attr("disabled", false);
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Mandatory input. <br/>Please provide either total sample size of GWAS study or column name of N in input file.</div></td>');
    submit=false;
    tablecheck=false;
  }else if($('#N').val().length>0){
    $('#Ncol').attr("disabled", true);
    if($('#N').val()<50){
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input. Smple size must be greater than 50.</div></td>');
      submit=false;
      tablecheck=false;
    }else{
      $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. The total sample size will be applied to all SNPs.</div></td>');
    }
  }else{
    $('#N').attr("disabled", true);
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. The defined column will be used for sample size per SNP.</div></td>');
  }

  if($('#leadP').val().length==0){
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
  }else{
    if(isNaN($('#leadP').val())){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }else if($('#leadP').val()>=0 && $('#leadP').val()<=1){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }
  }

  if($('#r2').val().length==0){
    $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
    tablecheck=false;
  }else{
    if(isNaN($('#r2').val())){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }else if($('#r2').val()>=0 && $('#r2').val()<=1){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }
  }

  if($('#gwasP').val().length==0){
    $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
    tablecheck=false;
  }else{
    if(isNaN($('#gwasP').val())){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }else if($('#gwasP').val()>=0 && $('#gwasP').val()<=1){
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[3].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }
  }

  // Population is always OK [4]
  // KGSNPs is always OK [5]

  if($('#maf').val().length==0){
    $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
    tablecheck=false;
  }else{
    if(isNaN($('#maf').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }else if($('#maf').val()>=0 && $('#maf').val()<=1){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }
  }

  if($('#mergeDist').val().length==0){
    $(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-ban"></i> Invalid input</div></td>');
    submit=false;
    tablecheck=false;
  }else{
    if(isNaN($('#mergeDist').val())){
      $(table.rows[7].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input</div></td>');
      submit=false;
      tablecheck=false;
    }else{
      $(table.rows[7].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
  }

  if(tablecheck==false){
    $('#NewJobParamsPanel').parent().attr("class", "panel panel-danger");
  }else{
    $('#NewJobParamsPanel').parent().attr("class", "panel panel-default");
  }

  tablecheck=true;
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
    if($('#posMapWindow').val().length>0){
      $('#posMapAnnot').attr("disabled", true);
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. SNPs are mapped to genes up to '+$('#posMapWindow').val()+' kb</div></td>');
    }else{
      $('#posMapAnnot').attr("disabled", false);
      if(ms==0){
        $('#posMapWindow').attr("disabled", false);
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> Please either specify maximum distance or select functional consequences of SNPs to map to genes.</div></td>');
      }else{
        $('#posMapWindow').attr("disabled", true);
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK. SNPs with selected functional consequences on genes will be mapped.</div></td>');
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
      submit=false;
      tablecheck=false;
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
      tablecheck=false;
    }else{
      if(isNaN($('#posMapCADDth').val())){
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
        tablecheck=false;
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
      tablecheck=false;
    }else{
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
    if(isNaN($('#posMapChr15Max').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
      submit=false;
      tablecheck=false;
    }else{
      if($('#posMapChr15Max').val()>=1 && $('#posMapChr15Max').val()<=15){
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
        submit=false;
        tablecheck=false;
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

  if(tablecheck==false){
    $('#NewJobPosMapPanel').parent().attr("class", "panel panel-danger");
  }else{
    $('#NewJobPosMapPanel').parent().attr("class", "panel panel-default");
  }

  tablecheck=true;
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
    // console.log($('#eqtlMapTs').children('.level1').html())
    // $('.level1').each(function(){
    //   var cur = $(this);
    //   while(cur.next().hasClass('level2')){
    //     cur = cur.next();
    //     console.log(cur.val());
    //   }
    //
    // });
    if(ts>0){
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }else{
      $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Please select at least one tissue type.</div></td>');
      submit=false;
      tablecheck=false;
    }
    if($('#sigeqtlCheck').is(":checked")==true){
      $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK. Only significant snp-gene pairs will be used.</div></td>');
    }else{
      if($('#eqtlP').val().length==0){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Please either check only significant eQTLs or type P-value threshold.</div></td>');
        submit=false;
        tablecheck=false;
      }else if(isNaN($('#eqtlP').val())){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
        tablecheck=false;
      }else if($('#eqtlP').val()>=0 && $('#eqtlP').val()<=1){
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[2].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
        tablecheck=false;
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
      tablecheck=false;
    }else{
      if(isNaN($('#eqtlMapCADDth').val())){
        $(table.rows[1].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input.</div></td>');
        submit=false;
        tablecheck=false;
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
      tablecheck=false;
    }else{
      $(table.rows[5].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> OK.</div></td>');
    }
    if(isNaN($('#eqtlMapChr15Max').val())){
      $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
      submit=false;
      tablecheck=false;
    }else{
      if($('#eqtlMapChr15Max').val()>=1 && $('#eqtlMapChr15Max').val()<=15){
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-check"></i> OK.</div></td>');
      }else{
        $(table.rows[6].cells[2]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'
          +'<i class="fa fa-ban"></i> Invalid input. Please choose between 1 to 15.</div></td>');
        submit=false;
        tablecheck=false;
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

  if(tablecheck==false){
    $('#NewJobEqtlMapPanel').parent().attr("class", "panel panel-danger");
  }else{
    $('#NewJobEqtlMapPanel').parent().attr("class", "panel panel-default");
  }

  tablecheck=true;
  table = $('#NewJobMHC')[0];
  if($('#MHCregion').is(':checked')==true){
	$('#MHCopt').show();
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-check"></i> OK. Normal MHC region will be excluded '+$('#MHCopt option:selected').text()+'.</div></td>');
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
	$('#MHCopt').hide();
    $(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
    $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
      +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  table = $('#NewJobPaintor')[0];
  $(table.rows[1].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
	+'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  if($('#paintor').is(':checked')==true){
	  $('#paintorAnnot').show();
	  $('#paintorOpt').prop('disabled', false);
	  $(table.rows[0].cells[2]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-check"></i> PAINTOR will be performed for identified genomic risk loci.<br/>Optional annotations can be selected from the select box.<br/>Please refer the tutorial for the complete list of annotations.</div></td>');
  }else{
	  $('#paintorAnnot').hide();
	  $('#paintorOpt').prop('disabled', true);
	  $(table.rows[0].cells[2]).html('<td><div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">'
        +'<i class="fa fa-exclamation-circle"></i> Optional.</div></td>');
  }

  if(submit){$('#SubmitNewJob').attr("disabled", false)}
  else{$('#SubmitNewJob').attr("disabled", true)}
}
