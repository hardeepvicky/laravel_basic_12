<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Models\DocumentCategory;
use App\Models\FieldSetting;
use App\Models\Uom;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class UomController extends BackendController
{
    public String $routePrefix = "admin.uom";

    protected $modelClass = Uom::class;

    protected Uom | null $model = null;

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
            ["field" => "parent_id", "type" => "int"],
            ["field" => "is_find_nested_child", "type" => "bool"],
            ["field" => "name", "type" => "string"],
            ["field" => "short_name", "type" => "string"],
            ["field" => "is_last_node", "type" => "int"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["parent_id"]) && $condition_list["parent_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        )
        {
            $child_id_list = $this->modelClass::getChildIdList([$condition_list["parent_id"]]);

            if ($child_id_list)
            {
                $builder->whereIn("id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["parent_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }

        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

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
                ["key" => "sort_by", "default" => "list_display_order"],
                ["key" => "sort_dir", "default" => "ASC"],
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

        $qb = $this->modelClass::query()->where("is_last_node", 0);

        $tree_list = $this->modelClass::getTreeList($qb);

        $this->setForView(compact("tree_list"));

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
            "parent_id" => ["nullable"],
            "short_name" => [],
            "list_display_order" => "",
            "is_last_node" => "",
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
        $this->model = new $this->modelClass();

        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $save_data = $validatedData;

            $this->model = $this->modelClass::create($save_data);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();

            return $this->afterSave($this->model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers(array $validatedData)
    {

    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

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
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));
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

    public function ajax_get_list($parent_id)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($parent_id)
        {
            $conditions[] = ["parent_id", "=", $parent_id];
        }

        $data = $this->modelClass::where($conditions)->select("id", "short_name")->get()->toArray();

        foreach($data as $k => $arr)
        {
            $data['name'] = $arr['short_name'];
        }

        $response['data'] = $data;

        return $this->responseJson($response);
    }
}
