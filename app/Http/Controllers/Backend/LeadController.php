<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\Dealer;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Lead;
use App\Models\LeadAddress;
use App\Models\LeadProduct;
use App\Models\Product;
use App\Models\State;
use App\Models\Type;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class LeadController extends BackendController
{
    public String $routePrefix = "lead";

    protected $modelClass = Lead::class;

    protected $model = null;

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
            ["field" => "status_type", "type" => "string"],
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "source_type", "type" => "string"],
            ["field" => "source_id", "type" => "int"],
            ["field" => "deal_type", "type" => "string"],
            ["field" => "dealer_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "state", "district", "city",
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "documentFile"
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

        $address_type_list = LeadAddress::getTypeList();

        $state_list = State::getIndianList();

        $type_conditions = [
            'type' => Type::getType($this->model, "source_type")
        ];

        if ($this->model->source_type)
        {
            $type_conditions['or_id'] = $this->model->source_type;
        }

        $source_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "deal_type")
        ];

        if ($this->model->deal_type)
        {
            $type_conditions['or_id'] = $this->model->deal_type;
        }

        $deal_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = $this->model->status_type;
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $source_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::LEAD_SOURCE], "name");

        $dealer_list = Dealer::getList();

        $product_list = Product::getList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $address_field_labels = FieldSetting::getDynamicFields(LeadAddress::class);

        $product_field_labels = FieldSetting::getDynamicFields(LeadProduct::class);
        
        
        $this->setForView(compact(
            "address_type_list", "state_list", 
            "source_type_list", "deal_type_list", "status_type_list", "source_list", "dealer_list", "product_list",
            "field_labels", "address_field_labels", "product_field_labels"
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

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "state_id" => ["required"],
            "district_id" => ["required"],
            "city_id" => ["required"],
            "status_type" => ["required"],
            "company_name" => ["nullable"],
            "brand_name" => ["nullable"],
            "company_gst_no" => ["nullable"],
            "source_type" => ["nullable"],
            "source_id" => ["nullable"],
            "purpose_price" => ["nullable"],
            "payment_method_type" => ["nullable"],
            "profile_img" => ["nullable"],
            "remarks" => ["nullable"],
            "leadFile" => [],
            "addresses" => [],
            "products" => [],
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
                throw new Exception("There are some errors");
            }

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['leadFile'])) {

            $ids = explode(",", $validatedData['leadFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
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

            $child_model = new LeadAddress();

            $existing_id_list = $this->model->address()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['addresses'] as $address)
            {
                $address['lead_id'] = $this->model->id;
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
                    $child_model = LeadAddress::find($address['id']);

                    if (!$child_model)
                    {
                        $child_model = new LeadAddress();
                    }
                    else
                    {
                        unset($existing_id_list[$address['id']]);
                    }
                }
                else
                {
                    $child_model = new LeadAddress();
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

        unset($validatedData['products']['(sr-id)']);

        if (isset($validatedData['products']))
        {
            $base_rules = [
                "product_id" => ["required"],
                "qty" => ["required"],
            ];

            $child_model = new LeadProduct();

            $existing_id_list = $this->model->products()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['products'] as $product)
            {
                $product['lead_id'] = $this->model->id;
                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($product));

                $validator = Validator::make($product, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {               
                        LaravelExtend::addError("", $error_msg, "Products");
                    }
                    continue;
                }

                $display_order++;
                if ($product['id'])
                {
                    $child_model = LeadProduct::find($product['id']);

                    if (!$child_model)
                    {
                        $child_model = new LeadProduct();
                    }
                    else
                    {
                        unset($existing_id_list[$product['id']]);
                    }
                }
                else
                {
                    $child_model = new LeadProduct();
                }

                $product['list_display_order'] = $display_order;

                $child_model->fill($product);

                $this->model->products()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->products()->where("id", $existing_id_list)->get();

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
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "products"
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $products = $this->model->products?->toArray() ?? [];
        $addresses = $this->model->address?->toArray() ?? [];

        $this->setForView(compact("model", "form", "products", "addresses"));

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
        $this->model = $model = $this->modelClass::with([
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $addresses = $this->model->address?->toArray() ?? [];

        $this->setForView(compact("model", "form", "addresses"));

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
}
