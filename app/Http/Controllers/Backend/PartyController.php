<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\BaseModelInterface;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\District;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Party;
use App\Models\PartyAddress;
use App\Models\PartyHistory;
use App\Models\PartyTerm;
use App\Models\State;
use App\Models\Type;
use App\Models\User;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PartyController extends BackendController
{
    public String $routePrefix = "party";

    protected $modelClass = Party::class;

    protected $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "documentFile"
        ]);

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
            ["field" => "type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "company_name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "mobile2", "type" => "string"],
            ["field" => "phone_no", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "state", "district", "city",
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

        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

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

        $type_list = Party::getTypeList();

        $address_type_list = PartyAddress::getTypeList();

        $state_list = State::getIndianList();

        $type_conditions = [
            'type' => Type::getType(new Lead(), "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $status_type_list_for_form = array_diff($status_type_list, ["Converted"]);

        $type_conditions = [
            'type' => Type::getType(new LeadHistory(), "action_type")
        ];

        $action_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $history_response_list = DropDownValue::getList("id", "display_name", ["type" =>  DropDownValue::LEAD_HISTORY_RESPONSE], "list_display_order");

        $user_list = User::getList("id", "display_name", ["type" => User::TYPE_INTERNAL], "person_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $term_field_labels = FieldSetting::getDynamicFields(PartyTerm::class);

        $address_field_labels = FieldSetting::getDynamicFields(PartyAddress::class);

        $history_field_labels = FieldSetting::getDynamicFields(PartyHistory::class);

        $this->setForView(compact(
            "type_list", "address_type_list", "state_list", "status_type_list", "status_type_list_for_form",
            "action_type_list", "history_response_list", "user_list",
            "field_labels", "term_field_labels", "address_field_labels", "history_field_labels"
        ));

        return parent::beforeViewRender();
    }

    public function create(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $refer_from = $request->get("refer_from", false);

        $this->setForView(compact("model", 'form', 'refer_from'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "type" => ["required"],
            "name" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "phone_no" => ["nullable"],
            "company_name" => ["nullable"],
            "gst_no" => ["nullable"],
            "state_id" => ["required"],
            "district_id" => ["required"],
            "city_id" => ["required"],
            "description" => ["nullable"],
            "terms" => [],
            "partyFile" => [],
            "addresses" => [],
            "is_active" => "",
            "list_display_order" => "",
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
            $validatedData['assign_to_user_id'] = LaravelExtend::getAuthUserId();
            $this->model = $this->modelClass::create($validatedData);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            $save_and_close = (bool) request("save_and_close", false);

            if ($save_and_close)
            {
                return redirect()->to(get_current_path_url([
                    'last_insert_id' => $this->model->id,
                    "last_insert_type" => "party"
                ]));
            }

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['partyFile'])) {

            $ids = explode(",", $validatedData['partyFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }

        unset($validatedData['terms']['(sr-id)']);

        // d($validatedData, true);

        if (isset($validatedData['terms']))
        {
            $rules = [
                "name" => ["required"],
                "is_active" => ""
            ];

            $child_model = new PartyTerm();

            $existing_id_list = $this->model->terms()->pluck("id", "id")->toArray();

            $validatedData['party_id'] = $this->model->id;

            // $rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($validatedData));

            $display_order = 0;

            foreach($validatedData['terms'] as $term)
            {
                $validator = Validator::make($term, $rules, [
                    "name.required" => "Term is required"
                ]);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Addresses");
                    }
                    continue;
                }

                $display_order++;
                if ($term['id'])
                {
                    $child_model = PartyTerm::find($term['id']);

                    if (!$child_model)
                    {
                        $child_model = new PartyTerm();
                    }
                    else
                    {
                        unset($existing_id_list[$term['id']]);
                    }
                }
                else
                {
                    $child_model = new PartyTerm();
                }

                $term['list_display_order'] = $display_order;

                $child_model->fill($term);

                $this->model->terms()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->terms()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }
        }

        unset($validatedData['addresses']['(sr-id)']);

        if (isset($validatedData['addresses']))
        {
            $base_rules = [
                "type" => ["required"],
                "addr1" => ["required"],
                "addr2" => ["nullable"],
                "pincode" => ["required"],
                "is_primary" => ""
            ];

            $child_model = new PartyAddress();

            $existing_id_list = $this->model->address()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['addresses'] as $address)
            {
                $address['party_id'] = $this->model->id;
                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($address));

                $validator = Validator::make($address, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Addresses");
                    }
                    continue;
                }

                $display_order++;
                if ($address['id'])
                {
                    $child_model = PartyAddress::find($address['id']);

                    if (!$child_model)
                    {
                        $child_model = new PartyAddress();
                    }
                    else
                    {
                        unset($existing_id_list[$address['id']]);
                    }
                }
                else
                {
                    $child_model = new PartyAddress();
                }

                $address['list_display_order'] = $display_order;

                $child_model->fill($address);

                $this->model->address()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->address()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
        ])->findOrFail($id);

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
        $model = $this->modelClass::with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
        ])->indOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function ajax_get_list($type)
    {
        $response = ["status" => 1];

        try {

            $records = $this->modelClass::where("type", $type)->orderBy("list_display_order")->get();

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

    public function import_csv(Request $request)
    {
        $sample = "samples/party.csv";

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
                    "Type" => "type",
                    "Name" => "name",
                    "Mobile" => "mobile",
                    "Mobile2" => "mobile2",
                    "Phone No" => "phone_no",
                    "Email" => "email",
                    "Company Name" => "company_name",
                    "GST No" => "gst_no",
                    "State" => "state_id",
                    "District" => "district_id",
                    "City" => "city_id",
                    "Description" => "description",
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
                    "Type Found" => 0,
                    "State Found" => 0,
                    "District Found" => 0,
                    "City Found" => 0,
                    "Party Insert" => 0,
                    "Party Update" => 0,
                ];

                $my_country = Country::getMyCountry();

                $state_list = array_flip(Util::applyAll(State::getList("id", "name", ["country_id" => $my_country->id]), ["strtolower"]));

                $type_list = array_flip(Util::applyAll(Party::getTypeList(), ["strtolower"]));

                foreach($csv_records as $i => $record)
                {
                    $record = Util::applyAll($record, array("trim"));
                    $validate = true;

                    $required_fields = ["type", "name", "mobile", "email", "state_id", "district_id", "city_id", "is_active", "list_display_order"];

                    foreach($required_fields as $field)
                    {
                        if (!isset($record[$field]) || strlen($record[$field]) == 0)
                        {
                            LaravelExtend::addError($header_flip[$field], "Required", "Row #" . ($i + 2));
                            $validate = false;
                        }
                    }

                    $type_name = strtolower($record["type"]);
                    if (isset($type_list[$type_name]))
                    {
                        $record['type'] = $type_list[$type_name];
                        $msg_list['Type Found']++;
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['city_id'], $record['city_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

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

                            $city_name = strtolower($record["city_id"]);

                            $city = City::where("state_id", $record['state_id'])
                                            ->where("district_id", $record['district_id'])
                                            ->where("name", $city_name)->select(["id", "district_id"])->first();

                            if ($city)
                            {
                                $record['city_id'] = $city->id;
                                $msg_list['City Found']++;
                            }
                            else
                            {
                                LaravelExtend::addError($header_flip['city_id'], $record['city_id'] . " is not found", "Row #" . ($i + 2));
                                $validate = false;
                            }
                        }
                        else
                        {
                            LaravelExtend::addError($header_flip['district_id'], $record['district_id'] . " is not found", "Row #" . ($i + 2));
                            $validate = false;
                        }
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['state_id'], $record['state_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    if ($validate)
                    {
                        $model = new $this->modelClass();

                        $record['created_by'] = $record['updated_by'] = LaravelExtend::getAuthUserId();

                        // d($record, true);

                        $is_insert = $is_update = null;
                        if ($model->insertOrUpdate($record, $is_insert, $is_update, true))
                        {
                            $accept_count++;

                            if ($is_insert)
                            {
                                $msg_list['Party Insert']++;
                            }
                            else if ($is_update)
                            {
                                $msg_list['Party Update']++;
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

        $type_list = Party::getTypeList();

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['name'] => $record['name'],
                $field_labels['type'] => get_value_from_list($type_list, $record['type']),
                $field_labels['mobile'] => $record['mobile'] ?? "",
                $field_labels['mobile2'] => $record['mobile2'] ?? "",
                $field_labels['phone_no'] => $record['phone_no'] ?? "",
                $field_labels['email'] => $record['email'] ?? "",
                $field_labels['company_name'] => $record['company_name'] ?? "",
                $field_labels['gst_no'] => $record['gst_no'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['district_id'] => $record['district']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",
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


    public function history($party_id, Request $request)
    {
        $record = $this->modelClass::with([
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "history" => function($q){
                $q->orderBy("id", "DESC");
            },
            "partyFile",
            "documentFile"
        ])->findOrFail($party_id);

        $record->convertTimeZoneForDisplay();

        $form = [
            'url' => route($this->routePrefix . '.history_store', $party_id),
            'method' => 'POST',
        ];

        $histories = $record->history;
        $addresses = $record->address?->toArray() ?? [];

        $model = new PartyHistory();

        $this->setForView(compact("record", "form", "model", "histories", "addresses"));

        return $this->view(__FUNCTION__);
    }

    public function history_store($party_id, Request $request)
    {
        $party = $this->modelClass::findOrFail($party_id);

        $rules = [
            "action_type" => ["required"],
            "status_type" => ["required"],
            "action" => "",
            "response_type_id" => "",
            "response" => "",
            "remarks" => "",
            "next_followup_date_time" => "",
        ];

        switch($request->input("action_type"))
        {
            case PartyHistory::ACTION_TYPE_CALL:
                $rules['action'] = ["required"];
                $rules['response_type_id'] = ["required"];
                break;
            case PartyHistory::ACTION_TYPE_SEND_SMS:
            case PartyHistory::ACTION_TYPE_SEND_EMAIL:
            case PartyHistory::ACTION_TYPE_SEND_WHATSAPP:
                $rules['action'] = ["required"];
                break;
        }

        switch($request->input("status_type"))
        {
            case Lead::STATUS_COLD:
            case Lead::STATUS_HOT:
            case Lead::STATUS_WARM:
                $rules['next_followup_date_time'] = ["required"];
                break;
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $party->canChangeHistory();

            $validatedData['party_id'] = $party_id;

            $model = new PartyHistory();

            $validatedData = $model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $validatedData['assign_to_user_id'] = $party->assign_to_user_id;

            $model = PartyHistory::create($validatedData);

            $party->action_type = $validatedData['action_type'];
            $party->remarks = $validatedData['remarks'];

            if ($validatedData['response_type_id'])
            {
                $party->response_type_id = $validatedData['response_type_id'];                
            }

            $party->save();

            if ($validatedData['status_type'] != $party->status_type)
            {
                $party->changeStatus($validatedData['status_type']);
            }

            if ($validatedData['next_followup_date_time'])
            {
                $party->changeNextFollowupDateTime($validatedData['next_followup_date_time']);
            }

            if ($validatedData['action_type'] == PartyHistory::ACTION_TYPE_CALL && $party->assignTo?->name)
            {
                $voip_no = $party->assignTo->name;
                if (!is_numeric($voip_no))
                {
                    $msg = "User " . $party->assignTo->getDisplayName() . " has name : $voip_no, but only numeric is used for voip call";
                    throw new UserException($msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $url = config("app.ASTERISK_SERVER_URL") . "?voip_no=" . $voip_no;

                $storage_path = Party::getStoragePath() . "asterisk/" . $voip_no;

                $disk = Party::getStorageDisk();

                $full_path = $disk->path($storage_path);

                $current_dt = DateUtility::getDateObj(null);
                $current_dt->setTimezone(DateUtility::$login_user_time_zone);
                $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);

                get_and_save_file_from_url($url, $full_path . "/" . $datetime . ".wav");

                $model->recording_file = $storage_path . "/" . $datetime . ".wav";
                $model->save();
            }

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 1,
                ]);
            }

            $this->saveSqlLog();

            return back()->with('success', "History Saved successfully");
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


    public function change_files($lead_id, Request $request)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $rules = [
            "dealerFile" => ["required"],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {

            if (isset($validatedData['dealerFile'])) {

                $ids = explode(",", $validatedData['dealerFile']);

                $document_files = DocumentFile::find($ids);

                $lead->documentFile()->sync($document_files);
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


    public function transfer($party_id, Request $request)
    {
        $party = $this->modelClass::findOrFail($party_id);

        $rules = [
            "action_type" => ["required"],
            "assign_to_user_id" => ["required"],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            if ($party->status_type == Lead::STATUS_DEAD)
            {
                throw new UserException("Party is dead", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($party->status_type == Lead::STATUS_FAKE)
            {
                throw new UserException("Party is Fake", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($validatedData['assign_to_user_id'] == $party->assign_to_user_id)
            {
                throw new UserException("Party is already assigned to this user", UserException::ERROR_USER_ACTION_RESTRICT);
            }
            else
            {
                $party->changeAssignTo($validatedData);
            }

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 1,
                ]);
            }


            $this->saveSqlLog();

            return back()->with('success', "History Saved successfully");
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

            $this->saveSqlLog();

            $request = request();

            if ($request->ajax()) {
                return $this->responseJson([
                    'status' => 0,
                    'msg' => get_exception_display_message($ex),
                    "custom_errors" => LaravelExtend::$errors
                ]);
            }

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    protected function delete(BaseModelInterface $model)
    {
        try {
            DB::beginTransaction();

            $contact = Contact::where("type", "party")->where("ref_id", $model->id)->first();

            if (!$contact->safeDelete($contact))
            {
                throw new \Exception("Record has associated data. can't delete");
            }
            
            $this->deleteCascade($model);

            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }
}
