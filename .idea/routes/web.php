<?php

// use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
// use App\Http\Controllers\BillHeadController;
// use App\Http\Controllers\BillMaasterController;
use App\Http\Controllers\BulkSmsController;
// use App\Http\Controllers\CounterPaymentController;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\LiftController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Reports\AuditLogReportController;
use App\Http\Controllers\RoleController;
// use App\Http\Controllers\RoomController;
// use App\Http\Controllers\BureauController;
// use App\Http\Controllers\FloorController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OperatorController ; 

use App\Http\Controllers\AptOverrideController ;
use App\Http\Controllers\StickerController ;
use App\Http\Controllers\StickerPrintController ;
use App\Http\Controllers\RegionController ;
use App\Http\Controllers\CenterController ;
use App\Http\Controllers\ServiceController ;
use App\Http\Controllers\CounterController ;
use App\Http\Controllers\DeviceController ;
use App\Http\Controllers\HolidayController ;
use App\Http\Controllers\VisaTypeController ;
use App\Http\Controllers\PortController ;
use App\Http\Controllers\RejectReasonController ;
use App\Http\Controllers\CorrectionTypeController ;

// use App\Http\Controllers\MemberController;
// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\RoomTypeController;
// use App\Http\Controllers\TabPaymentController;
// use App\Http\Controllers\UserRankController;
// use App\Http\Controllers\RoomBookingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\WebsiteManagementController;
// use App\Http\Controllers\Reports\BillingReportController;
use App\Http\Controllers\Reports\TransactionReportController ; 
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;




Route::get('/clear-all', function () {
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');

    return 'All clear commands executed!';
});

Route::get('/cache-all', function () {
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    Artisan::call('config:cache');
    return 'All cache commands executed!';
});


// Password reset routes
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');


Route::post('/change-password', [NewPasswordController::class, 'change'])
    ->name('password.change')->middleware(['auth', 'throttle:5,1']);


// Route::get('/', [HomeController::class, 'index'])->name('website.index');
Route::get('/', [HomeController::class, 'signin'])->name('website.index');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('website.gallery');
Route::get('/service-item/{id}', [HomeController::class, 'service'])->name('website.service');
Route::get('/signin', [HomeController::class, 'signin'])->name('website.signin')->middleware('throttle:login');
Route::get('/signup', [HomeController::class, 'signup'])->name('website.signup')->middleware('throttle:register');
Route::get('/profile', [HomeController::class, 'profile'])->name('website.profile');
Route::get('/bill', [HomeController::class, 'billList'])->name('website.billList');
Route::get('/room-booking', [HomeController::class, 'roomBooking'])->name('website.roomBooking');
Route::get('/room-booking-list', [HomeController::class, 'roomBookingList'])->name('website.roomBookingList');
Route::get('/bill-detail-invoice/{invoiceNo}', [HomeController::class, 'billDetailByInvoice'])->name('website.billDetailByInvoice');
Route::get('/bill-payment/{invoiceNo}', [HomeController::class, 'billPaymentByInvoice'])->name('website.billPaymentByInvoice');
Route::get('/mess-policy/pdf', [HomeController::class, 'messPolicyPDF'])->name('website.messPolicyPDF');


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role_permissions.index');
        Route::get('/role-permissions/{role_id}', [RolePermissionController::class, 'getPermissions']);
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/role-permission', [RolePermissionController::class, 'store'])->name('role_permissions.store');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::get('/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/store', [UserManagementController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });



