<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\DocumentFile;
use App\Models\FieldSetting;
use App\Models\Project;
use App\Models\ProjectTarget;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ProjectController extends BackendController
{
    public String $routePrefix = "project";

    protected $modelClass = Project::class;

    protected Project | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "projectTarget" => function($q){
                $q->orderBy("list_display_order");
            },
            "projectTarget.task",
            "documentFile"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        foreach($records as $record)
        {            
            foreach($record->projectTarget as $target)
            {
                $target->task_count = $target->task->count();
                $target->complete_task_count = $target->task()->where("is_completed", 1)->count();
                $target->complete_per = round($target->complete_task_count * 100 / $target->task_count);
            }
        }

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

        $builder = $this->modelClass::where($conditions);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass;

            $sort_params = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $target_field_labels = FieldSetting::getDynamicFields(ProjectTarget::class);

        $this->setForView(compact(
            "field_labels", "target_field_labels", 
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

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],            
            "description" => ["nullable"],            
            "list_display_order" => ["required"],
            "is_active" => "",
            "project_target" => []
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

        $model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $this->model = $this->modelClass::create($validatedData);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            $save_and_close = (bool) request("save_and_close", false);

            if ($save_and_close)
            {
                return redirect()->to(get_current_path_url([
                    'last_insert_id' => $this->model->id,                    
                ]));
            }

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['projectFile'])) {

            $ids = explode(",", $validatedData['projectFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }

        unset($validatedData['project_target']['(sr-id)']);

        if (isset($validatedData['project_target']))
        {
            $rules = [
                "name" => ["required"],
                "end_date" => ["required"]
            ];

            $child_model = new ProjectTarget();

            $existing_id_list = $this->model->projectTarget()->pluck("id", "id")->toArray();

            $validatedData['project_id'] = $this->model->id;

            $display_order = 0;

            foreach($validatedData['project_target'] as $project_target)
            {
                $validator = Validator::make($project_target, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Terms");
                    }
                    continue;
                }

                $display_order++;
                if ($project_target['id'])
                {
                    $child_model = ProjectTarget::find($project_target['id']);

                    if (!$child_model)
                    {
                        $child_model = new ProjectTarget();
                    }
                    else
                    {
                        unset($existing_id_list[$project_target['id']]);
                    }
                }
                else
                {
                    $child_model = new ProjectTarget();
                }

                $project_target['list_display_order'] = $display_order;

                $project_target = $child_model->convertTimeZoneBeforeQueryToDatabase($project_target);

                $child_model->fill($project_target);

                $this->model->projectTarget()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->projectTarget()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::with([
            "projectTarget" => function($q){
                $q->orderBy("list_display_order");
            },
            "projectFile"
        ])->findOrFail($id);

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
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $this->model->fill($validatedData);

            $this->model->save();

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

    public function ajax_get_target_list($project_id)
    {
        $response = ["status" => 1];

        try {

            $records = ProjectTarget::where("project_id", $project_id)->orderBy("list_display_order")->get();

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
