var leadSNPtable_selected=null;
var intervalTable_selected=null;
var SNPtable_selected=null;
var posAnnotPlot;
$(document).ready(function(){
  // var jobID = "{{$jobID}}";
  // console.log(status);
  console.log(jobid);
  // console.log(status.length);
  // $('#resultsSide').hide();
  if(status.length==0){
    // console.log("status:NULL");
  }else{
    // var job = IPGAPvar.jobtype;
    // var email = IPGAPvar.email;
    // var filedir = IPGAPvar.filedir;
    // var jobID = IPGAPvar.jobID;
    // var jobtitle = IPGAPvar.jobtitle;

    $('#annotPlotSubmit').attr("disabled", true);
    $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or interval to plot. If you haven\'t selected any row, please click one of the row of lead SNP or interval table.</div>');
    if($('#annotPlot_Chrom15').is(":checked")==false){
      $('#annotPlotChr15Opt').hide();
    }

    AjaxLoad();
    var jobStatus;
    var jobcheck = setInterval(function(){
      $.ajax({
        url: 'checkJobStatus',
        type: "POST",
        data: {
          jobID: jobid,
        },
        error: function(){
          alert("ERROR: checkJobStatus")
        },
        success: function(data){
          $('#test').html(data);
          jobStatus = data;
          // $('#results').show();
          // $('#resultsSide').show();
        },
        complete: function(){
          if(jobStatus!="RUNNING"){
            $('#overlay').remove();
            // $('#test').append(" timer is done");
            clearInterval(jobcheck);
            if(jobStatus=="OK"){
              loadResults();
            }else if(jobStatus=="NEW"){
              newJob();
            }else{
              errorHandling(jobStatus);
              $('#jobinfoSide').show();
              jobInfo(jobid);
            }
            return;
          }else{
            $('#overlay').html('<div id="overlay"><div id="loading">'
                  +'<p>Your job is runnning. Please wait for a moment.'
                  +'<br/>We will send you an email after the job is done '
                  +'(if you have provided your email address).'
                  +'<br/>If you didn\'t submit your email address, please bookmark this page.</p>'
                  +'<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>'
                  +'</div></div>');
          }
        }
      });
    }, 5000);

    function newJob(){
      var filedir;
      var posMap;
      var eqtlMap;
      $.ajax({
        url: 'CandidateSelection',
        type: 'POST',
        data: {
            jobID: jobid
        },
        processing: true,
        beforeSend: function(){
          // $('#logSNPfiltering').append('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
          JobRunLoad();
        },
        success: function(data){
          // $('#logSNPfiltering').html('<div class="alert alert-success"><h4> Step 1. Candidate SNPs filtering is done</h4></div>');
          // $('#test').html(data);
          // $('#overlay').remove();
        },
        error: function(){
          // alert("Error occored (SNPfilt)");
        },
        complete: function(){
          $('#overlay').remove();
          $('#logs').hide();

          $.ajax({
            url: 'checkJobStatus',
            type: "POST",
            data: {
              jobID: jobid,
            },
            error: function(){
              alert("ERROR: checkJobStatus")
            },
            success: function(data){
              $('#test').html(data);
              jobStatus = data;
            },
            complete: function(){
              if(jobStatus=="OK"){
                $.ajax({
                    url: 'getParams',
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
                    },
                    complete: function(){
                      jobInfo(jobid);
                      GWplot(jobid);
                      QQplot(jobid);
                      showResultTables(filedir, jobid, posMap, eqtlMap);
                      $('#results').show();
                      $('#jobinfoSide').show();
                      $('#resultsSide').show();
                      $('.sidePanel').each(function(){
                        if(this.id=="jobInfo"){
                          $('#'+this.id).show();
                        }else{
                          $('#'+this.id).hide();
                        }
                      });
                      $("#sidebar.sidebar-nav").find(".active").removeClass("active");
                      $('#sidebar.sidebar-nav li a').each(function(){
                        if($(this).attr("href")=="#jobInfo"){
                          $(this).parent().addClass("active");
                        }
                      });
                    }
                });
              }else{
                errorHandling(jobStatus);
                $('#jobinfoSide').show();
                jobInfo(jobid);
                $('a[href="#jobInfo"]').trigger('click');
              }
            }
          });
                    // jobInfo(jobid);
          // GWplot(jobid);
          // QQplot(jobid);
          // showResultTables(filedir, jobid, posMap, eqtlMap);
          // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
        }
      });
      // var filedir = IPGAPvar.filedir;
      // var leadfile = IPGAPvar.leadSNPsfileup;
      // var regionfile = IPGAPvar.regionsfileup;
      // var gwasformat = IPGAPvar.gwasformat;
      // var addleadSNPs = IPGAPvar.addleadSNPs;
      // var N = IPGAPvar.N;
      // var leadP = IPGAPvar.leadP;
      // var r2 = IPGAPvar.r2;
      // var gwasP = IPGAPvar.gwasP;
      // var pop = IPGAPvar.pop;
      // var KGSNPs = IPGAPvar.KGSNPs;
      // var maf = IPGAPvar.maf;
      // var mergeDist = IPGAPvar.mergeDist;
      // // var Xchr = IPGAPvar.Xchr;
      //
      // var exMHC = IPGAPvar.exMHC;
      // var extMHC = IPGAPvar.extMHC;
      //
      // var genetype = IPGAPvar.genetype;
      //
      // var posMap = IPGAPvar.posMap;
      // var posMapWindow = IPGAPvar.posMapWindow;
      // var posMapWindowSize = IPGAPvar.posMapWindowSize;
      // var posMapAnnot = IPGAPvar.posMapAnnot;
      // var posMapCADDth = IPGAPvar.posMapCADDth;
      // var posMapRDBth = IPGAPvar.posMapRDBth;
      // var posMapChr15 = IPGAPvar.posMapChr15;
      // var posMapChr15Max = IPGAPvar.posMapChr15Max;
      // var posMapChr15Meth = IPGAPvar.posMapChr15Meth;
      //
      // var eqtlMap = IPGAPvar.eqtlMap;
      // var eqtlMaptss = IPGAPvar.eqtlMaptss;
      // var eqtlMapSigeqtl = IPGAPvar.eqtlMapSigeqtl;
      // var eqtlMapeqtlP = IPGAPvar.eqtlMapeqtlP;
      // var eqtlMapCADDth = IPGAPvar.eqtlMapCADDth;
      // var eqtlMapRDBth = IPGAPvar.eqtlMapRDBth;
      // var eqtlMapChr15 = IPGAPvar.eqtlMapChr15;
      // var eqtlMapChr15Max = IPGAPvar.eqtlMapChr15Max;
      // var eqtlMapChr15Meth = IPGAPvar.eqtlMapChr15Meth;

      // $('#test').html("<h3>New Job</h3><p>test</p><p>email: "+email+"<br/>genetype: "+genetype+"</p>");
      // $('#results').show();
      // document.getElementById('test').innerHTML="<p>posMapChr15: "+posMapChr15+"</p>";

      // $('#results').show();
      // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');

      // $.ajax({
      //   url: 'CandidateSelection',
      //   type: 'POST',
      //   data: {
      //       jobID: jobID,
      //       email: email,
      //       jobtitle: jobtitle,
      //       filedir: filedir,
      //       gwasformat: gwasformat,
      //       leadfile: leadfile,
      //       addleadSNPs: addleadSNPs,
      //       regionfile: regionfile,
      //       N: N,
      //       leadP: leadP,
      //       r2: r2,
      //       gwasP: gwasP,
      //       pop: pop,
      //       KGSNPs: KGSNPs,
      //       maf: maf,
      //       mergeDist: mergeDist,
      //       // Xchr: Xchr,
      //       exMHC: exMHC,
      //       extMHC: extMHC,
      //       genetype: genetype,
      //       posMap: posMap,
      //       posMapWindow: posMapWindow,
      //       posMapWindowSize: posMapWindowSize,
      //       posMapAnnot: posMapAnnot,
      //       posMapCADDth: posMapCADDth,
      //       posMapRDBth: posMapRDBth,
      //       posMapChr15: posMapChr15,
      //       posMapChr15Max: posMapChr15Max,
      //       posMapChr15Meth: posMapChr15Meth,
      //       eqtlMap: eqtlMap,
      //       eqtlMaptss: eqtlMaptss,
      //       eqtlMapSigeqtl: eqtlMapSigeqtl,
      //       eqtlMapeqtlP: eqtlMapeqtlP,
      //       eqtlMapCADDth: eqtlMapCADDth,
      //       eqtlMapRDBth: eqtlMapRDBth,
      //       eqtlMapChr15: eqtlMapChr15,
      //       eqtlMapChr15Max: eqtlMapChr15Max,
      //       eqtlMapChr15Meth: eqtlMapChr15Meth
      //   },
      //   processing: true,
      //   // beforeSend: function(){
      //   //   // $('#logSNPfiltering').append('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
      //   //   JobRunLoad();
      //   // },
      //   success: function(data){
      //     // $('#logSNPfiltering').html('<div class="alert alert-success"><h4> Step 1. Candidate SNPs filtering is done</h4></div>');
      //     // $('#test').html(data);
      //     // $('#overlay').remove();
      //   },
      //   error: function(){
      //     // alert("Error occored (SNPfilt)");
      //   },
      //   complete: function(){
      //     $('#overlay').remove();
      //     $('#logs').hide();
      //     $('#results').show();
      //     $('#jobinfoSide').show();
      //     $('#resultsSide').show();
      //     $('.sidePanel').each(function(){
      //       if(this.id=="jobInfo"){
      //         $('#'+this.id).show();
      //       }else{
      //         $('#'+this.id).hide();
      //       }
      //     });
      //     $("#sidebar.sidebar-nav").find(".active").removeClass("active");
      //     $('#sidebar.sidebar-nav li a').each(function(){
      //       if($(this).attr("href")=="#jobInfo"){
      //         $(this).parent().addClass("active");
      //       }
      //     });
      //     jobInfo(jobID);
      //     GWplot(jobID);
      //     QQplot(jobID);
      //     showResultTables(filedir, jobID, posMap, eqtlMap);
      //     // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
      //   }
      // });
    }

    function loadResults(){
      var filedir;
      var posMap;
      var eqtlMap;
      // AjaxLoad();
      $('#jobinfoSide').show();
      $('.sidePanel').each(function(){
        if(this.id=="jobInfo"){
          $('#'+this.id).show();
        }else{
          $('#'+this.id).hide();
        }
      });
      $("#sidebar.sidebar-nav").find(".active").removeClass("active");
      $('#sidebar.sidebar-nav li a').each(function(){
        if($(this).attr("href")=="#jobInfo"){
          $(this).parent().addClass("active");
        }
      });
      $.ajax({
          url: 'getParams',
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
          },
          complete: function(){
            jobInfo(jobid);
            GWplot(jobid);
            QQplot(jobid);
            showResultTables(filedir, jobid, posMap, eqtlMap);
            $('#results').show();
            $('#resultsSide').show();
          }
      });
    }

    // if(status==="newjob"){
    //   $("#results").hide();
    //   // get parameters
    //   // var job = IPGAPvar.jobtype;
    //   var email = IPGAPvar.email;
    //   var filedir = IPGAPvar.filedir;
    //   var jobID = IPGAPvar.jobID;
    //   var jobtitle = IPGAPvar.jobtitle;
    //   var leadfile = IPGAPvar.leadSNPsfileup;
    //   var regionfile = IPGAPvar.regionsfileup;
    //   var gwasformat = IPGAPvar.gwasformat;
    //   var addleadSNPs = IPGAPvar.addleadSNPs;
    //   var N = IPGAPvar.N;
    //   var leadP = IPGAPvar.leadP;
    //   var r2 = IPGAPvar.r2;
    //   var gwasP = IPGAPvar.gwasP;
    //   var pop = IPGAPvar.pop;
    //   var KGSNPs = IPGAPvar.KGSNPs;
    //   var maf = IPGAPvar.maf;
    //   var mergeDist = IPGAPvar.mergeDist;
    //   // var Xchr = IPGAPvar.Xchr;
    //
    //   var exMHC = IPGAPvar.exMHC;
    //   var extMHC = IPGAPvar.extMHC;
    //
    //   var genetype = IPGAPvar.genetype;
    //
    //   var posMap = IPGAPvar.posMap;
    //   var posMapWindow = IPGAPvar.posMapWindow;
    //   var posMapWindowSize = IPGAPvar.posMapWindowSize;
    //   var posMapAnnot = IPGAPvar.posMapAnnot;
    //   var posMapCADDth = IPGAPvar.posMapCADDth;
    //   var posMapRDBth = IPGAPvar.posMapRDBth;
    //   var posMapChr15 = IPGAPvar.posMapChr15;
    //   var posMapChr15Max = IPGAPvar.posMapChr15Max;
    //   var posMapChr15Meth = IPGAPvar.posMapChr15Meth;
    //
    //   var eqtlMap = IPGAPvar.eqtlMap;
    //   var eqtlMaptss = IPGAPvar.eqtlMaptss;
    //   var eqtlMapSigeqtl = IPGAPvar.eqtlMapSigeqtl;
    //   var eqtlMapeqtlP = IPGAPvar.eqtlMapeqtlP;
    //   var eqtlMapCADDth = IPGAPvar.eqtlMapCADDth;
    //   var eqtlMapRDBth = IPGAPvar.eqtlMapRDBth;
    //   var eqtlMapChr15 = IPGAPvar.eqtlMapChr15;
    //   var eqtlMapChr15Max = IPGAPvar.eqtlMapChr15Max;
    //   var eqtlMapChr15Meth = IPGAPvar.eqtlMapChr15Meth;
    //
    //   // $('#test').html("<h3>New Job</h3><p>test</p><p>email: "+email+"<br/>genetype: "+genetype+"</p>");
    //   // $('#results').show();
    //   // document.getElementById('test').innerHTML="<p>posMapChr15: "+posMapChr15+"</p>";
    //
    //   // $('#results').show();
    //   // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
    //   $.ajax({
    //     url: 'CandidateSelection',
    //     type: 'POST',
    //     data: {
    //         jobID: jobID,
    //         email: email,
    //         jobtitle: jobtitle,
    //         filedir: filedir,
    //         gwasformat: gwasformat,
    //         leadfile: leadfile,
    //         addleadSNPs: addleadSNPs,
    //         regionfile: regionfile,
    //         N: N,
    //         leadP: leadP,
    //         r2: r2,
    //         gwasP: gwasP,
    //         pop: pop,
    //         KGSNPs: KGSNPs,
    //         maf: maf,
    //         mergeDist: mergeDist,
    //         // Xchr: Xchr,
    //         exMHC: exMHC,
    //         extMHC: extMHC,
    //         genetype: genetype,
    //         posMap: posMap,
    //         posMapWindow: posMapWindow,
    //         posMapWindowSize: posMapWindowSize,
    //         posMapAnnot: posMapAnnot,
    //         posMapCADDth: posMapCADDth,
    //         posMapRDBth: posMapRDBth,
    //         posMapChr15: posMapChr15,
    //         posMapChr15Max: posMapChr15Max,
    //         posMapChr15Meth: posMapChr15Meth,
    //         eqtlMap: eqtlMap,
    //         eqtlMaptss: eqtlMaptss,
    //         eqtlMapSigeqtl: eqtlMapSigeqtl,
    //         eqtlMapeqtlP: eqtlMapeqtlP,
    //         eqtlMapCADDth: eqtlMapCADDth,
    //         eqtlMapRDBth: eqtlMapRDBth,
    //         eqtlMapChr15: eqtlMapChr15,
    //         eqtlMapChr15Max: eqtlMapChr15Max,
    //         eqtlMapChr15Meth: eqtlMapChr15Meth
    //     },
    //     processing: true,
    //     beforeSend: function(){
    //       // $('#logSNPfiltering').append('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
    //       JobRunLoad();
    //     },
    //     success: function(data){
    //       // $('#logSNPfiltering').html('<div class="alert alert-success"><h4> Step 1. Candidate SNPs filtering is done</h4></div>');
    //       // $('#test').html(data);
    //       // $('#overlay').remove();
    //     },
    //     error: function(){
    //       // alert("Error occored (SNPfilt)");
    //     },
    //     complete: function(){
    //       $('#overlay').remove();
    //       $('#logs').hide();
    //       $('#results').show();
    //       $('#jobinfoSide').show();
    //       $('#resultsSide').show();
    //       $('.sidePanel').each(function(){
    //         if(this.id=="jobInfo"){
    //           $('#'+this.id).show();
    //         }else{
    //           $('#'+this.id).hide();
    //         }
    //       });
    //       $("#sidebar.sidebar-nav").find(".active").removeClass("active");
    //       $('#sidebar.sidebar-nav li a').each(function(){
    //         if($(this).attr("href")=="#jobInfo"){
    //           $(this).parent().addClass("active");
    //         }
    //       });
    //       jobInfo(jobID);
    //       GWplot(jobID);
    //       QQplot(jobID);
    //       showResultTables(filedir, jobID, posMap, eqtlMap);
    //       // $('#test').html('<h4>Your job is running<img src="'+public_path+'" align="middle"/></h4>');
    //     }
    //   });
    // }else{
    //   $('#logs').hide();
    //   // $('#results').show();
    //   // $('#resultsSide').show();
    //   // console.log(jobid);
    //   // console.log(status);
    //   // get parameters
    //   // var email = IPGAPvar.email;
    //   // var filedir = IPGAPvar.filedir;
    //   // var jobID = IPGAPvar.jobID;
    //   // var jobtitle = IPGAPvar.jobtitle;
    //   // var posMap = IPGAPvar.posMap;
    //   // var eqtlMap = IPGAPvar.eqtlMap;
    //   // $('#test').html("<p>posMap: "+posMap+" eqtlMap: "+eqtlMap+"</p>");
    //   var filedir;
    //   var posMap;
    //   var eqtlMap;
    //   var jobStatus;
    //   AjaxLoad();
    //   $('#jobinfoSide').show();
    //   $('.sidePanel').each(function(){
    //     if(this.id=="jobInfo"){
    //       $('#'+this.id).show();
    //     }else{
    //       $('#'+this.id).hide();
    //     }
    //   });
    //   $("#sidebar.sidebar-nav").find(".active").removeClass("active");
    //   $('#sidebar.sidebar-nav li a').each(function(){
    //     if($(this).attr("href")=="#jobInfo"){
    //       $(this).parent().addClass("active");
    //     }
    //   });
    //   var jobcheck = setInterval(function(){
    //     $.ajax({
    //       url: 'checkJobStatus',
    //       type: "POST",
    //       data: {
    //         jobID: jobid,
    //       },
    //       error: function(){
    //         alert("ERROR: checkJobStatus")
    //       },
    //       success: function(data){
    //         $('#test').html(data);
    //         jobStatus = data;
    //         // $('#results').show();
    //         // $('#resultsSide').show();
    //       },
    //       complete: function(){
    //         if(jobStatus!="RUNNING"){
    //           $('#overlay').remove();
    //           // $('#test').append(" timer is done");
    //           clearInterval(jobcheck);
    //           if(jobStatus=="OK"){
    //             loadResults();
    //           }else{
    //             // errorHandling(jobStatus);
    //             jobInfo(jobid);
    //           }
    //           return;
    //         }
    //       }
    //     });
    //   }, 5000);
    //
    //   function loadResults(){
    //     $.ajax({
    //         url: 'getParams',
    //         type: 'POST',
    //         data:{
    //           jobID: jobid
    //         },
    //         error: function(){
    //           alert("JobQuery getParams error");
    //         },
    //         success: function(data){
    //           // $('#test').html(data)
    //           var tmp = data.split(":");
    //           filedir = tmp[0];
    //           posMap = parseInt(tmp[1]);
    //           eqtlMap = parseInt(tmp[2]);
    //         },
    //         complete: function(){
    //           jobInfo(jobid);
    //           GWplot(jobid);
    //           QQplot(jobid);
    //           showResultTables(filedir, jobid, posMap, eqtlMap);
    //           $('#results').show();
    //           $('#resultsSide').show();
    //         }
    //     });
    //   }
    // }
    //
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

