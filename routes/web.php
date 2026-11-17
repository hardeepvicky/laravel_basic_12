<?php

use App\Helpers\EmailHelper;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerifyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Backend\AppMenuController;
use App\Http\Controllers\Backend\AutoSerialPatternController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DeveloperController;
use App\Http\Controllers\Backend\DocumentCategoryController;
use App\Http\Controllers\Backend\DocumentFileController;
use App\Http\Controllers\Backend\DropDownValueController;
use App\Http\Controllers\Backend\EmailTemplateController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PublicController;
use App\Http\Controllers\Backend\RecurringTaskController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SampleController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\TaskController;
use App\Http\Controllers\Backend\TypeController;
use App\Http\Controllers\Backend\UomController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WhatsappTemplateController;
use App\Http\Controllers\HomeController;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mailjet\Resources;


Route::get("/", function () {
    return redirect("/home");
});

Route::any("interakt-hook", function(Request $request){

    $data = $request->all();
    Log::channel("hooks")->debug("interakt Hook", $data);

    header('HTTP/1.1 200 OK');
});

Route::any("mailjet-webhook", function(Request $request){

    $data = $request->all();

    Log::channel("hooks")->debug("mailjet Hook", $data);

    //MailjetWebhook::create($data);

    header('HTTP/1.1 200 OK');
});

// Route::get('/', [FrontendController::class, "index"]);
Route::get('/home', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/signin', [LoginController::class, 'sign_in_form'])->name('sign_in');
Route::get('/login/auto', [LoginController::class, 'auto_login'])->name('auto.login');

Route::post('/check_before_login', [LoginController::class, 'check_before_login'])->name('check_before_login');

Route::get('/theme', [HomeController::class, 'theme']);
Route::get('/developer-components', [HomeController::class, 'developer_components']);
Route::get('/test', [HomeController::class, 'test']);

$controllerClass = OtpVerifyController::class;
Route::get('otp_verify/{uid}', [$controllerClass, 'index'])->name("otp_verify");
Route::get('send_email_user_otp/{uid}', [$controllerClass, 'send_email_user_otp'])->name("send_email_user_otp");
Route::get('send_whatsapp_user_otp/{uid}', [$controllerClass, 'send_whatsapp_user_otp'])->name("send_whatsapp_user_otp");
Route::post('ajax_otp_verify', [$controllerClass, 'ajax_otp_verify']);

Route::any('/create_password/{uid}', [RegisterController::class, 'create_password'])->name("create_password");
Route::any('/forgot_password', [RegisterController::class, 'forgot_password'])->name("forgot_password");

// Route to redirect to Google's OAuth page
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');

// Route to handle the callback from Google
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::get('register', [RegisterController::class, 'signup_form'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register');

Route::post("signup_with_third_party/save_data_before_signup", [RegisterController::class, 'save_data_before_signup'])->name("signup.with.third.party.save.data.before.signup");
Route::post("signin_with_third_party/save_data_before_signin", [RegisterController::class, 'save_data_before_signin'])->name("signin.with.third.party.save.data.before.signin");

Route::group(['prefix' => 'public', 'as' => 'public.'], function () {
    Route::post('ajax_upload', [PublicController::class, 'ajax_upload']);
    Route::post('ajax_upload_base64', [PublicController::class, 'ajax_upload_base64']);
});

Route::get("/get_file_url", [PublicController::class, "get_file_url"])->name("get_file_url");

$route_prefix = "sample";
$controllerClass = SampleController::class;
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

    $name = "item";
    Route::get($name, [$controllerClass, $name])->name($name);
});

Route::get("import_log_status/{import_log_id}", [HomeController::class, "import_log_status"])->name("import_log_status");

Route::group(['middleware' => ['auth', 'check_auth', 'role_permission']], function () {

    $route_prefix = "dashboard";
    $controllerClass = DashboardController::class;
    Route::get($route_prefix, [DashboardController::class, 'index'])->name($route_prefix);

    $route_prefix = "user";
    $controllerClass = UserController::class;
    Route::resource($route_prefix, $controllerClass)->except("show");
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name, [$controllerClass, $name])->name($name);

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

    $route_prefix = "type";
    $controllerClass = TypeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_code_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "drop-down-value";
    $controllerClass = DropDownValueController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "task";
    $controllerClass = TaskController::class;
    Route::resource($route_prefix, $controllerClass);
    $name = "today";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "today_my_task";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "today_other_task";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "status_confirm";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "status_in_progress";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "status_complete";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "status_verified";
        Route::put($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "history";
        Route::any($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "history_destroy";
        Route::delete($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{task_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_count";
        Route::get($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, $name])->name($name);

            $name = "csv_history";
            Route::get($name, [$controllerClass, $name])->name($name);
        });
    });

    $route_prefix = "recurring-task";
    $controllerClass = RecurringTaskController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_count";
        Route::get($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "app-menu";
    $controllerClass = AppMenuController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });


    $route_prefix = "document-category";
    $controllerClass = DocumentCategoryController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "document-file";
    $controllerClass = DocumentFileController::class;
    Route::resource($route_prefix, $controllerClass);

    $name = "archive_index";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "add_via_upload";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "archive";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "unarchive";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "uom";
    $controllerClass = UomController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {

        $name = "index";
        Route::get($name, [NotificationController::class, $name])->name($name);

        $name = "my";
        Route::any($name, [NotificationController::class, $name])->name($name);

        $name = "ajax_mark_read";
        Route::get($name, [NotificationController::class, $name])->name($name);
    });


    $route_prefix = "whatsapp-template";
    $controllerClass = WhatsappTemplateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    $route_prefix = "email-template";
    $controllerClass = EmailTemplateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    $route_prefix = "auto-serial-pattern";
    $controllerClass = AutoSerialPatternController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function () {

        $name = "index";
        Route::get($name, [PermissionController::class, $name])->name($name);

        $name = "assign";
        Route::any($name, [PermissionController::class, $name])->name($name);

        $name = "assign_to_many";
        Route::any($name, [PermissionController::class, $name])->name($name);

        $name = "ajax_get_permissions";
        Route::post($name, [PermissionController::class, $name])->name($name);

        $name = "ajax_delete";
        Route::post($name, [PermissionController::class, $name])->name($name);

        $name = "ajax_request_access";
        Route::any($name . "/{route_name}", [PermissionController::class, $name])->name($name);
    });

    $controller_prefix = "setting";
    $controllerClass = SettingsController::class;
    $name = "general";
    Route::any($controller_prefix . "/" . $name, [$controllerClass, $name])->name($controller_prefix . "." . $name);
    
    Route::group(['prefix' => "developer", 'as' => "developer."], function () {

        $name = "email_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "whatsapp_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "permission_change_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "import_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "sql_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "ajax_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "command_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $route_prefix = "laravel_logs";
        Route::get($route_prefix . "/{file_name?}", [DeveloperController::class, "laravel_logs"])->name($route_prefix);

        $name = "laravel_routes_index";
        Route::get($name, [DeveloperController::class, $name])->name($name);
    });
});

/* -------------------------------------------------------------------------*/
Route::get("/test/whatsapp", function () {

    $request_params = [
        "countryCode" => "+91",
        "phoneNumber" => ["9814040490"],
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
    $EmailHelper->setSandbox(false);
    $response = $EmailHelper->send();
    dump($response);
});

Route::get("/test/mongo", function () {
    $records = EmailLog::limit(100)->get()->toArray();
    d($records);
});

Route::get('/phpinfo', function () {
    phpinfo();
});