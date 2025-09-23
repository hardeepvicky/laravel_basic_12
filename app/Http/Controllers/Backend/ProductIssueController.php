<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\FieldSetting;
use App\Models\ProductCategory;
use App\Models\ProductIssue;
use App\Models\ProductIssueSolution;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ProductIssueController extends BackendController
{
    public String $routePrefix = "product-issue";

    protected $modelClass = ProductIssue::class;
    protected ProductIssue | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $this->setForView(compact("records", "product_category_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
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

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $issue_solution_field_labels = FieldSetting::getDynamicFields(ProductIssueSolution::class);

        $this->setForView(compact("field_labels", "issue_solution_field_labels"));

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

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    private function set_data_for_add_edit($model)
    {
        $qb = ProductCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->product_category_id)
        {
            $qb->orWhere("id", $model->product_category_id);
        }

        $product_category_records = ProductCategory::getTreeRecords($qb);

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name");;

        $this->setForView(compact("product_category_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "product_category_id" => ["required"],
            "name" => ["required"],
            "list_display_order" => "",
            "is_active" => "",
            "issue_solutions" => []
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


    private function _saveOthers($validatedData)
    {
        unset($validatedData['issue_solutions']['(sr-id)']);

        if (isset($validatedData['issue_solutions']))
        {
            // d($validatedData['issue_solutions'], true);

            $base_rules = [
                "name" => ["required"],
            ];

            $child_model = new ProductIssueSolution();

            $existing_id_list = $this->model->issueSolution()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['issue_solutions'] as $specification)
            {
                $specification['issue_id'] = $this->model->id;
                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($specification));

                $validator = Validator::make($specification, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Issue");
                    }
                    continue;
                }

                $display_order++;
                if ($specification['id'])
                {
                    $child_model = ProductIssueSolution::find($specification['id']);

                    if (!$child_model)
                    {
                        $child_model = new ProductIssueSolution();
                    }
                    else
                    {
                        unset($existing_id_list[$specification['id']]);
                    }
                }
                else
                {
                    $child_model = new ProductIssueSolution();
                }

                $specification['list_display_order'] = $display_order;

                $child_model->fill($specification);

                $this->model->issueSolution()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->issueSolution()->where("id", $existing_id_list)->get();

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
        $model = $this->model = $this->modelClass::with(["issueSolution"])->findOrFail($id);

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

        $this->set_data_for_add_edit($model);

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

    public function ajax_get_list()
    {
        $response = ["status" => 1];

        $condition_list = $this->getConditions(null, [
            ["field" => "product_serial_id", "type" => "int"],
        ], true);

        $qb = $this->modelClass::query();

        if ( isset($condition_list['product_serial_id']) )
        {
            $product_serial = ProductSerial::with(["product"])->find($condition_list['product_serial_id']);

            if ($product_serial)
            {
                $qb->where("product_category_id", $product_serial->product->product_category_id);
            }
        }

        $records = $qb->get();

        $response['data'] = [];

        foreach ($records as $record) {
            $row = [
                "id" => $record->id,
                "name" => $record->getDisplayName(),
            ];

            $response['data'][] = $row;
        }

        return $this->responseJson($response);
    }

    public function ajax_get_solution_list()
    {
        $response = ["status" => 1];

        $condition_list = $this->getConditions(null, [
            ["field" => "issue_id", "type" => "int"],
            ["field" => "product_serial_id", "type" => "int"],
        ], true);

        $qb = $this->modelClass::query();

        if ( isset($condition_list['issue_id']) )
        {
            $qb->where("id", $condition_list['issue_id']);
        }

        if ( isset($condition_list['product_serial_id']) )
        {
            $product_serial = ProductSerial::with(["product"])->find($condition_list['product_serial_id']);

            if ($product_serial)
            {
                $qb->where("product_category_id", $product_serial->product->product_category_id);
            }
        }

        $records = $qb->with(["issueSolution"])->get();

        $response['data'] = [];

        foreach ($records as $record) {

            foreach($record->issueSolution as $issueSolution)
            {
                $row = [
                    "id" => $issueSolution->id,
                    "name" => $issueSolution->getDisplayName(),
                ];

                $response['data'][] = $row;
            }
        }

        return $this->responseJson($response);
    }
}