function JobRunLoad(){
  var over = '<div id="overlay"><div id="loading">'
        +'<p>Your job is runnning. Please wait for a moment.'
        +'<br/>We will send you an email after the job is done '
        +'(if you have provided your email address).'
        +'<br/>If you didn\'t submit your email address, please bookmark this page.</p>'
        +'<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>'
        +'</div></div>';
  $(over).appendTo('body');
}

function AjaxLoad(){
  var over = '<div id="overlay"><div id="loading">'
        +'<p>Loading data ...</p>'
        +'<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>'
        +'</div></div>';
  $(over).appendTo('body');
}

function errorHandling(status){
  if(status == "ERROR:001"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +'ERROR:001 (Not enough columns are provided in GWAS summary statistics file)<br/>'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status == "ERRUR:002"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +'ERROR:002 (Error from MAGMA)<br/>'
        +'This error might be because of the rsID and/or p-value columns are wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status == "ERRUR:003" || status=="ERROR:004"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error during SNPs filtering for manhattan plot)<br/>'
        +'This error might be because of the p-value column is wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status=="ERROR:005"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error from lead SNPs and candidate SNPs identification)<br/>'
        +'This error occures when no candidate SNPs were identified.'
        +'It might be becaseu there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS tagged SNPs.'
        +'In that case, you can relax threshold or provide predefined lead SNPs.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#snp2gene">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status=="ERROR:006"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error from lead SNPs and candidate SNPs identification)<br/>'
        +'This error might be because of either invalid input parameters or columns which are wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status=="ERRUR:007"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error during SNPs annotation extraction)<br/>'
        +'This error might be because of either invalid input parameters or columns which are wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status=="ERROR:008" || status=="ERRUR:009"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error during extracting ecternal data sources)<br/>'
        +'This error might be because of either invalid input parameters or columns which are wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }else if(status=="ERRUR:010"){
    $('#ErrorMess').html('<div class="alert alert-denger">'
        +status+' (Error during gene mapping)<br/>'
        +'This error might be because of either invalid input parameters or columns which are wrongly labeled.'
        +'Please make sure your input file have sufficient column names. You might just have chosen wrond file format.'
        +'Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>'
        +'</div>');
  }
}

