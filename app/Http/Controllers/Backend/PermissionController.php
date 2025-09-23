<?php

namespace App\Http\Controllers\Backend;

use App\Acl\AccessControl;
use App\Acl\SectionRoutes;
<<<<<<< HEAD
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
=======
use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\Notification;
use App\Models\PermissionChangeLog;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
>>>>>>> base_v3
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class PermissionController extends BackendController
{
    public String $routePrefix = "permissions";

    public function index()
    {
        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "role_id", "type" => "int", "view_field" => "role_id"],
        ], true);

        $section_conditions = $this->getConditions(Route::currentRouteName() . "-section", [
<<<<<<< HEAD
            ["field" => "section_name", "type" => "int", "view_field" => "section_name"],
            ["field" => "action_name", "type" => "int", "view_field" => "action_name"],
        ], true);

        $sections = SectionRoutes::get();

        if ($conditions || $section_conditions) {

            $records = RoleRouteName::where($conditions)->with([
                "role" => function ($query) {
                    $query->select(["id", "name", "code"]);
                },
                "routeName" => function ($query) {
                    $query->select(["id", "name"]);
                }
            ])->get()->toArray();

            //d($section_conditions); exit;

            foreach ($records as $k => $record) {
                if (isset($record['role']) && isset($record['route_name'])) {

                    $records[$k]['section'] = "Section is not set in SectionRoutes.php";
                    $records[$k]['can_be_delete'] = true;                    

                    if (in_array($record['route_name']['name'], SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER)) {
                        $records[$k]['can_be_delete'] = false;
                        $records[$k]['role']['name'] = "Allow For All Login User";
                    }

                    if ($record['role']['code'] == Role::TYPE_SYSTEM_ADMIN && in_array($record['route_name']['name'], SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN)) {
                        $records[$k]['can_be_delete'] = false;
                        $records[$k]['role']['name'] = "Allow For All System Admin";
                    }

                    foreach ($sections as $section_name => $actions) {
                        foreach ($actions as $action_name => $route_list) {
                            if (in_array($record['route_name']['name'], $route_list)) {
                                $records[$k]['section'] = $section_name;
                                $records[$k]['action'] = $action_name;
                            }
                        }
                    }

                    if (isset($section_conditions['section_name']) && $section_conditions['section_name'] != $records[$k]['section']) {
                        unset($records[$k]);
                    }

                    if (isset($section_conditions['action_name']) && $section_conditions['action_name'] != $records[$k]['action']) {
                        unset($records[$k]);
                    }

                } else {
                    unset($records[$k]);
                }

            }

            usort($records, function ($a, $b) {
                return strcmp($a['role']["name"], $b['role']["name"]);
            });

            foreach(SectionRoutes::ALLOW_ROUTES_FOR_ANY_LOGIN_USER as $route_name)
            {
                $records[] = [
                    'section' => '',
                    'action' => $route_name,
                    'can_be_delete' => false,                    
                    'role' => [
                        'name' => 'Allow For All Login User'
                    ]
                ];
            }

            //d($conditions); exit;
            if ($conditions && isset($conditions["role_id"]) && $conditions["role_id"])
            {
                $role = Role::findOrFail($conditions["role_id"]); 
                if ($role->code)
                {
                    foreach(SectionRoutes::ALLOW_ROUTES_FOR_SYSTEM_ADMIN as $route_name)
                    {
                        $records[] = [
                            'section' => '',
                            'action' => $route_name,
                            'can_be_delete' => false,                    
                            'role' => [
                                'name' => 'Allow For All System Admin User'
                            ]
                        ];
                    }
                }
            }

=======
            ["field" => "section_list_key", "type" => "", "view_field" => "section_list_key"],
        ], true);

        // d($section_conditions);

        $sections = SectionRoutes::get();

        // d($sections);

        $section_list = SectionRoutes::getList($sections);

        // d($section_list);

        if ($conditions || $section_conditions) {
            $role_id_list = isset($conditions['role_id']) && $conditions['role_id'] ? [$conditions['role_id']['value']] : [];

            $choosen_route_list = isset($section_conditions['section_list_key']) && $section_conditions['section_list_key'] ? get_value_from_list($section_list, $section_conditions['section_list_key']) : [];

            $records = AccessControl::getAllowedRoutes($role_id_list, $choosen_route_list);

            // d($records, true);
>>>>>>> base_v3

            $this->setForView(compact("records"));
        }

        $role_list = Role::getList();

<<<<<<< HEAD
        $keys = array_keys($sections);
        $section_list = array_combine($keys, $keys);

