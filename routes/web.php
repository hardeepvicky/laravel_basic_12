<?php

use App\Helpers\EmailHelper;
use App\Helpers\Util;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerifyController;
use App\Http\Controllers\Auth\RegisterController;
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
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\Backend\TypeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\HomeController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use App\Models\City;
use Mailjet\Resources;

Route::get('/', function () {
    return redirect()->route("home");
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Auth::routes();

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('/check_before_login', [LoginController::class, 'check_before_login'])->name('check_before_login');


Route::get('/theme', [HomeController::class, 'theme']);
Route::get('/developer-components', [HomeController::class, 'developer_components']);
Route::get('/test', [HomeController::class, 'test']);

Route::get('/otp_verify/{uid}', [OtpVerifyController::class, 'index'])->name("otp_verify");
Route::get('/send_email_user_otp/{uid}', [OtpVerifyController::class, 'send_email_user_otp'])->name("send_email_user_otp");
Route::get('/send_whatsapp_user_otp/{uid}', [OtpVerifyController::class, 'send_whatsapp_user_otp'])->name("send_whatsapp_user_otp");

Route::get('/ajax_send_email_otp/{id}', [RegisterController::class, 'ajax_send_email_otp']);
Route::get('/email_otp_verify/{uid}', [RegisterController::class, 'email_otp_verify'])->name("email_otp_verify");
Route::post('/ajax_email_otp_verify', [RegisterController::class, 'ajax_email_otp_verify']);

Route::any('/create_password/{uid}', [RegisterController::class, 'create_password'])->name("create_password");
Route::any('/forgot_password', [RegisterController::class, 'forgot_password'])->name("forgot_password");

// Route to redirect to Google's OAuth page
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');

// Route to handle the callback from Google
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');


Route::group(['prefix' => 'public', 'as' => 'public.'], function () {
    Route::post('ajax_upload', [PublicController::class, 'ajax_upload']);
    Route::post('ajax_upload_base64', [PublicController::class, 'ajax_upload_base64']);
});

Route::group(['middleware' => ['auth', 'role_permission']], function () {

    $name = "dashboard";
    Route::get($name, [DashboardController::class, 'index'])->name($name);

    $route_prefix = "user";
    $controllerClass = UserController::class;
    Route::resource($route_prefix, $controllerClass)->except("show");
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

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
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // state
    $route_prefix = "state";
    $controllerClass = StateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {});

    // City
    $route_prefix = "city";
    $controllerClass = CityController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_get_list";
        Route::get($name . "/{state_id}", [$controllerClass, $name])->name($name);
    });


    // Department
    $route_prefix = "department";
    $controllerClass = DepartmentController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Designation
    $route_prefix = "designation";
    $controllerClass = DesignationController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Type
    $route_prefix = "type";
    $controllerClass = TypeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    // Employee
    $route_prefix = "employee";
    $controllerClass = EmployeeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

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

    $controller_prefix = "setting";
    $controllerClass = SettingsController::class;
    $name = "general";
    Route::any($controller_prefix . "/" . $name, [$controllerClass, $name])->name($controller_prefix . "." . $name);

    Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {
        $name = "email";
        Route::get($name, [DeveloperController::class, $name])->name($name);
    });

    Route::group(['prefix' => "developer", 'as' => "developer."], function () {

        $name = "sql_log";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "laravel_routes_index";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "email_log";
        Route::get($name, [DeveloperController::class, $name])->name($name);
    });
});



/* -------------------------------------------------------------------------*/
Route::get("/test/whatsapp", function () {

    $request_params = [
        "countryCode" => "+91",
        "phoneNumber" => ["7888698831", "9814040490"],
        "type" => "Template",
        "template" => [
            "name" => "otp",
            "languageCode" => "en_GB",
            "bodyValues" => [
                "123456"
            ],
            "buttonValues" => [
                "1" => [
                    "Copy"
                ]
            ]
        ]
    ];

    d($request_params);

    $request_params = json_encode($request_params);

    d($request_params);

    $headers = [
        'Authorization : Basic ' . env("INTERAKT_API_TOKEN"),
        'Content-Type : application/json'
    ];

    d($headers);

    d(json_encode($headers));


    $response = curl_post_request("https://api.interakt.ai/v1/public/message/", $request_params, $headers);


    d($response);
});


Route::get("/test/sms/viop", function () {

    $user_pwd = env('DINSTAR_USER') . ":" . env('DINSTAR_PASSWORD');

    d($user_pwd);

    $api_url = env('DINSTAR_API_URL');

    d($api_url);

    $post = [
        "text" => "Hello World",
        "param" => [
            [
                "number" => "9814040490",
                "user_id" => "1"
            ]
        ],
        "port" => [env("DINSTAR_DEFAULT_PORT"),],
        "encoding" => "unicode"
    ];

    d($post);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => 'unicode',
        CURLOPT_USERPWD => $user_pwd,
        CURLOPT_HTTPAUTH => CURLAUTH_ANY,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($post),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {
        d($response);
        $data = json_decode($response);
        d($data);

        if ($data['error_code'] >= 200 && $data['error_code'] < 300) {
            d("No Error");
        } else if ($data['error_code'] == 400) {
            d("Request format is not valid");
        } else if ($data['error_code'] == 413) {
            d("the count of telephone numbers is more than 32");
        } else if ($data['error_code'] >= 500) {
            d("other errors");
        }
    } else {
        d("No Response");
    }
});

Route::get("/test/sms", function () {
    $mj = new \Mailjet\Client(env("MAILJET_API_KEY"), env("MAILJET_API_SECRET"), true, ['url' => "api.mailjet.com", 'version' => 'v4', 'call' => false]);
    $body = [
        'From' => "MJPilot",
        'To' => "+919814040490",
        'Text' => "Have a nice SMS flight with Mailjet!"
    ];
    $response = $mj->post(Resources::$SmsSend, ['body' => $body]);
    d($response->getData());
    d($response->success());
});

Route::get("/test/email", function () {

    $EmailHelper = new EmailHelper();
    $EmailHelper->setSubject("Testing Email");
    $EmailHelper->setBody("This is test Email");
    $EmailHelper->setType("test");
    $EmailHelper->addTo("hardeepvicky1@gmail.com", "Hardeep Singh");
    $EmailHelper->addCc("hardeep.singh417@gmail.com", "Hardeep Singh");
    $EmailHelper->addAttachment("img/dummy.jpg");
    $response = $EmailHelper->send();
    dump($response);
});
