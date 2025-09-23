<?php

namespace App\Http\Controllers\Backend;

use App\Models\Country;
use App\Models\ExpState;
use App\Models\FieldSetting;
use DateTimeZone;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ExportStateController extends BackendController
{
    public String $routePrefix = "export-state";

    protected $modelClass = ExpState::class;

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
            ["field" => "country_id", "type" => "int"],
            ["field" => "iso2", "type" => "string"],
            ["field" => "country_id", "type" => "int"],
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
        $country_list = Country::getListCache();

        $timezone_list = DateTimeZone::listIdentifiers();
        $timezone_list = array_combine($timezone_list, $timezone_list);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("country_list", "timezone_list", "field_labels"));

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
            "country_id" => ["required"],
            "name" => ["required"],
            "iso2" => ["required"],
            "iso3166_2" => ["required"],
            "timezone" => ["required"],
            "type" => ["nullable"],
            "list_display_order" => ["required"],
            "is_active" => [""],
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "name.required" => "State is required",
            "name.unique" => "State already exist",
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $rules = $this->_common_validation_rules();

        $rules = array_merge_recursive($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            d($validatedData, true);
            $model = $this->modelClass::create($validatedData);

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
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

        $rules = array_merge_recursive($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $model = $this->modelClass::findOrFail($id);

            $model->fill($validatedData);

            $model->save();

            $this->updateUserInfoAfterSave($model);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function ajax_get_list($country_id)
    {
        $response = ["status" => 1];

        $conditions = [
            "country_id" => $country_id
        ];

        $list = $this->modelClass::getList("id", "display_name", $conditions, "name");

        $response['data'] = [];

        foreach($list as $k => $v)
        {
            $response['data'][] = [
                'id' => $k,
                'name' => $v,
            ];
        }

        return $this->responseJson($response);
    }
}
