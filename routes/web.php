<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BulkSmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;

use App\Http\Controllers\RoleController;

use App\Http\Controllers\QCentryController ; 
use App\Http\Controllers\DeviceController ;
use App\Http\Controllers\OrderNoController ;
use App\Http\Controllers\OperationListController ;
use App\Http\Controllers\OperationBankController ; 
use App\Http\Controllers\FloorController ;
use App\Http\Controllers\LineController ;
use App\Http\Controllers\LineQCController ;
use App\Http\Controllers\StickerPrintController ;

use App\Http\Controllers\SewingMachineController ; 
use App\Http\Controllers\MachineTypeController; 
use App\Http\Controllers\MachineBrandController;
// use App\Http\Controllers\ShopController;
use App\Http\Controllers\OperatorController ; 
use App\Http\Controllers\EmpDesignationTypeController ; 
use App\Http\Controllers\EmployeeGradeController ; 
use App\Http\Controllers\EmployeeDesignationController ; 

use App\Http\Controllers\SmsController ; 


// use App\Http\Controllers\Reports\AuditLogReportController;

// use App\Http\Controllers\CallTokenController ; 
// use App\Http\Controllers\AppReceiveController ;
// use App\Http\Controllers\FrpReceiveController ; 
// use App\Http\Controllers\Send2HCIController ; 
// use App\Http\Controllers\Accept4mHCIController ; 
// use App\Http\Controllers\Reject4mHCIController ; 
// use App\Http\Controllers\Direct4mHCIController ; 
// use App\Http\Controllers\ManualAppReceiveController ; 
// use App\Http\Controllers\ReadyCenterController ; 
// use App\Http\Controllers\BiometricController ; 
// use App\Http\Controllers\FormFillController ; 
// use App\Http\Controllers\ReadyDelDeleteController; 
// use App\Http\Controllers\CenterDeliveryController ; 
// use App\Http\Controllers\Send2MOFAController ; 
// use App\Http\Controllers\StageUpdateController ; 
// use App\Http\Controllers\ReadyDelActController ; 
// use App\Http\Controllers\CodeUpdateController ; 
// use App\Http\Controllers\AcceptRejectController ; 
// use App\Http\Controllers\BackupController ; 
// use App\Http\Controllers\VisaDurationController ;
// use App\Http\Controllers\MoneyReceiptController ;
// use App\Http\Controllers\CurrencyRateController ; 
// use App\Http\Controllers\EntryTypeController ; 
// use App\Http\Controllers\TokenOverrideController ; 
// use App\Http\Controllers\Forms4mHCIController ; 
// use App\Http\Controllers\PortReceiveController ;
// use App\Http\Controllers\DigitizationScanController ; 
// use App\Http\Controllers\WebfileEditController ; 
// use App\Http\Controllers\AptOverrideController ;
// use App\Http\Controllers\ApprovalController ;  
// use App\Http\Controllers\AptListImportController ;
// use App\Http\Controllers\AdjustUndeliveredController ; 
// use App\Http\Controllers\StickerController ;

// use App\Http\Controllers\RegionController ;
// use App\Http\Controllers\CenterController ;
// use App\Http\Controllers\ServiceController ;
// use App\Http\Controllers\CounterController ;

use App\Http\Controllers\HolidayController ;
// use App\Http\Controllers\VisaTypeController ;
// use App\Http\Controllers\StickerTypeController ; 
// use App\Http\Controllers\PortNameController ;
// use App\Http\Controllers\RejectReasonController ;
// use App\Http\Controllers\CorrectionTypeController ;


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
use App\Events\CardScanned;


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


Route::get('/test-card', function () {
    event(new CardScanned('TEST123', '21001'));

    return 'Card event sent';
});


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

    Route::middleware('permission:can_view')->group(function () {
        Route::get('/qc-entry', [QCentryController::class, 'index'])->name('qc-entry.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/qc-entry', [QCentryController::class, 'store'])->name('qc-entry.store');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/qc-entry/{id}', [QCentryController::class, 'destroy'])->name('qc-entry.destroy');
    });

    Route::get('/check-qc-card', [QCentryController::class, 'checkQCcard'])->name('check-qc-card');
//devices management
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

//sewing machine November 10, 2026
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/sewingMachine', [SewingMachineController::class, 'index'])->name('sewingMachine.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/sewingMachine', [SewingMachineController::class, 'store'])->name('sewingMachine.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/sewingMachine/{device}', [SewingMachineController::class, 'update'])->name('sewingMachine.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/sewingMachine/{device}',[SewingMachineController::class, 'destroy'])->name('sewingMachine.destroy');
    });

