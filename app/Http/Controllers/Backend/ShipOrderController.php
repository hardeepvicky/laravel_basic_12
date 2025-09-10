<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\ExpCity;
use App\Models\ExpCountry;
use App\Models\ExportOrder;
use App\Models\ExpParty;
use App\Models\ExpState;
use App\Models\FieldSetting;
use App\Models\Maker;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ShipOrder;
use App\Models\ShipOrderProduct;
use App\Models\ShipOrderProductSerial;
use App\Models\StockProduct;
use App\Models\Type;
use App\Models\User;
use App\Models\ViewExportContact;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ShipOrderController extends BackendController
{
    public String $routePrefix = "ship-order";

    protected $modelClass = ShipOrder::class;

    protected ShipOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "shipOrderProduct"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $export_order_condition_list = $this->getConditions($cache_key . ".1", [
            ["field" => "export_order_no", "type" => ""],
        ], true);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "export_contact_id", "type" => "int"],
            ["field" => "status_type", "type" => "string"],
            ["field" => "tracking_no", "type" => "string"],
            ["field" => "docket_no", "type" => "string"],
            ["field" => "zipcode", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "country_id", "type" => "int"],
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
            "country", 
            "state", 
            "city",
            "contact",
            "exportOrder",
        ]);

        if (isset($export_order_condition_list['export_order_no']))
        {
            $builder->whereHas("exportOrderProduct", function($q) use($export_order_condition_list) {
                $q->whereHas("exportOrder", function($q)  use($export_order_condition_list) {
                    $q->where("order_no", "like", "%" . $export_order_condition_list['export_order_no']['value'] . "%");
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

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $country_list = ExpCountry::getListCache();

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $carrier_list = ExpParty::getList("id", "display_name", ["type" => ExpParty::TYPE_CARRIER]);

        $contacts = ViewExportContact::getList("id", "display_name", [], "id", "DESC");

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
            "exportOrder",
            "exportOrder.branch",
            "contact",
            "country",
            "state",
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

        $export_order_field_labels = FieldSetting::getDynamicFields(ExportOrder::class);

        foreach ($records as $model) {

            $record = $model->toArray();

            $csv_records[] = [
                'ID' => $record['id'],
                $export_order_field_labels['order_no'] => $record['export_order']['order_no'] ?? "",
                $export_order_field_labels['party_order_ref_no'] => $record['export_order']['party_order_ref_no'] ?? "",
                $export_order_field_labels['branch_id'] => $record['export_order']['branch']['display_name'] ?? "",

                $field_labels['order_from_type'] => $model->orderFrom(),
                $field_labels['export_contact_id'] => $record['contact']['name'] ?? "",
                $field_labels['status_type'] => get_value_from_list($status_type_list, $record['status_type']),

                $export_order_field_labels['estimated_dispatch_date'] => $record['export_order']['estimated_dispatch_date'] ?? "",

                $field_labels['addr1'] => $record['addr1'] ?? "",
                $field_labels['addr2'] => $record['addr2'] ?? "",
                $field_labels['zipcode'] => $record['zipcode'] ?? "",
                $field_labels['country_id'] => $record['country']['name'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",

                $field_labels['docket_no'] => $record['docket_no'] ?? "",
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
            "exportOrder",
            "exportOrder.branch",
            "contact",
            "shipOrderProduct"
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

        $export_order_field_labels = FieldSetting::getDynamicFields(ExportOrder::class);

        $ship_order_product_field_labels = FieldSetting::getDynamicFields(ShipOrderProduct::class);

        foreach ($records as $model) {

            $record = $model->toArray();
            foreach($model->shipOrderProduct as $shipOrderProduct)
            {
                $csv_records[] = [
                    'ID' => $record['id'],
                    $export_order_field_labels['order_no'] => $record['export_order']['order_no'] ?? "",
                    $export_order_field_labels['party_order_ref_no'] => $record['export_order']['party_order_ref_no'] ?? "",
                    $export_order_field_labels['branch_id'] => $record['export_order']['branch']['display_name'] ?? "",

                    $field_labels['order_from_type'] => $model->orderFrom(),
                    $field_labels['export_contact_id'] => $record['contact']['name'] ?? "",
                    $field_labels['status_type'] => get_value_from_list($status_type_list, $record['status_type']),

                    $ship_order_product_field_labels['product_id'] => get_value_from_list($product_list, $shipOrderProduct->product_id),
                    $ship_order_product_field_labels['make_id'] => get_value_from_list($make_list, $shipOrderProduct->make_id),
                    $ship_order_product_field_labels['qty'] => $shipOrderProduct->qty ?? "",
                    'Sent Qty' => $shipOrderProduct->shipOrderProductSerial->count() ?? "",

                    'Created' => if_date_time($shipOrderProduct->created_at),
                    'Created By' => $user_cache_list[$shipOrderProduct->created_by] ?? "",
                    'Updated' => if_date_time($shipOrderProduct->updated_at),
                    'Updated By' => $user_cache_list[$shipOrderProduct->updated_by] ?? "",
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
        $file_prefix = str_class_name_to_human_text(ShipOrderProduct::class);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function create_from_export_order(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $save_records = [];

            foreach($request->export_order_ids as $export_order_id)
            {
                $export_order = ExportOrder::with([
                    "exportOrderProduct",
                    "exportOrderProduct.manufactureOrder",
                    "contact"
                ])->findOrFail($export_order_id);

                if (!$export_order->contact)
                {
                    continue;
                }

                $total_export_order_qty = 0;

                foreach($export_order->exportOrderProduct as $exportOrderProduct)
                {
                    $total_export_order_qty += $exportOrderProduct->order_qty;
                }

                $save_data = [
                    'ship_order' => [],
                    'ship_order_products' => [],
                    'exportOrder' => $export_order,
                    "total_export_order_qty" => $total_export_order_qty
                ];

                $save_data['ship_order'] = [
                    "order_from_type" => $export_order->order_from_type,
                    "contact_id" => $export_order->contact_id,
                    "export_order_id" => $export_order->id,
                ];

                switch($export_order->order_from_type)
                {
                    case "lead":
                        $export_order->contact->load("lead");
                        $address = $export_order->contact->lead->address()->where("is_primary", 1)->first();
                        $country_id = $export_order->contact->lead->country_id;
                        $state_id = $export_order->contact->lead->state_id;
                        $city_id = $export_order->contact->lead->city_id;
                        break;
                    case "customer":
                        $export_order->contact->load("customer");
                        $address = $export_order->contact->customer->lead->address()->where("is_primary", 1)->first();
                        $country_id = $export_order->contact->customer->country_id;
                        $state_id = $export_order->contact->customer->state_id;
                        $city_id = $export_order->contact->customer->city_id;
                        break;
                    case "party":
                        $export_order->contact->load("party");
                        $address = $export_order->contact->party->address()->where("is_primary", 1)->first();
                        $country_id = $export_order->contact->party->country_id;
                        $state_id = $export_order->contact->party->state_id;
                        $city_id = $export_order->contact->party->city_id;
                        break;
                    case "dealer":
                        $export_order->contact->load("dealer");
                        $address = $export_order->contact->dealer->address()->where("is_primary", 1)->first();
                        $country_id = $export_order->contact->dealer->country_id;
                        $state_id = $export_order->contact->dealer->state_id;
                        $city_id = $export_order->contact->dealer->city_id;
                        break;
                    case "competitor":
                        $export_order->contact->load("competitor");
                        $address = $export_order->contact->competitor->address()->where("is_primary", 1)->first();
                        $country_id = $export_order->contact->competitor->country_id;
                        $state_id = $export_order->contact->competitor->state_id;
                        $city_id = $export_order->contact->competitor->city_id;
                        break;
                }

                if (!isset($address) || empty($address))
                {
                    throw new UserException("Address not found of Order # " . $export_order->getDisplayName());
                }

                $save_data['ship_order']['addr1'] = $address->addr1;
                $save_data['ship_order']['addr2'] = $address->addr2;
                $save_data['ship_order']['zipcode'] = $address->pincode;
                $save_data['ship_order']['country_id'] = $country_id;
                $save_data['ship_order']['state_id'] = $state_id;
                $save_data['ship_order']['city_id'] = $city_id;
                $save_data['ship_order']['status_type'] = ShipOrder::STATUS_PENDING;

                foreach($export_order->exportOrderProduct as $exportOrderProduct)
                {
                    $pendig_qty = $exportOrderProduct->pendingOrderQty();

                    if ($pendig_qty)
                    {
                        $save_data['ship_order_products'][] = [
                            "product_id" => $exportOrderProduct->product_id,
                            "make_id" => $exportOrderProduct->make_id,
                            "qty" => $pendig_qty,
                        ];

                        $exportOrderProduct->increment('sent_qty', $pendig_qty);
                    }
                }

                if ($save_data['ship_order_products'])
                {
                    $save_records[$export_order->contact_id][] = $save_data;
                }
            }

            $save_DO_count = 0;
            foreach($save_records as $sale_records)
            {
                foreach($sale_records as $save_record)
                {
                    $old_ship_orders = ShipOrder::with(["exportOrderProduct"])->where("export_order_id", $save_record['ship_order']['export_order_id'])->get();

                    $dispatched_qty = 0;

                    foreach($old_ship_orders as $old_ship_order)
                    {
                        foreach($old_ship_order->exportOrderProduct as $exportOrderProduct)
                        {
                            $dispatched_qty += $exportOrderProduct->qty;
                        }
                    }

                    if ($save_record['total_export_order_qty'] > $dispatched_qty)
                    {
                        $model = ShipOrder::create($save_record['ship_order']);
                        foreach($save_record['ship_order_products'] as $so_product)
                        {
                            $child_model = new ShipOrderProduct($so_product);
                            $model->shipOrderProduct()->save($child_model);
                        }

                        $save_record['exportOrder']->status = ExportOrder::STATUS_GODOWN;
                        $save_record['exportOrder']->save();
                        $save_record['exportOrder']->checkForSent();

                        $save_DO_count++;
                    }
                }
            }

            DB::commit();

            return back()->with("success", "$save_DO_count Ship Orders successfully placed");
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
            'carrier_id' => ["required"],
            'tracking_no' => ["required"],
            'tracking_phone_no' => ["nullable"],
            'shipping_fee' => ["required", "numeric"],
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

            if ($validatedData['status_type'] != ShipOrder::STATUS_PENDING)
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

                $shipOrderProduct = ShipOrderProduct::where("ship_order_id", $id)->where("product_id", $product_serial_no->product_id)->where("make_id", $product_serial_no->make_id)->with([
                    "shipOrder"
                ])->first();

                if (!$shipOrderProduct)
                {
                    $product = Product::findOrFail($product_serial_no->product_id);
                    $make = Maker::findOrFail($product_serial_no->make_id);

                    $msg = "Product " . $product->getDisplayName();
                    $msg .= " with Make " . $make->getDisplayName();
                    $msg .= " not included in Order";

                    throw new UserException($msg);
                }

                if ($shipOrderProduct->qty == $shipOrderProduct->shipOrderProductSerial->count())
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

                $shipOrderProductSerial = new ShipOrderProductSerial([
                    "product_serial_id" => $product_serial_no->id
                ]);

                $shipOrderProductSerial = $shipOrderProduct->shipOrderProductSerial()->save($shipOrderProductSerial);

                DB::commit();

                $response['product_serial_no'] = $data['product_serial_no'];
                $response['status'] = 1;
                $response['delete_url'] = route($this->routePrefix . ".ajax_delete_product_serial", [$shipOrderProductSerial->id]);
                $response['shipOrderProduct'] = [
                    "id" => $shipOrderProduct->id,
                    "sent_qty" => $shipOrderProduct->shipOrderProductSerial()->count()
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
            "shipOrderProduct.shipOrderProductSerial.productSerial"
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

            $model = ShipOrderProductSerial::with([
                "productSerial",
                "shipOrderProduct.shipOrder"
            ])->find($id);
          
            if (!$model)
            {
                throw new UserException("Wrong ID");
            }

            if (empty($model->shipOrderProduct->shipOrder->status_type) || $model->shipOrderProduct->shipOrder->status_type == ShipOrder::STATUS_PENDING)
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
                
                $msg = "Ship Order has Status of " . get_value_from_list($status_type_list, $model->shipOrderProduct->shipOrder->status_type);
                $msg .= ", Can't Delete";

                throw new UserException($msg);
            }

            $model->productSerial->is_sold = 1;
            $model->productSerial->save();

            DB::commit();

            $response['product_serial_no'] = $model->productSerial->serial_no;
            $response['status'] = 1;            
            $response['shipOrderProduct'] = [
                "id" => $model->shipOrderProduct->id,
                "sent_qty" => $model->shipOrderProduct->shipOrderProductSerial()->count()
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

