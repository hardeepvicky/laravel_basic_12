<?php

namespace App\Acl;

use App\Exceptions\UserException;
use App\Models\AppMenu;
use App\Models\RoleRouteName;

class SectionRoutes
{
    /**
     * list of routes which are allowed for any login user
     */
    public const ALLOW_ROUTES_FOR_ANY_LOGIN_USER = [
        "dashboard",
        "user.my-profile",
        "user.change.password",

        "*.ajax_get_list",
        "*.ajax_get_code_list",
        "*.ajax_get_count",
        "*.ajax_get",
    ];

    /**
     * list of routes for system admin role
     */
    public const ALLOW_ROUTES_FOR_SYSTEM_ADMIN = [
        'permissions.index',
        'permissions.assign',
        'permissions.assign_to_many',
        'permissions.ajax_delete',
        'permissions.ajax_get_permissions',
    ];

    /**
     * function to retrive all sections
     */
    public static function get($role_id_list = null)
    {
        $app_menus = AppMenu::getTreeRecordsWithRelation();

        // d($app_menus);

        $sections = self::getDynamicSections($app_menus);

        $sections[] = self::developer();

        $database_routes = [];

        $qb = RoleRouteName::with(['routeName' => function ($query) {
            $query->select(['id', 'name']);
        }])->with("role");

        if ($role_id_list)
        {
            if (!is_array($role_id_list))
            {
                $role_id_list = [$role_id_list];
            }

            $qb->whereIn("role_id", $role_id_list);
        }

        $database_routes = $qb->get()->toArray();

        return self::addExtraToSections($sections, $database_routes);
    }

    public static function getDynamicSections($app_menus)
    {
        $dynamic_sections = [];

        // d($app_menus, true);

        foreach ($app_menus as $app_menu) {

            $section = [
                "text" => $app_menu['name'],
                "route_list" => [],
                "children" => [],
            ];

            if (isset($app_menu['route_name']) && $app_menu['route_name'])
            {
                $section['route_list'][] = $app_menu['route_name'];
            }

            if (isset($app_menu['children']) && is_array($app_menu['children'])) {
                $section['children'] = array_merge($section['children'], self::getDynamicSections($app_menu['children']));
            }

            $related_sections = [];

            if (isset($app_menu['app_menu_related_link']) && is_array($app_menu['app_menu_related_link']))
            {
                foreach ($app_menu['app_menu_related_link'] as $app_menu_related_link) {
                    // d($section);
                    // d($app_menu_related_link);
                    if (isset($app_menu_related_link['route_name_array'])) {
                        if (is_string($app_menu_related_link['route_name_array'])) {
                            $app_menu_related_link['route_name_array'] = [$app_menu_related_link['route_name_array']];
                        }

                        $related_section = [
                            "text" => $app_menu_related_link['name'],
                            "route_list" => []
                        ];

                        $related_section['route_list'] = $app_menu_related_link['route_name_array'];

                        // d($sub_section, true);

                        $parent_section_found = false;

                        if ($section['text'] == $related_section['text'])
                        {
                            $parent_section_found = true;
                            $section['route_list'] = array_merge($section['route_list'], $related_section["route_list"]);
                        }

                        if ($parent_section_found)
                        {
                            unset($related_section);
                        }

                        if (isset($related_section))
                        {
                            $related_sections[] = $related_section;
                        }
                    }
                }
            }

            $dynamic_sections[] = $section;

            if (isset($related_sections) && is_array($related_sections))
            {
                foreach($related_sections as $related_section)
                {
                    $dynamic_sections[] = $related_section;
                }
            }
        }

        return $dynamic_sections;
    }

