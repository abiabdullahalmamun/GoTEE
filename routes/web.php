<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BulkSmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Reports\AuditLogReportController;
use App\Http\Controllers\RoleController;

// use App\Http\Controllers\RoomController;
// use App\Http\Controllers\BureauController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OperatorController ; 
use App\Http\Controllers\SmsController ; 
use App\Http\Controllers\CallTokenController ; 
use App\Http\Controllers\AppReceiveController ;
use App\Http\Controllers\FrpReceiveController ; 
use App\Http\Controllers\Send2HCIController ; 
use App\Http\Controllers\Accept4mHCIController ; 
use App\Http\Controllers\Reject4mHCIController ; 
use App\Http\Controllers\Direct4mHCIController ; 
use App\Http\Controllers\ManualAppReceiveController ; 
use App\Http\Controllers\ReadyCenterController ; 
use App\Http\Controllers\BiometricController ; 
use App\Http\Controllers\FormFillController ; 
use App\Http\Controllers\ReadyDelDeleteController; 
use App\Http\Controllers\CenterDeliveryController ; 
use App\Http\Controllers\Send2MOFAController ; 
use App\Http\Controllers\StageUpdateController ; 
use App\Http\Controllers\ReadyDelActController ; 
use App\Http\Controllers\CodeUpdateController ; 
use App\Http\Controllers\AcceptRejectController ; 
use App\Http\Controllers\BackupController ; 
use App\Http\Controllers\VisaDurationController ;
use App\Http\Controllers\MoneyReceiptController ;
use App\Http\Controllers\CurrencyRateController ; 
use App\Http\Controllers\EntryTypeController ; 
use App\Http\Controllers\TokenOverrideController ; 
use App\Http\Controllers\Forms4mHCIController ; 
use App\Http\Controllers\PortReceiveController ;
use App\Http\Controllers\DigitizationScanController ; 
use App\Http\Controllers\WebfileEditController ; 
use App\Http\Controllers\AptOverrideController ;
use App\Http\Controllers\ApprovalController ;  
use App\Http\Controllers\AptListImportController ;
use App\Http\Controllers\AdjustUndeliveredController ; 
use App\Http\Controllers\StickerController ;
use App\Http\Controllers\StickerPrintController ;
use App\Http\Controllers\RegionController ;
use App\Http\Controllers\CenterController ;
use App\Http\Controllers\ServiceController ;
use App\Http\Controllers\CounterController ;
use App\Http\Controllers\DeviceController ;
use App\Http\Controllers\HolidayController ;
use App\Http\Controllers\VisaTypeController ;
use App\Http\Controllers\StickerTypeController ; 
use App\Http\Controllers\PortNameController ;
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

use App\Http\Controllers\Reports\MotorStatusReportController ; 

// use App\Http\Controllers\Reports\BillingReportController;
// use App\Http\Controllers\Reports\RejectReportController ; 
// use App\Http\Controllers\Reports\FormFillReportController ; 
// use App\Http\Controllers\Reports\BiometricReportController ; 

// use App\Http\Controllers\Reports\TransactionReportController ; 
// use App\Http\Controllers\Reports\SearchDataController ; 
// use App\Http\Controllers\Reports\ReceiveReportController ; 
// use App\Http\Controllers\Reports\FrpReceiveReportController ; 
// use App\Http\Controllers\Reports\StickerReportController ; 
// use App\Http\Controllers\Reports\OverrideReportController; 
// use App\Http\Controllers\Reports\SmsReportController ; 
// use App\Http\Controllers\Reports\Sent2hciReportController ; 
// use App\Http\Controllers\Reports\Rec4mhciReportController ; 
// use App\Http\Controllers\Reports\ReadyCenterReportController; 
// use App\Http\Controllers\Reports\DeliveryReportController ; 
// use App\Http\Controllers\Reports\MissingStickerReportController ; 
// use App\Http\Controllers\Reports\UndeliveredReportController ; 
// use App\Http\Controllers\Reports\ActivityReportController;
// use App\Http\Controllers\Reports\TokenReportController ;
// use App\Http\Controllers\Reports\DigitizationReportController ; 
use App\Http\Controllers\Reports\DBqueryController ; 
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


