<?php

use App\Exceptions\UserException;
use App\Helpers\Menu;
use App\Http\Middleware\AddLogContext;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\BackendViewRender;
use App\Http\Middleware\RolePermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check_auth' => Authenticate::class,
            'role_permission' => RolePermission::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'interakt-hook',
            'mailjet-webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (HttpException $e, Request $request) {
            $error_code = $e->getStatusCode();

            $error_msg = $e->getMessage();

            $request = request();

            $current_url = $request->url();

            $current_route_name = Route::currentRouteName();

            $contact_support_email = config("app.DEVELOPER_TEAM_EMAIL");

            $data = compact("error_code", "error_msg", "current_url", "current_route_name", "contact_support_email");

            if ($request->ajax()) {
                $view_name = "errors.ajax.$error_code";
                if (!view()->exists($view_name)) {
                    $view_name = "errors.ajax.default";
                }

                return response()->view($view_name, $data, $e->getStatusCode());
            } else {
                $view_name = "errors.$error_code";
                if (!view()->exists($view_name)) {
                    $view_name = "errors.default";
                }

                $auth_user = Auth::user();

                if ($auth_user) {
                    //below code if its is backend user
                    $data['layout'] = 'backend.layouts.main';

                    Menu::setCurrentRouteName($current_route_name);

                    $menus = Menu::get(Auth::user()->id);

                    $header_menus = Menu::getList($menus);
                    $header_menu_list = [];

                    foreach($header_menus as $header_menu)
                    {
                        if ($header_menu['url'] && $header_menu['url'] != "#")
                        {
                            $header_menu_list[$header_menu['url']] = $header_menu['title'];
                        }
                    }

                    $partial_path = "backend.partials";

                    $breadcums = Menu::getBreadcums($menus);

                    $data = array_merge($data, compact("menus", "header_menu_list", "partial_path", "breadcums"));
                } else {

                    $partial_path = "frontend.partials";

                    $data = array_merge($data, compact("partial_path"));

                    $data['layout'] = 'frontend.layouts.main';
                }

                return response()->view($view_name, $data, $error_code);
            }
        });

        $exceptions->renderable(function (UserException $e) {

            $error_code = $e->getCode();

            $error_msg = $e->getMessage();

            $request = request();

            $current_url = $request->url();

            $current_route_name = Route::currentRouteName();

            $ajax_view_name = "errors.ajax.user_exception";
            if (!view()->exists($ajax_view_name)) {
                $ajax_view_name = "errors.ajax.default";
            }

            $data = array_merge($e->data, compact("error_code", "error_msg", "current_url", "current_route_name", "ajax_view_name"));

            // dd($data);

            if ($request->ajax()) {
                return response()->view($ajax_view_name, $data, $error_code);
            } else {

                $auth_user = Auth::user();

                if ($auth_user) {
                    //below code if its is backend user
                    $data['layout'] = 'backend.layouts.main';

                    Menu::setCurrentRouteName($current_route_name);

                    $menus = Menu::get(Auth::user()->id);

                    $header_menus = Menu::getList($menus);
                    $header_menu_list = [];

                    foreach($header_menus as $header_menu)
                    {
                        if ($header_menu['url'] && $header_menu['url'] != "#")
                        {
                            $header_menu_list[$header_menu['url']] = $header_menu['title'];
                        }
                    }

                    $partial_path = "backend.partials";

                    $breadcums = Menu::getBreadcums($menus);

                    $data = array_merge($data, compact("menus", "header_menu_list", "partial_path", "breadcums"));
                } else {
                    $data['layout'] = 'frontend.layouts.main';
                }

                return response()->view("errors.default", $data, $error_code);
            }
        });

        $exceptions->renderable(function (ModelNotFoundException $e) {

            $error_code = $e->getCode();

            d($error_code);

            dd($e->getModel());

            $error_msg = $e->getMessage();

            $request = request();

            $current_url = $request->url();

            $current_route_name = Route::currentRouteName();

            $ajax_view_name = "errors.ajax.user_exception";
            if (!view()->exists($ajax_view_name)) {
                $ajax_view_name = "errors.ajax.default";
            }

            $data = array_merge($e->data, compact("error_code", "error_msg", "current_url", "current_route_name", "ajax_view_name"));

            // dd($data);

            if ($request->ajax()) {
                return response()->view($ajax_view_name, $data, $error_code);
            } else {

                $auth_user = Auth::user();

                if ($auth_user) {
                    //below code if its is backend user
                    $data['layout'] = 'backend.layouts.main';

                    Menu::setCurrentRouteName($current_route_name);

                    $menus = Menu::get(Auth::user()->id);

                    $header_menu_list = Menu::getList($menus);

                    $partial_path = "backend.partials";

                    $breadcums = Menu::getBreadcums($menus);

                    $data = array_merge($data, compact("menus", "header_menu_list", "partial_path", "breadcums"));
                } else {
                    $data['layout'] = 'frontend.layouts.main';
                }

                return response()->view("errors.default", $data, $error_code);
            }
        });
    })->create();
