<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\State;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class CityController extends BackendController
{
    public String $routePrefix = "city";

    protected $modelClass = City::class;

    protected $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);
        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with(["state", "district"]);

        if ($apply_sort)
        {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass();

            $sort_params = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

        $district_conditions = [];

        if (isset($condition_list['state_id']))
        {
            $district_conditions['state_id'] = $condition_list['state_id'];
        }
        else
        {
            $district_conditions['state_id'] = State::where("country_id", $my_country->id)->pluck("id")->toArray();
        }
        
        $district_list = District::getList("id", "display_name", $district_conditions);
    
        $this->setForView(compact("district_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $state_list = State::getIndianList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);
        
        $this->setForView(compact("state_list", "field_labels"));
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
            'state_id' => ["required"],
            'district_id' => ["required"],
            'name' => ["required", "max:80"],
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "City is required",
            "name.unique" => "City already exist",
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $this->model = new $this->modelClass();

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $this->model = $this->modelClass::create($validatedData);
            
            DB::commit();

            return $this->afterSave($this->model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            
            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);;
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

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
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);;
        }
    }

    public function copy($id)
    {
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function import_csv(Request $request)
    {
        $sample = "samples/city.csv";

        $this->setForView([
            "sample" => "/" . $sample
        ]);

        $import_log_id = $request->get("import_log_id", false);
        if ($import_log_id)
        {
            $importLogModel = ImportLog::find($import_log_id);
        }

        if (!isset($importLogModel))
        {
            $save = [
                'type' => str_class_name_without_namespace($this->modelClass),
                "status_info" => "Processing...."
            ];

            $importLogModel = ImportLog::create($save);
            $import_log_id = $importLogModel->id;
        }

        $form = [
            'url' => route($this->routePrefix . '.import.csv'),
            'method' => 'POST',
        ];

        $this->setForView(compact("form", "import_log_id"));

        if ($request->isMethod('POST'))
        {
            $import_log_id = "";

            $this->setForView(compact("import_log_id"));

            $rules = [
                "file_csv" => [
                    "required", 
                    "file",
                    "max:5200",
                    "mimes:csv"
                ],
            ];

            $request->validate($rules);

            try
            {
                $start_seconds = microtime(true);

                $this->beforeCsvImport();

                $path = ImportLog::getStoragePath();

                $filepath = $request->file('file_csv')->store($path, DEFAULT_DISK);

                $full_file_path = ImportLog::getStorageDisk()->path($filepath);

                $importUtility = new ImportUtility($sample, $full_file_path);

                if (!$importUtility->checkHeaders())
                {
                    $this->setForView(array('errors' => $importUtility->errors));
                    
                    foreach($importUtility->errors as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg);
                    }

                    throw new UserException("CSV Column headers are mismatched", UserException::ERROR_USER_DATA_WRONG);
                }

                $headers = array(
                    "State" => "state_id",
                    "District" => "district_id",
                    "City" => "name",
                    "Active" => "is_active",
                    "List Display Order" => "list_display_order"
                );

                $header_flip = array_flip($headers);

                $csv_records = $importUtility->replaceHeaders($headers);
                //dd($csv_records); exit;

                $total_count = count($csv_records);

                $save = [
                    'file' => $filepath,
                    'total_count' => $total_count
                ];

                $importLogModel->fill($save);

                $accept_count = 0;
                $msg_list = [
                    "State Found" => 0,
                    "District Found" => 0,
                    "City Insert" => 0,
                    "City Update" => 0,
                ];

                $my_country = Country::getMyCountry();

                $state_list = array_flip(Util::applyAll(State::getList("id", "name", ["country_id" => $my_country->id]), ["strtolower"]));

                foreach($csv_records as $i => $record)
                {
                    $record = Util::applyAll($record, array("trim"));
                    $validate = true;

                    $state_name = strtolower($record["state_id"]);
                    if (isset($state_list[$state_name]))
                    {
                        $record['state_id'] = $state_list[$state_name];
                        $msg_list['State Found']++;

                        $district_name = strtolower($record["district_id"]);

                        $district = District::where("state_id", $record['state_id'])->where("name", $district_name)->select(["id", "state_id"])->first();

                        if ($district)
                        {
                            $record['district_id'] = $district->id;
                            $msg_list['District Found']++;
                        }
                        else
                        {
                            LaravelExtend::addError($header_flip['district_id'], $record['district_id'] . " is not found in District", "Row #" . ($i + 2));
                            $validate = false;
                        }
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['state_id'], $record['state_id'] . " is not found in State", "Row #" . ($i + 2));
                        $validate = false;
                    }
                    
                    if ($validate)
                    {   
                        $model = new $this->modelClass();

                        $record['created_by'] = $record['updated_by'] = LaravelExtend::getAuthUserId();

                        $is_insert = $is_update = null;
                        if ($model->insertOrUpdate($record, $is_insert, $is_update, true))
                        {
                            $accept_count++;

                            if ($is_insert)
                            {
                                $msg_list['City Insert']++;
                            }
                            else if ($is_update)
                            {
                                $msg_list['City Update']++;
                            }

                            $percent = round(($i + 1) * 100 / $total_count, 1);

                            $importLogModel->status_info = "Processing... $percent % " . ", Time Taken : " . Util::niceTime(microtime(true) - $start_seconds);
                            $importLogModel->saveQuietly();
                        }
                        else
                        {
                            LaravelExtend::addError("", "Fail To Save", "Row #" . ($i + 2));
                        }
                    }
                }

                if (isset($model))
                {
                    $model->afterSave();
                }

                $sqlLogModel = LaravelExtend::saveSqlLog([
                    "import_log_id" => $importLogModel->id
                ]);

                $importLogModel->sql_log_id = $sqlLogModel->id;
                $importLogModel->accept_count = $accept_count;
                $importLogModel->exec_time = microtime(true) - $start_seconds;

                $text_list = [];
                foreach($msg_list as $text => $count)
                {
                    $text_list[] = $text . " : " . $count;
                }

                $importLogModel->is_fully_run = true;

                $msg = "Total Count : $total_count, Accept Count : $accept_count";
                $msg .= ", " . implode(", ", $text_list);

                if (LaravelExtend::$errors)
                {
                    throw new UserException("Unable to Save all records. " . $msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $msg = "All File rows updated successfully. " . $msg;
                $importLogModel->flash_msg = $msg;
                $importLogModel->save();

                Session::flash('success', $msg);
            }
            catch(UserException $ex)
            {
                $msg = $ex->getMessage();

                if (isset($importLogModel))
                {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;

                    if (LaravelExtend::$errors)
                    {
                        $importLogModel->errors = LaravelExtend::$errors;                    
                    }

                    $importLogModel->save();
                }
                
                return back()->With("fail", $msg)->with("custom_errors", LaravelExtend::$errors);
            }
            
            catch(\Exception $ex)
            {
                $msg = get_exception_display_message($ex);

                if (isset($importLogModel))
                {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;
                    $importLogModel->save();
                }
                
                Session::flash('fail', $msg);  
                
                return back()->With("fail", $msg);
            }
        }

        return $this->view(__FUNCTION__);
    }

    public function ajax_get_list($state_id = 0, $district_id = 0)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($state_id)
        {
            $conditions["state_id"] = $state_id;
        }

        if ($district_id)
        {
            $conditions["district_id"] = $district_id;
        }

        $list = $this->modelClass::getList("id", "display_name", $conditions, "list_display_order");

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

    public function ajax_get_list_with_district($state_id)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [
            ["is_active", "=", 1]
        ];

        if ($state_id)
        {
            $conditions[] = ["state_id", "=", $state_id];
        }

        $response['data'] = City::getWithDistrict([], $conditions, [["is_active", "=", 1]]);

        return $this->responseJson($response);
    }
}