// Route::get('/', [HomeController::class, 'signin'])->name('website.signin')->middleware('throttle:login');
// Route::get('/', [HomeController::class, 'index'])->name('website.index');
Route::get('/', [HomeController::class, 'signin'])->name('website.index');
// Route::get('/gallery', [HomeController::class, 'gallery'])->name('website.gallery');
// Route::get('/service-item/{id}', [HomeController::class, 'service'])->name('website.service');
// Route::get('/signin', [HomeController::class, 'signin'])->name('website.signin')->middleware('throttle:login');
// Route::get('/signup', [HomeController::class, 'signup'])->name('website.signup')->middleware('throttle:register');
// Route::get('/profile', [HomeController::class, 'profile'])->name('website.profile');
// Route::get('/bill', [HomeController::class, 'billList'])->name('website.billList');
// Route::get('/room-booking', [HomeController::class, 'roomBooking'])->name('website.roomBooking');
// Route::get('/room-booking-list', [HomeController::class, 'roomBookingList'])->name('website.roomBookingList');
// Route::get('/bill-detail-invoice/{invoiceNo}', [HomeController::class, 'billDetailByInvoice'])->name('website.billDetailByInvoice');
// Route::get('/bill-payment/{invoiceNo}', [HomeController::class, 'billPaymentByInvoice'])->name('website.billPaymentByInvoice');
// Route::get('/mess-policy/pdf', [HomeController::class, 'messPolicyPDF'])->name('website.messPolicyPDF');


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
        Route::post('/usersupdate/{user}',  [UserManagementController::class, 'updateAssign'])->name('users.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
 
   
    //GoTEE 5.0 Report Sep 23, 2026

    //MOTOR STATUS Report Sep 23, 2026
    Route::middleware(['permission:can_view'])->group(function () {
        Route::get('/motor-status', [MotorStatusReportController::class, 'index'])->name('motor-status.index');
     
        Route::get('/motor-status/export-excel', [MotorStatusReportController::class, 'exportExcel'])->name('motor-status.excel');
        Route::get('/motor-status/export-pdf', [MotorStatusReportController::class, 'exportPDF'])->name('motor-status.pdf');

        // Route::get('/exception-Report-summary', [ExceptionReportController::class, 'summary'])->name('exception-Report.summary');
    });

 

    //QUERY BUILDER 
    Route::middleware(['permission:can_view'])->group(function () {
        Route::get('/query-builder', [DBqueryController::class, 'index'])->name('query-builder.index');
        // Route::post('/override-Report', [StickerReportController::class, 'search'])->name('override-Report.search');
       Route::get('/query-builder/export-excel', [DBqueryController::class, 'exportExcel'])->name('query-builder.excel');
        Route::get('/query-builder/export-pdf', [DBqueryController::class, 'exportPDF'])->name('query-builder.pdf');
        Route::get('/query-builder-summary', [DBqueryController::class, 'summary'])->name('query-builder.summary');
    });


  


    // Route::get('/query-builder/columns/{id}', [DbQueryController::class, 'getColumns']);
    // Route::middleware(['permission:can_view'])->group(function () {
    //     Route::get('/undelivered-pass', [UndeliveredReportController::class, 'index'])->name('undelivered-pass.index');

    //     Route::post('/undelivered-pass', [UndeliveredReportController::class, 'search'])->name('undelivered-pass.search');
     
    //     Route::get('/undelivered-pass/export-excel', [UndeliveredReportController::class, 'exportExcel'])->name('undelivered-pass.excel');
    //     Route::get('/undelivered-pass/export-pdf', [UndeliveredReportController::class, 'exportPDF'])->name('undelivered-pass.pdf');
    // });
  // Route::middleware('permission:can_view')->group(function () {
  //      Route::post('/searchWebfile', [SearchDataController::class, 'search'])->name('searchWebfile.search');
  // });
    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/Receive-Report', [ReceiveReportController::class, 'index'])->name('Receive-Report.index');   
    //     Route::post('/Receive-Report', [ReceiveReportController::class, 'search'])->name('Receive-Report.search');      
    // });
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


    //Override Approve
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/AdminApproval', [ApprovalController::class, 'index'])->name('AdminApproval.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/AdminApproval', [ApprovalController::class, 'store'])->name('AdminApproval.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/AdminApproval/{id}', [ApprovalController::class, 'destroy'])->name('AdminApproval.destroy');
    });


    //SMS Controller
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/send-sms', [SmsController::class, 'index'])->name('send-sms.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/send-sms', [SmsController::class, 'store'])->name('send-sms.store');
    });


  //ADJUST UNDELIVERED PASSPORT
   Route::middleware('permission:can_view')->group(function () {
        Route::get('/adjust-undelivered', [AdjustUndeliveredController::class, 'index'])->name('adjust-undelivered.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/adjust-undelivered', [AdjustUndeliveredController::class, 'store'])->name('adjust-undelivered.store');
    });

    Route::post('/adjust-undelivered-sort', [AdjustUndeliveredController::class, 'sortFiles'])->name('adjust-undelivered.sort');

    Route::post('/adjust-undelivered-pushexcel', [AdjustUndeliveredController::class, 'pushExcel'])->name('push.excel');
    Route::post('/adjust-undelivered-pushdb', [AdjustUndeliveredController::class, 'pushDb'])->name('push.db');



  //APPLICANT LIST IMPORT
   Route::middleware('permission:can_view')->group(function () {
        Route::get('/applicant_list_import', [AptListImportController::class, 'index'])->name('applicant_list_import.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/applicant_list_import', [AptListImportController::class, 'store'])->name('applicant_list_import.store');
    });


