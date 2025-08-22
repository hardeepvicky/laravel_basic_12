<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\AutoSerialPattern;
use App\Models\Branch;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Maker;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\Setting;
use App\Models\SqlLog;
use App\Models\User;
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
            "category",
            "product",
            "maker",
            "dealer",
            "branch"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

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
            ["field" => "product_category_id", "type" => "int"],
            ["field" => "is_find_nested_child", "type" => "bool"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "serial_no", "type" => "string"],
            ["field" => "is_in", "type" => "int"],
            ["field" => "is_sold", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["product_category_id"]) && $condition_list["product_category_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        ) {
            $child_id_list = ProductCategory::getChildIdList([$condition_list["product_category_id"]]);

            if ($child_id_list) {
                $builder->whereIn("product_category_id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["product_category_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }

        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

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

        $product_list = [];

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
        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("maker_list", "branch_list", "field_labels"));

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

        return $this->view(__FUNCTION__);
    }

    private function _common_validation_rules()
    {
        return [            
            "product_category_id" => ["required"],
            "product_id" => ["required"],
            "make_id" => ["required"],
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

        $next_serial_no = ProductSerial::getNextSerialNo();

        $type_list = [
            "auto" => "Auto",
            "manual" => "Manual",
        ];

        $this->setForView(compact("product_category_list", "next_serial_no", "type_list"));
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules['type'] = ["required"];
        $rules['no_of_serials'] = ["nullable", "numeric", "max:1000"];

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

            if ($save_data['type'] == "auto") {
                for ($i = 0; $i < $save_data['no_of_serials']; $i++) {
                    $save_data['serial_no'] = ProductSerial::getNextSerialNo();
                    AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PRODUCT);

                    $this->model = new $this->modelClass;
                    $this->model->fill($save_data);

                    if (!$this->model->save()) {
                        throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                    }
                }
            } else {
                $save_data['serial_no'] = ProductSerial::getNextSerialNo();
                AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PRODUCT);

                $this->model->fill($save_data);

                if (!$this->model->save()) {
                    throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                }

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

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['children'])) {

            unset($validatedData['children']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['children'])) {
                $rules = [
                    "product_serial_id" => ["required"],
                ];

                foreach ($validatedData['children'] as $child) {
                    $validator = Validator::make($child, $rules, [
                        "product_serial_id.required" => "Serial No. is required"
                    ]);

                    if ($validator->fails())
                    {
                        foreach($validator->errors()->all() as $error_msg)
                        {               
                            LaravelExtend::addError("", $error_msg, "Sub Products");
                        }
                        continue;
                    }

                    $child_model = $this->modelClass::findOrFail($child['product_serial_id']);
                    $child_model->parent_id = $this->model->id;
                    $child_model->save();
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

        $children = $this->modelClass::where("parent_id", $id)->get()->toArray();

        $this->setForView(compact("model", "form", "children"));

        return $this->view(__FUNCTION__);
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {

            $save_data = $validatedData;

            unset($save_data['children']);

            // dd($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

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

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function copy($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    public function import_excel(Request $request)
    {
        $sample = "samples/item.csv";

        $this->setForView([
            "sample" => route("sample.item")
        ]);

        $import_log_id = $request->get("import_log_id", false);
        if ($import_log_id) {
            $importLogModel = ImportLog::find($import_log_id);
        }

        if (!isset($importLogModel)) {
            $save = [
                'type' => str_class_name_without_namespace($this->modelClass),
                "status_info" => "Processing...."
            ];

            $importLogModel = ImportLog::create($save);
            $import_log_id = $importLogModel->id;
        }

        $form = [
            'url' => route($this->routePrefix . '.import.csv'),
            'method' => 'POST',
        ];

        $this->setForView(compact("form", "import_log_id"));

        if ($request->isMethod('POST')) {
            $import_log_id = "";

            $this->setForView(compact("import_log_id"));
            $rules = [
                "file" => [
                    "required",
                    "file",
                    "max:5200",
                    "mimes:xlsx"
                ],
            ];

            $request->validate($rules);

            try {
                $start_seconds = microtime(true);

                $this->beforeCsvImport();

                $path = ImportLog::getStoragePath();

                $filepath = $request->file('file')->store($path, DEFAULT_DISK);

                $full_file_path = ImportLog::getStorageDisk()->path($filepath);

                $importUtility = new ImportUtility($sample, $full_file_path);

                if (!$importUtility->checkHeaders()) {
                    $this->setForView(array('errors' => $importUtility->errors));

                    foreach ($importUtility->errors as $error_msg) {
                        LaravelExtend::addError("", $error_msg);
                    }

                    throw new UserException("CSV Column headers are mismatched", UserException::ERROR_USER_DATA_WRONG);
                }

                $headers = array(
                    "Category" => "item_category_id",
                    "Name" => "name",
                    "Description" => "description",
                    "Color" => "color_id",
                    "Size" => "size",
                    "Brand" => "brand_id",
                    "Specification" => "specification",
                    "Active" => "is_active",
                    "List Display Order" => "list_display_order"
                );

                $header_flip = array_flip($headers);

                $csv_records = $importUtility->replaceHeaders($headers);
                // d($csv_records, true);

                $total_count = count($csv_records);

                $save = [
                    'file' => $filepath,
                    'total_count' => $total_count
                ];

                $importLogModel->fill($save);

                $accept_count = 0;
                $msg_list = [
                    "Category Found" => 0,
                    "Color Found" => 0,
                    "Brand Found" => 0,
                    "Item Insert" => 0,
                    "Item Update" => 0,
                ];

                $qb = ItemCategory::where("is_active", 1);

                $item_category_records = ItemCategory::getTreeRecords($qb);

                $item_category_list = Util::getTreeListArray($item_category_records, "id", "name");
                $item_category_list = array_flip(Util::applyAll($item_category_list, ["strtolower"]));

                $brand_list = array_flip(Util::applyAll(DropDownValue::getList("id", "name", ["type" => DropDownValue::BRAND]), ["strtolower"]));

                $color_list = array_flip(Util::applyAll(DropDownValue::getList("id", "name", ["type" => DropDownValue::COLOR]), ["strtolower"]));

                $sku_pattern = Setting::getValue("item_sku_pattern");

                if (!$sku_pattern) {
                    $label = Setting::LABELS['item_sku_pattern'];
                    throw new UserException("Setting : $label is not set yet", UserException::ERROR_USER_DATA_WRONG);
                }

                foreach ($csv_records as $i => $record) {
                    $record = Util::applyAll($record, array("trim"));
                    $validate = true;

                    $category_name = strtolower($record["item_category_id"]);
                    if (isset($item_category_list[$category_name])) {
                        $record['item_category_id'] = $item_category_list[$category_name];
                        $msg_list['Category Found']++;
                    } else {
                        LaravelExtend::addError($header_flip['item_category_id'], $record['item_category_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    $color_name = strtolower($record["color_id"]);
                    if (isset($color_list[$color_name])) {
                        $record['color_id'] = $color_list[$color_name];
                        $msg_list['Color Found']++;
                    } else {
                        LaravelExtend::addError($header_flip['color_id'], $record['color_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    $brand_name = strtolower($record["brand_id"]);
                    if (isset($brand_list[$brand_name])) {
                        $record['brand_id'] = $brand_list[$brand_name];
                        $msg_list['Brand Found']++;
                    } else {
                        LaravelExtend::addError($header_flip['brand_id'], $record['brand_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    if ($validate) {
                        $model = new $this->modelClass();
                        $record['sku'] = Item::getSkuFromPattern($sku_pattern, $record);

                        // d($record, true);

                        $is_insert = $is_update = null;
                        if ($model->insertOrUpdate($record, $is_insert, $is_update)) {
                            $accept_count++;

                            if ($is_insert) {
                                $msg_list['Item Insert']++;
                            } else if ($is_update) {
                                $msg_list['Item Update']++;
                            }

                            $percent = round(($i + 1) * 100 / $total_count, 1);

                            $importLogModel->status_info = "Processing... $percent % " . ", Time Taken : " . Util::niceTime(microtime(true) - $start_seconds);
                            $importLogModel->saveQuietly();
                        } else {
                            LaravelExtend::addError("", "Fail To Save", "Row #" . ($i + 2));
                        }
                    }
                }

                $sqlLogModel = LaravelExtend::saveSqlLog([
                    "import_log_id" => $importLogModel->id
                ]);

                $importLogModel->sql_log_id = $sqlLogModel->id;
                $importLogModel->accept_count = $accept_count;
                $importLogModel->exec_time = microtime(true) - $start_seconds;

                $text_list = [];
                foreach ($msg_list as $text => $count) {
                    $text_list[] = $text . " : " . $count;
                }

                $importLogModel->is_fully_run = true;

                $msg = "Total Count : $total_count, Accept Count : $accept_count";
                $msg .= ", " . implode(", ", $text_list);

                if (LaravelExtend::$errors) {
                    throw new UserException("Unable to Save all records. " . $msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $msg = "All File rows updated successfully. " . $msg;
                $importLogModel->flash_msg = $msg;
                $importLogModel->save();

                Session::flash('success', $msg);
            } catch (UserException $ex) {
                $msg = $ex->getMessage();

                if (isset($importLogModel)) {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;

                    if (LaravelExtend::$errors) {
                        $importLogModel->errors = LaravelExtend::$errors;
                    }

                    $importLogModel->save();
                }

                return back()->With("fail", $msg)->with("custom_errors", LaravelExtend::$errors);
            } catch (\Exception $ex) {
                $msg = get_exception_display_message($ex);

                if (isset($importLogModel)) {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;
                    $importLogModel->save();
                }

                Session::flash('fail', $msg);

                return back()->With("fail", $msg);
            }
        }

        return $this->view("import");
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "category",
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

        $item_category_records = ItemCategory::getTreeRecords(ItemCategory::query());

        $item_category_list = Util::getTreeListArray($item_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {
            
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['product_category_id'] => get_value_from_list($item_category_list, $record['product_category_id']),
                $field_labels['product_id'] => $record['product']['display_name'] ?? "",
                $field_labels['make_id'] => $record['maker']['display_name'] ?? "",
                $field_labels['serial_no'] => "`" . $record['serial_no'],
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                $field_labels['customer_id'] => $record['customer']['display_name'] ?? "",
                $field_labels['dealer_id'] => $record['dealer']['display_name'] ?? "",                
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
                ["field" => "product_serial_id", "type" => "int"],
                ["field" => "product_id", "type" => "int"],
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
