<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuditAgencyController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductattributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QmSheetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Middleware\PreventBackHistory;



Route::middleware([RedirectIfAuthenticated::class, PreventBackHistory::class])->group(function ()  {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Route::middleware('auth')->group(function () {
//     Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// });



//User Routes
Route::resource('User', UserController::class);
Route::get('/client/list', [UserController::class, 'showClient'])->name('Client.list');

Route::post('/assign-roles', [RoleController::class, 'assignRoles'])->name('roles.assign');
Route::get('dowmload-user-excel', [UserController::class,'excelDownloadUser'])->name('excelDownloadUser');

// Uploads
Route::get('user-upload', [UploadController::class,'userUpload'])->name('userUpload');
Route::get('bulk-deactivate', [UploadController::class,'bulkDeactivate'])->name('bulkDeactivate');
Route::get('download-user', [UploadController::class,'downloadUser'])->name('downloadUser');
Route::post('user-import', [UserController::class,'userImport'])->name('userImport');
Route::post('bulk_user_deactivate', [UploadController::class,'bulk_user_deactivate'])->name('bulk_user_deactivate');

//Branch Controller 
Route::get('/get-regions', [BranchController::class,'getRegions']);
Route::get('/getStates/{id}', [BranchController::class,'getStates']);
Route::get('/getCities/{id}', [BranchController::class,'getCities']);

//Location Routes
Route::get('location/city_view', [LocationController::class,'cityView'])->name('location.city_view');
Route::resource('location', LocationController::class);
Route::post('location/update',[LocationController::class,'update']);

//Product Routes
Route::resource('product', ProductController::class);
Route::resource('productattribute', ProductattributeController::class);

//Audit Cycle
Route::match(['get', 'put'], 'edit-audit-cycle/{id}', [AuditController::class, 'editCycle'])->name('edit-audit-cycle');

Route::any('create-audit-cycle', [AuditController::class,'createCycle'])->name('createCycle');
Route::get('list-audit-cycle', [AuditController::class,'listCycle'])->name('listCycle');
Route::post('/toggle-status', [AuditController::class,'toggleStatus'])->name('toggle.status');

//Agency Controller
Route::resource('agency', AgencyController::class);

//Audit Agency
Route::resource('audit_agency',AuditAgencyController::class);

//QM SHEET
Route::resource('qm_sheet', QmSheetController::class);
Route::get('qm_sheet/{sheet_id}/add_parameter',[QmSheetController::class,'add_parameter']);
Route::get('qm_sheet/{sheet_id}/list_parameter',[QmSheetController::class,'list_parameter']);
Route::get('qm_sheet/{sheet_id}/parameter',[QmSheetController::class,'list_parameter']);
Route::post('qm_sheet/store_parameters',[QmSheetController::class,'store_parameters'])->name('store_parameters');
Route::delete('delete_parameter/{id}',[QmSheetController::class,'delete_parameter'])->name('delete_parameter');
Route::get('parameter/{id}/edit',[QmSheetController::class,'edit_parameter']);
Route::post('update_parameter',[QmSheetController::class,'update_parameter'])->name('update_parameter');
Route::get('delete_sub_parameter/{id}',[QmSheetController::class,'delete_sub_parameter']);

// Protect routes that require authentication
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Route::get('/', function () {
//     return view('welcome');
// });
