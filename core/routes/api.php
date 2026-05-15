<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\ApiAbreController;
use App\Http\Controllers\ApimenageController; 
use App\Http\Controllers\ApisectionController;
use App\Http\Controllers\ApilocaliteController; 
use App\Http\Controllers\ApiparcelleController; 
use App\Http\Controllers\ApiProgrammeController;
use App\Http\Controllers\ApievaluationController;
use App\Http\Controllers\ApilivraisonController; 
use App\Http\Controllers\ApiapplicationController;
use App\Http\Controllers\ApiestimationController; 
use App\Http\Controllers\ApiproducteurController; 
use App\Http\Controllers\ApissrteclrmsController; 
use App\Http\Controllers\ApiAgroEvaluationContoller;
use App\Http\Controllers\ApiFormationStaffController;
use App\Http\Controllers\ApigetlistedatasController; 
use App\Http\Controllers\ApisuiviparcelleController; 
use App\Http\Controllers\ApisuiviformationController; 

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
// DB::listen(function($sql){
//     Log::info($sql->sql);
//     Log::info($sql->bindings);
//     Log::info($sql->time);
// });
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::match(['POST'],'getupdateapp', [AuthController::class, 'getUpdateapp']);
Route::match(['POST'],'getdomain', [AuthController::class, 'getdomain']);
Route::match(['POST'],'getdelegues', [AuthController::class, 'getdelegues']);
Route::match(['POST'],'getapplicateurs', [AuthController::class, 'getapplicateurs']);
Route::match(['POST'],'connexion', [AuthController::class, 'connexion']);
Route::match(['POST'],'getroleuser', [AuthController::class, 'getRoleUser']);

Route::match(['POST'],'apiproducteur', [ApiproducteurController::class, 'store']);
Route::match(['POST'],'getproducteurs', [ApiproducteurController::class, 'getproducteurs']);
Route::match(['POST'],'apiinfosproducteur', [ApiproducteurController::class, 'apiinfosproducteur']);
Route::match(['POST'],'getproducteurupdate', [ApiproducteurController::class, 'getproducteurUpdate']);
Route::match(['POST'],'getstaff', [ApiproducteurController::class, 'getstaff']);

Route::match(['POST'],'apimenage', [ApimenageController::class, 'store']);
// gestion des livraisons
Route::match(['POST'],'getmagasinsection', [ApilivraisonController::class, 'getMagasinsection']);
Route::match(['POST'],'getmagasincentraux', [ApilivraisonController::class, 'getMagasincentraux']);
Route::match(['POST'],'apilivraisonmagasinsection', [ApilivraisonController::class,'store']);
Route::match(['POST'],'apilivraisonmagasincentral', [ApilivraisonController::class,'store_livraison_magasincentral']);
Route::match(['POST'],'livraisonbroussebymagasinsection', [ApilivraisonController::class, 'livraisonbroussebymagasinsection']);
Route::match(['POST'],'gettransporteurs', [ApilivraisonController::class, 'gettransporteurs']);
Route::match(['POST'],'getvehicules', [ApilivraisonController::class, 'getvehicules']);
Route::match(['POST'],'getremorques', [ApilivraisonController::class, 'getremorques']);
//fin gestion des livraisons
Route::match(['POST'],'apiparcelle', [ApiparcelleController::class, 'store']);
Route::match(['POST'],'getparcelles', [ApiparcelleController::class, 'index']);
Route::match(['POST'],'getparcelleupdate', [ApiparcelleController::class, 'getparcelleUpdate']);

Route::match(['POST'],'apisuiviparcelle', [ApisuiviparcelleController::class, 'store']);
Route::match(['POST'],'apisuiviformation', [ApisuiviformationController::class, 'store']); 
Route::match(['POST'],'apivisiteur', [ApisuiviformationController::class, 'storeVisiteur']);
Route::match(['POST'],'getvisiteurs', [ApisuiviformationController::class, 'getvisiteurs']);
Route::match(['POST'],'apitypethemeformation', [ApisuiviformationController::class, 'getTypethemeformation']); 
Route::match(['POST'],'gettypeformation', [ApisuiviformationController::class, 'getTypeformation']); 
Route::match(['POST'],'getthemes', [ApisuiviformationController::class, 'getThemes']); 
Route::match(['POST'],'getformationsbyuser', [ApisuiviformationController::class, 'getformationsByUser']);
Route::match(['POST'],'getlocalite', [ApilocaliteController::class, 'index']); 
Route::match(['POST'],'apilocalite', [ApilocaliteController::class, 'store']); 
Route::match(['POST'],'getlistedatas', [ApigetlistedatasController::class, 'index']);
Route::match(['POST'],'apiestimation', [ApiestimationController::class, 'store']); 
Route::match(['POST'],'apissrteclrms', [ApissrteclrmsController::class, 'store']); 
Route::match(['POST'],'apiniveauxclasse', [ApissrteclrmsController::class, 'getNiveauxclasse']); 
Route::match(['POST'],'apiapplication', [ApiapplicationController::class, 'store']); 
Route::match(['POST'],'apievaluation', [ApievaluationController::class, 'store']); 
Route::match(['POST'],'getinspections', [ApievaluationController::class, 'getInspectionsNonApplicableEtNonConforme']); 
Route::match(['POST'],'updateinspection', [ApievaluationController::class, 'updateInspection']);
Route::match(['POST'],'getquestionnaire', [ApievaluationController::class, 'getQuestionnaire']); 
Route::match(['POST'],'getnotation', [ApievaluationController::class, 'getNotation']); 
Route::match(['POST'],'getcampagne', [AuthController::class, 'getCampagne']);

//route formation staff

Route::match(['POST'],'apiformationstaff', [ApiFormationStaffController::class, 'store']); 
// route pour gestions des besoins en arbres
Route::match(['POST'],'apiagroevaluation', [ApiAgroEvaluationContoller::class, 'store']);
Route::match(['POST'],'getbesoinsproducteurs', [ApiAgroEvaluationContoller::class, 'getproducteursBesoin']);
Route::match(['POST'],'apidistribution', [ApiAgroEvaluationContoller::class, 'store_distribution']);
Route::match(['POST'],'getbesoinprod', [ApiAgroEvaluationContoller::class, 'besoinproducteur']);
Route::match(['POST'],'getproducteursdistribues', [ApiAgroEvaluationContoller::class, 'producteursDistribues']);
Route::match(['POST'],'getapprovisionnementsection', [ApiAgroEvaluationContoller::class, 'getApprovisionnementSection']);
// route pour post planting
    Route::match(['POST'],'postplanting', [ApiAgroEvaluationContoller::class, 'storePostPlanting']);
    Route::match(['POST'],'getdistributionproducteur', [ApiAgroEvaluationContoller::class, 'getdistributionproducteur']);

// route pour sous themes
Route::match(['POST'],'getsousthemes', [ApisuiviformationController::class, 'getsousthemes']);

//route pour la gestion des sections

Route::match(['POST'],'getsections',[ApisectionController::class, 'getsections']);

//route pour la gestion des programmes

Route::match(['POST'],'getprogrammes',[ApiProgrammeController::class, 'index']);

// get arbre 

Route::match(['POST'],'getarbre',[ApiAbreController::class, 'getarbre']);


