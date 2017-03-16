<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
  return view('pages.home');
});

Route::get('tutorial', function(){
  return view('pages.tutorial');
});

Route::get('links', function(){
  return view('pages.links');
});

Route::get('updates', function(){
  return view('pages.updates');
});

// Set up the auth routes
Route::auth();

// ********************** SNP2GENE ************************

Route::get('snp2gene', function(){
  $jobID = null;
  return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>null]);
})->middleware('auth');

Route::get('snp2gene/getJobList/{email?}/{limit?}', 'JobController@getJobList');

Route::post('snp2gene/newJob', 'JobController@newJob');

Route::get('snp2gene/checkJobStatus/{jobid}', 'JobController@checkJobStatus');

Route::post('snp2gene/getParams', 'JobController@getParams');

Route::post('snp2gene/Error5', 'JobController@Error5');

Route::post('snp2gene/CandidateSelection', 'JobController@CandidateSelection');

Route::post('snp2gene/DTfile', 'JsController@DTfile');

Route::post('snp2gene/DTfileServerSide', 'JsController@DTfileServerSide');

Route::post('snp2gene/jobInfo', 'JsController@jobInfo');

Route::get('snp2gene/manhattan/{type}/{jobID}/{file}', 'D3jsController@manhattan');

Route::get('snp2gene/QQplot/{type}/{jobID}/{plot}', 'D3jsController@QQplot');

Route::get('snp2gene/MAGMAtsplot/{type}/{jobID}', 'D3jsController@MAGMAtsplot');

Route::post('snp2gene/paramTable', 'JsController@paramTable');

Route::post('snp2gene/sumTable', 'JsController@sumTable');

Route::post('snp2gene/locusPlot', "D3jsController@locusPlot");

Route::get('snp2gene/d3text/{jobID}/{file}', 'D3jsController@d3js_textfile');

Route::get('snp2gene/legendText/{file}', 'D3jsController@legendText');

Route::post('snp2gene/annotPlot', 'JobController@annotPlot');

Route::post('snp2gene/filedown', 'JobController@filedown');

Route::post('snp2gene/geneTable', 'JsController@geneTable');

Route::get('snp2gene/getPrioGenes/{jobID}', 'D3jsController@getPrioGenes');

Route::get('snp2gene/{jobID}', function($jobID){
  return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'jobquery']);
})->middleware('auth');

Route::post('snp2gene/deleteJob', 'JobController@deleteJob');

Route::post('snp2gene/imgdown', 'JobController@imgdown')->middleware('auth');

// ********************** GENE2FUNC ************************

Route::get('gene2func', function(){
  return view('pages.gene2func', ['status'=>'new', 'id'=>'none']);
})->middleware('auth');

Route::get('gene2func/getG2FJobList', 'JobController@getG2FJobList');

Route::post('gene2func/submit', 'JobController@gene2funcSubmit')->middleware('auth');

Route::post('gene2func/geneQuery', 'JobController@geneQuery');

Route::post('gene2func/geneSubmit', 'JobController@snp2geneGeneQuery')->middleware('auth');

Route::post('gene2func/fileDown', 'JobController@gene2funcFileDown');

Route::post('gene2func/geneTable', 'JsController@geneTable');

Route::get('gene2func/d3text/{jobID}/{file}', 'G2FController@d3js_textfile');

Route::get('gene2func/DEGPlot/{type}/{jobID}', 'G2FController@DEGPlot');

Route::get('gene2func/ExpTsPlot/{type}/{jobID}', 'G2FController@ExpTsPlot');

Route::get('gene2func/{jobID}', function($jobID){
  return view('pages.gene2func', ['status'=>'getJob', 'id'=>$jobID]);
})->middleware('auth');

Route::post('gene2func/deleteJob', 'JobController@G2FdeleteJob');

Route::post('gene2func/imgdown', 'JobController@imgdown')->middleware('auth');