//Form Fill-in Dec 24, 2025
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/form-fill', [FormFillController::class, 'index'])->name('form-fill.index');
   });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/form-fill', [FormFillController::class, 'store'])->name('form-fill.store');
    });
    Route::get('/form-fill-print/{id}', [FormFillController::class, 'print'])->name('form-fill.print');

    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/form-fill-edit/{id}', [FormFillController::class, 'edit'])->name('form-fill.edit');
        Route::put('/form-fill-edit/{id}', [FormFillController::class, 'update'])->name('form-fill.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/form-fill-delete/{id}', [FormFillController::class, 'destroy'])->name('form-fill.destroy');
    });


//Designate Walk-in, TokenType    
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/changeTokenType', [TokenOverrideController::class, 'index'])->name('changeTokenType.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/changeTokenType', [TokenOverrideController::class, 'store'])->name('changeTokenType.store');
    });

//Document Receive Scan
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/digitizationScan', [DigitizationScanController::class, 'index'])->name('digitizationScan.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/digitizationScan', [DigitizationScanController::class, 'store'])->name('digitizationScan.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/digitizationScan/{id}', [DigitizationScanController::class, 'destroy'])->name('digitizationScan.destroy');
    });

//Designate Document Receive from HCI    
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/receiveDocument4mhci', [Forms4mHCIController::class, 'index'])->name('receiveDocument4mhci.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/receiveDocument4mhci', [Forms4mHCIController::class, 'store'])->name('receiveDocument4mhci.store');
    });


//Application Receive
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/app-receive', [AppReceiveController::class, 'index'])->name('app-receive.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/app-receive', [AppReceiveController::class, 'store'])->name('app-receive.store');
    });
    Route::get('/app-receive-print/{id}', [AppReceiveController::class, 'print'])->name('app-receive.print');

    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/app-receive-edit/{id}', [AppReceiveController::class, 'edit'])->name('app-receive.edit');
        Route::put('/app-receive-edit/{id}', [AppReceiveController::class, 'update'])->name('app-receive.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/app-receive-delete/{id}', [AppReceiveController::class, 'destroy'])->name('app-receive.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/manual-receive', [ManualAppReceiveController::class,'index'])->name('manual-receive.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/manual-receive', [ManualAppReceiveController::class, 'store'])->name('manual-receive.store');
    });

//Webfile Replace
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/web-replace', [WebfileEditController::class, 'index'])->name('web-replace.index');
    });
   Route::middleware('permission:can_create')->group(function () {
        Route::post('/web-replace', [WebfileEditController::class, 'store'])->name('web-replace.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/web-replace/{id}', [WebfileEditController::class, 'edit'])->name('web-replace.edit');
        Route::put('/web-replace-edit/{id}', [WebfileEditController::class, 'update'])->name('web-replace.update');
    });
