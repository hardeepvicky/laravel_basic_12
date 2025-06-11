<?php
namespace App\Acl;

class SectionRoutes
{
    /**
     * list of routes which are allowed for any login user
     */
    public const ALLOW_ROUTES_FOR_ANY_LOGIN_USER = [
        "dashboard",
        "user.my-profile",
        "user.change-password",

        "*.ajax_get_list",
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

        $sections["HR"] = [
            "Department" => self::department(),
            "Designation" => self::designation(),
            "Employee" => self::employee(),            
        ];

        $sections["Members"] = [            
            "User" => self::user(),
            "Role" => self::role()
        ];

        $sections["System Management"] = [            
            "State" => self::state(),
            "City" => self::city(),
            "Type" => self::type(),
        ];

        $sections[]["Developer"] = self::developer();

        return $sections;
    }

    protected static function commonRoutes($routePrefix)
    {
        $routes =  [
            "Summary" => [$routePrefix . ".index"],
            "Add" => [
                $routePrefix . ".create",
                $routePrefix . ".store"
            ],
            "Edit" => [
                $routePrefix . ".edit",
                $routePrefix . ".update"
            ],
            "Delete" => [$routePrefix . ".destroy"]
        ];

        return $routes;
    }

    private static function home()
    {
        $routes =  [
            "Dashbaord" => ["dashboard"],
        ];

        return $routes;
    }

    private static function department()
    {
        $routePrefix = "department";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [$routePrefix . ".activate", $routePrefix . ".de_activate"];

        return $routes;
    }

    private static function designation()
    {
        $routePrefix = "designation";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [$routePrefix . ".activate", $routePrefix . ".de_activate"];

        return $routes;
    }

    private static function employee()
    {
        $routePrefix = "employee";

        $routes = self::commonRoutes($routePrefix);

        $routes['Activate / Deactivate'] = [$routePrefix . ".activate", $routePrefix . ".de_activate"];

        return $routes;
    }


    private static function user()
    {
        $routePrefix = "user";

        $routes = self::commonRoutes($routePrefix);
        $routes['Activate / Deactivate'] = [$routePrefix . ".activate", $routePrefix . ".de_activate"];

        return $routes;
    }

    private static function role()
    {
        $routePrefix = "role";

        $routes = self::commonRoutes($routePrefix);
        $routes['Activate / Deactivate'] = [$routePrefix . ".activate", $routePrefix . ".de_activate"];

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


    private static function developer()
    {
        $routePrefix = "developer";

        $routes = [];

        $routes["SQL Log Summary"] = [$routePrefix . ".sql_log"];
        $routes["Laravel Routes Summary"] = [$routePrefix . ".laravel_routes_index"];
        
        return $routes;
    }

}