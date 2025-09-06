<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
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

class BranchController extends BackendController
{
    public String $routePrefix = "branch";

    protected $modelClass = Branch::class;

    protected Branch | null $model = null;

    protected function getModuleName(): string
    {
        return "Unit";
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
            ["field" => "type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "phone_no", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with(["state", "district", "city"]);

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

        

        $my_country = Country::getMyCountry();

        $state_conditions = [
            ["country_id", $my_country->id],
            ["is_active", 1]
        ];

        $district_conditions = [
            ["is_active", 1]
        ];

        if (isset($condition_list['state_id']))
        {
            $state_conditions[] = ["id", $condition_list['state_id']];
            $district_conditions[] = ["state_id", $condition_list['state_id']];
        }
        
        $district_cities = City::getWithDistrict($state_conditions, $district_conditions, [["is_active", 1]]);
    
        $district_city_collection = collect($district_cities);

        $district_city_list = $district_city_collection->pluck('name', 'id')->toArray();

        $this->setForView(compact("district_city_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $type_conditions = [
            'type' => Type::getType($this->model, "type")
        ];

        if ($this->model->type)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->type);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }   
        }

        $type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $company_conditions = [];

        if ($this->model->company_id)
        {
            $company_conditions["or_id"] = $this->model->company_id;
        }

        $state_list = State::getIndianList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("type_list", "state_list", "field_labels"));

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
            "type" => ["required"],
            "name" => ["required"],
            "email" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "phone_no" => ["nullable"],
            "gst_no" => ["nullable"],
            "state_id" => ["required"],
            "district_id" => ["required"],
            "city_id" => ["required"],
            "addr1" => ["required"],
            "addr2" => ["required"],
            "description" => ["nullable"],
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
            $this->model = $this->modelClass::create($validatedData);

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

        $terms = $this->model->terms?->toArray() ?? [];

        $this->setForView(compact("model", "form", "terms"));

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

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $terms = $this->model->terms?->toArray() ?? [];

        $this->setForView(compact("model", "form", "terms"));

        return $this->view("form");
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

        $type_group_list = Type::getListOfModel($this->modelClass);

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['type'] => get_value_from_list($type_group_list['type'], $record['type']),
                $field_labels['name'] => $record['name'],
                $field_labels['mobile'] => $record['mobile'] ?? "",
                $field_labels['phone_no'] => $record['phone_no'] ?? "",
                $field_labels['email'] => $record['email'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['district_id'] => $record['district']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",
                $field_labels['addr1'] => $record['addr1'] ?? "",
                $field_labels['addr2'] => $record['addr2'] ?? "",
                $field_labels['gst_no'] => $record['gst_no'] ?? "",
                $field_labels['description'] => $record['description'] ?? "",

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
