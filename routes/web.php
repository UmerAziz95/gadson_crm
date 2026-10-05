<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;


use Illuminate\Support\Facades\Route;

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
    return redirect('/manager/login');
});
Route::get('manager/forgotpassword', [ManagerController::class, 'forgotpassword'])->name('manager.forgotpassword');
Route::post('manager/forgetpasswordemailvalidate', [ManagerController::class, 'forgot_password_validate_email'])->name('manager.forgetpasswordemailvalidate');
Route::post('manager/verifyotp', [ManagerController::class, 'verify_otp'])->name('manager.verifyotp');
Route::post('manager/resetpassword', [ManagerController::class, 'reset_password'])->name('manager.resetpassword');

Route::get('admin/forgotpassword', [AdminController::class, 'forgotpassword'])->name('admin.forgotpassword');
Route::post('admin/forgetpasswordemailvalidate', [AdminController::class, 'forgot_password_validate_email'])->name('admin.forgetpasswordemailvalidate');
Route::post('admin/verifyotp', [AdminController::class, 'verify_otp'])->name('admin.verifyotp');
Route::post('admin/resetpassword', [AdminController::class, 'reset_password'])->name('admin.resetpassword');

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', [AdminController::class, 'login'])->name('/');
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/loginSubmit', [AdminController::class, 'loginSubmit'])->name('admin.loginSubmit');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');


    Route::group(['middleware' => ['AdminAuth']], function () {

        /************** PAGE ROUTES ******************/
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/makegraph', [AdminController::class, 'makeanalyticsgraph'])->name('admin.makegraph');
        Route::get('/subscription', [AdminController::class, 'subscription'])->name('admin.subscription');
        Route::get('/managers', [AdminController::class, 'managers'])->name('admin.managers');
        Route::post('/addManager', [AdminController::class, 'add_managers'])->name('admin.addManager');
        Route::get('/buildings', [AdminController::class, 'buildings'])->name('admin.buildings');
        Route::get('/addBuilding', [AdminController::class, 'add_building'])->name('admin.addBuilding');
        Route::post('/storeBuilding', [AdminController::class, 'store_building'])->name('admin.storeBuilding');
        Route::get('/buildings/{id}/edit', [AdminController::class, 'edit_building'])->name('admin.building.edit');
        Route::post('/updateBuilding', [AdminController::class, 'update_building'])->name('admin.updateBuilding');
        
        Route::get('/appartments', [AdminController::class, 'appartments'])->name('admin.appartments');
        Route::get('/addAppartment', [AdminController::class, 'add_appartment'])->name('admin.addAppartment');
        Route::get('/appartments/{id}/edit', [AdminController::class, 'edit_appartment'])->name('admin.appartment.edit');
        
        Route::get('/tasks', [AdminController::class, 'assigned_tasks'])->name('admin.assigned_tasks');
        Route::get('/tasks/add', [AdminController::class, 'add_task_view'])->name('admin.add_task');
        Route::get('/tasks/{id}/edit', [AdminController::class, 'edit_task'])->name('admin.task.edit');
        Route::get('/profile', [AdminController::class, 'edit_profile'])->name('admin.profile');
        
        
        /************** AJAX ROUTES ******************/
        // Route::post('/ajax', [AdminController::class, 'ajax'])->name('admin.ajax');
        
        Route::get('/getmanagers', [AdminController::class, 'get_managers_list'])->name('admin.getmanagers');
        Route::post('/deletemanager', [AdminController::class, 'delete_manager'])->name('admin.deletemanager');
        Route::post('/changestatus', [AdminController::class, 'change_status'])->name('admin.changestatus');
        Route::post('/getmanagerdata', [AdminController::class, 'get_manager_data'])->name('admin.getmanagerdata');
        Route::post('/updateManager', [AdminController::class, 'update_manager'])->name('admin.updateManager');
        
        Route::get('/getbuildings', [AdminController::class, 'get_buildings_list'])->name('admin.getbuildings');
        Route::post('/deletebuilding', [AdminController::class, 'delete_building'])->name('admin.deletebuilding');
        Route::post('/getStates', [AdminController::class, 'get_states_list'])->name('admin.getStates');
        Route::post('/getCities', [AdminController::class, 'get_cities_list'])->name('admin.getCities');
        Route::get('/getappartments', [AdminController::class, 'get_appartments_list'])->name('admin.getappartments');
        Route::post('/storeAppartment', [AdminController::class, 'store_appartment'])->name('admin.storeAppartment');
        Route::post('/updateAppartment', [AdminController::class, 'update_appartment'])->name('admin.updateAppartment');
        Route::post('/deleteappartment', [AdminController::class, 'delete_appartment'])->name('admin.deleteappartment');
        
        
        Route::post('/getAppartmentsList', [AdminController::class, 'get_appartment_list'])->name('admin.getAppartmentsList');
        Route::post('/storeTask', [AdminController::class, 'store_task'])->name('admin.storeTask');
        Route::get('/getTasksList', [AdminController::class, 'get_tasks_list'])->name('admin.getTasksList');
        Route::post('/updateTask', [AdminController::class, 'update_task'])->name('admin.updateTask');
        Route::post('/deleteTask', [AdminController::class, 'delete_task'])->name('admin.deleteTask');
        // getTaskDetails
        Route::post('/getTaskDetails', [AdminController::class, 'get_task_details'])->name('admin.getTaskDetails');
        Route::post('/gettimelinesdetail', [ManagerController::class, 'get_time_line_details'])->name('manager.gettimelinesdetail');
        Route::post('/getfilteredtasks', [AdminController::class, 'get_filtered_tasks'])->name('admin.getfilteredtasks');
        Route::post('/getfilteredcompletedtasks', [AdminController::class, 'get_filtered_done_tasks'])->name('admin.getfilteredcompletedtasks');
        Route::get('/getUserProfile', [AdminController::class, 'get_profile_data'])->name('admin.getUserProfile');
        Route::post('/updateprofile', [AdminController::class, 'update_profile'])->name('admin.updateprofile');
        
    });
});

