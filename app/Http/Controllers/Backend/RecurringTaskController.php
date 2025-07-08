<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Department;
use App\Models\FieldSetting;
use App\Models\RecurringTask;
use App\Models\RecurringTaskDate;
use App\Models\Task;
use App\Models\TaskRecurringDate;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class RecurringTaskController extends BackendController
{
    public String $routePrefix = "recurring-task";

    protected $modelClass = RecurringTask::class;
    protected RecurringTask | null $model = null;

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
            ["field" => "recurring_type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "dept_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "priority", "type" => "int"],
            ["field" => "is_draft", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "department",
            "assignToUser",
            "recurringTaskDate",
            "tasks"
        ]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass();

            $sort = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort['sort_by'], $sort['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $user_conditions = [
            "type" => User::TYPE_INTERNAL,
        ];

        if ($this->model->assign_to_user_id) {
            $user_conditions['or_id'] = $this->model->assign_to_user_id;
        }

        $user_list = User::getList("id", "display_name", $user_conditions);

        $type_conditions = [];
        $type_conditions['type'] = Type::getType(new Task(), "task_type");

        if ($this->model->type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }
        $task_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");


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
        $type_conditions['type'] = Type::getType(new Task(), "duration_type");
        if ($this->model->duration_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->duration_type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $duration_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $dept_conditions = [];
        if ($this->model->dept_id) {
            $dept_conditions['or_id'] = $this->model->dept_id;
        }

        $department_list = Department::getList("id", "display_name", $dept_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $recurring_dates_field_labels = FieldSetting::getDynamicFields(RecurringTaskDate::class);

        $this->setForView(compact("user_list", "task_type_list", "recurring_type_list", "duration_type_list", "department_list", "field_labels", "recurring_dates_field_labels"));

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

        $default_start_date_time = DateUtility::change(date('d-M-Y 10:00'), 1, DateUtility::DAYS, DateUtility::DATETIME_OUT_FORMAT);

        $this->setForView(compact("model", 'form', 'default_start_date_time'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "task_type" => ["required"],
            "name" => ["required"],
            "description" => ["nullable"],
            "recurringTaskDate" => [],
            "is_send_notification" => "",
            "is_draft" => "",
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

        // d($rules, true);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $this->_beforeSave($validatedData);

            unset($save_data['recurringTaskDate']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            // d($save_data, true);

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

        if (isset($validatedData['is_draft']) && $validatedData['is_draft']) {
            $validatedData['status'] = Task::STATUS_DRAFT;
        }

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        unset($validatedData['recurringTaskDate']['(sr-id)']);

        $this->model->recurringTaskDate()->delete();

        if (isset($validatedData['recurringTaskDate'])) {

            $rules = [
                "date" => ["required"],
            ];

            $child_model = new RecurringTaskDate();

            $messages = [];

            foreach ($validatedData['recurringTaskDate'] as $record) {
                try {
                    $record = $child_model->convertTimeZoneBeforeQueryToDatabase($record);
                    $new_rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($record));

                    $validator = Validator::make($record, $new_rules, $messages);

                    if ($validator->fails()) {
                        $error_msgs = $validator->messages()->all();
                        $this->addError("", $error_msgs, "Recurring Dates");
                    } else {
                        unset($record['id']);
                        $this->model->recurringTaskDate()->create($record);
                    }
                } catch (Exception $ex) {
                    $this->addError("", $ex->getMessage(), "Recurring Dates");
                }
            }
        }

        if ($this->custom_errors) {
            // d($this->custom_errors); exit;
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["recurringTaskDate"])->findOrFail($id);

        $model->convertTimeZoneForDisplay();

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $recurringTaskDate = $this->model->recurringTaskDate->toArray();

        $this->setForView(compact("model", "form", "recurringTaskDate"));

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

            unset($save_data['recurringTaskDate']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

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