//machine  brand
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/machineBrand', [MachineBrandController::class, 'index'])->name('machineBrand.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/machineBrand', [MachineBrandController::class, 'store'])->name('machineBrand.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/machineBrand/{device}', [MachineBrandController::class, 'update'])->name('machineBrand.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/machineBrand/{device}',[MachineBrandController::class, 'destroy'])->name('machineBrand.destroy');
    });

//machine type
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/machineType', [MachineTypeController::class, 'index'])->name('machineType.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/machineType', [MachineTypeController::class, 'store'])->name('machineType.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/machineType/{device}', [MachineTypeController::class, 'update'])->name('machineType.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/machineType/{device}',[MachineTypeController::class, 'destroy'])->name('machineType.destroy');
    });


//    //Operator management
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/operators', [OperatorController::class, 'store'])->name('operators.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/operators/{device}', [OperatorController::class, 'update'])->name('operators.update');
        // Route::get('/device-service/{device}', [OperatorController::class, 'DevSvc'])->name('orders.assign');
        // Route::put('/deviceSvcUpdate',  [OperatorController::class, 'updateAssign'])->name('orders.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/operators/{device}',[OperatorController::class, 'destroy'])->name('operators.destroy');
    });


//operator Designation
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/designation', [EmployeeDesignationController::class, 'index'])->name('designation.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/designation', [EmployeeDesignationController::class, 'store'])->name('designation.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/designation/{device}', [EmployeeDesignationController::class, 'update'])->name('designation.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/designation/{device}',[EmployeeDesignationController::class, 'destroy'])->name('designation.destroy');
    });

//Designation Group
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/desginationType', [EmpDesignationTypeController::class, 'index'])->name('desginationType.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/desginationType', [EmpDesignationTypeController::class, 'store'])->name('desginationType.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/desginationType/{device}', [EmpDesignationTypeController::class, 'update'])->name('desginationType.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/desginationType/{device}',[EmpDesignationTypeController::class, 'destroy'])->name('desginationType.destroy');
    });

//operator Grade
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/empgrade', [EmployeeGradeController::class, 'index'])->name('empgrade.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/empgrade', [EmployeeGradeController::class, 'store'])->name('empgrade.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/empgrade/{device}', [EmployeeGradeController::class, 'update'])->name('empgrade.update');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/empgrade/{device}',[EmployeeGradeController::class, 'destroy'])->name('empgrade.destroy');
    });
//
    //Operation Bank
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/operationBank', [OperationBankController::class, 'index'])->name('operationBank.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/operationBank', [OperationBankController::class, 'store'])->name('operationBank.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/operationBank/{device}', [OperationBankController::class, 'update'])->name('operationBank.update');
        // Route::get('/device-service/{device}', [OperationBankController::class, 'DevSvc'])->name('orders.assign');
        // Route::put('/deviceSvcUpdate',  [OperationBankController::class, 'updateAssign'])->name('orders.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/operationBank/{device}',[OperationBankController::class, 'destroy'])->name('operationBank.destroy');
    });

    //Order management
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/orders', [OrderNoController::class, 'index'])->name('orders.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/orders', [OrderNoController::class, 'store'])->name('orders.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/orders/{device}', [OrderNoController::class, 'update'])->name('orders.update');
        // Route::get('/device-service/{device}', [OrderNoController::class, 'DevSvc'])->name('orders.assign');
        // Route::put('/deviceSvcUpdate',  [OrderNoController::class, 'updateAssign'])->name('orders.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/orders/{device}',[OrderNoController::class, 'destroy'])->name('orders.destroy');
    });

//OperationList
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/operationList', [OperationListController::class, 'index'])->name('operationList.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/operationList', [OperationListController::class, 'store'])->name('operationList.store');
    });
    Route::middleware('permission:can_edit')->group(function () {
        Route::put('/operationList/{device}', [OperationListController::class, 'update'])->name('operationList.update');
        // Route::get('/device-service/{device}', [OrderNoController::class, 'DevSvc'])->name('orders.assign');
        // Route::put('/deviceSvcUpdate',  [OrderNoController::class, 'updateAssign'])->name('orders.updateAssign');
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/operationList/{device}',[OperationListController::class, 'destroy'])->name('operationList.destroy');
    });

    Route::get('/operation-list/load/{orderCode}', [OperationListController::class, 'loadOperations'])->name('operationList.load');
