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
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemFile;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Type;
use App\Models\Uom;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class ItemController extends BackendController
{
    public String $routePrefix = "item";

    protected $modelClass = Item::class;
    protected Item | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "tags",
            "color",
            "brand",
            "mainImage",
            "images",
            "otherFiles",
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        foreach($records as $record)
        {
            if ($record->mainImage?->id)
            {
                $record->mainImage->afterFind();
            }
        }

        $item_category_records = ItemCategory::getTreeRecords(ItemCategory::query());

        $item_category_list = Util::getTreeListArray($item_category_records, "id", "name", false, false);

        $this->setForView(compact("records", "item_category_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "item_category_id", "type" => "int"],
            ["field" => "is_find_nested_child", "type" => "bool"],
            ["field" => "name", "type" => "string"],
            ["field" => "sku", "type" => "string"],
            ["field" => "color_id", "type" => "int"],
            ["field" => "brand_id", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["item_category_id"]) && $condition_list["item_category_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        )
        {
            $child_id_list = ItemCategory::getChildIdList([$condition_list["item_category_id"]]);

            if ($child_id_list)
            {
                $builder->whereIn("item_category_id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["item_category_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }

        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        // d($conditions, true);

        $builder = $builder->where($conditions);

        $tag_conditions = $this->getConditions($cache_key . "-2", [
            ["field" => "name", "type" => "string", "view_field" => "tag_name"],
        ]);

        $builder = $this->modelClass::where($conditions);

        if ($tag_conditions) {
            $builder->wherehas("tags", function ($query) use ($tag_conditions) {
                $query->where($tag_conditions);
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

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $color_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::COLOR], "name");
        $brand_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::BRAND], "name");

        $sku_pattern = Setting::getValue("item_sku_pattern");

        if (!$sku_pattern)
        {
            $label = Setting::LABELS['item_sku_pattern'];
            throw new UserException("Setting : $label is not set yet", UserException::ERROR_USER_DATA_WRONG);
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("color_list", "brand_list", "sku_pattern", "field_labels"));

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

        $this->set_data_for_add_edit($model);

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function set_data_for_add_edit($model)
    {
        $qb = ItemCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->item_category_id)
        {
            $qb->orWhere("id", $model->item_category_id);
        }

        $item_category_records = ItemCategory::getTreeRecords($qb);

        $item_category_list = Util::getTreeListArray($item_category_records, "id", "name");

        $this->setForView(compact("item_category_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "description" => ["nullable"],
            "item_category_id" => ["required"],
            "color_id" => ["nullable"],
            "brand_id" => ["nullable"],
            "size" => ["nullable"],
            "specification" => ["nullable"],
            "sku" => ["required"],
            "images" => [],
            "other_files" => [],
            "tag_name" => [],
            "list_display_order" => "",
            "is_active" => "",
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

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            unset($save_data['tag_name']);
            unset($save_data['images']);
            unset($save_data['other_files']);

            // dd($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            $document_file = $this->model->images()->first();

            if ($document_file)
            {
                $this->model->main_image_id = $document_file->id;

                $this->model->save();
            }

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

    private function _saveOthers($validatedData)
    {
        $this->model->itemFile()->delete();

        if (isset($validatedData['images'])) {

            $ids = explode(",", $validatedData['images']);

            $document_files = DocumentFile::find($ids);

            foreach($document_files as $document_file)
            {
                $itemFile = new ItemFile([
                    "file_type" => "image",
                    "document_file_id" => $document_file->id
                ]);

                $this->model->itemFile()->save($itemFile);
            }
        }

        if (isset($validatedData['other_files'])) {

            $ids = explode(",", $validatedData['other_files']);

            $document_files = DocumentFile::find($ids);

            foreach($document_files as $document_file)
            {
                $itemFile = new ItemFile([
                    "file_type" => "",
                    "document_file_id" => $document_file->id
                ]);

                $this->model->itemFile()->save($itemFile);
            }
        }

        if (isset($validatedData['tag_name'])) {
            // dd($validatedData['tag_name']);
            $model_class_name = str_class_name_without_namespace($this->modelClass);

            if (!$model_class_name) {
                throw new UserException("could not found model_class_name");
            }

            $model_class_name = str_class_name_to_human_text($model_class_name);

            $tag_names = explode(",", $validatedData['tag_name']);

            $this->model->tags()->delete();

            foreach ($tag_names as $tag_name) {
                $save_data = [
                    "main_table_field" => "name",
                    "name" => $tag_name,
                ];

                $save_data['search_term'] = $model_class_name . "." . $save_data['name'];

                Tag::createTag($save_data, $this->model, $this->model);
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with([
            "tags", "images", "otherFiles"
        ])->findOrFail($id);

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

            unset($save_data['tag_name']);
            unset($save_data['images']);
            unset($save_data['other_files']);

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
        $model = $this->model = $this->modelClass::with([
            "tags", "images", "otherFiles"
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    public function ajax_get_list($category_id)
    {
        $response = ["status" => 1];

        try {

            $records = $this->modelClass::where("item_category_id", $category_id)->get();

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

    public function ajax_get_child_uom_list($item_id)
    {
        $response = ["status" => 1];

        try {

            $model = $this->modelClass::where("id", $item_id)->with(["category"])->first();
            
            if (!$model)
            {
                throw new UserException("Invalid Item ID : $item_id");
            }

            $records = Uom::where("is_active", 1)
                        ->where(function($q) use($model) {
                            $q->where("id", $model->category->base_uom_id)
                            ->orWhere("parent_id", $model->category->base_uom_id);
                        })
                        ->orderBy("list_display_order", "ASC")
                        ->get();

            $response['data'] = [];

            foreach ($records as $record) {
                $row = [
                    "id" => $record->id,
                    "name" => $record->short_name,
                    "conversion_factor" => $record->conversion_factor,
                ];

                $response['data'][] = $row;
            }
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function mark_as_main_image($id, $document_file_id)
    {
        $response = ["status" => 0, "msg" => ""];

        try
        {
            $product = $this->modelClass::findorFail($id);

            $document_file = DocumentFile::findorFail($document_file_id);
            $document_file->afterFind();

            $product->main_image_id = $document_file->id;
            $product->save();

            $response['status'] = 1;

            $response['url'] = $document_file->file_url;
        }
        catch(Exception $ex)
        {
            $response['status'] = 0;
            $response['msg'] = get_exception_display_message($ex);
        }

        return $this->responseJson($response);
    }

    public function import_excel(Request $request)
    {
        $sample = "samples/item.csv";

        $this->setForView([
            "sample" => route("sample.item")
        ]);

        $import_log_id = $request->get("import_log_id", false);
        if ($import_log_id)
        {
            $importLogModel = ImportLog::find($import_log_id);
        }

        if (!isset($importLogModel))
        {
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

        if ($request->isMethod('POST'))
        {
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

            try
            {
                $start_seconds = microtime(true);

                $this->beforeCsvImport();

                $path = ImportLog::getStoragePath();

                $filepath = $request->file('file')->store($path, DEFAULT_DISK);

                $full_file_path = ImportLog::getStorageDisk()->path($filepath);

                $importUtility = new ImportUtility($sample, $full_file_path);

                if (!$importUtility->checkHeaders())
                {
                    $this->setForView(array('errors' => $importUtility->errors));
                    
                    foreach($importUtility->errors as $error_msg)
                    {
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

                if (!$sku_pattern)
                {
                    $label = Setting::LABELS['item_sku_pattern'];
                    throw new UserException("Setting : $label is not set yet", UserException::ERROR_USER_DATA_WRONG);
                }
                
                foreach($csv_records as $i => $record)
                {
                    $record = Util::applyAll($record, array("trim"));
                    $validate = true;

                    $category_name = strtolower($record["item_category_id"]);
                    if (isset($item_category_list[$category_name]))
                    {
                        $record['item_category_id'] = $item_category_list[$category_name];
                        $msg_list['Category Found']++;
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['item_category_id'], $record['item_category_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    $color_name = strtolower($record["color_id"]);
                    if (isset($color_list[$color_name]))
                    {
                        $record['color_id'] = $color_list[$color_name];
                        $msg_list['Color Found']++;
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['color_id'], $record['color_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }

                    $brand_name = strtolower($record["brand_id"]);
                    if (isset($brand_list[$brand_name]))
                    {
                        $record['brand_id'] = $brand_list[$brand_name];
                        $msg_list['Brand Found']++;
                    }
                    else
                    {
                        LaravelExtend::addError($header_flip['brand_id'], $record['brand_id'] . " is not found", "Row #" . ($i + 2));
                        $validate = false;
                    }
                    
                    if ($validate)
                    {   
                        $model = new $this->modelClass();
                        $record['sku'] = Item::getSkuFromPattern($sku_pattern, $record);

                        // d($record, true);
                        
                        $is_insert = $is_update = null;
                        if ($model->insertOrUpdate($record, $is_insert, $is_update))
                        {
                            $accept_count++;

                            if ($is_insert)
                            {
                                $msg_list['Item Insert']++;
                            }
                            else if ($is_update)
                            {
                                $msg_list['Item Update']++;
                            }

                            $percent = round(($i + 1) * 100 / $total_count, 1);

                            $importLogModel->status_info = "Processing... $percent % " . ", Time Taken : " . Util::niceTime(microtime(true) - $start_seconds);
                            $importLogModel->saveQuietly();
                        }
                        else
                        {
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
                foreach($msg_list as $text => $count)
                {
                    $text_list[] = $text . " : " . $count;
                }

                $importLogModel->is_fully_run = true;

                $msg = "Total Count : $total_count, Accept Count : $accept_count";
                $msg .= ", " . implode(", ", $text_list);

                if (LaravelExtend::$errors)
                {
                    throw new UserException("Unable to Save all records. " . $msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $msg = "All File rows updated successfully. " . $msg;
                $importLogModel->flash_msg = $msg;
                $importLogModel->save();

                Session::flash('success', $msg);
            }
            catch(UserException $ex)
            {
                $msg = $ex->getMessage();

                if (isset($importLogModel))
                {
                    $importLogModel->flash_msg = $msg;
                    $importLogModel->have_error = 1;

                    if (LaravelExtend::$errors)
                    {
                        $importLogModel->errors = LaravelExtend::$errors;                    
                    }

                    $importLogModel->save();
                }
                
                return back()->With("fail", $msg)->with("custom_errors", LaravelExtend::$errors);
            }
            
            catch(\Exception $ex)
            {
                $msg = get_exception_display_message($ex);

                if (isset($importLogModel))
                {
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
            "tags", "mainImage"
        ]);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        d($records, true);

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
