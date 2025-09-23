<?php

namespace App\Acl;

<<<<<<< HEAD
use App\Helpers\RoleType;
use App\Models\Role;
use App\Models\RouteName;
=======
use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Helpers\RoleType;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\RouteName;
use App\Traits\CacheTrait;
>>>>>>> base_v3
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccessControl
{
<<<<<<< HEAD
    private static $instance = null;

    public static function init()
=======
    use CacheTrait;
    
    private static $instance = null;
    public static $cache_key = "AccessControl";

    public static function init(): AccessControl
>>>>>>> base_v3
    {
        if (is_null(self::$instance)) {
            self::$instance = new AccessControl();
        }

        return self::$instance;
    }

    public function isAllow(String $route_name, array $role_id_list)
    {
        $route_name = trim($route_name);

<<<<<<< HEAD
        foreach(SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER as $allowed_route)
        {
            if ($route_name == $allowed_route)
            {
=======
        foreach (SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER as $allowed_route) {
            if ($route_name == $allowed_route) {
>>>>>>> base_v3
                return true;
            }

            $pos = strpos($allowed_route, "*.");
<<<<<<< HEAD
            if ($pos >= 0)
            {
                $str = substr($allowed_route, $pos + 2);
                
                $matched = preg_match("/$str/", $route_name);

                if ($matched)
                {
=======
            if ($pos >= 0) {
                $str = substr($allowed_route, $pos + 2);

                $matched = preg_match("/$str/", $route_name);

                if ($matched) {
>>>>>>> base_v3
                    return true;
                }
            }
        }

<<<<<<< HEAD
        if ($role_id_list)
        {
            $role_ids = implode(",", $role_id_list);

            $q = "
                SELECT
                    COUNT(1) AS c
                FROM
                    role_route_names RRN
                    INNER JOIN route_names RR ON RR.id = RRN.route_name_id
                WHERE
                    RRN.role_id IN ($role_ids) AND RR.name = '$route_name';
            ";

            $record = DB::select($q);

            if ($record && $record[0]->c > 0) {
=======
        if ($role_id_list) {
            $count = RoleRouteName::whereIn("role_id", $role_id_list)->whereHas("routeName", function($q) use($route_name) {
                $q->where("name", $route_name);
            })->count();

            if ($count > 0) {
>>>>>>> base_v3
                return true;
            }

            $system_role_count = Role::where("id", $role_id_list)->where('code', Role::TYPE_SYSTEM_ADMIN)->count();

<<<<<<< HEAD
            if ($system_role_count > 0 && in_array($route_name, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)) {
                return true;
=======
            if ($system_role_count > 0) {
                $is_allow_all_routes = config("app.ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES");

                if ($is_allow_all_routes) {
                    return true;
                }

                if (in_array($route_name, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)) {
                    return true;
                }
>>>>>>> base_v3
            }
        }

        return false;
    }

<<<<<<< HEAD
    public function getListOfAllowedRouteNames(array $role_id_list)
    {
        // by default allow 
        $allow_route_list = SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER;

        // check for system admin
        foreach($role_id_list as $role_id)
        {
            $role = Role::where('code', Role::TYPE_SYSTEM_ADMIN)->findOrFail($role_id);

            if ($role)
            {
                $allow_route_list = array_merge($allow_route_list, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN);
            }
        }

        $role_ids = implode(",", $role_id_list);

        $q = "
            SELECT DISTINCT
                RR.name
            FROM
                role_route_names RRN
                INNER JOIN route_names RR ON RR.id = RRN.route_name_id
            WHERE
                RRN.role_id IN ($role_ids)
        ";

        $records = DB::select($q);


        foreach ($records as $record) {
            $allow_route_list[] = $record->name;
        }

        return $allow_route_list;
    }

    public function getMenuCacheKey($user_id)
=======
    public static function getListOfAllowedRouteNames(array $role_id_list)
    {
        array_check_data_type_and_throw_error($role_id_list, ["integer", "string"]);

        $role_id_list = array_unique($role_id_list);

        // by default allow 
        $allow_route_list = SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER;

        $is_allow_all_routes = config("app.ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES");

        $all_routes = LaravelExtend::getRoutes();

        // check for system admin
        foreach ($role_id_list as $role_id) {
            $role = Role::where('code', Role::TYPE_SYSTEM_ADMIN)->find($role_id);

            if ($role) {
                if ($is_allow_all_routes) {
                    foreach ($all_routes as $route) {
                        $allow_route_list[] = $route['route_name'];
                    }
                } else {
                    $allow_route_list = array_merge($allow_route_list, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN);
                }
            }
        }

        if ($role_id_list) {
            $role_ids = implode(",", $role_id_list);

            $q = "
                SELECT DISTINCT
                    RR.name
                FROM
                    role_route_names RRN
                    INNER JOIN route_names RR ON RR.id = RRN.route_name_id
                WHERE
                    RRN.role_id IN ($role_ids)
            ";

            $records = DB::select($q);


            foreach ($records as $record) {
                $allow_route_list[] = $record->name;
            }
        }

        return array_unique($allow_route_list);
    }

    public static function getAllowedRoutes(array $role_id_list = [], array $chosen_route_list = [])
    {
        array_check_data_type_and_throw_error($role_id_list, ["integer", "string"]);

        $role_id_list = array_unique($role_id_list);

        $admin_role = null;

        $sections = SectionRoutes::get($role_id_list);

        $section_list = SectionRoutes::getList($sections);

        if ($role_id_list) {
            $admin_role = Role::where('code', Role::TYPE_SYSTEM_ADMIN)->whereIn("id", $role_id_list)->first();
        }

        $is_allow_all_routes_to_admin = config("app.ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES");

        $arrayHelper = new ArrayHelper($section_list);

        $records = [];

        if (isset($admin_role)) {
            if ($is_allow_all_routes_to_admin) {
                $all_routes = LaravelExtend::getRoutes();
                foreach ($all_routes as $route) {

                    $keys = $arrayHelper->getKeyIfValueExistInArray([], $route['route_name'], false);

                    if ($keys)
                    {
                        $label = array_key_first($keys);

                        $records[] = [
                            "section" => $label,
                            "role" => "Admin is allowed for all routes",
                            "route_list" => [$route['route_name']],
                            "can_be_delete" => false
                        ];
                    }
                }
            }
            else
            {
                $routes = SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN;

                foreach ($routes as $route_name) {

                    $keys = $arrayHelper->getKeyIfValueExistInArray([], $route_name, false);

                    if ($keys)
                    {
                        $label = array_key_first($keys);
                    }
                    else
                    {
                        $label = str_function_name_to_human_text($route_name);
                    }

                    $records[] = [
                        "section" => $label,
                        "role" => "Default Allowed Routes For Admin",
                        "route_list" => [$route_name],
                        "can_be_delete" => false
                    ];
                }
            }
        }

        $records = array_merge($records, self::_getSectionsForPermissionSummary($sections, $chosen_route_list));

        $routes = SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER;

        foreach ($routes as $route_name) {

            $keys = $arrayHelper->getKeyIfValueExistInArray([], $route_name, false);

            if ($keys)
            {
                $label = array_key_first($keys);
            }
            else
            {
                $label = str_function_name_to_human_text($route_name);
            }

            $records[] = [
                "section" => $label,
                "role" => "Default Allowed Routes For Any Login User",
                "route_list" => [$route_name],
                "can_be_delete" => false
            ];
        }

        // d($records); exit;

        return $records;
    }

    private static function _getSectionsForPermissionSummary(array $sections, array $chosen_route_list = [], $prefix = "")
    {
        $records = [];

        foreach($sections as $section)
        {
            if (isset($section['children']) && is_array($section['children']))
            {
                $records = array_merge($records, self::_getSectionsForPermissionSummary($section['children'], $chosen_route_list, $prefix . $section['text'] . " | "));
            }

            if (!empty($chosen_route_list) && isset($section['route_list']) && is_array($section['route_list']))
            {
                $found = false;
                foreach($section['route_list'] as $route_name)
                {
                    if (in_array($route_name, $chosen_route_list))
                    {
                        $found = true;
                    }
                }

                if (!$found)
                {
                    $section = [];
                }
            }

            if (isset($section['allow_role_routes']) && $section['allow_role_routes'])
            {
                foreach($section['allow_role_routes'] as $allow_role_route)
                {
                    $record = [
                        "section" => $prefix . $section['text'],
                        "role" => $allow_role_route['name'],
                        "route_list" => $allow_role_route['route_list'],
                        "can_be_delete" => true
                    ];

                    $records[] = $record;
                }
            }

        }

        return $records;
    }

    public static function getCacheKey(): string
    {
        $str = static::class;

        return static::optimizeCacheKey($str);
    }

    public static function getMenuCacheKey($user_id)
>>>>>>> base_v3
    {
        return "menu_user_" . $user_id;
    }

<<<<<<< HEAD
    public function clearMenuCache(array $user_id_list)
    {
        foreach ($user_id_list as $user_id) {
            $cache_key = $this->getMenuCacheKey($user_id);

            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }
=======
    public static function clearMenuCache(array $user_id_list = [])
    {
        if (empty($user_id_list))
        {
            self::forgotCache();
        }

        foreach ($user_id_list as $user_id) {
            $cache_key = self::getMenuCacheKey($user_id);
            self::forgotCache($cache_key);
        }
    }

    public static function getCacheMenu($user_id)
    {
        return self::getCache(self::getMenuCacheKey($user_id));
    }

    public static function addMenuToCache($user_id, $data)
    {
        $cache_time = 60 * 60 * 24 * 30;

        return self::addCache(self::getMenuCacheKey($user_id), $data, $cache_time);
>>>>>>> base_v3
    }

    public function syncRouteNamesToDatabase()
    {
<<<<<<< HEAD
        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();

        $route_list = [];
        foreach ($routeCollection as $route) {
            $name = trim($route->getName());
            if ($name) {
                $route_list[] = $name;
            }
        }

        sort($route_list);
=======
        $routes = LaravelExtend::getRoutes();
>>>>>>> base_v3

        $routeNameModel = new RouteName();

        $old_id_list = RouteName::pluck('name', 'id')->toArray();

        $saved_route_name_list = [];

<<<<<<< HEAD
        foreach ($route_list as $route_name) {
            if (
                in_array($route_name, SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER)
                && in_array($route_name, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)
=======
        foreach ($routes as $route) {
            if (
                in_array($route['route_name'], SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER)
                && in_array($route['route_name'], SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)
>>>>>>> base_v3
            ) {
                continue;
            }


<<<<<<< HEAD
            $id = $routeNameModel->insertIgnoreIfExist(["name" => $route_name]);

            unset($old_id_list[$id]);

            $saved_route_name_list[$route_name] = $id;
=======
            $model = $routeNameModel->insertIgnoreIfExist(["name" => $route['route_name']]);

            unset($old_id_list[$model->id]);

            $saved_route_name_list[$route['route_name']] = $model->id;
>>>>>>> base_v3
        }

        if ($old_id_list) {
            RouteName::destroy(array_keys($old_id_list));
        }

        return $saved_route_name_list;
    }
}
