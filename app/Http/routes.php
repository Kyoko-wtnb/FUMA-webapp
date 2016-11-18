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
  return view('pages.home'); #local
  #webserver return view('pages.home', ['subdir' => '/IPGAP']);
});

Route::get('tutorial', function(){
  return view('pages.tutorial'); #local
  #webserver return view('pages.tutorial', ['subdir' => '/IPGAP']);
});

Route::get('snp2gene', function(){
  return view('pages.snp2gene'); #local
  #webserver return view('pages.snp2gene', ['subdir' => '/IPGAP']);
});

Route::get('GWASresult', function(){
  return view('pages.GWASresult'); #local
  #webserver return view('pages.GWASresult', ['subdir' => '/IPGAP']);
});

Route::get('gene2func', function(){
  return view('pages.gene2func', ['status'=>'new']); #local
  #webserver return view('pages.gene2func', ['subdir' => '/IPGAP', 'status'=>'new']);
});

Route::get('links', function(){
  return view('pages.links'); #local
  #webserver return view('pages.links', ['subdir' => '/IPGAP']);
});

Route::get('contact', function(){
  return view('pages.contact'); #local
  #webserver return view('pages.contact', ['subdir' => '/IPGAP']);
});

Route::post('jobcheck', 'JobCheck@index'); #local
#webserver Route::post('snp2gene/jobcheck', 'JobCheck@index');

Route::post('snp2gene/newJob', 'JobController@newJob');

Route::post('snp2gene/queryJob', 'JobController@queryJob');

Route::post('snp2gene/CandidateSelection', 'JobController@CandidateSelection');

Route::post('snp2gene/DTfile', 'JsController@DTfile');

Route::post('snp2gene/jobInfo', 'JsController@jobInfo');

Route::post('snp2gene/paramTable', 'JsController@paramTable');

Route::post('snp2gene/sumTable', 'JsController@sumTable');

Route::get('snp2gene/locusPlot/{ldI}/{type}/{jobID}', "D3jsController@locusPlot");

Route::get('snp2gene/d3text/{jobID}/{file}', 'D3jsController@d3js_textfile');

Route::post('snp2gene/annotPlot', 'JobController@annotPlot');

Route::post('snp2gene/filedown', 'JobController@filedown');

Route::post('snp2gene/geneSubmit', 'JobController@snp2geneGeneQuery');

Route::post('gene2func/submit', 'JobController@gene2funcSubmit');

Route::post('gene2func/geneQuery', 'JobController@geneQuery');
Route::post('snp2gene/geneQuery', 'JobController@geneQuery');

Route::get('gene2func/d3text/{jobID}/{file}', 'D3jsController@d3js_textfile');

Route::get('GWASresult/d3text/{dbName}/{file}', 'D3jsController@d3js_GWAS_textfile');

Route::get('GWASresult/QQplot/{dbName}/{type}', 'D3jsController@d3js_GWAS_QQ');

Route::post('GWASresult/gwasDBtable', 'JobController@gwasDBtable');
