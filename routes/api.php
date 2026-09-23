<?php

use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\BulkSmsController;
use App\Http\Controllers\Api\OldDataController;
use App\Http\Controllers\Api\HardwareController;
use App\Http\Controllers\Api\WebSearchController ; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('metrics', [DashboardApiController::class, 'getMetrics']);
});


// Route::prefix('admin/dashboard')->group(function () {
//     Route::get('metrics', [DashboardApiController::class, 'getMetrics']);
//     // Route::get('recent-bookings', [DashboardApiController::class, 'getRecentBookings']);
//     // Route::get('recent-bills', [DashboardApiController::class, 'getRecentBills']);
//     // Route::get('bill-categories', [DashboardApiController::class, 'getBillCategories']);
//     // Route::get('room-status', [DashboardApiController::class, 'getRoomStatus']);
//     // Route::get('revenue', [DashboardApiController::class, 'getRevenueData']);
// });


//Jun 30, 2025 by abi
Route::middleware('auth:sanctum')->post('/old-data', [OldDataController::class, 'store']);
Route::middleware('auth:sanctum')->post('/get-host-key', [OldDataController::class, 'getkey']);

Route::middleware('auth:sanctum')->post('/sync-devs', [HardwareController::class, 'syncDev']);
Route::middleware('auth:sanctum')->post('/get-token',[HardwareController::class, 'issueToken']);
// Route::middleware('auth:sanctum')->post('/get-token',[HardwareController::class, 'issueToken']);
Route::middleware('auth:sanctum')->post('/get-display', [HardwareController::class, 'issueDisplay']);
Route::middleware('auth:sanctum')->post('/get-audio', [HardwareController::class, 'issueAudio']);
Route::middleware('auth:sanctum')->post('/del-audio', [HardwareController::class, 'delAudio']);

Route::middleware('auth:sanctum')->post('/data-search', [WebSearchController::class, 'search']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {

});
