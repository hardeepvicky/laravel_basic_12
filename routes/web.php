<?php

use App\Helpers\EmailHelper;
use App\Helpers\Util;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerifyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Backend\AppMenuController;
use App\Http\Controllers\Backend\AutoSerialPatternController;
use App\Http\Controllers\Backend\BinController;
use App\Http\Controllers\Backend\BomController;
use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CityController;
use App\Http\Controllers\Backend\CompetitorController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\CountryController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DealerController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\DeveloperController;
use App\Http\Controllers\Backend\DispatchOrderController;
use App\Http\Controllers\Backend\DistrictController;
use App\Http\Controllers\Backend\DocumentCategoryController;
use App\Http\Controllers\Backend\DocumentFileController;
use App\Http\Controllers\Backend\DropDownValueController;
use App\Http\Controllers\Backend\EmailTemplateController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\EnquiryController as BackendEnquiryController;
use App\Http\Controllers\Backend\ExportCityController;
use App\Http\Controllers\Backend\ExportCompetitorController;
use App\Http\Controllers\Backend\ExportContactController;
use App\Http\Controllers\Backend\ExportCountryController;
use App\Http\Controllers\Backend\ExportCustomerController;
use App\Http\Controllers\Backend\ExportDealerController;
use App\Http\Controllers\Backend\ExportLeadController;
use App\Http\Controllers\Backend\ExportOrderController;
use App\Http\Controllers\Backend\ExportPartyController;
use App\Http\Controllers\Backend\ExportPaymentController;
use App\Http\Controllers\Backend\ExportStateController;
use App\Http\Controllers\Backend\HolidayController;
use App\Http\Controllers\Backend\ItemBrandController;
use App\Http\Controllers\Backend\ItemCategoryController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\ItemSerialController;
use App\Http\Controllers\Backend\LeadController;
use App\Http\Controllers\Backend\MakerController;
use App\Http\Controllers\Backend\ManufactureOrderController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PartyController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProductCategoryController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductionTargetController;
use App\Http\Controllers\Backend\ProductIssueController;
use App\Http\Controllers\Backend\ProductSerialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PublicController;
use App\Http\Controllers\Backend\PurchaseOrderController;
use App\Http\Controllers\Backend\PurchaseOrderPaymentController;
use App\Http\Controllers\Backend\RecordHistoryController;
use App\Http\Controllers\Backend\RecurringTaskController;
use App\Http\Controllers\Backend\RequirementController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SaleOrderController;
use App\Http\Controllers\Backend\SampleController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\ShipOrderController;
use App\Http\Controllers\Backend\SmsTemplateController;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\Backend\StockItemController;
use App\Http\Controllers\Backend\StockProductController;
use App\Http\Controllers\Backend\SubDepartmentController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\TaskController;
use App\Http\Controllers\Backend\TypeController;
use App\Http\Controllers\Backend\UomController;
use App\Http\Controllers\Backend\UserCategoryController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WhatsappTemplateController;
use App\Http\Controllers\Frontend\EnquiryController;
use App\Http\Controllers\Frontend\PublicController as FrontendPublicController;
use App\Http\Controllers\HomeController;
use App\Jobs\Backend\JobSendSmsWelcome;
use App\Models\EmailLog;
use App\Models\MailjetWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Exporter;
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
Route::post('/get_date_time_for_timezone', [HomeController::class, 'get_date_time_for_timezone'])->name('get_date_time_for_timezone');

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

Route::any('unsubscribe_email/{uid}', [FrontendPublicController::class, 'unsubscribe_user_email'])->name("unsubscribe_email");

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

$route_prefix = "enquiry";
$controllerClass = EnquiryController::class;
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

    Route::get("/", [EnquiryController::class, "index"])->name("index");

    Route::post("/store", [EnquiryController::class, "store"])->name("store");

    $name = "ajax_get_state_list";
    Route::any("$name/{country_id}", [$controllerClass, $name])->name($name);

    $name = "ajax_get_city_list";
    Route::any("$name/{state_id}", [$controllerClass, $name])->name($name);
});

$route_prefix = "country";
$controllerClass = CountryController::class;
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

    $name = "ajax_get";
    Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
});

$route_prefix = "state";
$controllerClass = StateController::class;
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

    $name = "ajax_get";
    Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

    $name = "ajax_get_list";
    Route::get($name . "/{country_id}", [$controllerClass, $name])->name($name);
});

