<?php

namespace App\Http\Controllers\Backend;

use App\Acl\AccessControl;
use App\Acl\SectionRoutes;
use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\Notification;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ["field" => "section_list_key", "type" => "", "view_field" => "section_list_key"],
        ], true);

        // d($section_conditions);

        $sections = SectionRoutes::get();

        // d($sections);

        $section_list = SectionRoutes::getList($sections);

        // d($section_list);

        if ($conditions || $section_conditions) {

            $role_id_list = isset($conditions['role_id']) && $conditions['role_id'] ? [$conditions['role_id']] : [];

            $choosen_route_list = isset($section_conditions['section_list_key']) && $section_conditions['section_list_key'] ? get_value_from_list($section_list, $section_conditions['section_list_key']) : [];

            $records = AccessControl::getAllowedRoutes($role_id_list, $choosen_route_list);

            // d($records, true);
            
            $this->setForView(compact("records"));
        }

        $role_list = Role::getList();

        $this->setForView(compact("role_list", "section_list"));

        return $this->view(__FUNCTION__);
    }

    public function assign(Request $request)
    {
        if ($request->isMethod('post')) {
            $accessControl = AccessControl::init();
            $synced_route_name_list = $accessControl->syncRouteNamesToDatabase();

            $data = $request->all();

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
                }
            }

            $choosen_route_list = array_unique($choosen_route_list);

            // d($choosen_route_list, true);

            $is_insert = false;

            $new_permission_count = 0;

            $old_save_id_list = RoleRouteName::whereIn("role_id", $data['role_id'])->pluck("id", "id")->toArray();

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
                        $counters['new']++;
                    }

                    unset($old_save_id_list[$roleRouteName->id]);
                }
            }

            RoleRouteName::withoutEvents(function() use ($old_save_id_list){
                RoleRouteName::destroy($old_save_id_list);            
            });

            $counters['deleted'] = count($old_save_id_list);

            $user_id_list = User::whereHas("roles", function ($q) use ($data) {
                $q->whereIn("role_id", $data['role_id']);
            })->pluck("id")->toArray();

            $accessControl->clearMenuCache($user_id_list);

            $this->saveSqlLog();

            $msg_list = [];

            foreach($counters as $label => $count)
            {
                $label = str_function_name_to_human_text($label);
                $msg_list[] = "$label : $count";
            }

            Session::flash('success', 'Permissions are saved, ' . implode(", ", $msg_list));
        }

        $role_list = Role::getList();

        $this->setForView(compact("role_list"));

        return $this->view(__FUNCTION__);
    }

    public function ajax_get_permissions()
    {
        $request = request();

        $role_ids = $request->get("role_ids", []);

        if ($role_ids && !is_array($role_ids))
        {
            $role_ids = explode(",", $role_ids);
        }

        $sections = SectionRoutes::get($role_ids);

        $this->setForView(compact("sections"));

        $this->layout = "backend.layouts.ajax";

        return $this->view(__FUNCTION__);
    }

    public function ajax_delete(Request $request)
    {
        $respone = ["status" => 0, "msg" => "Unkown Error"];

        try {
            $id = $request->get("id");

            $id_list = explode(",", $id);
            
            DB::beginTransaction();

            $records = RoleRouteName::whereIn("id", $id_list)->get();

            $role_list = [];

            foreach($records as $record)
            {
                $role_list[] = $record->role_id;

                RoleRouteName::withoutEvents(function() use($record){
                    $record->delete();
                });
            }

            $user_id_list = User::whereHas("roles", function ($q) use ($role_list) {
                $q->whereIn("user_roles.role_id", $role_list);
            })->pluck("id")->toArray();

            $accessControl = AccessControl::init();

            $accessControl->clearMenuCache($user_id_list);

            DB::commit();

            $respone["status"] = 1;
            $respone["msg"] = "Success";
        } catch (Exception $ex) {
            DB::rollBack();
            $respone['msg'] = $ex->getMessage();
        }

        return $this->responseJson($respone);
    }

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
}
