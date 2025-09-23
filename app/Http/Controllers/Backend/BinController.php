<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\Bin;
use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\FieldSetting;
use App\Models\State;
use App\Models\Type;
use App\Models\User;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class BinController extends BackendController
{
    public String $routePrefix = "bin";

    protected $modelClass = Bin::class;

    protected Bin | null $model = null;

    protected function getModuleName(): string
    {
        return "Location";
    }

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
            ["field" => "name", "type" => "string"],
            ["field" => "code", "type" => "string"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);
        $builder->with([
            "branch"
        ]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass;

            $sort_params = $this->getSortBy($this->model, $cache_key_for_sort, [
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

        $branch_conditions = [];

        if ($this->model->branch_id)
        {
            $branch_conditions["or_id"] = $this->model->branch_id;
        }

        $branch_list = Branch::getList("id", "display_name", $branch_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("branch_list", "field_labels"));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $model->code = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_BIN_CODE);

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
            "name" => ["required"],
            "branch_id" => ["required"],
            "remarks" => ["nullable"],
            "list_display_order" => "",
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

        $model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            
            $validatedData['code'] = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_BIN_CODE);

            $this->model = $this->modelClass::create($validatedData);

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_BIN_CODE);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($model);
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
        $this->model = $model = $this->modelClass::findOrFail($id);

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
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $this->model->fill($validatedData);

            $this->model->save();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function copy($id)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $model->code = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_BIN_CODE);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function create_bulk()
    {
        $this->beforeCreate();

        $form = [
            'url' => route($this->routePrefix . '.store_bulk'),
            'method' => 'POST',
        ];

        $this->setForView(compact('form'));

        return $this->view(__FUNCTION__);
    }

    public function store_bulk(Request $request)
    {
        $this->beforeCreate();

        $rules = [
            'branch_id' => ['required'],
            'qty' => ['required', "numeric", "min:1", "max:1000"],
            'remarks' => ['nullable'],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {

            $counters = [
                "Insert" => 0,
                "Update" => 0,
            ];

            $branch = Branch::findOrFail($validatedData['branch_id']);

            $model = null;

            for($i = 1; $i <= $validatedData['qty']; $i++)
            {
                $save_data = [
                    'branch_id' => $validatedData['branch_id'],
                    'remarks' => $validatedData['remarks'],
                    "list_display_order" => $i,
                    "is_active" => 1
                ];

                $save_data['code'] = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_BIN_CODE);
                $save_data['name'] = $branch->name . " " . $save_data['code'];

                $is_insert = $is_update = false;
                $model = Bin::insertOrUpdate($save_data, $is_insert, $is_update);

                if ($is_insert)
                {
                    $counters['Insert']++;
                }
                else if ($is_update)
                {
                    $counters['Update']++;
                }

                AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_BIN_CODE);
            }

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            $msg = "Records save successfully";

            foreach($counters as $label => $count)
            {
                $msg .= " , " . $label . ":" . $count;
            }

            return $this->afterSave($model, null, $msg);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);
        
        $this->beforeCsvExport($builder);

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

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['branch_id'] => $record['branch']['name'] ?? "",
                $field_labels['name'] => $record['name'],
                $field_labels['code'] => $record['code'],
                $field_labels['remarks'] => $record['remarks'] ?? "",

                $field_labels['is_active'] => get_value_from_list($yes_no_list, $record['is_active']),
                $field_labels['list_display_order'] => $record['list_display_order'] ?? "",

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
}
