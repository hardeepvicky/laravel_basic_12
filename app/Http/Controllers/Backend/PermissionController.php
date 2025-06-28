<?php

namespace App\Http\Controllers\Backend;

use App\Acl\AccessControl;
use App\Acl\SectionRoutes;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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

        $section_list = SectionRoutes::getList($sections);

        if ($conditions || $section_conditions) {

            $role_id_list = isset($conditions['role_id']) && $conditions['role_id'] ? [$conditions['role_id']] : [];

            $records = AccessControl::getAllowedRoutes($role_id_list, $section_conditions['section_list_key'] ?? null);

            usort($records, function ($a, $b) {
                return strcmp($a["role"]['name'], $b['role']["name"]);
            });

            $this->setForView(compact("records"));
        }

        $role_list = Role::getList();

        $this->setForView(compact("role_list", "section_list"));

        return $this->view(__FUNCTION__);
    }

    public function assign(Request $request)
    {
        if ($request->isMethod('post')) {
            $sections = SectionRoutes::get();

            $accessControl = AccessControl::init();
            $synced_route_name_list = $accessControl->syncRouteNamesToDatabase();

            $data = $request->all();

            $role = Role::findOrFail($data['role_id']);

            $choosen_route_list = [];

            // dd($data['data']);
            foreach ($data['route_keys'] as $route_key) {
                $routes = SectionRoutes::getRoutesFromKey($route_key, $sections);

                $choosen_route_list = array_merge($routes, $choosen_route_list);
            }

            $choosen_route_list = array_unique($choosen_route_list);

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

            $user_id_list = User::whereHas("roles", function ($q) use ($role) {
                $q->where("user_roles.role_id", $role->id);
            })->pluck("id")->toArray();

            $accessControl->clearMenuCache($user_id_list);

            $this->saveSqlLog();

            Session::flash('success', 'Permissions are saved');
        }

        $role_list = Role::getList();

        $this->setForView(compact("role_list"));

        return $this->view(__FUNCTION__);
    }

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

        foreach ($sections as $section_name => $sub_sections) {
            foreach ($sub_sections as $sub_section_name => $actions) {
                foreach ($actions as $action_name => $action_arr) {

                    $is_checked = false;
                    foreach ($action_arr['routes'] as $route_name) {
                        if (in_array($route_name, $saved_route_list)) {
                            $is_checked = true;
                        }
                    }

                    $actions[$action_name]['is_checked'] = $is_checked;
                }

                $sections[$section_name][$sub_section_name] = $actions;
            }
        }

        $this->setForView(compact("sections"));

        $this->layout = "backend.layouts.ajax";

        return $this->view(__FUNCTION__);
    }

    public function ajax_delete(Request $request)
    {
        $respone = ["status" => 0, "msg" => "Unkown Error"];

        try {
            $id = $request->get("id");

            $record = RoleRouteName::select('id')->findOrFail($id);

            $record->delete();

            $respone["status"] = 1;
            $respone["msg"] = "Success";
        } catch (Exception $ex) {
            $respone['msg'] = $ex->getMessage();
        }

        return $this->responseJson($respone);
    }
}