    public static function addExtraToSections(array $sections, $database_routes)
    {
        foreach ($sections as $k => $arr) {
            if (isset($arr['children']) && $arr['children']) {
                $arr['children'] = self::addExtraToSections($arr['children'], $database_routes);
            } else if (isset($arr['route_list']) && $arr['route_list']) {

                $arr['database_routes'] = [];
                $arr['allow_role_routes'] = [];
                $arr['allow_route_list'] = [];

                $arr['state']['selected'] = false;

                foreach($database_routes as $database_route)
                {
                    if (in_array($database_route['route_name']['name'], $arr['route_list']))
                    {
                        $arr["database_routes"][] = $database_route;
                        $arr['state']['selected'] = true;

                        $role = $database_route["role"];

                        if (!isset($arr['allow_role_routes'][$role['id']]))
                        {
                            $arr['allow_role_routes'][$role['id']] = $role;
                        } 

                        $arr['allow_role_routes'][$role['id']]["route_list"][$database_route['id']] = $database_route['route_name']['name'];

                        $arr['allow_route_list'][] = $database_route['route_name']['name'];
                    }
                }

                // if (!empty($arr['allow_role_routes']))
                // {
                //     d($arr);
                // }

                $arr["li_attr"] = [
                    "data-routes" => implode(",", $arr['route_list'])
                ];
            }

            $sections[$k] = $arr;
        }

        return $sections;
    }

    public static function getList($sections = null, $prefex = "")
    {
        if (!$sections) {
            $sections = self::get();
        }

        if (!is_array($sections)) {
            throw new UserException("argument 1 should be array", UserException::ERROR_GENERAL);
        }

        $section_list = [];

        foreach ($sections as $arr) {

            if (isset($arr['children']) && is_array($arr['children']) && $arr['children']) {
                $children_list = self::getList($arr['children'], $prefex . $arr['text'] . " | ");
                $section_list = array_merge($section_list, $children_list);
            } else if (isset($arr['route_list']) && is_array($arr['route_list']) && $arr['route_list']) {
                foreach ($arr['route_list'] as $route_name) {
                    $section_list[$prefex . $arr['text']][] = $route_name;
                }
            }
        }

        return $section_list;
    }

    public static function getRoutesFromKey($key, $sections = null)
    {
        if (!$sections) {
            $sections = self::get();
        }

        if (!is_array($sections)) {
            throw new UserException("argument 1 should be array", UserException::ERROR_GENERAL);
        }

        foreach ($sections as $section_name => $sub_sections) {
            foreach ($sub_sections as $sub_section_name => $actions) {
                foreach ($actions as $action_name => $action_arr) {
                    if (isset($action_arr['list_key']) && $action_arr['list_key'] == $key) {
                        return $action_arr['routes'];
                    }
                }
            }
        }

        return [];
    }


    protected static function commonRoutes($routePrefix)
    {
        $routes =  [
            "List" => [
                "routes" => [$routePrefix . ".index"]
            ],
            "Add" => [
                "routes" => [
                    $routePrefix . ".create",
                    $routePrefix . ".store"
                ]
            ],
            "Edit" => [
                "routes" => [
                    $routePrefix . ".edit",
                    $routePrefix . ".update"
                ]
            ],
            "Delete" => [
                "routes" => [
                    $routePrefix . ".destroy"
                ]
            ]
        ];

        return $routes;
    }

    private static function home()
    {
        $routes =  [
            "Dashbaord" => [
                "routes" => ["dashboard"]
            ],
        ];

        return $routes;
    }

    private static function developer()
    {
        $routePrefix = "developer";

        $section = [
            "text" => "Developer",
            "children" => []
        ];

        $section['children'][] = [
            "text" => "Email Logs",
            "route_list" => [$routePrefix . ".email_logs"]
        ];

        $section['children'][] = [
            "text" => "SQL Logs",
            "route_list" => [$routePrefix . ".sql_logs"]
        ];

        $section['children'][] = [
            "text" => "Ajax Logs",
            "route_list" => [$routePrefix . ".ajax_logs"]
        ];

        $section['children'][] = [
            "text" => "Laravel Routes Summary",
            "route_list" => [$routePrefix . ".laravel_routes_index"]
        ];

        return $section;
    }

    private static function user_category()
    {
        $routePrefix = "user-category";

        $routes = self::commonRoutes($routePrefix);
        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }

    private static function task()
    {
        $routePrefix = "task";

        $routes = self::commonRoutes($routePrefix);
        $routes['Today'] = [
            "routes" => [
                $routePrefix . ".today",
            ]
        ];

        $routes['Change Status'] = [
            "routes" => [
                $routePrefix . ".status_confirm",
                $routePrefix . ".status_in_progress",
                $routePrefix . ".status_complete",
                $routePrefix . ".status_verified",
            ]
        ];

        return $routes;
    }
}
