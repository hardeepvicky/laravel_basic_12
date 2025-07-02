<?php

namespace App\Acl;

use App\Exceptions\UserException;
use App\Models\AppMenu;

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
    public static function get()
    {
        $sections = [];

        $sections[]["Home"] = self::home();

        $app_menus = AppMenu::getTreeRecordsWithRelation();

        $sections["Master"] = [
            "User Category" => self::user_category(),
        ];

        $sections["Admin"] = [
            "User" => self::user(),
            "Role" => self::role(),
            "Setting" => self::setting(),
        ];

        $sections[]["Developer"] = self::developer();

        $dynamic_sections = self::getDynamicSections($app_menus);

        $sections = array_merge($sections, $dynamic_sections);

        // d($sections, true);

        return self::addExtraToSections($sections);
    }

    public static function getDynamicSections($app_menus)
    {
        $dynamic_sections = [];

        // d($app_menus, true);

        foreach ($app_menus as $app_menu) {
            $main_section = [];
            if (isset($app_menu['children']) && $app_menu['children']) {                
                $have_grand_child = false;
                foreach ($app_menu['children'] as $children) {

                    $main_section[$children['name']]["routes"][] = $children['route_name'];                    

                    if (isset($children['app_menu_related_link']) && $children['app_menu_related_link']) {
                        foreach ($children['app_menu_related_link'] as $app_menu_related_link) { 
                            if (!isset($main_section[$app_menu_related_link['name']]["routes"]))
                            {
                                $main_section[$app_menu_related_link['name']]["routes"] = [];
                            }

                            $main_section[$app_menu_related_link['name']]["routes"] = array_merge($main_section[$app_menu_related_link['name']]["routes"], $app_menu_related_link["route_name_array"]);
                        }
                    }

                    if (isset($children['children']) && $children['children'])
                    {
                        $have_grand_child = true;
                        $sub_section = [];
                        foreach($children['children'] as $grand_child)
                        {
                            $sub_section[$grand_child['name']]["routes"][] = $grand_child['route_name'];                    
                            if (isset($grand_child['app_menu_related_link']) && $grand_child['app_menu_related_link']) {
                                foreach ($grand_child['app_menu_related_link'] as $app_menu_related_link) { 

                                    if (!isset($sub_section[$app_menu_related_link['name']]["routes"]))
                                    {
                                        $sub_section[$app_menu_related_link['name']]["routes"] = [];
                                    }

                                    $sub_section[$app_menu_related_link['name']]["routes"] = array_merge($sub_section[$app_menu_related_link['name']]["routes"], $app_menu_related_link["route_name_array"]);
                                }
                            }
                        }

                        $main_section[$children['name']] = $sub_section;
                    }
                }

                if ($have_grand_child)
                {
                    $dynamic_sections[$app_menu['name']] = $main_section;
                }
                else
                {
                    $dynamic_sections[][$app_menu['name']] = $main_section;
                }
            }
        }

        // d($dynamic_sections); exit;

        return $dynamic_sections;
    }

    public static function addExtraToSections(array $sections)
    {
        foreach ($sections as $section_name => $sub_sections) {
            foreach ($sub_sections as $sub_section_name => $actions) {
                foreach ($actions as $action_name => $route_list) {
                    $key = self::getListKey($section_name, $sub_section_name, $action_name);

                    $actions[$action_name]['list_key'] = $key;
                }
                $sub_sections[$sub_section_name] = $actions;
            }

            $sections[$section_name] = $sub_sections;
        }

        return $sections;
    }

    public static function getListKey($section_name, $sub_section_name, $action_name)
    {
        $list = [];
        if ($section_name && !is_numeric($section_name)) {
            $list[] = $section_name;
        }

        if ($sub_section_name && !is_numeric($sub_section_name)) {
            $list[] = $sub_section_name;
        }

        if ($action_name && !is_numeric($action_name)) {
            $list[] = $action_name;
        }

        $key = str_normalize_for_array_key(implode(".", $list));

        return $key;
    }

    public static function getListValue($section_name, $sub_section_name, $action_name)
    {
        $list = [];
        if ($section_name && !is_numeric($section_name)) {
            $list[] = $section_name;
        }

        if ($sub_section_name && !is_numeric($sub_section_name) > 0) {
            $list[] = $sub_section_name;
        }

        if ($action_name && !is_numeric($action_name) > 0) {
            $list[] = $action_name;
        }

        return implode(" | ", $list);;
    }

    public static function getList($sections = null)
    {
        if (!$sections) {
            $sections = self::get();
        }

        if (!is_array($sections)) {
            throw new UserException("argument 1 should be array", UserException::ERROR_GENERAL);
        }

        $section_list = [];

        foreach ($sections as $section_name => $sub_sections) {
            foreach ($sub_sections as $sub_section_name => $actions) {
                foreach ($actions as $action_name => $action_arr) {
                    if (isset($action_arr['list_key'])) {
                        $key = $action_arr['list_key'];
                    } else {
                        $key = self::getListKey($section_name, $sub_section_name, $action_name);
                    }

                    if (array_key_exists($key, $section_list)) {
                        throw new UserException("duplicate key generated for section_list");
                    }

                    $section_list[$key] = self::getListValue($section_name, $sub_section_name, $action_name);
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

    private static function department()
    {
        $routePrefix = "department";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }

    private static function designation()
    {
        $routePrefix = "designation";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }

    private static function employee()
    {
        $routePrefix = "employee";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }


    private static function user()
    {
        $routePrefix = "user";

        $routes = self::commonRoutes($routePrefix);
        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }

    private static function role()
    {
        $routePrefix = "role";

        $routes = self::commonRoutes($routePrefix);
        $routes['Activate / Deactivate'] = [
            "routes" => [
                $routePrefix . ".activate",
                $routePrefix . ".de_activate"
            ]
        ];

        return $routes;
    }

    private static function state()
    {
        $routePrefix = "state";

        $routes = self::commonRoutes($routePrefix);

        return $routes;
    }

    private static function city()
    {
        $routePrefix = "city";

        $routes = self::commonRoutes($routePrefix);

        return $routes;
    }

    private static function type()
    {
        $routePrefix = "type";

        $routes = self::commonRoutes($routePrefix);

        return $routes;
    }

    private static function setting()
    {
        $routePrefix = "setting";

        $routes = [];

        $routes["General"] = [
            "routes" => [
                $routePrefix . ".general"
            ]
        ];

        return $routes;
    }

    private static function developer()
    {
        $routePrefix = "developer";

        $routes = [];

        $routes["Email Logs"] = [
            "routes" => [
                $routePrefix . ".email_logs"
            ]
        ];
        $routes["SQL Logs"] = [
            "routes" => [
                $routePrefix . ".sql_logs"
            ]
        ];

        $routes["Ajax Logs"] = [
            "routes" => [
                $routePrefix . ".ajax_logs"
            ]
        ];

        $routes["Laravel Routes Summary"] = [
            "routes" => [
                $routePrefix . ".laravel_routes_index"
            ]
        ];

        return $routes;
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