$route_prefix = "sample";
$controllerClass = SampleController::class;
Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

    $name = "item";
    Route::get($name, [$controllerClass, $name])->name($name);
});

Route::get("import_log_status/{import_log_id}", [HomeController::class, "import_log_status"])->name("import_log_status");

Route::group(['middleware' => ['auth', 'check_auth', 'role_permission', 'backend_view_render']], function () {

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

    $route_prefix = "country";
    $controllerClass = CountryController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
    });

    $route_prefix = "state";
    $controllerClass = StateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{state_id?}/{district_id?}", [$controllerClass, $name])->name($name);
    });

    $route_prefix = "city";
    $controllerClass = CityController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{state_id?}/{district_id?}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list_with_district";
        Route::get($name . "/{state_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "district";
    $controllerClass = DistrictController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{state_id?}/{district_id?}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "export-country";
    $controllerClass = ExportCountryController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_by_phonecode";
        Route::get($name . "/{phonecode}", [$controllerClass, $name])->name($name);
    });

    $route_prefix = "export-state";
    $controllerClass = ExportStateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{country_id?}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "export-city";
    $controllerClass = ExportCityController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{country_id?}/{state_id?}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "department";
    $controllerClass = DepartmentController::class;
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

    $route_prefix = "sub-department";
    $controllerClass = SubDepartmentController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{department_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "designation";
    $controllerClass = DesignationController::class;
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

    $route_prefix = "user-category";
    $controllerClass = UserCategoryController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

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

    $route_prefix = "employee";
    $controllerClass = EmployeeController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
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

    $route_prefix = "tags";
    $controllerClass = TagsController::class;
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
        $name = "ajax_get_list";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_list_for_header";
        Route::get($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "product-category";
    $controllerClass = ProductCategoryController::class;
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

    $route_prefix = "product";
    $controllerClass = ProductController::class;
    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "gallery";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "preview";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "preview_small";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        Route::get("/", [$controllerClass, "index"])->name("index");

        $name = "create";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "store";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "edit";
        Route::get("/{id}/" . $name . "/{tab?}", [$controllerClass, $name])->name($name);

        $name = "update";
        Route::put("/{id}/{tab?}", [$controllerClass, $name])->name($name);

        $name = "destroy";
        Route::delete("/{id}", [$controllerClass, $name])->name($name);

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "ajax_load_attribute_fields";
        Route::get($name . "/{category_id}/{id?}", [$controllerClass, $name])->name($name);

        $name = "mark_as_main_image";
        Route::get($name . "/{id}/{document_file_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, $name])->name($name);

            $name = "csv_attribute";
            Route::get($name, [$controllerClass, $name])->name($name);

            $name = "csv_specification";
            Route::get($name, [$controllerClass, $name])->name($name);
        });
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

    $route_prefix = "enquiries";
    $controllerClass = EnquiryController::class;
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
        Route::get("/", [BackendEnquiryController::class, "index"])->name("index");
    });

    Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {

        $name = "index";
        Route::get($name, [NotificationController::class, $name])->name($name);

        $name = "my";
        Route::any($name, [NotificationController::class, $name])->name($name);

        $name = "ajax_mark_read";
        Route::get($name, [NotificationController::class, $name])->name($name);
    });

    $route_prefix = "change-history";
    $controllerClass = RecordHistoryController::class;
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
        Route::get("/", [$controllerClass, "index"])->name("index");
        Route::any("change_history_of_record", [$controllerClass, "change_history_of_record"])->name("change_history_of_record");
        Route::get("ajax_get_model_fields/{model_name}", [$controllerClass, "ajax_get_model_fields"])->name("ajax_get_model_fields");
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

    $route_prefix = "sms-template";
    $controllerClass = SmsTemplateController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
    });

    $route_prefix = "party";
    $controllerClass = PartyController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{party_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{party_id}", [$controllerClass, $name])->name($name);

        $name = "transfer";
        Route::post($name . "/{party_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{party_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "dealer";
    $controllerClass = DealerController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{dealer_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{dealer_id}", [$controllerClass, $name])->name($name);

        $name = "transfer";
        Route::post($name . "/{dealer_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{dealer_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "competitor";
    $controllerClass = CompetitorController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });


    $route_prefix = "lead";
    $controllerClass = LeadController::class;
    Route::resource($route_prefix, $controllerClass);

    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);    

    $name = "add_draft";
    Route::any($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "transfer_lead";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "address";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "dispatch";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "snooze";
        Route::post($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $name = "convert_to_customer";
        Route::get($name . "/{lead_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "csv_history";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "customer";
    $controllerClass = CustomerController::class;
    Route::resource($route_prefix, $controllerClass);

    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{customer_id}", [$controllerClass, $name])->name($name);
        
        $name = "history_store";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "dispatch";
        Route::post($name . "/{customer_id}", [$controllerClass, $name])->name($name);

        $name = "snooze";
        Route::post($name . "/{customer_id}", [$controllerClass, $name])->name($name);

        $name = "convert_to_lead";
        Route::get($name . "/{customer_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "item-category";
    $controllerClass = ItemCategoryController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_with_parent";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{category_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "item";
    $controllerClass = ItemController::class;    
    $name = "gallery";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "preview";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "preview_small";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        Route::get("/", [$controllerClass, "index"])->name("index");

        $name = "create";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "store";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "edit";
        Route::get("/{id}/" . $name . "/{tab?}", [$controllerClass, $name])->name($name);

        $name = "update";
        Route::put("/{id}/{tab?}", [$controllerClass, $name])->name($name);

        $name = "destroy";
        Route::delete("/{id}", [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_child_uom_list";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_default_uom";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_specifications_of_item";
        Route::get($name . "/{name?}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_sizes_of_item";
        Route::get($name . "/{name?}", [$controllerClass, $name])->name($name);

        $name="ajax_get_list";
        Route::get($name . "/{category_id?}" , [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "mark_as_main_image";
        Route::get($name . "/{id}/{document_file_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "excel";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "campaign";
    $controllerClass = CampaignController::class;
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

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, $name])->name($name);
        });
    });

    $route_prefix = "dispatch-order";
    $controllerClass = DispatchOrderController::class;
    Route::resource($route_prefix, $controllerClass)->except(["create", "store", "destroy"]);
    $name = "send";
    Route::any($route_prefix . "-" . $name . "/{id}", [$controllerClass, $name])->name($route_prefix . "." .$name);
    $name = "ajax_delete_product_serial";
    Route::get($route_prefix . "-" . $name . "/{id}", [$controllerClass, $name])->name($route_prefix . "." .$name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "create_from_sale_order";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "product_csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "manufacture-order";
    $controllerClass = ManufactureOrderController::class;
    Route::resource($route_prefix, $controllerClass)->except(["create", "store", "destroy"]);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "branch";
    $controllerClass = BranchController::class;
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

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "bin";
    $controllerClass = BinController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "create_bulk";
        Route::get("create/bulk", [$controllerClass, $name])->name($name);

        $name = "store_bulk";
        Route::post("store/bulk", [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
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

    $route_prefix = "make";
    $controllerClass = MakerController::class;
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

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "item-brand";
    $controllerClass = ItemBrandController::class;
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

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);
        
        $name = "ajax_get_list";
        Route::get($name . "/{category_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "product-serial";
    $controllerClass = ProductSerialController::class;
    Route::resource($route_prefix, $controllerClass);
    $name="ajax_get_unlinked_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name="ajax_get_unlinked_child_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name="ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "sale-order";
    $controllerClass = SaleOrderController::class;
    Route::resource($route_prefix, $controllerClass);
    $name="ajax_get_unlinked_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name="ajax_get_available_qty_and_qty_to_manufacture";
    Route::get($route_prefix . "-" . $name . "/{product_id}/{make_id?}/{order_qty?}/{sale_order_id?}", [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{contact_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_pending_payable_amount";
        Route::get($name . "/{id}/{payment_id?}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "product_csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "payment";
    $controllerClass = PaymentController::class;
    Route::resource($route_prefix, $controllerClass);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "stock-product";
    $controllerClass = StockProductController::class;
    Route::resource($route_prefix, $controllerClass)->except(["show", "edit", "update"]);
    $name = "export_csv";
    Route::get($route_prefix . "/export/csv", [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "transactions";
    Route::get($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "in";
    Route::any($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "out";
    Route::any($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    $route_prefix = "holiday";
    $controllerClass = HolidayController::class;
    Route::resource($route_prefix, $controllerClass);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "export-lead";
    $controllerClass = ExportLeadController::class;
    Route::resource($route_prefix, $controllerClass);

    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{exp_customer_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "transfer_lead";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "address";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "dispatch";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "snooze";
        Route::post($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $name = "convert_to_customer";
        Route::get($name . "/{exp_lead_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "csv_history";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "export-customer";
    $controllerClass = ExportCustomerController::class;
    Route::resource($route_prefix, $controllerClass);

    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "de_activate";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{type}", [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{exp_customer_id}", [$controllerClass, $name])->name($name);

        $name = "dispatch";
        Route::post($name . "/{exp_customer_id}", [$controllerClass, $name])->name($name);

        $name = "snooze";
        Route::post($name . "/{exp_customer_id}", [$controllerClass, $name])->name($name);

        $name = "convert_to_lead";
        Route::get($name . "/{exp_customer_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "export-dealer";
    $controllerClass = ExportDealerController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{exp_dealer_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{exp_dealer_id}", [$controllerClass, $name])->name($name);

        $name = "transfer";
        Route::post($name . "/{exp_dealer_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{exp_dealer_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    
    $route_prefix = "export-party";
    $controllerClass = ExportPartyController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "history";
        Route::get($name . "/{exp_party_id}", [$controllerClass, $name])->name($name);

        $name = "history_store";
        Route::post($name . "/{exp_party_id}", [$controllerClass, $name])->name($name);

        $name = "transfer";
        Route::post($name . "/{exp_party_id}", [$controllerClass, $name])->name($name);

        $name = "change_files";
        Route::post($name . "/{exp_party_id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "export-competitor";
    $controllerClass = ExportCompetitorController::class;
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

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });

        $route_prefix = "import";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::any($name, [$controllerClass, "import_". $name])->name($name);
        });
    });

    $route_prefix = "contact";
    $controllerClass = ContactController::class;
    $name = "index";
    Route::get($route_prefix, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "chooser";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "ajax_get_list_for_mobile";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    $route_prefix = "export-contact";
    $controllerClass = ExportContactController::class;
    Route::resource($route_prefix, $controllerClass)->except("show");
    $name = "chooser";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "ajax_get_list_for_mobile";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);
    });

    $route_prefix = "export-order";
    $controllerClass = ExportOrderController::class;
    Route::resource($route_prefix, $controllerClass);
    $name="ajax_get_unlinked_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name="ajax_get_available_qty_and_qty_to_manufacture";
    Route::get($route_prefix . "-" . $name . "/{product_id}/{make_id?}/{order_qty?}/{export_order_id?}", [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_list";
        Route::get($name . "/{export_contact_id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get_pending_payable_amount";
        Route::get($name . "/{id}/{export_payment_id?}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "export-payment";
    $controllerClass = ExportPaymentController::class;
    Route::resource($route_prefix, $controllerClass);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });


    $route_prefix = "ship-order";
    $controllerClass = ShipOrderController::class;
    Route::resource($route_prefix, $controllerClass)->except(["create", "store", "destroy"]);
    $name = "send";
    Route::any($route_prefix . "-" . $name . "/{id}", [$controllerClass, $name])->name($route_prefix . "." .$name);
    $name = "ajax_delete_product_serial";
    Route::get($route_prefix . "-" . $name . "/{id}", [$controllerClass, $name])->name($route_prefix . "." .$name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "create_from_export_order";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "product_csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "product-issue";
    $controllerClass = ProductIssueController::class;
    Route::resource($route_prefix, $controllerClass);
    $name="ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name="ajax_get_solution_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
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


    $route_prefix = "service";
    $controllerClass = ServiceController::class;
    Route::resource($route_prefix, $controllerClass);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "detail_csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "store-requirement";
    $controllerClass = RequirementController::class;
    Route::resource($route_prefix, $controllerClass);        
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);
        
        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });


    $route_prefix = "purchase-order";
    $controllerClass = PurchaseOrderController::class;
    Route::resource($route_prefix, $controllerClass);    
    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "copy";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "approve";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "decline";
        Route::get("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "receive_items";
        Route::any("{id}/" . $name, [$controllerClass, $name])->name($name);
        
        $name = "receive_items";
        Route::any("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "purchase_order_receive_item_destroy";
        Route::delete("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name="create_from_requirements";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "ajax_get_pending_payable_amount";
        Route::get($name . "/{id}/{payment_id?}", [$controllerClass, $name])->name($name);
        
        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);

            $name = "item_csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "purchase-order-payment";
    $controllerClass = PurchaseOrderPaymentController::class;
    Route::resource($route_prefix, $controllerClass);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "production-target";
    $controllerClass = ProductionTargetController::class;
    Route::resource($route_prefix, $controllerClass);    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "completion";
        Route::any("{id}/" . $name, [$controllerClass, $name])->name($name);

        $name = "completion_destroy";
        Route::delete("{id}/" . $name, [$controllerClass, $name])->name($name);

        $route_prefix = "export"; 
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "bom";
    $controllerClass = BomController::class;    
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        Route::get("/", [$controllerClass, "index"])->name("index");

        $name = "create";
        Route::get($name, [$controllerClass, $name])->name($name);

        $name = "store";
        Route::post($name, [$controllerClass, $name])->name($name);

        $name = "edit";
        Route::get("/{id}/" . $name . "/{tab?}", [$controllerClass, $name])->name($name);

        $name = "update";
        Route::put("/{id}/{tab?}", [$controllerClass, $name])->name($name);

        $name = "destroy";
        Route::delete("/{id}", [$controllerClass, $name])->name($name);

        $name = "ajax_get";
        Route::get($name . "/{id}", [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "item-serial";
    $controllerClass = ItemSerialController::class;
    Route::resource($route_prefix, $controllerClass);    
    $name="ajax_get_list";
    Route::get($route_prefix . "-" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {

        $name = "ajax_update_field";
        Route::post($name, [$controllerClass, $name])->name($name);

        $route_prefix = "export";
        Route::group(['prefix' => $route_prefix, 'as' => $route_prefix . '.'], function () use ($controllerClass) {
            $name = "csv";
            Route::get($name, [$controllerClass, "export_" . $name])->name($name);
        });
    });

    $route_prefix = "stock-item";
    $controllerClass = StockItemController::class;
    Route::resource($route_prefix, $controllerClass)->except(["show", "edit", "update"]);
    $name = "export_csv";
    Route::get($route_prefix . "/export/csv", [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "transactions";
    Route::get($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "in";
    Route::any($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);
    $name = "out";
    Route::any($route_prefix . "/" . $name, [$controllerClass, $name])->name($route_prefix . "." . $name);

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
    $name = "notification_setting";
    Route::any($controller_prefix . "/" . $name, [$controllerClass, $name])->name($controller_prefix . "." . $name);
    $name = "field_setting";
    Route::any($controller_prefix . "/" . $name . "/{type?}", [$controllerClass, $name])->name($controller_prefix . "." . $name);
    $name = "page_setting";
    Route::any($controller_prefix . "/" . $name . "/{type?}", [$controllerClass, $name])->name($controller_prefix . "." . $name);

    Route::group(['prefix' => "developer", 'as' => "developer."], function () {

        $name = "email_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "whatsapp_logs";
        Route::get($name, [DeveloperController::class, $name])->name($name);

        $name = "sms_logs";
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
        $data = Util::objToArray(json_decode($response));
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
    $EmailHelper->setSandbox(false);
    $response = $EmailHelper->send();
    dump($response);
});

Route::get("/test/mongo", function () {
    $records = EmailLog::limit(100)->get()->toArray();
    d($records);
});

Route::get("/test/external_drive", function () {

    // d(get_class_info(Storage::disk(DEFAULT_DISK)), true);

    Storage::disk(DEFAULT_DISK)->put('temp.txt', "Hi external drive working from laravel");

    d(Storage::disk(DEFAULT_DISK)->exists("temp.txt"));

    d(Storage::disk(DEFAULT_DISK)->get('temp.txt'));

    d(Storage::disk(DEFAULT_DISK)->url('temp.txt'));
});

Route::get("/test/external_drive/get", function () {

    $file = "temp.txt";

    if (!Storage::disk(DEFAULT_DISK)->fileExists($file)) {
        die("$file not exist");
    }

    $path = Storage::disk(DEFAULT_DISK)->path($file);

    $ext = pathinfo($file, PATHINFO_EXTENSION);

    download_start($path, get_mime_type_from_extension($ext));
});


Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get("/test/sms/dinstar", function () 
{
    $job = new JobSendSmsWelcome("Hardeep Singh", "9814040490", "91", "https://google.com", 75);
    dispatch($job);
});