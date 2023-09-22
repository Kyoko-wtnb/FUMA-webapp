<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\FumaController;
use App\Http\Controllers\LoggingController;
use App\Http\Controllers\S2GController;
use App\Http\Controllers\G2FController;
use App\Http\Controllers\CellController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UpdateController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('pages.home');
});

Route::get('/home', function () {
    return view('pages.home');
});

Route::get('appinfo', [FumaController::class, 'appinfo']);

Route::get('/tutorial', function () {
    return view('pages.tutorial');
});
Route::post('tutorial/download_variants', [FumaController::class, 'download_variants']);

Route::get('/links', function () {
    return view('pages.links');
});

Route::get('/updates', [UpdateController::class, 'showUpdates']);

Route::get('/faq', function () {
    return view('pages.faq');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

    Route::prefix('admin')->group(function () {
        Route::get('/jobs', [AdminController::class, 'showJobs']);
        Route::get('/analysis', [AdminController::class, 'showAnalysis']);

        Route::resources([
            'updates' => UpdateController::class,
        ]);

        Route::get('/', [AdminController::class, 'index']);
    });

});

Route::prefix('browse')->group(function () {
    Route::get('/', [BrowseController::class, 'index']);
    Route::get('/getGwasList', [BrowseController::class, 'getGwasList']);
    Route::get('/{id}', [BrowseController::class, 'index']);
    Route::post('/checkG2F', [BrowseController::class, 'checkG2F']);
    Route::post('/getParams', [BrowseController::class, 'getParams']);
    Route::post('/getFilesContents', [S2GController::class, 'getFilesContents']);
    Route::post('/MAGMA_expPlot', [S2GController::class, 'MAGMA_expPlot']);
    Route::post('/circos_chr', [FumaController::class, 'circos_chr']);
    Route::post('/paramTable', [FumaController::class, 'paramTable']);
    Route::post('/DTfile', [FumaController::class, 'DTfile']);
    Route::post('/DTfileServerSide', [FumaController::class, 'DTfileServerSide']);
    Route::post('/locusPlot', [FumaController::class, 'locusPlot']);
    Route::post('/sumTable', [FumaController::class, 'sumTable']);
    Route::post('/g2f_sumTable', [FumaController::class, 'g2f_sumTable']);
    Route::post('/g2f_paramTable', [FumaController::class, 'paramTable']);
    Route::post('/expDataOption', [FumaController::class, 'expDataOption']);
    Route::post('/filedown', [FumaController::class, 'filedown']);
    Route::post('/imgdown', [FumaController::class, 'imgdown']);
    Route::post('/annotPlot/getData', [FumaController::class, 'annotPlotGetData']);
    Route::post('/annotPlot/getGenes', [FumaController::class, 'annotPlotGetGenes']);

    Route::get('/d3text/{prefix}/{id}/{file}', [FumaController::class, 'd3text']);
    Route::get('/g2f_d3text/{prefix}/{id}/{file}', [FumaController::class, 'g2f_d3text']);
    Route::get('/circos_image/{prefix}/{id}/{file}', [FumaController::class, 'circos_image']);
    Route::post('/circosDown', [FumaController::class, 'circosDown']);
    Route::post('/annotPlot', [FumaController::class, 'annotPlot']);

    Route::get('/legendText/{file}', [FumaController::class, 'legendText']);
    Route::get('/expPlot/{prefix}/{id}/{dataset}', [FumaController::class, 'expPlot']);
    Route::get('/DEGPlot/{prefix}/{id}', [FumaController::class, 'DEGPlot']);
    Route::post('/geneTable', [FumaController::class, 'geneTable']);
    Route::post('/g2f_filedown', [FumaController::class, 'g2f_filedown']);
});

