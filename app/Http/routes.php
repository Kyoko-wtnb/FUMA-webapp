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

Route::get('contact', function(){
  return view('pages.contact');
});

// Set up the auth routes
Route::auth();

// ********************** SNP2GENE ************************

Route::get('snp2gene', function(){
  $jobID = null;
  return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>null]);
})->middleware('auth');

Route::get('snp2gene/getJobList/{email?}/{limit?}', 'JobController@getJobList');

Route::post('snp2gene/jobcheck', 'JobController@JobCheck');

Route::post('snp2gene/newJob', 'JobController@newJob');

Route::post('snp2gene/queryJob', 'JobController@getJobID');

Route::post('snp2gene/checkJobStatus', 'JobController@checkJobStatus');

Route::post('snp2gene/getParams', 'JobController@getParams');

Route::post('snp2gene/CandidateSelection', 'JobController@CandidateSelection');

Route::post('snp2gene/DTfile', 'JsController@DTfile');

Route::post('snp2gene/jobInfo', 'JsController@jobInfo');

Route::get('snp2gene/manhattan/{type}/{jobID}/{file}', 'D3jsController@manhattan');

Route::get('snp2gene/QQplot/{type}/{jobID}/{plot}', 'D3jsController@QQplot');

Route::post('snp2gene/paramTable', 'JsController@paramTable');

Route::post('snp2gene/sumTable', 'JsController@sumTable');

Route::get('snp2gene/locusPlot/{ldI}/{type}/{jobID}', "D3jsController@locusPlot");

Route::get('snp2gene/d3text/{jobID}/{file}', 'D3jsController@d3js_textfile');

Route::post('snp2gene/annotPlot', 'JobController@annotPlot');

Route::post('snp2gene/filedown', 'JobController@filedown');

Route::post('snp2gene/geneSubmit', 'JobController@snp2geneGeneQuery');

Route::post('snp2gene/geneTable', 'JsController@geneTable');

Route::get('snp2gene/{jobID}', function($jobID){
  return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'jobquery']);
})->middleware('auth');


// ********************** GENE2FUNC ************************

Route::get('gene2func', function(){
  return view('pages.gene2func', ['status'=>'new', 'id'=>'none']);
});

Route::post('gene2func/submit', 'JobController@gene2funcSubmit');

Route::post('gene2func/geneQuery', 'JobController@geneQuery');

Route::post('snp2gene/geneQuery', 'JobController@geneQuery');

Route::post('gene2func/fileDown', 'JobController@gene2funcFileDown');
Route::post('snp2gene/fileDown', 'JobController@gene2funcFileDown');

Route::post('gene2func/geneTable', 'JsController@geneTable');

Route::get('gene2func/d3text/{jobID}/{file}', 'D3jsController@d3js_textfile');

// ********************** GWASRESULT ************************

Route::get('GWASresult', function(){
  return view('pages.GWASresult', ['/IPGAP']);
});

Route::get('GWASresult/d3text/{dbName}/{file}', 'D3jsController@d3js_GWAS_textfile');

Route::get('GWASresult/QQplot/{dbName}/{type}', 'D3jsController@d3js_GWAS_QQ');

Route::post('GWASresult/gwasDBtable', 'JobController@gwasDBtable');

Route::post('GWASresult/SelectOption', 'JobController@SelectOption');

Route::post('GWASresult/selectTable', 'JobController@selectTable');

Route::get('GWASresult/manhattan/{type}/{jobID}/{file}', 'D3jsController@manhattan');

Route::get('GWASresult/QQplot/{type}/{jobID}/{plot}', 'D3jsController@QQplot');
