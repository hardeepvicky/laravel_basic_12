<?php

namespace App\Http\Controllers\Backend;

use App\Models\State;
use App\Models\EmployeeDocumentFile;
use App\Models\DocumentFile;
use App\Models\City;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Type;
use App\Rules\IndianMobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class EmployeeController extends BackendController
{
    public String $routePrefix = "employee";

    protected $modelClass = Employee::class;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        $this->_set_data_for_add_and_edit();

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
            ["field" => "department_id", "type" => "int"],
            ["field" => "designation_id", "type" => "int"],
            ["field" => "is_active", "type" => ""],
        ]);

        $builder = $this->modelClass::where($conditions);

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

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->_set_data_for_add_and_edit();

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _set_data_for_add_and_edit($model = null)
    {
        $department_list = Department::getList("id", "name");
        $designation_list = Designation::getList("id", "name");
        $state_list = State::getList("id", "name");

        $this->setForView(compact('department_list', 'designation_list', 'state_list'));
    }

    private function _common_validation_rules()
    {
        return [
            'name' => 'required|min:3',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'permanent_address' => 'required|min:20',
            'current_address' => 'required|min:20',
            'current_state_id' => 'required',
            'current_city_id' => 'required',
            'permanent_state_id' => 'required',
            'permanent_city_id' => 'required',
            "mobile" => ["required", new IndianMobileNumber()],
            "mobile2" => ["nullable", new IndianMobileNumber()],
            "whatsapp_mobile" => ["required", new IndianMobileNumber()],
            "email" => ["nullable", "email"],
            "dob" => ["required"],
            "doj" => ["required"],
            "is_active" => ""
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

        $messages = $this->_common_validation_messages();

        $rules = array_merge($rules, []);

        $table_name = (new $this->modelClass)->getTable();
        $rules['mobile'][] = "unique:$table_name,mobile";
        $rules['whatsapp_mobile'][] = "unique:$table_name,whatsapp_mobile";

        // d($rules, true);

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $model = $this->modelClass::create($validatedData);

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

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

        $this->_set_data_for_add_and_edit($model);

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $table_name = (new $this->modelClass)->getTable();
        $rules['mobile'][] = "unique:$table_name,mobile,$id";
        $rules['whatsapp_mobile'][] = "unique:$table_name,whatsapp_mobile, $id";

        // d($rules, true);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $model = $this->modelClass::findOrFail($id);

            $model->fill($validatedData);

            $model->save();

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }
}
