<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\BaseModelInterface;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\ExpCity;
use App\Models\ExpCountry;
use App\Models\ExpCustomer;
use App\Models\ExpDealer;
use App\Models\ExpLead;
use App\Models\ExpLeadAddress;
use App\Models\ExpLeadHistory;
use App\Models\ExpLeadItem;
use App\Models\ExpLeadProduct;
use App\Models\ExportContact;
use App\Models\ExpParty;
use App\Models\ExpState;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\Lead;
use App\Models\LeadAddress;
use App\Models\LeadHistory;
use App\Models\LeadItem;
use App\Models\LeadProduct;
use App\Models\Maker;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Product;
use App\Models\ShipOrder;
use App\Models\ShipOrderItem;
use App\Models\ShipOrderProduct;
use App\Models\Type;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Rules\MobileNumber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class ExportCustomerController extends BackendController
{
    public String $routePrefix = "export-customer";

    protected $modelClass = ExpCustomer::class;

    protected ExpCustomer | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "lead",
            "lead.products",
            "lead.address",
            "lead.documentFile",
        ]);

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
            ["field" => "mobile2", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "deal_type", "type" => "string"],
            ["field" => "dealer_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "country_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "response_type_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "state", "country", "city",
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
        
        $state_list = $city_list = [];

        $state_conditions = ["is_active" => 1];

        if (isset($condition_list['country_id']))
        {
            $state_conditions["country_id"] = $condition_list['country_id'];
            $state_list = ExpState::getList("id", "display_name", $state_conditions, "name");
        }
        
        $city_conditions = ["is_active" => 1];

        if (isset($condition_list['state_id']))
        {
            $city_conditions["state_id"] = $condition_list['state_id'];
            $city_list = ExpCity::getList("id", "display_name", $city_conditions, "name");
        }
    
        $this->setForView(compact("state_list", "city_list"));

        return $builder;
    }

    public function convert_to_lead($exp_customer_id)
    {
        $customer = $this->modelClass::with(["lead"])->findOrFail($exp_customer_id);

        $customer->convertToLead();

        return redirect()->route("lead.index")->with('success', "Convert to Lead Successfully");
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $lead = new Lead();

        $address_type_list = LeadAddress::getTypeList();

        $country_list = ExpCountry::getListCache();

        $type_conditions = [
            'type' => Type::getType($lead, "deal_type")
        ];

        if ($this->model->deal_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("deal_type", $this->model->deal_type);
        }

        $deal_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($lead, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $status_type_list_for_form = array_diff($status_type_list, ["Converted", "Dead", "Fake", "Lost"]);

        // dd($status_type_list_for_form);

        $type_conditions = [
            'type' => Type::getType($lead, "payment_method_type")
        ];

        if ($this->model->payment_method_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("payment_method_type", $this->model->payment_method_type);
        }

        $payment_method_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $dealer_list = ExpDealer::getList();

        $product_list = Product::getList();

        $item_list = Item::getList();

        $user_list = User::getList("id", "display_name", ["type" => User::TYPE_INTERNAL], "person_name");

        $type_conditions = [
            'type' => Type::getType(new LeadHistory(), "action_type")
        ];

        $action_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $lead_history_response_list = DropDownValue::getList("id", "display_name", ["type" =>  DropDownValue::LEAD_HISTORY_RESPONSE], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $address_field_labels = FieldSetting::getDynamicFields(LeadAddress::class);

        $product_field_labels = FieldSetting::getDynamicFields(LeadProduct::class);

        $item_field_labels = FieldSetting::getDynamicFields(LeadItem::class);

        $history_field_labels = FieldSetting::getDynamicFields(ExpLeadHistory::class);

        $this->setForView(compact(
            "country_list", "address_type_list",
            "deal_type_list", "status_type_list", "status_type_list_for_form", "payment_method_type_list", "dealer_list", "product_list", "item_list",
            "user_list", "action_type_list", "lead_history_response_list",
            "field_labels", "address_field_labels", "product_field_labels", "item_field_labels", "history_field_labels"
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

        $model->assign_to_user_id = LaravelExtend::getAuthUserId();

        $this->setForView(compact("model", 'form'));

        $this->_set_date_for_add_and_edit();

        return $this->view("form");
    }

    private function _set_date_for_add_and_edit()
    {
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "country_id" => ["required"],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "assign_to_user_id" => ["required"],
            "status_type" => ["required"],
            "company_name" => ["nullable"],
            "brand_name" => ["nullable"],
            "company_gst_no" => ["nullable"],
            "deal_type" => ["required"],
            "dealer_id" => [],
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

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $save_data = $this->_beforeSave($validatedData);
            unset($save_data['leadFile']);
            unset($save_data['addresses']);
            unset($save_data['products']);

            $save_data['is_customer'] = 1;
            $lead = ExpLead::create($save_data);

            $save_data['exp_lead_id'] = $lead->id;

            $this->model = $this->modelClass::create($save_data);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            $save_and_close = (bool) request("save_and_close", false);

            if ($save_and_close)
            {
                return redirect()->to(get_current_path_url([
                    'last_insert_id' => $this->model->id,
                    "last_insert_type" => "customer"
                ]));
            }

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _beforeSave($validatedData)
    {
        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['leadFile'])) {

            $ids = explode(",", $validatedData['leadFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->lead->documentFile()->sync($document_files);
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

            $child_model = new ExpLeadAddress();

            $existing_id_list = $this->model->lead->address()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['addresses'] as $address)
            {
                $address['exp_lead_id'] = $this->model->lead->id;
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
                    $child_model = ExpLeadAddress::find($address['id']);

                    if (!$child_model)
                    {
                        $child_model = new ExpLeadAddress();
                    }
                    else
                    {
                        unset($existing_id_list[$address['id']]);
                    }
                }
                else
                {
                    $child_model = new ExpLeadAddress();
                }

                $address['list_display_order'] = $display_order;

                $child_model->fill($address);

                $this->model->lead->address()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->lead->address()->where("id", $existing_id_list)->get();

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

            $child_model = new ExpLeadProduct();

            $existing_id_list = $this->model->lead->products()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['products'] as $product)
            {
                $product['exp_lead_id'] = $this->model->lead->id;
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
                    $child_model = ExpLeadProduct::find($product['id']);

                    if (!$child_model)
                    {
                        $child_model = new ExpLeadProduct();
                    }
                    else
                    {
                        unset($existing_id_list[$product['id']]);
                    }
                }
                else
                {
                    $child_model = new ExpLeadProduct();
                }

                $product['list_display_order'] = $display_order;

                $child_model->fill($product);

                $this->model->lead->products()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->lead->products()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }
        }

        unset($validatedData['items']['(sr-id)']);

        if (isset($validatedData['items']))
        {
            $base_rules = [
                "item_id" => ["required"],
                "uom_id" => ["required"],
                "qty" => ["required"],
            ];

            $child_model = new ExpLeadItem();

            $existing_id_list = $this->model->lead->items()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['items'] as $item)
            {
                $item['exp_lead_id'] = $this->model->lead->id;
                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($item));

                $validator = Validator::make($item, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Items");
                    }
                    continue;
                }

                $display_order++;
                if ($item['id'])
                {
                    $child_model = ExpLeadItem::find($item['id']);

                    if (!$child_model)
                    {
                        $child_model = new ExpLeadItem();
                    }
                    else
                    {
                        unset($existing_id_list[$item['id']]);
                    }
                }
                else
                {
                    $child_model = new ExpLeadItem();
                }

                $item['list_display_order'] = $display_order;

                $child_model->fill($item);

                $this->model->lead->items()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->lead->items()->where("id", $existing_id_list)->get();

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
            "lead",
            "lead.leadFile",
            "lead.address" => function($q){
                $q->orderBy("list_display_order");
            },
            "lead.products",
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $products = $this->model->lead->products?->toArray() ?? [];
        $addresses = $this->model->lead->address?->toArray() ?? [];

        $this->setForView(compact("model", "form", "products", "addresses"));
        $this->_set_date_for_add_and_edit();

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $save_data = $this->_beforeSave($validatedData);
            unset($save_data['leadFile']);
            unset($save_data['addresses']);
            unset($save_data['products']);

            $this->model->fill($save_data);

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
        $this->model = $model = $this->modelClass::with([
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "products"
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

    public function history($exp_customer_id, Request $request)
    {
        $record = $this->modelClass::with([
            "lead",
            "lead.address" => function($q){
                $q->orderBy("list_display_order");
            },
            "lead.products",
            "lead.items",
            "lead.history" => function($q){
                $q->orderBy("id", "DESC");
            },
            "lead.leadFile",
            "lead.documentFile",
        ])->findOrFail($exp_customer_id);

        $record->convertTimeZoneForDisplay();

        // d($record->toArray(), true);

        $form = [
            'url' => route('export-lead.history_store', $record->exp_lead_id),
            'method' => 'POST',
        ];

        $histories = $record->lead->history;
        $products = $record->lead->products?->toArray() ?? [];
        $items = $record->lead->items?->toArray() ?? [];
        $addresses = $record->lead->address?->toArray() ?? [];

        $carrier_list = ExpParty::getList("id", "display_name", ["type" => ExpParty::TYPE_CARRIER]);

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $model = new ExpLeadHistory();

        $this->setForView(compact("record", "form", "model", "histories", "products", "items", "addresses", "carrier_list", "make_list"));

        return $this->view(__FUNCTION__);
    }

    
    public function dispatch($exp_customer_id, Request $request)
    {
        $this->model = $exp_customer = $this->modelClass::findOrFail($exp_customer_id);

        $rules = [
            "entry_by" => ["required"],
            "action_type" => ["required"],
            "status_type" => ["required"],
            "purpose_price" => ["required"],
            "payable_amount" => ["required"],
            "payment_method_type" => ["required"],
            "next_followup_date_time" => "",
            "remarks" => "",
            "products" => [],
            "items" => [],
            "addresses" => [],
        ];

        $validatedData = $request->validate($rules);

        $rules = [
            "shipping_fee" => ["required"],
            "remarks" => "",
        ];

        $shipOrderData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            if ($exp_customer->status_type == ExpLead::STATUS_DEAD)
            {
                throw new UserException("Customer is dead", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($exp_customer->status_type == ExpLead::STATUS_FAKE)
            {
                throw new UserException("Customer is Fake", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            $exp_customer->canChangeHistory();

            $model = new ExpLeadHistory();

            $validatedData['exp_lead_id'] = $exp_customer->exp_lead_id;
            $validatedData = $model->convertTimeZoneBeforeQueryToDatabase($validatedData);
            $validatedData['assing_to_user_id'] = $exp_customer->assign_to_user_id;

            $model = ExpLeadHistory::create($validatedData);

            if ($validatedData['status_type'] != $exp_customer->status_type)
            {
                $exp_customer->changeStatus($validatedData['status_type']);
            }

            if ($validatedData['next_followup_date_time'])
            {
                $exp_customer->changeNextFollowupDateTime($validatedData['next_followup_date_time']);
            }

            $exp_customer->purpose_price = $validatedData['purpose_price'];
            $exp_customer->payable_amount = $validatedData['payable_amount'];
            $exp_customer->payment_method_type = $validatedData['payment_method_type'];
            $exp_customer->save();

            $this->_saveOthers($validatedData);

            $shipOrder = new ShipOrder();

            $contact = ExportContact::where("type", "customer")->where("ref_id", $exp_customer_id)->first();

            if (!$contact)
            {
                throw new UserException("Contact of Lead not found");
            }

            $shipOrder->fill($shipOrderData);
            $shipOrder->order_from_type = "customer";
            $shipOrder->export_contact_id = $contact->id;
            $shipOrder->status_type = ShipOrder::STATUS_PENDING;

            if ($exp_customer->deal_type == Lead::DEAL_TYPE_THROGH_DEALER)
            {
                $primary_address = $exp_customer->expDealer->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("Dealer have no Primary Address", UserException::ERROR_USER_DATA_WRONG);
                }

                $shipOrder->addr1 = $primary_address->addr1;
                $shipOrder->addr2 = $primary_address->addr2;
                $shipOrder->zipcode = $primary_address->pincode;
                $shipOrder->country_id = $exp_customer->expDealer->country_id;
                $shipOrder->state_id = $exp_customer->expDealer->state_id;
                $shipOrder->city_id = $exp_customer->expDealer->city_id;
            }
            else
            {
                $primary_address = $exp_customer->lead->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("No Primary Address Found", UserException::ERROR_USER_DATA_WRONG);
                }

                $shipOrder->addr1 = $primary_address->addr1;
                $shipOrder->addr2 = $primary_address->addr2;
                $shipOrder->zipcode = $primary_address->pincode;
                $shipOrder->country_id = $exp_customer->country_id;
                $shipOrder->state_id = $exp_customer->state_id;
                $shipOrder->city_id = $exp_customer->city_id;
            }

            if (!$shipOrder->save())
            {
                throw new UserException("Fail To Save Dispatch", UserException::ERROR_LARAVEL_FAIL);
            }

            $existing_id_list = $shipOrder->shipOrderProduct()->pluck("id", "id")->toArray();

            foreach($exp_customer->lead->products as $product)
            {
                $save = $product->toArray();
                unset($save['id']);
                $save['ship_order_id'] = $shipOrder->id;

                $child_model = new ShipOrderProduct($save);

                $child_model = ShipOrderProduct::insertOrUpdate($save, $is_insert, $is_update);

                unset($existing_id_list[$child_model->id]);
            }

            if ($existing_id_list)
            {
                $existing_records = $shipOrder->shipOrderProduct()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->getDisplayName(), "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }

            $existing_id_list = $shipOrder->shipOrderItem()->pluck("id", "id")->toArray();

            foreach($exp_customer->lead->items as $item)
            {
                $save = $item->toArray();
                unset($save['id']);
                $save['ship_order_id'] = $shipOrder->id;
                $child_model = new ShipOrderItem($save);

                $child_model = ShipOrderItem::insertOrUpdate($save, $is_insert, $is_update);

                unset($existing_id_list[$child_model->id]);
            }

            if ($existing_id_list)
            {
                $existing_records = $shipOrder->shipOrderItem()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->getDisplayName(), "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }

            $notification_data = [
                'from_user_id' => LaravelExtend::getAuthUserId(),
                "sub_type" => Notification::SUB_TYPE_INFO,
                "msg" => "New Ship Order of Customer from Exports"
            ];

            $notifyObj = new GeneralNotification($notification_data);

            $user_id_list = NotificationSetting::getUserIdList("on_ship_order_from_export_lead");

            if ($user_id_list)
            {
                $users = User::find($user_id_list);

                foreach($users as $user)
                {
                    $user->notify($notifyObj);
                }
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

            return back()->with('success', "Ship Order placed successfully");
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
                ]);
            }

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }


    public function ajax_get_list(Request $request)
    {
        $response = ["status" => 1, "data" => []];

        try
        {
            $search_term = $request->get("query");

            if ($search_term && strlen(trim($search_term)) > 0)
            {
                $search_term = urldecode(trim($search_term));
                
                $records = $this->modelClass::
                            where("mobile", 'LIKE', '%' . $search_term . '%')
                            ->orWhere("mobile2", 'LIKE', '%' . $search_term . '%')
                            ->orderBy("name", "ASC")
                            ->limit(20)
                            ->get();
                 
            }
            else 
            {
                $limit = $request->get("limit");

                if ($limit)
                {
                    $records = $this->modelClass::orderBy("name", "ASC")
                                ->limit($limit)
                                ->get();
                }
            }

            if  (isset($records) && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $row = [];
                    $row['display'] = $record->getDisplayName();
                    $row['url'] = route($this->routePrefix . ".edit", [$record]);

                    $response['data'][] = $row;
                }
            }    
        }
        catch(Exception $ex)
        {
            Log::error($ex->getMessage());
            throw $ex;
        }

        return $this->responseJson($response);
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

        d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $type_group_list = Type::getListOfModel(Lead::class);

        $drop_down_list = DropDownValue::getListCache("id", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['name'] => $record['name'],
                $field_labels['mobile'] => $record['mobile'],
                $field_labels['mobile2'] => $record['mobile2'] ?? "",
                $field_labels['email'] => $record['email'] ?? "",
                $field_labels['status_type'] => get_value_from_list($type_group_list['status_type'], $record['status_type']),
                $field_labels['assign_to_user_id'] => $record['assign_to']['display_name'] ?? "",
                $field_labels['deal_type'] => get_value_from_list($type_group_list['deal_type'], $record['deal_type']),
                $field_labels['dealer_id'] => $record['dealer']['name'] ?? "",
                $field_labels['country_id'] => $record['district']['name'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",
                $field_labels['company_name'] => $record['company_name'] ?? "",
                $field_labels['brand_name'] => $record['brand_name'] ?? "",
                $field_labels['company_gst_no'] => $record['company_gst_no'] ?? "",
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
        $file = $path . "Export-Customer-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    protected function delete(BaseModelInterface $model)
    {
        try {
            DB::beginTransaction();

            $contact = ExportContact::where("type", "customer")->where("ref_id", $model->id)->first();

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
