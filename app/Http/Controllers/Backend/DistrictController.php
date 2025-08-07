<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\District;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\State;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class DistrictController extends BackendController
{
    public String $routePrefix = "district";

    protected $modelClass = District::class;

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
            ["field" => "name", "type" => "string", "view_field" => "name"],
            ["field" => "state_id", "type" => "", "view_field" => "state_id"],
        ]);

        $builder = $this->modelClass::where($conditions);
        $builder->with("state");

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
            'name' => ["required", "max:80"],
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "District is required",
            "name.unique" => "District already exist",
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

            return back()->withInput()->with('fail', $ex->getMessage());
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

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function import_csv(Request $request)
    {
        $sample = "samples/districts.csv";

        $this->setForView([
            "sample" => "/" . $sample
        ]);

        $form = [
            'url' => route($this->routePrefix . '.import.csv'),
            'method' => 'POST',
        ];

        $this->setForView(compact("form"));


        if ($request->isMethod('POST'))
        {
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
                    "District" => "name",
                    "Active" => "is_active",
                    "List Display Order" => "list_display_order"
                );

                $header_flip = array_flip($headers);

                $csv_records = $importUtility->replaceHeaders($headers);

                $total_count = count($csv_records);

                $save = [
                    'type' => str_class_name_without_namespace($this->modelClass),
                    'file' => $filepath,
                    'total_count' => $total_count
                ];

                $importLogModel = ImportLog::create($save);

                $accept_count = 0;
                $msg_list = [
                    "State Found" => 0,
                    "District Insert" => 0,
                    "District Update" => 0,
                ];

                $state_list = array_flip(Util::applyAll(State::getListCache("id", "name"), ["strtolower"]));
                
                foreach($csv_records as $i => $record)
                {
                    $record = Util::applyAll($record, array("trim"));
                    $validate = true;

                    $state_name = strtolower($record["state_id"]);
                    if (isset($state_list[$state_name]))
                    {
                        $record['state_id'] = $state_list[$state_name];
                        $msg_list['State Found']++;
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['state_id'], $record['state_id'] . " is not found in State", "Row #" . ($i + 2));
                        $validate = false;
                    }
                    
                    if ($validate)
                    {   
                        $model = new $this->modelClass();

                        $is_insert = null;
                        if ($model->insertOrUpdate($record, $is_insert))
                        {
                            $accept_count++;

                            if ($is_insert)
                            {
                                $msg_list['District Insert']++;
                            }
                            else
                            {
                                $msg_list['District Update']++;
                            }
                        }
                        else
                        {
                            LaravelExtend::addError("", "Fail To Save" . " is not found in State", "Row #" . ($i + 2));
                        }
                    }
                }

                $importLogModel->accept_count = $accept_count;

                $text_list = [];
                foreach($msg_list as $text => $count)
                {
                    $text_list[] = $text . " : " . $count;
                }

                $importLogModel->is_fully_run = true;

                $msg = "Total Count = $total_count, Accept Count $accept_count";
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
                
                return back()->with("fail", $msg)->with("custom_errors", LaravelExtend::$errors);
            }
            
            catch(\Exception $ex)
            {
                $msg = $ex->getMessage();

                if (isset($importLogModel))
                {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;
                    $importLogModel->save();
                }
                
                return back()->with("fail", $msg)->with("custom_errors", LaravelExtend::$errors);
            }
        }

        return $this->view(__FUNCTION__);
    }

    public function ajax_get_list($state_id = 0)
    {
        $response = ["status" => 1];

        $conditions = [];

        if ($state_id)
        {
            $conditions[] = ["state_id", "=", $state_id];
        }

        $response['data'] = $this->modelClass::where($conditions)->select("id", "name")->get()->toArray();

        return $this->responseJson($response);
    }
}