Route::group(['prefix' => 'manager'], function () {
    Route::get('/', [ManagerController::class, 'login'])->name('/');
    Route::get('/login', [ManagerController::class, 'login'])->name('manager.login');
    Route::post('/loginSubmit', [ManagerController::class, 'loginSubmit'])->name('manager.loginSubmit');
    Route::get('/logout', [ManagerController::class, 'logout'])->name('manager.logout');
    
Route::group(['middleware' => ['ManagerAuth']], function () {

    // page routes  
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::post('/changedefaultpassword', [ManagerController::class, 'changeDefaultPassword'])->name('manager.changedefaultpassword');
    Route::get('/tasks', [ManagerController::class, 'assigned_tasks'])->name('manager.assigned_tasks');
    Route::get('/tasks/add', [ManagerController::class, 'add_task_view'])->name('manager.add_task');
    Route::get('/tasks/{id}/edit', [ManagerController::class, 'edit_task'])->name('manager.task.edit');
    Route::get('/profile', [ManagerController::class, 'edit_profile'])->name('manager.profile');
    



    // ajax routes 

    Route::get('/getTasksList', [ManagerController::class, 'get_tasks_list'])->name('manager.getTasksList');
    Route::get('/getDoneTasksList', [ManagerController::class, 'get_done_tasks_list'])->name('manager.getDoneTasksList');
    Route::get('/getCancelledTasksList', [ManagerController::class, 'get_cancelled_tasks_list'])->name('manager.getCancelledTasksList');
    Route::post('/changeTaskStatus', [ManagerController::class, 'change_task_status'])->name('manager.changeTaskStatus');
    Route::post('/changeDocumentStatus', [ManagerController::class, 'change_document_status'])->name('manager.changeDocumentStatus');
    Route::post('/addTaskTodoList', [ManagerController::class, 'add_to_do_list'])->name('manager.addTaskTodoList');
    Route::post('/gettimelinesdetail', [ManagerController::class, 'get_time_line_details'])->name('manager.gettimelinesdetail');
    Route::post('/getfilteredtasks', [ManagerController::class, 'get_filtered_tasks'])->name('manager.getfilteredtasks');
    Route::post('/getfilteredcompletedtasks', [ManagerController::class, 'get_filtered_done_tasks'])->name('manager.getfilteredcompletedtasks');
    Route::post('/getTaskTodoList', [ManagerController::class, 'get_task_todoList'])->name('manager.getTaskTodoList');
    Route::get('/getUserProfile', [ManagerController::class, 'get_profile_data'])->name('manager.getUserProfile');
    Route::post('/updateprofile', [ManagerController::class, 'update_profile'])->name('manager.updateprofile');
    Route::post('/getAppartmentsList', [ManagerController::class, 'get_appartment_list'])->name('manager.getAppartmentsList');
});
});