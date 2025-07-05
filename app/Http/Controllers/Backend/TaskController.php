<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\FieldSetting;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
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
            ["field" => "id", "type" => "int"],
            ["field" => "task_type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "priority", "type" => "int"],
            ["field" => "is_draft", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "assignToUser",
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

        $default_conditions[] = ['is_draft', 0];
        $default_conditions[] = ['is_verified', 0];

        $builder = $this->modelClass::where($default_conditions);

        $auth_user_id = $this->getAuthUserId();

        $builder->where("assign_to_user_id", $auth_user_id);

        $builder->where(function ($query) {
            $query->whereNotIn("status", [TASK::STATUS_CREATED, TASK::STATUS_VERIFIED]);
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
        $builder->where(function ($query) use ($auth_user_id) {
            $query->whereNotIn("status", [TASK::STATUS_CREATED, TASK::STATUS_VERIFIED]);
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

        $user_conditions = [
            "type" => User::TYPE_INTERNAL,
        ];

        if ($this->model->assign_to_user_id) {
            $user_conditions['or_id'] = $this->model->assign_to_user_id;
        }

        $user_list = User::getList("id", "display_name", $user_conditions);

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "task_type");

        if ($this->model->type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }
        $task_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "status");
        if ($this->model->recurring_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->status);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("user_list", "task_type_list", "status_list", "field_labels"));

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
            "is_draft" => "",
            "task_type" => ["required"],
            "name" => ["required"],
            "description" => ["nullable"],
            "assign_to_user_id" => [],
            "start_date_time" => [],
            "end_date_time" => [],
            "priority" => [],
            "is_send_notification" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [
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

        if (isset($validatedData['is_draft']) && $validatedData['is_draft'] == 1) {
            $validatedData['status'] = Task::STATUS_DRAFT;
        }

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        
    }

    protected function beforeEdit($model)
    {
        parent::beforeEdit($model);

        if (in_array($model->status, [Task::STATUS_CONFIRM, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETED, Task::STATUS_VERIFIED]))
        {
            $type_conditions = [];
            $type_conditions['type'] = Type::getType($this->model, "status");
            $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

            $msg = "Can't edit, Task Status is " . $status_list[$model->status];            

            $edit_status_label = [];

            foreach([Task::STATUS_DRAFT, Task::STATUS_CREATED, Task::STATUS_ASSIGNED] as $status)
            {
                $edit_status_label[] = $status_list[$status];
            }

            $msg .= ", Only Status with " . implode(", ", $edit_status_label);

            $msg .= " can be edit";
            
            abort(ACTION_NOT_PROCEED, $msg);
        }

    }
    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $model->afterFind();

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

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

            $records = $this->modelClass::where("task_type", $type)->get();

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
