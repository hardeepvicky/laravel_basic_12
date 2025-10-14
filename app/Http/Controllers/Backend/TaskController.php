<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\Dealer;
use App\Models\Department;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Lead;
use App\Models\Party;
use App\Models\Project;
use App\Models\Role;
use App\Models\SqlLog;
use App\Models\Tag;
use App\Models\Target;
use App\Models\Task;
use App\Models\TaskTakenAction;
use App\Models\TaskTodo;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
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

        $builder->with([
            "department",
            "assignToUser",
            "tags",
            "user",
            "documentFile",
            "taskTodo",
            "project",
            "target"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "task_type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "dept_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "priority", "type" => "int"],
            ["field" => "is_draft", "type" => "int"],
            ["field" => "start_date_time", "type" => "from_date", "view_field" => "from_start_date"],
            ["field" => "start_date_time", "type" => "to_date", "view_field" => "to_start_date"],
        ]);

        $tag_conditions = $this->getConditions($cache_key . "-2", [
            ["field" => "name", "type" => "string", "view_field" => "tag_name"],
        ]);

        $builder = $this->modelClass::where($conditions);

        if ($tag_conditions) {
            $builder->wherehas("tags", function ($query) use ($tag_conditions) {
                $query->where($tag_conditions);
            });
        }

        if ($apply_sort) {
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

    public function today()
    {
        $this->today_my_task(false);

        $this->today_other_task(false);

        $this->saveSqlLog();

        return $this->viewIndex(__FUNCTION__);
    }

    public function today_my_task(bool $render = true)
    {
        $this->model = new $this->modelClass;

        $cache_key_prefix = Route::currentRouteName();

        $status_code_list = [
            Task::STATUS_ASSIGNED,
            Task::STATUS_CONFIRM,
            Task::STATUS_IN_PROGRESS,
            TASK::STATUS_COMPLETED
        ];

        $builder = $this->modelClass::whereIn("status", $status_code_list);

        $builder->where("assign_to_user_id", LaravelExtend::getAuthUserId());

        $cache_key_for_sort = $cache_key_prefix . "-extra-params-" . __FUNCTION__;

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getSortBy(new $this->modelClass, $cache_key_for_sort, [
            ["key" => "sort_by", "default" => "start_date_time"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        SqlLog::addSelectQuery($builder);

        $my_tasks = $builder->get();

        foreach($my_tasks as $record)
        {
            $seconds_left = DateUtility::diff($record->end_date_time, DateUtility::getDate(null));

            $record->time_left = Util::niceTime($seconds_left);

            $record->afterFind();
        }

        $this->setForView(compact("my_tasks"));

        $type_conditions = [
            "code" => $status_code_list
        ];

        $type_conditions['type'] = Type::getType($this->model, "status");
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $msg = "Only those tasks display whose have status of " . implode(", ", $status_list);

        $msg = "In the Screen, " . $msg;

        LaravelExtend::addInfo($msg, "primary");

        if ($render)
        {
            return $this->view("ajax_today_my_task");
        }
    }

    public function today_other_task(bool $render = true)
    {
        $this->model = new $this->modelClass;

        $status_code_list = [
            Task::STATUS_ASSIGNED,
            Task::STATUS_CONFIRM,
            Task::STATUS_IN_PROGRESS,
            TASK::STATUS_COMPLETED
        ];

        $cache_key_prefix = Route::currentRouteName();

        $cache_key = $cache_key_prefix;

        $conditions = $this->getConditions($cache_key, [
            ["field" => "dept_id", "type" => "int", "view_field" => "other_dept_id"],
            ["field" => "assign_to_user_id", "type" => "int", "view_field" => "other_assign_to_user_id"],
            ["field" => "status", "type" => "string_equal", "view_field" => "other_task_status"],
            ["field" => "name", "type" => "string", "view_field" => "other_task_name"],
        ]);

        $cache_key = $cache_key_prefix . "-2";

        $tag_conditions = $this->getConditions($cache_key . "-2", [
            ["field" => "name", "type" => "string", "view_field" => "other_tag_name"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder = $this->modelClass::whereIn("status", $status_code_list)->where($conditions);

        if ($tag_conditions) {
            $builder->wherehas("tags", function ($query) use ($tag_conditions) {
                $query->where($tag_conditions);
            });
        }

        $auth_user = Auth::user();

        if ($auth_user->hasRole(Role::TYPE_SYSTEM_ADMIN)) {
        } else {
            $child_user_id_list = $auth_user->designation->getUserIdListBasedOnDesignationLevel();
            if ($child_user_id_list) {
                $builder->whereIn("assign_to_user_id", $child_user_id_list);
            }
        }

        $builder->where(function ($query)  {
            $query->whereNot("assign_to_user_id", LaravelExtend::getAuthUserId());
        });

        $builder->with([
            "assignToUser"
        ]);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params-" . __FUNCTION__;

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getSortBy($this->model, $cache_key_for_sort, [
            ["key" => "sort_by", "default" => "start_date_time"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        SqlLog::addSelectQuery($builder);

        $other_tasks = $builder->paginate($this->paginationLimit);

        foreach($other_tasks as $record)
        {
            $seconds_left = DateUtility::diff($record->end_date_time, DateUtility::getDate(null));

            $record->time_left = Util::niceTime($seconds_left);

            $record->afterFind();
        }

        $this->setForView(compact("other_tasks"));

        if ($render)
        {
            return $this->view("ajax_today_other_task");
        }
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new Task();
        }

        $users = User::getCache("users_type_internal");

        if (!$users)
        {
            $users = User::where("type", User::TYPE_INTERNAL)->where("is_active", 1)->get()->toArray();

            $cache_time = 60 * 60 * 24;
            
            User::addCache("users_type_internal", $users, $cache_time);
        }

        $user_list = [];

        foreach($users as $k => $user)
        {
            $user_list[$user['id']] = $user['display_name'];
            $users[$k]['name'] = $user['display_name'];
        }

        $type_conditions = [
            'type' => Type::getType($this->model, "task_type")
        ];

        if ($this->model->task_type)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->task_type);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }

        $task_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "status")
        ];
        if ($this->model->status)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->status);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "duration_type")
        ];
        if ($this->model->duration_type)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->duration_type);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }
        $duration_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "contact_type")
        ];
        if ($this->model->contact_type)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->contact_type);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }
        $contact_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $dept_conditions = [];

        if ($this->model->dept_id)
        {
            $dept_conditions["or_id"] = $this->model->dept_id;
        }

        $department_list = Department::getList("id", "display_name", $dept_conditions, "list_display_order");

        $party_list = Party::getList("id", "display_name", [], "list_display_order");

        $lead_list = Lead::getList("id", "display_name", ["is_customer" => 0]);

        $customer_list = Lead::getList("id", "display_name", ["is_customer" => 1]);

        $dealer_list = Dealer::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $task_todo_field_labels = FieldSetting::getDynamicFields(TaskTodo::class);

        $default_start_date_time = DateUtility::change(date('d-M-Y 10:00'), 1, DateUtility::DAYS, DateUtility::DATETIME_OUT_FORMAT);

        $this->setForView(compact(
            "user_list", "users", "task_type_list", 
            "status_list", "department_list", "duration_type_list", "party_list", "contact_type_list", "lead_list", "customer_list", "dealer_list",
            "field_labels", "task_todo_field_labels",
            "default_start_date_time"
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

        $model->priority = 1;
        $model->duration = 1;
        $model->is_timebound = 1;
        $model->duration_type = Task::DURATION_TYPE_DAY;

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "is_draft" => "",
            "name" => ["required"],
            "is_timebound" => ["required"],
            "assign_to_user_id" => [],
            "start_date_time" => [],
            "end_date_time" => [],
            "priority" => [],
            "contact_type" => "",
            "project_id" => "",
            "is_send_notification" => "",
            "tag_name" => [],
            "taskUser" => [],
            "taskFile" => [],
            'taskParty' => [],
            'taskLead' => [],
            'taskCustomer' => [],
            'taskDealer' => [],
            'taskTarget' => [],
            'task_todo' => [],
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

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $this->_beforeSave($validatedData);

            unset($save_data['tag_name']);
            unset($save_data['taskUser']);
            unset($save_data['taskFile']);
            unset($save_data['taskParty']);
            unset($save_data['task_todo']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            // dd($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _beforeSave($validatedData)
    {
        $validatedData['end_date_time'] = null;

        if (isset($validatedData['start_date_time']) && isset($validatedData['duration_type']) && isset($validatedData['duration'])) {
            $validatedData['end_date_time'] = Task::getEndDate($validatedData['start_date_time'], $validatedData['duration'], $validatedData['duration_type']);
        }

        // dd($validatedData);

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        // d($validatedData, true);
        if (isset($validatedData['taskUser']) && is_array($validatedData['taskUser'])) {
            $users = User::find($validatedData['taskUser']);
            $this->model->user()->sync($users);
        }

        if (isset($validatedData['taskTarget']) && is_array($validatedData['taskTarget'])) {
            $records = Target::find($validatedData['taskTarget']);
            $this->model->target()->sync($records);
        }

        if (isset($validatedData['taskParty']) && is_array($validatedData['taskParty'])) {
            $parties = Party::find($validatedData['taskParty']);
            $this->model->party()->sync($parties);
        }
        else
        {
            $this->model->taskParty()->delete();
        }

        if (isset($validatedData['taskLead']) && is_array($validatedData['taskLead'])) {
            $leads = Lead::find($validatedData['taskLead']);
            $this->model->lead()->sync($leads);
        }
        else
        {
            $this->model->taskLead()->delete();
        }

        if (isset($validatedData['taskCustomer']) && is_array($validatedData['taskCustomer'])) {
            $leads = Lead::find($validatedData['taskCustomer']);
            $this->model->customer()->sync($leads);
        }
        else
        {
            $this->model->taskCustomer()->delete();
        }

        if (isset($validatedData['taskDealer']) && is_array($validatedData['taskDealer'])) {
            $dealers = Lead::find($validatedData['taskDealer']);
            $this->model->dealer()->sync($dealers);
        }
        else
        {
            $this->model->taskDealer()->delete();
        }

        if (isset($validatedData['taskFile'])) {

            $ids = explode(",", $validatedData['taskFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }

        if (isset($validatedData['tag_name'])) {
            // dd($validatedData['tag_name']);
            $model_class_name = str_class_name_without_namespace($this->modelClass);

            if (!$model_class_name) {
                throw new UserException("could not found model_class_name");
            }

            $model_class_name = str_class_name_to_human_text($model_class_name);

            $tag_names = explode(",", $validatedData['tag_name']);

            $this->model->tags()->delete();

            foreach ($tag_names as $tag_name) {
                $save_data = [
                    "main_table_field" => "name",
                    "name" => $tag_name,
                ];

                $save_data['search_term'] = $model_class_name . "." . $save_data['name'];

                Tag::createTag($save_data, $this->model, $this->model);
            }
        }

        if (isset($validatedData['task_todo'])) {

            unset($validatedData['task_todo']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['task_todo'])) {
                $base_rules = [
                    "todo" => ["required"],                    
                ];

                $child_model = new TaskTodo();

                $existing_id_list = $this->model->taskTodo()->pluck("id", "id")->toArray();

                $validatedData['task_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['task_todo'] as $task_todo) {

                    $validator = Validator::make($task_todo, $base_rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Todo");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($task_todo['id']) {
                        $child_model = TaskTodo::find($task_todo['id']);

                        if (!$child_model) {
                            $child_model = new TaskTodo();
                        } else {
                            unset($existing_id_list[$task_todo['id']]);
                        }
                    } else {
                        $child_model = new TaskTodo();
                    }

                    $child_model->fill($task_todo);

                    $child_model = $this->model->taskTodo()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->taskTodo()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }
    }

    protected function beforeEdit($model)
    {
        parent::beforeEdit($model);

        $error_msg = "";

        $not_editable_status_codes = [Task::STATUS_COMPLETED, Task::STATUS_VERIFIED];

        $user_id_list = $model->assignToUser->designation->getParentUserIdListBasedOnDesignationLevel();
        $user_id_list[] = $model->created_by;

        if (!in_array(LaravelExtend::getAuthUserId(), $user_id_list))
        {
            throw new UserException("Only Task Creator or Parent Designation User of " . $model->assignToUser->full_name . " can edit", UserException::ERROR_USER_ACTION_RESTRICT);
        }


        if (in_array($model->status, $not_editable_status_codes)) {
            $type_conditions = [];
            $type_conditions['type'] = Type::getType($model, "status");
            $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

            $error_msg = "Can't Edit, Task Status is " . $status_list[$model->status];

            $not_delete_label = [];

            foreach ($not_editable_status_codes as $status_code) {
                $not_delete_label[] = $status_list[$status_code];
            }

            $error_msg .= ", Status with " . implode(", ", $not_delete_label);

            $error_msg .= " can not be delete";

            throw new UserException($error_msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }
    }

    protected function beforeDelete($model)
    {
        parent::beforeDelete($model);

        $error_msg = "";

        if (!$model->canDelete($error_msg)) {
            throw new UserException($error_msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["tags", "taskUser", "taskFile", "target", "taskTodo"])->findOrFail($id);

        $model->convertTimeZoneForDisplay();

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

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {

            $save_data = $this->_beforeSave($validatedData);

            unset($save_data['tag_name']);
            unset($save_data['taskUser']);
            unset($save_data['taskFile']);
            unset($save_data['task_todo']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            // dd($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
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
        try {
            $this->model = $this->modelClass::findOrFail($id);

            $this->model->changeStatus(Task::STATUS_CONFIRM);

            $this->saveSqlLog();

            return back()->with("success", "Status Changed Successfully");
        } catch (Exception $ex) {
            $this->saveSqlLog();
            throw $ex;
        }
    }

    public function status_in_progress($id)
    {
        try {
            $this->model = $this->modelClass::findOrFail($id);

            $this->model->changeStatus(Task::STATUS_IN_PROGRESS);

            $this->saveSqlLog();

            return back()->with("success", "Status Changed Successfully");
        } catch (Exception $ex) {
            $this->saveSqlLog();
            throw $ex;
        }
    }

    public function status_complete($id, Request $request)
    {
        try {
            $this->model = $this->modelClass::findOrFail($id);

            $validatedData = $request->validate([
                "todo" => [],
                "description" => ["nullable"]
            ]);

            // d($validatedData, true);

            $is_completed = true;

            if (isset($validatedData['todo']))
            {
                foreach($validatedData['todo'] as $todo_id => $value)
                {
                    if (!$value)
                    {
                        $is_completed = false;
                    }

                    $todo = TaskTodo::find($todo_id);

                    if ($todo)
                    {
                        $todo->is_completed = $value;

                        if ($value)
                        {
                            $todo->complete_date_time = date(DateUtility::DATETIME_FORMAT);
                        }
                        else
                        {
                            $todo->complete_date_time = null;
                        }

                        $todo->save();
                    }
                }

                unset($validatedData["todo"]);
            }

            $this->model->fill($validatedData);
            $this->model->save();

            if ($is_completed)
            {
                $this->model->changeStatus(Task::STATUS_COMPLETED);
            }

            $msg = "Data saved and Status changed Successfully";

            if ($request->ajax()) {
                return $this->responseJson(['status' => 1, 'msg' => $msg]);
            }

            $this->saveSqlLog();

            return back()->with("success", $msg);
        } catch (Exception $ex) {

            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();
            throw $ex;
        }
    }

    public function status_verified($id, Request $request)
    {
        try {
            $this->model = $this->modelClass::findOrFail($id);

            $validatedData = $request->validate([
                "rating" => ["required", "numeric", "min:1", "max:5"],
                "review_remarks" => ["nullable"]
            ]);

            $this->model->fill($validatedData);

            $this->model->changeStatus(Task::STATUS_VERIFIED);

            $msg = "Data saved and Status changed Successfully";

            if ($request->ajax()) {
                return $this->responseJson(['status' => 1, 'msg' => $msg]);
            }

            $this->saveSqlLog();

            return back()->with("success", $msg);
        } catch (Exception $ex) {

            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();
            throw $ex;
        }
    }

    public function history($id, Request $request)
    {
        $record = $this->modelClass::with(["tags", "taskUser", "taskFile", "taskTakenAction"])->findOrFail($id);

        $model = new TaskTakenAction();

        if ($request->isMethod('post')) {
            $rules = [
                "action_taken_id" => ["required"],
                "response_taken_id" => ["required"],
                "remarks" => "",
            ];

            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            try {

                $save_data = $validatedData;

                $save_data = $model->convertTimeZoneBeforeQueryToDatabase($save_data);

                // dd($save_data);

                $save_data['task_id'] = $id;

                $model->fill($save_data);

                if (!$model->save()) {
                    throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                }

                if (LaravelExtend::$errors) {
                    throw new Exception("There are some errors");
                }

                DB::commit();

                return back()->with('success', "History has saved");

            } catch (Exception $ex) {
                DB::rollBack();

                $this->saveSqlLog();

                return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
            }
        }

        $record->convertTimeZoneForDisplay();

        $history_records = TaskTakenAction::where("task_id", $id)->orderBy("id", "DESC")->get();

        $form = [
            'url' => route($this->routePrefix . '.history', $id),
            'method' => 'POST',
        ];

        $task_taken_actions_fields = FieldSetting::getDynamicFields($model);

        $action_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::TASK_ACTION_TAKEN], "list_display_order");

        $response_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::TASK_RESPONSE], "list_display_order");

        $id_list = $record->taskFile->pluck("document_file_id")->toArray();

        $document_files = DocumentFile::find($id_list);

        $this->setForView(compact("record", "form", "model", "task_taken_actions_fields", "action_list", "response_list", "history_records", "document_files"));

        return $this->view(__FUNCTION__);
    }

    public function history_destroy($id)
    {
        $request = request();

        try {
            $model = TaskTakenAction::findOrFail($id);

            if (!$this->delete($model)) {
                throw new Exception("Fail to Delete");
            }

            $msg = "Task History deleted successfully";

            $this->saveSqlLog();

            if ($request->ajax()) {
                return response()->json(['status' => 1, 'msg' => $msg]);
            }

            return back()->with('success', $msg);

        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }

    public function change_files($task_id, Request $request)
    {
        $model = $this->modelClass::findOrFail($task_id);

        $rules = [
            "taskFile" => ["required"],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {

            if (isset($validatedData['taskFile'])) {

                $ids = explode(",", $validatedData['taskFile']);

                $document_files = DocumentFile::find($ids);

                $model->documentFile()->sync($document_files);
            }

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 1,
                ]);
            }

            $this->saveSqlLog();

            return back()->with('success', "Files Saved successfully");
        } 
        catch (UserException $ex) {
            DB::rollBack();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 0,
                    'msg' => $ex->getMessage(),
                    "custom_errors" => LaravelExtend::$errors
                ]);
            }

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
        catch (Exception $ex) {
            DB::rollBack();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 0,
                    'msg' => get_exception_display_message($ex),
                    "custom_errors" => LaravelExtend::$errors
                ]);
            }

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "department",
            "assignToUser",
            "tags",
            "user",
            "documentFile",
        ]);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $type_list = Type::getListCache("code", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $user_list = [];

            foreach($record['user'] as $user)
            {
                $user_list[] = $user['display_name'];
            }

            $tag_list = [];

            foreach($record['tags'] as $tag)
            {
                $tag_list[] = $tag['display_name'];
            }

            $csv_records[] = [
                'ID' => $record['id'],

                $field_labels['assign_to_user_id'] => $record['assign_to_user']['display_name'],
                $field_labels['dept_id'] => $record['department']['display_name'],
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['name'] => $record['name'],
                $field_labels['description'] => $record['description'] ?? "",
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['start_date_time'] => if_date_time($record['start_date_time']),
                $field_labels['duration'] => $record['duration'],
                $field_labels['duration_type'] => get_value_from_list($type_list, $record['duration_type']),
                $field_labels['end_date_time'] => if_date_time($record['end_date_time']),
                $field_labels['priority'] => $record['priority'],
                $field_labels['status'] => get_value_from_list($type_list, $record['status']),
                $field_labels['is_draft'] => get_value_from_list($yes_no_list, $record['is_draft']),
                $field_labels['is_completed'] => get_value_from_list($yes_no_list, $record['is_completed']),
                $field_labels['complete_date_time'] => if_date_time($record['complete_date_time']),
                $field_labels['complete_by'] => get_value_from_list($user_cache_list, $record['complete_by']),
                $field_labels['is_verified'] => get_value_from_list($yes_no_list, $record['is_verified']),
                $field_labels['verified_date_time'] => if_date_time($record['verified_date_time']),
                $field_labels['verified_by'] => get_value_from_list($user_cache_list, $record['verified_by']),
                $field_labels['rating'] => $record['rating'] ?? "",
                $field_labels['review_remarks'] => $record['review_remarks'] ?? "",
                $field_labels['taskUser'] => implode(", ", $user_list),
                $field_labels['tag_name'] => implode(", ", $tag_list),

                'Created' => if_date_time($record['created_at']),
                'Created By' => $user_cache_list[$record['created_by']] ?? "",
                'Updated' => if_date_time($record['updated_at']),
                'Updated By' => $user_cache_list[$record['updated_by']] ?? "",
            ];
        }

        // d($csv_records, true);

        $this->saveSqlLog();

        $path = laravel_constant('path.temp');
        FileUtility::createFolder($path);

        $current_dt = DateUtility::getDateObj(null);
        $current_dt->setTimezone(DateUtility::$login_user_time_zone);
        $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);
        $file = $path . $this->tableName . "_$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function ajax_complete_form($id)
    {
        $model = $this->model = $this->modelClass::with(["taskTodo"])->findOrFail($id);

        $this->setForView(compact("model"));

        return $this->view(__FUNCTION__);
    }
}
