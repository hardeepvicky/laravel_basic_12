<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderProduct;
use App\Models\FieldSetting;
use App\Models\Party;
use App\Models\SaleOrder;
use App\Models\State;
use App\Models\Type;
use App\Models\User;
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
            "products", "items"
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
            ["field" => "country_id", "type" => "int"],
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
            "country", "state", "district", "city", 
            "contact",
            "saleOrder",
        ]);

        if (isset($sale_order_condition_list['sale_order_no']))
        {
            $builder->whereHas("saleOrderProduct", function($q) use($sale_order_condition_list) {
                $q->whereHas("saleOrder", function($q)  use($sale_order_condition_list) {
                    $q->where("order_no", "like", "%" . $sale_order_condition_list['sale_order_no'] . "%");
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

        if (isset($condition_list['country_id']))
        {
            $country_id = $condition_list['country_id'];

            $state_list = State::getList("id", "display_name", ["country_id" => $country_id]);

            $state_conditions = [
                ["country_id", $country_id],
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
        else
        {
            $state_list = $district_city_list = [];
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

        $contact_list = Contact::getList("id", "display_name", [], "id", "DESC");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact(
            "country_list", "status_type_list", "carrier_list", "contact_list",
            "field_labels", 
        ));

        return parent::beforeViewRender();
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
                            "qty" => $pendig_qty,
                        ];

                        $saleOrderProduct->increment('sent_qty', $pendig_qty);
                    }
                }

                if ($save_data['dispatch_order_products'])
                {
                    $save_records[$sale_order->contact_id] = $save_data;
                }
            }

            $save_DO_count = 0;
            foreach($save_records as $save_record)
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

            DB::commit();

            return back()->with("success", "$save_DO_count Dispatch Orders successfully placed");
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            return back()->with("fail", get_exception_display_message($ex));
        }
    }
}

