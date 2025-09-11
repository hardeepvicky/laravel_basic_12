<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderProduct;
use App\Models\DispatchOrderProductSerial;
use App\Models\FieldSetting;
use App\Models\Maker;
use App\Models\Party;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\SaleOrder;
use App\Models\State;
use App\Models\StockProduct;
use App\Models\Type;
use App\Models\User;
use App\Models\ViewContact;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DispatchOrderController extends BackendController
{
    public String $routePrefix = "dispatch-order";

    protected $modelClass = DispatchOrder::class;

    protected DispatchOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "products", 
            "products.product",
            "products.product.category",
            "products.dispatchOrderProductSerial",
            "items"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $sale_order_condition_list = $this->getConditions($cache_key, [
            ["field" => "sale_order_no", "type" => ""],
        ], true);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "contact_id", "type" => "int"],
            ["field" => "type", "type" => "string"],
            ["field" => "status_type", "type" => "string"],
            ["field" => "tracking_no", "type" => "string"],
            ["field" => "builty_no", "type" => "string"],
            ["field" => "docket_no", "type" => "string"],
            ["field" => "pincode", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "carrier_id", "type" => "int"],
            ["field" => "from_date", "type" => "date", "view_field" => "from_date"],
            ["field" => "to_date", "type" => "date", "view_field" => "to_date"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::query();

        if (isset($condition_list['from_date']))
        {
            $builder->whereDate('created_at', ">=", $condition_list['from_date']);
        }

        if (isset($condition_list['to_date']))
        {
            $builder->whereDate('created_at', "<=", $condition_list['to_date']);
        }

        $conditions = $array_helper->unsetValues($conditions, ["from_date", "to_date"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        $builder->where($conditions);

        $builder->with([
            "state", "district", "city",
            "contact",
            "saleOrder",
        ]);

        if (isset($sale_order_condition_list['sale_order_no']))
        {
            $builder->whereHas("saleOrderProduct", function($q) use($sale_order_condition_list) {
                $q->whereHas("saleOrder", function($q)  use($sale_order_condition_list) {
                    $q->where("order_no", "like", "%" . $sale_order_condition_list['sale_order_no']['value'] . "%");
                });
            });
        }

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

        $state_list = State::getList("id", "display_name", ["country_id" => $my_country->id]);
        $district_city_list = [];

        if (isset($condition_list['state_id']))
        {
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
        }
        

        $this->setForView(compact("state_list", "district_city_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $country_list = Country::getList("id", "name");

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $carrier_list = Party::getList("id", "display_name", ["type" => Party::TYPE_CARRIER]);

        $contacts = ViewContact::getList("id", "display_name", [], "id", "DESC");

        $contact_list = [];

        foreach($contacts as $k => $arr)
        {
            $contact_list[$k] = $arr['name'];
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact(
            "country_list", "status_type_list", "carrier_list", "contact_list",
            "field_labels",
        ));

        return parent::beforeViewRender();
    }

    public function export_csv()
    {
        $this->model = new $this->modelClass();

        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "saleOrder",
            "saleOrder.branch",
            "contact",
            "country",
            "state",
            "district",
            "city",
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];
        
        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $sale_order_field_labels = FieldSetting::getDynamicFields(SaleOrder::class);

        foreach ($records as $model) {

            $record = $model->toArray();

            $csv_records[] = [
                'ID' => $record['id'],
                $sale_order_field_labels['order_no'] => $record['sale_order']['order_no'] ?? "",
                $sale_order_field_labels['party_order_ref_no'] => $record['sale_order']['party_order_ref_no'] ?? "",
                $sale_order_field_labels['branch_id'] => $record['sale_order']['branch']['display_name'] ?? "",

                $field_labels['order_from_type'] => $model->orderFrom(),
                $field_labels['contact_id'] => $record['contact']['name'] ?? "",
                $field_labels['status_type'] => get_value_from_list($status_type_list, $record['status_type']),

                $sale_order_field_labels['estimated_dispatch_date'] => $record['sale_order']['estimated_dispatch_date'] ?? "",

                $field_labels['addr1'] => $record['addr1'] ?? "",
                $field_labels['addr2'] => $record['addr2'] ?? "",
                $field_labels['pincode'] => $record['pincode'] ?? "",
                $field_labels['country_id'] => $record['country']['name'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['district_id'] => $record['district']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",

                $field_labels['docket_no'] => $record['docket_no'] ?? "",
                $field_labels['builty_no'] => $record['builty_no'] ?? "",
                $field_labels['tracking_no'] => $record['tracking_no'] ?? "",
                $field_labels['tracking_phone_no'] => $record['tracking_phone_no'] ?? "",
                $field_labels['shipping_fee'] => $record['shipping_fee'] ?? "",
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
        $file_prefix = str_class_name_to_human_text($this->modelClass);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function export_product_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "saleOrder",
            "saleOrder.branch",
            "contact",
            "products", 
            "products.product",
            "products.product.category",
            "products.dispatchOrderProductSerial",
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];
        
        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $product_list = Product::getListCache();
        $make_list = Maker::getListCache();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $sale_order_field_labels = FieldSetting::getDynamicFields(SaleOrder::class);

        $dispatch_order_product_field_labels = FieldSetting::getDynamicFields(DispatchOrderProduct::class);

        foreach ($records as $model) {

            $record = $model->toArray();
            
            foreach($model->products as $dispatchOrderProduct)
            {
                $child_record = $dispatchOrderProduct->toArray();

                $csv_records[] = [
                    'ID' => $record['id'],
                    $sale_order_field_labels['order_no'] => $record['sale_order']['order_no'] ?? "",
                    $sale_order_field_labels['party_order_ref_no'] => $record['sale_order']['party_order_ref_no'] ?? "",
                    $sale_order_field_labels['branch_id'] => $record['sale_order']['branch']['display_name'] ?? "",

                    $field_labels['order_from_type'] => $model->orderFrom(),
                    $field_labels['contact_id'] => $record['contact']['name'] ?? "",
                    $field_labels['status_type'] => get_value_from_list($status_type_list, $record['status_type']),

                    $dispatch_order_product_field_labels['product_id'] => get_value_from_list($product_list, $child_record['product_id']),
                    $dispatch_order_product_field_labels['make_id'] => get_value_from_list($make_list, $child_record['make_id']),
                    $dispatch_order_product_field_labels['qty'] => $child_record['qty'] ?? "",
                    'Sent Qty' => $dispatchOrderProduct->dispatchOrderProductSerial->count() ?? "",

                    'Created' => if_date_time($dispatchOrderProduct['created_at']),
                    'Created By' => $user_cache_list[$dispatchOrderProduct['created_by']] ?? "",
                    'Updated' => if_date_time($dispatchOrderProduct['updated_at']),
                    'Updated By' => $user_cache_list[$dispatchOrderProduct['updated_by']] ?? "",
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
        $file_prefix = str_class_name_to_human_text(DispatchOrderProduct::class);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function create_from_sale_order(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $save_records = [];

            foreach($request->sale_order_ids as $sale_order_id)
            {
                $sale_order = SaleOrder::with([
                    "saleOrderProduct",
                    "saleOrderProduct.manufactureOrder",
                    "contact"
                ])->findOrFail($sale_order_id);

                if (!$sale_order->contact)
                {
                    continue;
                }

                $total_sale_order_qty = 0;

                foreach($sale_order->saleOrderProduct as $saleOrderProduct)
                {
                    $total_sale_order_qty += $saleOrderProduct->order_qty;
                }

                $save_data = [
                    'dispatch_order' => [],
                    'dispatch_order_products' => [],
                    'saleOrder' => $sale_order,
                    "total_sale_order_qty" => $total_sale_order_qty
                ];

                $save_data['dispatch_order'] = [
                    "order_from_type" => $sale_order->order_from_type,
                    "contact_id" => $sale_order->contact_id,
                    "sale_order_id" => $sale_order->id,
                ];

                switch($sale_order->order_from_type)
                {
                    case "lead":
                        $sale_order->contact->load("lead");
                        $address = $sale_order->contact->lead->address()->where("is_primary", 1)->first();
                        $state_id = $sale_order->contact->lead->state_id;
                        $district_id = $sale_order->contact->lead->district_id;
                        $city_id = $sale_order->contact->lead->city_id;
                        break;
                    case "customer":
                        $sale_order->contact->load("customer");
                        $address = $sale_order->contact->customer->lead->address()->where("is_primary", 1)->first();
                        $state_id = $sale_order->contact->customer->state_id;
                        $district_id = $sale_order->contact->customer->district_id;
                        $city_id = $sale_order->contact->customer->city_id;
                        break;
                    case "party":
                        $sale_order->contact->load("party");
                        $address = $sale_order->contact->party->address()->where("is_primary", 1)->first();
                        $state_id = $sale_order->contact->party->state_id;
                        $district_id = $sale_order->contact->party->district_id;
                        $city_id = $sale_order->contact->party->city_id;
                        break;
                    case "dealer":
                        $sale_order->contact->load("dealer");
                        $address = $sale_order->contact->dealer->address()->where("is_primary", 1)->first();
                        $state_id = $sale_order->contact->dealer->state_id;
                        $district_id = $sale_order->contact->dealer->district_id;
                        $city_id = $sale_order->contact->dealer->city_id;
                        break;
                    case "competitor":
                        $sale_order->contact->load("competitor");
                        $address = $sale_order->contact->competitor->address()->where("is_primary", 1)->first();
                        $state_id = $sale_order->contact->competitor->state_id;
                        $district_id = $sale_order->contact->competitor->district_id;
                        $city_id = $sale_order->contact->competitor->city_id;
                        break;
                }

                if (!isset($address) || empty($address))
                {
                    throw new UserException("Address not found of Order # " . $sale_order->getDisplayName());
                }

                $save_data['dispatch_order']['addr1'] = $address->addr1;
                $save_data['dispatch_order']['addr2'] = $address->addr2;
                $save_data['dispatch_order']['pincode'] = $address->pincode;
                $save_data['dispatch_order']['country_id'] = Country::getMyCountry()?->id;
                $save_data['dispatch_order']['state_id'] = $state_id;
                $save_data['dispatch_order']['district_id'] = $district_id;
                $save_data['dispatch_order']['city_id'] = $city_id;
                $save_data['dispatch_order']['status_type'] = DispatchOrder::STATUS_PENDING;

                foreach($sale_order->saleOrderProduct as $saleOrderProduct)
                {
                    $pendig_qty = $saleOrderProduct->pendingOrderQty();

                    if ($pendig_qty)
                    {
                        $save_data['dispatch_order_products'][] = [
                            "product_id" => $saleOrderProduct->product_id,
                            "make_id" => $saleOrderProduct->make_id,
                            "qty" => $pendig_qty,
                        ];

                        $saleOrderProduct->increment('sent_qty', $pendig_qty);
                    }
                }

                if ($save_data['dispatch_order_products'])
                {
                    $save_records[$sale_order->contact_id][] = $save_data;
                }
            }

            $save_DO_count = 0;
            foreach($save_records as $sale_records)
            {
                foreach($sale_records as $save_record)
                {
                    $old_dispatch_orders = DispatchOrder::with(["products"])->where("sale_order_id", $save_record['dispatch_order']['sale_order_id'])->get();

                    $dispatched_qty = 0;

                    foreach($old_dispatch_orders as $old_dispatch_order)
                    {
                        foreach($old_dispatch_order->products as $product)
                        {
                            $dispatched_qty += $product->qty;
                        }
                    }

                    if ($save_record['total_sale_order_qty'] > $dispatched_qty)
                    {
                        $model = DispatchOrder::create($save_record['dispatch_order']);
                        foreach($save_record['dispatch_order_products'] as $dispatch_product)
                        {
                            $child_model = new DispatchOrderProduct($dispatch_product);
                            $model->products()->save($child_model);
                        }

                        $save_record['saleOrder']->status = SaleOrder::STATUS_GODOWN;
                        $save_record['saleOrder']->save();
                        $save_record['saleOrder']->checkForSent();

                        $save_DO_count++;
                    }
                }
            }

            DB::commit();

            return back()->with("success", "$save_DO_count Dispatch Orders successfully placed");
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            return back()->with("fail", get_exception_display_message($ex));
        }
    }

    private function _common_validation_rules()
    {
        return [
            'status_type' => ["required"],
            'docket_no' => ["required"],
            'builty_no' => ["nullable"],
            'tracking_no' => ["nullable"],
            'tracking_phone_no' => ["nullable"],
            'shipping_fee' => ["required", "numeric"],
            'carrier_id' => [],
            "remarks" => []
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
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
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            // dd($validatedData);
            $validatedData = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            if ($validatedData['status_type'] != DispatchOrder::STATUS_PENDING)
            {
                if (!$this->model->canMarkSent())
                {
                    throw new UserException("Can not Mark Sent before scan all serials");
                }
            }

            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex));
        }
    }

    public function send($id, Request $request)
    {
        if ($request->isMethod("post"))
        {
            $response = ["status" => 0];
            $data = $request->all();

            try
            {
                $required_fields = ["product_serial_no"];

                foreach($required_fields as $field)
                {
                    if (!isset($data[$field]) || empty($data[$field]))
                    {
                        throw new UserException("Missing or empty $field", UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $product_serial_no = ProductSerial::where("serial_no", $data['product_serial_no'])->first();

                if (!$product_serial_no)
                {
                    throw new UserException("Wrong Serial No.", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$product_serial_no->make_id)
                {
                    throw new UserException("Serial No. have no make", UserException::ERROR_USER_DATA_WRONG);
                }

                $dispatchOrderProduct = DispatchOrderProduct::where("dispatch_order_id", $id)->where("product_id", $product_serial_no->product_id)->where("make_id", $product_serial_no->make_id)->with([
                    "dispatchOrder"
                ])->first();

                if (!$dispatchOrderProduct)
                {
                    $product = Product::findOrFail($product_serial_no->product_id);
                    $make = Maker::findOrFail($product_serial_no->make_id);

                    $msg = "Product " . $product->getDisplayName();
                    $msg .= " with Make " . $make->getDisplayName();
                    $msg .= " not included in Order";

                    throw new UserException($msg);
                }

                if ($dispatchOrderProduct->qty == $dispatchOrderProduct->dispatchOrderProductSerial->count())
                {
                    throw new UserException("All Quantity has sent of this product");
                }

                DB::beginTransaction();

                if ($product_serial_no->is_in)
                {
                    StockProduct::changeQty($product_serial_no->branch_id, $product_serial_no->bin_id, $product_serial_no->product_id, $product_serial_no->make_id, -1);

                    $product_serial_no->markOut();
                }

                $product_serial_no->is_sold = 1;
                $product_serial_no->save();

                $dispatchOrderProductSerial = new DispatchOrderProductSerial([
                    "product_serial_id" => $product_serial_no->id
                ]);

                $dispatchOrderProductSerial = $dispatchOrderProduct->dispatchOrderProductSerial()->save($dispatchOrderProductSerial);

                DB::commit();

                $response['product_serial_no'] = $data['product_serial_no'];
                $response['status'] = 1;
                $response['delete_url'] = route($this->routePrefix . ".ajax_delete_product_serial", [$dispatchOrderProductSerial->id]);
                $response['dispatchOrderProduct'] = [
                    "id" => $dispatchOrderProduct->id,
                    "sent_qty" => $dispatchOrderProduct->dispatchOrderProductSerial()->count()
                ];

            }
            catch(Exception $ex)
            {
                DB::rollBack();

                $response['status'] = 0;
                $response['msg'] = $ex->getMessage();
            }

            return $this->responseJson($response);
        }

        $model = $this->model = $this->modelClass::with([
            "products.dispatchOrderProductSerial.productSerial"
        ])->findOrFail($id);

        $this->setForView(compact("model"));

        return $this->view(__FUNCTION__);
    }

    public function ajax_delete_product_serial($id, Request $request)
    {
        $response = ["status" => 0];

        try
        {
            DB::beginTransaction();

            $model = DispatchOrderProductSerial::with([
                "productSerial",
                "dispatchOrderProduct.dispatchOrder"
            ])->find($id);
          
            if (!$model)
            {
                throw new UserException("Wrong ID");
            }

            if (empty($model->dispatchOrderProduct->dispatchOrder->status_type) || $model->dispatchOrderProduct->dispatchOrder->status_type == DispatchOrder::STATUS_PENDING)
            {
                $model->delete();
            }
            else
            {
                $this->model = new $this->modelClass();

                $type_conditions = [
                    'type' => Type::getType($this->model, "status_type")
                ];
                
                $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");
                
                $msg = "Dispatch Order has Status of " . get_value_from_list($status_type_list, $model->dispatchOrderProduct->dispatchOrder->status_type);
                $msg .= ", Can't Delete";

                throw new UserException($msg);
            }

            $model->productSerial->is_sold = 1;
            $model->productSerial->save();

            DB::commit();

            $response['product_serial_no'] = $model->productSerial->serial_no;
            $response['status'] = 1;            
            $response['dispatchOrderProduct'] = [
                "id" => $model->dispatchOrderProduct->id,
                "sent_qty" => $model->dispatchOrderProduct->dispatchOrderProductSerial()->count()
            ];

        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
       
    }
}

