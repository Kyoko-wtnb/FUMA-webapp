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

Route::group(['middleware'=>'web'], function(){
	Route::get('/', function () {
		return view('pages.home');
	});

	Route::get('appinfo', 'FumaController@appinfo');

	Route::get('tutorial', function(){
		return view('pages.tutorial');
	});

	Route::get('links', function(){
		return view('pages.links');
	});

	Route::get('updates', function(){
		return view('pages.updates');
	});
});

// Set up the auth routes
Route::auth();

// ********************** Browse ************************
Route::group(['middleware'=>'web'], function(){
	Route::get('browse', function(){
		return view('pages.browse', ['id'=>null, 'page'=>'browse', 'prefix'=>'public']);
	});

	Route::get('browse/getGwasList', 'BrowseController@getGwasList');

	Route::post('browse/checkG2F', 'BrowseController@checkG2F');

	Route::post('browse/getParams', 'BrowseController@getParams');

	Route::get('browse/manhattan/{prefix}/{id}/{file}', 'FumaController@manhattan');

	Route::get('browse/QQplot/{prefix}/{id}/{plot}', 'FumaController@QQplot');

	Route::get('browse/MAGMA_expPlot/{prefix}/{id}', 'FumaController@MAGMA_expPlot');

	Route::post('browse/DTfile', 'FumaController@DTfile');

	Route::post('browse/paramTable', 'FumaController@paramTable');

	Route::post('browse/sumTable', 'FumaController@sumTable');

	Route::post('browse/DTfileServerSide', 'FumaController@DTfileServerSide');

	Route::get('browse/d3text/{prefix}/{id}/{file}', 'FumaController@d3text');

	Route::get('browse/g2f_d3text/{prefix}/{id}/{file}', 'FumaController@g2f_d3text');

	Route::post('browse/locusPlot', "FumaController@locusPlot");

	Route::post('browse/circos_chr', 'FumaController@circos_chr');

	Route::get('browse/circos_image/{prefix}/{id}/{file}', 'FumaController@circos_image');

	Route::post('browse/circosDown', 'FumaController@circosDown');

	Route::post('browse/filedown', 'BrowseController@filedown');

	Route::post('browse/imgdown', 'BrowseController@imgdown');

	Route::post('browse/annotPlot', 'FumaController@annotPlot');

	Route::post('browse/annotPlot/getData', 'FumaController@annotPlotGetData');

	Route::post('browse/annotPlot/getGenes', 'FumaController@annotPlotGetGenes');

	Route::get('browse/legendText/{file}', 'FumaController@legendText');

	Route::post('browse/g2f_paramTable', 'FumaController@g2f_paramTable');

	Route::post('browse/g2f_sumTable', 'FumaController@g2f_sumTable');

	Route::post('browse/expDataOption', 'FumaController@expDataOption');

	Route::get('browse/expPlot/{prefix}/{id}/{dataset}', 'FumaController@expPlot');

	Route::get('browse/DEGPlot/{prefix}/{id}', 'FumaController@DEGPlot');

	Route::post('browse/geneTable', 'FumaController@geneTable');

	Route::post('browse/g2f_filedown', 'FumaController@g2f_filedown');

	Route::get('browse/{id}', function($id){
		return view('pages.browse', ['id'=>$id, 'page'=>'browse', 'prefix'=>'public']);
	});
});

