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
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemFile;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
