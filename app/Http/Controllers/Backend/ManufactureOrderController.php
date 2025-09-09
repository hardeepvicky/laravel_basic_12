<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Maker;
use App\Models\ManufactureOrder;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class ManufactureOrderController extends BackendController
{
    public String $routePrefix = "manufacture-order";

    protected $modelClass = ManufactureOrder::class;

    protected ManufactureOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);
        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $sale_order_condition_list = $this->getConditions($cache_key, [
            ["field" => "sale_order_no", "type" => ""],
        ], true);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "manufacture_complete_by", "type" => "int"],
            ["field" => "from_order_date_time", "type" => "date"],
            ["field" => "to_order_date_time", "type" => "date"],
            ["field" => "from_manufacture_complete_date_time", "type" => "date"],
            ["field" => "to_manufacture_complete_date_time", "type" => "date"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::query();

        if (isset($condition_list['from_order_date_time']))
        {
            $builder->whereDate('order_date_time', ">=", $condition_list['from_order_date_time']);
        }

        if (isset($condition_list['to_order_date_time']))
        {
            $builder->whereDate('order_date_time', "<=", $condition_list['to_order_date_time']);
        }

        if (isset($condition_list['from_manufacture_complete_date_time']))
        {
            $builder->whereDate('manufacture_complete_date_time', ">=", $condition_list['from_manufacture_complete_date_time']);
        }

        if (isset($condition_list['to_manufacture_complete_date_time']))
        {
            $builder->whereDate('manufacture_complete_date_time', "<=", $condition_list['to_manufacture_complete_date_time']);
        }

        $conditions = $array_helper->unsetValues($conditions, ["from_order_date_time", "to_order_date_time", "from_manufacture_complete_date_time", "to_manufacture_complete_date_time"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        $builder->where($conditions);

        $builder->with([
            "product", 
            "make",
            "saleOrderProduct.saleOrder"
        ]);

        if (isset($sale_order_condition_list['sale_order_no']))
        {
            $builder->whereHas("saleOrderProduct", function($q) use($sale_order_condition_list) {
                $q->whereHas("saleOrder", function($q)  use($sale_order_condition_list) {
                    $q->where("order_no", "like", "%" . $sale_order_condition_list['sale_order_no'] . "%");
                });
            });
        }

        if ($apply_sort)
        {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass();

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
        if (!parent::beforeViewRender()) {
            return false;
        }

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $user_list = User::getList("id", "display_name", ["type" => User::TYPE_INTERNAL]);

        $status_list = ManufactureOrder::getStatusList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);
        
        $this->setForView(compact("product_list", "make_list", "user_list", "status_list", "field_labels"));
    }


    private function _common_validation_rules()
    {
        return [
            'assign_to_user_id' => ["required"],
            'manufactured_qty' => ["required"],
            'manufacture_complete_date_time' => [],
            'manufacture_complete_by' => [],            
            'remarks' => ["nullable", "max:255"],
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

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

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $rules["manufactured_qty"][] = "numeric";
        $rules["manufactured_qty"][] = "max:" . $this->model->order_qty;

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $validatedData = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }
}
