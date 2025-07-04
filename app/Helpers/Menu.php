<?php

namespace App\Helpers;

use App\Acl\AccessControl;
use App\Models\AppMenu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class Menu
{
    private static $menus = [];
    private static $current_route_name = "";
    private static $dynamic_menu_need_to_insert_in_static = [];

    public static function setCurrentRouteName($current_route_name)
    {
        if ($current_route_name && is_string($current_route_name)) {
            self::$current_route_name = strtolower(trim($current_route_name));
            BaseMenu::setCurrentRouteName(self::$current_route_name);
        }
    }

    public static function getDynamicMenus(array $app_menus, $depth = 0)
    {
        $dynamic_menus = [];

        $static_menu_list = self::getStaticMenuList();

        foreach ($app_menus as $app_menu) {
            $dynamic_menu = [
                "title" => $app_menu['name'],
                "css_class" => $app_menu['css_class'],
                "icon" => $app_menu['icon_css_class'],
                "related_links" => []
            ];

            if (isset($app_menu['app_menu_related_link']) && $app_menu['app_menu_related_link']) {
                foreach ($app_menu['app_menu_related_link'] as $app_menu_related_link) {
                    if ($app_menu_related_link['route_name_array']) {
                        foreach ($app_menu_related_link['route_name_array'] as $route_name) {
                            $related_link = [
                                "title" => $app_menu_related_link['name'],
                                "route_name" => $route_name,
                                "related_links" => []
                            ];

                            $dynamic_menu['related_links'][] = $related_link;
                        }
                    }
                }
            }

            if (isset($app_menu['children']) && $app_menu['children']) {
                $dynamic_menu['links'] = self::getDynamicMenus($app_menu['children'], $depth + 1);
            } else if ($app_menu['is_link'] && $app_menu['route_name']) {
                $dynamic_menu['route_name'] = $app_menu['route_name'];
            }

            if (!$dynamic_menu['icon']) {
                if ($depth == 0) {
                    $dynamic_menu['icon'] = "fas fa-layer-group";
                } else if ($depth > 0) {
                    $dynamic_menu['icon'] = 'fas fa-cube';
                }
            }

            if ($depth > 0 && $app_menu['is_link'] && $app_menu['route_name']) {
                $dynamic_menu['icon'] .= ' child-menu-icon';
            }

            if (isset($app_menu['show_in_static_menu']) && $app_menu['show_in_static_menu'])
            {
                if (is_string($app_menu['show_in_static_menu']))
                {
                    $app_menu['show_in_static_menu'] = [$app_menu['show_in_static_menu']];
                }

                foreach($app_menu['show_in_static_menu'] as $static_label)
                {
                    if (isset($static_menu_list[$static_label]))
                    {
                        self::$dynamic_menu_need_to_insert_in_static[$static_label][] = $dynamic_menu;
                    }
                }
            }

            $dynamic_menus[] = $dynamic_menu;
        }

        return $dynamic_menus;
    }

    public static function get($auth_user_id)
    {
        self::$menus = [];

        self::$menus = AccessControl::getCacheMenu($auth_user_id);

        // d(self::$menus);
        if (!empty(self::$menus)) {
            return self::checkForActive(self::$menus);
        }

        $app_menus = AppMenu::getTreeRecordsWithRelation([
            ["is_active", 1]
        ]);

        // d($app_menus, true);
        $app_menus = self::getDynamicMenus($app_menus);

        $home_menu = (new HomeMenu)->get();

        if (isset(self::$dynamic_menu_need_to_insert_in_static['Home']))
        {
            $home_menu['links'] = array_merge($home_menu['links'], self::$dynamic_menu_need_to_insert_in_static['Home']);
        }

        self::$menus[] = $home_menu;

        self::$menus = array_merge(self::$menus, $app_menus);

        $admin_menu = (new AdminMenu)->get();

        if (isset(self::$dynamic_menu_need_to_insert_in_static['Admin']))
        {
            $admin_menu['links'] = array_merge($admin_menu['links'], self::$dynamic_menu_need_to_insert_in_static['Admin']);
        }

        self::$menus[] = $admin_menu;

        $developer_menu = (new DeveloperMenu)->get();

        if (isset(self::$dynamic_menu_need_to_insert_in_static['Developer']))
        {
            $developer_menu['links'] = array_merge($developer_menu['links'], self::$dynamic_menu_need_to_insert_in_static['Developer']);
        }
        
        self::$menus[] = $developer_menu;

        self::_filterMenuForUser($auth_user_id);

        // d(self::$menus, true);

        AccessControl::addMenuToCache($auth_user_id, self::$menus);

        return self::checkForActive(self::$menus);
    }

    public static function getStaticMenuList()
    {
        $list = ["Dashboard", "Admin", "Developer"];

        $ret_list = [];

        foreach($list as $k => $v)
        {
            $ret_list[$v] = $v;
        }

        return $ret_list;
    }

    public static function getBreadcums($menus)
    {
        $breadcums = self::findBreadCum($menus);

        //d($breadcums); exit;

        return $breadcums;
    }

    public static function checkForActive($menus)
    {
        foreach ($menus as $k => $menu) {
            if (isset($menu['route_name'])) {
                $menus[$k]['is_active'] = self::isActiveLink($menu);
            } else if (isset($menu['links'])) {
                $menus[$k]['links'] = self::checkForActive($menu['links']);
            }
        }

        return $menus;
    }

    public static function isActiveLink(array $link)
    {
        if ($link['route_name'] == self::$current_route_name) {
            return true;
        } else if (isset($link["related_links"]) && is_array($link["related_links"])) {
            foreach ($link["related_links"] as $related_link) {
                $is_active = self::isActiveLink($related_link);

                if ($is_active) {
                    return true;
                }
            }
        }

        return false;
    }


    private static function findBreadCum(array $menus, array $parents = [])
    {
        foreach ($menus as $menu) {
            $aray_helper = new ArrayHelper($menu);
            $parent = $aray_helper->getOnlyWhichHaveKeys(["title", "route_name"]);

            if (isset($menu['links'])) {
                $temp = $parents;
                $temp[] = $parent;
                $ret = self::findBreadCum($menu['links'], $temp);

                if ($ret) {
                    return $ret;
                }
            }

            if (isset($menu['related_links'])) {
                foreach ($menu['related_links'] as $related_link) {
                    if (isset($related_link['route_name'])) {
                        if ($related_link['route_name'] == self::$current_route_name) {
                            $parents[] = $parent;

                            $aray_helper = new ArrayHelper($related_link);
                            $temp = $aray_helper->getOnlyWhichHaveKeys(["title", "route_name"]);

                            $parents[] = $temp;

                            return $parents;
                        }
                    }
                }
            }

            if (isset($menu['route_name'])) {
                if ($menu['route_name'] == self::$current_route_name) {
                    $parents[] = $parent;
                    return $parents;
                }
            }
        }

        return [];
    }

    public static function getList(array $menus, String $prefix = "")
    {
        $list = [];
        foreach ($menus as $menu) {
            if (isset($menu['route_name'])) {
                $list[] = [
                    "title" => $prefix . $menu['title'],
                    "url" => route($menu['route_name'])
                ];
            } else if (isset($menu["links"])) {
                $list = array_merge($list, self::getList($menu["links"], $prefix . $menu['title'] . " -> "));
            }
        }

        return $list;
    }

    private static function _filterMenuForUser($user_id)
    {
        $user = User::findOrFail($user_id);

        $role_id_list = [];
        foreach ($user->userRole->toArray() as $user_role) {
            $role_id_list[] = $user_role['role_id'];
        }

        $acccessControl = AccessControl::init();
        $allowed_route_name_list = $acccessControl->getListOfAllowedRouteNames($role_id_list);
        // d($allowed_route_name_list, true);

        foreach (self::$menus as $k => $sub_menu) {
            if (isset($sub_menu['links'])) {
                foreach ($sub_menu['links'] as $k2 => $sub_menu2) {
                    if (isset($sub_menu2['links'])) {
                        foreach ($sub_menu2['links'] as $k3 => $sub_menu3) {
                            if (isset($sub_menu3['route_name'])) {
                                if (!in_array($sub_menu3['route_name'], $allowed_route_name_list)) {
                                    unset($sub_menu2['links'][$k3]);
                                }
                            }
                        }

                        if (empty($sub_menu2['links'])) {
                            unset($sub_menu['links'][$k2]);
                        } else {
                            $sub_menu['links'][$k2] = $sub_menu2;
                        }
                    } else if (isset($sub_menu2['route_name'])) {
                        if (!in_array($sub_menu2['route_name'], $allowed_route_name_list)) {
                            unset($sub_menu['links'][$k2]);
                        }
                    }
                }
            } else if (isset($sub_menu['route_name'])) {
                if (!in_array($sub_menu['route_name'], $allowed_route_name_list)) {
                    unset(self::$menus[$k]);
                }
            }

            if (empty($sub_menu['links'])) {
                unset(self::$menus[$k]);
            } else {
                self::$menus[$k] = $sub_menu;
            }
        }
    }
}

