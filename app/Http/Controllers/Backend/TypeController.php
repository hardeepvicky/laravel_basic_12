<?php

namespace App\Http\Controllers\Backend;

<<<<<<< HEAD
=======
use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\FieldSetting;
use App\Models\Task;
>>>>>>> base_v3
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TypeController extends BackendController
{
    public String $routePrefix = "type";

    protected $modelClass = Type::class;

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
=======
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string", "view_field" => "name"],
            ["field" => "code", "type" => "string", "view_field" => "code"],
>>>>>>> base_v3
            ["field" => "type", "type" => "string", "view_field" => "type"],
            ["field" => "short_name", "type" => "string", "view_field" => "short_name"],
            ["field" => "is_active", "type" => "", "view_field" => "is_active"],
        ]);

        $builder = $this->modelClass::where($conditions);

<<<<<<< HEAD
        if ($apply_sort)
        {
=======
        if ($apply_sort) {
>>>>>>> base_v3
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

<<<<<<< HEAD
=======
    protected function beforeViewRender()
    {
        $type_list = Type::getTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);
        
        $this->setForView(compact("type_list", "field_labels"));

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
            "type" => ["required"],
            "name" => ["required"],
            "code" => ["required"],
            "list_display_order" => ["nullable"],
            "short_name" => ["nullable"],
>>>>>>> base_v3
            "is_active" => ""
        ];
    }

    private function _common_validation_messages()
    {
<<<<<<< HEAD
        return [
            "name.required" => "Name is required",
            "name.unique" => "Name already exist",
            "type.required" => "Source is required",
            "type.unique" => "Source already exist",
        ];
=======
        return [];
>>>>>>> base_v3
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

<<<<<<< HEAD
        $messages = $this->_common_validation_messages();

        $rules = array_merge($rules, [
            'name' => 'required|min:2|unique:' . $this->tableName,
            'type' => 'required|unique:' . $this->tableName,
            'short_name' => 'required|unique:' . $this->tableName,
        ]);

        $messages = array_merge($messages, [

        ]);

=======
        $model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

>>>>>>> base_v3
        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

<<<<<<< HEAD
        try
        {
            $model = $this->modelClass::create($validatedData);
            
            $this->updateUserInfoAfterSave($model);
            
            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            
=======
        try {
            $model = $this->modelClass::create($validatedData);

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

>>>>>>> base_v3
            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function edit($id)
    {
        $model = $this->modelClass::findOrFail($id);

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
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

<<<<<<< HEAD
        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, [

        ]);

        $table_name = $this->tableName;

        $rules = array_merge($rules, [
            'name' => "required|min:3|unique:$table_name,name,$model->id",
            'type' => "required|unique:$table_name,type,$model->id",
            'short_name' => "required|unique:$table_name,short_name,$model->id",
        ]);

=======
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

>>>>>>> base_v3
        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

<<<<<<< HEAD
        try
        {
            $model = $this->modelClass::findOrFail($id);
=======
        try {
            // d($validatedData, true);
>>>>>>> base_v3

            $model->fill($validatedData);

            $model->save();

            $this->updateUserInfoAfterSave($model);

            DB::commit();

<<<<<<< HEAD
            $this->saveSqlLog();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
=======
            return $this->afterSave($model);
        } catch (Exception $ex) {
>>>>>>> base_v3
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }
<<<<<<< HEAD
}

=======

    public function copy($id)
    {
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function ajax_get_code_list($type)
    {
        $response = ["status" => 1, "data" => []];

        try {

            $codes = Type::getCodes($type);

            if (!isset($codes['code_list']))
            {
                throw new UserException("code_list key is not found in codes varibale");
            }

            foreach ($codes['code_list'] as $code) {
                $response['data'][] = [
                    'id' => $code,
                    'name' => $code,
                ];
            }
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
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
>>>>>>> base_v3