//Foreign Passport Jan 14, 2026
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/frpReceive', [FrpReceiveController::class, 'index'])->name('frpReceive.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/frpReceive', [FrpReceiveController::class, 'store'])->name('frpReceive.store');
    });
    Route::get('/frpReceive-print/{id}', [FrpReceiveController::class, 'print'])->name('frpReceive.print');

    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/frpReceive-edit/{id}', [FrpReceiveController::class, 'edit'])->name('frpReceive.edit');
        Route::put('/frpReceive-edit/{id}', [FrpReceiveController::class, 'update'])->name('frpReceive.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/frpReceive-delete/{id}', [FrpReceiveController::class, 'destroy'])->name('frpReceive.destroy');
    });

    Route::post('/check-webfile-foreign', [FrpReceiveController::class, 'checkweb'])->name('check.webpayf');
     Route::post('/check-RecptNo', [FrpReceiveController::class, 'checkreceipt'])->name('check.receipt');



    Route::middleware('permission:can_view')->group(function () {
        Route::get('/app-send2hci', [Send2HCIController::class, 'index'])->name('app-send2hci.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/app-send2hci', [Send2HCIController::class, 'store'])->name('app-send2hci.store');
    });

    Route::get('/get-sent2hci-webfiles', [Send2HCIController::class, 'getSend2hciList'])->name('sent2hci.web');

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/accept4mhci', [Accept4mHCIController::class, 'index'])->name('accept4mhci.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/accept4mhci', [Accept4mHCIController::class, 'store'])->name('accept4mhci.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/accept4mhci/{id}', [Accept4mhciController::class, 'destroy'])->name('accept4mhci.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/reject4mhci', [Reject4mHCIController::class, 'index'])->name('reject4mhci.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/reject4mhci', [Reject4mHCIController::class, 'store'])->name('reject4mhci.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/reject4mhci/{id}', [Reject4mHCIController::class, 'destroy'])->name('reject4mhci.destroy');
    });


    Route::middleware('permission:can_view')->group(function () {
        Route::get('/direct4mhci', [Direct4mHCIController::class, 'index'])->name('direct4mhci.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/direct4mhci', [Direct4mHCIController::class, 'store'])->name('direct4mhci.store');
    });
      Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/direct4mhci/{id}', [Direct4mHCIController::class, 'destroy'])->name('direct4mhci.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/acceptRejectAct', [AcceptRejectController::class, 'index'])->name('acceptRejectAct.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/acceptRejectAct', [AcceptRejectController::class, 'store'])->name('acceptRejectAct.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/acceptRejectAct/{id}', [AcceptRejectController::class, 'destroy'])->name('acceptRejectAct.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/readyCenter', [ReadyCenterController::class, 'index'])->name('readyCenter.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/readyCenter', [ReadyCenterController::class, 'store'])->name('readyCenter.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/readyCenter/{id}', [ReadyCenterController::class, 'destroy'])->name('readyCenter.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/centerDelivery', [CenterDeliveryController::class, 'index'])->name('centerDelivery.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/centerDelivery', [CenterDeliveryController::class, 'store'])->name('centerDelivery.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/centerDelivery/{id}', [CenterDeliveryController::class, 'destroy'])->name('centerDelivery.destroy');
    });

//Sent to MOFA
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/send2MOFA', [Send2MOFAController::class, 'index'])->name('send2MOFA.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/send2MOFA', [Send2MOFAController::class, 'store'])->name('send2MOFA.store');
    });
    // Route::middleware('permission:can_delete')->group(function () {
    //     Route::delete('/send2MOFA/{id}', [Send2MOFAController::class, 'destroy'])->name('send2MOFA.destroy');
    // });
