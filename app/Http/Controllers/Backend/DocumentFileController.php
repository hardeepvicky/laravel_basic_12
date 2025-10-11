<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ArrayHelper;
use App\Helpers\FileUtility;
use App\Helpers\ImageUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\Department;
use App\Models\DocumentCategory;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DocumentFileController extends BackendController
{
    protected int $paginationLimit = 10;
    
    public String $routePrefix = "document-file";

    protected $modelClass = DocumentFile::class;

    protected DocumentFile | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->where("is_archive", 0);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        // d(LaravelExtend::getQueryLog("", -2));

        $category_records = DocumentCategory::getTreeRecords(DocumentCategory::query());

        $category_list = Util::getTreeListArray($category_records, "id", "name", false, false);

        $this->setForView(compact("records", "category_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function archive_index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->where("is_archive", 1);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        // d(LaravelExtend::getQueryLog("", -3));

        $category_records = DocumentCategory::getTreeRecords(DocumentCategory::query());

        $category_list = Util::getTreeListArray($category_records, "id", "name", false, false);

        LaravelExtend::addInfo("Files will be delete permanently after 7 days", "primary");

        $this->setForView(compact("records", "category_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "document_category_id", "type" => "int"],
            ["field" => "is_find_nested_child", "type" => "int"],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["document_category_id"]) && $condition_list["document_category_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        ) {
            $child_id_list = DocumentCategory::getChildIdList([$condition_list["document_category_id"]]);

            if ($child_id_list) {
                $builder->whereIn("document_category_id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["document_category_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }

        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        // d($conditions, true);

        if ($conditions) {
            $builder = $builder->where($conditions);
        }

        $conditions2 = $this->getConditions($cache_key . ".2", [            
            ["field" => "dept_id", "type" => "int", "default" => LaravelExtend::getAuthUserId()],
        ], true);

        if (isset($conditions2['dept_id']))
        {
            $builder->whereHas("createdBy", function($q) use($conditions2) {
                $q->where("dept_id", $conditions2['dept_id']['value']);
            });
        }


        if ($apply_sort) {
            $this->model = new $this->modelClass;

            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $sort_by_list = [
                "id" => "ID",
                "name" => "Name",
                "type" => "Type",
                "document_category_id" => "Document Category",
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
        }

        $dept_list = Department::getList("id", "display_name", [], "list_display_order");

        $this->setForView(compact("dept_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $type_list = $this->model->getTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $index_table_id = request("index_table_id", "index_table");
        $modal_id = request("modal_id", "");
        $multiple = request("multiple", 0);

        $this->setForView(compact("field_labels", "type_list", "index_table_id", "modal_id", "multiple"));

        return parent::beforeViewRender();
    }

    public function create(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $add_via = $request->get('add_via');

        $this->setForView(compact("model", 'form', 'add_via'));

        $this->set_data_for_add_edit($model);

        $view_name = "form";

        if ($add_via) {
            $view_name = "add_via_" . $add_via;
        }

        return $this->view($view_name);
    }

    private function _common_validation_rules($add_via)
    {
        if ($add_via == "upload") {
            return [
                "name" => ["required"],
                "file_original" => ["required", "file"],
                "document_category_id" => ["required"],
            ];
        } else if ($add_via == "url") {
            return [
                "name" => ["required"],
                "url" => ["required"],
                "file_ext" => ["required"],
                "document_category_id" => ["required"],
            ];
        }

        return [];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function set_data_for_add_edit($model)
    {
        $qb = DocumentCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->document_category_id) {
            $qb->orWhere("id", $model->document_category_id);
        }

        $document_category_records = DocumentCategory::getTreeRecords($qb);

        $document_category_list = Util::getTreeListArray($document_category_records, "id", "name");

        $ext_list = DropDownValue::getList("short_name", "display_name", ["type" => DropDownValue::FILE_EXTENSION]);

        $this->setForView(compact("document_category_list", "ext_list"));
    }

    public function store(Request $request)
    {
        $this->model = new $this->modelClass();

        $this->beforeCreate();

        $rules = $this->_common_validation_rules($request->input("add_via"));

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // dd($validatedData);
            $validatedData = $this->_beforeSave($request, $validatedData);

            $save_data = $validatedData;

            // d($save_data, true);

            $this->model = $this->modelClass::create($save_data);

            if (LaravelExtend::$errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _beforeSave(Request $request, array $validatedData)
    {
        if ($request->hasFile('file_original')) {
            $path = rtrim(DocumentFile::getStoragePath(), "/");

            $file = $request->file('file_original');

            $filepath = $file->store($path, DEFAULT_DISK);
            $validatedData['file_original'] = $filepath;

            $validatedData['storage_base_path'] = DocumentFile::getStorageDisk()->path("/");

            $validatedData['file_name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $validatedData['file_ext'] = pathinfo($validatedData['file_original'], PATHINFO_EXTENSION);


            $validatedData['type'] = FileUtility::getFileTypeFromExt($validatedData['file_ext']);

            $full_file_path = DocumentFile::getStorageDisk()->path($filepath);

            if ($validatedData['type'] == "image") {
                $imageInfo = getimagesize($full_file_path);

                if ($imageInfo !== false) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    $validatedData['image_height'] = $height;
                    $validatedData['image_width'] = $width;

                    if ($width >= 1024) {
                        $width = 1024;
                    }

                    if ($height >= 768) {
                        $height = 768;
                    }

                    $validatedData['file_name'] = $validatedData['file_name'] . "-" . $width;

                    $filename = FileUtility::getAutoincreamentFileName($validatedData['file_name'], $validatedData['file_ext'], $validatedData['storage_base_path'] . $path . "/");

                    // dd($filename);

                    $validatedData['file_name'] = pathinfo($filename, PATHINFO_FILENAME);

                    $validatedData['file'] = $file->storeAs($path, $filename, DEFAULT_DISK);
                    $imgUtility = new ImageUtility($validatedData['storage_base_path'] . $validatedData['file_original'], $validatedData['storage_base_path'] . $validatedData['file']);
                    $imgUtility->resize($width, $height);
                }
            }

            if (!isset($validatedData['file'])) {
                $validatedData['file'] = $validatedData['file_original'];
            }

            $full_file_path = DocumentFile::getStorageDisk()->path($validatedData['file']);

            $validatedData['file_full_path'] = $full_file_path;

            $size = filesize($validatedData['storage_base_path'] . $validatedData['file']);
            $size = $size / (1024 * 1024);
            $validatedData['file_size_in_mb'] = $size;
        } else if (isset($validatedData['url'])) {
            $validatedData['file_name'] = $validatedData['name'];
            $validatedData['type'] = FileUtility::getFileTypeFromExt($validatedData['file_ext']);
        }

        return $validatedData;
    }

    public function archive($id)
    {
        $request = request();

        try {
            $model = $this->modelClass::findOrFail($id);

            $model->is_archive = 1;

            if (!$model->save()) {
                throw new Exception("Fail to Save");
            }

            $msg = $model->name . " archived successfully";

            if ($request->ajax()) {
                return $this->responseJson(['status' => 1, 'msg' => $msg]);
            }

            return back()->with('success', $msg);
        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }

    public function unarchive($id)
    {
        $request = request();

        try {
            $model = $this->modelClass::findOrFail($id);

            $model->is_archive = 0;

            if (!$model->save()) {
                throw new Exception("Fail to Save");
            }

            $msg = $model->name . " un-archived successfully";

            if ($request->ajax()) {
                return $this->responseJson(['status' => 1, 'msg' => $msg]);
            }

            return back()->with('success', $msg);
        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }
}
