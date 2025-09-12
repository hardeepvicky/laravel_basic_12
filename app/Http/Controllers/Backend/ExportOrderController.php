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
use App\Models\Contact;
use App\Models\ExpLead;
use App\Models\ExportContact;
use App\Models\ExportOrder;
use App\Models\ExportOrderProduct;
use App\Models\ViewExportContact;
use App\Models\FieldSetting;
use App\Models\Maker;
use App\Models\ManufactureOrder;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\SaleOrderProduct;
use App\Models\SqlLog;
use App\Models\StockProduct;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ExportOrderController extends BackendController
{
    public String $routePrefix = "export-order";

    protected $modelClass = ExportOrder::class;
    protected ExportOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "exportOrderProduct.product",
            "exportOrderProduct.product.category",
            "exportOrderProduct.make",
            "contact",
            "branch",
            "customer",
            "customer.city",
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
            ["field" => "export_contact_id", "type" => "string_equal"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $conditions = $array_helper->unsetValues($conditions, ["export_contact_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        if (isset($condition_list['export_contact_id']))
        {
            list($order_from_type, $contact_id) = explode(".", $condition_list['export_contact_id']);

            $conditions[] = ["order_from_type", $order_from_type];
            $conditions[] = ["export_contact_id", $contact_id];
        }

        $builder = $this->modelClass::query();

        // d($conditions);

        $builder = $builder->where($conditions);

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

        $contact_list = ViewExportContact::getList();

        // d($contact_list, true);

        $maker_list = Maker::getList("id", "display_name", [], "list_display_order");
        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");
        
        $default_maker = Maker::where("is_default", 1)->first();

        $order_from_type_list = ExportOrder::getOrderFromTypeList();

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

        $export_order_product_field_labels = FieldSetting::getDynamicFields(ExportOrderProduct::class);

        $this->setForView(compact("maker_list", "branch_list", "contact_list",
            "order_from_type_list", "status_list",
            "default_maker",
            "field_labels", "export_order_product_field_labels"
        ));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_EXPORT_ORDER);

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", 'form'));

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [            
            "party_order_ref_no" => ["nullable"],
            "export_contact_id" => ["required"],
            "total_amount" => ["required"],
            "advance_amount" => ["nullable"],
            "remarks" => "",
            "terms" => "",
            "export_order_products" => []
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function set_data_for_add_edit($model) {}

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
            list($order_from_type, $contact_id) = explode(".", $validatedData['export_contact_id']);

            $validatedData['order_from_type'] = $order_from_type;
            $validatedData['export_contact_id'] = $contact_id;

            $save_data = $validatedData;
            unset($save_data['export_order_products']);

            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);
            $save_data['status'] = ExportOrder::STATUS_NEW;

            $this->model = new $this->modelClass;
            $this->model->fill($save_data);

            $this->model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_EXPORT_ORDER);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_EXPORT_ORDER);

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
        if (isset($validatedData['export_order_products'])) {

            unset($validatedData['export_order_products']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['export_order_products'])) {
                $base_rules = [
                    "product_id" => ["required"],
                    "make_id" => ["required"],
                    "order_qty" => ["required"],
                    "available_qty" => ["nullable"],
                    "qty_to_manufacture" => ["nullable"],
                    "rate" => ["required"],
                    "amount" => ["required"],
                    "remarks" => ["nullable"],
                ];

                $child_model = new ExportOrderProduct();

                $existing_id_list = $this->model->exportOrderProduct()->pluck("id", "id")->toArray();

                $validatedData['export_order_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['export_order_products'] as $export_order_product) {

                    $validator = Validator::make($export_order_product, $base_rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Products");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($export_order_product['id']) {
                        $child_model = ExportOrderProduct::find($export_order_product['id']);

                        if (!$child_model) {
                            $child_model = new ExportOrderProduct();
                        } else {
                            unset($existing_id_list[$export_order_product['id']]);
                        }
                    } else {
                        $child_model = new ExportOrderProduct();
                    }

                    $export_order_product['display_order'] = $display_order;

                    $child_model->fill($export_order_product);

                    $this->model->exportOrderProduct()->save($child_model);

                    if ($export_order_product['qty_to_manufacture'] > 0)
                    {
                        $save_arr = [
                            "order_from" => "sale",
                            "order_date_time" => DateUtility::getDate(null, DateUtility::DATETIME_FORMAT),
                            "product_id" => $export_order_product['product_id'],
                            "make_id" => $export_order_product['make_id'],
                            "order_qty" => $export_order_product['qty_to_manufacture'],
                            "status" => ManufactureOrder::STATUS_PENDING,
                            "remarks" => $export_order_product['remarks'],
                        ];

                        $old_mo = $child_model->manufactureOrder()->first();

                        if ($old_mo)
                        {
                            $old_mo->fill($save_arr);
                            $old_mo->save();
                        }
                        else
                        {
                            $mo = new ManufactureOrder($save_arr);
                            $child_model->manufactureOrder()->save($mo);
                        }
                    }
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->exportOrderProduct()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }

        if (isset($validatedData['order_from_type']) && $validatedData['order_from_type'] == "lead" && !$this->model->exp_customer_id)
        {
            try
            {
                $contact = ExportContact::findOrFail($validatedData['export_contact_id']);

                $lead = ExpLead::with(["customer"])->findOrFail($contact->ref_id);
    
                if ($lead->customer)
                {
                    $cutomer = $lead->customer;
                }
                else
                {
                    $cutomer = $lead->convertToCustomer();
                }
                
                $contact = ExportContact::where("type", "customer")->where("ref_id", $cutomer->id)->first();

                if (!$contact)
                {
                    $contact = ExportContact::createContact("customer", $cutomer->id);
                }

                $this->model->order_from_type = "customer";
                $this->model->export_contact_id = $contact->id;
                $this->model->exp_customer_id = $cutomer->id;
                $this->model->save();

                Session::flash("info", "Lead converted to Customer");
            }
            catch(UserException $ex)
            {
                Session::flash("info", "System get error while converting lead to customer, please do it manually");
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["exportOrderProduct"])->findOrFail($id);

        $this->beforeEdit($this->model);

        if (strtoupper($model->order_from_type) == "LEAD" && $model->customer_id)
        {
            $model->order_from_type = "customer";
            $model->export_contact_id = $model->customer_id;
        }

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->set_data_for_add_edit($model);

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

            list($order_from_type, $contact_id) = explode(".", $validatedData['export_contact_id']);

            $validatedData['order_from_type'] = $order_from_type;
            $validatedData['export_contact_id'] = $contact_id;

            $save_data = $validatedData;

            unset($save_data['export_order_products']);

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
        $model = $this->model = $this->modelClass::with(["exportOrderProduct"])->findOrFail($id);

        $model->order_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_EXPORT_ORDER);

        if (strtoupper($model->order_from_type) == "LEAD" && $model->exp_customer_id)
        {
            $model->order_from_type = "customer";
            $model->export_contact_id = $model->exp_customer_id;
        }

        $this->beforeCreate();

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->set_data_for_add_edit($model);

        $this->setForView(compact("model", "form"));

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

        $order_from_type_list = ExportOrder::getOrderFromTypeList();

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

        $order_from_type_list = ExportOrder::getOrderFromTypeList();

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

        $sale_order_product_field_labels = FieldSetting::getDynamicFields(SaleOrderProduct::class);

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

                    $sale_order_product_field_labels['product_id'] => $saleOrderProduct['product']['display_name'] ?? "",
                    $sale_order_product_field_labels['make_id'] => $saleOrderProduct['make']['display_name'] ?? "",
                    $sale_order_product_field_labels['order_qty'] => $saleOrderProduct['order_qty'] ?? "",
                    $sale_order_product_field_labels['rate'] => $saleOrderProduct['rate'] ?? "",
                    $sale_order_product_field_labels['amount'] => $saleOrderProduct['amount'] ?? "",
                    $sale_order_product_field_labels['available_qty'] => $saleOrderProduct['available_qty'] ?? "",
                    $sale_order_product_field_labels['qty_to_manufacture'] => $saleOrderProduct['qty_to_manufacture'] ?? "",
                    $sale_order_product_field_labels['sent_qty'] => $saleOrderProduct['sent_qty'] ?? "",
                    $sale_order_product_field_labels['remarks'] => $saleOrderProduct['remarks'] ?? "",

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

    public function ajax_get_list($export_contact_id = 0)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($export_contact_id)
        {
            $conditions["export_contact_id"] = $export_contact_id;
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

            $qb = ExportOrderProduct::where("product_id", $condition_list['product_id']);


            if (isset($condition_list['for']) && $condition_list['for'] == "ProductSerial")
            {
                $qb->where(function ($q) use ($condition_list) {

                    $exist_export_order_products_id_list = ProductSerial::whereNotNull('export_order_product_id')->pluck("export_order_product_id")->toArray();

                    if ($exist_export_order_products_id_list)
                    {
                        $q->whereNotIn('id', $exist_export_order_products_id_list);
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

    public function ajax_get_available_qty_and_qty_to_manufacture($product_id, $make_id, $order_qty, $export_order_id = 0)
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
            $so_order_qty = $builder->sum("order_qty");

            $builder = ExportOrderProduct::where("product_id", $product_id)->where("make_id", $make_id);

            if ($export_order_id)
            {
                $builder->whereNot("export_order_id", $export_order_id);
            }

            $eo_order_qty = $builder->sum("order_qty");
            
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

    public function ajax_get_pending_payable_amount($id, $export_payment_id = 0)
    {
        $response = ["status" => "1", "pending_amount" => 0];

        $eo = $this->modelClass::findOrFail($id);

        if ($export_payment_id)
        {
            $payment_sum = $eo->payment()->whereNot("id", $export_payment_id)->sum("amount");
        }
        else
        {
            $payment_sum = $eo->payment->sum("amount");
        }

        $response['pending_amount'] = $eo->total_amount - $eo->advance_amount - $payment_sum;

        return $this->responseJson($response);
    }
}
