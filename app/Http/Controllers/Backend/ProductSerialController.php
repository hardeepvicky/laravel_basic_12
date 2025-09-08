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
use App\Models\Maker;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\SaleOrderProduct;
use App\Models\SqlLog;
use App\Models\User;
use App\Models\ViewOrder;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductSerialController extends BackendController
{
    public String $routePrefix = "product-serial";

    protected $modelClass = ProductSerial::class;
    protected ProductSerial | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "product",
            "product.category",
            "maker",
            "dealer",
            "branch"
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

        $product_conditions = $this->getConditions($cache_key . ".2", [
            ["field" => "product_category_id", "type" => "int"],
            ["field" => "is_find_nested_child", "type" => "bool"],
        ]);

        $array_helper =  new ArrayHelper($product_conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "serial_no", "type" => "string"],
            ["field" => "is_in", "type" => "int"],
            ["field" => "is_sold", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        // d($conditions, true);

        $builder = $builder->where($conditions);

        if ($product_conditions)
        {
            $builder->whereHas("product", function($q) use ($condition_list){
                if (
                    (isset($condition_list["product_category_id"]) && $condition_list["product_category_id"])
                    && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
                ) {
                    $child_id_list = ProductCategory::getChildIdList([$condition_list["product_category_id"]]);

                    if ($child_id_list) {
                        $q->whereIn("product_category_id", $child_id_list);
                    }
                }
                else
                {
                    $q->where("product_category_id", $condition_list["product_category_id"]);
                }
            });
        }

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

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        if (isset($condition_list['product_category_id'])) {
            $product_list = Product::getList("id", "display_name", ["product_category_id" => $condition_list['product_category_id']], "list_display_order");
        }

        $this->setForView(compact("product_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $maker_list = Maker::getList("id", "display_name", [], "list_display_order");

        $default_maker = Maker::where("is_default", 1)->first();

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $type_list = ProductSerial::getTypeList();

        $order_list = ViewOrder::getList("id", "display_name", [], "id", "DESC");

        // d($order_list, true);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("maker_list", "default_maker", "branch_list", "type_list", "order_list", "field_labels"));

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

        $model->no_of_serials = 1;

        $this->setForView(compact("model", 'form'));

        $this->set_data_for_add_edit($model);

        return $this->view(__FUNCTION__);
    }

    private function _common_validation_rules()
    {
        return [
            "product_id" => ["required"],
            "make_id" => ["required"],
            "order_id" => ["nullable"],
            "serial_no" => ["required"],
            "manufacture_date_time" => ["required"],
            "children" => []
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function set_data_for_add_edit($model)
    {
        $qb = ProductCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->product_category_id) {
            $qb->orWhere("id", $model->product_category_id);
        }

        $product_category_records = ProductCategory::getTreeRecords($qb);

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name");

        $auto_next_serial_no = ProductSerial::getAutoNextSerialNo();

        $manual_next_serial_no = ProductSerial::getManualNextSerialNo();

        $type_list = [
            "auto" => "Auto",
            "manual" => "Manual",
        ];

        $this->setForView(compact("product_category_list", "auto_next_serial_no", "manual_next_serial_no", "type_list"));
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules['hierarchy_type'] = ["required"];
        $rules['serial_type'] = ["required"];
        $rules['no_of_serials'] = ["nullable", "numeric", "min:1", "max:1000"];

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
            unset($save_data['children']);

            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;
            $save_data['is_in'] = 1;

            $save_data['serial_no'] = str_replace("-", "", $save_data['serial_no']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            for ($i = 0; $i < $save_data['no_of_serials']; $i++) {

                $this->model = new $this->modelClass;
                $this->model->fill($save_data);

                if (!$this->model->save()) {
                    throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                }

                $this->_afterSave();

                $save_data['serial_no'] = ProductSerial::getNextSerialNo($save_data['serial_no']);
                AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PRODUCT_SERIAL);
            }

            if ($save_data['no_of_serials'] == 1)
            {
                $this->_saveOthers($validatedData);
            }

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

    private function _afterSave()
    {
        if ($this->model->order_id)
        {
            $order = Order::findOrFail($this->model->order_id);

            if ($order->type == "sale")
            {
                $sale_order_product = SaleOrderProduct::where("sale_order_id", $order->ref_id)
                                        ->where("product_id", $this->model->product_id)
                                        ->where("make_id", $this->model->make_id)
                                        ->first();

                if (!$sale_order_product)
                {
                    throw new UserException("Sale Order Product not found");
                }

                $this->model->sale_order_product_id = $sale_order_product->id;
                $this->model->save();
            }
            else if ($order->type == "export")
            {
                $export_order_product = ExportOrderProduct::where("export_order_id", $order->ref_id)
                                        ->where("product_id", $this->model->product_id)
                                        ->where("make_id", $this->model->make_id)
                                        ->first();

                if (!$export_order_product)
                {
                    throw new UserException("Export Order Product not found");
                }

                $this->model->export_order_product_id = $export_order_product->id;
                $this->model->save();
            }
            
            if ($this->model->parent_id)
            {
                //todo
            }
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['children'])) {

            unset($validatedData['children']['(sr-id)']);

            // d($validatedData, true);

            $this->model->children()->update(["parent_id" => 0]);

            if (isset($validatedData['children'])) {
                $rules = [
                    "product_id" => ["required"],
                    "make_id" => ["required"],
                    "serial_no" => ["required", "max:8"],
                ];

                foreach ($validatedData['children'] as $child) {
                    $validator = Validator::make($child, $rules);

                    if ($validator->fails())
                    {
                        foreach($validator->errors()->all() as $error_msg)
                        {
                            LaravelExtend::addError("", $error_msg, "Child Serial No.");
                        }
                        continue;
                    }

                    $db_child_product_serial = ProductSerial::where("serial_no", $child['serial_no'])->first();

                    $continue = true;
                    if ($db_child_product_serial)
                    {
                        if ($db_child_product_serial->product_id != $child['product_id'])
                        {
                            LaravelExtend::addError("Model", "Product of Serial No. and choosen Product are mistmatched", "Child Serial No.");
                            $continue = false;
                        }

                        if ($db_child_product_serial->make_id != $child['make_id'])
                        {
                            LaravelExtend::addError("Make", "Make of Serial No. and choosen Make are mistmatched", "Child Serial No.");
                            $continue = false;
                        }

                        if ($continue)
                        {
                            $db_child_product_serial->parent_id = $this->model->id;
                            $db_child_product_serial->save();
                        }
                    }
                    else
                    {
                        $save_data = [
                            "manufacture_date" => $this->model->manufacture_date,
                            "branch_id" => $this->model->branch_id,
                            "sale_order_product_id" => $this->model->sale_order_product_id,
                            "serial_type" => "manual",
                            "hierarchy_type" => ProductSerial::TYPE_CHILD
                        ];

                        $save_data = array_merge($save_data, $child);

                        $child_model = new ProductSerial($save_data);
                        $serial_no = str_replace("-", "", $child['serial_no']);                        
                        $serial_no = str_pad($serial_no, 8, "0", STR_PAD_LEFT);
                        $child_model->serial_no = $serial_no;
                        $child_model->hierarchy_type = ProductSerial::TYPE_CHILD;                        
                        $this->model->children()->save($child_model);
                        
                        ProductSerial::getNextSerialNo($serial_no);
                        AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PRODUCT_SERIAL);
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $model->convertTimeZoneForDisplay();

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->set_data_for_add_edit($model);

        $children = $this->modelClass::where("parent_id", $id)->get()->toArray();

        $this->setForView(compact("model", "form", "children"));

        return $this->view(__FUNCTION__);
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

            unset($save_data['children']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);
            // d($save_data);

            $this->model->fill($save_data);
            // d($this->model->manufacture_date_time, true);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_afterSave();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "product",
            "maker",
            "branch"
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

        $type_list = ProductSerial::getTypeList();

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['hierarchy_type'] => get_value_from_list($type_list, $record['hierarchy_type']),
                'Category' => get_value_from_list($product_category_list, $record['product']["product_category_id"]),
                $field_labels['product_id'] => $record['product']['display_name'] ?? "",
                $field_labels['make_id'] => $record['maker']['display_name'] ?? "",
                $field_labels['serial_type'] => $record['serial_type'],
                $field_labels['serial_no'] => "`" . $record['serial_no'],
                $field_labels['manufacture_date_time'] => $record['manufacture_date_time'],
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",                
                $field_labels['is_in'] => get_value_from_list($yes_no_list, $record['is_in']),
                $field_labels['is_sold'] => get_value_from_list($yes_no_list, $record['is_sold']),
                $field_labels['is_return'] => get_value_from_list($yes_no_list, $record['is_return']),
                $field_labels['return_count'] => $record['return_count'] ?? "",

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

    public function ajax_get_unlinked_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "product_id", "type" => "int"],
                ["field" => "make_id", "type" => "int"],
            ];

            $conditions = $this->getConditions(null, $pattern_array);
            $condition_list = $this->getConditions(null, $pattern_array, true);

            if (empty($condition_list)) {
                throw new UserException("No any condition applied");
            }

            $qb = $this->modelClass::where($conditions);

            SqlLog::addSelectQuery($qb);

            $records = $qb->get();

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

    public function ajax_get_unlinked_child_list(Request $request)
    {
        $response = ["status" => 1];

        try {

            $search_term = $request->get("query");

            $pattern_array = [
                ["field" => "product_serial_id", "type" => "int"],
                ["field" => "product_id", "type" => "int"],
                ["field" => "make_id", "type" => "int"],
            ];

            $condition_list = $this->getConditions(null, $pattern_array, true);

            $qb = $this->modelClass::where(function($q) use($condition_list) {
                $q->where("parent_id", 0);

                if (isset($condition_list['product_serial_id']) && $condition_list['product_serial_id']) {
                    $q->orWhere("parent_id", $condition_list['product_serial_id']);
                }
            });

            if (isset($condition_list['product_id']))
            {
                $qb->where("product_id", $condition_list['product_id']);
            }

            if (isset($condition_list['make_id']))
            {
                $qb->where("make_id", $condition_list['make_id']);
            }

            if (isset($search_term) && trim($search_term))
            {
                $qb->where("serial_no", 'like', '%' . trim($search_term) . '%');
            }

            SqlLog::addSelectQuery($qb);

            $qb->with([
                "product",
                "maker",
            ]);

            $records = $qb->get();

            $response['data'] = [];

            foreach ($records as $record) 
            {
                $display = $record->serial_no;

                $display .= " (" . ($record->product?->model_name ?? "-") . ")";
                $display .= " (" . ($record->maker?->model_name ?? "-") . ")";

                $row = [
                    "id" => $record->id,
                    "name" => $record->serial_no,
                    "display" => $display,
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
