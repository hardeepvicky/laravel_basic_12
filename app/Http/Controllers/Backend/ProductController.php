<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAttribute;
use App\Models\ProductFile;
use App\Models\ProductKeyword;
use App\Models\Uom;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductController extends BackendController
{
    public String $routePrefix = "product";

    protected $modelClass = Product::class;

    protected Product | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "productAttributes" => function($q){
                $q->orderBy("display_order", "ASC");
            },
            "productAttributes.categoryAttribute",
            "mainImage",
            "images",
            "videos",
            "otherFiles",
            "keywords",
            "catalog.document"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        Product::convertRecordsForChildUom($records);

        // d($records->toArray(), true);

        foreach($records as $record)
        {
            if ($record->mainImage?->id)
            {
                $record->mainImage->afterFind();
            }
        }

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
            ["field" => "is_find_nested_child", "type" => "bool", "view_field" => "is_find_nested_child"],
            ["field" => "name", "type" => "string"],
            ["field" => "model_name", "type" => "string"],
            ["field" => "sku", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["product_category_id"]) && $condition_list["product_category_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        )
        {
            $child_id_list = ProductCategory::getChildIdList([$condition_list["product_category_id"]]);

            if ($child_id_list)
            {
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

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("field_labels"));

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

    private function set_data_for_add_edit($model)
    {
        $qb = ProductCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->product_category_id)
        {
            $qb->orWhere("id", $model->product_category_id);
        }

        $product_category_records = ProductCategory::getTreeRecords($qb);

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name");

        $uom_list = Uom::getUomListForCode("size");

        $this->setForView(compact("product_category_list", "uom_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "product_category_id" => ["required"],
            "product_catalog_id" => ["required"],
            "name" => ["required", "min:3", "max:80"],
            "model_name" => ["required", "max:80"],
            "length" => ["required"],
            "width" => ["required"],
            "height" => ["required"],
            "size_uom_id" => ["required"],
            "net_weight" => ["required"],
            "gross_weight" => ["required"],
            "sku" => ["required", "max:255"],
            "description" => ["nullable"],
            "description_html" => ["nullable"],
            "seo_title" => ["nullable", "max:80"],
            "meta_main_keyword" => ["nullable", "max:45"],
            "meta_description" => ["nullable", "max:255"],
            "product_attributes" => [],
            "images" => [],
            "videos" => [],
            "other_files" => [],
            "keywords" => [],
            "list_display_order" => "",
            "is_active" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // dd($validatedData);

            $save_data = $validatedData;
            unset($save_data['product_attributes']);
            unset($save_data['images']);
            unset($save_data['videos']);
            unset($save_data['other_files']);

            $this->model->fill($save_data);

            if (!$this->model->save())
            {
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
                throw new Exception("Fail to Save");
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
        if (isset($validatedData['product_attributes']))
        {
            $this->model->productAttributes()->delete();

            foreach($validatedData['product_attributes'] as $category_id => $value)
            {
                $categoryAttribute = ProductCategoryAttribute::findOrFail($category_id);

                $child_model = new ProductAttribute([
                    "product_category_attribute_id" => $category_id,
                    "name" => $categoryAttribute->name,
                    "value" => $value,
                    "display_order" => $categoryAttribute->list_display_order
                ]);

                $this->model->productAttributes()->save($child_model);
            }
        }

        $this->model->productFile()->delete();

        if (isset($validatedData['images'])) {

            $ids = explode(",", $validatedData['images']);

            $document_files = DocumentFile::find($ids);

            foreach($document_files as $document_file)
            {
                $productFile = new ProductFile([
                    "file_type" => "image",
                    "document_file_id" => $document_file->id
                ]);

                $this->model->productFile()->save($productFile);
            }
        }

        if (isset($validatedData['videos'])) {

            $ids = explode(",", $validatedData['videos']);

            $document_files = DocumentFile::find($ids);

            foreach($document_files as $document_file)
            {
                $productFile = new ProductFile([
                    "file_type" => "video",
                    "document_file_id" => $document_file->id
                ]);

                $this->model->productFile()->save($productFile);
            }
        }

        if (isset($validatedData['other_files'])) {

            $ids = explode(",", $validatedData['other_files']);

            $document_files = DocumentFile::find($ids);

            foreach($document_files as $document_file)
            {
                $productFile = new ProductFile([
                    "file_type" => "",
                    "document_file_id" => $document_file->id
                ]);

                $this->model->productFile()->save($productFile);
            }
        }

        if (isset($validatedData['keywords'])) {

            $this->model->keywords()->delete();

            $keywords = explode(",", $validatedData['keywords']);

            foreach($keywords as $keyword)
            {
                $child = new ProductKeyword([
                    "name" => $keyword,
                ]);

                $this->model->keywords()->save($child);
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with([
            "images", "videos", "otherFiles", "keywords"
        ])
        ->findOrFail($id);

        $this->beforeEdit($model);

        $model->convertToChildUom();

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

        $this->set_data_for_add_edit($model);

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;
            unset($save_data['product_attributes']);
            unset($save_data['images']);
            unset($save_data['videos']);
            unset($save_data['otherFiles']);

            $this->model->fill($save_data);

            if (!$this->model->save())
            {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("Fail to Save");
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
        $model = $this->model = $this->modelClass::with([
            "images", "videos", "otherFiles", "keywords"
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

    public function ajax_get_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "name", "type" => "string"],
                ["field" => "product_category_id", "type" => "int"],
            ];

            $conditions = $this->getConditions(null, $pattern_array);

            if (empty($conditions))
            {
                throw new UserException("No any condition applied");
            }

            $qb = $this->modelClass::where($conditions);
            $qb->where("is_active", 1);

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

    public function ajax_load_attribute_fields($category_id, $id = 0)
    {
        $category = ProductCategory::with([
            "categoryAttribute" => function($q){
                $q->where("is_active", 1);
                $q->orderBy("list_display_order", "ASC");
            },
            ])->findOrFail($category_id);

        if ($id)
        {
            $model = $this->modelClass::with(["productAttributes"])->find($id);
        }
        else
        {
            $model = new $this->modelClass();
        }

        $drop_down_lists = [];

        foreach($category->categoryAttribute as $categoryAttribute)
        {
            $categoryAttribute->value = "";

            if ($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_DROPDOWN)
            {
                $drop_down_lists[$categoryAttribute->id] = DropDownValue::getList("id", "display_name", ["type" => $categoryAttribute->dropdown_type]);
            }

            foreach($model->productAttributes as $productAttribute)
            {
                if ($productAttribute->product_category_attribute_id == $categoryAttribute->id)
                {
                    $categoryAttribute->value = $productAttribute->value;
                }
            }
        }

        $uom_list = Uom::getListCache();

        $this->setForView(compact("category", "model", "drop_down_lists", "uom_list"));

        return $this->view(__FUNCTION__);
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

    public function csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "mainImage",
            "catalog.document"
        ]);

        $records = $builder->get();

        Product::convertRecordsForChildUom($records);

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],

                $field_labels['product_category_id'] => get_value_from_list($product_category_list, $record['product_category_id']),
                $field_labels['name'] => $record['name'],
                $field_labels['model_name'] => $record['model_name'],
                $field_labels['sku'] => $record['sku'],
                $field_labels['length'] => $record['length'],
                $field_labels['width'] => $record['width'],
                $field_labels['height'] => $record['height'],
                $field_labels['size_uom_id'] => $record['size_uom_name'],
                $field_labels['net_weight'] => $record['net_weight'],
                $field_labels['gross_weight'] => $record['gross_weight'],
                'Weight UOM' => 'kg.',
                $field_labels['height'] => $record['height'],
                $field_labels['description'] => $record['description'] ?? "",
                $field_labels['seo_title'] => $record['seo_title'] ?? "",
                $field_labels['meta_main_keyword'] => $record['meta_main_keyword'] ?? "",
                $field_labels['product_catalog_id'] => $record['catalog']['document']['url'] ?? "",

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

    public function csv_attribute()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "productAttributes" => function($q){
                $q->orderBy("display_order", "ASC");
            },
            "productAttributes.categoryAttribute",
        ]);

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

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            foreach($record['product_attributes'] as $attribute)
            {
                $csv_records[] = [
                    'Product ID' => $record['id'],

                    $field_labels['product_category_id'] => get_value_from_list($product_category_list, $record['product_category_id']),
                    $field_labels['name'] => $record['name'],
                    $field_labels['model_name'] => $record['model_name'],
                    $field_labels['sku'] => $record['sku'],

                    'Attribute' => $attribute['category_attribute']['name'] ?? "",
                    'value' => $attribute['value'] ?? "",
                    
                    'Created' => if_date_time($attribute['created_at']),
                    'Created By' => $user_cache_list[$attribute['created_by']] ?? "",
                    'Updated' => if_date_time($attribute['updated_at']),
                    'Updated By' => $user_cache_list[$attribute['updated_by']] ?? "",
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
        $file_prefix = str_class_name_to_human_text(ProductCategoryAttribute::class);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
