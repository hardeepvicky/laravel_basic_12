<?php

namespace App\Http\Middleware;

use App\Acl\AccessControl;
use App\Helpers\LaravelExtend;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $route_name = $request->route()->getName();

        if (!$route_name) {
            die("Current Url has no Route Name. Please Assign Route name.");
        }

        $user = Auth::user();

        $role_id_list = [];
        foreach ($user->userRole->toArray() as $user_role) {
            $role_id_list[] = $user_role['role_id'];
        }

        $accessControl = AccessControl::init();

        if (!$accessControl->isAllow($route_name, $role_id_list)) {
            Log::info("RolePermission : isAllow : false");
            $is_allow_all_routes = config("app.ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES");
            Log::info("RolePermission : ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES : " . ($is_allow_all_routes ? "True" : "False"));
            LaravelExtend::writeQueryLog(LaravelExtend::getQueryLog("", -5));
            abort(401);
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void {}
}
