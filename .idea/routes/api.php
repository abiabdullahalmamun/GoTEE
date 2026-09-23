<?php

use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\BulkSmsController;
use App\Http\Controllers\Api\OldDataController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/get-user-info-by-idno', [BillController::class, 'getUserInfoByIdno'])->name('getUserInfoByIdno');
Route::middleware('auth')->group(function () {
    Route::get('/bill-list-by-user', [BillController::class, 'index']);
});
Route::get('/bills/{bill}/items', [BillController::class, 'items']);

Route::get('/users/{user_ba_no}', [BillController::class, 'getUserByBaNo']);
Route::get('/bills/unpaid/{user_id}', [BillController::class, 'getUnpaidBills']);
Route::get('/bill-heads', [BillController::class, 'getBillHeads']);
Route::post('/payments/process', [BillController::class, 'counterPaymentProcessPayment']);
Route::post('/payment-request', [BillController::class, 'paymentRequest']);
Route::get('/get-rooms-by-type', [BillController::class, 'roomsByType']);
Route::get('/active-members', [BulkSmsController::class, 'ActiveMembers']);
Route::post('/send-bulk-sms', [BulkSmsController::class, 'sendBulkSms']);;


Route::prefix('admin/dashboard')->group(function () {
    Route::get('metrics', [DashboardApiController::class, 'getMetrics']);
    Route::get('recent-bookings', [DashboardApiController::class, 'getRecentBookings']);
    Route::get('recent-bills', [DashboardApiController::class, 'getRecentBills']);
    Route::get('bill-categories', [DashboardApiController::class, 'getBillCategories']);
    Route::get('room-status', [DashboardApiController::class, 'getRoomStatus']);
    Route::get('revenue', [DashboardApiController::class, 'getRevenueData']);
});

//Jun 30, 2025 by abi
Route::middleware('auth:sanctum')->post('/old-data', [OldDataController::class, 'store']);
Route::middleware('auth:sanctum')->post('/get-host-key', [OldDataController::class, 'getkey']);





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {

});
