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
use App\Models\Department;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Item;
use App\Models\ItemBrand;
use App\Models\ItemBrandPart;
use App\Models\ItemCategory;
use App\Models\ItemFile;
use App\Models\ItemSubDepartment;
use App\Models\Setting;
use App\Models\SubDepartment;
use App\Models\Tag;
use App\Models\Uom;
use App\Models\User;
use App\View\Components\Inputs\DropDown;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ItemController extends BackendController
{
    public String $routePrefix = "item";

    const TABS = [
        "default" => "Default",
        "additional" => "Additional Details",
    ];

    protected $modelClass = Item::class;
    protected Item | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "rootItemCategory",
            "parentItemCategory",
            "childItemCategory",
            "itemBrandPart",
            "itemBrandPart.make",
            "origin",
            "certification",
            "tags",
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

        $item_brand_list = ItemBrand::getList("id", "display_name", [], "list_display_order");

        $this->setForView(compact("records", "item_brand_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "root_item_category_id", "type" => "int"],
            ["field" => "name", "type" => "string"],
            ["field" => "item_brand_id", "type" => "int"],
            ["field" => "size", "type" => "string"],
            ["field" => "specification", "type" => "string"],
            ["field" => "sku", "type" => "string"],
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

        $dept_conditions = $this->getConditions($cache_key . "-3", [
            ["field" => "dept_id", "type" => "int"],
            ["field" => "sub_dept_id", "type" => "int"],
        ], true);

        $sub_dept_conditions = [];

        if ( isset($dept_conditions['dept_id']) && !empty($dept_conditions['dept_id']) )
        {
            $builder->whereHas("itemSubDepartment", function($q) use($dept_conditions)
            {
                $q->where("dept_id", $dept_conditions['dept_id']['value']);
            });

            $sub_dept_conditions['department_id'] = $dept_conditions['dept_id']['value'];
        }

        $sub_dept_list = SubDepartment::getList("id", "display_name", $sub_dept_conditions, "list_display_order");

        $this->setForView(compact("sub_dept_list"));

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

        $root_item_category_list = ItemCategory::getList("id", "display_name", ["parent_id" => 0], "list_display_order");


        $origin_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::ORIGIN], "list_display_order");
        $certification_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::CERTIFICATION], "list_display_order");

        $sku_pattern = Setting::getValue("item_sku_pattern");

        if (!$sku_pattern)
        {
            $label = Setting::LABELS['item_sku_pattern'];
            throw new UserException("Setting : $label is not set yet", UserException::ERROR_USER_DATA_WRONG);
        }

        $selected_department_ids = Setting::getValue("item_module_department_ids");

        if (!$selected_department_ids)
        {
            $label = Setting::LABELS['item_module_department_ids'];
            throw new UserException("Setting : $label is not set yet", UserException::ERROR_USER_DATA_WRONG);
        }

        $dept_list = Department::getList("id", "display_name", [], "list_display_order");

        $qb = ItemCategory::where("is_active", 1);

        $item_category_records = ItemCategory::getTreeRecords($qb);

        $item_category_list = Util::getTreeListArray($item_category_records, "id", "name", false, false, "", " >> ");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $item_sub_department_field_labels = FieldSetting::getDynamicFields(ItemSubDepartment::class);

        $item_brand_parts_field_labels = FieldSetting::getDynamicFields(ItemBrandPart::class);

        $this->setForView(compact(
            "root_item_category_list", "dept_list", "origin_list", "certification_list", "sku_pattern", "selected_department_ids", "item_category_list",
            "field_labels", "item_sub_department_field_labels", "item_brand_parts_field_labels"
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

        $current_tab = $model->tab_name = array_key_first(self::TABS);

        $this->setForView(compact("model", 'form', 'current_tab'));

        $this->set_data_for_add_edit($model);

        return $this->view("tabs");
    }

    private function set_data_for_add_edit($model)
    {
        $tabs = self::TABS;

        $name_list = Item::select("name")->distinct()->pluck('name')->toArray();
        $specification_list = Item::select("specification")->distinct()->pluck('specification')->toArray();
        $size_list = Item::select("size")->distinct()->pluck('size')->toArray();

        $item_list = Item::pluck('sku', 'id')->toArray();

        $this->setForView(compact("tabs", "name_list", "item_list", "specification_list", "size_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "root_item_category_id" => ["required"],
            "parent_item_category_id" => ["nullable"],
            "child_item_category_id" => ["nullable"],
            "size" => ["nullable"],
            "specification" => ["nullable"],
            "sku" => ["required"],
            "item_sub_departments" => [],
            "item_brand_parts" => [],
            "images" => [],
            "tab_name" => "",            
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
            unset($save_data['item_sub_departments']);
            unset($save_data['item_brand_parts']);

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

            $this->saveSqlLog();

            $next_tab = Util::getNextTab(self::TABS, $validatedData['tab_name'], $is_last_tab);

            return redirect()->route($this->routePrefix . ".edit", ["id" => $this->model->id, "tab" => $next_tab])->with('success', 'Record created successfully');

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

            if ($ids)
            {
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
        }

        if (isset($validatedData['other_files'])) {

            $ids = explode(",", $validatedData['other_files']);

            if ($ids)
            {
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

        if (isset($validatedData['item_sub_departments']) )
        {
            unset($validatedData['item_sub_departments']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['item_sub_departments'])) {
                $base_rules = [
                    "dept_id" => ["required"],
                    "sub_dept_id" => ["required"],
                ];

                $child_model = new ItemSubDepartment();

                $existing_id_list = $this->model->itemSubDepartment()->pluck("id", "id")->toArray();

                $validatedData['item_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['item_sub_departments'] as $item_sub_department) {

                    $validator = Validator::make($item_sub_department, $base_rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Departments");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($item_sub_department['id']) {
                        $child_model = ItemSubDepartment::find($item_sub_department['id']);

                        if (!$child_model) {
                            $child_model = new ItemSubDepartment();
                        } else {
                            unset($existing_id_list[$item_sub_department['id']]);
                        }
                    } else {
                        $child_model = new ItemSubDepartment();
                    }

                    // dd($item_sub_department);

                    $child_model->fill($item_sub_department);

                    $child_model = $this->model->itemSubDepartment()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->itemSubDepartment()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }


        if (isset($validatedData['item_brand_parts']) )
        {
            unset($validatedData['item_brand_parts']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['item_brand_parts'])) {
                $base_rules = [
                    "make_id" => ["required"],
                    "part_no" => ["required"],
                ];

                $existing_id_list = $this->model->itemBrandPart()->pluck("id", "id")->toArray();

                $validatedData['item_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['item_brand_parts'] as $item_brand_part) {

                    $item_brand_part['item_id'] = $this->model->id;

                    $display_order++;
                    if ($item_brand_part['id']) {
                        $child_model = ItemBrandPart::find($item_brand_part['id']);

                        if (!$child_model) {
                            $child_model = new ItemBrandPart();
                        } else {
                            unset($existing_id_list[$item_brand_part['id']]);
                        }
                    } else {
                        $child_model = new ItemBrandPart();
                    }

                    $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($item_brand_part));
                    $validator = Validator::make($item_brand_part, $rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Departments");
                        }
                        continue;
                    }

                    // dd($item_brand_part);

                    $child_model->fill($item_brand_part);

                    $child_model = $this->model->itemBrandPart()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->itemBrandPart()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }
    }

    public function edit($id, $current_tab = "default")
    {
        $model = $this->model = $this->modelClass::with([
            "tags", "images", "otherFiles"
        ])->findOrFail($id);

        $this->beforeEdit($this->model);

        $this->model->child_item_category_id_main = $this->model->child_item_category_id ?? $this->model->parent_item_category_id ?? $this->model->root_item_category_id;

        $form = [
            'url' => route($this->routePrefix . '.update', [$id, $current_tab]),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form", "current_tab"));

        $this->set_data_for_add_edit($model);

        return $this->view("tabs");
    }

    public function update($id, $current_tab, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        switch($current_tab)
        {
            case "default":
                $rules = $this->_common_validation_rules();
                $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
                $messages = $this->_common_validation_messages();
                break;

            case "additional":
                $rules = [
                    "part_no" => ["nullable"],
                    "weight" => ["nullable"],
                    "minimum_order_qty" => ["nullable"],
                    "origin_id" => ["nullable"],
                    "certification_id" => ["nullable"],
                    "reference" => ["nullable"],
                    "other_files" => [],
                    "tag_name" => [],
                    "description" => ["nullable"],
                    "list_display_order" => "",
                    "is_active" => "",
                    "can_be_use_for_bom" => "",
                ];
                break;
        }


        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // dd($validatedData);

        DB::beginTransaction();

        try {

            $save_data = $validatedData;

            unset($save_data['tag_name']);
            unset($save_data['images']);
            unset($save_data['other_files']);
            unset($save_data['item_sub_departments']);
            unset($save_data['item_brand_parts']);

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

            $this->saveSqlLog();

            $is_last_tab = false;
            $next_tab = Util::getNextTab(self::TABS, $current_tab, $is_last_tab);

            if ($is_last_tab)
            {
                return redirect()->route($this->routePrefix . ".index")->with('success', 'Record updated successfully');
            }
            else
            {
                return redirect()->route($this->routePrefix . ".edit", ["id" => $this->model->id, "tab" => $next_tab])->with('success', 'Record updated successfully');
            }

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

        $this->model->child_item_category_id_main = $this->model->child_item_category_id ?? $this->model->parent_item_category_id ?? $this->model->root_item_category_id;

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function ajax_get_list($category_id = "")
    {
        $response = ["status" => 1];

        try {

            $qb = $this->modelClass::query();

            if ($category_id)
            {
                $qb->where("item_category_id", $category_id);
            }

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

    public function ajax_get_child_uom_list($item_id)
    {
        $response = ["status" => 1, "data" => []];

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

    public function ajax_get_default_uom($item_id)
    {
        $response = ["status" => 1, "data" => []];

        try {

            $model = $this->modelClass::where("id", $item_id)->with(
                ["category", "category.uom"]
            )->first();

            if (!$model)
            {
                throw new UserException("Invalid Item ID : $item_id");
            }

            $response['data'] = $model->category->uom->toArray();

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
            "rootItemCategory",
            "parentItemCategory",
            "childItemCategory",
            "itemBrand",
            "origin",
            "certification",
            "tags",
            "mainImage",
        ]);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
            $record->mainImage?->afterFind();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $tag_list = [];

            foreach($record['tags'] as $tag)
            {
                $tag_list[] = $tag['display_name'];
            }
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['name'] => $record['name'],
                $field_labels['specification'] => $record['specification'] ?? "",
                $field_labels['size'] => $record['size'] ?? "",
                $field_labels['item_brand_id'] => $record['item_brand']['name'] ?? "",
                $field_labels['sku'] => $record['sku'] ?? "",
                $field_labels['root_item_category_id'] => $record['root_item_category']['name'] ?? "",
                $field_labels['parent_item_category_id'] => $record['parent_item_category']['name'] ?? "",
                $field_labels['child_item_category_id'] => $record['child_item_category']['name'] ?? "",
                $field_labels['origin_id'] => $record['origin']['name'] ?? "",
                $field_labels['certification_id'] => $record['certification']['name'] ?? "",
                $field_labels['part_no'] => $record['part_no'] ?? "",
                $field_labels['weight'] => $record['weight'] ?? "",
                $field_labels['minimum_order_qty'] => $record['minimum_order_qty'] ?? "",
                $field_labels['description'] => $record['description'] ?? "",
                $field_labels['main_image_id'] => isset($record['main_image']['file_url']) ? url($record['main_image']['file_url']) : "",
                'Main Image Name' => $record['main_image']['display_name'] ?? "",

                $field_labels['tag_name'] => implode(", ", $tag_list),

                $field_labels['is_active'] => get_value_from_list($yes_no_list, $record['is_active']),
                $field_labels['list_display_order'] => $record['list_display_order'] ?? "",

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

    public function ajax_get_specifications_of_item($name = "")
    {
        $response = ["status" => 1, "data" => []];

        $response['data'] = Item::where("name", $name)->select("specification")->distinct()->pluck("specification")->toArray();

        return $this->responseJson($response);
    }

    public function ajax_get_sizes_of_item($name = "")
    {
        $response = ["status" => 1, "data" => []];

        $response['data'] = Item::where("name", $name)->select("size")->distinct()->pluck("size")->toArray();

        return $this->responseJson($response);
    }

    public function gallery()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "root_item_category_id", "type" => "int"],
            ["field" => "name", "type" => "string"],
        ]);

        $builder = $this->modelClass::query();

        $conditions[] = ["is_active", 1];

        $builder->where($conditions);

        $builder->with([
            "mainImage",
        ]);

        $this->model = new $this->modelClass;

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $sort_by_list = [
            "id" => "ID",
            "name" => "Name",            
            "created_at" => "Created",
            "updated_at" => "Updated",
            "created_by" => "Created By",
            "updated_by" => "Updated By",
        ];

        $sort_dir_list = [
            "ASC" => "Ascending",
            "DESC" => "Descending"
        ];

        $this->setForView(compact("sort_by_list", "sort_dir_list"));

        $clear_cache = request('is_sort_clear', false);
        $value = request('value', false);

        if ($value) {
            $builder->orderByRaw('FIELD(id,' . $value . ') DESC');
            $builder->orderBy("id", "DESC");

            $this->setForView([
                "selected_value" => $value,
                "selected_id_list" => explode(",", $value)
            ]);

            $sort = [
                'sort_by' => 'id',
                'sort_dir' => 'DESC'
            ];

        } else {
            $sort = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort['sort_by'], $sort['sort_dir']);
        }

        $this->setForView($sort);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        foreach($records as $record)
        {
            if ($record->mainImage?->id)
            {
                $record->mainImage->afterFind();
            }
        }

        $root_category_list = ItemCategory::getList("id", "display_name", ["parent_id" => 0], "list_display_order");
        
        $index_table_id = request("index_table_id", "index_table");
        $modal_id = request("modal_id", "");
        $multiple = request("multiple", 0);

        $this->setForView(compact("records", "root_category_list", "index_table_id", "modal_id", "multiple"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function preview(Request $request)
    {
        $ids = $request->get("ids");

        if ($ids)
        {
            $id_list = explode(",", $ids);

            $builder = $this->modelClass::whereIn("id", $id_list);
            $builder->with([
                "mainImage",
            ]);

            $records = $builder->get();

            foreach($records as $record)
            {
                if ($record->mainImage?->id)
                {
                    $record->mainImage->afterFind();
                }
            }

            $this->setForView(compact("records"));

            return $this->viewIndex(__FUNCTION__);
        }
    }

    public function preview_small(Request $request)
    {
        $ids = $request->get("ids");

        if ($ids)
        {
            $id_list = explode(",", $ids);

            $builder = $this->modelClass::whereIn("id", $id_list);

            $records = $builder->get();

            $this->setForView(compact("records"));

            return $this->viewIndex(__FUNCTION__);
        }
    }
}