//BIOMETRIC SCAN DEC 24, 2025
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/biometric', [BiometricController::class, 'index'])->name('biometric.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/biometric', [BiometricController::class, 'store'])->name('biometric.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/biometric/{id}', [BiometricController::class, 'destroy'])->name('biometric.destroy');
    });


    //Oct 12, 2025
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/readyDelDelete', [ReadyDelDeleteController::class, 'index'])->name('readyDelDelete.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/readyDelDelete', [ReadyDelDeleteController::class, 'store'])->name('readyDelDelete.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/readyDelDelete/{id}', [ReadyDelDeleteController::class, 'destroy'])->name('readyDelDelete.destroy');
    });



    Route::middleware('permission:can_view')->group(function () {
        Route::get('/updateStage', [StageUpdateController::class, 'index'])->name('updateStage.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/updateStage', [StageUpdateController::class, 'store'])->name('updateStage.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/updateStage/{id}', [StageUpdateController::class, 'destroy'])->name('updateStage.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/readyDelAct', [ReadyDelActController::class, 'index'])->name('readyDelAct.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/readyDelAct', [ReadyDelActController::class, 'store'])->name('readyDelAct.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/readyDelAct/{id}', [ReadyDelActController::class, 'destroy'])->name('readyDelAct.destroy');
    });

    //CODE UPDATE 
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/codeUpdate', [CodeUpdateController::class, 'index'])->name('codeUpdate.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/codeUpdate', [CodeUpdateController::class, 'store'])->name('codeUpdate.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/codeUpdate/{id}', [CodeUpdateController::class, 'destroy'])->name('codeUpdate.destroy');
    });
    // Route::delete('/accept4mhci/{id}', [Accept4mhciController::class, 'destroy'])->name('accept4mhci.destroy');

    // Route::middleware('permission:can_delete')->group(function () {
    //     Route::delete('/accept4mhci/{id}', [Accept4mHCIController::class, 'destroy'])->name('accept4mhci.destroy');
    // });    
    // Route::middleware('permission:can_create')->group(function () {
    //     Route::post('/accept4mhci', [Accept4mHCIController::class, 'store'])->name('accept4mhci.store');
    // });

        


