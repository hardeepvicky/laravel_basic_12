<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Department;
use App\Models\FieldSetting;
use App\Models\RecurringTask;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

        $builder->with([
            "department", "assignToUser", "tags",
        ]);

        if ($tag_conditions)
        {
            $builder->wherehas("tags", function($query) use($tag_conditions) {
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
        $cache_key_prefix = Route::currentRouteName();

        if (is_null($this->model)) {
            $this->model = new Task();
        }

        $status_code_list = [
            Task::STATUS_ASSIGNED, Task::STATUS_CONFIRM, Task::STATUS_IN_PROGRESS, TASK::STATUS_COMPLETED
        ];

        $str = "'" . implode("','", $status_code_list) . "'" ;

        $default_conditions[] = ['status', "IN", $str];

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
            $query->whereNot("assign_to_user_id", $auth_user_id);
        });

        $builder->with([
            "assignToUser"
        ]);

        $records = $builder->get();

        $this->setForView(compact("records", "my_tasks"));

        $type_conditions = [
            "code" => $status_code_list
        ];
        $type_conditions['type'] = Type::getType($this->model, "status");
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $msg = "Only those tasks display whose have status of " . implode(", ", $status_list);

        $msg = "In the Screen, " . $msg;

        LaravelExtend::addInfo($msg, "primary");

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
        if ($this->model->status) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->status);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "duration_type");
        if ($this->model->duration_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->duration_type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $duration_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        // d($this->model->duration);
        // d(LaravelExtend::getQueryLog());
        // d($duration_list, true);

        $dept_conditions = [];
        if ($this->model->dept_id) {
            $dept_conditions['or_id'] = $this->model->dept_id;
        }

        $department_list = Department::getList("id", "display_name", $dept_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $default_start_date_time = DateUtility::change(date('d-M-Y 10:00'), 1, DateUtility::DAYS, DateUtility::DATETIME_OUT_FORMAT);

        $this->setForView(compact("user_list", "task_type_list", "status_list",  "department_list", "duration_type_list", "field_labels", "default_start_date_time"));

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

        $default_start_date_time = DateUtility::change(date('d-M-Y 10:00'), 1, DateUtility::DAYS, DateUtility::DATETIME_OUT_FORMAT);

        $this->setForView(compact("model", 'form', 'default_start_date_time'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "is_draft" => "",
            "name" => ["required"],
            "description" => ["nullable"],
            "assign_to_user_id" => [],
            "start_date_time" => [],
            "end_date_time" => [],
            "priority" => [],
            "is_send_notification" => "",
            "tag_name" => []
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
        $validatedData['status'] = Task::STATUS_CREATED;

        if (isset($validatedData['is_draft']) && $validatedData['is_draft'] == 1) {
            $validatedData['status'] = Task::STATUS_DRAFT;
        }

        $validatedData['end_date_time'] = null;

        if (isset($validatedData['start_date_time']) && isset($validatedData['duration_type']) && isset($validatedData['duration']))
        {
            $validatedData['end_date_time'] = Task::getEndDate($validatedData['start_date_time'], $validatedData['duration'], $validatedData['duration_type']);
        }

        // dd($validatedData);

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['tag_name']))
        {
            $model_class_name = str_class_name_without_namespace($this->modelClass);

            if (!$model_class_name)
            {
                throw new UserException("could not found model_class_name");
            }

            $model_class_name = str_class_name_to_human_text($model_class_name);
            
            $tag_names = explode(",", $validatedData['tag_name']);

            $this->model->tags()->delete();

            foreach($tag_names as $tag_name)
            {
                $save_data = [
                    "name" => $tag_name,
                ];

                $save_data['search_term'] = $model_class_name . "." . $save_data['name'];

                Tag::createTag($save_data, $this->model, $this->model);
            }
        }
    }

    protected function beforeEdit($model)
    {
        parent::beforeEdit($model);

        $error_msg = "";

        if (!$model->canEditOrDelete($error_msg))
        {
            throw new UserException($error_msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }
    }

    protected function beforeDelete($model)
    {
        parent::beforeDelete($model);

        $error_msg = "";

        if (!$model->canEditOrDelete($error_msg))
        {
            throw new UserException($error_msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with("tags")->findOrFail($id);

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