//FCMS Date Jun 9, ,2025
    Route::controller(ShopController::class)
    ->middleware('permission:can_view')->group(function () {
        Route::get('/shops', 'index')->name('shops.index');
        Route::post('/shops', 'store')->name('shops.store');
        Route::put('/shops/{shop}', 'update')->name('shops.update');
        Route::delete('/shops/{shop}', 'destroy')->name('shops.destroy');
    });

    Route::controller(OperatorController::class)
    ->middleware('permission:can_view')->group(function () {
        Route::get('/operators', 'index')->name('operators.index');
        Route::post('/operators', 'store')->name('operators.store');
        Route::put('/operators/{operator}', 'update')->name('operators.update');
        Route::delete('/operators/{operator}', 'destroy')->name('operators.destroy');
        Route::get('/operator-shop/{operator}', 'OprShop')->name('operators.assign');
        Route::put('/operatorShopUpdate', 'updateAssign')->name('operators.updateAssign');
    });
    
    /* Route::controller(TerminalController::class)
    ->middleware('permission:can_view')->group(function () {
        Route::get('/terminals', 'index')->name('terminals.index');
        Route::post('/terminals', 'store')->name('terminals.store');
        Route::put('/terminals/{terminal}', 'update')->name('terminals.update');
        Route::delete('/terminals/{terminal}', 'destroy')->name('terminals.destroy');
        Route::get('/terminals-shop/{terminal}', 'assign')->name('terminals.assign');
        Route::put('/terminalsShopUpdate', 'updateAssign')->name('terminals.updateAssign');
    }); */



    Route::controller(TransactionReportController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/transaction-reports', 'index')->name('transaction-reports.index');
            Route::get('/billing-summary-reports', 'billingSummaryReport')->name('reports.billing-summary');
            Route::get('/reports/room-booking-calendar', 'roomBookingCalendar')->name('reports.room-booking-calendar');
            Route::get('/reports/payment-history-report', 'paymentHistoryReport')->name('reports.patyment-history-report');
            Route::get('/reports/user-booking-report', 'userBookingReport')->name('reports.user-booking-report');

        });
		
    // IVAC Date 02-07-2025 abi
   Route::get('/searchTransactions', [TransactionReportController::class, 'searchTx'])->name('transaction-reports.searchTx');     


   Route::middleware('permission:can_view')->group(function () {
        Route::get('/print-barcode-sticker', [StickerPrintController::class, 'index'])->name('barcode-sticker.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/print-sticker-barcode', [StickerPrintController::class, 'store'])->name('barcode-sticker.store');
    });

   Route::middleware('permission:can_view')->group(function () {
        Route::get('/apt-override', [AptOverrideController::class, 'index'])->name('apt-override.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/apt-override', [AptOverrideController::class, 'store'])->name('apt-override.store');
    });
	// IVAC Date 26-06-2025

	Route::middleware('permission:can_view')->group(function () {
        Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/regions', [RegionController::class, 'store'])->name('regions.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/regions/{region}', [RegionController::class, 'update'])->name('regions.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/regions/{region}',[RegionController::class, 'destroy'])->name('regions.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/centers', [CenterController::class, 'index'])->name('centers.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/centers', [CenterController::class, 'store'])->name('centers.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/centers/{center}', [CenterController::class, 'update'])->name('centers.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/centers/{center}',[CenterController::class, 'destroy'])->name('centers.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/services/{service}',[ServiceController::class, 'destroy'])->name('services.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/counters', [CounterController::class, 'index'])->name('counters.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/counters', [CounterController::class, 'store'])->name('counters.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/counters/{counter}', [CounterController::class, 'update'])->name('counters.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/counters/{counter}',[CounterController::class, 'destroy'])->name('counters.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/devices/{device}',[DeviceController::class, 'destroy'])->name('devices.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/holidays/{holiday}',[HolidayController::class, 'destroy'])->name('holidays.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/visa_types', [VisaTypeController::class, 'index'])->name('visa_types.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/visa_types', [VisaTypeController::class, 'store'])->name('visa_types.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/visa_types/{visa_type}', [VisaTypeController::class, 'update'])->name('visa_types.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/visa_types/{visa_type}',[VisaTypeController::class, 'destroy'])->name('visa_types.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/ports', [PortController::class, 'store'])->name('ports.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/ports/{port}', [PortController::class, 'update'])->name('ports.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/ports/{port}',[PortController::class, 'destroy'])->name('ports.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/reject_reasons', [RejectReasonController::class, 'index'])->name('reject_reasons.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/reject_reasons', [RejectReasonController::class, 'store'])->name('reject_reasons.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/reject_reasons/{reject_reason}', [RejectReasonController::class, 'update'])->name('reject_reasons.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/reject_reasons/{reject_reason}',[RejectReasonController::class, 'destroy'])->name('reject_reasons.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/correction_types', [CorrectionTypeController::class, 'index'])->name('correction_types.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/correction_types', [CorrectionTypeController::class, 'store'])->name('correction_types.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/correction_types/{correction_type}', [CorrectionTypeController::class, 'update'])->name('correction_types.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/correction_types/{correction_type}',[CorrectionTypeController::class, 'destroy'])->name('correction_types.destroy');
    });
	


    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    // });
    // Route::middleware('permission:can_create')->group(function () {
    //     Route::post('/menus', [ShopController::class, 'store'])->name('menus.store');
    //     Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    // });
    // Route::middleware('permission:can_delete')->group(function () {
    //     Route::delete('/menus/{menu}', [ShopController::class, 'destroy'])->name('menus.destroy');
    // });











    Route::middleware('permission:can_view')->group(function () {
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
    });


    Route::middleware('permission:can_view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/website-ms', [WebsiteManagementController::class, 'index'])->name('website-ms');
    });

    Route::middleware('permission:can_create')->prefix('web-ms')->group(function () {
        Route::get('/sliders', [WebsiteManagementController::class, 'fetchSlider'])->name('sliders');
        Route::post('/sliders', [WebsiteManagementController::class, 'sliderStore'])->name('sliders.store');
        Route::delete('/sliders/{slider}', [WebsiteManagementController::class, 'sliderDestroy'])->name('sliders.destroy');

        Route::get('/galleries', [WebsiteManagementController::class, 'fetchGallery'])->name('gallery');
        Route::post('/galleries', [WebsiteManagementController::class, 'galleryStore'])->name('gallery.store');
        Route::delete('/galleries/{gallery}', [WebsiteManagementController::class, 'galleryDestroy'])->name('gallery.destroy');

        Route::get('/header-info', [WebsiteManagementController::class, 'headerInfo'])->name('headerInfo.show');
        Route::post('/header-info', [WebsiteManagementController::class, 'headerInfoStore'])->name('headerInfo.create');

        Route::get('/footer-info', [WebsiteManagementController::class, 'footerInfo'])->name('footerInfo.show');
        Route::post('/footer-info', [WebsiteManagementController::class, 'footerInfoStore'])->name('footerInfo.create');

        Route::get('/footer-links', [WebsiteManagementController::class, 'footerLink'])->name('footerLink.show');
        Route::post('/footer-links', [WebsiteManagementController::class, 'footerLinkStore'])->name('footerLink.create');
        Route::delete('/footer-links/{link}', [WebsiteManagementController::class, 'footerLinkDestroy'])->name('footerLink.destroy');

        Route::get('/services', [WebsiteManagementController::class, 'fetchService'])->name('serviceLink.index');
        Route::post('/services', [WebsiteManagementController::class, 'storeService'])->name('serviceLink.create');
        Route::post('/services/{id}', [WebsiteManagementController::class, 'updateService'])->name('serviceLink.update');
        Route::delete('/services/{id}', [WebsiteManagementController::class, 'serviceLinkDestroy'])->name('serviceLink.destroy');

    });


    Route::controller(RoomTypeController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/room_types', 'index')->name('room_types.index');
            Route::post('/room_types', 'store')->name('room_types.store');
            Route::put('/room_types/{room_type}', 'update')->name('room_types.update');
            Route::delete('/room_types/{room_type}', 'destroy')->name('room_types.destroy');
        });

    Route::controller(FloorController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/floors', 'index')->name('floors.index');
            Route::post('/floors', 'store')->name('floors.store');
            Route::put('/floors/{floor}', 'update')->name('floors.update');
            Route::delete('/floors/{floor}', 'destroy')->name('floors.destroy');
        });

    Route::controller(UserRankController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/user-ranks', 'index')->name('userRanks.index');
            Route::post('/user-ranks', 'store')->name('userRanks.store');
            Route::put('/user-ranks/{floor}', 'update')->name('userRanks.update');
            Route::delete('/user-ranks/{floor}', 'destroy')->name('userRanks.destroy');
        });

    Route::controller(LiftController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/lifts', 'index')->name('lifts.index');
            Route::post('/lifts', 'store')->name('lifts.store');
            Route::put('/lifts/{lift}', 'update')->name('lifts.update');
            Route::delete('/lifts/{lift}', 'destroy')->name('lifts.destroy');
        });

    Route::controller(RoomController::class)
    ->middleware('permission:can_view')->group(function () {
        Route::get('/rooms', 'index')->name('rooms.index');
        Route::post('/rooms', 'store')->name('rooms.store');
        Route::put('/rooms/{room}', 'update')->name('rooms.update');
        Route::delete('/rooms/{room}', 'destroy')->name('rooms.destroy');
    });

    Route::get('/bill-list-by-user', [BillController::class, 'index']);

    //bill master
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/bill-master', [BillMaasterController::class, 'index'])->name('bill-master.index');
        Route::get('/bill-master/{id}', [BillMaasterController::class, 'show'])->name('bill-master.show');
        Route::get('/bill-master-print/{id}', [BillMaasterController::class, 'print'])->name('bill-master.print');
    });
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/get-bookings-details/{user_id}', [BillMaasterController::class, 'getBookingDetails']);
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::get('/master-create', [BillMaasterController::class, 'create'])->name('bill-master.create');
        Route::post('/master-store', [BillMaasterController::class, 'store'])->name('bill-master.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/bill-master/{bill}/edit', [BillMaasterController::class, 'edit'])->name('bill-master.edit');
        Route::put('/bill-master/{bill}', [BillMaasterController::class, 'update'])->name('bill-master.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/bill-master/{id}', [BillMaasterController::class, 'destroy'])->name('bill-master.destroy');
    });

    Route::middleware('permission:can_create')->group(function () {
        Route::get('/admin/counter-payment', [CounterPaymentController::class, 'create'])->name('counterPayment.create');
    });


    Route::middleware('permission:can_view')->group(function () {
        Route::get('/payment/receipt/{txn_no}', [CounterPaymentController::class, 'showReceipt'])
            ->name('payment.receipt');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show');
    });

    Route::middleware('permission:can_create')->group(function () {
        Route::get('/member-create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/member-store', [MemberController::class, 'store'])->name('members.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/members/{id}/approve', [MemberController::class, 'approve'])->name('members.approve');
    });

    Route::middleware('permission:can_edit')->group(function () {

        Route::get('/member/{user}', [MemberController::class, 'edit'])
             ->name('members.edit');

        Route::put('/member/{user}', [MemberController::class, 'update'])
             ->name('members.update');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/admin/room-booking', [RoomBookingController::class, 'index'])->name('adminRoomBooking.index');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/admin/room-booking-create', [RoomBookingController::class, 'create'])->name('adminRoomBooking.create');
        Route::post('/admin/room-booking-store', [RoomBookingController::class, 'adminRoomBookingStore'])->name('roomBookingByUserIdNo.store');
    });

    Route::post('/room-booking-store', [RoomBookingController::class, 'roomBooking'])->name('adminRoomBooking.store');

    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/admin/room-booking-approve/{id}', [RoomBookingController::class, 'roomBookingEdit'])->name('adminRoomBooking.edit');
        Route::put('/admin/room-booking-approve-update/{id}', [RoomBookingController::class, 'roomBookingUpdate'])->name('adminRoomBooking.update');
    });

    Route::controller(BillHeadController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/bill-heads', 'index')->name('bill_heads.index');
            Route::post('/bill-heads', 'store')->name('bill_heads.store');
            Route::put('/bill-heads/{bill_head}', 'update')->name('bill_heads.update');
            Route::delete('/bill-heads/{bill_head}', 'destroy')->name('bill_heads.destroy');
        });


    Route::controller(BillingReportController::class)
        ->middleware('permission:can_view')->group(function () {
            Route::get('/billing-reportsA', 'index')->name('billing-reports.index');
            Route::get('/billing-summary-reports', 'billingSummaryReport')->name('reports.billing-summary');
            Route::get('/reports/room-booking-calendar', 'roomBookingCalendar')->name('reports.room-booking-calendar');
            Route::get('/reports/payment-history-report', 'paymentHistoryReport')->name('reports.patyment-history-report');
            Route::get('/reports/user-booking-report', 'userBookingReport')->name('reports.user-booking-report');

        });

    Route::middleware('permission:can_create')->group(function () {
        Route::get('/admin/bulk-sms', [BulkSmsController::class, 'create'])->name('bulkSMS.create');
    });

    Route::controller(BureauController::class)
        ->middleware('permission:can_view')
        ->group(function () {
            Route::get('/bureaus', 'index')->name('bureaus.index');
            Route::post('/bureaus', 'store')->name('bureaus.store');
            Route::put('/bureaus/{bureau}', 'update')->name('bureaus.update');
            Route::delete('/bureaus/{bureau}', 'destroy')->name('bureaus.destroy');
        });

    Route::controller(AuditLogReportController::class)
        ->middleware('permission:can_view')
        ->group(function () {
            Route::get('/reports/audit-logs', 'index')->name('audit-logs.index');
            Route::get('/reports/audit-logs/export', 'export')->name('audit-logs.export');
        });


