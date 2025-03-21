<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\CityController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\DeveloperController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PublicController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\Backend\TypeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\HomeController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use App\Models\City;


Route::get('/', function () {
    return redirect()->route("home");
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Auth::routes();

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::get('/theme', [HomeController::class, 'theme']);
Route::get('/developer-components', [HomeController::class, 'developer_components']);
Route::get('/test', [HomeController::class, 'test']);


Route::group(['prefix' => 'public', 'as' => 'public.'], function () {
    Route::post('ajax_upload', [PublicController::class, 'ajax_upload']);
    Route::post('ajax_upload_base64', [PublicController::class, 'ajax_upload_base64']);
});

Route::group(['middleware' => ['auth']], function () {

    $name = "dashboard";
    Route::get($name, [DashboardController::class, 'index'])->name($name);

    $route_prefix = "user";
    $controllerClass = UserController::class;
    Route::resource($route_prefix, $controllerClass)->except("show");
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        Route::get("my-profile", [$controllerClass, 'my_profile'])->name("my.profile");

        Route::any("change-password", [$controllerClass, 'change_password'])->name("change.password");
    });

    $route_prefix = "role";
    $controllerClass = RoleController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
        
        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

// state
$route_prefix = "state";
$controllerClass = StateController::class;
Route::resource($route_prefix, $controllerClass);
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
    
});

// City
$route_prefix = "city";
$controllerClass = CityController::class;
Route::resource($route_prefix, $controllerClass);
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
    
});


// Department
    $route_prefix = "department";
    $controllerClass = DepartmentController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
        
        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Designation
    $route_prefix = "designation";
    $controllerClass = DesignationController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
        
        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Type
    $route_prefix = "type";
    $controllerClass = TypeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
        
        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Employee
    $route_prefix = "employee";
    $controllerClass = EmployeeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use($controllerClass) {
        
        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function () {

        $name = "index";
        Route::get($name, [PermissionController::class, $name])->name($name);

        $name = "assign";
        Route::any($name, [PermissionController::class, $name])->name($name);

        $name = "assign_to_many";
        Route::any($name, [PermissionController::class, $name])->name($name);

        $name = "ajax_get_permissions";
        Route::get("$name/{id}", [PermissionController::class, $name])->name($name);

        $name = "ajax_delete";
        Route::post($name, [PermissionController::class, $name])->name($name);

        $name = "ajax_request_access";
        Route::any($name . "/{route_name}", [PermissionController::class, $name])->name($name);
    });

    Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {
        $name = "email";
        Route::get($name, [DeveloperController::class, $name])->name($name);
    });

    Route::group(['prefix' => "developer", 'as' => "developer."], function () {

        $name = "sql_log";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "laravel_routes_index";
        Route::get($name, [DeveloperController::class, $name])->name($name);
    });
   
    
});
Route::get('verify-otp/{email}',[UserController::class,'otp_verified_view'])->name('verify.otp')->middleware('auth');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// show cities based on selected state //
Route::get('/cities/{stateId}', function ($stateId) {
    
    $cities = City::where('state_id', $stateId)->pluck('name', 'id');
    
    return response()->json(['cities' => $cities]);
});
// show cities based on selected state //