function jobInfo(jobID){
  $.ajax({
    url: "jobInfo",
    type: "POST",
    data: {
      jobID: jobID,
    },
    error: function(){
      alert("jobInfo error");
    },
    success: function(data){
      $('#jobInfoTable').html(data);
    }
  });
}

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
  d3.select("#manhattanPane").style("height", height+margin.top+margin.bottom);
  d3.select("#geneManhattanPane").style("height", height+margin.top+margin.bottom);
  var svg = d3.select("#manhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvas1 = d3.select('#manhattan').append("div").attr("class", "canvasarea")
  	           .style("left", margin.left)
            	.style("top", margin.top)
            	.append("canvas")
            	.attr("class", "canvasarea")
            	.attr("width", width)
            	.attr("height", height)
            	.node().getContext('2d');
  var svg2 = d3.select("#genesManhattan").append("svg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append("g")
            .attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvas2 = d3.select('#genesManhattan').append("div").attr("class", "canvasarea")
  	           .style("left", margin.left)
            	.style("top", margin.top)
            	.append("canvas")
            	.attr("class", "canvasarea")
            	.attr("width", width)
            	.attr("height", height)
            	.node().getContext('2d');
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

    data.forEach(function(d){
    		// if(d.p<=0.005 || d.bp%200==0){
    			canvas1.beginPath();
    			// canvas1.arc( x(d.bp+chromStart[d.chr-1]), y(-Math.log10(d.p)), 2, 0, 2*Math.PI);
          canvas1.arc( x(d[1]+chromStart[d[0]-1]), y(-Math.log10(d[2])), 2, 0, 2*Math.PI);
    			// if(d.chr%2==0){canvas1.fillStyle="steelblue";}
          if(d[0]%2==0){canvas1.fillStyle="steelblue";}
    			else{canvas1.fillStyle="blue";}
    			canvas1.fill();
    		// }
    	});

    svg.append("line")
  	 .attr("x1", 0).attr("x2", width)
    	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
  	svg.append("g").attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
    svg.append("g").attr("class", "y axis").call(yAxis);

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

    data.forEach(function(d){
    		canvas2.beginPath();
    		// canvas2.arc( x((d.START+d.STOP)/2+chromStart[d.CHR-1]), y(-Math.log10(d.P)), 2, 0, 2*Math.PI);
        canvas2.arc( x((d[1]+d[2])/2+chromStart[d[0]-1]), y(-Math.log10(d[3])), 2, 0, 2*Math.PI);
    		// if(d.CHR%2==0){canvas2.fillStyle="steelblue";}
        if(d[0]%2==0){canvas2.fillStyle="steelblue";}
    		else{canvas2.fillStyle="blue";}
    		canvas2.fill();
    	});
    svg2.append("line")
  	 .attr("x1", 0).attr("x2", width)
    	.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
  	svg2.append("g").attr("class", "x axis")
      .attr("transform", "translate(0,"+height+")").call(xAxis).selectAll("text").remove();
    svg2.append("g").attr("class", "y axis").transition().duration(3000).call(yAxis);

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
  });
}