//    Route::middleware('permission:can_edit')->group(function () {
//        Route::get('/members/{user}/edit', [MemberController::class, 'edit'])->name('members.edit');
//        Route::put('/members/{user}', [MemberController::class, 'update'])->name('members.update');
//    });
//    Route::middleware('permission:can_delete')->group(function () {
//        Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');
//    });


    // SSLCOMMERZ Start
//    Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
//    Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

    Route::post('/pay-bill', [BillController::class, 'billPaymentRequestProcess'])->name('website.billPaymentRequestProcess');
    Route::post('/pay', [SslCommerzPaymentController::class, 'index'])->name('website.sslComInitiatePayment');
    Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

    Route::post('/success', [SslCommerzPaymentController::class, 'success']);
    Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
    Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

    Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
    //SSLCOMMERZ END




    // routes/web.php
    Route::get('/payment-tap', [TabPaymentController::class, 'showForm'])->name('payment.form');
    Route::post('/payment/process', [TabPaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/callback', [TabPaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::get('/payment/success', [TabPaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed', [TabPaymentController::class, 'failed'])->name('payment.failed');



});

Route::get('/get-ranks/{rankTypeId}', [MemberController::class, 'getRank'])
     ->name('get-ranks');

Route::get('/get-ranks-frontend/{rankTypeId}', [HomeController::class, 'getRank'])
     ->name('get-ranks.frontend');



Route::get('/permission-denied', function () {
    return view('errors.403');
})->name('permission.denied');

require __DIR__.'/auth.php';
