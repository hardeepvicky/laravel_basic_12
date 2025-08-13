<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\Carrier;
use App\Models\City;
use App\Models\Country;
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
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Product;
use App\Models\State;
use App\Models\Type;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class LeadController extends BackendController
{
    public String $routePrefix = "lead";

    protected $modelClass = Lead::class;

    protected Lead | null $model = null;

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
        $builder->where("is_customer", 0);

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

        $my_country = Country::getMyCountry();

        $district_cities = City::getWithDistrict([["country_id", $my_country->id]], [["is_active", 1]], [["is_active", 1]]);
        
        $district_city_collection = collect($district_cities);

        $district_city_list = $district_city_collection->pluck('name', 'id')->toArray();

        $type_conditions = [
            'type' => Type::getType($this->model, "source_type")
        ];

        if ($this->model->source_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("source_type", $this->model->source_type);
        }

        $source_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "deal_type")
        ];

        if ($this->model->deal_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("deal_type", $this->model->deal_type);
        }

        $deal_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($this->model, "payment_method_type")
        ];

        if ($this->model->payment_method_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("payment_method_type", $this->model->payment_method_type);
        }

        $payment_method_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $source_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::LEAD_SOURCE], "name");

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
            "address_type_list", "state_list", "district_cities", "district_city_list",
            "source_type_list", "deal_type_list", "status_type_list", "source_list", "payment_method_type_list", "dealer_list", "product_list", "item_list",
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

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "assign_to_user_id" => ["required"],
            "status_type" => ["required"],
            "company_name" => ["nullable"],
            "brand_name" => ["nullable"],
            "company_gst_no" => ["nullable"],
            "source_type" => ["nullable"],
            "source_id" => ["nullable"],
            "deal_type" => ["required"],
            "dealer_id" => [],
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

            $this->model = $this->modelClass::create($save_data);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
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

        unset($validatedData['items']['(sr-id)']);

        if (isset($validatedData['items']))
        {
            $base_rules = [
                "lead_id" => ["required"],
                "uom_id" => ["required"],
                "qty" => ["required"],
            ];

            $child_model = new LeadItem();

            $existing_id_list = $this->model->items()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['items'] as $item)
            {
                $item['lead_id'] = $this->model->id;
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

                $this->model->items()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->items()->where("id", $existing_id_list)->get();

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

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
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

    public function history($lead_id, Request $request)
    {
        $record = $this->modelClass::with([
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "products",
            "items",
            "history" => function($q){
                $q->orderBy("id", "DESC");
            },
            "leadFile",
            "documentFile"
        ])->findOrFail($lead_id);

        $record->convertTimeZoneForDisplay();

        // d($record->toArray(), true);

        $form = [
            'url' => route($this->routePrefix . '.history_store', $lead_id),
            'method' => 'POST',
        ];

        $products = $record->products?->toArray() ?? [];
        $items = $record->items?->toArray() ?? [];
        $addresses = $record->address?->toArray() ?? [];

        $carrier_list = Carrier::getList();

        $model = new LeadHistory();

        $this->setForView(compact("record", "form", "model", "products", "items", "addresses", "carrier_list"));

        return $this->view(__FUNCTION__);
    }

    public function history_store($lead_id, Request $request)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $rules = [
            "entry_by" => ["required"],
            "action_type" => ["required"],
            "status_type" => ["required"],
            "action" => "",
            "response_type_id" => "",
            "response" => "",
            "remarks" => "",
            "next_meeting_date_time" => "",
        ];

        switch($request->input("action_type"))
        {
            case LeadHistory::ACTION_TYPE_CALL:
                $rules['action'] = ["required"];
                $rules['response_type_id'] = ["required"];                                
                break;
            case LeadHistory::ACTION_TYPE_SEND_SMS:
            case LeadHistory::ACTION_TYPE_SEND_EMAIL:
            case LeadHistory::ACTION_TYPE_SEND_WHATSAPP:
                $rules['action'] = ["required"];
                break;
            case LeadHistory::ACTION_TYPE_CHANGE_STATUS:
                $rules['status_type'] = ["required"];
                break;
            case LeadHistory::ACTION_TYPE_LEAD_TRANSFER:
                $rules['assing_to_user_id'] = ["required"];
                break;
            case LeadHistory::ACTION_TYPE_DISPATCH:
                $rules['remarks'] = ["required"];
                break;
        }

        switch($request->input("status_type"))
        {
            case Lead::STATUS_COLD:
            case Lead::STATUS_HOT:
            case Lead::STATUS_WARM:    
                $rules['next_meeting_date_time'] = ["required"];
                break;        
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $lead->canChangeHistory();

            $validatedData['lead_id'] = $lead_id;

            $model = new LeadHistory();

            $validatedData = $model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $model = LeadHistory::create($validatedData);

            if ($validatedData['status_type'] != $lead->status_type)
            {
                $lead->changeStatus($validatedData['status_type']);
            }

            if ($validatedData['next_meeting_date_time'])
            {
                $lead->changeNextFollowupDateTime($validatedData['next_meeting_date_time']);
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

    public function transfer_lead($lead_id, Request $request)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $rules = [
            "entry_by" => ["required"],
            "action_type" => ["required"],
            "assign_to_user_id" => ["required"],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            if ($lead->status_type == Lead::STATUS_DEAD)
            {
                throw new UserException("Lead is dead", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($lead->status_type == Lead::STATUS_FAKE)
            {
                throw new UserException("Lead is Fake", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($validatedData['assign_to_user_id'] == $lead->assign_to_user_id)
            {
                throw new UserException("Lead is already assigned to this user", UserException::ERROR_USER_ACTION_RESTRICT);
            }
            else
            {
                $lead->changeAssignTo($validatedData);
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

    public function dispatch($lead_id, Request $request)
    {
        $this->model = $lead = $this->modelClass::findOrFail($lead_id);

        $rules = [
            "entry_by" => ["required"],
            "action_type" => ["required"],
            "status_type" => ["required"],
            "payable_amount" => ["required"],
            "next_meeting_date_time" => "",
            "remarks" => "",
            "products" => [],
            "items" => [],
            "addresses" => [],
        ];

        $validatedData = $request->validate($rules);

        $rules = [
            "carrier_id" => ["required"],
            "docket_no" => ["required"],
            "builty_no" => "",
            "tracking_no" => "",
            "tracking_phone_no" => "",
            "shipping_fee" => ["required"],
            "shipping_fee_gst_per" => ["required"],
            "remarks" => "",
        ];

        $dispatchData = $request->validate($rules);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            if ($lead->status_type == Lead::STATUS_DEAD)
            {
                throw new UserException("Lead is dead", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($lead->status_type == Lead::STATUS_FAKE)
            {
                throw new UserException("Lead is Fake", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            $lead->canChangeHistory();

            $model = new LeadHistory();

            $validatedData['lead_id'] = $lead_id;
            $validatedData = $model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $model = LeadHistory::create($validatedData);

            if ($validatedData['status_type'] != $lead->status_type)
            {
                $lead->changeStatus($validatedData['status_type']);
            }

            if ($validatedData['next_meeting_date_time'])
            {
                $lead->changeNextFollowupDateTime($validatedData['next_meeting_date_time']);
            }

            $this->_saveOthers($validatedData);

            $dispatch = new DispatchOrder();
            $dispatch->type = str_class_name_to_human_text($this->modelClass);
            $dispatch->status_type = DispatchOrder::STATUS_PENDING;
            $dispatch->lead_id = $lead_id;

            $dispatch->fill($dispatchData);

            $my_country = Country::getMyCountry();

            if ($lead->deal_type == Lead::DEAL_TYPE_THROGH_DEALER)
            {
                $primary_address = $lead->dealer->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("Dealer have no Primary Address", UserException::ERROR_USER_DATA_WRONG);
                }

                $dispatch->addr1 = $primary_address->addr1;
                $dispatch->addr2 = $primary_address->addr2;
                $dispatch->pincode = $primary_address->pincode;
                $dispatch->country_id = $my_country->id;
                $dispatch->state_id = $lead->dealer->state_id;
                $dispatch->district_id = $lead->dealer->district_id;
                $dispatch->city_id = $lead->dealer->city_id;
            }
            else
            {
                $primary_address = $lead->address()->where("is_primary", 1)->first();

                if (!$primary_address)
                {
                    throw new UserException("No Primary Address Found", UserException::ERROR_USER_DATA_WRONG);
                }
                
                $dispatch->addr1 = $primary_address->addr1;
                $dispatch->addr2 = $primary_address->addr2;
                $dispatch->pincode = $primary_address->pincode;
                $dispatch->country_id = $my_country->id;
                $dispatch->state_id = $lead->state_id;
                $dispatch->district_id = $lead->district_id;
                $dispatch->city_id = $lead->city_id;
            }

            if (!$dispatch->save())
            {
                throw new UserException("Fail To Save Dispatch", UserException::ERROR_LARAVEL_FAIL);
            }

            $existing_id_list = $this->model->products()->pluck("id", "id")->toArray();

            foreach($lead->products as $product)
            {
                $save = $product->toArray();
                unset($save['id']);
                unset($save['lead_id']);
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

            $existing_id_list = $this->model->items()->pluck("id", "id")->toArray();

            foreach($lead->items as $item)
            {
                $save = $item->toArray();
                unset($save['id']);
                unset($save['lead_id']);
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
                "msg" => "New Dispatch Order of Lead from Sales"
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
                ]);
            }

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function history_destroy($id)
    {
        $request = request();

        try {
            $model = TaskTakenAction::findOrFail($id);

            if (!$this->delete($model)) {
                throw new Exception("Fail to Delete");
            }

            $msg = "Task History deleted successfully";

            $this->saveSqlLog();

            if ($request->ajax()) {
                return response()->json(['status' => 1, 'msg' => $msg]);
            }

            return back()->with('success', $msg);

        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }

    public function change_files($lead_id, Request $request)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $rules = [
            "leadFile" => ["required"],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {

            if (isset($validatedData['leadFile'])) {

                $ids = explode(",", $validatedData['leadFile']);

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

    public function convert_to_customer($lead_id)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $lead->is_customer = 1;
        $lead->status_type = Lead::STATUS_CONVERTED;

        $lead->save();

        return redirect()->route("customer.index")->with('success', "Convert to Customer Successfully");
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

        $yes_no_list = laravel_constant('yes_no_list');

        $type_list = Type::getListCache("code", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $user_list = [];

            foreach($record['user'] as $user)
            {
                $user_list[] = $user['display_name'];
            }

            $tag_list = [];

            foreach($record['tags'] as $tag)
            {
                $tag_list[] = $tag['display_name'];
            }

            $csv_records[] = [
                'ID' => $record['id'],

                $field_labels['assign_to_user_id'] => $record['assign_to_user']['display_name'],
                $field_labels['dept_id'] => $record['department']['display_name'],
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['name'] => $record['name'],
                $field_labels['description'] => $record['description'] ?? "",
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['start_date_time'] => if_date_time($record['start_date_time']),
                $field_labels['duration'] => $record['duration'],
                $field_labels['duration_type'] => get_value_from_list($type_list, $record['duration_type']),
                $field_labels['end_date_time'] => if_date_time($record['end_date_time']),
                $field_labels['priority'] => $record['priority'],
                $field_labels['status'] => get_value_from_list($type_list, $record['status']),
                $field_labels['is_draft'] => get_value_from_list($yes_no_list, $record['is_draft']),
                $field_labels['is_completed'] => get_value_from_list($yes_no_list, $record['is_completed']),
                $field_labels['complete_date_time'] => if_date_time($record['complete_date_time']),
                $field_labels['complete_by'] => get_value_from_list($user_cache_list, $record['complete_by']),
                $field_labels['is_verified'] => get_value_from_list($yes_no_list, $record['is_verified']),
                $field_labels['verified_date_time'] => if_date_time($record['verified_date_time']),
                $field_labels['verified_by'] => get_value_from_list($user_cache_list, $record['verified_by']),
                $field_labels['rating'] => $record['rating'] ?? "",
                $field_labels['review_remarks'] => $record['review_remarks'] ?? "",
                $field_labels['taskUser'] => implode(", ", $user_list),
                $field_labels['tag_name'] => implode(", ", $tag_list),

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
        $file = $path . $this->tableName . "_$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