class BaseMenu
{
    const ICON_MENU_ROOT = 'fas fa-layer-group';
    const ICON_MENU_ROOT_CHILD = 'fas fa-cube';
    const ICON_MENU_LIST = 'fas fa-table';
    const ICON_MENU_CREATE = 'fas fa-plus-circle';

    private static $current_route_name = "";

    const LINK_TYPE_SUMMARY = "summary";
    const LINK_TYPE_ADD = "add";
    const LINK_TYPE_EDIT = "edit";
    const LINK_TYPE_DELETE = "add";

    public static function setCurrentRouteName(String $current_route_name)
    {
        self::$current_route_name = strtolower(trim($current_route_name));
    }

    public static function get(): array
    {
        return [];
    }

    public static function getModule(String $title, $icon, array $links = [])
    {
        if (!$icon) {
            $icon = self::ICON_MENU_ROOT;
        }

        return [
            'title' => $title,
            'icon' => $icon,
            "links" => $links
        ];
    }


    public static function getLink(String $route_name, $title, String $icon, array $related_links = [], String $link_type = "")
    {
        $route_name = trim($route_name);

        if (Route::has($route_name)) {
            if (!$title) {
                $title = self::getLinkTitleFromRouteName($route_name, $link_type);
            }

            $link = [
                "title" => $title,
                "icon" => "child-menu-icon " . $icon,
                "route_name" => $route_name,
                "related_links" => $related_links
            ];

            return $link;
        }

        return [
            "title" => "Route $route_name Not Exist",
            "icon" => "",
            "route_name" => null,
            "related_links" => []
        ];
    }

