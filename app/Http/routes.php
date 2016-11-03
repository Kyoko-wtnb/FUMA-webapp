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

Route::get('snp2gene', function(){
  return view('pages.snp2gene');
});

Route::get('gene2func', function(){
  return view('pages.gene2func');
});

Route::get('links', function(){
  return view('pages.links');
});

Route::get('contact', function(){
  return view('pages.contact');
});

Route::post('jobcheck', 'JobCheck@index');

Route::post('snp2gene/newJob', 'JobController@newJob');

Route::post('snp2gene/queryJob', 'JobController@queryJob');

Route::post('snp2gene/CandidateSelection', 'JobController@CandidateSelection');

Route::post('snp2gene/DTfile', 'JsController@DTfile');

Route::post('snp2gene/paramTable', 'JsController@paramTable');

Route::post('snp2gene/sumTable', 'JsController@sumTable');

Route::get('snp2gene/locusPlot/{ldI}/{type}/{jobID}', "D3jsContoroller@locusPlot");

Route::get('snp2gene/d3text/{jobID}/{file}', 'D3jsContoroller@d3js_textfile');

Route::post('snp2gene/annotPlot', 'JobController@annotPlot');

Route::post('snp2gene/filedown', 'Jobcontroller@filedown');

Route::post('gene2func/geneQuery', 'JobController@geneQuery');

Route::get('gene2func/d3text/{jobID}/{file}', 'D3jsContoroller@d3js_textfile');
