<?php

namespace App\Acl;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Helpers\RoleType;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\RouteName;
use App\Traits\CacheTrait;
use Illuminate\Support\Facades\DB;

class AccessControl
{
    use CacheTrait;
    
    private static $instance = null;
    public static $cache_key = "AccessControl";

    public static function init(): AccessControl
    {
        if (is_null(self::$instance)) {
            self::$instance = new AccessControl();
        }

        return self::$instance;
    }

    public function isAllow(String $route_name, array $role_id_list)
    {
        $route_name = trim($route_name);

        foreach (SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER as $allowed_route) {
            if ($route_name == $allowed_route) {
                return true;
            }

            $pos = strpos($allowed_route, "*.");
            if ($pos >= 0) {
                $str = substr($allowed_route, $pos + 2);

                $matched = preg_match("/$str/", $route_name);

                if ($matched) {
                    return true;
                }
            }
        }

        if ($role_id_list) {
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
                return true;
            }

            $system_role_count = Role::where("id", $role_id_list)->where('code', Role::TYPE_SYSTEM_ADMIN)->count();

            if ($system_role_count > 0) {
                $is_allow_all_routes = env("ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES", false);

                if ($is_allow_all_routes) {
                    return true;
                }

                if (in_array($route_name, SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getListOfAllowedRouteNames(array $role_id_list)
    {
        array_check_data_type_and_throw_error($role_id_list, ["integer", "string"]);

        $role_id_list = array_unique($role_id_list);

        // by default allow 
        $allow_route_list = SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER;

        $is_allow_all_routes = env("ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES", false);

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

        return $allow_route_list;
    }

    public static function getAllowedRoutes(array $role_id_list = [], $section_list_key = null)
    {
        array_check_data_type_and_throw_error($role_id_list, ["integer", "string"]);

        $role_id_list = array_unique($role_id_list);

        $admin_role = null;

        $sections = SectionRoutes::get();

        // d($sections, true);

        if ($role_id_list) {
            $admin_role = Role::where('code', Role::TYPE_SYSTEM_ADMIN)->whereIn("id", $role_id_list)->first();
        }

        $is_allow_all_routes_to_admin = env("ACL_IS_SYSTEM_ADMIN_ALLOW_ALL_ROUTES", false);

        if (empty($role_id_list) || $admin_role) {
            if ($is_allow_all_routes_to_admin) {
                $all_routes = LaravelExtend::getRoutes();
                foreach ($all_routes as $route) {
                    $records[$route['route_name']] = [
                        "role" => [
                            "name" => "Admin is allowed for all routes"
                        ],
                        "can_be_delete" => false
                    ];
                }
            }
        }

        $qb = RoleRouteName::query();

        if ($role_id_list) {
            $qb->whereIn("role_id", $role_id_list);
        }

        $role_route_records = $qb->with([
            "role" => function ($query) {
                $query->select(["id", "name", "code"]);
            },
            "routeName" => function ($query) {
                $query->select(["id", "name"]);
            }
        ])->get()->toArray();

        foreach ($role_route_records as $k => $record) {

            $records[$record['route_name']['name']] = [
                "can_be_delete" => true,
                "role" => $record['role'],
                "id" => $record['id']
            ];
        }

        // by default allow 
        $allow_route_list = SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER;

        foreach ($allow_route_list as $route_name) {
            $records[$route_name] = [
                "role" => [
                    "name" => "Allowed For Any Login User"
                ],
                "can_be_delete" => false
            ];
        }

        if (!$is_allow_all_routes_to_admin && $admin_role) {
            $allow_route_list = SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN;

            foreach ($allow_route_list as $route_name) {
                $records[$route_name] = [
                    "role" => [
                        "name" => "Allowed For Admin Only"
                    ],
                    "can_be_delete" => false
                ];
            }
        }

        $ret_records = [];


        foreach ($sections as $section_name => $sub_sections) {
            foreach ($sub_sections as $sub_section_name => $actions) {
                foreach ($actions as $action_name => $action_arr) {
                    $routes = [];
                    $found_record = [];

                    foreach ($records as $route_name => $record) {
                        if (in_array($route_name, $action_arr['routes'])) {
                            $list_key = $action_arr['list_key'] ?? "";

                            if ($section_list_key) {
                                if ($list_key == $section_list_key) {
                                    $found_record = $record;
                                    $routes[] =  $route_name;
                                }
                            } else {
                                $routes[] =  $route_name;
                                $found_record = $record;
                            }
                        }
                    }

                    if ($routes && $found_record) {
                        $found_record['routes'] = $routes;
                        $found_record['section'] = SectionRoutes::getListValue($section_name, $sub_section_name, $action_name);
                        $ret_records[] = $found_record;
                    }
                }
            }
        }

        return $ret_records;
    }

    public static function getMenuCacheKey($user_id)
    {
        return "menu_user_" . $user_id;
    }

    public static function clearMenuCache(array $user_id_list)
    {
        foreach ($user_id_list as $user_id) {
            self::forgotCache(self::getMenuCacheKey($user_id));
        }
    }

    public static function getCacheMenu($user_id)
    {
        return self::getCache(self::getMenuCacheKey($user_id));
    }

    public static function addMenuToCache($user_id, $data)
    {
        return self::addCache(self::getMenuCacheKey($user_id), $data);
    }

    public function syncRouteNamesToDatabase()
    {
        $routes = LaravelExtend::getRoutes();

        $routeNameModel = new RouteName();

        $old_id_list = RouteName::pluck('name', 'id')->toArray();

        $saved_route_name_list = [];

        foreach ($routes as $route) {
            if (
                in_array($route['route_name'], SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER)
                && in_array($route['route_name'], SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)
            ) {
                continue;
            }


            $id = $routeNameModel->insertIgnoreIfExist(["name" => $route['route_name']]);

            unset($old_id_list[$id]);

            $saved_route_name_list[$route['route_name']] = $id;
        }

        if ($old_id_list) {
            RouteName::destroy(array_keys($old_id_list));
        }

        return $saved_route_name_list;
    }
}
