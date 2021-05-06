<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::view('/', 'pages.home');

Route::get('appinfo', 'FumaController@appinfo');

Route::view('tutorial', 'pages.tutorial');

Route::view('links', 'pages.links');

Route::view('updates', 'pages.updates');

/**
 * Some documentation about the behind the scenes cleverness here
 * 
 * User, Roles and Permissions are handled by (RESTful) resource controllers
 * which contain index(), create(), edit(), update(), and destroy()
 * methods for teh resource in question.
 * 
 * The following routes are generated automatically (users example 
 * roles and permissions are the same), note including 
 * the "admin" prefix for clarity
 * 
 * HTTP                              RouteName
 * ----                              ---------
 * GET  /admin/users                 users.index
 * GET  /admin/users/create          users.create
 * POST /admin/users                 users.store
 * GET  /admin/{user}                users.show
 * GET  /admin/{user}/edit           users.edit
 * PUT  /admin/users/{user}          users.update
 * DELETE /admin/users/{user}        users.destroy
 * 
 * In the controllers and views(e.g. UserController, users/index.blad.php) 
 * you find these route referenced using, for example,: 
 * 			"redirect()->route('users.index')"
 * 			"a href="{{ route('roles.index') }}"
 */
Route::group(['middleware'=>'auth', 'prefix'=>'admin'], function() {
	Route::resource('users', 'UserController');

	Route::resource('roles', 'RoleController');
	
	Route::resource('permissions', 'PermissionController');
});

// Set up the auth routes
//Route::auth();

// ********************** Browse - non authenticated routes************************
Route::group(['prefix'=>'browse'], function(){

	Route::view('/', 'pages.browse', ['id'=>null, 'page'=>'browse', 'prefix'=>'public']);

	Route::get('getGwasList', 'BrowseController@getGwasList');

	Route::post('checkG2F', 'BrowseController@checkG2F');

	Route::post('getParams', 'BrowseController@getParams');

	Route::get('manhattan/{prefix}/{id}/{file}', 'FumaController@manhattan');

	Route::get('QQplot/{prefix}/{id}/{plot}', 'FumaController@QQplot');

	Route::get('MAGMA_expPlot/{prefix}/{id}', 'FumaController@MAGMA_expPlot');

	Route::post('DTfile', 'FumaController@DTfile');

	Route::post('paramTable', 'FumaController@paramTable');

	Route::post('sumTable', 'FumaController@sumTable');

	Route::post('DTfileServerSide', 'FumaController@DTfileServerSide');

	Route::get('d3text/{prefix}/{id}/{file}', 'FumaController@d3text');

	Route::get('g2f_d3text/{prefix}/{id}/{file}', 'FumaController@g2f_d3text');

	Route::post('locusPlot', "FumaController@locusPlot");

	Route::post('circos_chr', 'FumaController@circos_chr');

	Route::get('circos_image/{prefix}/{id}/{file}', 'FumaController@circos_image');

	Route::post('circosDown', 'FumaController@circosDown');

	Route::post('filedown', 'BrowseController@filedown');

	Route::post('imgdown', 'BrowseController@imgdown');

	Route::post('annotPlot', 'FumaController@annotPlot');

	Route::post('annotPlot/getData', 'FumaController@annotPlotGetData');

	Route::post('annotPlot/getGenes', 'FumaController@annotPlotGetGenes');

	Route::get('legendText/{file}', 'FumaController@legendText');

	Route::post('g2f_paramTable', 'FumaController@g2f_paramTable');

	Route::post('g2f_sumTable', 'FumaController@g2f_sumTable');

	Route::post('expDataOption', 'FumaController@expDataOption');

	Route::get('expPlot/{prefix}/{id}/{dataset}', 'FumaController@expPlot');

	Route::get('DEGPlot/{prefix}/{id}', 'FumaController@DEGPlot');

	Route::post('geneTable', 'FumaController@geneTable');

	Route::post('g2f_filedown', 'FumaController@g2f_filedown');

	Route::get('{id}', 'FumaController@browseId');

});