// Group of protected pages by auth middleware
Route::group(['middleware' => ['auth']], function () {

    Route::post('{any}/logClientError', [LoggingController::class, 'logClientError']);

    // ********************** SNP2GENE ************************
    Route::prefix('snp2gene')->group(function () {
        Route::get('/', [S2GController::class, 'index']);
        Route::get('/getJobList/{email?}/{limit?}', [S2GController::class, 'getJobList']);
        Route::post('/newJob', [S2GController::class, 'newJob']);
        Route::post('/getjobIDs', [S2GController::class, 'getjobIDs']);
        Route::post('/getGeneMapIDs', [S2GController::class, 'getFinishedjobsIDs']);
        Route::post('/geneMap', [S2GController::class, 'geneMap']);
        Route::post('/loadParams', [S2GController::class, 'loadParams']);
        Route::get('/checkJobStatus/{jobid}', [S2GController::class, 'checkJobStatus']);
        Route::post('/getParams', [S2GController::class, 'getParams']);
        Route::post('/getFilesContents', [S2GController::class, 'getFilesContents']);
        Route::post('/MAGMA_expPlot', [S2GController::class, 'MAGMA_expPlot']);
        Route::post('/Error5', [S2GController::class, 'Error5']);
        Route::get('/{jobID}', [S2GController::class, 'authcheck']);
        Route::post('/checkPublish', [S2GController::class, 'checkPublish']);
        Route::post('/publish', [S2GController::class, 'publish']);
        Route::post('/deletePublicRes', [S2GController::class, 'deletePublicRes']);
        Route::post('/filedown', [S2GController::class, 'filedown']);
        Route::post('/deleteJob', [S2GController::class, 'deleteJob']);

        Route::post('/DTfile', [FumaController::class, 'DTfile']);
        Route::post('/DTfileServerSide', [FumaController::class, 'DTfileServerSide']);
        Route::post('/paramTable', [FumaController::class, 'paramTable']);
        Route::post('/sumTable', [FumaController::class, 'sumTable']);
        Route::post('/locusPlot', [FumaController::class, 'locusPlot']);
        Route::get('/d3text/{prefix}/{id}/{file}', [FumaController::class, 'd3text']);
        Route::post('/annotPlot/legendText', [FumaController::class, 'legendText']);
        Route::post('/annotPlot', [FumaController::class, 'annotPlot']);
        Route::post('/annotPlot/getData', [FumaController::class, 'annotPlotGetData']);
        Route::post('/annotPlot/getGenes', [FumaController::class, 'annotPlotGetGenes']);
        Route::post('/circos_chr', [FumaController::class, 'circos_chr']);
        Route::get('/circos_image/{prefix}/{id}/{file}', [FumaController::class, 'circos_image']);
        Route::post('/circosDown', [FumaController::class, 'circosDown']);
        Route::post('/imgdown', [FumaController::class, 'imgdown']);
    });

    // ********************** GENE2FUNC ************************
    Route::prefix('gene2func')->group(function () {
        Route::get('/', [G2FController::class, 'index']);
        Route::get('/getG2FJobList', [G2FController::class, 'getJobList']);
        Route::post('/submit', [G2FController::class, 'gene2funcSubmit']);
        Route::post('/geneQuery', [G2FController::class, 'geneQuery']);
        Route::post('/geneSubmit', [G2FController::class, 'snp2geneGeneQuery']);
        Route::get('/{jobID}', [G2FController::class, 'authcheck']);
        Route::post('/deleteJob', [G2FController::class, 'deleteJob']);

        Route::post('/g2f_filedown', [FumaController::class, 'g2f_filedown']);
        Route::post('/g2f_paramTable', [FumaController::class, 'paramTable']);
        Route::post('/g2f_sumTable', [FumaController::class, 'g2f_sumTable']);
        Route::post('/expDataOption', [FumaController::class, 'expDataOption']);
        Route::get('/expPlot/{prefix}/{id}/{dataset}', [FumaController::class, 'expPlot']);
        Route::get('/DEGPlot/{prefix}/{id}', [FumaController::class, 'DEGPlot']);
        Route::post('/geneTable', [FumaController::class, 'geneTable']);
        Route::get('/g2f_d3text/{prefix}/{id}/{file}', [FumaController::class, 'g2f_d3text']);
        Route::post('/imgdown', [FumaController::class, 'imgdown']);
    });

    // ********************** Cell Type ************************
    Route::prefix('celltype')->group(function () {
        Route::get('/', [CellController::class, 'index']);
        Route::post('/getS2GIDs', [CellController::class, 'getS2GIDs']);
        Route::post('/checkMagmaFile', [CellController::class, 'checkMagmaFile']);
        Route::get('/getJobList', [CellController::class, 'getJobList']);
        Route::post('/deleteJob', [CellController::class, 'deleteJob']);
        Route::post('/submit', [CellController::class, 'newJob']);
        Route::get('/{jobID}', [CellController::class, 'authcheck']);
        Route::get('/checkJobStatus/{jobID}', [CellController::class, 'checkJobStatus']);
        Route::post('/checkFileList', [CellController::class, 'checkFileList']);
        Route::post('/getDataList', [CellController::class, 'getDataList']);
        Route::post('/filedown', [CellController::class, 'filedown']);
        Route::post('/getPerDatasetData', [CellController::class, 'getPerDatasetData']);
        Route::post('/getStepPlotData', [CellController::class, 'getStepPlotData']);

        Route::post('/imgdown', [FumaController::class, 'imgdown']);
    });
});
