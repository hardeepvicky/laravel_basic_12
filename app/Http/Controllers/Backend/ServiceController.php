<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\FieldSetting;
use App\Models\ProductSerial;
use App\Models\Service;
use App\Models\ServiceIssue;
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

    public function copy($id)
    {
        $model = $this->model = $this->modelClass::with(["issueSolution"])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }
}