    public static function addRelatedLink(String $route_name, String $title, array $related_links = [])
    {
        $route_name = trim($route_name);

        if (Route::has($route_name)) {
            $link = [
                "title" => $title,
                "route_name" => trim($route_name),
                "related_links" => $related_links
            ];

            return $link;
        }

        return [
            "title" => "Route $route_name Not Exist",
            "route_name" => null,
            "related_links" => []
        ];
    }

    public static function isActiveLink(array $link)
    {
        if ($link['route_name'] == self::$current_route_name) {
            return true;
        } else if (isset($link["related_links"]) && is_array($link["related_links"])) {
            foreach ($link["related_links"] as $related_link) {
                $is_active = self::isActiveLink($related_link);

                if ($is_active) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getControllerDefaultLinks(String $routePrefix, String $title, String $icon = "")
    {
        $links = [
            "title" => $title,
            "icon" => $icon,
            "links" => [
                self::getLink($routePrefix . ".index", "List", self::ICON_MENU_LIST, [
                    self::addRelatedLink($routePrefix . ".edit", "Edit"),
                    self::addRelatedLink($routePrefix . ".view", "View"),
                    self::addRelatedLink($routePrefix . ".copy", "Copy"),
                ]),
                self::getLink($routePrefix . ".create", "Add", self::ICON_MENU_CREATE),
            ],
        ];

        return $links;
    }

    public static function getLinkTitleFromRouteName(String $route_name, String $link_type)
    {
        $title = $route_name;

        $arr = explode(".", $route_name);

        if (count($arr) > 1) {
            $title = end($arr);
        }

        $title = str_replace("_", " ", $title);

        switch ($link_type) {
            case self::LINK_TYPE_SUMMARY:
                $title = str_replace("index", "summary", $title);
                break;
        }

        $title = ucwords($title);

        return $title;
    }
}

class HomeMenu extends BaseMenu
{
    public static function get(): array
    {
        $links = [];

        $links[] = self::getLink("dashboard", "Dashboard", 'fa-solid fa-gauge');

        return self::getModule("Dashboard", 'fas fa-home', $links);
    }
}


class AdminMenu extends BaseMenu
{
    public static function get(): array
    {
        $links = [];

        $links[] = self::permission();
        $links[] = self::type();

        return self::getModule("Admin", self::ICON_MENU_ROOT, $links);
    }

    public static function permission()
    {
        $routePrefix = "permissions";

        $links = [
            self::getLink($routePrefix . ".index", "List", self::ICON_MENU_LIST),
            self::getLink($routePrefix . ".assign", "Assign", 'fa-solid fa-gear'),
        ];

        return self::getModule("Permission", self::ICON_MENU_ROOT_CHILD, $links);
    }

    private static function type()
    {
        $routePrefix = "type";

        $links = self::getControllerDefaultLinks($routePrefix, "Type", self::ICON_MENU_ROOT_CHILD);

        return $links;
    }
}

class DeveloperMenu extends BaseMenu
{
    public static function get(): array
    {
        $links = [];

        $links[] = self::setting();
        $links[] = self::app_menu();
        $links[] = self::logs();

        return self::getModule("Developer", null, $links);
    }

    public static function setting()
    {
        $routePrefix = "setting";

        $links = [
            self::getLink($routePrefix . ".general", "General", 'fa-solid fa-gear'),
            self::getLink($routePrefix . ".field_setting", "Field Setting", 'fa-solid fa-gear'),
        ];

        return self::getModule("Setting", self::ICON_MENU_ROOT_CHILD, $links);
    }

    private static function app_menu()
    {
        $routePrefix = "app-menu";

        $links = self::getControllerDefaultLinks($routePrefix, "Menu", self::ICON_MENU_ROOT_CHILD);

        return $links;
    }

    public static function logs()
    {
        $links = [];

        $routePrefix = "developer";

        $links[] = self::getLink($routePrefix . ".email_logs", null, self::ICON_MENU_LIST, [], self::LINK_TYPE_SUMMARY);
        $links[] = self::getLink($routePrefix . ".sql_logs", null, self::ICON_MENU_LIST, [], self::LINK_TYPE_SUMMARY);
        $links[] = self::getLink($routePrefix . ".ajax_logs", null, self::ICON_MENU_LIST, [], self::LINK_TYPE_SUMMARY);
        $links[] = self::getLink($routePrefix . ".command_logs", null, self::ICON_MENU_LIST, [], self::LINK_TYPE_SUMMARY);
        $links[] = self::getLink($routePrefix . ".laravel_routes_index", null, self::ICON_MENU_LIST, [], self::LINK_TYPE_SUMMARY);

        return self::getModule("Logs", null, $links);
    }
}