//PORT ENDORSEMENT 
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/portupdate', [PortReceiveController::class, 'index'])->name('portupdate.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/portupdate', [PortReceiveController::class, 'store'])->name('portupdate.store');
    });
    Route::get('/portupdate-print/{id}', [PortReceiveController::class, 'print'])->name('portupdate.print');

    Route::middleware('permission:can_edit')->group(function () {
        Route::get('/portupdate-edit/{id}', [PortReceiveController::class, 'edit'])->name('portupdate.edit');
        Route::put('/portupdate-edit/{id}', [PortReceiveController::class, 'update'])->name('portupdate.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/portupdate-delete/{id}', [PortReceiveController::class, 'destroy'])->name('portupdate.destroy');
    });







    Route::get('/get-users-by-center/{centerId}', [AppReceiveController::class, 'getUsersByCenter'])->name('get.UsersByCenter');
    Route::get('/currq/{svctypeId}', [AppReceiveController::class, 'getCurrQ'])->name('get.currq');
    
    // Route::get('/defq/{svctypeId}', [AppReceiveController::class, 'getDefQ'])->name('get.defq');

    Route::post('/check-code-webfile', [AppReceiveController::class, 'checkCodeweb'])->name('check.code');
    Route::post('/check-webfile-payment', [AppReceiveController::class, 'checkwebpay'])->name('check.webpay');
    Route::post('/check-sticker-barcode', [AppReceiveController::class, 'checkbarcodeSticker'])->name('check.barcode');
    Route::post('/check-passport', [AppReceiveController::class, 'checkpassport'])->name('check.passport');
    Route::post('/update-tdd', [AppReceiveController::class, 'updatetdd'])->name('update.token');

    Route::post('/call-latest-token', [CallTokenController::class, 'callLatestToken'])->name('call-latest.token');
    Route::post('/defer-latest-token', [CallTokenController::class, 'deferLatestToken'])->name('defer-latest.token');
    Route::post('/set-counter-login', [AppReceiveController::class, 'counterLogin'])->name('counter.login');

    Route::post('/save-optype', [AppReceiveController::class, 'saveOpType'])->name('optype.save');
    Route::post('/save-visatype', [AppReceiveController::class, 'saveVisaType'])->name('visa.save');
    Route::post('/save-stickertype', [AppReceiveController::class, 'saveStickerType'])->name('sticker.save');

    Route::post('/send-otp', [SmsController::class, 'sendotp'])->name('send.otp');
    Route::post('/check-otp', [SmsController::class, 'checkotp'])->name('check.otp');
    
    Route::post('/save-dropdown-selection', function (\Illuminate\Http\Request $request){
        session(['dropdown_'.$request->key => $request->value]);
        return response()->json(['status' => 'saved']);
    });

    Route::post('/applications/reject', [AppReceiveController::class, 'reject'])->name('applications.reject');


    Route::middleware('permission:can_view')->group(function () {
        Route::get('/searchWebfile', [SearchDataController::class, 'index'])->name('searchWebfile.index');
    });
    Route::middleware('permission:can_view')->group(function () {
           Route::post('/searchWebfile', [SearchDataController::class, 'search'])->name('searchWebfile.search');
    });
    // Route::get('/searchContact', [SearchDataController::class, 'searchContact'])->name('application.searchcontact');



    // Route::middleware('permission:can_create')->group(function () {
    //     Route::get('/admin/counter-payment', [CounterPaymentController::class, 'create'])->name('counterPayment.create');
    // });
     Route::middleware('permission:can_create')->group(function () {
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::get('/backup/download', [BackupController::class, 'store'])->name('backup.store');
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
        Route::get('/counters-svc/{counter}', [CounterController::class, 'CounterService'])->name('counters.assign');
        Route::put('/counterServiceUpdate',  [CounterController::class, 'updateAssign'])->name('counters.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/counters/{counter}',[CounterController::class, 'destroy'])->name('counters.destroy');
    });
    Route::post('/enable-countercall/{counter}', [CounterController::class, 'enablecall'])
    ->name('countercall.enable');
	

	Route::middleware('permission:can_view')->group(function () {
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::get('/device-service/{device}', [DeviceController::class, 'DevSvc'])->name('devices.assign');
        Route::put('/deviceSvcUpdate',  [DeviceController::class, 'updateAssign'])->name('devices.updateAssign');

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
	
//sticker
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/sticker_type', [StickerTypeController::class, 'index'])->name('sticker_type.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/sticker_type', [StickerTypeController::class, 'store'])->name('sticker_type.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/sticker_type/{stickers}', [StickerTypeController::class, 'update'])->name('sticker_type.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/sticker_type/{stickers}',[StickerTypeController::class, 'destroy'])->name('sticker_type.destroy');
    });

	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/visa_types', [VisaTypeController::class, 'index'])->name('visa_types.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/visa_types', [VisaTypeController::class, 'store'])->name('visa_types.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/visa_types/{visa_type}', [VisaTypeController::class, 'update'])->name('visa_types.update');
        Route::get('/visa_types/{visa_type}', [VisaTypeController::class, 'VisaTypeTdd'])->name('visa_types.assign');
        Route::put('/visaTypeTddUpdate',  [VisaTypeController::class, 'updateAssign'])->name('visa_types.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/visa_types/{visa_type}',[VisaTypeController::class, 'destroy'])->name('visa_types.destroy');
    });
	
	
	Route::middleware('permission:can_view')->group(function () {
        Route::get('/ports', [PortNameController::class, 'index'])->name('ports.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/ports', [PortNameController::class, 'store'])->name('ports.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/ports/{port}', [PortNameController::class, 'update'])->name('ports.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/ports/{port}',[PortNameController::class, 'destroy'])->name('ports.destroy');
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
	

//visa type Jan 13, 2026
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/moneyReceipt', [MoneyReceiptController::class, 'index'])->name('moneyReceipt.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/moneyReceipt', [MoneyReceiptController::class, 'store'])->name('moneyReceipt.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/moneyReceipt/{moneyreceipt}', [MoneyReceiptController::class, 'update'])->name('moneyReceipt.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/moneyReceipt/{moneyreceipt}',[MoneyReceiptController::class, 'destroy'])->name('moneyReceipt.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/entryType', [EntryTypeController::class, 'index'])->name('entryType.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/entryType', [EntryTypeController::class, 'store'])->name('entryType.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/entryType/{entrytype}', [EntryTypeController::class, 'update'])->name('entryType.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/entryType/{entrytype}',[EntryTypeController::class, 'destroy'])->name('entryType.destroy');
    });

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/visaDuration', [VisaDurationController::class, 'index'])->name('visaDuration.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/visaDuration', [VisaDurationController::class, 'store'])->name('visaDuration.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/visaDuration/{visaduration}', [VisaDurationController::class, 'update'])->name('visaDuration.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/visaDuration/{visaduration}',[VisaDurationController::class, 'destroy'])->name('visaDuration.destroy');
    });


