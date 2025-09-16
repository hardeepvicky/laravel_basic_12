<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\AutoSerialPattern;
use App\Models\Branch;
use App\Models\ExportOrderProduct;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\Maker;
use App\Models\Party;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Requirement;
use App\Models\SqlLog;
use App\Models\Type;
use App\Models\User;
use App\Models\ViewContact;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends BackendController
{
    public String $routePrefix = "purchase-order";

    protected $modelClass = PurchaseOrder::class;
    protected PurchaseOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "purchaseOrderItem.item",
            "purchaseOrderItem.item.category",
            "purchaseOrderItem.item.category.uom",
            "purchaseOrderItem.make",
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

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
            ["field" => "order_no", "type" => "string"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "party_id", "type" => "int"],
            ["field" => "status", "type" => "string_equal"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $conditions = $array_helper->unsetValues($conditions, ["contact_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        if (isset($condition_list['contact_id']))
        {
            list($order_from_type, $contact_id) = explode(".", $condition_list['contact_id']);

            $conditions[] = ["order_from_type", $order_from_type];
            $conditions[] = ["contact_id", $contact_id];
        }

        $builder = $this->modelClass::query();

        // d($conditions);

        $builder = $builder->where($conditions);

        $builder->with([
            "branch",
            "party",
        ]);

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

        $contact_list = ViewContact::getList();

        // d($contact_list, true);

        $maker_list = Maker::getList("id", "display_name", [], "list_display_order");
        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");
        $party_list = Party::getList("id", "display_name", ["type" => [Party::TYPE_OEM, Party::TYPE_SUPPLIER, Party::TYPE_VENDOR]], "list_display_order");
        $item_list = Item::getList("id", "display_name", [], "list_display_order");

        $default_maker = Maker::where("is_default", 1)->first();

        $status_list = PurchaseOrder::getStatusTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $purchase_order_item_field_labels = FieldSetting::getDynamicFields(PurchaseOrderItem::class);

        $this->setForView(compact("maker_list", "branch_list", "party_list", "default_maker", "item_list", "status_list",
            "field_labels", "purchase_order_item_field_labels"
        ));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PURCHASE_ORDER);

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
            "party_id" => ["required"],
            "total_amount" => ["required"],
            "advance_amount" => ["nullable"],
            "remarks" => "",
            "purchase_order_items" => [],
            "requirement_ids" => []
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $this->model = new $this->modelClass();

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
            unset($save_data['purchase_order_items']);
            unset($save_data['requirement_ids']);

            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);
            $save_data['status'] = PurchaseOrder::STATUS_PENDING;

            $this->model = new $this->modelClass;
            $this->model->fill($save_data);

            $this->model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PURCHASE_ORDER);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PURCHASE_ORDER);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['purchase_order_items'])) {

            unset($validatedData['purchase_order_items']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['purchase_order_items'])) {
                $base_rules = [
                    "item_id" => ["required"],
                    "make_id" => ["required"],
                    "part_no" => ["nullable"],
                    "qty" => ["required"],
                    "uom_id" => ["required"],
                    "rate" => ["required"],
                    "amount" => ["required"],
                    "remarks" => ["nullable"],
                ];

                $child_model = new PurchaseOrderItem();

                $existing_id_list = $this->model->purchaseOrderItem()->pluck("id", "id")->toArray();

                $validatedData['purchase_order_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['purchase_order_items'] as $purchase_order_item) {

                    $validator = Validator::make($purchase_order_item, $base_rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Products");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($purchase_order_item['id']) {
                        $child_model = PurchaseOrderItem::find($purchase_order_item['id']);

                        if (!$child_model) {
                            $child_model = new PurchaseOrderItem();
                        } else {
                            unset($existing_id_list[$purchase_order_item['id']]);
                        }
                    } else {
                        $child_model = new PurchaseOrderItem();
                    }

                    $purchase_order_item['display_order'] = $display_order;

                    $child_model->fill($purchase_order_item);

                    $child_model = $this->model->purchaseOrderItem()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->purchaseOrderItem()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }

        if (isset($validatedData['requirement_ids']))
        {
            foreach($validatedData['requirement_ids'] as $requirement_id)
            {
                $requirement = Requirement::find($requirement_id);

                if ($requirement)
                {
                    $requirement->status = Requirement::STATUS_PO_CREATED;
                    $requirement->save();
                }
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $screen = "edit";

        $this->setForView(compact("model", "form", "screen"));

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

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            unset($save_data['purchase_order_items']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            $this->model->fill($save_data);

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
        $model = $this->model = $this->modelClass::with(["purchaseOrderItem"])->findOrFail($id);

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PURCHASE_ORDER);

        $this->beforeCreate();

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function create_from_requirements(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PURCHASE_ORDER);
        
        $requirement_ids = $request->get("requirement_ids");

        if (empty($requirement_ids))
        {
            throw new UserException("Requirements IDS are required");
        }

        $requirements = Requirement::where("status", Requirement::STATUS_PENDING)->whereIn("id", $requirement_ids)->get()->toArray();

        foreach($requirements as $k => $requirement)
        {
            unset($requirements[$k]['id']);
        }
        
        // dd($requirements);

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form", "requirements", "requirement_ids"));

        return $this->view("form");
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "branch",
            "contact"
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $order_from_type_list = SaleOrder::getOrderFromTypeList();

        $type_conditions = [
            'type' => Type::getType($this->model, "status")
        ];
        if ($this->model->status)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->status);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['order_no'] => $record['order_no'] ?? "",
                $field_labels['party_order_ref_no'] => $record['party_order_ref_no'] ?? "",
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                $field_labels['order_from_type'] => get_value_from_list($order_from_type_list, $record['order_from_type']),
                $field_labels['contact_id'] => $record['contact']['name'] ?? "",
                $field_labels['status'] => get_value_from_list($status_list, $record['status']),
                $field_labels['estimated_dispatch_date'] => $record['estimated_dispatch_date'] ?? "",
                $field_labels['total_amount'] => $record['total_amount'] ?? "",
                $field_labels['discount_per'] => $record['discount_per'] ?? "",
                $field_labels['discount'] => $record['discount'] ?? "",
                $field_labels['payable_amount'] => $record['payable_amount'] ?? "",
                $field_labels['paid_amount'] => $record['paid_amount'] ?? "",
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
            "branch",
            "contact",
            "saleOrderProduct" => function($q){
                $q->orderBy("display_order");
            },
            "saleOrderProduct.product",
            "saleOrderProduct.make",
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $order_from_type_list = SaleOrder::getOrderFromTypeList();

        $type_conditions = [
            'type' => Type::getType($this->model, "status")
        ];
        if ($this->model->status)
        {
            $type_id_list = Type::getIdFromCode($this->model, $this->model->status);

            if ($type_id_list)
            {
                $type_conditions['or_id'] = $type_id_list;
            }
        }
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $purchase_order_item_field_labels = FieldSetting::getDynamicFields(SaleOrderProduct::class);

        foreach ($records as $record) {

            foreach($record['sale_order_product'] as $saleOrderProduct)
            {
                $csv_records[] = [
                    'ID' => $record['id'],
                    $field_labels['order_no'] => $record['order_no'] ?? "",
                    $field_labels['party_order_ref_no'] => $record['party_order_ref_no'] ?? "",
                    $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                    $field_labels['order_from_type'] => get_value_from_list($order_from_type_list, $record['order_from_type']),
                    $field_labels['contact_id'] => $record['contact']['name'] ?? "",
                    $field_labels['status'] => get_value_from_list($status_list, $record['status']),

                    $purchase_order_item_field_labels['product_id'] => $saleOrderProduct['product']['display_name'] ?? "",
                    $purchase_order_item_field_labels['make_id'] => $saleOrderProduct['make']['display_name'] ?? "",
                    $purchase_order_item_field_labels['order_qty'] => $saleOrderProduct['order_qty'] ?? "",
                    $purchase_order_item_field_labels['rate'] => $saleOrderProduct['rate'] ?? "",
                    $purchase_order_item_field_labels['amount'] => $saleOrderProduct['amount'] ?? "",
                    $purchase_order_item_field_labels['available_qty'] => $saleOrderProduct['available_qty'] ?? "",
                    $purchase_order_item_field_labels['qty_to_manufacture'] => $saleOrderProduct['qty_to_manufacture'] ?? "",
                    $purchase_order_item_field_labels['sent_qty'] => $saleOrderProduct['sent_qty'] ?? "",
                    $purchase_order_item_field_labels['remarks'] => $saleOrderProduct['remarks'] ?? "",

                    'Created' => if_date_time($saleOrderProduct['created_at']),
                    'Created By' => $user_cache_list[$saleOrderProduct['created_by']] ?? "",
                    'Updated' => if_date_time($saleOrderProduct['updated_at']),
                    'Updated By' => $user_cache_list[$saleOrderProduct['updated_by']] ?? "",
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
        $file_prefix = str_class_name_to_human_text(SaleOrderProduct::class);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function ajax_get_list($contact_id = 0)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($contact_id)
        {
            $conditions["contact_id"] = $contact_id;
        }

        $list = $this->modelClass::getList("id", "display_name", $conditions, "id", "DESC");

        $response['data'] = [];

        foreach($list as $k => $v)
        {
            $response['data'][] = [
                'id' => $k,
                'name' => $v,
            ];
        }

        return $this->responseJson($response);
    }

    public function ajax_get_unlinked_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "id", "type" => "int"],
                ["field" => "product_id", "type" => "int"],
                ["field" => "make_id", "type" => "int"],
                ["field" => "for", "type" => ""],
            ];

            $condition_list = $this->getConditions(null, $pattern_array, true);

            if (empty($condition_list)) {
                throw new UserException("No any condition applied");
            }

            $qb = SaleOrderProduct::where("product_id", $condition_list['product_id']);

            if (isset($condition_list['for']) && $condition_list['for'] == "ProductSerial")
            {
                $qb->where(function ($q) use ($condition_list) {

                    $exist_sale_order_products_id_list = ProductSerial::whereNotNull('sale_order_product_id')->pluck("sale_order_product_id")->toArray();

                    if ($exist_sale_order_products_id_list)
                    {
                        $q->whereNotIn('id', $exist_sale_order_products_id_list);
                    }

                    if (isset($condition_list['id']) && $condition_list['id']) {
                        $q->orWhere("id", $condition_list['id']);
                    }
                });
            }

            $qb->with([
                "saleOrder"
            ]);

            SqlLog::addSelectQuery($qb);

            $records = $qb->get();

            $response['data'] = [];

            foreach ($records as $record) {
                $row = [
                    "id" => $record->id,
                    "name" => $record->saleOrder->getDisplayName(),
                ];

                $response['data'][] = $row;
            }
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function ajax_get_available_qty_and_qty_to_manufacture($product_id, $make_id, $order_qty, $sale_order_id = 0)
    {
        $response = ["status" => 1, "available_qty" => 0, "qty_to_manufacture" => 0];

        $qb = StockProduct::where("make_id", $make_id)->where("product_id", $product_id);
        $models = $qb->get();

        if ($models)
        {
            foreach($models as $model)
            {
                $response['available_qty'] += $model->getAvialiableQty();
            }

            $builder = SaleOrderProduct::where("product_id", $product_id)->where("make_id", $make_id);

            if ($sale_order_id)
            {
                $builder->whereNot("sale_order_id", $sale_order_id);
            }

            $so_order_qty = $builder->sum("order_qty");

            $eo_order_qty = ExportOrderProduct::where("product_id", $product_id)->where("make_id", $make_id)->sum("order_qty");

            $response['available_qty'] -= ($so_order_qty + $eo_order_qty);

            if ($response['available_qty'] < 0)
            {
                $response['available_qty'] = 0;
            }

            $left_qty = $response['available_qty'] - $order_qty;

            if ($left_qty < 0)
            {
                $response['qty_to_manufacture'] = abs($left_qty);
            }
        }
        else
        {
            $response['qty_to_manufacture'] = $order_qty;
        }

        return $this->responseJson($response);
    }

    public function ajax_get_pending_payable_amount($id, $payment_id = 0)
    {
        $response = ["status" => "1", "pending_amount" => 0];

        $so = $this->modelClass::findOrFail($id);

        if ($payment_id)
        {
            $payment_sum = $so->payment()->whereNot("id", $payment_id)->sum("amount");
        }
        else
        {
            $payment_sum = $so->payment->sum("amount");
        }

        $response['pending_amount'] = $so->total_amount - $so->advance_amount - $payment_sum;

        return $this->responseJson($response);
    }
}