function QQplot(jobID){
  var margin = {top:30, right: 30, bottom:50, left:50},
      width = 300,
      height = 300;
  d3.select("#QQplotPane").style("height", height+margin.top+margin.bottom);
  // create svg and canvas objects
  var qqSNP = d3.select("#QQplot").append("svg")
              .attr("width", width+margin.left+margin.right)
              .attr("height", height+margin.top+margin.bottom)
              .append("g")
              .attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvasSNP = d3.select('#QQplot')
                  .append("div")
                  .attr("class", "canvasarea")
                	.style("left", margin.left)
                	.style("top", margin.top)
                	.append("canvas")
                	.attr("class", "canvasarea")
                	.attr("width", width+margin.right)
                	.attr("height", height+margin.bottom)
                	.node().getContext('2d');

  var qqGene = d3.select("#geneQQplot").append("svg")
                .attr("width", width+margin.left+margin.right)
                .attr("height", height+margin.top+margin.bottom)
                .append("g").attr("transform", "translate("+margin.left+","+margin.top+")");
  var canvasGene = d3.select('#geneQQplot').append("div")
                    .attr("class", "canvasarea")
                  	.style("left", margin.left)
                  	.style("top", margin.top)
                  	.append("canvas")
                  	.attr("class", "canvasarea")
                  	.attr("width", width+margin.right)
                  	.attr("height", height+margin.bottom)
                  	.node().getContext('2d');
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

    data.forEach(function(d){
  		// if(d.obs>1.5 | d.n%100==0){
  			canvasSNP.beginPath();
  			canvasSNP.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  			canvasSNP.fillStyle="grey";
  			canvasSNP.fill();
  		// }
  	});

  	qqSNP.append("g").attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")").call(xAxis);
    qqSNP.append("g").attr("class", "y axis").call(yAxis);
    qqSNP.append("line")
  	    .attr("x1", 0).attr("x2", x(maxP))
        .attr("y1", y(0)).attr("y2", y(maxP))
        .style("stroke", "red")
        .style("stroke-dasharray", ("3,3"));
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+width/2+","+(-15)+")")
      .text("GWAS summary statistics")
      .style("font-size", "20px");
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
      .text("Observed -log10 P-value");
    qqSNP.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width/2)+","+(height+35)+")")
      .text("Expected -log10 P-value");

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

    data.forEach(function(d){
  		canvasGene.beginPath();
  		canvasGene.arc(x(d.exp), y(d.obs), 2, 0, 2*Math.PI);
  		canvasGene.fillStyle="grey";
  		canvasGene.fill();
  	});

  	qqGene.append("g").attr("class", "x axis")
          .attr("transform", "translate(0,"+height+")").call(xAxis);
        	qqGene.append("g").attr("class", "y axis").call(yAxis);
  	qqGene.append("line")
    	.attr("x1", 0).attr("x2", x(maxP))
    	.attr("y1", y(0)).attr("y2", y(maxP))
    	.style("stroke", "red")
    	.style("stroke-dasharray", ("3,3"));
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+width/2+","+(-15)+")")
      .text("Gene-based statistics")
      .style("font-size", "20");
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(-35)+","+height/2+")rotate(-90)")
      .text("Observed -log10 P-value");
    qqGene.append("text").attr("text-anchor", "middle")
      .attr("transform", "translate("+(width/2)+","+(height+35)+")")
      .text("Expected -log10 P-value");
  });
}

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
      serverSide: false,
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
      serverSide: false,
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
        {"data": "Interval", name: "Interval"},
        {"data": "uniqID", name: "uniqID"},
        {"data": "rsID", name: "rsID"},
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
        {"data": "eqtlDirection", name:"eqtlDirection"},
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
        {"data": "eqtlDirection", name:"eqtlDirection"},
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
        {"data": "db", name:"DB"},
        {"data": "tissue", name:"tissue"},
        {"data": "gene", name:"Gene"},
        {"data": "symbol", name:"Symbol"},
        {"data": "p", name:"P-value"},
        {"data": "FDR", name:"FDR"},
        {"data": "tz", name:"t/z"}
      ],
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
          {"data": "leadSNP", name:"lead SNP"},
          {"data": "chr", name:"chr"},
          {"data": "bp", name:"bp"},
          {"data": "snp", name:"snp"},
          {"data": "PMID", name:"PMID"},
          {"data": "Trait", name:"Trait"},
          {"data": "FirstAuth", name:"FirstAuth"},
          {"data": "Date", name:"Date"},
          {"data": "P", name:"P-value"}
        ],
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10,
        dom: 'lBfrtip',
        buttons: ['csv']
  });
  // file = "ExAC.txt";
  // var eqtlTable = $('#exacTable').DataTable({
  //   processing: true,
  //   serverSide: false,
  //   select: true,
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
    var rowI = leadTable.row(this).index();
    leadSNPtable_selected=rowI;
    document.getElementById('annotPlotSelect_leadSNP').value=leadSNPtable_selected;

    if(document.getElementById('annotPlotSelect_leadSNP').checked===true){
      document.getElementById('annotPlotSubmit').disabled=false;
      annotSelect('leadSNP');
    }
    // if($('#annotPlotSelect_leadSNP').is(':checked')===true){
    //   $('#annotPlotSubmit').attr('disabled',false);
    //   document.getElementById('annotPlotSelect_leadSNP').value=leadSNPtable_selected;
    // }
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
    document.getElementById('annotPlotSelect_interval').value=intervalTable_selected;

    if(document.getElementById('annotPlotSelect_interval').checked===true){
      document.getElementById('annotPlotSubmit').disabled=false;
      annotSelect('interval');
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
            // .attr("id", "SnpAnnotPlotsvg")
            .attr("width", width+margin.left+margin.right)
            .attr("height", height+margin.top+margin.bottom)
            .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");
  // var svg = d3.select('#SnpAnnotPlotSVG')
  //           .attr("width", width+margin.left+margin.right)
  //           .attr("height", height+margin.top+margin.bottom)
  //           .append('g').attr("transform", "translate("+margin.left+","+margin.top+")");

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
        height = 15*y_element.length;
    var y = d3.scale.ordinal().domain(y_element).rangeBands([0, height], 0.1);
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
  if(i=="leadSNP"){
    if($('#annotPlotSelect_leadSNP').is(":checked")==true){
      $('#annotPlotSelect_interval').attr("checked", false);
      if(leadSNPtable_selected===null){
        $('#annotPlotSubmit').attr("disabled", true);
        $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">You haven\'t selected any lead SNP. Please click one of the row of lead SNP table.</div>');
        alert("Lead SNP is not selected.\nPlease select one from the table.")
      }else{
        Chr15Select();
      }
    }else{
      $('#annotPlotSubmit').attr("disabled", true);
      $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or interval to plot. If you haven\'t selected any row, please click one of the row of lead SNP or interval table.</div>');
    }
  }else if(i=="interval"){
    if($('#annotPlotSelect_interval').is(":checked")==true){
      $('#annotPlotSelect_leadSNP').attr("checked", false);
      if(intervalTable_selected===null){
        $('#annotPlotSubmit').attr("disabled", true);
        $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">You haven\'t selected any lead SNP. Please click one of the row of lead SNP table.</div>');
        alert("Lead SNP is not selected.\nPlease select one from the table.")
      }else{
        Chr15Select();
      }
    }else{
      $('#annotPlotSubmit').attr("disabled", true);
      $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or interval to plot. If you haven\'t selected any row, please click one of the row of lead SNP or interval table.</div>');
    }
  }else{
    if($('#annotPlotSelect_leadSNP').is(":checked")==false && $('#annotPlotSelect_interval').is(":checked")==false){
      $('#annotPlotSubmit').attr("disabled", true);
      $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">Please select either lead SNP or interval to plot. If you haven\'t selected any row, please click one of the row of lead SNP or interval table.</div>');
    }else{
      Chr15Select();
    }
  }
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
      $('#CheckAnnotPlotOpt').html('<div class="alert alert-danger">You have selected to plot 15-core chromatin state. Please select at least one tissue/cell type.</div>');
      $('#annotPlotSubmit').attr("disabled", true);
    }else if(ts.length>0 && gts.length>0){
      $('#CheckAnnotPlotOpt').html("<br/><div class='alert alert-warning'>OK. Both individual and general tisue/cell types are selected.<br/>All selected tissue/cell types will be used for filtering.</div>");
      $('#annotPlotSubmit').attr("disabled", false);
    }else if(ts.length>0){
      $('#CheckAnnotPlotOpt').html("<br/><div class='alert alert-success'>OK. Selected individual tissue/cell types will be used for chromatine state filtering.</div>");
      $('#annotPlotSubmit').attr("disabled", false);
    }else if(gts.length>0){
      $('#CheckAnnotPlotOpt').html("<br/><div class='alert alert-success'>OK. Selected general tissue/cell types will be used for chromatine state filtering.</div>");
      $('#annotPlotSubmit').attr("disabled", false);
    }
  }else{
    $('#annotPlotChr15Opt').hide();
    $('#annotPlotSubmit').attr("disabled", false);
    $('#CheckAnnotPlotOpt').html('<div class="alert alert-success">OK. Good to go. Click "Plot" to create regional plot with selected annotations.</div>');
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