//CurrencyRate Jan 25, 2026
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/currencyRate', [CurrencyRateController::class, 'index'])->name('currencyRate.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/currencyRate', [CurrencyRateController::class, 'store'])->name('currencyRate.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/currencyRate/{currency}', [CurrencyRateController::class, 'update'])->name('currencyRate.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/currencyRate/{currency}',[CurrencyRateController::class, 'destroy'])->name('currencyRate.destroy');
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


    // Route::controller(RoomTypeController::class)
    //     ->middleware('permission:can_view')->group(function () {
    //         Route::get('/room_types', 'index')->name('room_types.index');
    //         Route::post('/room_types', 'store')->name('room_types.store');
    //         Route::put('/room_types/{room_type}', 'update')->name('room_types.update');
    //         Route::delete('/room_types/{room_type}', 'destroy')->name('room_types.destroy');
    //     });

    // Route::controller(FloorController::class)
    //     ->middleware('permission:can_view')->group(function () {
    //         Route::get('/floors', 'index')->name('floors.index');
    //         Route::post('/floors', 'store')->name('floors.store');
    //         Route::put('/floors/{floor}', 'update')->name('floors.update');
    //         Route::delete('/floors/{floor}', 'destroy')->name('floors.destroy');
    //     });

    // Route::controller(UserRankController::class)
    //     ->middleware('permission:can_view')->group(function () {
    //         Route::get('/user-ranks', 'index')->name('userRanks.index');
    //         Route::post('/user-ranks', 'store')->name('userRanks.store');
    //         Route::put('/user-ranks/{floor}', 'update')->name('userRanks.update');
    //         Route::delete('/user-ranks/{floor}', 'destroy')->name('userRanks.destroy');
    //     });

    // Route::controller(LiftController::class)
    //     ->middleware('permission:can_view')->group(function () {
    //         Route::get('/lifts', 'index')->name('lifts.index');
    //         Route::post('/lifts', 'store')->name('lifts.store');
    //         Route::put('/lifts/{lift}', 'update')->name('lifts.update');
    //         Route::delete('/lifts/{lift}', 'destroy')->name('lifts.destroy');
    //     });

    // Route::controller(RoomController::class)
    // ->middleware('permission:can_view')->group(function () {
    //     Route::get('/rooms', 'index')->name('rooms.index');
    //     Route::post('/rooms', 'store')->name('rooms.store');
    //     Route::put('/rooms/{room}', 'update')->name('rooms.update');
    //     Route::delete('/rooms/{room}', 'destroy')->name('rooms.destroy');
    // });

    // Route::get('/bill-list-by-user', [BillController::class, 'index']);

    //bill master
    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/bill-master', [BillMaasterController::class, 'index'])->name('bill-master.index');
    //     Route::get('/bill-master/{id}', [BillMaasterController::class, 'show'])->name('bill-master.show');
    //     Route::get('/bill-master-print/{id}', [BillMaasterController::class, 'print'])->name('bill-master.print');
    // });
    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/get-bookings-details/{user_id}', [BillMaasterController::class, 'getBookingDetails']);
    // });
    // Route::middleware('permission:can_create')->group(function () {
    //     Route::get('/master-create', [BillMaasterController::class, 'create'])->name('bill-master.create');
    //     Route::post('/master-store', [BillMaasterController::class, 'store'])->name('bill-master.store');
    // });
    // Route::middleware('permission:can_edit')->group(function () {
    //     Route::get('/bill-master/{bill}/edit', [BillMaasterController::class, 'edit'])->name('bill-master.edit');
    //     Route::put('/bill-master/{bill}', [BillMaasterController::class, 'update'])->name('bill-master.update');
    // });
    // Route::middleware('permission:can_delete')->group(function () {
    //     Route::delete('/bill-master/{id}', [BillMaasterController::class, 'destroy'])->name('bill-master.destroy');
    // });



    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/payment/receipt/{txn_no}', [CounterPaymentController::class, 'showReceipt'])
    //         ->name('payment.receipt');
    // });

    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    //     Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show');
    // });

    // Route::middleware('permission:can_create')->group(function () {
    //     Route::get('/member-create', [MemberController::class, 'create'])->name('members.create');
    //     Route::post('/member-store', [MemberController::class, 'store'])->name('members.store');
    // });
    // Route::middleware('permission:can_edit')->group(function () {
    //     Route::put('/members/{id}/approve', [MemberController::class, 'approve'])->name('members.approve');
    // });

    // Route::middleware('permission:can_edit')->group(function () {

    //     Route::get('/member/{user}', [MemberController::class, 'edit'])
    //          ->name('members.edit');

    //     Route::put('/member/{user}', [MemberController::class, 'update'])
    //          ->name('members.update');
    // });

    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/admin/room-booking', [RoomBookingController::class, 'index'])->name('adminRoomBooking.index');
    // });
    // Route::middleware('permission:can_edit')->group(function () {
    //     Route::get('/admin/room-booking-create', [RoomBookingController::class, 'create'])->name('adminRoomBooking.create');
    //     Route::post('/admin/room-booking-store', [RoomBookingController::class, 'adminRoomBookingStore'])->name('roomBookingByUserIdNo.store');
    // });

    // Route::post('/room-booking-store', [RoomBookingController::class, 'roomBooking'])->name('adminRoomBooking.store');

    // Route::middleware('permission:can_edit')->group(function () {
    //     Route::get('/admin/room-booking-approve/{id}', [RoomBookingController::class, 'roomBookingEdit'])->name('adminRoomBooking.edit');
    //     Route::put('/admin/room-booking-approve-update/{id}', [RoomBookingController::class, 'roomBookingUpdate'])->name('adminRoomBooking.update');
    // });

    // Route::controller(BillHeadController::class)
    //     ->middleware('permission:can_view')->group(function () {
    //         Route::get('/bill-heads', 'index')->name('bill_heads.index');
    //         Route::post('/bill-heads', 'store')->name('bill_heads.store');
    //         Route::put('/bill-heads/{bill_head}', 'update')->name('bill_heads.update');
    //         Route::delete('/bill-heads/{bill_head}', 'destroy')->name('bill_heads.destroy');
    //     });




    Route::middleware('permission:can_create')->group(function () {
        Route::get('/admin/bulk-sms', [BulkSmsController::class, 'create'])->name('bulkSMS.create');
    });

    // Route::controller(BureauController::class)
    //     ->middleware('permission:can_view')
    //     ->group(function () {
    //         Route::get('/bureaus', 'index')->name('bureaus.index');
    //         Route::post('/bureaus', 'store')->name('bureaus.store');
    //         Route::put('/bureaus/{bureau}', 'update')->name('bureaus.update');
    //         Route::delete('/bureaus/{bureau}', 'destroy')->name('bureaus.destroy');
    //     });

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

    // Route::post('/pay-bill', [BillController::class, 'billPaymentRequestProcess'])->name('website.billPaymentRequestProcess');
  
    // Route::post('/pay', [SslCommerzPaymentController::class, 'index'])->name('website.sslComInitiatePayment');
    // Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

    // Route::post('/success', [SslCommerzPaymentController::class, 'success']);
    // Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
    // Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

    // Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
    //SSLCOMMERZ END




    // routes/web.php
    // Route::get('/payment-tap', [TabPaymentController::class, 'showForm'])->name('payment.form');
    // Route::post('/payment/process', [TabPaymentController::class, 'processPayment'])->name('payment.process');
    // Route::get('/payment/callback', [TabPaymentController::class, 'handleCallback'])->name('payment.callback');
    // Route::get('/payment/success', [TabPaymentController::class, 'success'])->name('payment.success');
    // Route::get('/payment/failed', [TabPaymentController::class, 'failed'])->name('payment.failed');



});

// Route::get('/get-ranks/{rankTypeId}', [MemberController::class, 'getRank'])
//      ->name('get-ranks');

Route::get('/get-ranks-frontend/{rankTypeId}', [HomeController::class, 'getRank'])
     ->name('get-ranks.frontend');



Route::get('/permission-denied', function () {
    return view('errors.403');
})->name('permission.denied');

require __DIR__.'/auth.php';
