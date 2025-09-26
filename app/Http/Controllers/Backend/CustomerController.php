<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\BaseModelInterface;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\DispatchOrderProduct;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
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
use App\Models\Party;
use App\Models\Product;
use App\Models\State;
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

class CustomerController extends BackendController
{
    public String $routePrefix = "customer";

    protected $modelClass = Customer::class;

    protected Customer | null $model = null;

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
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "response_type_id", "type" => "int"],
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

    public function convert_to_lead($customer_id)
    {
        $customer = $this->modelClass::with(["lead"])->findOrFail($customer_id);

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

        $state_list = State::getIndianList();

        $type_conditions = [
            'type' => Type::getType($lead, "deal_type")
        ];

        if ($this->model->deal_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("deal_type", $this->model->deal_type);
        }

        $deal_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType(new Customer(), "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");
        $status_type_list_for_form = array_diff($status_type_list, ["Converted", "Fake"]);

        $type_conditions = [
            'type' => Type::getType($lead, "payment_method_type")
        ];

        if ($this->model->payment_method_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("payment_method_type", $this->model->payment_method_type);
        }

        $payment_method_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $dealer_list = Dealer::getList();

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

        $history_field_labels = FieldSetting::getDynamicFields(LeadHistory::class);

        $this->setForView(compact(
            "address_type_list", "state_list",
            "deal_type_list", "status_type_list", "status_type_list_for_form",
            "payment_method_type_list", "dealer_list", "product_list", "item_list",
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
        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

        $district_cities = City::getWithDistrict([["country_id", $my_country->id]], [["is_active", 1]], [["is_active", 1]]);
        
        $district_city_collection = collect($district_cities);

        $district_city_list = $district_city_collection->pluck('name', 'id')->toArray();

        $this->setForView(compact("district_cities", "district_city_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "mobile" => ["required", new MobileNumber],
            "name" => ["nullable"],
            "mobile2" => ["nullable", new MobileNumber],
            "state_id" => ["nullable"],
            "city_id" => ["nullable"],
            "assign_to_user_id" => ["nullable"],
            "status_type" => ["required"],
            "company_name" => ["nullable"],
            "brand_name" => ["nullable"],
            "company_gst_no" => ["nullable"],
            "deal_type" => ["nullable"],
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

            if (empty($save_data['name']))
            {
                $save_data['name'] = "Unknown";
            }

            $lead = Lead::create($save_data);
            $save_data['lead_id'] = $lead->id;
            $save_data['assign_to_user_id'] = LaravelExtend::getAuthUserId();

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
        if (isset($validatedData['city_id']) && $validatedData['city_id'])
        {
            $my_country = Country::getMyCountry();

            $district_cities = City::getWithDistrict([["country_id", $my_country->id]], [["is_active", 1]], [["is_active", 1]]);

            foreach($district_cities as $city)
            {
                if ($city['id'] == $validatedData['city_id'])
                {
                    $validatedData['district_id'] = $city['district_id'];
                    break;
                }
            }

            if (!isset($validatedData['district_id']))
            {
                LaravelExtend::addError("City", "District ID not found");
            }
        }

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

            $child_model = new LeadAddress();

            $existing_id_list = $this->model->lead->address()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['addresses'] as $address)
            {
                $address['lead_id'] = $this->model->lead->id;
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

            $child_model = new LeadProduct();

            $existing_id_list = $this->model->lead->products()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['products'] as $product)
            {
                $product['lead_id'] = $this->model->lead->id;
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
                "lead_id" => ["required"],
                "uom_id" => ["required"],
                "qty" => ["required"],
            ];

            $child_model = new LeadItem();

            $existing_id_list = $this->model->lead->items()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['items'] as $item)
            {
                $item['lead_id'] = $this->model->lead->id;
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
                    $child_model = LeadItem::find($item['id']);

                    if (!$child_model)
                    {
                        $child_model = new LeadItem();
                    }
                    else
                    {
                        unset($existing_id_list[$item['id']]);
                    }
                }
                else
                {
                    $child_model = new LeadItem();
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

    public function history($customer_id, Request $request)
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
        ])->findOrFail($customer_id);

        $record->convertTimeZoneForDisplay();

        $form = [
            'url' => route('lead.history_store', $record->lead_id),
            'method' => 'POST',
        ];

        $histories = $record->lead->history;
        $products = $record->lead->products?->toArray() ?? [];
        $items = $record->lead->items?->toArray() ?? [];
        $addresses = $record->lead->address?->toArray() ?? [];

        $carrier_list = Party::getList("id", "display_name", ["type" => Party::TYPE_CARRIER]);

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $model = new LeadHistory();

        $this->setForView(compact("record", "form", "model", "histories", "products", "items", "addresses", "carrier_list", "make_list"));

        return $this->view(__FUNCTION__);
    }

    
    public function dispatch($customer_id, Request $request)
    {
        $this->model = $customer = $this->modelClass::with("lead")->findOrFail($customer_id);

        $rules = [
            "entry_by" => ["required"],
            "action_type" => ["required"],
            "status_type" => ["required"],
            "purpose_price" => ["required"],
            "payable_amount" => ["required"],
            "payment_method_type" => ["required"],
            "next_meeting_date_time" => "",
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

        $dispatchData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            if ($customer->status_type == Lead::STATUS_DEAD)
            {
                throw new UserException("Customer is dead", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($customer->status_type == Lead::STATUS_FAKE)
            {
                throw new UserException("Customer is Fake", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            $customer->canChangeHistory();

            $model = new LeadHistory();

            $validatedData['lead_id'] = $customer->lead_id;
            $validatedData = $model->convertTimeZoneBeforeQueryToDatabase($validatedData);
            $validatedData['assing_to_user_id'] = $customer->assign_to_user_id;

            $model = LeadHistory::create($validatedData);

            if ($validatedData['status_type'] != $customer->status_type)
            {
                $customer->changeStatus($validatedData['status_type']);
            }

            if ($validatedData['next_meeting_date_time'])
            {
                $customer->changeNextFollowupDateTime($validatedData['next_meeting_date_time']);
            }

            $customer->purpose_price = $validatedData['purpose_price'];
            $customer->payable_amount = $validatedData['payable_amount'];
            $customer->payment_method_type = $validatedData['payment_method_type'];
            $customer->save();

            $this->_saveOthers($validatedData);

            $contact = Contact::where("type", "customer")->where("ref_id", $customer_id)->first();

            if (!$contact)
            {
                throw new UserException("Contact of Lead not found");
            }

            $dispatch = new DispatchOrder();
            $dispatch->fill($dispatchData);
            $dispatch->contact_id = $contact->id;
            $dispatch->order_from_type = "customer";
            $dispatch->status_type = DispatchOrder::STATUS_PENDING;

            $my_country = Country::getMyCountry();

            if (!$my_country)
            {
                throw new UserException("Country India not found");
            }

            if ($customer->deal_type == Lead::DEAL_TYPE_THROGH_DEALER)
            {
                $primary_address = $customer->dealer->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("Dealer have no Primary Address", UserException::ERROR_USER_DATA_WRONG);
                }

                $dispatch->addr1 = $primary_address->addr1;
                $dispatch->addr2 = $primary_address->addr2;
                $dispatch->pincode = $primary_address->pincode;
                $dispatch->country_id = $my_country->id;
                $dispatch->state_id = $customer->dealer->state_id;
                $dispatch->district_id = $customer->dealer->district_id;
                $dispatch->city_id = $customer->dealer->city_id;
            }
            else
            {
                $primary_address = $customer->lead->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("No Primary Address Found", UserException::ERROR_USER_DATA_WRONG);
                }
                
                $dispatch->addr1 = $primary_address->addr1;
                $dispatch->addr2 = $primary_address->addr2;
                $dispatch->pincode = $primary_address->pincode;
                $dispatch->country_id = $my_country->id;
                $dispatch->state_id = $customer->state_id;
                $dispatch->district_id = $customer->district_id;
                $dispatch->city_id = $customer->city_id;
            }

            if (!$dispatch->save())
            {
                throw new UserException("Fail To Save Dispatch", UserException::ERROR_LARAVEL_FAIL);
            }

            $existing_id_list = $dispatch->products()->pluck("id", "id")->toArray();

            foreach($customer->lead->products as $product)
            {
                $save = $product->toArray();
                unset($save['id']);
                $save['dispatch_order_id'] = $dispatch->id;

                $child_model = new DispatchOrderProduct($save);

                $child_model = DispatchOrderProduct::insertOrUpdate($save, $is_insert, $is_update);

                unset($existing_id_list[$child_model->id]);
            }

            if ($existing_id_list)
            {
                $existing_records = $dispatch->products()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }

            $existing_id_list = $dispatch->items()->pluck("id", "id")->toArray();

            foreach($customer->lead->items as $item)
            {
                $save = $item->toArray();
                unset($save['id']);
                $save['dispatch_order_id'] = $dispatch->id;

                $child_model = new DispatchOrderItem($save);

                $child_model = DispatchOrderItem::insertOrUpdate($save, $is_insert, $is_update);

                unset($existing_id_list[$child_model->id]);
            }

            if ($existing_id_list)
            {
                $existing_records = $dispatch->items()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }

            $notification_data = [
                'from_user_id' => LaravelExtend::getAuthUserId(),
                "sub_type" => Notification::SUB_TYPE_INFO,
                "msg" => "New Dispatch Order of Customer from Sales"
            ];

            $notifyObj = new GeneralNotification($notification_data);

            $user_id_list = NotificationSetting::getUserIdList("on_dispatch_order_from_sales_lead");

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

            return back()->with('success', "Dispatch Order placed successfully");
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

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
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

        $type_group_list = Type::getListOfModel(Customer::class);

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
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['district_id'] => $record['district']['name'] ?? "",
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
        $file = $path . "Customer-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
    
    protected function delete(BaseModelInterface $model)
    {
        try {
            DB::beginTransaction();

            $contact = Contact::where("type", "customer")->where("ref_id", $model->id)->first();

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
