<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\Branch;
use App\Models\Department;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\Maker;
use App\Models\Requirement;
use App\Models\SubDepartment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class RequirementController extends BackendController
{
    public String $routePrefix = "store-requirement";

    protected $modelClass = Requirement::class;
    protected Requirement | null $model = null;

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
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "order_no", "type" => "string"],
            ["field" => "part_no", "type" => "string"],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "dept_id", "type" => "int"],
            ["field" => "sub_dept_id", "type" => "int"],
            ["field" => "item_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::query();

        // d($conditions);

        $builder = $builder->where($conditions);

        $builder->with([            
            "department",
            "subDepartment",
            "item",
            "make",
            "uom"
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

        $sub_dept_list = [];
        if (isset($condition_list['dept_id']))
        {
            $sub_dept_list = SubDepartment::getList("id", "display_name", ["dept_id" => $condition_list['dept_id'] ], "list_display_order");
        }

        $this->setForView(compact("sub_dept_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $dept_list = Department::getList("id", "display_name", [], "list_display_order");        
        $item_list = Item::getList("id", "display_name", [], "list_display_order");
        $maker_list = Maker::getList("id", "display_name", [], "list_display_order");
        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");        

        $status_list = Requirement::getStatusTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("maker_list", "branch_list", "dept_list",
            "item_list", "status_list",
            "field_labels"
        ));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_REQUIREMENT);

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", 'form'));

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [            
            "dept_id" => ["required"],
            "sub_dept_id" => ["nullable"],
            "item_id" => ["required"],
            "make_id" => ["required"],
            "qty" => ["required", "numeric", "min:1"],
            "uom_id" => ["required"],
            "part_no" => ["nullable"],
            "remarks" => "",            
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function set_data_for_add_edit($model) {}

    public function store(Request $request)
    {
        $this->beforeCreate();

        $this->model = new $this->modelClass();

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
            
            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;
            
            $save_data['status'] = Requirement::STATUS_PENDING;

            $this->model = new $this->modelClass;
            $this->model->fill($save_data);

            $this->model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_REQUIREMENT);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_REQUIREMENT);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $screen = "edit";

        $this->setForView(compact("model", "form", "screen"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
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

    public function copy($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_REQUIREMENT);

        $this->beforeCreate();

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }


    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $status_list = Requirement::getStatusTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['order_no'] => $record['order_no'] ?? "",
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                $field_labels['dept_id'] => $record['department']['name'] ?? "",
                $field_labels['sub_dept_id'] => $record['sub_department']['name'] ?? "",
                $field_labels['item_id'] => $record['item']['name'] ?? "",
                $field_labels['make_id'] => $record['make']['name'] ?? "",
                $field_labels['part_no'] => $record['part_no'] ?? "",
                $field_labels['qty'] => $record['qty'] ?? "",
                $field_labels['uom_id'] => $record['uom']['name'] ?? "",
                $field_labels['status'] => get_value_from_list($status_list, $record['status']),                
                $field_labels['remarks'] => $record['remarks'] ?? "",

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
        $file_prefix = str_class_name_to_human_text($this->modelClass);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function ajax_get_list()
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        $list = $this->modelClass::getList("id", "display_name", $conditions, "id", "DESC");

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
}
