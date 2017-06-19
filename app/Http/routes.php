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

// ********************** Browse ************************

Route::get('browse', function(){
	$gwasID = null;
	return view('pages.browse', ['gwasID' => $gwasID]);
});

Route::get('browse/getGwasList', 'BrowseController@getGwasList');

Route::post('browse/getParams', 'BrowseController@getParams');

Route::get('browse/manhattan/{type}/{id}/{file}', 'BrowseController@manhattan');

Route::get('browse/QQplot/{type}/{id}/{plot}', 'BrowseController@QQplot');

Route::get('browse/MAGMAtsplot/{type}/{prefix}/{id}', 'BrowseController@MAGMAtsplot');

Route::post('browse/DTfile', 'BrowseController@DTfile');

Route::post('browse/paramTable', 'BrowseController@paramTable');

Route::post('browse/sumTable', 'BrowseController@sumTable');

Route::post('browse/DTfileServerSide', 'BrowseController@DTfileServerSide');

Route::get('browse/d3text/{prefix}/{id}/{file}', 'BrowseController@d3js_textfile');

Route::post('browse/locusPlot', "BrowseController@locusPlot");

Route::post('browse/circos_chr', 'BrowseController@circos_chr');

Route::get('browse/circos_image/{prefix}/{id}/{file}', 'BrowseController@circos_image');

Route::post('browse/circosDown', 'BrowseController@circosDown');

Route::post('browse/filedown', 'BrowseController@filedown');

Route::post('browse/g2fFileDown', 'BrowseController@g2fFileDown');

Route::post('browse/imgdown', 'BrowseController@imgdown');

Route::post('browse/annotPlot', 'BrowseController@annotPlot');

Route::post('browse/annotPlot/getData', 'BrowseController@annotPlotGetData');

Route::post('browse/annotPlot/getGenes', 'BrowseController@annotPlotGetGenes');

Route::get('browse/legendText/{file}', 'BrowseController@legendText');

Route::get('browse/DEGPlot/{type}/{jobID}', 'BrowseController@DEGPlot');

Route::post('browse/geneTable', 'BrowseController@geneTable');

Route::get('browse/{gwasID}', function($gwasID){
	return view('pages.browse', ['gwasID' => $gwasID]);
});

// ********************** Middleware auth group************************
Route::group(['middleware'=>'auth'], function(){
	// ********************** SNP2GENE ************************
	Route::get('snp2gene', function(){
		$jobID = null;
		return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>null]);
	});

	Route::get('snp2gene/getJobList/{email?}/{limit?}', 'S2GController@getJobList');

	Route::post('snp2gene/newJob', 'S2GController@newJob');

	Route::get('snp2gene/checkJobStatus/{jobid}', 'S2GController@checkJobStatus');

	Route::post('snp2gene/getParams', 'S2GController@getParams');

	Route::post('snp2gene/Error5', 'S2GController@Error5');

	// Route::post('snp2gene/CandidateSelection', 'JobController@CandidateSelection');

	Route::post('snp2gene/DTfile', 'FumaController@DTfile');

	Route::post('snp2gene/DTfileServerSide', 'FumaController@DTfileServerSide');

	// Route::post('snp2gene/jobInfo', 'JsController@jobInfo');

	Route::get('snp2gene/manhattan/{prefix}/{id}/{file}', 'S2GController@manhattan');

	Route::get('snp2gene/QQplot/{prefix}/{id}/{plot}', 'S2GController@QQplot');

	Route::get('snp2gene/MAGMAtsplot/{type}/{prefix}/{id}', 'S2GController@MAGMAtsplot');

	Route::post('snp2gene/paramTable', 'S2GController@paramTable');

	Route::post('snp2gene/sumTable', 'S2GController@sumTable');

	Route::post('snp2gene/locusPlot', "S2GController@locusPlot");

	Route::get('snp2gene/d3text/{prefix}/{id}/{file}', 'FumaController@d3js_textfile');

	Route::get('snp2gene/legendText/{file}', 'S2GController@legendText');

	Route::post('snp2gene/annotPlot', 'S2GController@annotPlot');

	Route::post('snp2gene/annotPlot/getData', 'S2GController@annotPlotGetData');

	Route::post('snp2gene/annotPlot/getGenes', 'S2GController@annotPlotGetGenes');

	Route::post('snp2gene/filedown', 'S2GController@filedown');

	// Route::post('snp2gene/geneTable', 'JsController@geneTable');

	Route::post('snp2gene/circos_chr', 'S2GController@circos_chr');

	Route::get('snp2gene/circos_image/{prefix}/{id}/{file}', 'S2GController@circos_image');

	Route::post('snp2gene/circosDown', 'S2GController@circosDown');

	Route::post('snp2gene/deleteJob', 'S2GController@deleteJob');

	Route::post('snp2gene/imgdown', 'FumaController@imgdown');

	Route::get('snp2gene/{jobID}', function($jobID){
		return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'jobquery']);
	});

	// ********************** GENE2FUNC ************************
	Route::get('gene2func', function(){
		return view('pages.gene2func', ['status'=>'new', 'id'=>'none']);
	});

	Route::get('gene2func/getG2FJobList', 'G2FController@getJobList');

	Route::post('gene2func/submit', 'G2FController@gene2funcSubmit');

	Route::post('gene2func/geneQuery', 'G2FController@geneQuery');

	Route::post('gene2func/geneSubmit', 'G2FController@snp2geneGeneQuery');

	Route::post('gene2func/fileDown', 'G2FController@filedown');

	Route::post('gene2func/geneTable', 'G2FController@geneTable');

	Route::get('gene2func/d3text/{prefix}/{id}/{file}', 'FumaController@d3js_textfile');

	Route::get('gene2func/DEGPlot/{type}/{jobID}', 'G2FController@DEGPlot');

	Route::get('gene2func/ExpTsPlot/{type}/{jobID}', 'G2FController@ExpTsPlot');

	Route::get('gene2func/{jobID}', function($jobID){
		return view('pages.gene2func', ['status'=>'getJob', 'id'=>$jobID]);
	});

	Route::post('gene2func/deleteJob', 'G2FController@deleteJob');

	Route::post('gene2func/imgdown', 'FumaController@imgdown');

});