// ********************** Middleware auth group************************
Route::group(['middleware'=>['web','auth']], function(){
	// ********************** SNP2GENE ************************
	Route::get('snp2gene', function(){
		return view('pages.snp2gene', ['id'=>null, 'status'=>null, 'page'=>'snp2gene', 'prefix'=>'jobs']);
	});

	Route::get('snp2gene/getJobList/{email?}/{limit?}', 'S2GController@getJobList');

	Route::get('snp2gene/getPublicIDs', 'S2GController@getPublicIDs');

	Route::post('snp2gene/newJob', 'S2GController@newJob');

	Route::post('snp2gene/getjobIDs', 'S2GController@getjobIDs');

	Route::post('snp2gene/getGeneMapIDs', 'S2GController@getGeneMapIDs');

	Route::post('snp2gene/geneMap', 'S2GController@geneMap');

	Route::post('snp2gene/loadParams', 'S2GController@loadParams');

	Route::get('snp2gene/checkJobStatus/{jobid}', 'S2GController@checkJobStatus');

	Route::post('snp2gene/getParams', 'S2GController@getParams');

	Route::post('snp2gene/Error5', 'S2GController@Error5');

	Route::post('snp2gene/DTfile', 'FumaController@DTfile');

	Route::post('snp2gene/DTfileServerSide', 'FumaController@DTfileServerSide');

	Route::get('snp2gene/manhattan/{prefix}/{id}/{file}', 'FumaController@manhattan');

	Route::get('snp2gene/QQplot/{prefix}/{id}/{plot}', 'FumaController@QQplot');

	Route::get('snp2gene/MAGMA_expPlot/{prefix}/{id}', 'FumaController@MAGMA_expPlot');

	Route::post('snp2gene/paramTable', 'FumaController@paramTable');

	Route::post('snp2gene/sumTable', 'FumaController@sumTable');

	Route::post('snp2gene/locusPlot', "FumaController@locusPlot");

	Route::get('snp2gene/d3text/{prefix}/{id}/{file}', 'FumaController@d3text');

	Route::get('snp2gene/legendText/{file}', 'FumaController@legendText');

	Route::post('snp2gene/annotPlot', 'FumaController@annotPlot');

	Route::post('snp2gene/annotPlot/getData', 'FumaController@annotPlotGetData');

	Route::post('snp2gene/annotPlot/getGenes', 'FumaController@annotPlotGetGenes');

	Route::post('snp2gene/filedown', 'S2GController@filedown');

	Route::post('snp2gene/circos_chr', 'FumaController@circos_chr');

	Route::get('snp2gene/circos_image/{prefix}/{id}/{file}', 'FumaController@circos_image');

	Route::post('snp2gene/circosDown', 'FumaController@circosDown');

	Route::post('snp2gene/deleteJob', 'S2GController@deleteJob');

	Route::post('snp2gene/imgdown', 'FumaController@imgdown');

	Route::get('snp2gene/{jobID}', 'S2GController@authcheck');

	Route::post('snp2gene/checkPublish', 'S2GController@checkPublish');

	Route::post('snp2gene/publish', 'S2GController@publish');
	Route::post('snp2gene/updatePublicRes', 'S2GController@updatePublicRes');
	Route::post('snp2gene/deletePublicRes', 'S2GController@deletePublicRes');

	// ********************** GENE2FUNC ************************
	Route::get('gene2func', function(){
		return view('pages.gene2func', ['status'=>'new', 'id'=>'none', 'page'=>'gene2func', 'prefix'=>'gene2func']);
	});

	Route::get('gene2func/getG2FJobList', 'G2FController@getJobList');

	Route::post('gene2func/submit', 'G2FController@gene2funcSubmit');

	Route::post('gene2func/geneQuery', 'G2FController@geneQuery');

	Route::post('gene2func/geneSubmit', 'G2FController@snp2geneGeneQuery');

	Route::post('gene2func/g2f_filedown', 'FumaController@g2f_filedown');

	Route::post('gene2func/g2f_paramTable', 'FumaController@g2f_paramTable');

	Route::post('gene2func/g2f_sumTable', 'FumaController@g2f_sumTable');

	Route::post('gene2func/expDataOption', 'FumaController@expDataOption');

	Route::get('gene2func/expPlot/{prefix}/{id}/{dataset}', 'FumaController@expPlot');

	Route::get('gene2func/DEGPlot/{prefix}/{id}', 'FumaController@DEGPlot');

	Route::post('gene2func/geneTable', 'FumaController@geneTable');

	Route::get('gene2func/g2f_d3text/{prefix}/{id}/{file}', 'FumaController@g2f_d3text');

	Route::get('gene2func/{jobID}', 'G2FController@authcheck');

	Route::post('gene2func/deleteJob', 'G2FController@deleteJob');

	Route::post('gene2func/imgdown', 'FumaController@imgdown');

	// ********************** Cell Type ************************
	Route::get('celltype', function(){
		return view('pages.celltype', ['id'=>null, 'status'=>null, 'page'=>'celltype', 'prefix'=>'celltype']);
	});

	Route::post('celltype/getS2GIDs', 'CellController@getS2GIDs');

	Route::post('celltype/checkMagmaFile', 'CellController@checkMagmaFile');

	Route::get('celltype/getJobList', 'CellController@getJobList');

	Route::post('celltype/deleteJob', 'CellController@deleteJob');

	Route::post('celltype/submit', 'CellController@newJob');

	Route::get('celltype/{jobID}', 'CellController@authcheck');

	Route::get('celltype/checkJobStatus/{jobID}', 'CellController@checkJobStatus');

	Route::post('celltype/checkFileList', 'CellController@checkFileList');

	Route::post('celltype/getDataList', 'CellController@getDataList');

	Route::post('celltype/filedown', 'CellController@filedown');

	Route::post('celltype/getPerDatasetData', 'CellController@getPerDatasetData');

	Route::post('celltype/getStepPlotData', 'CellController@getStepPlotData');

	Route::post('celltype/imgdown', 'FumaController@imgdown');

});