=======
>>>>>>> base_v3
        $this->setForView(compact("role_list", "section_list"));

        return $this->view(__FUNCTION__);
    }

    public function assign(Request $request)
    {
        if ($request->isMethod('post')) {
<<<<<<< HEAD
            $sections = SectionRoutes::get();

=======
>>>>>>> base_v3
            $accessControl = AccessControl::init();
            $synced_route_name_list = $accessControl->syncRouteNamesToDatabase();

            $data = $request->all();

<<<<<<< HEAD
            $role = Role::findOrFail($data['role_id']);

            //d($sections);

            $choosen_route_list = [];
            foreach ($data['data'] as $section_name => $data_actions) {
                if (isset($sections[$section_name])) {
                    foreach ($data_actions as $action_name) {
                        if (isset($sections[$section_name][$action_name])) {
                            $choosen_route_list = array_merge($choosen_route_list, $sections[$section_name][$action_name]);
                        }
                    }
=======
            $roles = Role::where("id", $data['role_id']);

            $choosen_route_list = [];

            $counters = [
                "new"  => 0,
                "deleted"  => 0,
            ];

            foreach ($data['route_names'] as $route_name) {
                if (strpos($route_name, ",") !== false)
                {
                    $route_names = explode(",", $route_name);

                    $choosen_route_list = array_merge($choosen_route_list, $route_names);
                }
                else
                {
                    $choosen_route_list[] = $route_name;
>>>>>>> base_v3
                }
            }

            $choosen_route_list = array_unique($choosen_route_list);

<<<<<<< HEAD
            $roleRouteName = new RoleRouteName();

            $old_save_id_list = RoleRouteName::where("role_id", "=", $role->id)->pluck("id", "id");
            foreach ($choosen_route_list as $route_name) {
                if (!isset($synced_route_name_list[$route_name])) {
                    die("$route_name not found. may be you forgot to add in SectionRoutes.php");
                }

                $saved_id = $roleRouteName->insertIgnoreIfExist([
                    "role_id" => $data['role_id'],
                    "route_name_id" => $synced_route_name_list[$route_name],
                ]);

                unset($old_save_id_list[$saved_id]);
            }

            RoleRouteName::destroy($old_save_id_list);

            $user_id_list = User::pluck("id")->toArray();
=======
            // d($choosen_route_list, true);

            $is_insert = false;

            $new_permission_count = 0;

            $old_role_route_names = RoleRouteName::with(["routeName"])->whereIn("role_id", $data['role_id'])->get();

            $old_save_routes = [];

            foreach($old_role_route_names as $role_route_name)
            {
                $old_save_routes[$role_route_name->id] = $role_route_name;
            }

            $sections = SectionRoutes::getList();

            $route_section_list = [];

            foreach($sections as $label => $route_names)
            {
                foreach($route_names as $route_name)
                {
                    $route_section_list[$route_name] = $label;
                }
            }

            foreach ($choosen_route_list as $route_name) {
                if (!isset($synced_route_name_list[$route_name])) {
                    throw new UserException("$route_name not found");
                }

                foreach($data['role_id'] as $role_id)
                {
                    $roleRouteName = RoleRouteName::insertIgnoreIfExist([
                        "role_id" => $role_id,
                        "route_name_id" => $synced_route_name_list[$route_name],
                    ], $is_insert, true);

                    if ($is_insert)
                    {
                        $save_data = [
                            "type" => "NEW",
                            "role_id" => $role_id,
                            "route_name" => $route_name,
                            "section_name" => get_value_from_list($route_section_list, $route_name),
                        ];

                        PermissionChangeLog::create($save_data);

                        $counters['new']++;
                    }

                    unset($old_save_routes[$roleRouteName->id]);
                }
            }

            RoleRouteName::withoutEvents(function() use ($old_save_routes){
                RoleRouteName::destroy(array_keys($old_save_routes));
            });

            foreach($old_save_routes as $old_save_route)
            {
                $save_data = [
                    "type" => "DELETE",
                    "role_id" => $old_save_route->role_id,
                    "route_name" => $old_save_route->routeName->name,
                    "section_name" => get_value_from_list($route_section_list, $old_save_route->routeName->name),
                ];

                PermissionChangeLog::create($save_data);

                $counters['deleted']++;
            }

            $user_id_list = User::whereHas("roles", function ($q) use ($data) {
                $q->whereIn("role_id", $data['role_id']);
            })->pluck("id")->toArray();
>>>>>>> base_v3

            $accessControl->clearMenuCache($user_id_list);

            $this->saveSqlLog();

<<<<<<< HEAD
            Session::flash('success', 'Permission are saved');
=======
            $msg_list = [];

            foreach($counters as $label => $count)
            {
                $label = str_function_name_to_human_text($label);
                $msg_list[] = "$label : $count";
            }

            Session::flash('success', 'Permissions are saved, ' . implode(", ", $msg_list));
>>>>>>> base_v3
        }

        $role_list = Role::getList();

        $this->setForView(compact("role_list"));

        return $this->view(__FUNCTION__);
    }

<<<<<<< HEAD
    public function ajax_get_permissions($role_id)
    {
        $role_route_names = RoleRouteName::select(['role_id', 'route_name_id'])->where("role_id", "=", $role_id)
            ->with(['routeName' => function ($query) {
                $query->select(['id', 'name']);
            }])->get();

        $saved_route_list = [];

        foreach ($role_route_names->toArray() as $role_route) {
            $saved_route_list[] = $role_route['route_name']['name'];
        }

        $sections = SectionRoutes::get();

        foreach ($sections as $section_name => $actions) {
            foreach ($actions as $action_name => $route_list) {
                unset($actions[$action_name]);

                $is_checked = false;
                foreach ($route_list as $route_name) {
                    if (in_array($route_name, $saved_route_list)) {
                        $is_checked = true;
                    }
                }

                $actions[$action_name]['is_checked'] = $is_checked;
            }

            $sections[$section_name] = $actions;
        }
=======
    public function ajax_get_permissions()
    {
        $request = request();

        $role_ids = $request->get("role_ids", []);

        if ($role_ids && !is_array($role_ids))
        {
            $role_ids = explode(",", $role_ids);
        }

        $sections = SectionRoutes::get($role_ids);
>>>>>>> base_v3

        $this->setForView(compact("sections"));

        $this->layout = "backend.layouts.ajax";

        return $this->view(__FUNCTION__);
    }

    public function ajax_delete(Request $request)
    {
        $respone = ["status" => 0, "msg" => "Unkown Error"];

        try {
            $id = $request->get("id");

<<<<<<< HEAD
            $record = RoleRouteName::select('id')->findOrFail($id);

            $record->delete();
=======
            $id_list = explode(",", $id);

            DB::beginTransaction();

            $records = RoleRouteName::whereIn("id", $id_list)->get();

            $role_list = [];

            $sections = SectionRoutes::getList();

            $route_section_list = [];

            foreach($sections as $label => $route_names)
            {
                foreach($route_names as $route_name)
                {
                    $route_section_list[$route_name] = $label;
                }
            }

            foreach($records as $record)
            {
                $role_list[] = $record->role_id;

                $record->delete();

                $save_data = [
                    "type" => "DELETE",
                    "role_id" => $record->role_id,
                    "route_name" => $record->routeName->name,
                    "section_name" => get_value_from_list($route_section_list, $record->routeName->name),
                ];

                PermissionChangeLog::create($save_data);
            }

            $user_id_list = User::whereHas("roles", function ($q) use ($role_list) {
                $q->whereIn("user_roles.role_id", $role_list);
            })->pluck("id")->toArray();

            $accessControl = AccessControl::init();

            $accessControl->clearMenuCache($user_id_list);

            DB::commit();
>>>>>>> base_v3

            $respone["status"] = 1;
            $respone["msg"] = "Success";
        } catch (Exception $ex) {
<<<<<<< HEAD
=======
            DB::rollBack();
>>>>>>> base_v3
            $respone['msg'] = $ex->getMessage();
        }

        return $this->responseJson($respone);
    }
<<<<<<< HEAD
=======

    public function ajax_request_access($route_name)
    {
        $respone = ["status" => 0, "msg" => "Unkown Error"];

        try {
            if (!$route_name)
            {
                throw new Exception("Route Name : $route_name not is found");
            }

            $auth_user = Auth::user();

            $notification_data = [
                'from_user_id' => $auth_user->id,
                "sub_type" => Notification::SUB_TYPE_CRITICAL,
                "msg" => $auth_user->getDisplayName() .  " is requesting for permission"
            ];

            $section_list = SectionRoutes::getList();

            foreach($section_list as $label => $route_list)
            {
                if (in_array($route_name, $route_list))
                {
                    $notification_data['msg'] .= " of " . $label;
                    break;
                }
            }

            $users = User::whereHas("roles", function($q){
                $q->where("code", Role::TYPE_SYSTEM_ADMIN);
            })->get();

            $notifyObj = new GeneralNotification($notification_data);

            foreach($users as $user)
            {
                $notification_class_name = str_class_name_without_namespace(GeneralNotification::class);

                $count = Notification::where("notifiable_id", $user->id)
                            ->whereNull("read_at")
                            ->where("type", 'like', '%'. $notification_class_name)
                            ->where("sub_type", Notification::SUB_TYPE_CRITICAL)
                            ->where("from_user_id", $notification_data['from_user_id'])
                            ->count();

                if ($count == 0)
                {
                    $user->notify($notifyObj);
                }
            }

            $db_logs = LaravelExtend::getQueryLog("", -5);

            LaravelExtend::writeQueryLog($db_logs);

            $respone["status"] = 1;
            $respone["msg"] = "Success";
        } catch (Exception $ex) {
            $respone["status"] = 0;
            $respone['msg'] = $ex->getMessage();
            throw $ex;
        }

        return $this->responseJson($respone);
    }
>>>>>>> base_v3
}
