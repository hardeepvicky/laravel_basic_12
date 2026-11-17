<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\FieldSetting;
use App\Models\Task;
use App\Models\Type;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class WhatsappTemplateController extends BackendController
{
    public String $routePrefix = "admin.whatsapp-template";

    protected $modelClass = WhatsappTemplate::class;

    protected $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
            ["field" => "language", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $sort_params = $this->getRequestData($cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
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

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [           
            "template_name" => ["required"],
            "language" => ["required"],
            "defaultHeaderValues" => "",
            "defaultBodyValues" => "",
            "defaultButtonValues" => "",
            "is_active" => ""
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

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getRequiredValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $validatedData = $this->_beforeSave($validatedData);

            $model = $this->modelClass::create($validatedData);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    private function _beforeSave($validatedData)
    {
        if (isset($validatedData['defaultHeaderValues']) && is_array($validatedData['defaultHeaderValues']))
        {
            unset($validatedData['defaultHeaderValues']['(sr-id)']);

            $validatedData['default_header_values'] = array_values($validatedData['defaultHeaderValues']);

            unset($validatedData['defaultHeaderValues']);
        }

        if (isset($validatedData['defaultBodyValues']) && is_array($validatedData['defaultBodyValues']))
        {
            unset($validatedData['defaultBodyValues']['(sr-id)']);

            $validatedData['default_body_values'] = array_values($validatedData['defaultBodyValues']);

            unset($validatedData['defaultBodyValues']);
        }

        if (isset($validatedData['defaultButtonValues']) && is_array($validatedData['defaultButtonValues']))
        {
            $validatedData['default_button_values'] = [];

            foreach($validatedData['defaultButtonValues'] as $k => $v)
            {
                if (is_int($k))
                {
                    $validatedData['default_button_values'][$k] = [$v];
                }
            }

            unset($validatedData['defaultButtonValues']);
        }

        return $validatedData;
    }

    public function edit($id)
    {
        $model = $this->modelClass::findOrFail($id);

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
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getRequiredValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $validatedData = $this->_beforeSave($validatedData);

            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function copy($id)
    {
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }
}
