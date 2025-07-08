<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DepartmentController extends BackendController
{
    public String $routePrefix = "department";

    protected $modelClass = Department::class;

    protected Department | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d(LaravelExtend::getQueryLog("", -3));

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string", "view_field" => "name"],
            ["field" => "is_active", "type" => "", "view_field" => "is_active"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "id"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);
        
        $builder->with(["headUser"]);

        if ($apply_sort)
        {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass;

            $sort = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort['sort_by'], $sort['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $user_conditions = [
            "type" => User::TYPE_INTERNAL,
        ];

        if ($this->model->dept_head_user_id) {
            $user_conditions['or_id'] = $this->model->dept_head_user_id;
        }

        $user_list = User::getList("id", "display_name", $user_conditions);

        $field_labels = $this->model->getFieldLabels();

        $this->setForView(compact("user_list", "field_labels"));

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
            "dept_head_user_id" => ["nullable"],
            "list_display_order" => "",
            "is_active" => ""
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "Department name is required",
            "name.unique" => "Department name already exist",
        ];
    }

    public function store(Request $request)
    {
        $this->model = new $this->modelClass();

        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $model = $this->modelClass::create($validatedData);

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }
            
            $this->updateUserInfoAfterSave($model);
            
            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            
            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            $this->model->fill($save_data);

            $this->model->save();

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }
}
