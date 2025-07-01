<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Task;
use App\Models\TaskRecurringDate;
use App\Models\Type;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class TaskController extends BackendController
{
    public String $routePrefix = "task";

    protected $modelClass = Task::class;
    protected Task | null $model = null;

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
            ["field" => "name", "type" => "string"],
            ["field" => "description", "type" => "string"],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "assign_to_user_id", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "assignToUser", "taskRecurringDate"
        ]);

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

    public function today()
    {
        $cache_key_prefix = Route::currentRouteName();

        $default_conditions[] = ['type', Task::TYPE_ONE_TIME];
        $default_conditions[] = ['is_draft', 0];
        $default_conditions[] = ['is_verified', 0];

        $builder = $this->modelClass::where($default_conditions);

        $auth_user_id = $this->getAuthUserId();

        $builder->where("assign_to_user_id", $auth_user_id);

        $builder->where(function($query){        
            $query->whereNotIn("status", [TASK::STATUS_CREATED, TASK::STATUS_VERIFIED]);
            $query->whereDate("start_date_time", "<=", date(DateUtility::DATE_FORMAT));
        });

        $cache_key_for_sort = $cache_key_prefix . "-extra-params-" . __FUNCTION__;

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "start_date_time"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $my_tasks = $builder->get();

        $cache_key = $cache_key_prefix . "-2";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "status", "type" => "string_equal", "view_field" => "other_task_status"],
            ["field" => "assign_to_user_id", "type" => "int", "view_field" => "other_assign_to_user_id"],
        ]);

        $builder = $this->modelClass::where($default_conditions)->where($conditions);
        $builder->where(function($query) use($auth_user_id){  
            $query->whereNotIn("status", [TASK::STATUS_CREATED, TASK::STATUS_VERIFIED]);
            $query->whereDate("start_date_time", "<=", date(DateUtility::DATE_FORMAT));
            $query->whereNot("assign_to_user_id", $auth_user_id);
        });

        $builder->with([
            "assignToUser"
        ]);

        $records = $builder->get();

        $this->setForView(compact("records", "my_tasks"));

        return $this->viewIndex(__FUNCTION__);
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new Task();
        }

        $user_conditions = [];

        if ($this->model->assign_to_user_id) {
            $user_conditions['or_id'] = $this->model->assign_to_user_id;
        }

        $user_list = User::getList("id", "display_name", $user_conditions);

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "type");

        if ($this->model->type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }
        $type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");


        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "one_time_type");

        if ($this->model->type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->one_time_type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $one_time_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "recurring_type");
        if ($this->model->recurring_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->recurring_type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $recurring_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "status");
        if ($this->model->recurring_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->status);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "complete_time_in_days");
        if ($this->model->complete_time_in_days) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->complete_time_in_days);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $complete_in_days_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $field_labels = $this->model->getFieldLabels();

        $this->setForView(compact("user_list", "type_list", "one_time_type_list", "recurring_type_list", "status_list", "complete_in_days_list", "field_labels"));

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

        $model->priority = 1;

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "type" => [],
            "assign_to_user_id" => [],
            "one_time_type" => [],
            "recurring_type" => [],
            "start_date_time" => [],
            "end_date_time" => [],
            "priority" => [],
            "complete_time_in_days" => [],
            "description" => ["nullable"],
            "is_send_notification" => "",
            "is_draft" => "",
            "is_active" => "",
            "taskRecurringDate" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "User Category is required",
            "name.unique" => "User Category already exist",
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $this->_beforeSave($validatedData);

            unset($save_data['taskRecurringDate']);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }

    private function _beforeSave($validatedData)
    {
        $validatedData['status'] = Task::STATUS_CREATED;

        if (isset($validatedData['is_draft']) && $validatedData['is_draft'])
        {
            $validatedData['status'] = Task::STATUS_DRAFT;
        }

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        unset($validatedData['taskRecurringDate']['(sr-id)']);

        $this->model->taskRecurringDate()->delete();

        if (isset($validatedData['taskRecurringDate'])) {

            $rules = [
                "date" => ["required"],
            ];

            $child_model = new TaskRecurringDate();

            $messages = [
            ];

            foreach ($validatedData['taskRecurringDate'] as $record) {
                try {
                    d($record);
                    $new_rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($record));
                    
                    $validator = Validator::make($record, $new_rules, $messages);
                    
                    if ($validator->fails()) {
                        $error_msgs = $validator->messages()->all();
                        $this->addError("", $error_msgs, "Recurring Dates");
                    } else {
                        unset($record['id']);
                        $this->model->taskRecurringDate()->create($record);
                    }
                } 
                catch (Exception $ex) {
                    $this->addError("", $ex->getMessage(), "Recurring Dates");
                }
            }
        }

        if ($this->custom_errors)
        {
            // d($this->custom_errors); exit;
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["taskRecurringDate"])->findOrFail($id);

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $taskRecurringDate = $this->model->taskRecurringDate->toArray();

        $this->setForView(compact("model", "form", "taskRecurringDate"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $save_data = $this->_beforeSave($validatedData);

            unset($save_data['taskRecurringDate']);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) { 
                throw new Exception("Fail to Save");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            $this->custom_errors = array_merge_recursive($this->custom_errors, $this->model->custom_errors);

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
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

    public function status_confirm($id)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->model->changeStatus(Task::STATUS_CONFIRM);

        return back()->with("success", "Status Changed Successfully");
    }

    public function status_in_progress($id)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->model->changeStatus(Task::STATUS_IN_PROGRESS);

        return back()->with("success", "Status Changed Successfully");
    }

    public function status_complete($id)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->model->changeStatus(Task::STATUS_COMPLETED);

        return back()->with("success", "Status Changed Successfully");
    }

    public function status_verified($id)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->model->changeStatus(Task::STATUS_VERIFIED);

        return back()->with("success", "Status Changed Successfully");
    }
}