//QR Stricker
   Route::middleware('permission:can_view')->group(function () {
        Route::get('/print-barcode-sticker', [StickerPrintController::class, 'index'])->name('barcode-sticker.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/print-sticker-barcode', [StickerPrintController::class, 'store'])->name('barcode-sticker.store');
    });

//Floor Management 
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/floor-list', [FloorController::class, 'index'])->name('floor-list.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/floor-list', [FloorController::class, 'store'])->name('floor-list.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/floor-list/{device}', [FloorController::class, 'update'])->name('floor-list.update');
   
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/floor-list/{device}',[FloorController::class, 'destroy'])->name('floor-list.destroy');
    });

    //Line Management
        Route::middleware('permission:can_view')->group(function () {
        Route::get('/line-list', [LineController::class, 'index'])->name('line-list.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/line-list', [LineController::class, 'store'])->name('line-list.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/line-list/{device}', [LineController::class, 'update'])->name('line-list.update');
  
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/line-list/{device}',[LineController::class, 'destroy'])->name('line-list.destroy');
    });
//lineQC Terminals 
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/lineQC-terminal', [LineQCController::class, 'index'])->name('lineQC-terminal.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/lineQC-terminal', [LineQCController::class, 'store'])->name('lineQC-terminal.store');
    });
   Route::middleware('permission:can_edit')->group(function () {
        Route::put('/lineQC-terminal/{device}', [LineQCController::class, 'update'])->name('lineQC-terminal.update');
   
    });
    Route::middleware('permission:can_delete')->group(function () {
        Route::delete('/lineQC-terminal/{device}',[LineQCController::class, 'destroy'])->name('lineQC-terminal.destroy');
    });




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


    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/apt-override', [AptOverrideController::class, 'index'])->name('apt-override.index');
    // });
    // Route::middleware('permission:can_create')->group(function () {
    //     Route::post('/apt-override', [AptOverrideController::class, 'store'])->name('apt-override.store');
    // });


    //Override Approve
    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/AdminApproval', [ApprovalController::class, 'index'])->name('AdminApproval.index');
    // });
    // Route::middleware('permission:can_create')->group(function () {
    //     Route::post('/AdminApproval', [ApprovalController::class, 'store'])->name('AdminApproval.store');
    // });
    // Route::middleware('permission:can_delete')->group(function () {
    //     Route::delete('/AdminApproval/{id}', [ApprovalController::class, 'destroy'])->name('AdminApproval.destroy');
    // });


    //SMS Controller
    Route::middleware('permission:can_view')->group(function () {
        Route::get('/send-sms', [SmsController::class, 'index'])->name('send-sms.index');
    });
    Route::middleware('permission:can_create')->group(function () {
        Route::post('/send-sms', [SmsController::class, 'store'])->name('send-sms.store');
    });


  //ADJUST UNDELIVERED PASSPORT
   // Route::middleware('permission:can_view')->group(function () {
   //      Route::get('/adjust-undelivered', [AdjustUndeliveredController::class, 'index'])->name('adjust-undelivered.index');
   //  });
   //  Route::middleware('permission:can_create')->group(function () {
   //      Route::post('/adjust-undelivered', [AdjustUndeliveredController::class, 'store'])->name('adjust-undelivered.store');
   //  });

   //  Route::post('/adjust-undelivered-sort', [AdjustUndeliveredController::class, 'sortFiles'])->name('adjust-undelivered.sort');

   //  Route::post('/adjust-undelivered-pushexcel', [AdjustUndeliveredController::class, 'pushExcel'])->name('push.excel');
   //  Route::post('/adjust-undelivered-pushdb', [AdjustUndeliveredController::class, 'pushDb'])->name('push.db');



  //APPLICANT LIST IMPORT
   // Route::middleware('permission:can_view')->group(function () {
   //      Route::get('/applicant_list_import', [AptListImportController::class, 'index'])->name('applicant_list_import.index');
   //  });
   //  Route::middleware('permission:can_create')->group(function () {
   //      Route::post('/applicant_list_import', [AptListImportController::class, 'store'])->name('applicant_list_import.store');
   //  });


 





    // Route::get('/get-users-by-center/{centerId}', [AppReceiveController::class, 'getUsersByCenter'])->name('get.UsersByCenter');
    // Route::get('/currq/{svctypeId}', [AppReceiveController::class, 'getCurrQ'])->name('get.currq');
    
    // // Route::get('/defq/{svctypeId}', [AppReceiveController::class, 'getDefQ'])->name('get.defq');

    // Route::post('/check-code-webfile', [AppReceiveController::class, 'checkCodeweb'])->name('check.code');
    // Route::post('/check-webfile-payment', [AppReceiveController::class, 'checkwebpay'])->name('check.webpay');
    // Route::post('/check-sticker-barcode', [AppReceiveController::class, 'checkbarcodeSticker'])->name('check.barcode');
    // Route::post('/check-passport', [AppReceiveController::class, 'checkpassport'])->name('check.passport');
    // Route::post('/update-tdd', [AppReceiveController::class, 'updatetdd'])->name('update.token');

    // Route::post('/call-latest-token', [CallTokenController::class, 'callLatestToken'])->name('call-latest.token');
    // Route::post('/defer-latest-token', [CallTokenController::class, 'deferLatestToken'])->name('defer-latest.token');
    // Route::post('/set-counter-login', [AppReceiveController::class, 'counterLogin'])->name('counter.login');

    // Route::post('/save-optype', [AppReceiveController::class, 'saveOpType'])->name('optype.save');
    // Route::post('/save-visatype', [AppReceiveController::class, 'saveVisaType'])->name('visa.save');
    // Route::post('/save-stickertype', [AppReceiveController::class, 'saveStickerType'])->name('sticker.save');

    // Route::post('/send-otp', [SmsController::class, 'sendotp'])->name('send.otp');
    // Route::post('/check-otp', [SmsController::class, 'checkotp'])->name('check.otp');
    
    // Route::post('/save-dropdown-selection', function (\Illuminate\Http\Request $request){
    //     session(['dropdown_'.$request->key => $request->value]);
    //     return response()->json(['status' => 'saved']);
    // });

    // Route::post('/applications/reject', [AppReceiveController::class, 'reject'])->name('applications.reject');


    // Route::middleware('permission:can_view')->group(function () {
    //     Route::get('/searchWebfile', [SearchDataController::class, 'index'])->name('searchWebfile.index');
    // });
    // Route::middleware('permission:can_view')->group(function () {
    //        Route::post('/searchWebfile', [SearchDataController::class, 'search'])->name('searchWebfile.search');
    // });
    // Route::get('/searchContact', [SearchDataController::class, 'searchContact'])->name('application.searchcontact');



    // Route::middleware('permission:can_create')->group(function () {
    //     Route::get('/admin/counter-payment', [CounterPaymentController::class, 'create'])->name('counterPayment.create');
    // });
     // Route::middleware('permission:can_create')->group(function () {
     //    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
     //    Route::get('/backup/download', [BackupController::class, 'store'])->name('backup.store');
     // });
	// IVAC Date 26-06-2025


 
	
	// Route::middleware('permission:can_view')->group(function () {
 //        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
 //    });
 //    Route::middleware('permission:can_create')->group(function () {
 //        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
 //    });
 //   Route::middleware('permission:can_edit')->group(function () {
 //        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
 //    });
 //    Route::middleware('permission:can_delete')->group(function () {
 //        Route::delete('/services/{service}',[ServiceController::class, 'destroy'])->name('services.destroy');
 //    });
	
	
	// Route::middleware('permission:can_view')->group(function () {
 //        Route::get('/counters', [CounterController::class, 'index'])->name('counters.index');
 //    });
 //    Route::middleware('permission:can_create')->group(function () {
 //        Route::post('/counters', [CounterController::class, 'store'])->name('counters.store');
 //    });
 //    Route::middleware('permission:can_edit')->group(function () {
 //        Route::put('/counters/{counter}', [CounterController::class, 'update'])->name('counters.update');
 //        Route::get('/counters-svc/{counter}', [CounterController::class, 'CounterService'])->name('counters.assign');
 //        Route::put('/counterServiceUpdate',  [CounterController::class, 'updateAssign'])->name('counters.updateAssign');
 //    });
 //    Route::middleware('permission:can_delete')->group(function () {
 //        Route::delete('/counters/{counter}',[CounterController::class, 'destroy'])->name('counters.destroy');
 //    });
 //    Route::post('/enable-countercall/{counter}', [CounterController::class, 'enablecall'])
 //    ->name('countercall.enable');
	


	
	
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


 

    Route::middleware('permission:can_create')->group(function () {
        Route::get('/admin/bulk-sms', [BulkSmsController::class, 'create'])->name('bulkSMS.create');
    });




});
 
Route::get('/get-ranks-frontend/{rankTypeId}', [HomeController::class, 'getRank'])
     ->name('get-ranks.frontend');



Route::get('/permission-denied', function () {
    return view('errors.403');
})->name('permission.denied');

require __DIR__.'/auth.php';
