<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\FieldSetting;
use App\Models\Maker;
use App\Models\ProductCategory;
use App\Models\SaleOrder;
use App\Models\SaleOrderProduct;
use App\Models\SqlLog;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SaleOrderController extends BackendController
{
    public String $routePrefix = "sale-order";

    protected $modelClass = SaleOrder::class;
    protected SaleOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "saleOrderProduct.product",
            "saleOrderProduct.product.category",
            "saleOrderProduct.maker",
            "dealer",
            "customer",
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

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "dealer_id", "type" => "int"],
            ["field" => "customer_id", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        // d($conditions, true);

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

        $maker_list = Maker::getList("id", "display_name", [], "list_display_order");
        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");
        $dealer_list = Dealer::getList("id", "display_name", [], "list_display_order");
        $customer_list = Customer::getList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $sale_order_product_field_labels = FieldSetting::getDynamicFields(SaleOrderProduct::class);

        $this->setForView(compact("maker_list", "branch_list", "dealer_list", "customer_list", 
            "field_labels", "sale_order_product_field_labels"
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

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "product_id" => ["required"],
            "make_id" => ["required"],
            "is_parent_serial_no" => ["required"],
            "serial_no" => ["required"],
            "remarks" => "",
            "children" => []
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

        $rules = $this->_common_validation_rules();

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
            unset($save_data['sale_order_products']);

            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;

            $this->model = new $this->modelClass;
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

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['sale_order_products'])) {

            unset($validatedData['sale_order_products']['(sr-id)']);

            d($validatedData, true);

            if (isset($validatedData['sale_order_products'])) {
                $base_rules = [
                    "product_id" => ["required"],
                    "make_id" => ["required"],
                    "qty" => ["required"],
                ];

                $child_model = new SaleOrderProduct();

                $existing_id_list = $this->model->saleOrderProduct()->pluck("id", "id")->toArray();

                $validatedData['sale_order_id'] = $this->model->id;

                

                $display_order = 0;

                foreach ($validatedData['sale_order_products'] as $sale_order_product) {

                    $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($validatedData));

                    $validator = Validator::make($sale_order_product, $rules, [
                        "name.required" => "Term is required"
                    ]);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Addresses");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($sale_order_product['id']) {
                        $child_model = SaleOrderProduct::find($sale_order_product['id']);

                        if (!$child_model) {
                            $child_model = new SaleOrderProduct();
                        } else {
                            unset($existing_id_list[$sale_order_product['id']]);
                        }
                    } else {
                        $child_model = new SaleOrderProduct();
                    }

                    $sale_order_product['display_order'] = $display_order;

                    $child_model->fill($sale_order_product);

                    $this->model->saleOrderProduct()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->saleOrderProduct()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
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

        $this->set_data_for_add_edit($model);

        $this->setForView(compact("model", "form"));

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

            unset($save_data['sale_order_products']);

            // dd($save_data);

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

        d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                'Category' => get_value_from_list($product_category_list, $record['product']["product_category_id"]),
                $field_labels['product_id'] => $record['product']['display_name'] ?? "",
                $field_labels['make_id'] => $record['maker']['display_name'] ?? "",
                $field_labels['serial_no'] => "`" . $record['serial_no'],
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                $field_labels['customer_id'] => $record['customer']['display_name'] ?? "",
                $field_labels['dealer_id'] => $record['dealer']['display_name'] ?? "",
                $field_labels['is_parent_serial_no'] => get_value_from_list($yes_no_list, $record['is_parent_serial_no']),
                $field_labels['is_in'] => get_value_from_list($yes_no_list, $record['is_in']),
                $field_labels['is_sold'] => get_value_from_list($yes_no_list, $record['is_sold']),
                $field_labels['is_return'] => get_value_from_list($yes_no_list, $record['is_return']),
                $field_labels['return_count'] => $record['return_count'] ?? "",
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

    public function ajax_get_unlinked_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "product_serial_id", "type" => "int"],
                ["field" => "product_id", "type" => "int"],
                ["field" => "make_id", "type" => "int"],
            ];

            $condition_list = $this->getConditions(null, $pattern_array, true);

            if (empty($condition_list)) {
                throw new UserException("No any condition applied");
            }

            $qb = $this->modelClass::where("product_id", $condition_list['product_id'])->where(function ($q) use ($condition_list) {
                $q->orWhere("parent_id", 0);

                if (isset($condition_list['product_serial_id']) && $condition_list['product_serial_id']) {
                    $q->orWhere("parent_id", $condition_list['product_serial_id']);
                }
            });

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
}
