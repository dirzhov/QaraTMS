<?php

use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TestResultsController;
use App\Http\Controllers\Api\AutomationTestRunApiController;

use App\Http\Controllers\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('health-check', function () {
    return response()->json([ 'status' => 'OK', 'timestamp' => \Carbon\Carbon::now() ]);
});
Route::post('auth', [ApiAuthController::class, 'login_post'])->name('api_auth');
Route::post('register', [ApiAuthController::class, 'register'])->name('api_register');

Route::middleware('auth:api')->group( function () {
//Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/project/{project_id}/autotest-runs', [AutomationTestRunApiController::class, 'index'])
        ->where('project_id', '[0-9]+');

    Route::apiResource('test_results',TestResultsController::class);

//    Route::get('/test_results/{test_run_id}', [AutomationTestRunApiController::class, 'index'])
//        ->where('test_run_id', '[0-9]+');

    Route::get('/test_results/page/{page?}/pageSize/{pageSize?}',[TestResultsController::class, 'index']);

    Route::post('/upload_screenshot', [TestResultsController::class, 'upload_screenshot']);
    Route::post('/upload_testlog', [TestResultsController::class, 'upload_testlog']);

    Route::get('/statistics/get_all_by_groups/{id}',[StatisticsController::class, 'get_all_by_groups']);
    Route::get('/statistics/get_all_by_priority/{id}',[StatisticsController::class, 'get_all_by_priority']);
    Route::get('/statistics/get_all_by_severity/{id}',[StatisticsController::class, 'get_all_by_severity']);
    Route::get('/statistics/get_all_by_duration/{id}',[StatisticsController::class, 'get_all_by_duration']);
    Route::get('/statistics/get_all_app_defects/{id}',[StatisticsController::class, 'get_all_app_defects']);
    Route::get('/statistics/get_defects_of_group/{id}/{group}',[StatisticsController::class, 'get_defects_of_group']);

});

