<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\FieldSetting;
use App\Models\ProductSerial;
use App\Models\Service;
use App\Models\ServiceIssue;
use App\Models\User;
use App\Models\ViewContact;
use App\Models\ViewExportContact;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BackendController
{
    public String $routePrefix = "service";

    protected $modelClass = Service::class;
    protected Service | null $model = null;

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
            ["field" => "ticket_no", "type" => "string"],
            ["field" => "contact_id", "type" => "int"],
            ["field" => "export_contact_id", "type" => "int"],
            ["field" => "is_fixed", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

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

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $issue_type_list = Service::getTypeList();

        $contact_type_list = [
            "sale" => "Sale Contact",
            "export" => "Export Contact",
        ];

        $contacts = ViewContact::getList();

        $contact_list = [];

        foreach($contacts as $contact)
        {
           $contact_list[$contact['id']] = $contact['name'];
        }

        $export_contacts = ViewExportContact::getList();

        $export_contact_list = [];

        foreach($export_contacts as $contact)
        {
           $export_contact_list[$contact['id']] = $contact['name'];
        }

        $product_serials = ProductSerial::with([
            "product",
            "maker",
        ])->get();

        $product_serial_list = [];

        foreach ($product_serials as $record)
        {
            $display = $record->serial_no;

            $display .= " (" . ($record->product?->model_name ?? "-") . ")";
            $display .= " (" . ($record->maker?->name ?? "-") . ")";

            $product_serial_list[$record->id] = $display;
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $service_issue_field_labels = FieldSetting::getDynamicFields(ServiceIssue::class);

        $this->setForView(compact("contact_type_list", "issue_type_list", "contact_list", "export_contact_list", "product_serial_list",
            "field_labels", "service_issue_field_labels"
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

        $model->ticket_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_SERVICE_TICKET);

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "product_serial_id" => ["required"],
            "contact_type" => ["required"],
            "contact_id" => [],
            "export_contact_id" => [],
            "charge_fee" => "",
            "remarks" => "",
            "is_fixed" => "",
            "service_issues" => [],
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

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {

            $this->model->fill($validatedData);

            $this->model->ticket_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_SERVICE_TICKET);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_SERVICE_TICKET);

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


    private function _saveOthers($validatedData)
    {
        unset($validatedData['service_issues']['(sr-id)']);

        if (isset($validatedData['service_issues']))
        {
            // d($validatedData['service_issues'], true);
            $base_rules = [
                "issue_type" => ["required"],
                "issue_id" => ["nullable"],
                "issue" => ["nullable"],
                "solution_type" => ["required"],
                "issue_solution_id" => ["nullable"],
                "solution" => ["nullable"],
            ];

            $child_model = new ServiceIssue();

            $existing_id_list = $this->model->serviceIssue()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['service_issues'] as $specification)
            {
                $specification['service_id'] = $this->model->id;
                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($specification));

                $validator = Validator::make($specification, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Specification");
                    }
                    continue;
                }

                $display_order++;
                if ($specification['id'])
                {
                    $child_model = ServiceIssue::find($specification['id']);

                    if (!$child_model)
                    {
                        $child_model = new ServiceIssue();
                    }
                    else
                    {
                        unset($existing_id_list[$specification['id']]);
                    }
                }
                else
                {
                    $child_model = new ServiceIssue();
                }

                // d($specification); exit;

                $child_model->fill($specification);

                $this->model->serviceIssue()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->serviceIssue()->where("id", $existing_id_list)->get();

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
        $model = $this->model = $this->modelClass::with(["serviceIssue"])->findOrFail($id);

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

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        // d($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {

            $this->model->fill($validatedData);

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

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "productSerial",
            "productSerial.product",
            "productSerial.maker",
            "contact",
            "exportContact",
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

        $contact_type_list = [
            "sale" => "Sale Contact",
            "export" => "Export Contact",
        ];

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['ticket_no'] => $record['ticket_no'],
                $field_labels['product_serial_id'] => "`" . $record['product_serial']['serial_no'] ?? "",
                'Serial Manufacture Date Time' => $record['product_serial']['manufacture_date_time'] ?? "",
                'Model Name' => $record['product_serial']['product']['model_name'] ?? "",
                'Brand' => $record['product_serial']['maker']['name'] ?? "",
                $field_labels['contact_type'] => get_value_from_list($contact_type_list, $record['contact_type']),
                $field_labels['contact_id'] => $record['contact']['display_name'],
                $field_labels['export_contact_id'] => $record['export_contact']['display_name'],
                $field_labels['remarks'] => $record['remarks'] ?? "",
                $field_labels['charge_fee'] => $record['charge_fee'] ?? "",
                $field_labels['is_fixed'] => get_value_from_list($yes_no_list, $record['is_fixed']),

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

    public function export_detail_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "productSerial",
            "productSerial.product",
            "productSerial.maker",
            "contact",
            "exportContact",
            "serviceIssue",
            "serviceIssue.issue",
            "serviceIssue.issueSolution",
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

        $issue_type_list = Service::getTypeList();

        $contact_type_list = [
            "sale" => "Sale Contact",
            "export" => "Export Contact",
        ];

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $service_issue_field_labels = FieldSetting::getDynamicFields(ServiceIssue::class);

        foreach($records as $record)
        {
            foreach($record['service_issue'] as $serviceIssue)
            {
                $csv_records[] = [
                    'ID' => $record['id'],
                    $field_labels['ticket_no'] => $record['ticket_no'],
                    $field_labels['product_serial_id'] => "`" . $record['product_serial']['serial_no'] ?? "",
                    'Serial Manufacture Date Time' => $record['product_serial']['manufacture_date_time'] ?? "",
                    'Model Name' => $record['product_serial']['product']['model_name'] ?? "",
                    'Brand' => $record['product_serial']['maker']['name'] ?? "",
                    $field_labels['contact_type'] => get_value_from_list($contact_type_list, $record['contact_type']),
                    $field_labels['contact_id'] => $record['contact']['display_name'],
                    $field_labels['export_contact_id'] => $record['export_contact']['display_name'],
                    $field_labels['remarks'] => $record['remarks'] ?? "",
                    $field_labels['charge_fee'] => $record['charge_fee'] ?? "",
                    $field_labels['is_fixed'] => get_value_from_list($yes_no_list, $record['is_fixed']),

                    $service_issue_field_labels['issue_type'] => get_value_from_list($issue_type_list, $serviceIssue['issue_type']),
                    $service_issue_field_labels['issue_id'] => $serviceIssue['issue']['name'] ?? $serviceIssue['issue'],
                    $service_issue_field_labels['solution_type'] => get_value_from_list($issue_type_list, $serviceIssue['solution_type']),
                    $service_issue_field_labels['issue_solution_id'] => $serviceIssue['issue_solution']['name'] ?? $serviceIssue['solution'],

                    'Created' => if_date_time($serviceIssue['created_at']),
                    'Created By' => $user_cache_list[$serviceIssue['created_by']] ?? "",
                    'Updated' => if_date_time($serviceIssue['updated_at']),
                    'Updated By' => $user_cache_list[$serviceIssue['updated_by']] ?? "",
                ];
            }
        }

        // d($csv_records, true);

        $this->saveSqlLog();

        $path = laravel_constant('path.temp');
        FileUtility::createFolder($path);

        $current_dt = DateUtility::getDateObj(null);
        $current_dt->setTimezone(DateUtility::$login_user_time_zone);
        $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);
        $file_prefix = str_class_name_to_human_text(ServiceIssue::class);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
