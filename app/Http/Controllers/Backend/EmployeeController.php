<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\DropDownValue;
use App\Models\Employee;
use App\Models\FieldSetting;
use App\Models\State;
use Illuminate\Http\Request;
use App\Rules\MobileNumber;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class EmployeeController extends BackendController
{
    public String $routePrefix = "employee";

    protected $modelClass = Employee::class;

    protected Employee | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }


    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix;

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "department_id", "type" => "int"],
            ["field" => "designation_id", "type" => "int"],
        ]);

        $cache_key = $cache_key_prefix . "-2";

        $builder = $this->modelClass::where($conditions);

        $builder->with(["currentState", "currentCity", "permanentState", "permanentCity", "department", "designation"]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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

        $dept_list = Department::getList("id", "display_name", [], "list_display_order");

        $desg_list = Designation::getList("id", "display_name", [], "list_display_order");

        $type_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::EMPLOYEE], "list_display_order");

        $state_list = State::getIndianList();

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact(
            "type_list", "state_list", "branch_list", "dept_list", "desg_list",
            "field_labels"
        ));

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
            'type' => ['required'],
            'name' => ['required'],
            'email' => ['nullable'],
            'department_id' => ['required'],
            'designation_id' => ['required'],
            'branch_id' => ['required'],
            'mobile' => ['required', new MobileNumber],
            'mobile2' => ['nullable', new MobileNumber],
            'whatsapp_mobile' => ['nullable', new MobileNumber],
            "photo" => "",
            "dob" => ["required"],
            "doj" => ["required"],
            "current_state_id" => ["required"],
            "current_city_id" => ["required"],
            "current_address" => ["required"],
            "current_address2" => "",
            "permanent_state_id" => ["required"],
            "permanent_city_id" => ["required"],
            "permanent_address" => ["required"],
            "permanent_address2" => "",
            "salary_type" => "",
            "salary_payment_mode" => "",
            "dont_send_sms" => "",
            "dont_send_whatsapp_msg" => "",
            "is_mobile_hide" => "",
            "is_active" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            
            $save_data = $this->_saveProfilePhoto($validatedData);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();
            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();
            $this->saveSqlLog();
            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function edit($id)
    {
        $model = $this->modelClass::with([])->findOrFail($id);
        $model->afterFind();

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
        // dd($request->all());
        $this->model = $this->modelClass::with([])->findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $validatedData = $this->_saveProfilePhoto($validatedData);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $this->model->fill($save_data);
            if (!$this->model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->saveSqlLog();

            return back()->with('fail', get_exception_display_message($ex))->withInput()->with("custom_errors", $this->custom_errors);
        }
    }

    private function _saveProfilePhoto(array $validatedData)
    {
        $validatedData['photo'] = trim($validatedData['photo']);
        if ($validatedData['photo']) {
            $ext = pathinfo($validatedData['photo'], PATHINFO_EXTENSION);

            $disk = $this->model->getStorageDisk();
            $path = $this->model->getStoragePath();
            if ($this->model->id) {
                $filename = $this->model->id . "." . $ext;
            } else {
                $filename = $this->model->getNextId() . "." . $ext;
            }

            $dest = $disk->path($path . $filename);

            $new_file = FileUtility::move($validatedData['photo'], $dest, TRUE);

            if ($new_file === false) {
                throw new UserException("Fail To move file from temp folder to users folder");
            }

            $new_file = pathinfo($new_file, PATHINFO_BASENAME);

            $validatedData['photo'] = $path . $new_file;
        } else {
            unset($validatedData['photo']);
        }

        return $validatedData;
    }


    private function _saveOthers($validatedData)
    {
        
    }

    public function ajax_get_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "department_id", "type" => "int"],
                ["field" => "desgination_id", "type" => "int"],
                ["field" => "branch_id", "type" => "int"],
            ];

            $conditions = $this->getConditions(null, $pattern_array);

            if (empty($conditions))
            {
                throw new UserException("No any condition applied");
            }

            $condition_list = $this->getConditions(null, $pattern_array, true);

            $qb = $this->modelClass::where($conditions);
            $qb->where("is_active", 1);

            $records = $qb->get();

            // d(LaravelExtend::getQueryLog("", -2));

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
