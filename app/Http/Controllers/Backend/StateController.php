<?php

namespace App\Http\Controllers\Backend;

<<<<<<< HEAD
=======
use App\Exceptions\UserException;
use App\Models\Country;
use App\Models\FieldSetting;
>>>>>>> base_v3
use App\Models\State;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class StateController extends BackendController
{
    public String $routePrefix = "state";

    protected $modelClass = State::class;

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
            ["field" => "country_id", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
>>>>>>> base_v3
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
        $country_list = Country::getListCache();

        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("country_list", "my_country", "field_labels"));

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
            "country_id" => ["required"],
            "name" => ["required"],
            "short_name" => ["nullable"],
            "is_active" => [""],
>>>>>>> base_v3
        ];
    }

    private function _common_validation_messages()
    {
        return [
<<<<<<< HEAD
            "name.required" => "Departname name is required",
            "name.unique" => "Departname name already exist",
=======
            "name.required" => "State is required",
            "name.unique" => "State already exist",
>>>>>>> base_v3
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

<<<<<<< HEAD
        $rules = $this->_common_validation_rules();

        $messages = $this->_common_validation_messages();

        $rules = array_merge($rules, [
            'name' => 'required|min:2|unique:' . $this->tableName,
            'code' => 'required|min:1|unique:' . $this->tableName,
        ]);

        $messages = array_merge($messages, [

        ]);
=======
        $model = new $this->modelClass();

        $rules = $this->_common_validation_rules();

        $rules = array_merge_recursive($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);
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
            'code' => "required|min:1|unique:$table_name,code,$model->id",
        ]);
=======
        $rules = array_merge_recursive($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);
>>>>>>> base_v3

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

<<<<<<< HEAD
        try
        {
=======
        try {
>>>>>>> base_v3
            $model = $this->modelClass::findOrFail($id);

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
=======

    
    public function ajax_get_list($country_id)
    {
        $response = ["status" => 1];

        $conditions = [
            "country_id" => $country_id
        ];

        $list = $this->modelClass::getList("id", "display_name", $conditions, "name");

        $response['data'] = [];

        foreach($list as $k => $v)
        {
            $response['data'][] = [
                'id' => $k,
                'name' => $v,
            ];
        }

        return $this->responseJson($response);
    }
>>>>>>> base_v3
}
