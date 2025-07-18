<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ArrayHelper;
use App\Helpers\FileUtility;
use App\Helpers\ImageUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\DocumentCategory;
use App\Models\DocumentFile;
use App\Models\FieldSetting;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DocumentFileController extends BackendController
{
    public String $routePrefix = "document-file";

    protected $modelClass = DocumentFile::class;

    protected DocumentFile | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d(LaravelExtend::getQueryLog("", -3));

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
        ]);

        $builder = $this->modelClass::query();

        if($conditions)
        {
            $builder = $builder->where($conditions);
        }

        if ($apply_sort)
        {
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

        if ($add_via)
        {
            $view_name = "add_via_" . $add_via;
        }

        return $this->view($view_name);
    }

    private function _common_validation_rules($add_via)
    {
        if ($add_via == "upload")
        {
            return [
                "name" => ["required"],
                "file_original" => ["required", "file"],
                "document_category_id" => ["required"],
            ];
        }

        return [];
    }

    private function _common_validation_messages()
    {
        return [
        ];
    }

    private function set_data_for_add_edit($model)
    {
        $qb = DocumentCategory::where("is_active", 1);

        $conditions = ["is_active" => 1];

        if ($model->document_category_id)
        {
            $qb->orWhere("id", $model->document_category_id);
        }

        $document_category_records = DocumentCategory::getTreeRecords($qb);

        $document_category_list = Util::getTreeListArray($document_category_records, "id", "name");

        $this->setForView(compact("document_category_list"));
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

        try
        {
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
        }
        catch(Exception $ex)
        {
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

            if ($validatedData['type'] == "image")
            {
                $imageInfo = getimagesize($full_file_path);

                if ($imageInfo !== false) {
                    $width = $imageInfo[0]; 
                    $height = $imageInfo[1];

                    $validatedData['image_height'] = $height;
                    $validatedData['image_width'] = $width;

                    if ($width >= 1024)
                    {
                        $width = 1024;
                    }

                    if ($height >= 768)
                    {
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
            
            if (!isset($validatedData['file']))
            {
                $validatedData['file'] = $validatedData['file_original'];
            }

            $full_file_path = DocumentFile::getStorageDisk()->path($validatedData['file']);

            $validatedData['file_full_path'] = $full_file_path;

            $size = filesize($validatedData['storage_base_path'] . $validatedData['file']);
            $size = $size / (1024 * 1024);
            $validatedData['file_size_in_mb'] = $size;
        }

        return $validatedData;
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["categoryAttribute"])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $save_data = $validatedData;

            $this->model->fill($save_data);

            $this->model->save();

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
}