// ********************** Middleware auth group snp2gene************************
Route::group(['middleware'=>'auth', 'prefix'=>'snp2gene'], function(){
    // ********************** SNP2GENE ************************
	Route::view('/', 'pages.snp2gene', ['id'=>null, 'status'=>null, 'page'=>'snp2gene', 'prefix'=>'jobs']);

	Route::get('getJobList/{email?}/{limit?}', 'S2GController@getJobList');

	Route::get('getPublicIDs', 'S2GController@getPublicIDs');

	Route::post('newJob', 'S2GController@newJob');

	Route::post('getjobIDs', 'S2GController@getjobIDs');

	Route::post('getGeneMapIDs', 'S2GController@getGeneMapIDs');

	Route::post('geneMap', 'S2GController@geneMap');

	Route::post('loadParams', 'S2GController@loadParams');

	Route::get('checkJobStatus/{jobid}', 'S2GController@checkJobStatus');

	Route::post('getParams', 'S2GController@getParams');

	Route::post('Error5', 'S2GController@Error5');

	Route::post('DTfile', 'FumaController@DTfile');

	Route::post('DTfileServerSide', 'FumaController@DTfileServerSide');

	Route::get('manhattan/{prefix}/{id}/{file}', 'FumaController@manhattan');

	Route::get('QQplot/{prefix}/{id}/{plot}', 'FumaController@QQplot');

	Route::get('MAGMA_expPlot/{prefix}/{id}', 'FumaController@MAGMA_expPlot');

	Route::post('paramTable', 'FumaController@paramTable');

	Route::post('sumTable', 'FumaController@sumTable');

	Route::post('locusPlot', "FumaController@locusPlot");

	Route::get('d3text/{prefix}/{id}/{file}', 'FumaController@d3text');

	Route::get('legendText/{file}', 'FumaController@legendText');

	Route::post('annotPlot', 'FumaController@annotPlot');

	Route::post('annotPlot/getData', 'FumaController@annotPlotGetData');

	Route::post('annotPlot/getGenes', 'FumaController@annotPlotGetGenes');

	Route::post('filedown', 'S2GController@filedown');

	Route::post('circos_chr', 'FumaController@circos_chr');

	Route::get('circos_image/{prefix}/{id}/{file}', 'FumaController@circos_image');

	Route::post('circosDown', 'FumaController@circosDown');

	Route::post('deleteJob', 'S2GController@deleteJob');

	Route::post('imgdown', 'FumaController@imgdown');

	Route::get('{jobID}', 'S2GController@authcheck');

	Route::post('checkPublish', 'S2GController@checkPublish');

	Route::post('publish', 'S2GController@publish');
	Route::post('updatePublicRes', 'S2GController@updatePublicRes');
	Route::post('deletePublicRes', 'S2GController@deletePublicRes');
});

 // ********************** Middleware auth group gene2func************************
Route::group(['middleware'=>'auth', 'prefix'=>'gene2func'], function(){
	// ********************** GENE2FUNC ************************
	Route::view('/', 'pages.gene2func', ['status'=>'new', 'id'=>'none', 'page'=>'gene2func', 'prefix'=>'gene2func']);

	Route::get('getG2FJobList', 'G2FController@getJobList');

	Route::post('submit', 'G2FController@gene2funcSubmit');

	Route::post('geneQuery', 'G2FController@geneQuery');

	Route::post('geneSubmit', 'G2FController@snp2geneGeneQuery');

	Route::post('g2f_filedown', 'FumaController@g2f_filedown');

	Route::post('g2f_paramTable', 'FumaController@g2f_paramTable');

	Route::post('g2f_sumTable', 'FumaController@g2f_sumTable');

	Route::post('expDataOption', 'FumaController@expDataOption');

	Route::get('expPlot/{prefix}/{id}/{dataset}', 'FumaController@expPlot');

	Route::get('DEGPlot/{prefix}/{id}', 'FumaController@DEGPlot');

	Route::post('geneTable', 'FumaController@geneTable');

	Route::get('g2f_d3text/{prefix}/{id}/{file}', 'FumaController@g2f_d3text');

	Route::get('{jobID}', 'G2FController@authcheck');

	Route::post('deleteJob', 'G2FController@deleteJob');

	Route::post('imgdown', 'FumaController@imgdown');
});

// ********************** Middleware auth group celltype************************
Route::group(['middleware'=>'auth', 'prefix'=>'celltype'], function(){
	// ********************** Cell Type ************************
	Route::view('/', 'pages.celltype', ['id'=>null, 'status'=>null, 'page'=>'celltype', 'prefix'=>'celltype']);

	Route::post('getS2GIDs', 'CellController@getS2GIDs');

	Route::post('checkMagmaFile', 'CellController@checkMagmaFile');

	Route::get('getJobList', 'CellController@getJobList');

	Route::post('deleteJob', 'CellController@deleteJob');

	Route::post('submit', 'CellController@newJob');

	Route::get('{jobID}', 'CellController@authcheck');

	Route::get('checkJobStatus/{jobID}', 'CellController@checkJobStatus');

	Route::post('checkFileList', 'CellController@checkFileList');

	Route::post('getDataList', 'CellController@getDataList');

	Route::post('filedown', 'CellController@filedown');

	Route::post('getPerDatasetData', 'CellController@getPerDatasetData');

	Route::post('getStepPlotData', 'CellController@getStepPlotData');

	Route::post('imgdown', 'FumaController@imgdown');

});
