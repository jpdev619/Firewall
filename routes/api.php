<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController\PRController;
use App\Http\Controllers\WebController\ITSupportController;
use App\Http\Controllers\WebController\MyProfileController;
use App\Http\Controllers\WebController\DirectoryController;
use App\Http\Controllers\WebController\DailyReportController;
use App\Http\Controllers\ApiController\DRLController;
use App\Http\Controllers\ApiController\LRController;
use App\Http\Controllers\WebController\WFController;
use App\Http\Controllers\ApiController\CMFireWallController;
use App\Http\Controllers\ApiController\IncidentReportsController;
use App\Http\Controllers\ApiController\InventorySystemController;
use App\Http\Controllers\ApiController\InfoManagementController;
use App\Http\Controllers\IncidentReportsAdmin;
use App\Http\Controllers\MainController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//FIREWALL REQUEST
Route::get('fw/fw_uploads/{id}', [CMFireWallController::class,'fw_uploads'])->name('fw.uploadfiles');
Route::post('/remove_file_fw', [CMFireWallController::class,'fw_remove_files'])->name('fw.removefile');
Route::get('fw/datatables/{id}/{dept}/{status}/{myreq}/{assigned}/{open}', [CMFireWallController::class,'fw_datatable'])->name('fw.datatables');
Route::post('fw_request', [CMFireWallController::class,'fw_request'])->name('fwr');
Route::post('/submit_fw', [CMFireWallController::class,'submitFW'])->name('fw.submitFW');
Route::post('/comment_fw', [CMFireWallController::class,'adding_comment'])->name('fw.comment');
Route::post('/cancel_fw', [CMFireWallController::class,'cancelStatusFW'])->name('fw.cancel');
Route::post('/updatestatus_fw',[CMFireWallController::class,'StatusFW'])->name('fw.status');
Route::post('/disapprove_fw', [CMFireWallController::class,'disappproveFW'])->name('fw.disapprove');
Route::post('/revise_fw', [CMFireWallController::class,'reviseFW'])->name('fw.revise');
Route::post('/acknoledged_fw', [CMFireWallController::class,'acknowledgedFW'])->name('fw.acknowledged');
Route::post('/markasdone_fw', [CMFireWallController::class,'markasdoneFW'])->name('fw.markasdone');

