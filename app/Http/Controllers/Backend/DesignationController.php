<?php

namespace App\Http\Controllers\Backend;

<<<<<<< HEAD
use App\Models\Designation;
=======
use App\Helpers\LaravelExtend;
use App\Models\Designation;
use App\Models\FieldSetting;
>>>>>>> base_v3
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DesignationController extends BackendController
{
    public String $routePrefix = "designation";

    protected $modelClass = Designation::class;

<<<<<<< HEAD
=======
    protected Designation | null $model = null;

>>>>>>> base_v3
    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
<<<<<<< HEAD
            ["field" => "name", "type" => "string", "view_field" => "name"],
            ["field" => "is_active", "type" => "", "view_field" => "is_active"],
=======
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
            ["field" => "short_name", "type" => "string"],
            ["field" => "level", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
>>>>>>> base_v3
        ]);

        $builder = $this->modelClass::where($conditions);

        if ($apply_sort)
        {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $sort_params = $this->getRequestData($cache_key_for_sort, [
<<<<<<< HEAD
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
=======
                ["key" => "sort_by", "default" => "list_display_order"],
                ["key" => "sort_dir", "default" => "ASC"],
>>>>>>> base_v3
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

<<<<<<< HEAD
=======
    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("field_labels"));

        return parent::beforeViewRender();
    }

>>>>>>> base_v3
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
<<<<<<< HEAD
=======
            "name" => ["required"],
            "short_name" => ["required"],
            "level" => ["required"],
            "list_display_order" => "",
>>>>>>> base_v3
            "is_active" => ""
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "Designation name is required",
            "name.unique" => "Designation name already exist",
        ];
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
=======
        $this->model = new $this->modelClass();

>>>>>>> base_v3
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

<<<<<<< HEAD
        $messages = $this->_common_validation_messages();

        $rules = array_merge($rules, [
            'name' => 'required|min:2|unique:' . $this->tableName,
        ]);

        $messages = array_merge($messages, [

        ]);

=======
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

>>>>>>> base_v3
        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $model = $this->modelClass::create($validatedData);
<<<<<<< HEAD
=======

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }
>>>>>>> base_v3
            
            $this->updateUserInfoAfterSave($model);
            
            DB::commit();

<<<<<<< HEAD
            $this->saveSqlLog();

=======
>>>>>>> base_v3
            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            
            $this->saveSqlLog();

<<<<<<< HEAD
            return back()->withInput()->with('fail', $ex->getMessage());
=======
            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
>>>>>>> base_v3
        }
    }

    public function edit($id)
    {
<<<<<<< HEAD
        $model = $this->modelClass::findOrFail($id);
=======
        $model = $this->model = $this->modelClass::findOrFail($id);
>>>>>>> base_v3

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
<<<<<<< HEAD
        $model = $this->modelClass::findOrFail($id);
=======
        $model = $this->model = $this->modelClass::findOrFail($id);
>>>>>>> base_v3

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

<<<<<<< HEAD
        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, [

        ]);

        $table_name = $this->tableName;

        $rules = array_merge($rules, [
            'name' => "required|min:3|unique:$table_name,name,$model->id",
        ]);

=======
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

>>>>>>> base_v3
        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

<<<<<<< HEAD
        try
        {
            $model = $this->modelClass::findOrFail($id);

            $model->fill($validatedData);

            $model->save();

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
=======
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
>>>>>>> base_v3
            DB::rollBack();

            $this->saveSqlLog();

<<<<<<< HEAD
            return back()->withInput()->with('fail', $ex->getMessage());
=======
            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
>>>>>>> base_v3
        }
    }
}
