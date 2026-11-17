<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\DocumentFile;
use App\Models\Role;
use App\Models\SqlLog;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
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
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "priority", "type" => "int"],
            ["field" => "is_draft", "type" => "int"],
            ["field" => "start_date_time", "type" => "from_date", "view_field" => "from_start_date"],
            ["field" => "start_date_time", "type" => "to_date", "view_field" => "to_start_date"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "assignToUser",
        ]);

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

        $default_start_date_time = DateUtility::change(date('d-M-Y 10:00'), 1, DateUtility::DAYS, DateUtility::DATETIME_OUT_FORMAT);

        $this->setForView(compact(
            "user_list", "users", "task_type_list", 
            "status_list", "duration_type_list", 
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
        $model->duration_type = Task::DURATION_TYPE_DAY;

        $this->setForView(compact("model", 'form'));

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
            "taskFile" => [],
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

            unset($save_data['taskFile']);

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

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['taskFile'])) {

            $ids = explode(",", $validatedData['taskFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }
    }

    protected function beforeEdit($model)
    {
        parent::beforeEdit($model);

        $error_msg = "";

        $not_editable_status_codes = [Task::STATUS_COMPLETED, Task::STATUS_VERIFIED];

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
        $model = $this->model = $this->modelClass::with(["taskFile"])->findOrFail($id);

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

            unset($save_data['taskFile']);

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

    public function status_complete($id)
    {
        try {
            $this->model = $this->modelClass::findOrFail($id);

            $this->model->changeStatus(Task::STATUS_COMPLETED);

            $this->saveSqlLog();

            return back()->with("success", "Status Changed Successfully");
        } catch (Exception $ex) {
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

    // public function csv()
    // {
    //     $cache_key_prefix = Route::currentRouteName();

    //     $builder = $this->_getBuildeForIndex($cache_key_prefix);

    //     $this->beforeCsvExport($builder);

    //     $records = $builder->get();

    //     foreach($records as $k => $record)
    //     {
    //         $record->afterFindForCsv();
    //     }

    //     $records = $records->toArray();

    //     // d($records, true);

    //     $csv_records = [];
    //     $user_cache_list = User::getListCache();

    //     $yes_no_list = laravel_constant('yes_no_list');

    //     $type_list = Type::getListCache("code", "display_name");

    //     $field_labels = FieldSetting::getDynamicFields($this->modelClass);

    //     foreach($records as $record)
    //     {
    //         $user_list = [];

    //         foreach($record['user'] as $user)
    //         {
    //             $user_list[] = $user['display_name'];
    //         }

    //         $tag_list = [];

    //         foreach($record['tags'] as $tag)
    //         {
    //             $tag_list[] = $tag['display_name'];
    //         }

    //         $csv_records[] = [
    //             'ID' => $record['id'],

    //             $field_labels['assign_to_user_id'] => $record['assign_to_user']['display_name'],
    //             $field_labels['dept_id'] => $record['department']['display_name'],
    //             $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
    //             $field_labels['name'] => $record['name'],
    //             $field_labels['description'] => $record['description'] ?? "",
    //             $field_labels['start_date_time'] => if_date_time($record['start_date_time']),
    //             $field_labels['duration'] => $record['duration'],
    //             $field_labels['duration_type'] => get_value_from_list($type_list, $record['duration_type']),
    //             $field_labels['end_date_time'] => if_date_time($record['end_date_time']),
    //             $field_labels['priority'] => $record['priority'],
    //             $field_labels['status'] => get_value_from_list($type_list, $record['status']),
    //             $field_labels['is_draft'] => get_value_from_list($yes_no_list, $record['is_draft']),
    //             $field_labels['is_completed'] => get_value_from_list($yes_no_list, $record['is_completed']),
    //             $field_labels['complete_date_time'] => if_date_time($record['complete_date_time']),
    //             $field_labels['complete_by'] => get_value_from_list($user_cache_list, $record['complete_by']),
    //             $field_labels['is_verified'] => get_value_from_list($yes_no_list, $record['is_verified']),
    //             $field_labels['verified_date_time'] => if_date_time($record['verified_date_time']),
    //             $field_labels['verified_by'] => get_value_from_list($user_cache_list, $record['verified_by']),
    //             $field_labels['rating'] => $record['rating'] ?? "",
    //             $field_labels['review_remarks'] => $record['review_remarks'] ?? "",
    //             $field_labels['tag_name'] => implode(", ", $tag_list),

    //             'Created' => if_date_time($record['created_at']),
    //             'Created By' => $user_cache_list[$record['created_by']] ?? "",
    //             'Updated' => if_date_time($record['updated_at']),
    //             'Updated By' => $user_cache_list[$record['updated_by']] ?? "",
    //         ];
    //     }

    //     // d($csv_records, true);

    //     $this->saveSqlLog();

    //     $path = laravel_constant('path.temp');
    //     FileUtility::createFolder($path);

    //     $current_dt = DateUtility::getDateObj(null);
    //     $current_dt->setTimezone(DateUtility::$login_user_time_zone);
    //     $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);
    //     $file = $path . $this->tableName . "_$datetime.csv";

    //     $csvUtility = new CsvUtility($file);
    //     $csvUtility->write($csv_records);

    //     download_start($file, "application/octet-stream");
    // }

}
