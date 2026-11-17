<?php

namespace App\Http\Controllers\Backend;

use App\Acl\AccessControl;
use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Helpers\Menu;
use App\Models\AppMenu;
use App\Models\AppMenuRelatedLink;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class AppMenuController extends BackendController
{
    public String $routePrefix = "app-menu";

    protected $modelClass = AppMenu::class;

    protected AppMenu | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        foreach($records as $k => $record)
        {
            $record->direct_child_count = $record->child->count();
            $record->getAllChildCount();
        }

        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "parent_id", "type" => "int", "view_field" => "parent_id"],
            ["field" => "is_find_nested_child", "type" => "bool", "view_field" => "is_find_nested_child"],
            ["field" => "name", "type" => "string"],
            ["field" => "route_name", "type" => "string"],
            ["field" => "is_link", "type" => "", "view_field" => "is_link"],
            ["field" => "is_active", "type" => "", "view_field" => "is_active"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["parent_id"]) && $condition_list["parent_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        )
        {
            $child_id_list = AppMenu::getChildIdList([$condition_list["parent_id"]]);

            if ($child_id_list)
            {
                $builder->whereIn("id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["parent_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }
        
        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        if($conditions)
        {
            $builder = $builder->where($conditions);
        }

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $sort_params = $this->getRequestData($cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $routes = LaravelExtend::getRoutes();

        foreach($routes as $route)
        {
            $route_list[$route['route_name']] = $route['route_name'];
        }

        // dd($route_list);

        $tree_list = AppMenu::getTreeList([
            ["is_link", 0]
        ]);

        $static_menu_list = Menu::getStaticMenuList();

        $this->setForView(compact("route_list", "tree_list", "static_menu_list"));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "menu_display_order" => ["required"],
            "department_id" => [],
            "parent_id" => ["nullable"],
            "css_class" => ["nullable"],
            "icon_css_class" => ["nullable"],
            "show_in_static_menu" => ["nullable"],
            "is_link" => "",
            "is_active" => "",
            "appMenuRelatedLink" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // dd($validatedData);
            $this->model = $this->modelClass::create($validatedData);

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $accessControl = new AccessControl();
            $accessControl->clearMenuCache();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with("appMenuRelatedLink")->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $appMenuRelatedLink = $this->model->appMenuRelatedLink->toArray();

        // d($appMenuRelatedLink, true);

        $this->setForView(compact("model", "form", "appMenuRelatedLink"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::with("appMenuRelatedLink")->findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));


        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            unset($save_data['appMenuRelatedLink']);

            if (!isset($save_data['show_in_static_menu']))
            {
                $save_data['show_in_static_menu'] = [];
            }

            // d($save_data, true);

            $this->model->fill($save_data);

            $this->model->save();

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $accessControl = new AccessControl();
            $accessControl->clearMenuCache();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }

    
    private function _saveOthers($validatedData)
    {
        unset($validatedData['appMenuRelatedLink']['(sr-id)']);
        
        if (isset($validatedData['appMenuRelatedLink'])) {
            
            $this->model->appMenuRelatedLink()->delete();

            $rules = [
                "name" => ["required"],
                "route_name_array" => ["required"],
            ];

            $child_model = new AppMenuRelatedLink();

            $messages = [   
            ];

            foreach ($validatedData['appMenuRelatedLink'] as $record) {
                try {
                    // d($record); exit;
                    $record['app_menu_id'] = $this->model->id;
                    $new_rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($record));
                    
                    $validator = Validator::make($record, $new_rules, $messages);
                    
                    if ($validator->fails()) {
                        $error_msgs = $validator->messages()->all();
                        $this->addError("", $error_msgs, "Related Link");
                    } else {
                        unset($record['id']);
                        $this->model->appMenuRelatedLink()->create($record);
                    }
                } 
                catch(UserException $ex)
                {
                    if ($ex->getCode() != UserException::ERROR_USER_DATA_WRONG)
                    {
                        $this->addError("", $ex->getMessage(), "Related Link");
                    }
                }
                catch (Exception $ex) {
                    $this->addError("", $ex->getMessage(), "Related Link");
                }
            }
        }

        if ($this->custom_errors)
        {
            // d($this->custom_errors); exit;
        }
    }

    public function copy($id)
    {
        $model = $this->model = $this->modelClass::with("appMenuRelatedLink")->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $appMenuRelatedLink = $this->model->appMenuRelatedLink->toArray();

        $this->setForView(compact("model", "form", "appMenuRelatedLink"));

        return $this->view("form");
    }

    public function ajax_get_list($type)
    {
        $response = ["status" => 1];

        try {

            $records = $this->modelClass::where("type", $type)->get();

            $response['data'] = [];

            foreach ($records as $record) {
                $row = [
                    "id" => $record->id,
                    "name" => $record->getDisplayName(),
                ];

                $response['data'][] = $row;
            }
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }
}
